/* COMPONENT Extensions Manager JS by Ioannis Sannos (datahell) */

function extMan5CopyExtension(tbl) {
	var tblObj = document.getElementById(tbl);
	var checkboxes = tblObj.querySelectorAll('td input.elx5_datacheck');
	if (!checkboxes) { return false; }
	var elid = 0;
	for (var cx=0; cx < checkboxes.length; cx++) {
		if (checkboxes[cx].checked) { elid = checkboxes[cx].value; break; }
	}
	elid = parseInt(elid, 10);
	if (elid < 1) { return; }
	var successfunc = function(xreply) {
		elx5StopPageLoader();
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			alert(e.message);
			return false;
		}
		if (parseInt(jsonObj.success, 10) < 1) {
			if (jsonObj.message != '') {
				alert(jsonObj.message);
			} else {
				alert('Action failed!');
			}
		} else {
			location.reload(true);
		}
	};
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader();
		alert('Action failed! '+errorThrown);
	};
	elx5StartPageLoader();

	var rnd = Math.floor((Math.random()*100)+1);
	var edata = { 'elid':elid, 'rnd':rnd };
	var eurl = tblObj.getAttribute('data-inpage')+'copy';
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

function extMan5PreviewModule(tbl) {
	var tblObj = document.getElementById(tbl);
	var checkboxes = tblObj.querySelectorAll('td input.elx5_datacheck');
	if (!checkboxes) { return false; }
	var elid = 0;
	for (var cx=0; cx < checkboxes.length; cx++) {
		if (checkboxes[cx].checked) { elid = checkboxes[cx].value; break; }
	}
	elid = parseInt(elid, 10);
	if (elid < 1) { return; }
	var eurl = document.getElementById('extmanmodpreview').innerHTML+'?id='+elid;
	elxPopup(eurl, 800, 500, 'modulepreview', 'yes');
}

function extMan5ExtensionTrans(tbl) {
	var tblObj = document.getElementById(tbl);
	var checkboxes = tblObj.querySelectorAll('td input.elx5_datacheck');
	if (!checkboxes) { return false; }
	var elid = 0;
	for (var cx=0; cx < checkboxes.length; cx++) {
		if (checkboxes[cx].checked) { elid = checkboxes[cx].value; break; }
	}
	elid = parseInt(elid, 10);
	if (elid < 1) { return; }
	var eurl = document.getElementById('extmanexttranslations').innerHTML+'&id='+elid;
	eurl = eurl.replace(/&amp;/g, '&');
	elxPopup(eurl, 650, 400, 'modulepreview', 'yes');
}

function extMan5ManagePlugin(tbl) {
	var tblObj = document.getElementById(tbl);
	var checkboxes = tblObj.querySelectorAll('td input.elx5_datacheck');
	if (!checkboxes) {
		var elid = 0;
	} else {
		var elid = 0;
		for (var cx=0; cx < checkboxes.length; cx++) {
			if (checkboxes[cx].checked) { elid = checkboxes[cx].value; break; }
		}
	}
	elid = parseInt(elid, 10);
	var eurl = document.getElementById('extmanmngplugins').innerHTML+'?fn=123456';
	if (elid > 0) { eurl += '&id='+elid; }
	eurl = eurl.replace(/&amp;/g, '&');
	elxPopup(eurl, 950, 700, 'pluginhelper', 'yes');
}

function extMan5InstallExtension() {
	let jdata = {};
	jdata.token = document.getElementById('ieftoken').value;
	jdata.rnd = Math.floor((Math.random()*100)+1);
	var upcfg = {
		'button': 'iefpackage',
		'maxSize': 20000,
		'allowedExtensions': ['zip'],
		'progressBar': 'iefpackagebar',
		'progressOuter': 'iefpackageouter',
		'msgBox': 'iefpackagemsgbox',
		'data': jdata,
		'name': 'package',
		'url': document.getElementById('fmiextension').action+'install'
	};
	extMan5PrepareUploader(upcfg);
	elx5ModalOpen('ie');
}


/*
cfg = {};
cfg.button = button id
cfg.maxSize = 20000 (kb)
cfg.allowedExtensions = ["jpg", "jpeg", "png", "pdf", "doc", "docx", "ppt", "xls", "xlsx", "txt", "zip"]
cfg.progressBar = progress bar id
cfg.progressOuter = progress bar Outer id
cfg.msgBox = message box id
cfg.data = {} additional json data to submit
cfg.name = upload file name
cfg.url = submit url
*/
function extMan5PrepareUploader(cfg) {
	var btn = document.getElementById(cfg.button),
	progressOuter = document.getElementById(cfg.progressOuter),
	msgBox = document.getElementById(cfg.msgBox);
	msgBox.className = 'elx5_invisible';
	let rnd = Math.floor((Math.random()*100)+1);
	if (typeof cfg.data == 'undefined') {
		var jdata = { 'rnd': rnd };
	} else if (cfg.data == '') {
		var jdata = { 'rnd': rnd };
	} else {
		var jdata = cfg.data;
	}
	if (typeof cfg.name == 'undefined') {
		cfg.name = 'uploadfile';
	} else if (cfg.name == '') {
		cfg.name = 'uploadfile';
	}

	var uploader = new ss.SimpleUpload({
		button: btn,
		url: cfg.url,
		name: cfg.name,
		multipart: true,
		hoverClass: 'hover',
		focusClass: 'focus',
		responseType: 'json',
		multiple: false,
		cors: false,
		maxUploads: 1,
		maxSize: cfg.maxsize,
		debug: false,
		allowedExtensions: cfg.allowedExtensions,
		data: jdata,
		startXHR: function() {
			msgBox.className = 'elx5_invisible';
			this.setData( jdata );
			progressOuter.style.display = 'block';
			this.setProgressBar(document.getElementById(cfg.progressBar));
		},
		onSubmit: function() {
			msgBox.className = 'elx5_invisible';
			msgBox.innerHTML = '';
			btn.innerHTML = btn.getAttribute('data-wait');
			btn.classList.add('elx5_notallowedbtn');
		},
		onComplete: function(filename, response) {
			btn.innerHTML = btn.getAttribute('data-selfile');
			btn.classList.remove('elx5_notallowedbtn');
			progressOuter.style.display = 'none';
			if (!response) {
				msgBox.className = 'elx5_error elx5_vspace';
				msgBox.innerHTML = 'Unable to upload file!';
				return;
			}
			if (response.success == 1) {
				if (response.message) {
					msgBox.innerHTML = response.message;
				} else {
					msgBox.innerHTML = 'Upload success!';
				}
				msgBox.className = 'elx5_smsuccess elx5_vspace';
			} else if (response.confirmup == 1) {
				var rmsg = response.message;
				rmsg += '<div class="elx5_vsspace elx5_center"><a href="javascript:void(null);" onclick="extMan5ContinueInstall(\'update\', \''+response.ufolder+'\', \''+cfg.msgBox+'\');" class="elx5_dataaction elx5_datahighlight">'+response.lngcinstall+' <i class="fas fa-arrow-alt-circle-right"></i></a></div>';
				msgBox.innerHTML = rmsg;
				msgBox.className = 'elx5_vspace';
			} else if (response.confirmin == 1) {
				var rmsg = response.message;
				rmsg += '<div class="elx5_vsspace elx5_center"><a href="javascript:void(null);" onclick="extMan5ContinueInstall(\'install\', \''+response.ufolder+'\', \''+cfg.msgBox+'\');" class="elx5_dataaction elx5_datahighlight">'+response.lngcinstall+' <i class="fas fa-arrow-alt-circle-right"></i></a></div>';
				msgBox.innerHTML = rmsg;
				msgBox.className = 'elx5_vspace';
			} else {
				if (response.message) {
					msgBox.innerHTML = response.message;
				} else {
					msgBox.innerHTML = 'An error occurred and the upload failed.';
				}
				msgBox.className = 'elx5_error elx5_vspace';
			}
		},
		onError: function() {
			btn.innerHTML = btn.getAttribute('data-selfile');
			btn.classList.remove('elx5_notallowedbtn');
			progressOuter.style.display = 'none';
			msgBox.className = 'elx5_error elx5_vspace';
			msgBox.innerHTML = 'Unable to upload file';
		}
	});
}

