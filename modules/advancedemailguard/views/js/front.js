/**
 * Advanced Anti Spam PrestaShop Module.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    ReduxWeb
 * @copyright 2017-2022 reduxweb.net
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
var AdvancedEmailGuard=function(){"use strict";function a(){return t.split(" ").map(function(t){return String.fromCharCode(t)}).join("").replace("{{date}}",(new Date).getFullYear())}function o(){if(p.isObj(s.recaptcha.forms)){if(v("contact_us",n.ps.v17pc?"form:has(button[name=submitMessage], input[name=submitMessage])":"form:not(#id_new_comment_form):has(button[name=submitMessage], input[name=submitMessage])"),v("register",n.ps.v17?"body:not(#checkout):not(#identity) form:has(button[name=submitCreate], input[name=submitCreate])":S()?"#opc_account_form:visible:has(button[name=submitAccount], input[name=submitAccount])":"form:has(button[name=submitAccount], input[name=submitAccount])"),v("login",n.ps.v17?"form:has(button[name=submitLogin], input[name=submitLogin])":"form:has(button[name=SubmitLogin], input[name=SubmitLogin])"),v("reset_password",n.ps.v17?"form.forgotten-password, body#password #content form:first":"#form_forgotpassword",n.ps.v17?"button[type=submit]:visible, input[type=submit]:visible":"button[type=submit], input[type=submit]"),v("quick_order",n.ps.v17?"body#checkout form:has(button[name=submitCreate], input[name=submitCreate])":S()?"#opc_account_form:visible:has(button[name=submitGuestAccount], input[name=submitGuestAccount])":"#new_account_form"),v("newsletter",n.ps.v15?"form:has(button[name=submitNewsletter], input[name=submitNewsletter]):visible":"form:has(button[name=submitNewsletter], input[name=submitNewsletter])",n.ps.v17?"button[type=submit]:visible, input[type=submit]:visible":"button[type=submit], input[type=submit]"),v("write_review",n.ps.v17pc?"#post-product-comment-form":"#id_new_comment_form"),v("notify_when_in_stock",c.isLegacyMAModuleEnabled?"form:has(#mailalert_link)":".js-mailalert",c.isLegacyMAModuleEnabled?"":"button[type=submit], input[type=submit], button:first"),v("send_to_friend","#send_friend_form_content"),s.recaptcha.hidden&&$("#"+f).length===0)s.recaptcha.hidden=false;for(var t in s.recaptcha.forms){p.recaptchaForms[t]=[];switch(t){case"register":if(!n.ps.v17){$(document).ajaxSuccess(function(t,e,i,a){var o=p.queryToObj(i.data);if(o.controller==="authentication"&&p.isDef(o.SubmitCreate)&&!a.hasError)setTimeout(function(){p.initRecaptchaForm("register")},700)});if(S()){$(document).on("click","#opc_createAccount",function(){setTimeout(function(){p.initRecaptchaForm("register")},700)});$(document).ajaxSend(function(t,e,i){var a=p.queryToObj(i.data);if(a.ajax==="true"&&p.isDef(a.submitAccount)&&a.is_new_customer==="1")i.data+=p.getRecFormTokenAsPayload("register")})}}break;case"login":if(S())$(document).ajaxSend(function(t,e,i){var a=p.queryToObj(i.data);if(a.ajax==="true"&&p.isDef(a.SubmitLogin))i.data+=p.getRecFormTokenAsPayload("login")});break;case"quick_order":if(S()){$(document).on("click","#opc_guestCheckout",function(){setTimeout(function(){p.initRecaptchaForm("quick_order")},700)});$(document).ajaxSend(function(t,e,i){var a=p.queryToObj(i.data);if(a.ajax==="true"&&p.isDef(a.submitAccount)&&a.is_new_customer==="0")i.data+=p.getRecFormTokenAsPayload("quick_order")})}break;case"notify_when_in_stock":var r=c.isLegacyMAModuleEnabled?"mailalerts":"ps_emailalerts";$(document).ajaxSend(function(t,e,i){if(i.url.includes(r)&&i.url.includes("process=add"))i.data+=p.getRecFormTokenAsPayload("notify_when_in_stock")});if(!c.isLegacyMAModuleEnabled)$(document).ajaxSuccess(function(t,e,i,a){var o=p.queryToObj(i.data);if(i.url.includes("controller=product")&&o.action==="refresh")p.initRecaptchaForm("notify_when_in_stock")});if(k())$(document).ajaxSuccess(function(t,e,i,a){if(i.url.includes(r)&&/process=(add|check)/.test(i.url)){var o=false;if(c.isLegacyMAModuleEnabled)o=a==="1";else try{var n=JSON.parse(a);o=n.error===false}catch(t){}if(o&&p.isDef(p.recaptchaForms["notify_when_in_stock"]))p.recaptchaForms["notify_when_in_stock"].forEach(function(t){if(!t.$submit.is(":visible"))t.hideWidget()})}});break;case"send_to_friend":$(document).ajaxSend(function(t,e,i){var a=p.queryToObj(i.data);if(a.action==="sendToMyFriend")i.data+=p.getRecFormTokenAsPayload("send_to_friend")});break}}s.recaptcha.deferred&&!function(){var t=[],e;for(e in s.recaptcha.forms)if(p.isDef(b[e]))t.push(b[e].form);return t.length!==0&&$(t.join(",")).length!==0}()||e(i)}}function e(t){var e,i;y||(e="?render=explicit&onload="+m,"shop"===s.recaptcha.language&&(e+="&hl="+n.languageCode),i=t,window[m]=function(){y||(y=!0,R(),"function"==typeof p.googleRecaptchaCallbackBefore&&p.googleRecaptchaCallbackBefore(),i(),"function"==typeof p.googleRecaptchaCallback&&p.googleRecaptchaCallback())},$("head").append('<script src="https://www.google.com/recaptcha/api.js'+e+'"><\/script>'))}function i(){for(var t in s.recaptcha.forms)p.initRecaptchaForm(t)}var c,s,n,r,p={},t="65 100 118 97 110 99 101 100 32 65 110 116 105 32 83 112 97 109 32 71 111 111 103 108 101 32 114 101 67 65 80 84 67 72 65 32 40 99 41 32 67 111 112 121 114 105 103 104 116 32 50 48 49 55 45 50 48 50 48 32 82 101 100 117 120 87 101 98 32 119 119 119 46 114 101 100 117 120 119 101 98 46 110 101 116",h="AdvancedEmailGuard",u=h+"Data",m=h+"InitRecaptcha",d="g-recaptcha-response",l="adveg-grecaptcha",f="adveg-grecaptcha-legal",b=(p.booted=!1,p.recaptchaForms={},p.googleRecaptchaCallback=null,p.googleRecaptchaCallbackBefore=null,{}),g={},y=!1,F=0,v=(p.extends=function(t,e){t.prototype=Object.create(e.prototype),t.prototype.constructor=t},p.isObj=function(t){return null!==t&&"object"==typeof t&&!Array.isArray(t)},p.isDef=function(t){return null!=t},p.queryToObj=function(t){var e={};return"string"==typeof t&&(t=0===(t=t.replace(/^\&+|\&+$/g,"")).lastIndexOf("?")?t.substr(1):t).split("&").forEach(function(t){t=t.split("=");e[t[0]]=decodeURIComponent(t[1])}),e},p.strToStudly=function(t){return t.replace(/^[a-z]|([-_][a-z])/gi,function(t){return t.toUpperCase().replace("-","").replace("_","")})},function(t,e,i){b[t]||p.registerFormSelectors(t,e,i)}),R=(p.registerFormSelectors=function(t,e,i){b[t]={form:e,submit:i||"button[type=submit], input[type=submit]"}},function(){var t,e;"v3"===s.recaptcha.type&&(0===(e=$("#"+l)).length&&(t="inline"===s.recaptcha.position?"inline":"fixed",e=$('<div id="'+l+'" class="adveg-grecaptcha-'+t+'" />'),$("body").append(e)),F=grecaptcha.render(l,{sitekey:s.recaptcha.key,badge:s.recaptcha.position,theme:s.recaptcha.theme,size:"invisible"}),s.recaptcha.hidden&&e.hide())}),k=(p.initRecaptchaForm=function(i){if(!p.isDef(b[i]))throw new Error('No selector defined for the "'+i+'" form type.');var a=b[i],o=s.recaptcha.forms[i];if(!p.isDef(o))throw new Error('No params defined for the "'+i+'" form type.');var n=p.strToStudly(i)+"Form",t=(p.isDef(p[n])||(n="RecaptchaForm"),$(a.form));0!==t.length&&(y?t.each(function(){var e=$(this),t=e.find(a.submit).first();p.recaptchaForms[i].some(function(t){return e.is(t.$form)})||p.recaptchaForms[i].push(new p[n](i,o,e,t))}):e(function(){p.initRecaptchaForm(i)}))},p.getRecFormWithToken=function(t){if(p.isDef(p.recaptchaForms[t]))for(var e=0;e<p.recaptchaForms[t].length;e++)if(""!==p.recaptchaForms[t][e].token)return p.recaptchaForms[t][e];return null},p.getRecFormTokenAsPayload=function(t){var e,t=p.getRecFormWithToken(t);return null===t?"":(e=t.token,t.onRecaptchaTokenExpired(),"&"+d+"="+e)},p.getRecV3ID=function(){return F},function(){return"v2_cbx"===s.recaptcha.type||"inline"===s.recaptcha.position}),S=function(){return c.isLegacyOPCEnabled&&"order-opc"===n.pageName};return p.RecaptchaForm=function(t,e,i,a){this.recID=0,this.name=t,this.params=e,this.$form=i,this.$submit=a,this.$inner=null,this.$outer=null,this.token="",this.origHandler=null,this.$gdprCbx=null,this.init()},p.RecaptchaForm.prototype.getFormElement=function(){return this.$form.get(0)},p.RecaptchaForm.prototype.getSubmitElement=function(){return this.$submit.get(0)},p.RecaptchaForm.prototype.init=function(){this.setSubmitHandler(),"v2_cbx"===s.recaptcha.type&&(this.setGDPRHandler(),this.disableSubmitBtn()),"v3"!==s.recaptcha.type&&this.addRecaptchaWidget()},p.RecaptchaForm.prototype.disableSubmitBtn=function(){this.$submit.prop("disabled",!0).addClass("adveg-grecaptcha-btn-disabled")},p.RecaptchaForm.prototype.enableSubmitBtn=function(){this.$submit.prop("disabled",!1).removeClass("adveg-grecaptcha-btn-disabled")},p.RecaptchaForm.prototype.setGDPRHandler=function(){var t=this.$form.find("input[name=psgdpr_consent_checkbox]");0!==t.length&&(this.$gdprCbx=t,t="."+this.$gdprCbx.attr("class"),$(document).off("change",t),this.$gdprCbx.on("change",this.onGDPRCheckboxChange.bind(this)))},p.RecaptchaForm.prototype.onGDPRCheckboxChange=function(){""!==this.token&&this.isGDPRChecked()?this.enableSubmitBtn():this.disableSubmitBtn()},p.RecaptchaForm.prototype.isGDPRChecked=function(){return null===this.$gdprCbx||this.$gdprCbx.is(":checked")},p.RecaptchaForm.prototype.setSubmitHandler=function(){this.$form.submit(this.onFormSubmit.bind(this))},p.RecaptchaForm.prototype.onFormSubmit=function(t){"v2_cbx"!==s.recaptcha.type&&(""!==this.token?this.insertInputWithToken():("v3"===s.recaptcha.type?(this.disableSubmitBtn(),grecaptcha.execute(F,{action:this.name}).then(this.onRecaptchaToken.bind(this),this.onRecaptchaError.bind(this))):grecaptcha.execute(this.recID),t.preventDefault()))},p.RecaptchaForm.prototype.setSubmitFormHandler=function(){var t=this.getFormElement();if(!p.isDef(g[this.name])){var e=$._data(t,"events");if(!p.isDef(e)||!Array.isArray(e.submit)||0===e.submit.length)return;g[this.name]=e.submit[0].handler}this.origHandler=g[this.name].bind(t),this.$form.off("submit"),this.$form.submit(this.onFormSubmitProxy.bind(this))},p.RecaptchaForm.prototype.setSubmitClickHandler=function(){if(!p.isDef(g[this.name])){var t=$._data(this.getSubmitElement(),"events");if(!p.isDef(t)||!Array.isArray(t.click)||0===t.click.length)return;g[this.name]=t.click[0].handler}this.origHandler=g[this.name],this.$submit.off("click"),this.$submit.click(this.onFormSubmitProxy.bind(this))},p.RecaptchaForm.prototype.setDocClickHandler=function(e){if(!p.isDef(g[this.name])){var t=$._data(document,"events").click.find(function(t){return t.selector===e});if(!p.isDef(t))return;g[this.name]=t.handler}this.origHandler=g[this.name],$(document).off("click",e),$(document).on("click",e,this.onFormSubmitProxy.bind(this))},p.RecaptchaForm.prototype.setGlobalFuncHandler=function(t){if(!p.isDef(g[this.name])){if(!p.isDef(window[t]))return;g[this.name]=window[t]}this.origHandler=g[this.name],window[t]=this.onFormSubmitProxy.bind(this)},p.RecaptchaForm.prototype.onFormSubmitProxy=function(t){var e;return"v2_cbx"===s.recaptcha.type?(e=this.origHandler(t),this.disableSubmitBtn(),grecaptcha.reset(this.recID)):""===this.token?("v3"===s.recaptcha.type?(this.disableSubmitBtn(),grecaptcha.execute(F,{action:this.name}).then(this.onRecaptchaToken.bind(this),this.onRecaptchaError.bind(this))):grecaptcha.execute(this.recID),p.isObj(t)&&t.preventDefault&&t.preventDefault()):(this.insertInputWithToken(),e=this.origHandler(t),"v3"===s.recaptcha.type?this.onRecaptchaTokenExpired():grecaptcha.reset(this.recID)),p.isDef(e)?e:!!p.isDef(t)&&void 0},p.RecaptchaForm.prototype.onRecaptchaToken=function(t){this.token=t,("v3"===s.recaptcha.type||"v2_cbx"===s.recaptcha.type&&this.isGDPRChecked())&&this.enableSubmitBtn(),"v2_cbx"!==s.recaptcha.type&&this.submitForm()},p.RecaptchaForm.prototype.onRecaptchaTokenExpired=function(){this.token="","v2_cbx"===s.recaptcha.type&&this.disableSubmitBtn()},p.RecaptchaForm.prototype.onRecaptchaError=function(){"v3"===s.recaptcha.type&&this.enableSubmitBtn(),alert("reCAPTCHA: "+r.genericError)},p.RecaptchaForm.prototype.insertInputWithToken=function(){var t;"v3"===s.recaptcha.type&&(0===(t=this.$form.find("textarea[name="+d+"]")).length&&(t=$('<textarea name="'+d+'" style="display: none;"></textarea>'),this.$form.append(t)),t.val(this.token))},p.RecaptchaForm.prototype.submitForm=function(){this.$submit.click()},p.RecaptchaForm.prototype.addRecaptchaWidget=function(){this.createRecaptchaContainer(),this.insertRecaptchaContainer(),this.renderRecaptchaWidget()},p.RecaptchaForm.prototype.createRecaptchaContainer=function(){var t;this.$inner=$('<div class="adveg-grecaptcha" />'),this.$outer=this.$inner,k()?(this.$inner.addClass("adveg-grecaptcha-inline"),t=this.$submit.parent().parent().hasClass("row"),"offset"===this.params.align?(this.$outer=this.$inner.wrap("<div />").parent(),this.$outer.addClass("col-md-"+(12-this.params.offset)+" col-md-offset-"+this.params.offset+" offset-md-"+this.params.offset),t||(this.$outer=this.$outer.wrap('<div class="row" />').parent())):(this.$inner.addClass("adveg-grecaptcha-"+this.params.align),t&&(this.$outer=this.$outer.wrap('<div class="col-xs-12" />').parent()))):this.$inner.addClass("adveg-grecaptcha-fixed")},p.RecaptchaForm.prototype.insertRecaptchaContainer=function(){(this.$submit.parent().is(this.$form)?this.$submit:this.$submit.parent()).before(this.$outer),k()&&this.$outer.before('<div class="clearfix" />')},p.RecaptchaForm.prototype.renderRecaptchaWidget=function(){this.recID=grecaptcha.render(this.getWidgetContainer(),this.getRecaptchaParams()),s.recaptcha.hidden&&this.hideWidget()},p.RecaptchaForm.prototype.getRecaptchaParams=function(){var t={sitekey:s.recaptcha.key,callback:this.onRecaptchaToken.bind(this),"expired-callback":this.onRecaptchaTokenExpired.bind(this),"error-callback":this.onRecaptchaError.bind(this),theme:s.recaptcha.theme};return"v2_cbx"===s.recaptcha.type?t.size=this.params.size:(t.size="invisible",t.isolated=!0,t.badge=s.recaptcha.position),t},p.RecaptchaForm.prototype.getWidgetContainer=function(){return this.$inner.get(0)},p.RecaptchaForm.prototype.hideWidget=function(){"v3"!==s.recaptcha.type&&this.$outer.hide()},p.RegisterForm=function(t,e,i,a){p.RecaptchaForm.call(this,t,e,i,a)},p.extends(p.RegisterForm,p.RecaptchaForm),p.RegisterForm.prototype.setSubmitHandler=function(){S()?n.ps.v16?this.setDocClickHandler("#submitAccount, #submitGuestAccount"):this.setSubmitClickHandler():p.RecaptchaForm.prototype.setSubmitHandler.call(this)},p.RegisterForm.prototype.insertRecaptchaContainer=function(){n.ps.v15&&!S()&&0!==this.$form.find("fieldset").length?this.$form.find("fieldset:last").append(this.$outer):p.RecaptchaForm.prototype.insertRecaptchaContainer.call(this)},p.LoginForm=function(t,e,i,a){p.RecaptchaForm.call(this,t,e,i,a)},p.extends(p.LoginForm,p.RecaptchaForm),p.LoginForm.prototype.onFormSubmit=function(t){n.ps.v17ch&&"order"===n.pageName&&this.$form.data("disabled",!1),p.RecaptchaForm.prototype.onFormSubmit.call(this,t)},p.LoginForm.prototype.setSubmitHandler=function(){S()?n.ps.v16?this.setDocClickHandler("#SubmitLogin"):this.setSubmitClickHandler():p.RecaptchaForm.prototype.setSubmitHandler.call(this)},p.QuickOrderForm=function(t,e,i,a){p.RecaptchaForm.call(this,t,e,i,a)},p.extends(p.QuickOrderForm,p.RecaptchaForm),p.QuickOrderForm.prototype.setSubmitHandler=function(){S()?n.ps.v16?this.setDocClickHandler("#submitAccount, #submitGuestAccount"):this.setSubmitClickHandler():p.RecaptchaForm.prototype.setSubmitHandler.call(this)},p.QuickOrderForm.prototype.onFormSubmit=function(t){n.ps.v17ch&&this.$form.data("disabled",!1),p.RecaptchaForm.prototype.onFormSubmit.call(this,t)},p.QuickOrderForm.prototype.insertRecaptchaContainer=function(){n.ps.v16&&!S()&&0!==this.$form.find(".box").length?this.$form.find(".box:last").append(this.$outer):n.ps.v15&&!S()&&0!==this.$form.find("fieldset:last").length?this.$form.find("fieldset:last").append(this.$outer):p.RecaptchaForm.prototype.insertRecaptchaContainer.call(this)},p.NewsletterForm=function(t,e,i,a){p.RecaptchaForm.call(this,t,e,i,a)},p.extends(p.NewsletterForm,p.RecaptchaForm),p.NewsletterForm.prototype.insertRecaptchaContainer=function(){"flex"===this.$submit.closest("div").css("display")?this.$submit.closest("div").before(this.$outer):p.RecaptchaForm.prototype.insertRecaptchaContainer.call(this)},p.NewsletterForm.prototype.setSubmitHandler=function(){p.isDef(window.psemailsubscription_subscription)&&p.isDef($._data(this.getFormElement(),"events"))?this.setSubmitFormHandler():p.RecaptchaForm.prototype.setSubmitHandler.call(this)},p.WriteReviewForm=function(t,e,i,a){p.RecaptchaForm.call(this,t,e,i,a)},p.extends(p.WriteReviewForm,p.RecaptchaForm),p.WriteReviewForm.prototype.setSubmitHandler=function(){n.ps.v17pc?this.setSubmitFormHandler():n.ps.v16?this.setDocClickHandler("#"+this.$submit.attr("id")):this.setSubmitClickHandler()},p.WriteReviewForm.prototype.insertRecaptchaContainer=function(){n.ps.v17pc?(this.$submit.parent().before(this.$outer),k()?this.$submit.closest(".post-comment-buttons").addClass("offset-md-6 offset-sm-6 col-md-offset-6 col-sm-offset-6"):function(){for(var t in p.recaptchaForms)if(0<p.recaptchaForms[t].length)return!0;return!1}()||this.$form.closest(".modal-dialog").attr("style","-webkit-transform: none!important; transform: none!important;")):p.RecaptchaForm.prototype.insertRecaptchaContainer.call(this)},p.NotifyWhenInStockForm=function(t,e,i,a){p.RecaptchaForm.call(this,t,e,i,a)},p.extends(p.NotifyWhenInStockForm,p.RecaptchaForm),p.NotifyWhenInStockForm.prototype.setSubmitHandler=function(){!c.isLegacyMAModuleEnabled&&this.$submit.hasClass("js-mailalert-add")?(this.setDocClickHandler(".js-mailalert-add"),this.origHandler&&(this.origHandler=this.origHandler.bind(this.getSubmitElement()))):this.setGlobalFuncHandler("addNotification")},p.NotifyWhenInStockForm.prototype.insertRecaptchaContainer=function(){this.$submit.before(this.$outer)},p.SendToFriendForm=function(t,e,i,a){p.RecaptchaForm.call(this,t,e,i,a)},p.extends(p.SendToFriendForm,p.RecaptchaForm),p.SendToFriendForm.prototype.setSubmitHandler=function(){this.setSubmitClickHandler()},p.run=function(){var t,e,i;return p.booted||(p.isObj(window[u])&&(i=window[u],c=i.meta,s=i.settings,n=i.context,r=i.trans,null!==c.validationError&&(t=0,(e=$('<div id="adveg-validation-failed" style="display: none;"><div><div>'+c.validationError+'</div><button type="button"><span>&times;</span></button></div></div>')).appendTo("body"),e.find("button:first").on("click",function(){clearTimeout(t),e.slideUp(200)}),setTimeout(function(){e.slideDown(200),t=setTimeout(function(){e.slideUp(200)},4500)},700)),o()),p.booted=!0),a},p}();document.addEventListener("DOMContentLoaded",AdvancedEmailGuard.run),String.prototype.includes||(String.prototype.includes=function(t,e){"use strict";if(t instanceof RegExp)throw TypeError("first argument must not be a RegExp");return-1!==this.indexOf(t,e=void 0===e?0:e)});
