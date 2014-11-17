/**
 * @author Francis Amankrah <frank@frnk.ca>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

var Form =
{
	doLookup: function(form)
	{
		// Performance check
		var query	= form.q.value.trim();
		if (query.length < 3) {
			form.q.focus();
			return false;
		}
		
		// Display loading message
		$('.extra-keys').fadeOut(150);
		$('#results').html('<div class="center">looking up '+ query +'...</div>');
		
		// Prepare request
		Remote.setData({q : query});
		
		// Send request
		Remote.sendData(this.doLookupResults, this);
		
		return false;
	},
	
	doLookupResults: function(data)
	{
		
		if (data.definitions.length > 0)
		{
			var html	= '';
			for (var i = 0; i < data.definitions.length; i++)
			{
				html	+= data.definitions[i].word + '<br />';
			}
			$('#results').html(html);
		}
		
		else {
			$('#results').html('<div class="center">no results found.</div>');
		}
	},
	
	setupKeyboard: function(input, parent)
	{
		// Initial setup
		if (!this._kbCount)
			this._kbCount	= 0;
		
		// Create extra keyboard
		var xkeyId		= 'xkey-'+ ++this._kbCount;
		parent			= parent || $(input).parent();
		var keyboard	= $('<div>').appendTo(parent).addClass('extra-keys').attr('id', xkeyId);
		
		// Add event listeners
		$(input).focus(function(e) {
			$('.extra-keys').each(function(index, element) {
				if ($(element).attr('id') != xkeyId)
					$(element).fadeOut(150);
			});
			keyboard.fadeIn(150);
		}).blur(function(e) {
			console.log(e.relatedTarget);
			if (!$(e.relatedTarget).hasClass(xkeyId))
				keyboard.fadeOut(150);
		});
		
		// Add special keys
		this.addExtraKey('ɛ', keyboard, xkeyId, input);
		this.addExtraKey('ɔ', keyboard, xkeyId, input);
		this.addExtraKey('õ', keyboard, xkeyId, input);
	},
	
	addExtraKey: function(key, keyboard, keyboardId, input)
	{
		$('<input>', {
			type: 'button',
			value: key,
			href: '#',
			'class': keyboardId
		}).appendTo(keyboard).click(function(event) {
			input.value	= input.value + key;
			input.focus();
			return false;
		});
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
	
	log: function(msg) {
		if (console) console.log('Form.js - '+ msg);
	}
};

// Initiate
$(document).ready(function()
{
	// Initialize app
	App.init();
	
	$('.close').click(App.closeDialogs.bind(App));
});

