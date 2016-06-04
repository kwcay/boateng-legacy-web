/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 * @author  Frank Yaw (frank@frnk.ca)
 */

/**
 * @param options.form      Form name in DOM.
 * @param options.clear     Selector for clear button.
 * @param options.container Container for search results.
 */
var DiNkomoDictionary = function(options) {

    options = options || {};

    // Find dictionary form & form elements.
    var form = {};
    form.form = $(document[options.form || 'dictionary']);
    form.query = $(document[options.form || 'dictionary'].q);
    form.clear = $(options.clear || 'input[name=clear]');
    form.container = $(options.results || '#results');
    if (!form.form)
    {
        return Utilities.error('Dictionary form not found.');
    }
    else if (!form.query || !form.clear)
    {
        return Utilities.error('Dictionary form not valid.');
    }
    else if (!form.container)
    {
        return Utilities.error('Dictionary results container not found.');
    }

    // Container contents.
    form.clearedContainer = form.container.html();

    // API endpoint.
    form.endpoint = '/api/0.1/search/:query';

    /**
     * On submit event listener for search form.
     *
     * @param object event
     * @return void
     */
    var onSubmit = function(event) {

        event.preventDefault();
        var form = event.data;

        // Performance check
        var query = form.query.val().trim();
        if (query.length < 2) {
            form.query.focus();
            return false;
        }

        // Display loading message
        form.container.html('<div class="text-center">looking up '+ query +'...</div>');

        // Start ajax request
        $.ajax({
            url: form.endpoint.replace(':query', App.urlencode(query)) + '?lang='+ (options.langCode || ''),
            type: 'GET',
            error: form.onSubmitError,
            success: function(results)
            {
                if (results.length > 0)
                {
                    var html =
                        '<div class="text-center">' +
                            'we found <em>'+ results.length +'</em> results' +
                            ' for <i>' + query + '</i>.' +
                        '</div>' +
                        '<ol>';

                    for (var i in results)
                    {
                        switch (results[i].resourceType)
                        {
                            case 'language':
                                html += form.formatLanguageResult(results[i]);
                                break;

                            case 'definition':
                                html += form.formatDefinitionResult(results[i]);
                                break;
                        }
                    }

                    form.container.html(html +'</ol>');
                }

                else {
                    form.container.html(
                        '<div class="center">' +
                            'we couldn\'t find anything matching that query ' +
                            '<span class="fa fa-frown-o"></span>' +
                        '</div>'
                    );
                }
            }
        });

        //return false;
    };

    /**
     * Handles ajax errors.
     *
     * @param object xhr
     * @param string status
     * @param string error
     */
    form.onSubmitError = function(xhr, status, error) {

        Utilities.error('Dictionary error: '+ error +' ('+ xhr.status +')');

        this.results.html(
            '<div class="center">'+
                'Seems like we ran into a snag <span class="fa fa-frown-o"></span> ' +
                'please try again later.' +
            '</div>');

    }.bind(form);

    /**
     * Formats a language result.
     *
     * @param object language
     */
    form.formatLanguageResult = function(language) {

        var row =
            '<li>' +
                '<a href="' + language.uri + '">' +
                    language.name +
                '</a> ';

        // Add parent language.
        if (language.parentCode && language.parentCode.length >= 3) {
            row +=
            'is a child language of ' +
            '<a href="' + language.parentUri + '">' +
                language.parentName +
            '</a>';
        }

        else {
            row += 'is a language';
        }

        row += '</li>';

        return row;
    };

    /**
     * Formats a definition results.
     *
     * @param object definition
     */
    form.formatDefinitionResult = function(definition) {

        var row =
            '<li>' +
                '<a href="' + definition.uri + '">' +
                    definition.mainTitle +
                '</a> ' +
                '<small>' +
                    '(' + definition.subType + ')' +
                '</small> '+
                'is a ' + definition.type + ' that means ' +
                '<i>' + definition.translation.practical.eng + '</i> in '+
                '<a href="' + definition.mainLanguage.uri + '">' +
                    definition.mainLanguage.name +
                '</a>' +
            '</li>';

        return row;
    };

    // Attach onSubmit listener.
    form.form.submit(form, onSubmit);

    // Attach clear form listener.
    form.clear.click(function() {
       this.query.val('');
       this.container.html(this.clearedContainer);
       this.query.focus();
   }.bind(form));

   // If the form already has a query, look it up.
   if (form.query.val().trim().length > 0) {
       form.form.submit();
   }

    Utilities.info('Dictionary form initialized.');

    return form;
};
