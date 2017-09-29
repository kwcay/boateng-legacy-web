window.FormHelper=function(e){"use strict";var n=function(e,n){return!!(e&&e.value&&e.value.trim&&e.value.trim().length)&&void ApiHelper.get("/en/api/check-title/"+n+"/"+encodeURIComponent(e.value.trim())).then(function(e){console.log(e)}).catch(function(e){console.log(e)})};return{checkTitle:n}}(window.ApiHelper);
window.userTest=!0;
//# sourceMappingURL=source.js.map
