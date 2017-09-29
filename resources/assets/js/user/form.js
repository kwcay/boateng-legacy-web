/**
 *
 */
window.FormHelper = (function(api) {
  "use strict";

  /**
   * @param  {object}  element
   * @return {boolean}
   */
  var checkTitle = function(element, languages) {
    if (! element || ! element.value || ! element.value.trim || ! element.value.trim().length)
      return false;

    ApiHelper
      .get("/en/api/check-title/" + languages + "/" + encodeURIComponent(element.value.trim()))
      .then(function(response) {
        console.log(response);
      })
      .catch(function(reason) {
        console.log(reason);
      });
  };

  return {
    checkTitle: checkTitle
  };
})(window.ApiHelper);
