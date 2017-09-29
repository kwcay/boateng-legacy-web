
window.ApiHelper = (function (csrf, logger) {
  // Set CSRF token
  var token = csrf && csrf.content && csrf.content.length ? csrf.content : null;

  /**
   * @param  {string}  endpoint
   * @return {Promise}
   */
  var getResource = function(endpoint) {
    return new Promise(function(resolve, reject) {
      var request = new XMLHttpRequest();

      request.open("get", endpoint);
      request.setRequestHeader("Accept", "application/json");
      request.onload = function() {
        return resolve(JSON.parse(request.responseText));
      };
      request.onerror = function() {
        return reject(xhr.statusText);
      };

      request.send();
    });
  };

  return {
    get: getResource,
    log: logger.log,
    warn: logger.warn,
    error: logger.error
  };
})(document.getElementsByName("csrf-token")[0], window.console);
