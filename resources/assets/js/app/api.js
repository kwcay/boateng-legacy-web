
window.ApiHelper = (function (csrf, logger) {
  // Set CSRF token
  const token = csrf && csrf.content && csrf.content.length ? csrf.content : null;

  /**
   * @param  {string} endpoint
   * @param  {string} method
   */
  function makeRequest(endpoint, method = 'GET') {
    if (!token) {
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
})(document.getElementsByName('csrf-token')[0], window.console);