/* CONTINUE INSTALL OR UPDATE */
function extMan5ContinueInstall(ctask, ufolder, msgboxid) {
	var msgBox = document.getElementById(msgboxid);
	msgBox.className = 'elx5_invisible';
	var successfunc = function(xreply) {
		elx5StopPageLoader();
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			msgBox.innerHTML = e.message;
			msgBox.className = 'elx5_error elx5_vspace';
			return false;
		}
		if (parseInt(jsonObj.success, 10) == 1) {
			if (jsonObj.message) {
				msgBox.innerHTML = jsonObj.message;
			} else {
				msgBox.innerHTML = 'Upload success!';
			}
			msgBox.className = 'elx5_smsuccess elx5_vspace';
		} else {//error
			if (jsonObj.message != '') {
				msgBox.innerHTML = jsonObj.message;
			} else {
				msgBox.innerHTML = 'Action failed!';
			}
			msgBox.className = 'elx5_error elx5_vspace';
		}
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader();
		msgBox.innerHTML = 'Action failed! '+errorThrown;
		msgBox.className = 'elx5_error elx5_vspace';
	};

	elx5StartPageLoader();
	let rnd = Math.floor((Math.random()*100)+1);
	var edata = { 'ufolder': ufolder, 'rnd': rnd };
	if (ctask == 'update') {
		var eurl = document.getElementById('fmiextension').action+'cupdate';
	} else {
		var eurl = document.getElementById('fmiextension').action+'cinstall';
	}
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

function extMan5SyncExtension() {
	if (!document.getElementById('sefextension')) { return; }
	var sObj = document.getElementById('sefextension');
	elx5ModalMessageHide('se');
	var edata = {};
	edata.extension = sObj.options[sObj.selectedIndex].value;
	if (edata.extension == '') { return; }
	edata.token = document.getElementById('seftoken').value;
	edata.rnd = Math.floor((Math.random()*100)+1);
	var successfunc = function(xreply) {
		elx5StopPageLoader();
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			elx5ModalMessageShow('se', e.message, 'elx5_error elx5_vspace');
			return false;
		}
		if (parseInt(jsonObj.success, 10) == 1) {
			if (jsonObj.message) {
				var msg = jsonObj.message;
			} else {
				var msg = 'Synchronization success!';
			}
			elx5ModalMessageShow('se', msg, 'elx5_smsuccess elx5_vspace');
		} else {//error
			if (jsonObj.message != '') {
				var msg = jsonObj.message;
			} else {
				var msg = 'Action failed!';
			}
			elx5ModalMessageShow('se', msg, 'elx5_error elx5_vspace');
		}
	}

	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader();
		elx5ModalMessageShow('se', 'Action failed! '+errorThrown, 'elx5_error elx5_vspace');
	}

	var eurl = document.getElementById('fmsextension').action;
	elx5StartPageLoader();
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

function extMan5CronJobs() {
	var successcronfunc = function(xreply) {
		elx5StopPageLoader();
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			alert(e.message);
			return false;
		}
		if (parseInt(jsonObj.success, 10) == 1) {
			location.reload(true);
		} else {//error
			if (jsonObj.errormsg != '') {
				alert(jsonObj.errormsg);
			} else {
				alert('Action failed!');
			}
		}
	}

	var errorcronfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader();
		alert('Action failed! '+errorThrown);
	}

	var edata = {};
	edata.rnd = Math.floor((Math.random()*100)+1);
	var eurl = document.getElementById('extensionstbl').getAttribute('data-cronpage');
	elx5StartPageLoader();
	elxAjax('POST', eurl, edata, null, null, successcronfunc, errorcronfunc);
}

function extMan5Filter(itemname, itemvalue, rest_options) {
	var eurl = document.getElementById('extensionstbl').getAttribute('data-listpage')+'?'+itemname+'='+itemvalue;
	if (rest_options != '') { eurl += '&'+rest_options; }
	window.location.href = eurl;
}

