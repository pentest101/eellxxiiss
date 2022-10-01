var comuserwait = 0;

function elx5UserEdit() {
	var uid = elx5SelectedTableItem('userstbl', false);
	if (uid === false) { return; }
	uid = parseInt(uid, 10);
	if (uid < 1) { return; }
	window.location.href = document.getElementById('fmsrusers').action+'users/edit.html?uid='+uid;
}

function elx5UserContactForm() {
	var uid = elx5SelectedTableItem('userstbl', false);
	if (uid === false) { return; }
	uid = parseInt(uid, 10);
	if (uid < 1) { return; }
	document.getElementById('cfuid').value = uid;
	var fln = document.getElementById('udataname'+uid).getAttribute('data-value');
	var eml = document.getElementById('udataemail'+uid).getAttribute('data-value');
	document.getElementById('murecipienttext').innerHTML = '<strong>'+fln+'</strong> &lt;'+eml+'&gt;';
	document.getElementById('cfsubject').value = '';
	document.getElementById('cfmessage').value = '';

	elx5ModalOpen('uc');
}

function elx5UserContactSend() {
	if (comuserwait == 1) { return; }
	elx5ModalMessageHide('uc');

	var edata = {};
	edata.uid = parseInt(document.getElementById('cfuid').value, 10);
	if (edata.uid < 1) { return; }
	edata.subject = document.getElementById('cfsubject').value;
	edata.message = document.getElementById('cfmessage').value;
	if (edata.subject == '') {
		document.getElementById('cfsubject').focus();
		return;
	}
	if (edata.message == '') {
		elx5ModalMessageShow('uc', 'Please type a message!', 'elx5_error');
		return;
	}

	var successfunc = function(xreply) {
		comuserwait = 0;
		document.getElementById('cfsendmsg').innerHTML = document.getElementById('cfsendmsg').getAttribute('data-sendlng');
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			elx5ModalMessageShow('uc', 'Could not load data!', 'elx5_error');
			return false;
		}
		if (parseInt(jsonObj.success, 10) < 1) {
			if (jsonObj.message != '') {
				var emsg = jsonObj.message;
			} else {
				var emsg = 'Action failed!';
			}
			elx5ModalMessageShow('uc', emsg, 'elx5_error');
		}
		if (parseInt(jsonObj.success, 10) == 1) {
			elx5ModalClose('uc');
		}
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		comuserwait = 0;
		document.getElementById('cfsendmsg').innerHTML = document.getElementById('cfsendmsg').getAttribute('data-sendlng');
		elx5ModalMessageShow('uc', 'Error! '+errorThrown, 'elx5_error');
	}

	document.getElementById('cfsendmsg').innerHTML = document.getElementById('cfsendmsg').getAttribute('data-waitlng');

	edata.rnd = Math.floor((Math.random()*100)+1);
	var eurl = document.getElementById('fmcontactuser').action;
	comuserwait = 1;
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

