/*
 *
 */
var Dialogs =
{
    /*
     * Attaches listener to "Add Resource" input.
     *
     * @param mixed input
     */
    setupAddResourceForm: function(input)
    {
        // Semantic UI search input.
        $(input).search({
            apiSettings: {
                url: 'language/search/{query}?semantic=1'
            },
            searchFields: ['name', 'alt_names'],
            searchDelay: 500,
            searchFullText: false,
            onSelect: function(result, response) {
                document.dialogResourceForm.lang = result.code;
            }
        });
    },

    addResource: function()
    {
        console.log(document.dialogResourceForm.lang);

        return false;
    }
};
