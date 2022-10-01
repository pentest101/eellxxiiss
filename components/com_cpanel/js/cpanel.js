/* Component CPANEL javascript */
/* Elxis CMS - http://www.elxis.org */

var cronrunwait = 0;

function elx5CPNewBackup(bktype) {
	if (bktype == 'db') {
		var sObj = document.getElementById('bkdbtable');
	} else {
		var sObj = document.getElementById('bkfsfolder');
	}
	let item = sObj.options[sObj.selectedIndex].value;
	var rnd = Math.floor((Math.random()*100)+1);
	var edata = { 'type':bktype, 'item':item, 'rnd':rnd };
	var eurl = document.getElementById('backupstbl').getAttribute('data-backuppage');

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
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

function elx5CPViewLog(is_download, fname) {
	var directlink = false;
	if (typeof fname !== 'undefined') {
		if ((fname !== false) && (fname != '')) { is_download = 0; var item = fname; var directlink = true; }
	}
	if (!directlink) {
		var item = elx5SelectedTableItem('logstbl', false);
	}
	if ((item === false) || (item == '')) { return; }
	is_download = parseInt(is_download, 10);
	if (is_download == 1) {
		var gourl = document.getElementById('logstbl').getAttribute('data-listpage')+'download?fname='+item;
		window.open(gourl);
	} else {
		var gourl = document.getElementById('logstbl').getAttribute('data-listpage')+'view?fname='+item;
		elxPopup(gourl, 900, 550, 'viewlog', 'yes');
	}
}

function elx5CPEditRoute(is_add) {
	elx5ModalMessageHide('rtm');
	if (is_add) {
		var sObj = document.getElementById('rtrtype');
		var selidx = 0;
		for (var i=0; i < sObj.length; i++) {
			if (sObj.options[i].value == 'page') {
				sObj.options[i].disabled = false;
				selidx = i;
			} else if (sObj.options[i].value == 'dir') {
				sObj.options[i].disabled = false;
			} else {
				sObj.options[i].disabled = true;
			}
		}
		sObj.selectedIndex = selidx;

		document.getElementById('rtrbase').readOnly = false;
		document.getElementById('rtrbase').value = '';
		document.getElementById('rtrroute').selectedIndex = 0;
		document.getElementById('rtisnew').value = 1;
		document.getElementById('rtroutewrap').className = 'elx5_zero';
		document.getElementById('rtroute2wrap').className = 'elx5_invisible';
		document.getElementById('elx5_modaltitlertm').innerHTML = document.getElementById('elx5_modalrtm').getAttribute('data-addlng');
	} else {
		var rbase_encoded = elx5SelectedTableItem('routestbl', false);
		if ((rbase_encoded === false) || (rbase_encoded == '')) {
			elx5ModalMessageShow('rtm', 'You must select an item!', 'elx5_warning');
			return;
		}
		var rbase = window.atob(rbase_encoded);//base64_decode
		var rtype = document.getElementById('dataprimary'+rbase_encoded).getAttribute('data-rtype');
		if (rtype == 'frontpage') {
			elx5ModalMessageShow('rtm', 'You cannot edit frontpage from here! Go to Elxis configuration instead.', 'elx5_error');
			return;
		}

		var sObj = document.getElementById('rtrtype');
		var selidx = 0;
		for (var i=0; i < sObj.length; i++) {
			if (sObj.options[i].value == rtype) {
				sObj.options[i].disabled = false;
				selidx = i;
			} else {
				sObj.options[i].disabled = true;
			}
		}
		sObj.selectedIndex = selidx;

		document.getElementById('rtrbase').value = rbase;
		document.getElementById('rtrbase').readOnly = true;
		if (rtype == 'component') {
			document.getElementById('rtroutewrap').className = 'elx5_invisible';
			document.getElementById('rtroute2wrap').className = 'elx5_zero';
			document.getElementById('rtrroute2').value = document.getElementById('dataroute'+rbase_encoded).getAttribute('data-value');
		} else {
			document.getElementById('rtroutewrap').className = 'elx5_zero';
			document.getElementById('rtroute2wrap').className = 'elx5_invisible';
			var rroute = document.getElementById('dataroute'+rbase_encoded).getAttribute('data-value');
			sObj = document.getElementById('rtrroute');
			selidx = 0;
			for (var i=0; i < sObj.length; i++) {
				if (sObj.options[i].value == rroute) { selidx = i; break; }
			}
			sObj.selectedIndex = selidx;
		}
		document.getElementById('rtisnew').value = 0;
		var modaltitle = document.getElementById('elx5_modalrtm').getAttribute('data-editlng');
		document.getElementById('elx5_modaltitlertm').innerHTML = modaltitle.replace('%s', rbase);
	}

	elx5ModalOpen('rtm');
}

function elx5CPSaveRoute() {
	var edata = {};
	edata.isnew = parseInt(document.getElementById('rtisnew').value, 10);
	var sObj = document.getElementById('rtrtype');
	edata.rtype = sObj.options[sObj.selectedIndex].value;
	if ((edata.isnew == 0) && (edata.rtype == 'component')) {
		edata.rroute = document.getElementById('rtrroute2').value;
	} else {
		sObj = document.getElementById('rtrroute');
		edata.rroute = sObj.options[sObj.selectedIndex].value;
	}
	edata.rbase = document.getElementById('rtrbase').value;

	if (edata.rtype == 'frontpage') {
		elx5ModalMessageShow('rtm', 'You cannot edit frontpage from here! Go to Elxis configuration instead.', 'elx5_error');
		return;
	}

	if (edata.isnew == 1) {
		if (edata.rbase == '') { document.getElementById('rtrbase').focus(); return; }
		if ((edata.rtype == '') || ((edata.rtype != 'page') && (edata.rtype != 'dir'))) {
			elx5ModalMessageShow('rtm', 'Type is invalid!', 'elx5_error');
			return;
		}
	}

	var successfunc = function(xreply) {
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			elx5ModalMessageShow('rtm', 'Could not load data!', 'elx5_error');
			return false;
		}
		if (parseInt(jsonObj.success, 10) < 1) {
			if (jsonObj.message != '') {
				var emsg = jsonObj.message;
			} else {
				var emsg = 'Action failed!';
			}
			elx5ModalMessageShow('rtm', emsg, 'elx5_error');
		}
		if (parseInt(jsonObj.success, 10) == 1) {
			elx5ModalClose('rtm');
			location.reload(true);
		}
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		document.getElementById('rtsave').innerHTML = document.getElementById('rtsave').getAttribute('data-savelng');
		elx5ModalMessageShow('rtm', 'Error! '+errorThrown, 'elx5_error');
	}

	document.getElementById('rtsave').innerHTML = document.getElementById('rtsave').getAttribute('data-waitlng');

	edata.rnd = Math.floor((Math.random()*100)+1);
	var eurl = document.getElementById('fmedroute').action;
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}


