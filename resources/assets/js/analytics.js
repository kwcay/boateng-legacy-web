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
    if (typeof keenClient == 'undefined')
      return this.log('Keen client not loaded.');

    data = data || {};
    data.referrer = document.referrer;
    data.keen = {
      timestamp: new Date().toISOString()
    };

    // Track event w/ Keen.io
    keenClient.addEvent(event, data, function(err, res) {

      // Handle errors.
      if (err) {
        console.error('Keen.io error: ' + err);
      }

      else
      {

      }

    });
  },

	log: function(msg) {
		if (console && this.isLocalEnvironment) {
        console.log('Analytics.js - '+ msg);
      }
	}
};
