!function(t,e){"function"==typeof define&&define.amd?define(e):"object"==typeof exports?module.exports=e():t.$clamp=e()}(this,function(){return function(t,e){function n(t,e){p.getComputedStyle||(p.getComputedStyle=function(t){return this.el=t,this.getPropertyValue=function(e){var n=/(\-([a-z]){1})/g;"float"==e&&(e="styleFloat"),n.test(e)&&(e=e.replace(n,function(){return arguments[2].toUpperCase()})),t.currentStyle[e]?t.currentStyle[e]:null},this});var n=p.getComputedStyle(t),r=n.getPropertyValue("-webkit-line-clamp"),o=n.getPropertyValue("overflow");if(r&&"ellipsis"==n.getPropertyValue("-webkit-box-orient")&&"hidden"==o){var i=parseInt(r,10);if(i)return void(t.style.webkitLineClamp=e.clamp,c(t)&&(t.style.webkitLineClamp=""));var a=t.style.webkitLineClamp;return t.style.webkitLineClamp="auto",t.style.webkitLineClamp=a}var l=e.clamp;"auto"==l?l=d(t):l.indexOf("px")>0?l=d(t,parseInt(l)):l.indexOf("em")>0&&(l=d(t,parseFloat(l)*s(t)));var u=i(t,l),f=t.innerHTML,h=m(t,u);h!=f&&(t.innerHTML=h,v(t,f))}function i(t,e){var n=document.createElement("div");n.style.position="absolute",n.style.left="-9999px",n.style.top="-9999px",n.style.width=t.clientWidth+"px",n.style.height=e+"px",document.body.appendChild(n);for(var i=n.getClientRects()[0].height,r=0;r<1e3&&i>e;)n.style.height=--i+"px";return document.body.removeChild(n),i}function r(t,e,n){return e.nodeType==Node.TEXT_NODE?o(t,e,n):e.nodeType==Node.ELEMENT_NODE?a(t,e,n):void 0}function o(t,e,n){var i=e.nodeValue.replace(y,"");if(n.lastChild=e,n.text=i,b.indexOf(i,n.text)>-1)return n.text=b.substring(0,b.indexOf(i,n.text)),n;var r=l(i.split(g));return r.length>1?u(t,e,r,n):void 0}function a(t,e,n){for(var i=e.childNodes,o=i.length,a=null,l=0;o>l;l++)if(a=r(t,i[l],n),a)return a;return!1}function l(t){return t.filter(function(t){var e=t.trim();return""!=e&&!isNaN(e.charAt(0))})}function u(t,e,n,i){var o=n.pop();return c(t,o)?(e.nodeValue=n.join(" "),i):e.nodeValue.length>o.length?(e.nodeValue=n.join(" ")+o.substring(0,e.nodeValue.length-o.length),u(t,e,n.concat([o]),i)):void 0}function c(t,e){return"undefined"!=typeof e?(t.nodeValue=e,t):t}function d(t,e){var n=e||t.clientHeight,r=f(t);return Math.max(Math.floor(n/r),0)}function s(t){var e=p.getComputedStyle(t).getPropertyValue("font-size");return parseFloat(e)}function f(t){var e=p.getComputedStyle(t).getPropertyValue("line-height");return"normal"==e&&(e=1.2*s(t)),parseFloat(e)}function m(t,e){var n=t.cloneNode(!0),i=document.createElement("div"),r=document.createElement("span"),o="",a=!1;if(!e)return t.innerHTML;n.style.position="absolute",n.style.left="-9999px",n.style.visibility="hidden",i.style.display="inline-block",i.style.width=n.style.width=t.clientWidth+"px",i.style.height=n.style.height=t.clientHeight+"px",r.style.position="absolute",r.style.left="-9999px",r.style.visibility="hidden",document.body.appendChild(n),document.body.appendChild(i),document.body.appendChild(r);for(var l="",u=t.childNodes,d=u.length,s=0;d>s;s++)o+=u[s].outerHTML||u[s].nodeValue;for(var f=o.split(" "),m=f.length,p=m;p>0;p--)l=f.slice(0,p).join(" "),r.innerHTML=l+e.truncationChar,r.clientHeight>i.clientHeight&&(a=!0,p=0);if(!a)return t.innerHTML;for(var h=l.split(""),g=h.length,v=g;v>0;v--)l=h.slice(0,v).join(""),r.innerHTML=l+e.truncationChar,r.clientHeight<=i.clientHeight&&(a=!0,p=0);return document.body.removeChild(n),document.body.removeChild(i),document.body.removeChild(r),l+e.truncationHTML+e.truncationChar}function p(t,e){e=e||{};var r={clamp:e.clamp||2,useNativeClamp:!0,splitOnChars:[".","-","–","—"," "],truncationChar:e.truncationChar||"…",truncationHTML:e.truncationHTML,animate:!1};if(t.length)for(var o=0;o<t.length;o++)n(t[o],r);else n(t,r)}var h={};h.add=function(t,e){return h[t]?void 0:h[t]=e},h.get=function(t){return h[t]};var g=/(\s|-|\.|,|;|\?|!)/,v=function(t,e){function n(){c(t),t.innerHTML=e,h.add(t,e)}var i=r.truncationHTML.match(/<a/i);i?(t.onclick=n,t.onkeydown=function(t){13==t.keyCode&&(n(),t.preventDefault())}):t.ondblclick=n},b="undefined"!=typeof t.textContent?t.textContent:t.innerText,y=/\s\s/g,p=window;return p}});