function elx5CPDeleteRoute() {
	var item = elx5SelectedTableItem('routestbl', false);
	if ((item === false) || (item == '')) { return; }
	var can_delete = parseInt(document.getElementById('datarow'+item).getAttribute('data-candelete'), 10);
	if (can_delete != 1) {
		alert('You cannot delete this item!');
		return;
	}
	var rtype = document.getElementById('dataprimary'+item).getAttribute('data-rtype');
	var othObj = { 'rtype': rtype, 'rbase': item };
	var otherdata = JSON.stringify(othObj);
	elx5DeleteTableRows('routestbl', false, otherdata);
}

function elx5CPConfirmLink(ctxt, lnk) {
	if (confirm(ctxt)) { location.href = lnk; }
}

function elx5CPMultisite(is_add, elxisadir) {
	elx5ModalMessageHide('msm');
	if (is_add) {
		document.getElementById('msname').value = '';
		document.getElementById('msfolder').value = '';
		document.getElementById('msfolder').readOnly = false;
		document.getElementById('msfolder').required = true;
		document.getElementById('msurlwrap').className = 'elx5_invisible';
		document.getElementById('msactive').checked = false;
		document.getElementById('msdatabasewrap').className = 'elx5_zero';
		var sObj = document.getElementById('msdb_type');
		var defval = sObj.getAttribute('data-defvalue');
		var selidx = 0;
		for (var i=0; i < sObj.length; i++) {
			if (sObj.options[i].value == defval) {
				selidx = i; break;
			}
		}
		sObj.selectedIndex = selidx;
		document.getElementById('msdb_host').value = document.getElementById('msdb_host').getAttribute('data-defvalue');
		document.getElementById('msdb_port').value = document.getElementById('msdb_port').getAttribute('data-defvalue');
		document.getElementById('msdb_name').value = document.getElementById('msdb_name').getAttribute('data-defvalue');
		document.getElementById('msdb_prefix').value = document.getElementById('msdb_prefix').getAttribute('data-defvalue');
		document.getElementById('msdb_user').value = document.getElementById('msdb_user').getAttribute('data-defvalue');
		document.getElementById('msdb_pass').value = '';
		document.getElementById('msdb_dsn').value = document.getElementById('msdb_dsn').getAttribute('data-defvalue');
		document.getElementById('msdb_scheme').value = document.getElementById('msdb_scheme').getAttribute('data-defvalue');
		document.getElementById('msdb_import').selectedIndex = 1;
		document.getElementById('msid').value = 0;
		document.getElementById('elx5_modaltitlemsm').innerHTML = document.getElementById('elx5_modalmsm').getAttribute('data-addlng');
		document.getElementById('mshtaccwrap').className = 'elx5_invisible';
	} else {
		var msid = elx5SelectedTableItem('msitestbl', false);
		if ((msid === false) || (msid == '')) {
			elx5ModalMessageShow('msm', 'You must select an item!', 'elx5_warning');
			return;
		}
		msid = parseInt(msid, 10);
		if (msid < 1) {
			elx5ModalMessageShow('msm', 'You must select an item!', 'elx5_warning');
			return;
		}
		document.getElementById('msname').value = document.getElementById('msdataname'+msid).getAttribute('data-value');
		document.getElementById('msfolder').value = document.getElementById('msdatafolder'+msid).getAttribute('data-value');
		if (msid == 1) {
			document.getElementById('msfolder').readOnly = true;
			document.getElementById('msfolder').required = false;
		} else {
			document.getElementById('msfolder').readOnly = false;
			document.getElementById('msfolder').required = true;
		}
		document.getElementById('msurl').innerHTML = document.getElementById('msdataurl'+msid).getAttribute('data-value');
		document.getElementById('msurlwrap').className = 'elx5_zero';
		if (msid == 1) {
			document.getElementById('msactive').checked = true;
		} else {
			if (document.getElementById('msdataactive'+msid).getAttribute('data-value') == 1) {
				document.getElementById('msactive').checked = true;
			} else {
				document.getElementById('msactive').checked = false;
			}
		}
		document.getElementById('msdatabasewrap').className = 'elx5_invisible';
		document.getElementById('msid').value = msid;
		document.getElementById('elx5_modaltitlemsm').innerHTML = document.getElementById('elx5_modalmsm').getAttribute('data-editlng');
		if (msid > 1) {
			var msfolder = document.getElementById('msdatafolder'+msid).getAttribute('data-value');
			var txt = 'RewriteRule ^'+msfolder+'/'+elxisadir+'/inner.php '+elxisadir+'/inner.php [L]'+"\r\n";
			txt += 'RewriteRule ^'+msfolder+'/inner.php(.*) inner.php [L]'+"\r\n";
			txt += 'RewriteRule ^'+msfolder+'/(.*) $1';
			document.getElementById('mshtaccrules').innerHTML = txt;
			document.getElementById('mshtaccwrap').className = 'elx5_zero';
		} else {
			document.getElementById('mshtaccwrap').className = 'elx5_invisible';
		}
	}
	elx5ModalOpen('msm');
}