function extMan5UnFilter(rest_options) {
	var eurl = document.getElementById('extensionstbl').getAttribute('data-listpage');
	if (rest_options != '') { eurl += '?'+rest_options; }
	window.location.href = eurl;
}

function extMan5EditPosition(posid) {
	posid = parseInt(posid, 10);
	if (posid == -1) {
		posid = elx5SelectedTableItem('positionstbl', false);
		if (posid === false) { return; }
	}

	var posname = '';
	var posdesc = '';
	if (posid > 0) {
		if (!document.getElementById('positionname'+posid)) { return; }
		posname = document.getElementById('positionname'+posid).getAttribute('data-value');
		posdesc = document.getElementById('positiondesc'+posid).getAttribute('data-value');
		var modaltitle = document.getElementById('emplngedit').value;
		modaltitle = modaltitle.replace(/ZZZ/g, posname);
	} else {
		posid = 0;
		var modaltitle = document.getElementById('emplngnew').value;
	}
	document.getElementById('elx5_modaltitleemp').innerHTML = '<i class="fas fa-edit"></i> '+modaltitle;

	document.getElementById('empposition').value = posname;
	document.getElementById('empdescription').value = posdesc;
	document.getElementById('empid').value = posid;

	elx5ModalOpen('emp');
}

function extMan5SavePosition() {
	var posid = document.getElementById('empid').value;
	if (isNaN(posid)) { posid = 0; }
	posid = parseInt(posid, 10);
	var edata = {};
	edata.position = document.getElementById('empposition').value;
	edata.description = document.getElementById('empdescription').value;
	edata.posid = posid;
	if (edata.position == '') {
		document.getElementById('empposition').focus();
		return;
	}
	edata.rnd = Math.floor((Math.random()*100)+1);
	edata.token = document.getElementById('emptoken').value;

	var successfunc = function(xreply) {
		elx5StopPageLoader();
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			alert(e.message);
			return false;
		}
		if (parseInt(jsonObj.success, 10) == 1) {
			if (posid > 0) {
				document.getElementById('positionname'+posid).setAttribute('data-value', edata.position);
				document.getElementById('positionname'+posid).innerHTML = '<a href="javascript:void(null);" onclick="extMan5EditPosition('+posid+');">'+edata.position+'</a>';
				document.getElementById('positiondesc'+posid).setAttribute('data-value', edata.description);
				document.getElementById('positiondesc'+posid).innerHTML = edata.description;
				elx5ModalClose('emp');
			} else {
				location.reload(true);
			}
		} else {//error
			if (jsonObj.message != '') {
				alert(jsonObj.message);
			} else {
				alert('Action failed!');
			}
		}
	}

	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader();
		alert('Action failed! '+errorThrown);
	}

	var eurl = document.getElementById('fmemodpos').action;
	elx5StartPageLoader();
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

function extMan5CopyTpl(tbl) {
	elx5ModalMessageHide('copytpl');
	var elid = elx5SelectedTableItem(tbl, false);
	elid = parseInt(elid, 10);
	if (elid < 1) { return; }
	if (!document.getElementById('datarow'+elid)) { return; }
	var original_title = document.getElementById('datarow'+elid).getAttribute('data-title');
	var original_template = document.getElementById('datarow'+elid).getAttribute('data-template');
	document.getElementById('ctforigtitle').innerHTML = original_title;
	document.getElementById('ctftitle').value = original_title+' (COPY)';
	document.getElementById('ctftemplate').value = '';
	document.getElementById('ctforiginalid').value = elid;
	document.getElementById('ctforiginaltemplate').value = original_template;
	elx5ModalOpen('copytpl');
}

function extMan5DoCopyTpl() {
	var edata = {};
	edata.title = document.getElementById('ctftitle').value.trim();
	edata.template = document.getElementById('ctftemplate').value.trim();
	edata.originalid = parseInt(document.getElementById('ctforiginalid').value, 10);
	edata.originaltemplate = document.getElementById('ctforiginaltemplate').value;
	if (edata.title == '') {
		elx5ModalMessageShow('copytpl', 'Title is empty!', 'elx5_error elx5_vspace', 2);
		document.getElementById('ctforigtitle').focus();
		return;
	}
	if (edata.template == '') {
		elx5ModalMessageShow('copytpl', 'Template name is empty!', 'elx5_error elx5_vspace', 2);
		document.getElementById('ctftemplate').focus();
		return;
	}
	var b = edata.template.replace(/[^a-z\_0-9]/gi,'');
	if (b != edata.template) {
		elx5ModalMessageShow('copytpl', 'Template name contains invalid characters!', 'elx5_error elx5_vspace', 2);
		document.getElementById('ctftemplate').focus();
		return;
	}
	if (edata.template.indexOf(edata.originaltemplate) > -1) {
		elx5ModalMessageShow('copytpl', 'Template name contains part of the original name!', 'elx5_error elx5_vspace', 2);
		document.getElementById('ctftemplate').focus();
		return;
	}
	edata.rnd = Math.floor((Math.random()*100)+1);

	var successfunc = function(xreply) {
		elx5StopPageLoader();
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			elx5ModalMessageShow('copytpl', 'Action failed! '+e.message, 'elx5_error elx5_vspace', 3);
			return false;
		}
		if (parseInt(jsonObj.success, 10) < 1) {
			if (jsonObj.message != '') {
				elx5ModalMessageShow('copytpl', jsonObj.message, 'elx5_error elx5_vspace', 3);
			} else {
				elx5ModalMessageShow('copytpl', 'Action failed!', 'elx5_error elx5_vspace', 3);
			}
		} else {
			location.reload(true);
		}
	};
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader();
		elx5ModalMessageShow('copytpl', 'Action failed! '+errorThrown, 'elx5_error elx5_vspace', 3);
	};
	elx5StartPageLoader();
	var eurl = document.getElementById('fmctemplate').action;
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

