var App={_kbFocus:null,init:function(){this.root=$("base").attr("href"),this.isLocalEnvironment="localhost"==window.location.hostname||window.location.hostname.match(/.*\.local$/i)||window.location.hostname.match(/.*\.vagrant$/i)?!0:!1,$.ajaxSetup({headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")}}),Dialogs.init(),Forms.init(),this.log("App initialized.")},setKeyboardFocus:function(o){this._kbFocus=o},keyboardInput:function(o){return this._kbFocus&&(this._kbFocus.value=this._kbFocus.value+o,this._kbFocus.focus()),!1},openDialog:function(o){return console.log("App.openDialog deprecated"),Dialogs.open(o)},closeDialogs:function(){return console.log("App.closeDialogs deprecated"),Dialogs.close()},redirect:function(o){window.location=o.length>1?this.root+o:this.root},urlencode:function(o){return encodeURIComponent((o+"").toString()).replace(/!/g,"%21").replace(/'/g,"%27").replace(/\(/g,"%28").replace(/\)/g,"%29").replace(/\*/g,"%2A").replace(/%20/g,"+")},log:function(o){console&&this.isLocalEnvironment&&console.log("App.js - "+o)}};
var Dialogs={init:function(){$(".close").click(App.closeDialogs.bind(App)),$(".has-dropdown-menu").dropdown()},open:function(o){return $(".dialog").hide(),this.toggle(o)},toggle:function(o){return box=".dialog."+o,$(box).is(":hidden")?$(box).fadeIn(240):$(box).fadeOut(240),!1},close:function(){return $(".dialog").fadeOut(240),!1},setupAddResourceForm:function(o){$(o).find(".prompt").val("")},addResource:function(){var o=document.addResourceDialogForm.lang.value.trim();return o.length<3?!1:(App.redirect(o+"/+"+$(document.addResourceDialogForm.type).val()),!1)}};
var Forms={_def:{},init:function(){$(".text-input").focus(function(){App.setKeyboardFocus(this),$("#keyboard").fadeIn(300)}),$(".en-text-input").focus(function(){App.setKeyboardFocus(null),$("#keyboard").fadeOut(300)}),$("#keyboard").draggable()},getDefinitionForm:function(e){return this._def[e]},setupLangSearch:function(e,n,a,t){if($(e)){$(e).selectize({valueField:"code",labelField:"name",searchField:["code","name","altNames"],options:n,plugins:t||null,create:!1,maxItems:a||1,render:{item:function(e,n){return'<div><span class="name">'+n(e.name)+"</span></div>"},score:function(e){return this.getScoreFunction(e)},option:function(e,n){var a=e.name;e.parentName&&e.parentName.length&&(a+=" (a sub-language of "+e.parentName+")");var t="";return e.altNames&&e.altNames.length&&(t='<span class="hint"> &mdash; Also known as '+e.altNames+"</span>"),'<div><span class="label">'+n(a)+"</span>"+t+"</div>"}},load:function(e,n){return e.trim().length?void $.ajax({url:App.root+"0.1/language/search/"+App.urlencode(e.trim()),type:"GET",error:function(){n()},success:function(e){n(e)}}):n()}})}},setupDefinitionLookup:function(e,n){e=e||"search",n=n||{},this._def[e]={form:$(document[e]),results:n.results||$("#results"),query:n.query||$(document[e].q),clear:n.clear||$("input[name=clear]"),language:{code:n.langCode||!1,name:n.langName||"another language"}},$(document[e]).submit([this._def[e]],function(e){e.preventDefault();var a=e.data[0],t=a.query.val().trim();if(t.length<2)return a.query.focus(),!1;a.results.html('<div class="center">looking up '+t+"...</div>");var s=App.root+"0.1";s+=n.langCode?"/word/search/"+App.urlencode(t)+"?lang="+n.langCode:"/search/"+App.urlencode(t)+"?method=fulltext",$.ajax({url:s,type:"GET",error:function(e,n,t){App.log("XHR error on search form: "+t+" ("+e.status+")"),a.results.html('<div class="center">Seems like we ran into a snag <span class="fa fa-frown-o"></span> please try again later.</div>')},success:function(e){if(e.length>0){var n='<div class="center">we found <em>'+e.length+"</em> results for <i>"+t+"</i>.</div><ol>";$.each(e,function(e,a){switch(a.resourceType){case"language":var t=a.parentLanguage?' is a child language of <a href="'+a.parentLanguage.uri+'">'+a.parentLanguage.name+"</a>":"";n+='<li><a href="'+a.uri+'">'+a.name+"</a> <small>(language)</small>"+t+"</li>";break;default:n+='<li><a href="'+a.uri+'">'+a.title+"</a> <small>("+a.subType+")</small> is a "+a.type+" that means <i>"+a.translation.practical.eng+'</i> in  <a href="'+a.mainLanguage.uri+'">'+a.mainLanguage.name+"</a></li>"}}),a.results.html(n+"</ol>")}else a.results.html('<div class="center">we couldn\'t find anything matching that query <span class="fa fa-frown-o"></span></div>')}})}),this._def[e].clear.click(function(){this.query.val(""),this.results.html('<div class="center">Use this <em>&#10548;</em> to lookup words<br />in '+this.language.name+".</div>"),this.query.focus()}.bind(this._def[e]))},resetDefinition:function(e){this._def[e]&&(this._def[e].query.value="",this._def[e].results.html('<div class="center">Use this <em>&#10548;</em> to lookup words<br />in '+this._def[e].langName+".</div>"),this._def[e].query.focus())},lookupDefinition:function(e){if(this._def[e]){var n=this._def[e].query.value.trim();return n.length<2?(this._def[e].query.focus(),!1):(this._def[e].results.html('<div class="center">looking up '+n+"...</div>"),$.ajax({url:App.root+"/definition/search/"+App.urlencode(n),type:"POST",error:function(n,a,t){Forms.log("XHR error on search form: "+n.status+" ("+t+")"),Forms.setDefinitionResult(e,'<div class="center">Seems like we ran into a snag <span class="fa fa-frown-o"></span> try again?</div>')},success:function(n){if(n.results.definitions.length>0){var a='<div class="center">we found <em>'+n.results.definitions.length+"</em> definitions for <i>"+n.results.query+"</i>.</div><ol>";$.each(n.results.definitions,function(e,n){a+='<li><a href="'+n.uri+'">'+n.data+"</a> <small>("+n.type+")</small> is a word that means <i>"+n.translation.en+'</i> in  <a href="'+n.language.uri+'">'+n.language.name+"</a></li>"}),Forms.setDefinitionResult(e,a+"</ol>")}else Forms.setDefinitionResult(e,'<div class="center">we couldn\'t find anything matching that query <span class="fa fa-frown-o"></span></div>')}}),!1)}},setDefinitionResult:function(e,n){this._def[e]&&this._def[e].results.html(n)},log:function(e){console&&this.isLocalEnvironment&&console.log("Forms.js - "+e)}};
var Resources={checkDefinitionTitle:function(n){if(n&&n.trim().length){n.addClass("loading");{({langCode:!1,onError:function(n,o,i){Resources.log("XHR error: "+i)}.bind(n),onSuccess:function(){}})}this.findDefinitionByTitle(n.value,function(){})}},findDefinitionByTitle:function(n,o){if(n.trim().length){o=o||{};var i=App.root+"/definition/exists/"+App.urlencode(n)+(o.langCode?"?lang="+o.langCode:"");$.ajax({url:i,type:"POST",error:o.onError,success:o.onSuccess})}},log:function(n){console&&this.isLocalEnvironment&&console.log("Resources.js - "+n)}};
//# sourceMappingURL=dinkomo.js.map