function elx5CPSaveMultisite() {
	var edata = {};
	edata.msid = parseInt(document.getElementById('msid').value, 10);
	edata.name = document.getElementById('msname').value;
	edata.folder = document.getElementById('msfolder').value;
	edata.active = (document.getElementById('msactive').checked === true) ? 1 : 0;

	if (edata.msid == 0) {
		var sObj = document.getElementById('msdb_type');
		edata.db_type = sObj.options[sObj.selectedIndex].value;
		edata.db_host = document.getElementById('msdb_host').value;
		if (edata.db_host == '') { document.getElementById('msdb_host').focus(); return; }
		edata.db_port = parseInt(document.getElementById('msdb_host').value, 10);
		edata.db_name = document.getElementById('msdb_name').value;
		if (edata.db_name == '') { document.getElementById('msdb_name').focus(); return; }
		edata.db_prefix = document.getElementById('msdb_prefix').value;
		edata.db_user = document.getElementById('msdb_user').value;
		if (edata.db_user == '') { document.getElementById('msdb_user').focus(); return; }
		edata.db_pass = document.getElementById('msdb_pass').value;
		if (edata.db_pass == '') { document.getElementById('msdb_pass').focus(); return; }
		edata.db_dsn = document.getElementById('msdb_dsn').value;
		edata.db_scheme = document.getElementById('msdb_scheme').value;
		var sObj = document.getElementById('msdb_import');
		edata.db_import = sObj.options[sObj.selectedIndex].value;
	}

	var successfunc = function(xreply) {
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			elx5ModalMessageShow('msm', 'Could not save data!', 'elx5_error');
			return false;
		}
		if (parseInt(jsonObj.success, 10) < 1) {
			if (jsonObj.message != '') {
				var emsg = jsonObj.message;
			} else {
				var emsg = 'Action failed!';
			}
			elx5ModalMessageShow('msm', emsg, 'elx5_error');
		}
		if (parseInt(jsonObj.success, 10) == 1) {
			elx5ModalClose('msm');
			location.reload(true);
		}
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		document.getElementById('mssave').innerHTML = document.getElementById('mssave').getAttribute('data-savelng');
		elx5ModalMessageShow('msm', 'Error! '+errorThrown, 'elx5_error');
	}

	document.getElementById('mssave').innerHTML = document.getElementById('mssave').getAttribute('data-waitlng');

	edata.rnd = Math.floor((Math.random()*100)+1);
	var eurl = document.getElementById('fmmsite').action;
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}


