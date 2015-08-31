/*
 *
 */
var Dialogs =
{
    definition: {
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

        // Add selected class to current link.
        $(link).addClass('selected');
        this.definition.type = $(link).data('type');
    },

    /*
     *
     */
    showDefinitionForm: function(form)
    {
        console.log(form.language.value);
        
        if (this.definition.langCode)
            App.redirect(this.definition.langCode +'/+'+ this.definition.type);

        return false;
    }
};
