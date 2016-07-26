/**
 * Copyright Di Nkɔmɔ(TM) 2016, all rights reserved.
 *
 */

/**
 * @todo  User http://requirejs.org/ as a dependency injector?
 */
var Analytics =
{
  /**
   * @param string event
   * @param object data
   */
  track: function(event, data) {

    // Performance check.
    if (!window.Keen)
      return;

    data = data || {};
    data.referrer = document.referrer;
    data.keen = {
      timestamp: new Date().toISOString()
    };

    // Track event w/ Keen.io
    window.Keen.addEvent(event, data, function(err, res) {

      // Handle errors.
      if (err) {
        this.log('An error occured while trying to track the event "'+ event +'".');
      }

    }.bind(this));
  },

	log: function(msg) {
		if (console && this.isLocalEnvironment) {
        console.log('Analytics.js - '+ msg);
      }
	}
};
