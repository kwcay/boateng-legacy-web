/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */
var Forms =
{
    /**
     * Definition lookup forms.
     */
    _def: {},

    /**
     *
     */
    init: function() {

        // Attach helper keyboard to text inputs.
        $('.text-input').focus(function() {
            App.setKeyboardFocus(this);
            $('#keyboard').fadeIn(300);
        });

        // Remove helper keyboard when focus is lost.
        $('.en-text-input').focus(function() {
            App.setKeyboardFocus(null);
            $('#keyboard').fadeOut(300);
        });

        // Make keyboard draggable.
        $('#keyboard').draggable();
    },

    /**
     *
     */
    getDefinitionForm: function(name) {
        return this._def[name];
    },

    /**
     * Prepares a language search input for AJAX calls.
     *
     * @param mixed input
     * @param object items
     * @param int max
     * @param array plugins
     */
    setupLangSearch: function(input, items, max, plugins)
    {
        // Performance check.
        if (!$(input)) return;

        // Initialize selectize input.
        var $select = $(input).selectize({
            valueField: 'code',
            labelField: 'name',
            searchField: ['code', 'name', 'altNames'],
            options: items,
            plugins: (plugins || null),
            create: false,
            maxItems: (max || 1),
            render: {
                item: function(item, escape) {
                    return  '<div>' +
                                '<span class="name">' + escape(item.name) + '</span>' +
                            '</div>';
                },
                score: function(search) {
                    return this.getScoreFunction(search);
                },
                option: function(item, escape)
                {
                    // Language title
                    var title   = item.name;
                    if (item.parentName && item.parentName.length)
                        title += ' (a sub-language of '+ item.parentName +')';

                    // Add a short desciption
                    var hint = '';
                    if (item.altNames && item.altNames.length)
                        hint = '<span class="hint"> &mdash; Also known as '+ item.altNames + '</span>';

                    // Return formatted HTML
                    return  '<div>' +
                                '<span class="label">' + escape(title) + '</span>' + hint +
                            '</div>';
                }
            },
            load: function(query, callback) {
                if (!query.trim().length) return callback();
                $.ajax({
                    url: App.root +'0.1/language/search/' + App.urlencode(query.trim()),
                    type: 'GET',
                    error: function() {
                        callback();
                    },
                    success: function(results) {
                        callback(results);
                    }
                });
            }
        });
    },

    /**
     * @param name
     * @param options
     */
    setupDefinitionLookup: function(name, options)
    {
        // Retrieve form elements.
        name = name || 'search';
        options = options || {};

        this._def[name] = {
            form: $(document[name]),
            results: options.results || $('#results'),
            query: options.query || $(document[name].q),
            clear:  options.clear || $('input[name=clear]'),
            language: {
                code: options.langCode || false,
                name: options.langName || 'another language'
            }
        };

        // Form submit function.
        $(document[name]).submit([this._def[name]], function(event)
        {
            event.preventDefault();
            var form = event.data[0];

            // Performance check
            var query	= form.query.val().trim();
            if (query.length < 2) {
                form.query.focus();
                return false;
            }

            // Display loading message
            form.results.html('<div class="center">looking up '+ query +'...</div>');

            // Build endpoint.
            // var endpoint = App.root + '0.1';
            var endpoint = '/0.1';
            if (options.langCode) {
                endpoint += '/word/search/' + App.urlencode(query) + '?lang='+ options.langCode;
            } else {
                endpoint += '/search/' + App.urlencode(query) + '?method=fulltext';
            }

            // Start ajax request
            $.ajax({
                url: endpoint,
                type: 'GET',
                error: function(xhr, status, error) {
                    App.log('XHR error on search form: '+ error +' ('+ xhr.status +')');
                    form.results.html(
                        '<div class="center">'+
                            'Seems like we ran into a snag <span class="fa fa-frown-o"></span> '+
                            'please try again later.'+
                        '</div>');
                },
                success: function(results)
                {
                    if (results.length > 0)
                    {
                        var html	=
                            '<div class="center">'+
                            'we found <em>'+ results.length +'</em> results'+
                            ' for <i>'+ query +'</i>.'+
                            '</div><ol>';

                        $.each(results, function(i, res) {

                            switch (res.resourceType)
                            {
                                case 'language':
                                    var parentData = res.parentLanguage ?
                                        ' is a child language of '+
                                        '<a href="'+ res.parentLanguage.uri +'">'+
                                        res.parentLanguage.name +
                                        '</a>' : '';
                                    html +=
                                        '<li>'+
                                        '<a href="'+ res.uri +'">'+ res.name +'</a>'+
                                        ' <small>(language)</small>'+ parentData +
                                        '</li>';
                                    break;

                                default:
                                    html +=
                                        '<li>'+
                                        '<a href="'+ res.uri +'">'+ res.title +'</a>'+
                                        ' <small>('+ res.subType +')</small>'+
                                        ' is a '+ res.type +' that means <i>'+ res.translation.practical.eng +'</i> in '+
                                        ' <a href="'+ res.mainLanguage.uri +'">'+ res.mainLanguage.name +'</a>'+
                                        '</li>';
                            }
                        });

                        form.results.html(html +'</ol>');
                    }

                    else {
                        form.results.html('<div class="center">we couldn\'t find anything matching that query <span class="fa fa-frown-o"></span></div>');
                    }
                }
            });

            //return false;
        });

        // Form clearing.
        this._def[name].clear.click(function() {
           this.query.val('');
           this.results.html('<div class="center">Use this <em>&#10548;</em> to lookup words<br />in '+ this.language.name +'.</div>');
           this.query.focus();
       }.bind(this._def[name]));

       // Submit form.
       if (this._def[name].query.val().trim().length > 0) {
           this._def[name].form.submit();
       }
    },

    /**
     *
     * @param name
     */
    resetDefinition: function(name)
    {
        // Performance check.
        if (!this._def[name]) return;

        // Clear form.
        this._def[name].query.value = '';
        this._def[name].results.html(
            '<div class="center">'+
                'Use this <em>&#10548;</em> to lookup words'+
                '<br />in '+ this._def[name].langName +'.'+
            '</div>'
        );

        this._def[name].query.focus();
    },

    /**
     *
     */
    lookupDefinition: function(name)
    {
        // Performance check
        if (!this._def[name]) return;

        var query = this._def[name].query.value.trim();
        if (query.length < 2) {
            this._def[name].query.focus();
            return false;
        }

        // Display loading message
        this._def[name].results.html('<div class="center">looking up '+ query +'...</div>');

        // Start ajax request
        $.ajax({
            url: App.root +'/definition/search/' + App.urlencode(query),
            type: 'POST',

            // onError
            error: function(xhr, status, error)
            {
                Forms.log('XHR error on search form: '+ xhr.status +' ('+ error +')');
                Forms.setDefinitionResult(name,
                    '<div class="center">'+
                        'Seems like we ran into a snag <span class="fa fa-frown-o"></span> try again?'+
                    '</div>'
                );
            },

            // onSuccess
            success: function(obj)
            {
                if (obj.results.definitions.length > 0)
                {
                    var html	=
                        '<div class="center">'+
                            'we found <em>'+ obj.results.definitions.length +'</em> definitions'+
                            ' for <i>'+ obj.results.query +'</i>.'+
                        '</div><ol>';

                    $.each(obj.results.definitions, function(i, def)
                    {
                        html +=
                            '<li>'+
                                '<a href="'+ def.uri +'">'+ def.data +'</a>'+
                                ' <small>('+ def.type +')</small>'+
                                ' is a word that means <i>'+ def.translation.en +'</i> in '+
                                ' <a href="'+ def.language.uri +'">'+ def.language.name +'</a>'+
                            '</li>';
                    });

                    Forms.setDefinitionResult(name, html +'</ol>');
                }

                else {
                    Forms.setDefinitionResult(name,
                        '<div class="center">'+
                            'we couldn\'t find anything matching that query <span class="fa fa-frown-o"></span>'+
                        '</div>'
                    );
                }
            }
        });

        return false;
    },

    /**
     * TODO: when is this method used?
     */
    setDefinitionResult: function(name, html)
    {
        // Performance check
        if (!this._def[name]) return;

        this._def[name].results.html(html);
    },

	log: function(msg) {
		if (console && this.isLocalEnvironment)
            console.log('Forms.js - '+ msg);
	}
};