function elx5UserEditACL(is_add) {
	if (is_add) {
		document.getElementById('eacl_category').selectedIndex = 0;
		document.getElementById('eacl_category').disabled = false;
		document.getElementById('eacl_category_other_box').className = 'elx5_invisible';
		document.getElementById('eacl_category_other').readOnly = false;
		document.getElementById('eacl_category_other').value = '';
		document.getElementById('eacl_element').selectedIndex = 0;
		document.getElementById('eacl_element').disabled = false;
		document.getElementById('eacl_element_other_box').className = 'elx5_invisible';
		document.getElementById('eacl_element_other').readOnly = false;
		document.getElementById('eacl_element_other').value = '';
		document.getElementById('eacl_aclaction').selectedIndex = 0;
		document.getElementById('eacl_aclaction').disabled = false;
		document.getElementById('eacl_aclaction_other_box').className = 'elx5_invisible';
		document.getElementById('eacl_aclaction_other').readOnly = false;
		document.getElementById('eacl_aclaction_other').value = '';
		document.getElementById('eacl_identity').readOnly = false;
		document.getElementById('eacl_identity').value = '0';
		document.getElementById('eacl_minlevel').value = '0';
		document.getElementById('eacl_minlevel_value').innerHTML = '0';
		document.getElementById('eacl_gid').selectedIndex = 0;
		document.getElementById('eacl_uid').value = '0';
		document.getElementById('eacl_aclvalue').selectedIndex = 1;
		document.getElementById('eacl_id').value = '0';

		document.getElementById('elx5_modaltitleacm').innerHTML = document.getElementById('elx5_modalacm').getAttribute('data-addlng');
		document.getElementById('elx5_modalmessageacm').className = 'elx5_invisible';
		document.getElementById('elx5_modalcontentsacm').className = 'elx5_zero';
		elx5ModalOpen('acm');
		return;
	}

	var aclid = elx5SelectedTableItem('acltbl', false);
	if (aclid === false) { return; }
	aclid = parseInt(aclid, 10);
	if (aclid < 1) { return; }

	var successfunc = function(xreply) {
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			elx5ModalMessageShow('acm', 'Could not load data!', 'elx5_error');
			return false;
		}
		if (parseInt(jsonObj.success, 10) < 1) {
			if (jsonObj.message != '') {
				var emsg = jsonObj.message;
			} else {
				var emsg = 'Action failed!';
			}
			elx5ModalMessageShow('acm', emsg, 'elx5_error');
		}

		if (parseInt(jsonObj.success, 10) == 1) {
			var i, opv;
			var sObj = document.getElementById('eacl_category');

			var selidx = -1, otheridx = -1, showother = false;
			for (i=0; i < sObj.length; i++) {
				optv = sObj.options[i].value;
				if (optv == jsonObj.category) { selidx = i; }
				if (optv == 'OTHER') { otheridx = i; }
			}
			if (selidx > -1) {
				sObj.selectedIndex = selidx;
				sObj.disabled = true;
				document.getElementById('eacl_category_other_box').className = 'elx5_invisible';
				document.getElementById('eacl_category_other').value = '';
			} else {
				sObj.selectedIndex = otheridx;
				sObj.disabled = true;
				document.getElementById('eacl_category_other_box').className = 'elx5_tsspace';
				document.getElementById('eacl_category_other').value = jsonObj.category;
				document.getElementById('eacl_category_other').readOnly = true;
			}

			sObj = document.getElementById('eacl_element');
			selidx = -1, otheridx = -1, showother = false;
			for (i=0; i < sObj.length; i++) {
				optv = sObj.options[i].value;
				if (optv == jsonObj.element) { selidx = i; }
				if (optv == 'OTHER') { otheridx = i; }
			}
			if (selidx > -1) {
				sObj.selectedIndex = selidx;
				sObj.disabled = true;
				document.getElementById('eacl_element_other_box').className = 'elx5_invisible';
				document.getElementById('eacl_element_other').value = '';
			} else {
				sObj.selectedIndex = otheridx;
				sObj.disabled = true;
				document.getElementById('eacl_element_other_box').className = 'elx5_tsspace';
				document.getElementById('eacl_element_other').value = jsonObj.element;
				document.getElementById('eacl_element_other').readOnly = true;
			}

			sObj = document.getElementById('eacl_aclaction');
			selidx = -1, otheridx = -1, showother = false;
			for (i=0; i < sObj.length; i++) {
				optv = sObj.options[i].value;
				if (optv == jsonObj.aclaction) { selidx = i; }
				if (optv == 'OTHER') { otheridx = i; }
			}
			if (selidx > -1) {
				sObj.selectedIndex = selidx;
				sObj.disabled = true;
				document.getElementById('eacl_aclaction_other_box').className = 'elx5_invisible';
				document.getElementById('eacl_aclaction_other').value = '';
			} else {
				sObj.selectedIndex = otheridx;
				sObj.disabled = true;
				document.getElementById('eacl_aclaction_other_box').className = 'elx5_tsspace';
				document.getElementById('eacl_aclaction_other').value = jsonObj.aclaction;
				document.getElementById('eacl_aclaction_other').readOnly = true;
			}

			document.getElementById('eacl_identity').value = jsonObj.identity;
			document.getElementById('eacl_identity').readOnly = true;

			sObj = document.getElementById('eacl_gid');
			selidx = 0;
			for (i=0; i < sObj.length; i++) {
				optv = sObj.options[i].value;
				if (optv == jsonObj.gid) { selidx = i; break; }
			}
			sObj.selectedIndex = selidx;

			sObj = document.getElementById('eacl_aclvalue');
			selidx = 0;
			for (i=0; i < sObj.length; i++) {
				optv = sObj.options[i].value;
				if (optv == jsonObj.aclvalue) { selidx = i; break; }
			}
			sObj.selectedIndex = selidx;

			document.getElementById('eacl_minlevel').value = jsonObj.minlevel;
			document.getElementById('eacl_minlevel_value').innerHTML = jsonObj.minlevel;
			document.getElementById('eacl_uid').value = jsonObj.uid;
			document.getElementById('eacl_id').value = jsonObj.aclid;

			elx5ModalMessageHide('acm');
			document.getElementById('elx5_modalcontentsacm').className = 'elx5_zero';
		}
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5ModalMessageShow('acm', 'Error! '+errorThrown, 'elx5_error');
	}

	document.getElementById('elx5_modaltitleacm').innerHTML = document.getElementById('elx5_modalacm').getAttribute('data-editlng');
	document.getElementById('elx5_modalmessageacm').className = 'elx5_vpad';
	document.getElementById('elx5_modalmessageacm').innerHTML = document.getElementById('elx5_modalbodyacm').getAttribute('data-waitlng');
	document.getElementById('elx5_modalcontentsacm').className = 'elx5_invisible';
	elx5ModalOpen('acm');

	var rnd = Math.floor((Math.random()*100)+1);
	var edata = { 'aclid':aclid, 'rnd':rnd };
	var eurl = document.getElementById('fmeditaclrule').action+'getacldata';
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

