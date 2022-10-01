/**
Package			Elxis CMS
Subpackage		Installer / JS
Author			Elxis Team ( http://www.elxis.org )
Copyright		(c) 2006-2019 Elxis Team (http://www.elxis.org). All rights reserved.
License			Elxis Public License ( http://www.elxis.org/elxis-public-license.html
Last update		2019-02-04 09:19:00 GMT
Description 	Javascript for Elxis installer
*/

function ielxSwitchLang() {
	document.getElementById('ielx_fmlng').submit();
}

function ielxAgreeTerms() {
	if (document.getElementById('ielxlicagree').checked) {
		document.getElementById('ielxcontbtn1').className = 'ielx_continue';
	} else {
		document.getElementById('ielxcontbtn1').className = 'ielx_nocontinue';
	}
}

function ielxSwitchBlock(show, hide1, hide2, hide3, terms) {
	if (terms == 1) {
		if (!document.getElementById('ielxlicagree').checked) { return; }
	}
	if (hide1 > 0) { document.getElementById('ielblock'+hide1).className = 'ielx_blockinv'; }
	if (hide2 > 0) { document.getElementById('ielblock'+hide2).className = 'ielx_blockinv'; }
	if (hide3 > 0) { document.getElementById('ielblock'+hide3).className = 'ielx_blockinv'; }
	document.getElementById('ielblock'+show).className = 'ielx_block';
}

function ielxToggle(elid) {
	if (!document.getElementById(elid)) { return; }
	if (document.getElementById(elid).className == 'ielx_invisible') {
		document.getElementById(elid).className = 'ielx_zero';
	} else {
		document.getElementById(elid).className = 'ielx_invisible';
	}
}

function ielxToggleFTP(useftp) {
	var useftp = document.getElementById('cfg_ftp').checked ? 1 : 0;
	if (useftp == 1) {
		document.getElementById('ftp_details').className = 'ielx_zero';
	} else {
		document.getElementById('ftp_details').className = 'ielx_invisible';
	}
}

function ielxCheckDB() {
	var dbtObj = document.getElementById('cfg_db_type');
	var dbtype = dbtObj.options[dbtObj.selectedIndex].value;
	var rnd = Math.floor((Math.random()*100)+1);

	var successfunc = function(xreply) {
		if (xreply == 'OK') {
			document.getElementById('dbresponse').className = 'ielx_success';//todo: fontawesome v5
			document.getElementById('dbresponse').innerHTML = '<i class="fa fa-check" aria-hidden="true"></i> The database settings are correct.';
		} else {
			var rmsg = 'Could not connect to database!';
			if (xreply.substr(0,4) == 'msg:') { rmsg = xreply.substr(4); }
			document.getElementById('dbresponse').className = 'ielx_error';
			document.getElementById('dbresponse').innerHTML = '<i class="fa fa-times" aria-hidden="true"></i> '+rmsg;
			setTimeout(function(){ document.getElementById('dbresponse').className = 'ielx_invisible'; }, 2500);
		}
	}

	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		document.getElementById('dbresponse').className = 'ielx_error';
		document.getElementById('dbresponse').innerHTML = '<i class="fa fa-times" aria-hidden="true"></i> '+errorThrown;
		setTimeout(function(){ document.getElementById('dbresponse').className = 'ielx_invisible'; }, 2500);
	}

	var edata = {
		'rnd': rnd,
		'action': 'checkdb',
		'dty': dbtype,
		'dho': document.getElementById('cfg_db_host').value,
		'dpo': parseInt(document.getElementById('cfg_db_port').value, 10),
		'dna': document.getElementById('cfg_db_name').value,
		'dpr': document.getElementById('cfg_db_prefix').value,
		'dus': document.getElementById('cfg_db_user').value,
		'dpa': document.getElementById('cfg_db_pass').value,
		'dds': document.getElementById('cfg_db_dsn').value,
		'dsc': document.getElementById('cfg_db_scheme').value
	};

	var eurl = document.getElementById('ei_baseurl').innerHTML+'/includes/install/inc/tools.php';
	document.getElementById('dbresponse').className = 'ielx_note';
	document.getElementById('dbresponse').innerHTML = 'Please wait...';

	elxAjax('POST', eurl, edata, 'dbresponse', '', successfunc, errorfunc);
}


