window.ApiHelper=function(e,n){var t=(e&&e.content&&e.content.length?e.content:null,function(e){return new Promise(function(n,t){var r=new XMLHttpRequest;r.open("get",e),r.setRequestHeader("Accept","application/json"),r.onload=function(){return n(JSON.parse(r.responseText))},r.onerror=function(){return t(xhr.statusText)},r.send()})});return{get:t,log:n.log,warn:n.warn,error:n.error}}(document.getElementsByName("csrf-token")[0],window.console);
window.boateng=function(){var o=function(){return console.log("Todo: open modal"),!1};return{modal:o}}();
//# sourceMappingURL=source.js.map