function elx5UserSaveACL() {
	var edata = {};
	var sObj = document.getElementById('eacl_category');
	edata.category = sObj.options[sObj.selectedIndex].value;
	if (edata.category == 'OTHER') { edata.category = document.getElementById('eacl_category_other').value; }
	if (edata.category == '') { sObj.focus(); return; }

	sObj = document.getElementById('eacl_element');
	edata.element = sObj.options[sObj.selectedIndex].value;
	if (edata.element == 'OTHER') { edata.element = document.getElementById('eacl_element_other').value; }
	if (edata.element == '') { sObj.focus(); return; }

	sObj = document.getElementById('eacl_aclaction');
	edata.aclaction = sObj.options[sObj.selectedIndex].value;
	if (edata.aclaction == 'OTHER') { edata.aclaction = document.getElementById('eacl_aclaction_other').value; }
	if (edata.aclaction == '') { sObj.focus(); return; }

	edata.identity = parseInt(document.getElementById('eacl_identity').value, 10);
	edata.minlevel = parseInt(document.getElementById('eacl_minlevel').value, 10);
	sObj = document.getElementById('eacl_gid');
	edata.gid = parseInt(sObj.options[sObj.selectedIndex].value, 10);
	edata.uid = parseInt(document.getElementById('eacl_uid').value, 10);
	sObj = document.getElementById('eacl_aclvalue');
	edata.aclvalue = parseInt(sObj.options[sObj.selectedIndex].value, 10);
	edata.aclid = parseInt(document.getElementById('eacl_id').value, 10);

	var successfunc = function(xreply) {
		document.getElementById('eacl_save').innerHTML = document.getElementById('eacl_save').getAttribute('data-savelng');
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			elx5ModalMessageShow('acm', 'Response is not a valid JSON document!', 'elx5_error');
			return false;
		}
		if (parseInt(jsonObj.success, 10) == 1) {
			elx5ModalClose('acm');
			location.reload(true);
		} else {
			if (jsonObj.message != '') {
				var emsg = jsonObj.message;
			} else {
				var emsg = 'Action failed!';
			}
			elx5ModalMessageShow('acm', emsg, 'elx5_error');
		}
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		document.getElementById('eacl_save').innerHTML = document.getElementById('eacl_save').getAttribute('data-savelng');
		elx5ModalMessageShow('acm', 'Error! '+errorThrown, 'elx5_error');
	}

	document.getElementById('eacl_save').innerHTML = document.getElementById('eacl_save').getAttribute('data-waitlng');
	edata.rnd = Math.floor((Math.random()*100)+1);
	var eurl = document.getElementById('fmeditaclrule').action+'save';
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