/* UPDATE EXTENSION (FROM UPDATES PAGE) */
function extMan5UpdateExtension(pcode) {
	if (pcode == '') {return; }
	var edata = {};
	edata.task = 'update';
	edata.pcode = pcode;
	edata.edcauth = document.getElementById('edcauth').innerHTML;
	edata.elxisid = document.getElementById('elxisid').innerHTML;
	edata.rnd = Math.floor((Math.random()*100)+1);
	var eurl = document.getElementById('extmanbase').innerHTML+'install/edc';

	var successfunc = function(xreply) {
		elx5StopPageLoader();
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			alert(e.message);
			return false;
		}
		if (parseInt(jsonObj.success, 10) == 1) {
			location.reload(true);
		} else {
			if (jsonObj.message != '') {
				alert(jsonObj.message);
			} else {
				alert('Action failed!');
			}
		}
	}

	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader();
		alert('Action failed! '+errorThrown);
	}

	elx5StartPageLoader();
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

/* LOAD POSITION MODS FOR ORDERING */
function elxman5LoadPositionOrder() {
	var pObj = document.getElementById('eextposition');
	var position = pObj.options[pObj.selectedIndex].value;
	var edata = {'position':position };
	var eurl = document.getElementById('extmanagerbase').innerHTML+'modules/positionorder';
	var successfunc = function(xreply) {
		var jsonObj = JSON.parse(xreply);
		if (parseInt(jsonObj.error, 10) > 0) {
			if (jsonObj.errormsg != '') {
				alert(jsonObj.errormsg);
			} else {
				alert('Request failed!');
			}
			return false;
		} else {
			var oObj = document.getElementById('eextordering');
			oObj.options.length = 0;
			var len = jsonObj.modules.length;
			for (var i = 0; i < len; i++) {
				var xobj = jsonObj.modules[i];
				for (var key in xobj) {
					if (i == len - 1) { var xsel = true; } else { var xsel = false; }
					oObj.options[i] = new Option(xobj[key], key, false, xsel);
    			}
			}
		}
	}
	elxAjax('POST', eurl, edata, null, null, successfunc, null);
}

/* SWITCH ACL CATEGORY */
function extman5SwitchACLCat(categ, elem) {
	var cObj = document.getElementById('aclcategory');
	var acat = cObj.options[cObj.selectedIndex].value;
	if (acat == categ) {
		document.getElementById('aclelement').value = elem;
		document.getElementById('aclelement').disabled = true;
	} else {
		document.getElementById('aclelement').value = '';
		document.getElementById('aclelement').disabled = false;
	}
}

/* SWITCH ACL TYPE */
function extman5SwitchACLType() {
	var aObj = document.getElementById('acltype');
	var atype = aObj.options[aObj.selectedIndex].value;
	if (atype == 'level') {acllevelbox
		document.getElementById('acllevelbox').className = 'elx5_formrow';
		document.getElementById('aclgroupbox').className = 'elx5_invisible';
		document.getElementById('acluserbox').className = 'elx5_invisible';
	} else if (atype == 'group') {
		document.getElementById('acllevelbox').className = 'elx5_invisible';
		document.getElementById('aclgroupbox').className = 'elx5_formrow';
		document.getElementById('acluserbox').className = 'elx5_invisible';
	} else if (atype == 'user') {
		document.getElementById('acllevelbox').className = 'elx5_invisible';
		document.getElementById('aclgroupbox').className = 'elx5_invisible';
		document.getElementById('acluserbox').className = 'elx5_formrow';
	} else {
		return false;
	}
}

function extMan5EditACLRule(ruleid, seluser, is_module) {
	ruleid = parseInt(ruleid, 10);
	is_module = parseInt(is_module, 10);

	if (ruleid == 0) {
		if (is_module == 0) {
			document.getElementById('aclcategory').selectedIndex = 0;
			document.getElementById('aclcategory').disabled = false;
			document.getElementById('aclcategory').classList.remove('elx5_readonly');
			document.getElementById('aclelement').value = '';
			document.getElementById('aclelement').readOnly = false;
			document.getElementById('aclelement').classList.remove('elx5_readonly');
		}
		document.getElementById('aclaction').value = '';
		document.getElementById('aclaction').readOnly = false;
		document.getElementById('aclaction').classList.remove('elx5_readonly');
		document.getElementById('acltype').selectedIndex = 0;
		document.getElementById('acllevelbox').className = 'elx5_formrow';
		document.getElementById('acllevel').selectedIndex = 0;
		document.getElementById('aclgroupbox').className = 'elx5_invisible';
		document.getElementById('aclgroup').selectedIndex = 0;
		document.getElementById('acluserbox').className = 'elx5_invisible';
		if (seluser == 1) {
			document.getElementById('acluser').selectedIndex = 0;
		} else {
			document.getElementById('acluser').value = 0;
		}
		document.getElementById('aclvalue').selectedIndex = 1;
		document.getElementById('acleditid').value = 0;
		elx5ModalOpen('eacl');
		return;
	}

	if (!document.getElementById('aclrow'+ruleid)) { return; }

	var acltype = 'level';
	if (is_module == 0) {
		var v = document.getElementById('aclcategory_'+ruleid).getAttribute('data-value');
		var selidx = 0;
		var sObj = document.getElementById('aclcategory');
		for (i = 0; i < sObj.length; i++) {
			if (sObj.options[i].value == v) { selidx = i; break; }
		}
		sObj.selectedIndex = selidx;
		document.getElementById('aclelement').value = document.getElementById('aclelement_'+ruleid).getAttribute('data-value');
	}
	document.getElementById('aclaction').value = document.getElementById('aclaction_'+ruleid).getAttribute('data-value');

	var v = document.getElementById('aclminlevel_'+ruleid).getAttribute('data-value');
	v = parseInt(v, 10);
	if (v -1) { acltype = 'level'; }
	var selidx = 0;
	var sObj = document.getElementById('acllevel');
	for (i = 0; i < sObj.length; i++) {
		if (sObj.options[i].value == v) { selidx = i; break; }
	}
	sObj.selectedIndex = selidx;

	var v = document.getElementById('aclgid_'+ruleid).getAttribute('data-value');
	v = parseInt(v, 10);
	if (v > 0) { acltype = 'group'; }
	var selidx = 0;
	var sObj = document.getElementById('aclgroup');
	for (i = 0; i < sObj.length; i++) {
		if (sObj.options[i].value == v) { selidx = i; break; }
	}
	sObj.selectedIndex = selidx;

	var v = document.getElementById('acluid_'+ruleid).getAttribute('data-value');
	v = parseInt(v, 10);
	if (v > 0) { acltype = 'user'; }
	if (seluser == 1) {
		var selidx = 0;
		var sObj = document.getElementById('acluser');
		for (i = 0; i < sObj.length; i++) {
			if (sObj.options[i].value == v) { selidx = i; break; }
		}
		sObj.selectedIndex = selidx;
	} else {
		document.getElementById('acluser').value = v;
	}

	var v = document.getElementById('aclaclvalue_'+ruleid).getAttribute('data-value');
	var selidx = 1;
	var sObj = document.getElementById('aclvalue');
	for (i = 0; i < sObj.length; i++) {
		if (sObj.options[i].value == v) { selidx = i; break; }
	}
	sObj.selectedIndex = selidx;

	if (acltype == 'level') {
		document.getElementById('acltype').selectedIndex = 0;
		document.getElementById('acllevelbox').className = 'elx5_formrow';
		document.getElementById('aclgroupbox').className = 'elx5_invisible';
		document.getElementById('acluserbox').className = 'elx5_invisible';
	} else if (acltype == 'group') {
		document.getElementById('acltype').selectedIndex = 1;
		document.getElementById('acllevelbox').className = 'elx5_invisible';
		document.getElementById('aclgroupbox').className = 'elx5_formrow';
		document.getElementById('acluserbox').className = 'elx5_invisible';
	} else {//user
		document.getElementById('acltype').selectedIndex = 2;
		document.getElementById('acllevelbox').className = 'elx5_invisible';
		document.getElementById('aclgroupbox').className = 'elx5_invisible';
		document.getElementById('acluserbox').className = 'elx5_formrow';
	}
	document.getElementById('acleditid').value = ruleid;
	if (is_module == 0) {
		document.getElementById('aclcategory').classList.add('elx5_readonly');
		document.getElementById('aclcategory').disabled = true;
		document.getElementById('aclelement').classList.add('elx5_readonly');
		document.getElementById('aclelement').readOnly = true;
	}
	document.getElementById('aclaction').classList.add('elx5_readonly');
	document.getElementById('aclaction').readOnly = true;
	elx5ModalOpen('eacl');
}

