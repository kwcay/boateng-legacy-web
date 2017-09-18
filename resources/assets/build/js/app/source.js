window.ApiHelper=function(e,n){var o=e&&e.content&&e.content.length?e.content:null,r=function(e,r){return o?(r=(r||"GET").toUpperCase(),void n.log("TODO: "+r+" request to "+e)):n.error("Invalid token.")};return{req:r,log:n.log,warn:n.warn,error:n.error}}(document.getElementsByName("csrf-token")[0],window.console);
window.boateng=function(){var o=function(){return console.log("Todo: open modal"),!1};return{modal:o}}();
//# sourceMappingURL=source.js.map