function elx5UserEditGroup(is_add, editgid) {
	if (typeof editgid == 'undefined') { editgid = 0; }
	editgid = parseInt(editgid, 10);

	if (is_add) {
		var gid = 0;
	} else if (editgid > 0) {
		var gid = editgid;
	} else {
		var gid = elx5SelectedTableItem('groupstbl', false);
		if (gid === false) { return; }
		gid = parseInt(gid, 10);
		if (gid < 1) { return; }
	}

	var successfunc = function(xreply) {
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			elx5ModalMessageShow('grm', 'Could not load data!', 'elx5_error');
			return false;
		}
		if (parseInt(jsonObj.success, 10) < 1) {
			if (jsonObj.message != '') {
				var emsg = jsonObj.message;
			} else {
				var emsg = 'Action failed!';
			}
			elx5ModalMessageShow('grm', emsg, 'elx5_error');
		}

		if (parseInt(jsonObj.success, 10) == 1) {
			document.getElementById('egr_id_text').innerHTML = jsonObj.gid;
			document.getElementById('egr_groupname').value = jsonObj.groupname;
			document.getElementById('egr_level').value = parseInt(jsonObj.level, 10);
			document.getElementById('egr_level_value').innerHTML = parseInt(jsonObj.level, 10);
			document.getElementById('egr_members_text').innerHTML = parseInt(jsonObj.members, 10);
			document.getElementById('egr_gid').value = parseInt(jsonObj.gid, 10);

			if (jsonObj.readonly == 1) {
				document.getElementById('egr_groupname').readOnly = true;
				document.getElementById('egr_level').disabled = true;
				document.getElementById('egr_save').className = 'elx5_btn elx5_notallowedbtn';
				document.getElementById('egr_save').disabled = true;
				document.getElementById('egr_nomodify').className = 'elx5_warning';
			} else {
				document.getElementById('egr_groupname').readOnly = false;
				document.getElementById('egr_level').disabled = false;
				document.getElementById('egr_save').className = 'elx5_btn elx5_sucbtn';
				document.getElementById('egr_save').disabled = false;
				document.getElementById('egr_nomodify').className = 'elx5_invisible';
			}

			var txt = '';
			if (jsonObj.groupstree) {
				for (k in jsonObj.groupstree) { txt += jsonObj.groupstree[k]+'<br />'; }
			}
			if (txt == '') { txt = '-'; }
			document.getElementById('egr_groupstree_text').innerHTML = txt;

			elx5ModalMessageHide('grm');
			document.getElementById('elx5_modalcontentsgrm').className = 'elx5_zero';
		}
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5ModalMessageShow('grm', 'Error! '+errorThrown, 'elx5_error');
	}

	document.getElementById('elx5_modaltitlegrm').innerHTML = document.getElementById('elx5_modalgrm').getAttribute('data-editlng');
	document.getElementById('elx5_modalmessagegrm').className = 'elx5_vpad';
	document.getElementById('elx5_modalmessagegrm').innerHTML = document.getElementById('elx5_modalbodygrm').getAttribute('data-waitlng');
	document.getElementById('elx5_modalcontentsgrm').className = 'elx5_invisible';
	elx5ModalOpen('grm');

	var rnd = Math.floor((Math.random()*100)+1);
	var edata = { 'gid':gid, 'rnd':rnd };
	var eurl = document.getElementById('fmeditgroup').action+'getgroupdata';
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

