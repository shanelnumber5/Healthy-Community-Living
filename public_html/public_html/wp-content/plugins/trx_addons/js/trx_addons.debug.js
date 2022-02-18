/**
 * Debug utilities (for internal use only!)
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.0
 */

/* global jQuery:false */
/* global TRX_ADDONS_STORAGE:false */

(function() {

	"use strict";
	
	window.trx_addons_debug_object = function(obj) {
		var recursive = arguments[1]!==undefined ? arguments[1] : 1;		// Show inner objects (arrays) in depth
		var showMethods = arguments[2]!==undefined ? arguments[2] : false;	// Show object's methods
		var level = arguments[3]!==undefined ? arguments[3] : 0;			// Nesting level (for internal usage only)
		var dispStr = "";
		var addStr = "";
		var curStr = "";
		if (level > 0) {
			dispStr += (obj===null ? "null" : typeof(obj)) + "\n";
			addStr = trx_addons_replicate(' ', level*2);
		}
		if (obj!==null) {
			for (var prop in obj) {
				if (!showMethods && typeof(obj[prop])=='function')	// || prop=='innerHTML' || prop=='outerHTML' || prop=='innerText' || prop=='outerText')
					continue;
				if (level<recursive && (typeof(obj[prop])=='object' || typeof(obj[prop])=='array') && obj[prop]!=obj)
					dispStr += addStr + prop + '=' + trx_addons_debug_object(obj[prop], recursive, showMethods, level+1);
				else {
					try {
						curStr = "" + obj[prop];
					} catch (e) {
						curStr = "--- Not evaluate ---";
					}
					dispStr += addStr+prop+'='+(typeof(obj[prop])=='string' ? '"' : '')+curStr+(typeof(obj[prop])=='string' ? '"' : '')+"\n";
				}
			}
		}
		return dispStr;	//decodeURI(dispStr);
	};
	
	window.trx_addons_debug_log = function(s) {
		if (TRX_ADDONS_STORAGE['user_logged_in']) {
			if (jQuery('#debug_log').length == 0) {
				jQuery('body').append('<div id="debug_log"><span id="debug_log_close" onclick="jQuery(\'#debug_log\').hide();">x</span><div id="debug_log_content"></div></div>'); 
			}
			jQuery('#debug_log_content').append('<br><pre>'+s+'</pre>');
			jQuery('#debug_log').show();
		}
	};
	
	window.dcl===undefined && (window.dcl = function(s) { console.log(s); });
	window.dco===undefined && (window.dco = function(s,r) { console.log(trx_addons_debug_object(s,r)); });
	window.dal===undefined && (window.dal = function(s) { if (TRX_ADDONS_STORAGE['user_logged_in']) alert(s); });
	window.dao===undefined && (window.dao = function(s,r) { if (TRX_ADDONS_STORAGE['user_logged_in']) alert(trx_addons_debug_object(s,r)); });
	window.ddl===undefined && (window.ddl = function(s) { trx_addons_debug_log(s); });
	window.ddo===undefined && (window.ddo = function(s,r) { trx_addons_debug_log(trx_addons_debug_object(s,r)); });

})();