import { generateClientSideTOC } from './modules/toc.js';
import { initDarkMode } from './modules/darkMode.js';
import { setupFloatingButtons } from './modules/floatingButtons.js';
import { setupSidebar } from './modules/sidebar.js';
import { setupURLCopy } from './modules/copyUrl.js';
import { setupProgressBar } from './modules/progressBar.js';
import { setupStarRating } from './modules/starRating.js';
import { setupReactionButtons } from './modules/reactions.js';
import { setupInfiniteScroll, setupSeriesLoadMoreButton } from './modules/infiniteScroll.js';
// version 1.2
import { setupLazyLoading } from './modules/lazyLoad.js';
import { initAllAds } from '../../components/ads/ads.js';
import { setupPostedDateToggles, setupLanguageToggle, setupCodeCopyButtons, removeProblematicAriaLabel } from './modules/utils.js';

'use strict';

const $ = window.jQuery;

initDarkMode();
document.addEventListener('DOMContentLoaded', setupFloatingButtons);
setupSidebar();
setupURLCopy();
setupProgressBar();
setupStarRating($);
setupReactionButtons($);
setupPostedDateToggles();
setupLanguageToggle();
setupCodeCopyButtons();
setupLazyLoading();
setupInfiniteScroll($);
removeProblematicAriaLabel();
setupSeriesLoadMoreButton($);

if (document.getElementById('gp-toc-container')) {
    generateClientSideTOC();
}

// Initialize ads after the window and all its resources have finished loading.
window.onload = function() {
    initAllAds();
};

document.addEventListener('DOMContentLoaded', function() {
    const emailPrivacyCheckbox = document.getElementById('wp-comment-email-privacy');
    const emailField = document.getElementById('email');

    if (emailPrivacyCheckbox && emailField) {
        const req = emailField.hasAttribute('aria-required');

        const updateEmailFieldState = () => {
            if (emailPrivacyCheckbox.checked) {
                emailField.disabled = true;
                emailField.required = false;
                emailField.value = '';
            } else {
                emailField.disabled = false;
                if (req) {
                    emailField.required = true;
                }
            }
        };

        updateEmailFieldState();
        emailPrivacyCheckbox.addEventListener('change', updateEmailFieldState);
    }

    // Smooth scroll to comment anchor
    if (window.location.hash && window.location.hash.indexOf('#comment-') === 0) {
        const commentId = window.location.hash;
        const commentElement = document.querySelector(commentId);
        if (commentElement) {
            setTimeout(() => {
                commentElement.scrollIntoView({ behavior: 'smooth' });
            }, 500);
        }
    }
});
