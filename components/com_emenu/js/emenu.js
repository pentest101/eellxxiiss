/* COMPONENT eMenu JS by Ioannis Sannos (datahell) */

function emenuSaveCollection() {
	if (document.getElementById('ecolcollection').value == '') {
		alert('Please type collection name!');
		document.getElementById('ecolcollection').focus();
		return;
	}
	if (document.getElementById('ecolmodtitle').value == '') {
		alert('Please type module title!');
		document.getElementById('ecolmodtitle').focus();
		return;
	}

	document.getElementById('elx5_modalmessageecol').className = 'elx5_invisible';

	let collection = document.getElementById('ecolcollection').value;
	let modtitle = document.getElementById('ecolmodtitle').value;
	let rnd = Math.floor((Math.random()*100)+1);
	let edata = { 'collection':collection, 'modtitle':modtitle, 'rnd':rnd };
	var eurl = document.getElementById('fmaddcollection').action;

	var successfunc = function(xreply) {
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			document.getElementById('elx5_modalmessageecol').innerHTML = 'Could not save collection!';
			document.getElementById('elx5_modalmessageecol').className = 'elx5_error';
			return false;
		}
		if (parseInt(jsonObj.success, 10) < 1) {
			if (jsonObj.message != '') {
				document.getElementById('elx5_modalmessageecol').innerHTML = jsonObj.message;
			} else {
				document.getElementById('elx5_modalmessageecol').innerHTML = 'Action failed!';
			}
			document.getElementById('elx5_modalmessageecol').className = 'elx5_error';
		}
		if (parseInt(jsonObj.success, 10) == 1) {
			elx5ModalClose('ecol');
			location.reload(true);
		}
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		document.getElementById('elx5_modalmessageecol').innerHTML = (errorThrown == '') ? 'Error!' : errorThrown;
		document.getElementById('elx5_modalmessageecol').className = 'elx5_error';
	}

	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}


function emenuCopyMoveItem(itemaction) {
	if (typeof itemaction === 'undefined') { return; }
	if (itemaction != 'copy') { itemaction = 'move'; }

	var menu_id = elx5SelectedTableItem('menuitemstbl', false);
	if (menu_id === false) { return; }
	menu_id = parseInt(menu_id, 10);
	if (menu_id < 1) { return; }

	if (itemaction == 'copy') {
		var sfx = 'emencp';
		var scObj = document.getElementById('emencpcollection');
		var collection = scObj.options[scObj.selectedIndex].value;
	} else {
		var sfx = 'emenmo';
		var scObj = document.getElementById('emenmocollection');
		var collection = scObj.options[scObj.selectedIndex].value;
	}

	var rnd = Math.floor((Math.random()*100)+1);
	var edata = { 'menu_id':menu_id, 'collection':collection, 'rnd':rnd };
	if (itemaction == 'copy') {
		var eurl = document.getElementById('fmcopymenu').action;
	} else {
		var eurl = document.getElementById('fmmovemenu').action;
	}

	document.getElementById('elx5_modalmessage'+sfx).className = 'elx5_invisible';

	var successfunc = function(xreply) {
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			document.getElementById('elx5_modalmessage'+sfx).innerHTML = 'Could not save area!';
			document.getElementById('elx5_modalmessage'+sfx).className = 'elx5_error';
			return false;
		}
		if (parseInt(jsonObj.success, 10) < 1) {
			if (jsonObj.message != '') {
				document.getElementById('elx5_modalmessage'+sfx).innerHTML = jsonObj.message;
			} else {
				document.getElementById('elx5_modalmessage'+sfx).innerHTML = 'Action failed!';
			}
			document.getElementById('elx5_modalmessage'+sfx).className = 'elx5_error';
		}
		if (parseInt(jsonObj.success, 10) == 1) {
			elx5ModalClose(sfx);
			if (itemaction == 'move') {
				location.reload(true);
			}
		}
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		document.getElementById('elx5_modalmessage'+sfx).innerHTML = 'Error! '+errorThrown; 
		document.getElementById('elx5_modalmessage'+sfx).className = 'elx5_error';
	}

	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}