function elx5UserSaveGroup() {
	var edata = {};
	edata.groupname = document.getElementById('egr_groupname').value;
	if (edata.groupname == '') { document.getElementById('egr_groupname').focus(); return; }
	edata.level = parseInt(document.getElementById('egr_level').value, 10);
	edata.gid = parseInt(document.getElementById('egr_gid').value, 10);

	var successfunc = function(xreply) {
		document.getElementById('egr_save').innerHTML = document.getElementById('egr_save').getAttribute('data-savelng');
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			elx5ModalMessageShow('grm', 'Response is not a valid JSON document!', 'elx5_error');
			return false;
		}
		if (parseInt(jsonObj.success, 10) == 1) {
			elx5ModalClose('grm');
			location.reload(true);
		} else {
			if (jsonObj.message != '') {
				var emsg = jsonObj.message;
			} else {
				var emsg = 'Action failed!';
			}
			elx5ModalMessageShow('grm', emsg, 'elx5_error');
		}
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		document.getElementById('egr_save').innerHTML = document.getElementById('egr_save').getAttribute('data-savelng');
		elx5ModalMessageShow('grm', 'Error! '+errorThrown, 'elx5_error');
	}

	document.getElementById('egr_save').innerHTML = document.getElementById('egr_save').getAttribute('data-waitlng');
	edata.rnd = Math.floor((Math.random()*100)+1);
	var eurl = document.getElementById('fmeditgroup').action+'save';
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}


function elx5UserPMSOpen(threadid, toid, toname) {
	threadid = parseInt(threadid, 10);
	toid = parseInt(toid, 10);
	if (threadid < 1) { threadid = 0; }
	if (toid < 1) { toid = 0; }
	if (typeof toname === 'undefined') { toname = '-'; }
	document.getElementById('spmrecipients').value = '';
	document.getElementById('spmrecipients_items').innerHTML = '';
	document.getElementById('elx5_user_pmsreplytext').innerHTML = toname;
	document.getElementById('spmreplyto').value = threadid;
	document.getElementById('spmtouid').value = toid;
	if (toid == 0) {
		document.getElementById('elx5_user_pmsrcptbox').className = 'elx5_zero';
		document.getElementById('elx5_user_pmsreplybox').className = 'elx5_invisible';
	} else {
		document.getElementById('elx5_user_pmsrcptbox').className = 'elx5_invisible';
		document.getElementById('elx5_user_pmsreplybox').className = 'elx5_zero';
	}
	document.getElementById('spmmessage').value = '';
	elx5ModalMessageHide('pms');
	elx5ModalOpen('pms');
}


function elx5UserPMSSend() {
	if (comuserwait == 1) { return; }

	elx5ModalMessageHide('pms');

	var edata = {};
	var touid = parseInt(document.getElementById('spmtouid').value, 10);
	if (touid < 1) {
		var recipients = document.getElementById('spmrecipients').value;
		if ((recipients == '') || (recipients == '0')) {
			elx5ModalMessageShow('pms', 'Please select a recipient!', 'elx5_error');
			return;
		}
		edata.toid = recipients;
		edata.replyto = 0;
	} else {
		edata.toid = touid;
		edata.replyto = parseInt(document.getElementById('spmreplyto').value, 10);

	}
	edata.message = document.getElementById('spmmessage').value;
	if (edata.message == '') {
		elx5ModalMessageShow('pms', 'Please type a message!', 'elx5_error');
		return;
	}
	edata.token = document.getElementById('spmtoken').value;

	var successfunc = function(xreply) {
		comuserwait = 0;
		document.getElementById('spmsendmsg').innerHTML = document.getElementById('spmsendmsg').getAttribute('data-sendlng');
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			elx5ModalMessageShow('pms', 'Could not load data!', 'elx5_error');
			return false;
		}
		if (parseInt(jsonObj.success, 10) < 1) {
			if (jsonObj.message != '') {
				var emsg = jsonObj.message;
			} else {
				var emsg = 'Action failed!';
			}
			elx5ModalMessageShow('pms', emsg, 'elx5_error');
		}
		if (parseInt(jsonObj.success, 10) == 1) {
			elx5ModalClose('pms');
			if (window.location.href.indexOf('/pms/') > -1) { location.reload(true); }
		}
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		comuserwait = 0;
		document.getElementById('spmsendmsg').innerHTML = document.getElementById('spmsendmsg').getAttribute('data-sendlng');
		elx5ModalMessageShow('pms', 'Error! '+errorThrown, 'elx5_error');
	}
	document.getElementById('spmsendmsg').innerHTML = document.getElementById('spmsendmsg').getAttribute('data-waitlng');

	edata.rnd = Math.floor((Math.random()*100)+1);
	var eurl = document.getElementById('fmsendpm').action+'send';
	comuserwait = 1;
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}