function ielxCheckFTP() {
	var rnd = Math.floor((Math.random()*100)+1);
	var edata = {
		'rnd': rnd,
		'action': 'checkftp',
		'fho': document.getElementById('cfg_ftp_host').value,
		'fpo': parseInt(document.getElementById('cfg_ftp_port').value, 10),
		'fus': document.getElementById('cfg_ftp_user').value,
		'fpa': document.getElementById('cfg_ftp_pass').value,
		'fro': document.getElementById('cfg_ftp_root').value
	};
	var successfunc = function(xreply) {
		try {
			var jsonObj = JSON.parse(xreply);
		} catch (e) {
			document.getElementById('ftpresponse').className = 'ielx_error';//todo: fontawesome v5
			document.getElementById('ftpresponse').innerHTML = '<i class="fa fa-times" aria-hidden="true"></i> Could not complete your request.';
			setTimeout(function(){ document.getElementById('ftpresponse').className = 'ielx_invisible'; }, 2500);
			return false;
		}

		if (parseInt(jsonObj.success, 10) == 1) {
			if (jsonObj.message != '') {
				document.getElementById('ftpresponse').className = 'ielx_warn';
				document.getElementById('ftpresponse').innerHTML = '<i class="fa fa-check" aria-hidden="true"></i> '+jsonObj.message;
			} else {
				document.getElementById('ftpresponse').className = 'ielx_success';
				document.getElementById('ftpresponse').innerHTML = '<i class="fa fa-check" aria-hidden="true"></i> The FTP settings are correct.';
			}
		} else {
			document.getElementById('ftpresponse').className = 'ielx_error';
			document.getElementById('ftpresponse').innerHTML = '<i class="fa fa-times" aria-hidden="true"></i> '+jsonObj.message;
		}
		setTimeout(function(){ document.getElementById('ftpresponse').className = 'ielx_invisible'; }, 2500);
	}

	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		document.getElementById('ftpresponse').className = '<i class="fa fa-times" aria-hidden="true"></i> ielx_error';
		document.getElementById('ftpresponse').innerHTML = errorThrown;
		setTimeout(function(){ document.getElementById('ftpresponse').className = 'ielx_invisible'; }, 2500);
	}

	var eurl = document.getElementById('ei_baseurl').innerHTML+'/includes/install/inc/tools.php';
	document.getElementById('ftpresponse').className = 'ielx_note';
	document.getElementById('ftpresponse').innerHTML = 'Please wait...';

	elxAjax('POST', eurl, edata, 'ftpresponse', '', successfunc, errorfunc);
}



function ielxAddLang() {
	var sObj = document.getElementById('cfg_sitelangs_selector');
	var val = sObj.options[sObj.selectedIndex].value;
	if (val == '') {
		document.getElementById('cfg_sitelangs').value = '';
		document.getElementById('cfg_sitelangs_items').innerHTML = '<div class="ielx_msel_itemall">'+sObj.getAttribute('data-lngalllangs')+'</div>';
		return;
	}
	if (val == 'none') {
		document.getElementById('cfg_sitelangs').value = 'none';
		document.getElementById('cfg_sitelangs_items').innerHTML = '<div class="ielx_msel_itemnone">'+sObj.getAttribute('data-lngnonelangs')+'</div>';
		return;
	}
	var curvals_str = document.getElementById('cfg_sitelangs').value;
	if (curvals_str == 'none') {
		curvals_str = '';
		document.getElementById('cfg_sitelangs').value = '';
	}
	var found = false;
	if (curvals_str != '') {
		var curvals = curvals_str.split(',');
		for (var i=0; i < curvals.length; i++) {
			if (curvals[i] == val) { found = true; break; }//already exists
		}
	}
	if (found) { return; }
	var lngup = val.toUpperCase();
	var lngremove = sObj.getAttribute('data-lngremove');
	if (curvals_str == '') {
		document.getElementById('cfg_sitelangs_items').innerHTML = '';
		curvals_str = val;
		var curvals = [val];
	} else {
		curvals.push(val); 
		curvals_str += ','+val;
	}
	document.getElementById('cfg_sitelangs').value = curvals_str;
	document.getElementById('cfg_sitelangs_items').innerHTML += '<a href="javascript:void(null);" class="ielx_msel_item" onclick="ielxRemoveLang(\''+val+'\');" title="'+lngremove+'">'+lngup+' <span>x</span></a>';
}

