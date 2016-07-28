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

    // Configure defaults & Keen.io parameters.
    data = data || {};
    data.referrer = document.referrer;
    data.keen = data.keen || {};
    data.keen.timestamp = new Date().toISOString();
    data.keen.ip = '${keen.ip}';
    data.keen.ua = '${keen.user_agent}';
    data.keen.addons = data.keen.addons || [];

    data.keen.addons.push({
      name: "keen:ip_to_geo",
      input: {
        ip: "keen.ip"
      },
      output: "geo_data"
    });

    data.keen.addons.push({
      name: "keen:ua_parser",
      input: {
        ua_string: "keen.ua"
      },
      output: "ua_data"
    });

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
