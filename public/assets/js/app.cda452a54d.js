'use strict';

window.ApiHelper = function (csrf, logger) {
  // Set CSRF token
  var TOKEN = csrf && csrf.content && csrf.content.length ? csrf.content : null;

  /**
   * @param  {string} endpoint
   * @param  {string} method
   */
  function makeRequest(endpoint) {
    var method = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'GET';

    if (!TOKEN) {
      return logger.error('Invalid token.');
    }

    logger.log('TODO: ' + method + ' request to ' + endpoint);

    return null;
  }

  return {
    req: makeRequest,
    log: logger.log,
    warn: logger.warn,
    error: logger.error
  };
}(document ? document.getElementsByName('csrf-token')[0] : null, window ? window.console : function () {});
"use strict";

window.boateng = function () {

    /**
     *
     */
    var openModal = function openModal() {
        console.log("Todo: open modal");

        return false;
    };

    return {
        modal: openModal
    };
}();