function ielxRemoveLang(val) {
	var sObj = document.getElementById('cfg_sitelangs_selector');
	var newvals = [];
	var found = false;
	var vals = document.getElementById('cfg_sitelangs').value.split(',');
	for (var k=0; k < vals.length; k++) {
		if (vals[k] != val) { newvals.push(vals[k]); }
	}
	if (newvals.length == 0) {
		sObj.selectedIndex = 0;
		document.getElementById('cfg_sitelangs').value = '';
		document.getElementById('cfg_sitelangs_items').innerHTML = '<div class="ielx_msel_itemall">'+sObj.getAttribute('data-lngalllangs')+'</div>';
		return;
	}
	var txt = '';
	var i, v, k, lngup;
	var lngremove = sObj.getAttribute('data-lngremove');
	for (i=0; i < sObj.length; i++) {
		v = sObj.options[i].value;
		if (v == '') { continue; }
		for (k=0; k < newvals.length; k++) {
			if (newvals[k] == v) {
				lngup = sObj.options[i].value.toUpperCase();
				txt += '<a href="javascript:void(null);" class="ielx_msel_item" onclick="ielxRemoveLang(\''+v+'\');" title="'+lngremove+'">'+lngup+' <span>x</span></a>';
				break;
			}
		}
	}
	document.getElementById('cfg_sitelangs').value = newvals.join();
	document.getElementById('cfg_sitelangs_items').innerHTML = txt;
}


function ielxTrim(str) {
	return str.replace(/^\s+|\s+$/g,'');
}


function ielxValidateConfig() {
	var sname = ielxTrim(document.getElementById('cfg_sitename').value);
	if (sname == '') {
		document.getElementById('cfg_sitename').focus();
		return false;
	}

	var dbtObj = document.getElementById('cfg_db_type');
	var dbtype = dbtObj.options[dbtObj.selectedIndex].value;
	if (dbtype == '') {
		dbtObj.focus();
		return false;
	}

	var dbhost = ielxTrim(document.getElementById('cfg_db_host').value);
	if (dbhost == '') {
		document.getElementById('cfg_db_host').focus();
		return false;
	}

	var dbname = ielxTrim(document.getElementById('cfg_db_name').value);
	if (dbname == '') {
		document.getElementById('cfg_db_name').focus();
		return false;
	}

	var dbprefix = ielxTrim(document.getElementById('cfg_db_prefix').value);
	if (dbprefix == '') {
		document.getElementById('cfg_db_prefix').focus();
		return false;
	}

	var dbscheme = ielxTrim(document.getElementById('cfg_db_scheme').value);
	var dbdsn = ielxTrim(document.getElementById('cfg_db_dsn').value);

	if (((dbtype == 'sqlite') || (dbtype == 'sqlite2')) && (dbscheme == '')) {
		alert('A schema string is required for '+dbtype);
		document.getElementById('cfg_db_scheme').focus();
		return false;
	}

	if ((dbdsn == '') && (dbscheme == '')) {
		var dbuser = ielxTrim(document.getElementById('cfg_db_user').value);
		var dbpass = ielxTrim(document.getElementById('cfg_db_pass').value);
		if (dbuser == '') {
			document.getElementById('cfg_db_user').focus();
			return false;
		}
		if (dbpass == '') {
			document.getElementById('cfg_db_pass').focus();
			return false;
		}
	}

	if (document.getElementById('cfg_ftp').checked) {
		var fhost = ielxTrim(document.getElementById('cfg_ftp_host').value);
		if (fhost == '') {
			document.getElementById('cfg_ftp_host').focus();
			return false;
		}

		var fport = ielxTrim(document.getElementById('cfg_ftp_port').value);
		fport = parseInt(fport, 10);
		if (fport < 1) { document.getElementById('cfg_ftp_port').value = 21; }

		var froot = ielxTrim(document.getElementById('cfg_ftp_root').value);
		if (froot == '') { froot = '/'; }

		var fuser = ielxTrim(document.getElementById('cfg_ftp_user').value);
		if (fuser == '') {
			document.getElementById('cfg_ftp_user').focus();
			return false;
		}
		var fpass = ielxTrim(document.getElementById('cfg_ftp_pass').value);
		if (fpass == '') {
			document.getElementById('cfg_ftp_pass').focus();
			return false;
		}
	}

	var mObj = document.getElementById('cfg_mail_method');
	var mailmethod = mObj.options[mObj.selectedIndex].value;
	if (mailmethod == 'smtp') {
		if (ielxTrim(document.getElementById('cfg_smtp_host').value) == '') {
			document.getElementById('cfg_smtp_host').focus();
			return false;
		}
		var sport = ielxTrim(document.getElementById('cfg_smtp_port').value);
		sport = parseInt(sport, 10);
		if (sport < 1) { document.getElementById('cfg_smtp_port').value = 25; }
		if (document.getElementById('cfg_smtp_auth').checked) {
			if (ielxTrim(document.getElementById('cfg_smtp_user').value) == '') {
				document.getElementById('cfg_smtp_user').focus();
				return false;
			}
			if (ielxTrim(document.getElementById('cfg_smtp_pass').value) == '') {
				document.getElementById('cfg_smtp_pass').focus();
				return false;
			}
		}
	}

	return true;
}