function extMan5SaveACLRule(seluser, is_module) {
	is_module = parseInt(is_module, 10);

	var edata = {};
	edata.id = parseInt(document.getElementById('acleditid').value, 10);
	if (is_module == 0) {
		var sObj = document.getElementById('aclcategory');
		edata.category = sObj.options[sObj.selectedIndex].value;
	} else {
		edata.category = document.getElementById('aclcategory').value;
	}
	edata.element = document.getElementById('aclelement').value;
	edata.identity = parseInt(document.getElementById('aclidentity').value, 10);
	edata.action = document.getElementById('aclaction').value;
	var sObj = document.getElementById('acllevel');
	edata.minlevel = parseInt(sObj.options[sObj.selectedIndex].value, 10);
	sObj = document.getElementById('aclgroup');
	edata.gid = parseInt(sObj.options[sObj.selectedIndex].value, 10);
	if (seluser == 1) {
		sObj = document.getElementById('acluser');
		edata.uid = parseInt(sObj.options[sObj.selectedIndex].value, 10);
	} else {
		edata.uid = parseInt(document.getElementById('acluser').value, 10);
	}
	sObj = document.getElementById('aclvalue');
	edata.aclvalue = parseInt(sObj.options[sObj.selectedIndex].value, 10);

	if (edata.category == '') { return; }
	if (edata.element == '') {
		document.getElementById('aclelement').focus();
		return;
	}
	if (edata.action == '') {
		document.getElementById('aclaction').focus();
		return;
	}
	if (edata.aclvalue < 0) {
		document.getElementById('aclvalue').focus();
		return;
	}

	sObj = document.getElementById('acltype');
	var atype = sObj.options[sObj.selectedIndex].value;
	if (atype == 'level') {
		edata.uid = 0;
		edata.gid = 0;
		if (edata.minlevel < 0) {
			document.getElementById('acllevel').focus();
			return;
		}
	} else if (atype == 'group') {
		edata.minlevel = -1;
		edata.uid = 0;
		if (edata.gid < 1) {
			document.getElementById('aclgroup').focus();
			return;
		}
	} else if (atype == 'user') {
		edata.minlevel = -1;
		edata.gid = 0;
		if (edata.uid < 1) {
			document.getElementById('acluser').focus();
			return;
		}
	} else {
		return;
	}

	var successfunc = function(xreply) {
		elx5StopPageLoader();
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			alert(e.message);
			return false;
		}
		if (parseInt(jsonObj.error, 10) > 0) {
			if (jsonObj.errormsg != '') {
				alert(jsonObj.errormsg);
			} else {
				alert('Request failed!');
			}
			return false;
		} else {
			if (edata.id > 0) {//edit
				if (is_module == 0) {
					document.getElementById('aclcategory_'+edata.id).setAttribute('data-value', jsonObj.category);
					document.getElementById('aclcategory_'+edata.id).innerHTML = jsonObj.category;
					document.getElementById('aclelement_'+edata.id).setAttribute('data-value', jsonObj.element);
					document.getElementById('aclelement_'+edata.id).innerHTML = jsonObj.elementtext;					
				}
				document.getElementById('aclaction_'+edata.id).setAttribute('data-value', jsonObj.action);
				document.getElementById('aclaction_'+edata.id).innerHTML = jsonObj.actiontext;
				document.getElementById('aclminlevel_'+edata.id).setAttribute('data-value', jsonObj.minlevel);
				document.getElementById('aclminlevel_'+edata.id).innerHTML = jsonObj.minleveltext;
				document.getElementById('aclgid_'+edata.id).setAttribute('data-value', jsonObj.gid);
				document.getElementById('aclgid_'+edata.id).innerHTML = jsonObj.gidtext;
				document.getElementById('acluid_'+edata.id).setAttribute('data-value', jsonObj.uid);
				document.getElementById('acluid_'+edata.id).innerHTML = jsonObj.uidtext;
				document.getElementById('aclaclvalue_'+edata.id).setAttribute('data-value', jsonObj.aclvalue);
				document.getElementById('aclaclvalue_'+edata.id).innerHTML = jsonObj.aclvalue;
			} else {
				extMan5AddACLRow(jsonObj, seluser, is_module);
			}
			elx5ModalClose('eacl');
		}
	}

	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader();
		alert('Action failed! '+errorThrown);
	}

	var eurl = document.getElementById('extaccesstbl').getAttribute('data-savepage');
	elx5StartPageLoader();
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}