function elx5CPRunCron(lastrunidx, elidx) {
	if (cronrunwait == 1) { return false; }
	var eurl = document.getElementById('fmconfig').getAttribute('data-inlink')+'utilities/runcron';
	var runnowtxt = document.getElementById(elidx).innerHTML;
	document.getElementById(elidx).innerHTML = document.getElementById('fmconfig').getAttribute('data-waitlng');
	var successfunc = function(xreply) {
		cronrunwait = 0;
		document.getElementById(elidx).innerHTML = runnowtxt;
		var jsonObj = JSON.parse(xreply);
		if (parseInt(jsonObj.success, 10) == 1) {
			document.getElementById('cronlastrun').innerHTML = jsonObj.lastrun;
		} else {
			if (jsonObj.errormsg != '') {
				alert(jsonObj.errormsg);
			} else {
				alert('Request failed!');
			}
			return false;
		}
	}

	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		cronrunwait = 0;
		document.getElementById(elidx).innerHTML = runnowtxt;
		alert('Error! '+errorThrown);
	}

	rnd = Math.floor((Math.random()*100)+1);
	var edata = { 'rnd':rnd };
	cronrunwait = 1;
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

function elx5CPCheckFTP() {
	var rObj = document.getElementById('elxcp_ftpresponse');
	rObj.innerHTML = document.getElementById('fmconfig').getAttribute('data-waitlng');
	rObj.className = 'elx5_sminfo';
	var successfunc = function(xreply) {
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			rObj.innerHTML = 'Action failed! Response is not a valid JSON document.';
			rObj.className = 'elx5_smwarning';
			return false;
		}
		if (parseInt(jsonObj.success, 10) == 1) {
			rObj.innerHTML = jsonObj.message;
			rObj.className = 'elx5_smsuccess';
		} else {
			rObj.innerHTML = (jsonObj.message != '') ? jsonObj.message : 'Action failed!';
			rObj.className = 'elx5_smwarning';
		}
	};
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		rObj.className = 'elx5_smwarning';
		rObj.innerHTML = 'Error! '+errorThrown;
	}

	var eurl = document.getElementById('fmconfig').getAttribute('data-inlink')+'utilities/checkftp';
	var edata = {
		'fho': document.getElementById('cfgftp_host').value,
		'fpo': parseInt(document.getElementById('cfgftp_port').value),
		'fus': document.getElementById('cfgftp_user').value,
		'fpa': document.getElementById('cfgftp_pass').value,
		'fro': document.getElementById('cfgftp_root').value
	};

	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