function elx5UserPMSDelete(threadid) {
	threadid = parseInt(threadid, 10);
	if (threadid < 1) { return; }
	if (!document.getElementById('fmsendpm')) { return; }

	var successfunc = function(xreply) {
		elx5StopPageLoader('elx5_user_pmsloading');
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
			if (document.getElementById('elx5_user_threads')) {//I am on "pms/" page
				var ulObj = document.getElementById('elx5_user_threads');
				if (document.getElementById('elx5_user_thread'+threadid)) {
					var liObj = document.getElementById('elx5_user_thread'+threadid);
					ulObj.removeChild(liObj);
				}
				if (ulObj.length == 0) {
					var liObj = document.createElement('li');
					liObj.id = 'elx5_user_thread0';
					liObj.className = 'elx5_user_nothreads';
					liObj.innerHTML = ulObj.getAttribute('data-lngnomsg');
					ulObj.appendChild(liObj);
				}
			} else {//If I am on specific thread page return to threads page, else reload page (just in case)
				var curl = window.location.href;
				var pos = curl.lastIndexOf('/pms/');
				if (pos > -1) {
					var newurl = curl.substr(0, pos)+'/pms/';
					window.location.href = newurl;
				} else {
					location.reload(true);
				}
			}
		}
	};

	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader('elx5_user_pmsloading');
		alert('Error! '+errorThrown);
	};

	var eurl = document.getElementById('fmsendpm').action+'delete';
	var rnd = Math.floor((Math.random()*100)+1);
	var edata = { 'id': threadid, 'rnd': rnd };
	elx5StartPageLoader('elx5_user_pmsloading');
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}


function elx5UserDeleteBookmark(id) {
	id = parseInt(id, 10);
	if (id < 1) { return; }
	if (!document.getElementById('fmeditbkm')) { return; }

	var successfunc = function(xreply) {
		elx5StopPageLoader('elx5_user_pmsloading');
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
			if (document.getElementById('elx5_user_bookmarks')) {
				var ulObj = document.getElementById('elx5_user_bookmarks');
				if (document.getElementById('elx5_user_bookmark_'+id)) {
					var liObj = document.getElementById('elx5_user_bookmark_'+id);
					ulObj.removeChild(liObj);
				}
			} else {
				location.reload(true);
			}
		}
	};

	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader('elx5_user_pmsloading');
		alert('Error! '+errorThrown);
	};

	var eurl = document.getElementById('fmeditbkm').action+'delete';
	var rnd = Math.floor((Math.random()*100)+1);
	var edata = { 'id': id, 'rnd': rnd };
	elx5StartPageLoader('elx5_user_pmsloading');
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}