function extMan5AddACLRow(jsonObj, seluser, is_module) {
	var tbl = document.getElementById('extaccesstbl');
	var lastRow = tbl.rows.length;

	var row = tbl.insertRow(lastRow);
	row.setAttribute('id', 'aclrow'+jsonObj.id);

	var idx = 0;
	if (is_module == 0) {
		var tdObj = row.insertCell(idx);
		tdObj.setAttribute('id', 'aclcategory_'+jsonObj.id);
		tdObj.setAttribute('data-value', jsonObj.category);
		tdObj.className = 'elx5_tabhide';
		var textNode = document.createTextNode(jsonObj.category);
		tdObj.appendChild(textNode);
		idx++;

		var tdObj = row.insertCell(idx);
		tdObj.setAttribute('id', 'aclelement_'+jsonObj.id);
		tdObj.setAttribute('data-value', jsonObj.element);
		tdObj.className = 'elx5_tabhide';
		var textNode = document.createTextNode(jsonObj.elementtext);
		tdObj.appendChild(textNode);
		idx++;
	}

	var tdObj = row.insertCell(idx);
	tdObj.setAttribute('id', 'aclaction_'+jsonObj.id);
	tdObj.setAttribute('data-value', jsonObj.action);
	var textNode = document.createTextNode(jsonObj.actiontext);
	tdObj.appendChild(textNode);
	idx++;

	var tdObj = row.insertCell(idx);
	tdObj.setAttribute('id', 'aclminlevel_'+jsonObj.id);
	tdObj.setAttribute('data-value', jsonObj.minlevel);
	tdObj.className = 'elx5_center elx5_lmobhide';
	var textNode = document.createTextNode(jsonObj.minleveltext);
	tdObj.appendChild(textNode);
	idx++;

	var tdObj = row.insertCell(idx);
	tdObj.setAttribute('id', 'aclgid_'+jsonObj.id);
	tdObj.setAttribute('data-value', jsonObj.gid);
	tdObj.className = 'elx5_mobhide';
	var textNode = document.createTextNode(jsonObj.gidtext);
	tdObj.appendChild(textNode);
	idx++;

	var tdObj = row.insertCell(idx);
	tdObj.setAttribute('id', 'acluid_'+jsonObj.id);
	tdObj.setAttribute('data-value', jsonObj.uid);
	tdObj.className = 'elx5_mobhide';
	var textNode = document.createTextNode(jsonObj.uidtext);
	tdObj.appendChild(textNode);
	idx++;

	var tdObj = row.insertCell(idx);
	tdObj.setAttribute('id', 'aclaclvalue_'+jsonObj.id);
	tdObj.setAttribute('data-value', jsonObj.aclvalue);
	tdObj.className = 'elx5_center elx5_lmobhide';
	var textNode = document.createTextNode(jsonObj.aclvalue);
	tdObj.appendChild(textNode);
	idx++;

	var actionstext = '<a href="javascript:void(null);" onclick="extMan5EditACLRule('+jsonObj.id+', '+seluser+', '+is_module+')" title="Edit" class="elx5_smbtn"><i class="fas fa-pencil-alt"></i></a> &#160; ';
	actionstext += '<a href="javascript:void(null);" onclick="extMan5DeleteACLRule('+jsonObj.id+')" title="Delete" class="elx5_smbtn elx5_errorbtn"><i class="fas fa-times"></i></a>';
	var tdObj = row.insertCell(idx);
	tdObj.className = 'elx5_center';
	tdObj.innerHTML = actionstext;
	idx++;
}

function extMan5DeleteACLRule(ruleid) {
	ruleid = parseInt(ruleid, 10);
	if (ruleid < 1) { return; }

	var successfunc = function(xreply) {
		elx5StopPageLoader();
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			alert('Error, not JSON response!');
			return false;
		}
		if (parseInt(jsonObj.success, 10) < 1) {
			if (jsonObj.message != '') {
				alert(jsonObj.message);
			} else {
				alert('Action failed!');
			}
		} else {
			var tbl = document.getElementById('extaccesstbl');
			var rowCount = tbl.rows.length;
			for (var i=0; i < rowCount; i++) {
				if (tbl.rows[i].id == 'aclrow'+ruleid) {
					tbl.deleteRow(i);
					break;
				}
			}
		}
	};

	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader();
		alert('Action failed! '+errorThrown);
	};

	var prompttxt = document.getElementById('extaccesstbl').getAttribute('data-deletelng');
	if (confirm(prompttxt)) {
		var rnd = Math.floor((Math.random()*100)+1);
		var edata = { 'elids': ruleid, 'rnd': rnd };
		var eurl = document.getElementById('extaccesstbl').getAttribute('data-deletepage');
		elx5StartPageLoader();
		elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
	}
}

//--------------- EDC -------------

function extMan5EDCSetCategory(catid) {
	catid = parseInt(catid, 10);
	if (catid < 1) {
		document.getElementById('extman5_edc_ctgsel').selectedIndex = 0;
		return;
	}
	var selidx = 1;
	var sObj = document.getElementById('extman5_edc_ctgsel');
	for (i = 0; i < sObj.length; i++) {
		if (sObj.options[i].value == catid) { selidx = i; break; }
	}
	sObj.selectedIndex = selidx;
}