function ielxMakeUname() {
	var eurl = document.getElementById('ei_baseurl').innerHTML+'/includes/install/inc/tools.php';
	var etype = 'POST';
	var curname = ielxTrim(document.getElementById('u_uname').value);
	var curlang = document.getElementById('langfamily').value;

	var successfunc = function(xreply) {
    	try {
        	var jsonObj = JSON.parse(xreply);
    	} catch (e) {
    		alert('Elxis could not complete your request due to an error.');
        	return false;
    	}

		if (parseInt(jsonObj.success, 10) == 1) {
			if (jsonObj.uname != '') {
    			document.getElementById('u_uname').value = jsonObj.uname;
			} else {
    			alert('Request failed! Elxis can not propose a username.');
			}
		} else {
			if (jsonObj.message != '') {
				alert(jsonObj.message);
			} else {
				alert('Elxis could not complete your request due to an error.');
			}
		}
	}

	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) { alert(errorThrown); }

	var rnd = Math.floor((Math.random()*100)+1);
	var edata = {
		'rnd': rnd,
		'action': 'makeuname',
		'curname': curname,
		'curlang': curlang
	};

	elxAjax(etype, eurl, edata, '', '', successfunc, errorfunc);
}


function ielxValidateUser() {
	var ufname = ielxTrim(document.getElementById('u_firstname').value);
	if (ufname == '') {
		document.getElementById('u_firstname').focus();
		return false;
	}
	if (ufname.length < 3) {
		alert('Your first name is too short!');
		document.getElementById('u_firstname').focus();
		return false;
	}

	var ulname = ielxTrim(document.getElementById('u_lastname').value);
	if (ulname == '') {
		document.getElementById('u_lastname').focus();
		return false;
	}
	if (ulname.length < 3) {
		alert('Your last name is too short!');
		document.getElementById('u_lastname').focus();
		return false;
	}

	var uemail = ielxTrim(document.getElementById('u_email').value);
	if (uemail == '') {
		document.getElementById('u_email').focus();
		return false;
	}

	if (elxValidateEmail(uemail, false) == false) {
		alert('Please fill in a valid email address!');
		document.getElementById('u_email').focus();
		return false;
	}

	var uuname = ielxTrim(document.getElementById('u_uname').value);
	var upword = ielxTrim(document.getElementById('u_pword').value);
	var upword2 = ielxTrim(document.getElementById('u_pword2').value);
	if (uuname == '') {
		document.getElementById('u_uname').focus();
		return false;
	}
	if (upword == '') {
		document.getElementById('u_pword').focus();
		return false;
	}
	if (upword2 == '') {
		document.getElementById('u_pword2').focus();
		return false;
	}
	var regex = /^[0-9A-Za-z_]+$/;
	if (!regex.test(uuname)){
		alert('Username may contain only latin alphanumeric characters and underscore!');
		document.getElementById('u_uname').focus();
		return false;
	}
	if (uuname.length < 4) {
		alert('Username is too short!');
		document.getElementById('u_uname').focus();
		return false;
	}
	if (!regex.test(upword)){
		alert('Password may contain only latin alphanumeric characters and underscore!');
		document.getElementById('u_pword').focus();
		return false;
	}
	if (upword.length < 8) {
		alert('Password should be at least 8 characters long!');
		document.getElementById('u_pword').focus();
		return false;
	}
	if (upword != upword2) {
		alert('Passwords do not match!');
		document.getElementById('u_pword2').focus();
		return false;
	}
	return true;
}
