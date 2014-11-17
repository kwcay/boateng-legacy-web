/**
 * @author Francis Amankrah <frank@frnk.ca>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

var App =
{
	init: function()
	{
		// 
		Remote.init();
	},
	
	redirect: function(path) {
		path	= path || '';
		window.location = $(document.head).find('base').attr('href') + path;
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
		if (console) console.log('App.js - '+ msg);
	}
};

// Initiate
$(document).ready(function(event)
{
	// Initialize app
	App.init();
	
	// Attach event listeners
	$('.close').click(App.closeDialogs.bind(App));
	
	// Setup extra keys on relevant input boxes
	$('.text-input').each(function(index, element) {
		Form.setupKeyboard(element);
	});
	
	console.log();
	console.log(document.head.base);
});
