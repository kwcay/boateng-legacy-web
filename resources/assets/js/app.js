
var App =
{

    _kbFocus: null,

	init: function()
	{
		//
        this.root = $('base').attr('href');
	},

    /**
     *
     */
    setKeyboardFocus: function(input) {
        this._kbFocus   = input;
    },

    /**
     *
     */
    keyboardInput: function(letter)
    {
        if (this._kbFocus) {
            this._kbFocus.value = this._kbFocus.value + letter;
            this._kbFocus.focus();
        }

        return false;
    },

	openDialog : function(id)
	{
		$('.dialog').hide();
		box	= '.dialog.'+ id;
		if ($(box).is(':hidden')) $(box).fadeIn(240);
		else $(box).fadeOut(240);
		return false;
	},

	closeDialogs : function() {
		$('.dialog').fadeOut(240);
		return false;
	},

	redirect: function(path) {
		window.location = path.length > 1 ? this.root + path : this.root;
	},

	urlencode : function(str)
	{
		return encodeURIComponent((str + '').toString())
		.replace(/!/g, '%21')
		.replace(/'/g, '%27')
		.replace(/\(/g, '%28')
		.replace(/\)/g, '%29')
		.replace(/\*/g, '%2A')
		.replace(/%20/g, '+');
	},

	log: function(msg) {
		if (console) console.log('App.js - '+ msg);
	}
};

// Initiate
$(document).ready(function(event)
{
	// Initialize app.
	App.init();

	// Attach event listeners.
	$('.close').click(App.closeDialogs.bind(App));

    // Attache helper keyboard to text inputs.
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

    // Tooltips
    $('.has-tooltip').popup({on: 'hover'});
    $('.has-inline-tooltip').popup({inline: true, on: 'hover'});

    // Setup AJAX headers
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});
