/**
 * Copyright Di Nkomo(TM) 2015, all rights reserved
 *
 */
var App =
{

    _kbFocus: null,

	init: function()
	{
		// Save URL root for redirects.
        this.root = $('base').attr('href');

        // Set the environment of the app.
        this.isLocalEnvironment =
            (window.location.hostname == 'localhost' ||
            window.location.hostname.match(/.*\.local$/i) ||
            window.location.hostname.match(/.*\.vagrant$/i)) ? true : false;

        // Setup AJAX headers
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Initialize other objects.
        Dialogs.init();
        Forms.init();
        this.log('App initialized.');
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
		console.log('App.openDialog deprecated');

        return Dialogs.open(id);
	},

	closeDialogs : function()
    {
		console.log('App.closeDialogs deprecated');

        return Dialogs.close();
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
		if (console && this.isLocalEnvironment)
            console.log('App.js - '+ msg);
	}
};
