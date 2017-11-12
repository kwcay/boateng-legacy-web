
window.ApiHelper = (function (csrf, logger) {
  // Set CSRF token
  const TOKEN = csrf && csrf.content && csrf.content.length ? csrf.content : null;

  /**
   * @param  {string} endpoint
   * @param  {string} method
   */
  function makeRequest(endpoint, method = 'GET') {
    if (!TOKEN) {
      return logger.error('Invalid token.');
    }

    logger.log(`TODO: ${method} request to ${endpoint}`);

    return null;
  }

  return {
    req: makeRequest,
    log: logger.log,
    warn: logger.warn,
    error: logger.error,
  };
})(document ? document.getElementsByName('csrf-token')[0] : null, window ? window.console : function(){});