function emenuMoveItem(moveup) {
	if (typeof moveup === 'undefined') { moveup = 1; }
	moveup = parseInt(moveup, 10);
	var menu_id = elx5SelectedTableItem('menuitemstbl', false);
	if (menu_id === false) { return; }
	menu_id = parseInt(menu_id, 10);
	if (menu_id < 1) { return; }

	var rnd = Math.floor((Math.random()*100)+1);
	var edata = { 'menu_id':menu_id, 'moveup':moveup, 'rnd':rnd };
	var eurl = document.getElementById('menuitemstbl').getAttribute('data-inpage')+'mitems/moveitem';

	var successfunc = function(xreply) {
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			alert('Action failed!');
			return;
		}
		if (parseInt(jsonObj.success, 10) < 1) {
			if (jsonObj.message != '') {
				alert(jsonObj.message);
			} else {
				alert('Action failed!');
			}
		}
		if (parseInt(jsonObj.success, 10) == 1) {
			location.reload(true);
		}
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		alert('Error! '+errorThrown);
	}

	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

/* SELECT COMPONENT */
function emenuPickComponent(cmp) {
	var sObj = document.getElementById('emenpickcomponent');	
	if (typeof cmp == 'undefined') {
		var component = sObj.options[sObj.selectedIndex].value;
	} else {
		component = cmp;
	}
	if (component == '') { return; }
	var rnd = Math.floor((Math.random()*100)+1);
	var edata = { 'component': component, 'rnd':rnd };
	var loadtext = sObj.getAttribute('data-loadlng');
	var eurl = sObj.getAttribute('data-datalink');
	elxAjax('POST', eurl, edata, 'emenu_generator', loadtext, null, null);
}

/* SET MENU ITEM INFO */
function emenuSetlink(xtitle, xlink, secure, alevel) {
	secure = parseInt(secure);
	document.getElementById('eprtitle').value = xtitle;
	document.getElementById('eprlink').value = xlink;
	if (secure == 1) {
		document.getElementById('eprsecure').checked = true;
	} else {
		document.getElementById('eprsecure').checked = false;
	}
	if (typeof alevel != 'undefined') {
		alevel = parseInt(alevel, 10);
		var selObj = document.getElementById('epralevel');
		for (var i=0; i < selObj.options.length; i++) {
			if (selObj.options[i].value == alevel) {
				selObj.selectedIndex = i;
			}
		}
	}
}

/* SET MENU ITEM INFO FROM POPUP WINDOW */
function emenuSetLinkPop(xtitle, xlink, secure, alevel) {
	secure = parseInt(secure);
	window.opener.document.getElementById('eprtitle').value = xtitle;
	window.opener.document.getElementById('eprlink').value = xlink;
	if (secure == 1) {
		window.opener.document.getElementById('eprsecure').checked = true;
	} else {
		window.opener.document.getElementById('eprsecure').checked = false;
	}
	if (typeof alevel != 'undefined') {
		alevel = parseInt(alevel, 10);
		var selObj = window.opener.document.getElementById('epralevel');
		for (var i=0; i < selObj.options.length; i++) {
			if (selObj.options[i].value == alevel) {
				selObj.selectedIndex = i;
			}
		}
	}
	window.close();
}

function emenuMenuItemTrans(tbl) {
	var tblObj = document.getElementById(tbl);
	var checkboxes = tblObj.querySelectorAll('td input.elx5_datacheck');
	if (!checkboxes) { return false; }
	var elid = 0;
	for (var cx=0; cx < checkboxes.length; cx++) {
		if (checkboxes[cx].checked) { elid = checkboxes[cx].value; break; }
	}
	elid = parseInt(elid, 10);
	if (elid < 1) { return; }
	var eurl = document.getElementById('emenutranslations').innerHTML+'&id='+elid;
	eurl = eurl.replace(/&amp;/g, '&');
	elxPopup(eurl, 650, 400, 'menutranspreview', 'yes');
}