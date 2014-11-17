/**
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */

var Remote =
{
	xhr : null,
	token : '[i:token]',
	data : {},
	
	init : function()
	{
		// Default AJAX settings
		$.ajaxSetup({
			dataType: 'json'
		});
	},
	
	setData : function(post) {
		this.data	= post;
		this.data.format		= 'json';
		this.data[this.token]	= 1;
	},
	
	sendData : function(callback, context)
	{
		// Send request and log result
		this.log('Sending request...');
		this.xhr	= $.ajax({data : this.data}).always(function(a, status, b) {
			Remote.end('XHR: '+ status);
		})
		
		// ...
		.done(function(result) {
			if (callback)
				callback.call(context, result);
		})
		
		// Failed request
		.fail(function() {
			// ...
		});
	},
	
	// Finish an ajax request
	end : function(sys) {
		this.log('XHR: '+ sys);
		this.xhr	= null;
		this.data	= {};
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
		if (console) console.log('Remote.js - '+ msg);
	}
};