function extMan5EDCFrontpage() {
	extMan5EDCSetCategory(0);
	var edata = {};
	edata.task = 'frontpage';
	edata.edcauth = document.getElementById('extman5_edcdata').getAttribute('data-auth');
	edata.rnd = Math.floor((Math.random()*100)+1);
	var successfunc = function(xreply) {
		elx5StopPageLoader();
		document.getElementById('extman5_edcmain').innerHTML = xreply;
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader();
		document.getElementById('extman5_edcmain').innerHTML = '<div class="elx5_error">Could not load EDC! '+errorThrown+'</div>';
	}
	var eurl = document.getElementById('extman5_edcbase').innerHTML+'browse/req';
	elx5StartPageLoader();
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

/* CONNECT TO ELXIS DC */
function extMan5EDCConnect() {
	var dataObj = document.getElementById('extman5_edcdata');
	if (dataObj.getAttribute('data-auth') != '') {
		extMan5EDCFrontpage();
		return;
	}

	var edata = {};
	edata.task = 'auth';
	edata.rnd = Math.floor((Math.random()*100)+1);
	var successfunc = function(xreply) {
		elx5StopPageLoader();
		var jsonObj = JSON.parse(xreply);
		if (parseInt(jsonObj.error, 10) > 0) {
			if (jsonObj.errormsg != '') {
				document.getElementById('extman5_edcauthmsg').innerHTML = jsonObj.errormsg;
			} else {
				document.getElementById('extman5_edcauthmsg').innerHTML = 'Authorization to EDC failed!';
			}
			document.getElementById('extman5_edcauthmsg').className = 'elx5_error';
			return false;
		} else {
			dataObj.setAttribute('data-auth', jsonObj.edcauth);
			extMan5EDCFrontpage();
		}
	}

	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader();

		document.getElementById('extman5_edcauthmsg').innerHTML = 'Connection to EDC failed! '+errorThrown;
		document.getElementById('extman5_edcauthmsg').className = 'elx5_error';
	}

	document.getElementById('extman5_edcauthmsg').className = 'elx5_invisible';
	var eurl = document.getElementById('extman5_edcbase').innerHTML+'browse/req';
	elx5StartPageLoader();
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

function extMan5EDCSwitchCtg() {
	var selObj = document.getElementById('extman5_edc_ctgsel');
	var catid = selObj.options[selObj.selectedIndex].value;
	catid = parseInt(catid, 10);
	if (catid < 1) {
		extMan5EDCFrontpage();
	} else {
		extMan5EDCLoadCategory(catid, 1);
	}
}

function extMan5EDCLoadCategory(catid, page) {
	if ((typeof page == "undefined") || (page == undefined)) { page = 1; }
	page = parseInt(page, 10);
	if (page < 1) { page = 1; }
	catid = parseInt(catid, 10);
	extMan5EDCSetCategory(catid);

	var successfunc = function(xreply) {
		elx5StopPageLoader();
		document.getElementById('extman5_edcmain').innerHTML = xreply;
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader();
		document.getElementById('extman5_edcmain').innerHTML = '<div class="elx5_error">Error! '+errorThrown+'</div>';
	}
	var edcauth = document.getElementById('extman5_edcdata').getAttribute('data-auth');
	var rnd = Math.floor((Math.random()*100)+1);
	var edata = { 'task':'category', 'catid':catid, 'fid':'0', 'page':page, 'edcauth':edcauth, 'rnd':rnd };
	var eurl = document.getElementById('extman5_edcbase').innerHTML+'browse/req';

	elx5StartPageLoader();
	elxAjax('POST', eurl, edata, null, null, successfunc, null);
}


function extMan5EDCAuthor(uid) {
	uid = parseInt(uid, 10);
	if (uid < 1) { alert('Invalid author!'); return false; }
	extMan5EDCSetCategory(0);

	var successfunc = function(xreply) {
		elx5StopPageLoader();
		document.getElementById('extman5_edcmain').innerHTML = xreply;
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader();
		document.getElementById('extman5_edcmain').innerHTML = '<div class="elx5_error">Could not load author extensions! '+errorThrown+'</div>';
	}

	var rnd = Math.floor((Math.random()*100)+1);
	var edcauth = document.getElementById('extman5_edcdata').getAttribute('data-auth');
	var edata = { 'task':'author', 'uid':uid, 'edcauth':edcauth, 'rnd':rnd };
	var eurl = document.getElementById('extman5_edcbase').innerHTML+'browse/req';

	elx5StartPageLoader();
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}


function extman5EDCLoadExtension(id, catid) {
	id = parseInt(id, 10);
	if (id < 1) { alert('Invalid extension!'); return false; }
	catid = parseInt(catid, 10);
	extMan5EDCSetCategory(catid);

	var successfunc = function(xreply) {
		elx5StopPageLoader();
		document.getElementById('extman5_edcmain').innerHTML = xreply;
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader();
		document.getElementById('extman5_edcmain').innerHTML = '<div class="elx5_error">Connection to EDC failed! '+errorThrown+'</div>';
	}

	var rnd = Math.floor((Math.random()*100)+1);
	var edcauth = document.getElementById('extman5_edcdata').getAttribute('data-auth');
	var edata = { 'task':'view', 'id':id, 'catid':catid, 'edcauth':edcauth, 'rnd':rnd };
	var eurl = document.getElementById('extman5_edcbase').innerHTML+'browse/req';

	elx5StartPageLoader();
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

function extman5EDCDownload(pcode) {
	if (pcode == '') { alert('This file can not be downloaded!'); return false; }
	var edcurl = document.getElementById('extman5_edcurl').innerHTML;
	if (edcurl == '') { alert('EDC location is unknown!'); return false; }
	var rnd = Math.floor((Math.random()*100)+1);
	var dataObj = document.getElementById('extman5_edcdata');
	var elxisid = dataObj.getAttribute('data-elxisid');
	var edcauth = dataObj.getAttribute('data-auth');
	if (edcauth == '') { alert('You are not authorized to access EDC!'); return false; }

	edcurl += '?task=download&elxisid='+elxisid+'&edcauth='+edcauth+'&pcode='+pcode+'&rnd='+rnd;
	elxPopup(edcurl, 400, 300, 'Download', 'no');
}

function extman5EDCPrompt(action, pcode, exttitle, extversion) {
	if (pcode == '') { alert('Installation package is missing!'); return false; }
	document.getElementById('extman5_edc_iresponse').className = 'elx5_invisible';
	if (action == 'install') {
		var lng_aboutto = edcLang.ABOUT_TO_INSTALL;
	} else if (action == 'update') {
		var lng_aboutto = edcLang.ABOUT_TO_UPDATE_TO;
	} else {
		alert('Invalid action!');
		return false;
	}
	lng_aboutto = lng_aboutto.replace(/X1/gi, '<strong>'+exttitle+'</strong>');
	lng_aboutto = lng_aboutto.replace(/X2/gi, '<strong>'+extversion+'</strong>');
	var prompttxt = '<p class="elx5_center">'+lng_aboutto+'</p><div class="elx5_vspace elx5_center">';
	if (action == 'install') {
		prompttxt += '<a href="javascript:void(null);" onclick="extman5EDCInstall(\''+pcode+'\', \'install\');" class="elx5_btn elx5_ibtn elx5_sucbtn">'+edcLang.INSTALL+'</a> ';
	} else {
		prompttxt += '<a href="javascript:void(null);" onclick="extman5EDCInstall(\''+pcode+'\', \'update\');" class="elx5_btn elx5_ibtn elx5_sucbtn">'+edcLang.UPDATE+'</a> ';
	}
	prompttxt += '<a href="javascript:void(null);" onclick="elx5ModalClose(\'edcinst\');" class="elx5_btn elx5_ibtn elx5_warnbtn">'+edcLang.CANCEL+'</a></div>';
	document.getElementById('extman5_edc_imessage').innerHTML = prompttxt;
	elx5ModalOpen('edcinst');
}

function extman5EDCInstall(pcode, edctask) {
	if (pcode == '') { alert('Installation package is missing!'); return false; }
	document.getElementById('extman5_edc_iresponse').className = 'elx5_invisible';
	document.getElementById('extman5_edc_imessage').className = 'elx5_center';

	var rnd = Math.floor((Math.random()*100)+1);
	var dataObj = document.getElementById('extman5_edcdata');
	var elxisid = dataObj.getAttribute('data-elxisid');
	var edcauth = dataObj.getAttribute('data-auth');
	var edata = { 'task':edctask, 'edcauth':edcauth, 'pcode':pcode, 'elxisid':elxisid, 'rnd':rnd };

	var successfunc = function(xreply) {
		elx5StopPageLoader();
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			alert(e.message);
			return false;
		}
		if (parseInt(jsonObj.success, 10) == 1) {
			var lng_insuccess = edcLang.EXT_INST_SUCCESS;
			var rtxt = lng_insuccess.replace(/X1/gi, jsonObj.exttype+' <strong>'+jsonObj.extension+'</strong>');
			rtxt = rtxt.replace(/X2/gi, '<strong>'+jsonObj.version+'</strong>');
			if (jsonObj.editlink != '') { rtxt += ' - <a href="'+jsonObj.editlink+'">'+edcLang.EDIT+'</a>'; }
			var responsetxt = '<div class="elx5_info">'+rtxt+'</div>';
			var len = jsonObj.warnings.length;
			if (len > 0) {
				responsetxt += '<h3>'+edcLang.SYSTEM_WARNINGS+'</h3>';
				for (var i = 0; i < len; i++) {
					var n = i + 1;
					responsetxt += '<div class="elx5_smwarning">'+jsonObj.warnings[i]+'</div>';
				}				
			}
			document.getElementById('extman5_edc_imessage').className = 'elx5_invisible';
			document.getElementById('extman5_edc_iresponse').innerHTML = responsetxt;
			document.getElementById('extman5_edc_iresponse').className = 'elx5_vlspace';
		} else {
			if (jsonObj.message != '') {
				document.getElementById('extman5_edc_iresponse').innerHTML = jsonObj.message;
			} else {
				document.getElementById('extman5_edc_iresponse').innerHTML = 'Action failed!';
			}
			document.getElementById('extman5_edc_iresponse').className = 'elx5_error elx5_vlspace';
			return false;
		} 
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader();
		document.getElementById('extman5_edc_iresponse').className = 'elx5_error elx5_vlspace';
		document.getElementById('extman5_edc_iresponse').innerHTML = 'Connection to EDC failed! '+errorThrown;
	}

	elx5StartPageLoader();
	var eurl = document.getElementById('extman5_edcbase').innerHTML+'install/edc';
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

function extman5EDCSearch() {
	if (!document.getElementById('edcsfkeyword')) { return; }
	elx5ModalMessageHide('edcsm');
	var keyword = document.getElementById('edcsfkeyword').value;
	keyword = keyword.replace(/^\s+|\s+$/gm,'');
	if ((keyword == '') || (keyword.length < 4)) {
		document.getElementById('edcsfkeyword').focus();
		return;
	}
	var successfunc = function(xreply) {
		elx5StopPageLoader();
		elx5ModalClose('edcsm');
		document.getElementById('extman5_edcmain').innerHTML = xreply;
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader();
		elx5ModalMessageShow('edcsm', 'Action failed! '+errorThrown, 'elx5_error elx5_vspace');
	}
	var rnd = Math.floor((Math.random()*100)+1);
	var edcauth = document.getElementById('extman5_edcdata').getAttribute('data-auth');
	var edata = { 'task': 'search', 'keyword': keyword, 'edcauth': edcauth, 'rnd': rnd };
	var eurl = document.getElementById('extman5_edcbase').innerHTML+'browse/req';
	elx5StartPageLoader();
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

function extman5EDCSearchSubmit() {
	extman5EDCSearch();
	return false;
}

function extMan5UsagePlugin(tbl) {
	var tblObj = document.getElementById(tbl);
	var checkboxes = tblObj.querySelectorAll('td input.elx5_datacheck');
	if (!checkboxes) {
		var elid = 0;
	} else {
		var elid = 0;
		for (var cx=0; cx < checkboxes.length; cx++) {
			if (checkboxes[cx].checked) { elid = checkboxes[cx].value; break; }
		}
	}
	elid = parseInt(elid, 10);
	if (elid < 1) { return; }
	var rnd = Math.floor((Math.random()*100)+1);
	var eurl = document.getElementById('extensionstbl').getAttribute('data-inpage')+'usage?id='+elid+'rnd='+rnd;
	elxPopup(eurl, 950, 700, 'pluginusage', 'yes');
}
