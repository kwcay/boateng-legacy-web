
var Resources =
{
    /**
     * Shortcut to verfify a definition title form input.
     */
    checkDefinitionTitle: function(input)
    {
        // Performance check.
        if (!input || !input.trim().length) return;

        // Add loading class.
        input.addClass('loading');

        var options =
        {
            langCode: false,
            onError: function(xhr, status, error) {
                Resources.log('XHR error: '+ error);
            }.bind(input),
            onSuccess: function(obj) {
                
            }
        };

        // Lookup definition title.
        this.findDefinitionByTitle(input.value, function() {

        });
    },

    /**
     * Looks up a definition by title.
     */
    findDefinitionByTitle: function(title, options)
    {
        // Performance check.
        if (!title.trim().length) return;
        options = options || {};

        // Build endpoint.
        var endpoint = App.root +'/definition/exists/' + App.urlencode(title) +
            (options.langCode ? '?lang='+ options.langCode : '');

        // Lookup definitions by title.
        $.ajax({
            url: endpoint,
            type: 'POST',
            error: options.onError,
            success: options.onSuccess
        });
    },

	log: function(msg) {
		if (console) console.log('Resources.js - '+ msg);
	}
};
