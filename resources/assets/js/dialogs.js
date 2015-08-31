/*
 *
 */
var Dialogs =
{
    resource: {
        type: 'word',
        langCode: null
    },

    /*
     * Related to "add resource" dialog.
     */
    toggleType: function(link)
    {
        // Remove selected class from all links.
        $('.dialog.resource .types a').removeClass('selected');

        // Add selected class to current link and update the resource type.
        $(link).addClass('selected');
        this.resource.type = $(link).data('type');
    },

    /*
     * Initializes resource-related dialog stuff.
     */
    initResource: function()
    {
        // Semantic UI search input.
        $('.dialog.resource .ui.dialog-resource-lang').search({
            apiSettings: {
                url: 'language/search/{query}?semantic=1'
            },
            searchFields: ['name', 'alt_names'],
            searchDelay: 500,
            searchFullText: false,
            onSelect: function(result, response) {
                App.redirect(result.code +'/+'+ this.resource.type);
            }.bind(this)
        });
    }
};
