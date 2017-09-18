/**
 *
 */
window.FormHelper = (function() {
  /**
   * @param  {object}  element
   * @return {boolean}
   */
  var checkTitle = function(element, languages) {
    if (! element || ! element.value || ! element.value.trim || ! element.value.trim().length)
      return false;

    var title = element.value.trim();

    console.log(title + " / " + languages);
  };

  return {
    checkTitle: checkTitle
  };
})();
