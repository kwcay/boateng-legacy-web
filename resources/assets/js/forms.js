
var Forms =
{
    /**
     * Definition lookup forms.
     */
    _def: {},

    /**
     * Prepares a language search input for AJAX calls.
     *
     * @param input
     * @param options
     * @param max
     * @param plugins
     */
    setupLangSearch: function(input, options, max, plugins)
    {
        if (!$(input)) return;
        $(input).selectize({
            valueField: 'code',
            labelField: 'name',
            searchField: ['code', 'name', 'altNames'],
            options: options,
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
                        hint = '<span class="hint"> &mdash; Also known as '+ item.altNames.join(', ') + '</span>';
                    
                    // Return formatted HTML
                    return  '<div>' +
                                '<span class="label">' + escape(title) + '</span>' + hint +
                            '</div>';
                }
            },
            load: function(query, callback) {
                if (!query.trim().length) return callback();
                $.ajax({
                    url: App.root +'language/search/' + App.urlencode(query.trim()),
                    type: 'POST',
                    error: function() {
                        callback();
                    },
                    success: function(obj) {
                        callback(obj.results.languages);
                    }
                });
            }
        });
    },

    /**
     *
     * @param name
     * @param options
     * @param lang
     */
    setupDefinitionLookup: function(name, options, lang)
    {
        // Retrieve form elements.
        name = name || 'search';
        console.log(typeof document[name]);
        if (!document[name] || !resultsDiv) return;
        var resultsDiv = options.results || $('#results'),
            langCode = options.langCode || false,
            langName = options.langName || false,
            clearInput = options.clear || $('input[name=clear]'),
            form = document[name];

        this._def[name] = {
            form: $(document[name]),
            results: options.results || $('#results'),
            query: options.query || $(document[name].q),
            language: {
                code: options.langCode || false,
                name: options.langName || 'another language'
            }
        };

        // TODO: document onSubmit function.
        $(form).submit();
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
                                '<a href="'+ def.uri +'">'+ def.word +'</a>'+
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

    setDefinitionResult: function(name, html)
    {
        // Performance check
        if (!this._def[name]) return;

        this._def[name].results.html(html);
    },
	
	log: function(msg) {
		if (console) console.log('Forms.js - '+ msg);
	}
};