function elx5CPSendTestMail() {
	var rObj = document.getElementById('elxcp_mailresponse');
	rObj.innerHTML = document.getElementById('fmconfig').getAttribute('data-waitlng');
	rObj.className = 'elx5_sminfo';
	var successfunc = function(xreply) {
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			rObj.innerHTML = 'Action failed! Response is not a valid JSON document.';
			rObj.className = 'elx5_smwarning';
			return false;
		}
		if (parseInt(jsonObj.success, 10) == 1) {
			rObj.innerHTML = jsonObj.message;
			rObj.className = 'elx5_smsuccess';
		} else {
			rObj.innerHTML = (jsonObj.message != '') ? jsonObj.message : 'Action failed!';
			rObj.className = 'elx5_smwarning';
		}
	};
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		rObj.className = 'elx5_smwarning';
		rObj.innerHTML = 'Error! '+errorThrown;
	}

	var eurl = document.getElementById('fmconfig').getAttribute('data-inlink')+'utilities/mailtest';
	var mObj = document.getElementById('cfgmail_method');
	var method = mObj.options[mObj.selectedIndex].value;
	var sObj = document.getElementById('cfgmail_smtp_secure');
	var msecure = sObj.options[sObj.selectedIndex].value;
	var saObj = document.getElementById('cfgmail_auth_method');
	var mauthmeth = saObj.options[saObj.selectedIndex].value;
	var mauth = 0;
	if (document.getElementById('cfgmail_smtp_auth').checked) { mauth = 1; }

	var edata = {
		'mmeth': method,
		'mname': document.getElementById('cfgmail_name').value,
		'memail': document.getElementById('cfgmail_email').value,
		'mfname': document.getElementById('cfgmail_from_name').value,
		'mfemail': document.getElementById('cfgmail_from_email').value,
		'mmname': document.getElementById('cfgmail_manager_name').value,
		'mmemail': document.getElementById('cfgmail_manager_email').value,
		'mhost': document.getElementById('cfgmail_smtp_host').value,
		'mport': parseInt(document.getElementById('cfgmail_smtp_port').value),
		'mauth': mauth,
		'mauthmeth': mauthmeth,
		'msecure': msecure,
		'muser': document.getElementById('cfgmail_smtp_user').value,
		'mpass': document.getElementById('cfgmail_smtp_pass').value
	};

	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

function elx5CPConfigGmail() {
	var sendermail = document.getElementById('cfgmail_from_email').value;
	var loginuser = document.getElementById('cfgmail_smtp_user').value;
	if (sendermail != '') {
		if (sendermail.indexOf('@gmail.') > -1) {
			if (loginuser == '') {
				document.getElementById('cfgmail_smtp_user').value = sendermail;
			} else if (loginuser.indexOf('@gmail.') == -1) {
				document.getElementById('cfgmail_smtp_user').value = sendermail;
			}
		} else if (loginuser.indexOf('@gmail.') > -1) {
			if (sendermail == '') {
				document.getElementById('cfgmail_from_email').value = loginuser;
			} else if (sendermail.indexOf('@gmail.') == -1) {
				document.getElementById('cfgmail_from_email').value = loginuser;
			}
		}
	}

	document.getElementById('cfgmail_method').value = 'gmail';
	document.getElementById('cfgmail_smtp_host').value = 'smtp.gmail.com';
	document.getElementById('cfgmail_smtp_port').value = '465';
	document.getElementById('cfgmail_smtp_secure').value = 'ssl';
	document.getElementById('cfgmail_smtp_auth').checked = true;
	document.getElementById('cfgmail_auth_method').value = 'LOGIN';
}

function elx5CPViewCodeFile() {
	var item = elx5SelectedTableItem('ceditortbl', false);
	if ((item === false) || (item == '')) { return; }
	if (!document.getElementById('datarow'+item)) { return; }
	var codeObj = document.getElementById('datarow'+item);
	var ftype = codeObj.getAttribute('data-type');
	var can_view = false;
	if ((ftype == 'css') || (ftype == 'js') || (ftype == 'html') || (ftype == 'xml')) { can_view = true; }
	if (!can_view) {
		alert('You cannot view files of type '+ftype+'!');
		return;
	}
	var relpath = codeObj.getAttribute('data-relpath');
	var gourl = document.getElementById('elxcp_rootlink').innerHTML+'/'+relpath;
	window.open(gourl);
}

function elx5CPValidateCodeFile() {
	var item = elx5SelectedTableItem('ceditortbl', false);
	if ((item === false) || (item == '')) { return; }
	if (!document.getElementById('datarow'+item)) { return; }
	var codeObj = document.getElementById('datarow'+item);
	var ftype = codeObj.getAttribute('data-type');
	if (ftype != 'css') {
		alert('You validate only CSS files!');
		return;
	}
	var relpath = codeObj.getAttribute('data-relpath');
	var valurl = document.getElementById('elxcp_rootlink').innerHTML+'/'+relpath;
	var gourl = 'https://jigsaw.w3.org/css-validator/validator?uri='+encodeURI(valurl);
	window.open(gourl);
}