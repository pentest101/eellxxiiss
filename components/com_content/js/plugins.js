/* Component Content javascript */
/* Elxis CMS - http://www.elxis.org */
/* Ioannis Sannos (datahell) */

/* LOAD PLUGIN */
function loadPlugin(fn) {
	var selObj = document.getElementById('plugin');
	var plugid = selObj.options[selObj.selectedIndex].value;
	plugid = parseInt(plugid, 10);
	if (document.getElementById('plugincode')) { document.getElementById('plugincode').value = ''; }
	if (plugid < 1) {
		if (document.getElementById('plug_load')) { document.getElementById('plug_load').innerHTML = ''; }
		return false;
	}
	var edata = {'id':plugid, 'fn':fn, 'task':'load'};
	var eurl = document.getElementById('fmplgimport').action;
	var successfunc = function(xreply) {
		elx5StopPageLoader();
		loadPluginHead(plugid, eurl);
		document.getElementById('plug_load').innerHTML = xreply;
	}
	elx5StartPageLoader();
	elxAjax('POST', eurl, edata, 'plug_load', null, successfunc, null);
}

/* ADD PLUGIN CODE IN COMPOSER INPUT BOX */
function addPluginCode(pcode) {
	if (pcode == '') {
		alert('Plugin code can not be empty!');
		return false;
	}
	document.getElementById('plugincode').value = pcode;
}


function elx5PlugTabSwitch(idx, total) {
	for (var i=0; i < total; i++) {
		if (i == idx) {
			document.getElementById('elx5pluglink_'+i).className = 'elx5_tab_open';
			document.getElementById('elx5plugtab_'+i).className = 'elx5_tab_content';
		} else {
			document.getElementById('elx5pluglink_'+i).className = '';
			document.getElementById('elx5plugtab_'+i).className = 'elx5_invisible';
		}
	}
}

/* LOAD PLUGIN HEAD ELEMENTS */
function loadPluginHead(plugid, eurl) {
	var rnd = Math.random();
	var e2data = {'id':plugid, 'task':'head', 'rnd':rnd };
	var success2func = function(x2reply) {
		var jsonObj = JSON.parse(x2reply);
		if (parseInt(jsonObj.error, 10) > 0) {
			return false;
		} else {
			var len = jsonObj.css.length;
			if (len > 0) {
				for (var i = 0; i < len; i++) {
					var selem = document.createElement('link');
					selem.type = 'text/css';
					selem.href = jsonObj.css[i];
					selem.media = 'all';
					selem.rel = 'stylesheet';
					document.getElementsByTagName('head')[0].appendChild(selem);
				}
			}
			var len2 = jsonObj.js.length;
			if (len2 > 0) {
				for (var i = 0; i < len2; i++) {
					var s2elem = document.createElement('script');
					s2elem.src = jsonObj.js[i];
					document.getElementsByTagName('head')[0].appendChild(s2elem);
				}
			}
		}
	}
	elxAjax('POST', eurl, e2data, null, null, success2func, null);
}

function stopPCodehigh() {}/* DEPRECARED */

/* IMPORT PLUGIN CODE IN EDITOR */
function plugImportCode(fn) {
	if (fn == '123456') {
		alert('You can not import code to editor from this page!');
		return;
	}
	var pcode = document.getElementById('plugincode').value;
	if (pcode == '') {
		alert('Plugin code can not be empty!');
		return false;
	}
	if (window.opener) {
			if (typeof window.opener.Jodit.instances[fn] == "undefined") {
			} else {
				var editor = window.opener.Jodit.instances[fn];
				if (pcode.indexOf('{') > -1) {
					editor.selection.insertHTML('<code class="elx5_plugin">'+pcode+'</code>');
				} else {
					editor.selection.insertHTML(pcode);
				}
			}
			window.close();
	} else {
		alert('Editor instance was not found!');
	}
}
