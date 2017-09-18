
window.ApiHelper = (function (csrf, logger) {
  // Set CSRF token
  var token = csrf && csrf.content && csrf.content.length ? csrf.content : null;

  /**
   * @param  {string} endpoint
   * @param  {string} method
   */
  var makeRequest = function(endpoint, method) {
    if (! token)
      return logger.error("Invalid token.");

    method = (method || "GET").toUpperCase();

    logger.log("TODO: " + method + " request to " + endpoint);
  };

  return {
    req: makeRequest,
    log: logger.log,
    warn: logger.warn,
    error: logger.error
  };
})(document.getElementsByName("csrf-token")[0], window.console);
