/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */
var Dialogs =
{
    init: function() {

        	// Attach event listeners.
        	$('.close').click(App.closeDialogs.bind(App));
            // $('.has-tooltip').popup({on: 'hover'});
            // $('.has-inline-tooltip').popup({inline: true, on: 'hover'});
            $('.has-dropdown-menu').dropdown();
    },

    open: function(dialog)
    {
		$('.dialog').hide();
        return this.toggle(dialog);
    },

    toggle: function(dialog)
    {
        box	= '.dialog.'+ dialog;
        if ($(box).is(':hidden')) $(box).fadeIn(240);
        else $(box).fadeOut(240);
        return false;
    },

	close : function() {
		$('.dialog').fadeOut(240);
		return false;
	},

    //
    // "Add resource" dialog.
    //

    setupAddResourceForm: function(input)
    {
        // Semantic UI search input.
        // $(input).search({
        //     apiSettings: {
        //         method : 'POST',
        //         url: 'language/search/{query}?semantic=1'
        //     },
        //     searchFields: ['name', 'alt_names'],
        //     searchDelay: 500,
        //     searchFullText: false,
        //     onSelect: function(result, response) {
        //         document.addResourceDialogForm.lang.value = result.code;
        //     }
        // });

        // Clear input.
        $(input).find('.prompt').val('');
    },

    addResource: function()
    {
        // Get language code.
        var code = document.addResourceDialogForm.lang.value.trim();
        if (code.length < 3) {
            return false;
        }

        App.redirect(code +'/+'+ $(document.addResourceDialogForm.type).val());
        return false;
    }
};