function elx5UserEditBookmark(id, newcid) {
	if (!document.getElementById('fmeditbkm')) { return; }
	id = parseInt(id, 10);

	if (id < 1) {
		newcid = parseInt(newcid, 10);
		if (newcid < 1) { newcid = 1; }
		var sObj = document.getElementById('ebmkcid');
		var idx = 0;
		for (i=0; i < sObj.length; i++) {
			if (sObj.options[i].value == newcid) { idx = i; break; }
		}
		sObj.selectedIndex = idx;
		document.getElementById('ebmktitle').value = '';
		document.getElementById('ebmklink').value = '';
		document.getElementById('ebmknote').value = '';
		document.getElementById('ebmkreminderdate').value = '';
		document.getElementById('ebmkid').value = 0;
		if (newcid == 5) {//reminder
			document.getElementById('elx5_user_remdatebox').className = 'elx5_zero';
		} else {
			document.getElementById('elx5_user_remdatebox').className = 'elx5_invisible';
		}
		elx5ModalOpen('bmk');
		return;
	}

	var successfunc = function(xreply) {
		elx5StopPageLoader('elx5_user_pmsloading');
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
			var sObj = document.getElementById('ebmkcid');
			var idx = 0;
			for (i=0; i < sObj.length; i++) {
				if (sObj.options[i].value == jsonObj.cid) { idx = i; break; }
			}
			sObj.selectedIndex = idx;
			document.getElementById('ebmktitle').value = jsonObj.title;
			document.getElementById('ebmklink').value = jsonObj.link;
			document.getElementById('ebmknote').value = jsonObj.note;
			document.getElementById('ebmkreminderdate').value = jsonObj.reminderdate;
			document.getElementById('ebmkid').value = jsonObj.id;
			if (jsonObj.cid == 5) {//reminder
				document.getElementById('elx5_user_remdatebox').className = 'elx5_zero';
			} else {
				document.getElementById('elx5_user_remdatebox').className = 'elx5_invisible';
			}
			elx5ModalOpen('bmk');
			return;
		}
	};

	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader('elx5_user_pmsloading');
		alert('Error! '+errorThrown);
	};

	var eurl = document.getElementById('fmeditbkm').action+'load';
	var rnd = Math.floor((Math.random()*100)+1);
	var edata = { 'id': id, 'rnd': rnd };
	elx5StartPageLoader('elx5_user_pmsloading');
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

function elx5UserSwitchBookmarkCategory() {
	if (!document.getElementById('ebmkcid')) { return; }
	var sObj = document.getElementById('ebmkcid');
	var cid = sObj.options[sObj.selectedIndex].value;
	cid = parseInt(cid, 10);
	if (cid == 5) { //reminder
		document.getElementById('elx5_user_remdatebox').className = 'elx5_zero';
	} else {
		document.getElementById('elx5_user_remdatebox').className = 'elx5_invisible';
	}
}


function elx5UserSaveBookmark() {
	if (comuserwait == 1) { return; }
	elx5ModalMessageHide('bmk');
	var edata = {};
	var sObj = document.getElementById('ebmkcid');
	edata.cid = parseInt(sObj.options[sObj.selectedIndex].value, 10);
	edata.title = document.getElementById('ebmktitle').value;
	edata.link = document.getElementById('ebmklink').value;
	edata.note = document.getElementById('ebmknote').value;
	edata.reminderdate = document.getElementById('ebmkreminderdate').value;
	edata.id = document.getElementById('ebmkid').value;
	edata.token = document.getElementById('ebmktoken').value;
	if (edata.cid == 5) {
		if (edata.reminderdate == '') {
			elx5ModalMessageShow('pms', 'Please set reminder date!', 'elx5_error');
			return;
		}
	} else {
		edata.reminderdate = '';
	}

	var successfunc = function(xreply) {
		comuserwait = 0;
		document.getElementById('ebmksavebmk').innerHTML = document.getElementById('ebmksavebmk').getAttribute('data-savelng');
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			elx5ModalMessageShow('bmk', 'Could not load data!', 'elx5_error');
			return false;
		}
		if (parseInt(jsonObj.success, 10) < 1) {
			if (jsonObj.message != '') {
				var emsg = jsonObj.message;
			} else {
				var emsg = 'Action failed!';
			}
			elx5ModalMessageShow('bmk', emsg, 'elx5_error');
		}
		if (parseInt(jsonObj.success, 10) == 1) {
			elx5ModalClose('bmk');
			location.reload(true);
		}
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		comuserwait = 0;
		document.getElementById('ebmksavebmk').innerHTML = document.getElementById('ebmksavebmk').getAttribute('data-savelng');
		elx5ModalMessageShow('bmk', 'Error! '+errorThrown, 'elx5_error');
	}
	document.getElementById('ebmksavebmk').innerHTML = document.getElementById('ebmksavebmk').getAttribute('data-waitlng');

	edata.rnd = Math.floor((Math.random()*100)+1);
	var eurl = document.getElementById('fmeditbkm').action+'save';
	comuserwait = 1;
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}
