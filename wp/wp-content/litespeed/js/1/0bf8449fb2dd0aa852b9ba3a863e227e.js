/*!
 * jQuery blockUI plugin
 * Version 2.70.0-2014.11.23
 * Requires jQuery v1.7 or later
 *
 * Examples at: http://malsup.com/jquery/block/
 * Copyright (c) 2007-2013 M. Alsup
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 * Thanks to Amir-Hossein Sobhi for some excellent contributions!
 */
!function(){"use strict";function e(e){e.fn._fadeIn=e.fn.fadeIn;var t=e.noop||function(){},o=/MSIE/.test(navigator.userAgent),n=/MSIE 6.0/.test(navigator.userAgent)&&!/MSIE 8.0/.test(navigator.userAgent),i=(document.documentMode,"function"==typeof document.createElement("div").style.setExpression&&document.createElement("div").style.setExpression);e.blockUI=function(e){d(window,e)},e.unblockUI=function(e){a(window,e)},e.growlUI=function(t,o,n,i){var s=e('<div class="growlUI"></div>');t&&s.append("<h1>"+t+"</h1>"),o&&s.append("<h2>"+o+"</h2>"),n===undefined&&(n=3e3);var l=function(t){t=t||{},e.blockUI({message:s,fadeIn:"undefined"!=typeof t.fadeIn?t.fadeIn:700,fadeOut:"undefined"!=typeof t.fadeOut?t.fadeOut:1e3,timeout:"undefined"!=typeof t.timeout?t.timeout:n,centerY:!1,showOverlay:!1,onUnblock:i,css:e.blockUI.defaults.growlCSS})};l();s.css("opacity");s.on("mouseover",function(){l({fadeIn:0,timeout:3e4});var t=e(".blockMsg");t.stop(),t.fadeTo(300,1)}).on("mouseout",function(){e(".blockMsg").fadeOut(1e3)})},e.fn.block=function(t){if(this[0]===window)return e.blockUI(t),this;var o=e.extend({},e.blockUI.defaults,t||{});return this.each(function(){var t=e(this);o.ignoreIfBlocked&&t.data("blockUI.isBlocked")||t.unblock({fadeOut:0})}),this.each(function(){"static"==e.css(this,"position")&&(this.style.position="relative",e(this).data("blockUI.static",!0)),this.style.zoom=1,d(this,t)})},e.fn.unblock=function(t){return this[0]===window?(e.unblockUI(t),this):this.each(function(){a(this,t)})},e.blockUI.version=2.7,e.blockUI.defaults={message:"<h1>Please wait...</h1>",title:null,draggable:!0,theme:!1,css:{padding:0,margin:0,width:"30%",top:"40%",left:"35%",textAlign:"center",color:"#000",border:"3px solid #aaa",backgroundColor:"#fff",cursor:"wait"},themedCSS:{width:"30%",top:"40%",left:"35%"},overlayCSS:{backgroundColor:"#000",opacity:.6,cursor:"wait"},cursorReset:"default",growlCSS:{width:"350px",top:"10px",left:"",right:"10px",border:"none",padding:"5px",opacity:.6,cursor:"default",color:"#fff",backgroundColor:"#000","-webkit-border-radius":"10px","-moz-border-radius":"10px","border-radius":"10px"},iframeSrc:/^https/i.test(window.location.href||"")?"javascript:false":"about:blank",forceIframe:!1,baseZ:1e3,centerX:!0,centerY:!0,allowBodyStretch:!0,bindEvents:!0,constrainTabKey:!0,fadeIn:200,fadeOut:400,timeout:0,showOverlay:!0,focusInput:!0,focusableElements:":input:enabled:visible",onBlock:null,onUnblock:null,onOverlayClick:null,quirksmodeOffsetHack:4,blockMsgClass:"blockMsg",ignoreIfBlocked:!1};var s=null,l=[];function d(d,c){var u,b,h=d==window,k=c&&c.message!==undefined?c.message:undefined;if(!(c=e.extend({},e.blockUI.defaults,c||{})).ignoreIfBlocked||!e(d).data("blockUI.isBlocked")){if(c.overlayCSS=e.extend({},e.blockUI.defaults.overlayCSS,c.overlayCSS||{}),u=e.extend({},e.blockUI.defaults.css,c.css||{}),c.onOverlayClick&&(c.overlayCSS.cursor="pointer"),b=e.extend({},e.blockUI.defaults.themedCSS,c.themedCSS||{}),k=k===undefined?c.message:k,h&&s&&a(window,{fadeOut:0}),k&&"string"!=typeof k&&(k.parentNode||k.jquery)){var y=k.jquery?k[0]:k,m={};e(d).data("blockUI.history",m),m.el=y,m.parent=y.parentNode,m.display=y.style.display,m.position=y.style.position,m.parent&&m.parent.removeChild(y)}e(d).data("blockUI.onUnblock",c.onUnblock);var g,v,I,w,U=c.baseZ;g=o||c.forceIframe?e('<iframe class="blockUI" style="z-index:'+U+++';display:none;border:none;margin:0;padding:0;position:absolute;width:100%;height:100%;top:0;left:0" src="'+c.iframeSrc+'"></iframe>'):e('<div class="blockUI" style="display:none"></div>'),v=c.theme?e('<div class="blockUI blockOverlay ui-widget-overlay" style="z-index:'+U+++';display:none"></div>'):e('<div class="blockUI blockOverlay" style="z-index:'+U+++';display:none;border:none;margin:0;padding:0;width:100%;height:100%;top:0;left:0"></div>'),c.theme&&h?(w='<div class="blockUI '+c.blockMsgClass+' blockPage ui-dialog ui-widget ui-corner-all" style="z-index:'+(U+10)+';display:none;position:fixed">',c.title&&(w+='<div class="ui-widget-header ui-dialog-titlebar ui-corner-all blockTitle">'+(c.title||"&nbsp;")+"</div>"),w+='<div class="ui-widget-content ui-dialog-content"></div>',w+="</div>"):c.theme?(w='<div class="blockUI '+c.blockMsgClass+' blockElement ui-dialog ui-widget ui-corner-all" style="z-index:'+(U+10)+';display:none;position:absolute">',c.title&&(w+='<div class="ui-widget-header ui-dialog-titlebar ui-corner-all blockTitle">'+(c.title||"&nbsp;")+"</div>"),w+='<div class="ui-widget-content ui-dialog-content"></div>',w+="</div>"):w=h?'<div class="blockUI '+c.blockMsgClass+' blockPage" style="z-index:'+(U+10)+';display:none;position:fixed"></div>':'<div class="blockUI '+c.blockMsgClass+' blockElement" style="z-index:'+(U+10)+';display:none;position:absolute"></div>',I=e(w),k&&(c.theme?(I.css(b),I.addClass("ui-widget-content")):I.css(u)),c.theme||v.css(c.overlayCSS),v.css("position",h?"fixed":"absolute"),(o||c.forceIframe)&&g.css("opacity",0);var x=[g,v,I],C=e(h?"body":d);e.each(x,function(){this.appendTo(C)}),c.theme&&c.draggable&&e.fn.draggable&&I.draggable({handle:".ui-dialog-titlebar",cancel:"li"});var S=i&&(!e.support.boxModel||e("object,embed",h?null:d).length>0);if(n||S){if(h&&c.allowBodyStretch&&e.support.boxModel&&e("html,body").css("height","100%"),(n||!e.support.boxModel)&&!h)var E=p(d,"borderTopWidth"),O=p(d,"borderLeftWidth"),T=E?"(0 - "+E+")":0,M=O?"(0 - "+O+")":0;e.each(x,function(e,t){var o=t[0].style;if(o.position="absolute",e<2)h?o.setExpression("height","Math.max(document.body.scrollHeight, document.body.offsetHeight) - (jQuery.support.boxModel?0:"+c.quirksmodeOffsetHack+') + "px"'):o.setExpression("height",'this.parentNode.offsetHeight + "px"'),h?o.setExpression("width",'jQuery.support.boxModel && document.documentElement.clientWidth || document.body.clientWidth + "px"'):o.setExpression("width",'this.parentNode.offsetWidth + "px"'),M&&o.setExpression("left",M),T&&o.setExpression("top",T);else if(c.centerY)h&&o.setExpression("top",'(document.documentElement.clientHeight || document.body.clientHeight) / 2 - (this.offsetHeight / 2) + (blah = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "px"'),o.marginTop=0;else if(!c.centerY&&h){var n="((document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "+(c.css&&c.css.top?parseInt(c.css.top,10):0)+') + "px"';o.setExpression("top",n)}})}if(k&&(c.theme?I.find(".ui-widget-content").append(k):I.append(k),(k.jquery||k.nodeType)&&e(k).show()),(o||c.forceIframe)&&c.showOverlay&&g.show(),c.fadeIn){var B=c.onBlock?c.onBlock:t,j=c.showOverlay&&!k?B:t,H=k?B:t;c.showOverlay&&v._fadeIn(c.fadeIn,j),k&&I._fadeIn(c.fadeIn,H)}else c.showOverlay&&v.show(),k&&I.show(),c.onBlock&&c.onBlock.bind(I)();if(r(1,d,c),h?(s=I[0],l=e(c.focusableElements,s),c.focusInput&&setTimeout(f,20)):function(e,t,o){var n=e.parentNode,i=e.style,s=(n.offsetWidth-e.offsetWidth)/2-p(n,"borderLeftWidth"),l=(n.offsetHeight-e.offsetHeight)/2-p(n,"borderTopWidth");t&&(i.left=s>0?s+"px":"0");o&&(i.top=l>0?l+"px":"0")}(I[0],c.centerX,c.centerY),c.timeout){var z=setTimeout(function(){h?e.unblockUI(c):e(d).unblock(c)},c.timeout);e(d).data("blockUI.timeout",z)}}}function a(t,o){var n,i,d=t==window,a=e(t),u=a.data("blockUI.history"),f=a.data("blockUI.timeout");f&&(clearTimeout(f),a.removeData("blockUI.timeout")),o=e.extend({},e.blockUI.defaults,o||{}),r(0,t,o),null===o.onUnblock&&(o.onUnblock=a.data("blockUI.onUnblock"),a.removeData("blockUI.onUnblock")),i=d?e(document.body).children().filter(".blockUI").add("body > .blockUI"):a.find(">.blockUI"),o.cursorReset&&(i.length>1&&(i[1].style.cursor=o.cursorReset),i.length>2&&(i[2].style.cursor=o.cursorReset)),d&&(s=l=null),o.fadeOut?(n=i.length,i.stop().fadeOut(o.fadeOut,function(){0==--n&&c(i,u,o,t)})):c(i,u,o,t)}function c(t,o,n,i){var s=e(i);if(!s.data("blockUI.isBlocked")){t.each(function(e,t){this.parentNode&&this.parentNode.removeChild(this)}),o&&o.el&&(o.el.style.display=o.display,o.el.style.position=o.position,o.el.style.cursor="default",o.parent&&o.parent.appendChild(o.el),s.removeData("blockUI.history")),s.data("blockUI.static")&&s.css("position","static"),"function"==typeof n.onUnblock&&n.onUnblock(i,n);var l=e(document.body),d=l.width(),a=l[0].style.width;l.width(d-1).width(d),l[0].style.width=a}}function r(t,o,n){var i=o==window,l=e(o);if((t||(!i||s)&&(i||l.data("blockUI.isBlocked")))&&(l.data("blockUI.isBlocked",t),i&&n.bindEvents&&(!t||n.showOverlay))){var d="mousedown mouseup keydown keypress keyup touchstart touchend touchmove";t?e(document).on(d,n,u):e(document).off(d,u)}}function u(t){if("keydown"===t.type&&t.keyCode&&9==t.keyCode&&s&&t.data.constrainTabKey){var o=l,n=!t.shiftKey&&t.target===o[o.length-1],i=t.shiftKey&&t.target===o[0];if(n||i)return setTimeout(function(){f(i)},10),!1}var d=t.data,a=e(t.target);return a.hasClass("blockOverlay")&&d.onOverlayClick&&d.onOverlayClick(t),a.parents("div."+d.blockMsgClass).length>0||0===a.parents().children().filter("div.blockUI").length}function f(e){if(l){var t=l[!0===e?l.length-1:0];t&&t.trigger("focus")}}function p(t,o){return parseInt(e.css(t,o),10)||0}}"function"==typeof define&&define.amd&&define.amd.jQuery?define(["jquery"],e):e(jQuery)}();
;