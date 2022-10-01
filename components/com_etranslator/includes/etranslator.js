/*
Elxis Translator
Author: Ioannis Sannos (a.k.a. datahell)
http://www.elxis.org
*/

function etransEditTranslation(is_add, linktrid) {
	var directlink = false;
	if (typeof linktrid !== 'undefined') {
		if (!isNaN(linktrid)) {
			linktrid = parseInt(linktrid, 10);
			if (linktrid > 0) { is_add = 0; var trid = linktrid; directlink = true; }
		}
	}
	if (!directlink) {
		var trid = elx5SelectedTableItem('translationstbl', false);
	}
	if (trid === false) { alert('You must select an item!'); return; }
	trid = parseInt(trid, 10);
	if (trid < 1) { alert('You must select an item!'); return; }

	if (is_add) {
		document.getElementById('elx5_modaltitle1').innerHTML = document.getElementById('elx5_modal1').getAttribute('data-addlng');
	} else {
		document.getElementById('elx5_modaltitle1').innerHTML = document.getElementById('elx5_modal1').getAttribute('data-editlng');
	}

	document.getElementById('elx5_modalmessage1').className = 'elx5_vpad';
	document.getElementById('elx5_modalmessage1').innerHTML = document.getElementById('elx5_modalbody1').getAttribute('data-waitlng');
	document.getElementById('elx5_modalcontents1').className = 'elx5_invisible';
	elx5ModalOpen('1');

	var successfunc = function(xreply) {
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			elx5ModalMessageShow('1', 'Could not load data!', 'elx5_error');
			return false;
		}
		if (parseInt(jsonObj.success, 10) < 1) {
			if (jsonObj.message != '') {
				var emsg = jsonObj.message;
			} else {
				var emsg = 'Action failed!';
			}
			elx5ModalMessageShow('1', emsg, 'elx5_error');
		}
		if (parseInt(jsonObj.success, 10) == 1) {
			if (parseInt(jsonObj.longtext, 10) == 1) {
				document.getElementById('etrans_transwrap').className = 'elx5_invisible';
				document.getElementById('etrans_texttranswrap').className = 'elx5_zero';
				var transObj = document.getElementById('etrans_translationtext');
				transObj.required = 'true';
				document.getElementById('etrans_translation').required = false;
			} else {
				document.getElementById('etrans_transwrap').className = 'elx5_zero';
				document.getElementById('etrans_texttranswrap').className = 'elx5_invisible';
				var transObj = document.getElementById('etrans_translation');
				transObj.required = true;
				document.getElementById('etrans_translationtext').required = false;
			}

			var lObj = document.getElementById('etrans_language');
			if (is_add == 0) {
				var idx = 0;
				for (var k=0; k < lObj.options.length; k++) {
					lObj.options[k].disabled = false;
					if (lObj.options[k].value == jsonObj.translang) {
						lObj.selectedIndex = k;
						lObj.className = 'elx5_select elx5_mlflag'+lObj.options[k].value;
						break;
					}
				}
				lObj.disabled = true;
			} else {
				lObj.disabled = false;
				if (jsonObj.curtranslangs != '') { 
					var clngs = jsonObj.curtranslangs.split(',');
					for (var k=0; k < lObj.options.length; k++) {
						var dis = false;
						for (var t=0; t < clngs.length; t++) {
							if (lObj.options[k].value == clngs[t]) {
								dis = true;
								break;
							}
						}
						lObj.options[k].disabled = dis;
					}
				}
			}

			document.getElementById('etrans_trid').value = jsonObj.trid;
			document.getElementById('etrans_elid').value = jsonObj.elid;
			document.getElementById('etrans_category').value = jsonObj.category;
			document.getElementById('etrans_element').value = jsonObj.element;
			document.getElementById('etrans_original').innerHTML = jsonObj.originaltext;
			transObj.value = jsonObj.translation;
			document.getElementById('etrans_longtext').value = jsonObj.longtext;

			elx5ModalMessageHide('1');
			document.getElementById('elx5_modalcontents1').className = 'elx5_zero';
		}
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5ModalMessageShow('1', 'Error! '+errorThrown, 'elx5_error');
	}

	var rnd = Math.floor((Math.random()*100)+1);
	var edata = { 'trid':trid, 'new': is_add, 'rnd':rnd };
	var eurl = document.getElementById('fmedtrans').action;
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}


/* SAVE TRANSLATION */
function etransSaveTranslation() {
	var edata = {};
	edata.longtext = parseInt(document.getElementById('etrans_longtext').value, 10);
	edata.trid = parseInt(document.getElementById('etrans_trid').value, 10);
	edata.elid = parseInt(document.getElementById('etrans_elid').value, 10);
	edata.category = document.getElementById('etrans_category').value;
	edata.element = document.getElementById('etrans_element').value;
	if (edata.longtext == 1) {
		edata.translation = document.getElementById('etrans_translationtext').value;
	} else {
		edata.translation = document.getElementById('etrans_translation').value;
	}

	edata.language = '';
	var lObj = document.getElementById('etrans_language');
	for (var k=0; k < lObj.options.length; k++) {
		if (lObj.options[k].selected) {
			edata.language = lObj.options[k].value;
			break;
		}
	}

	if (edata.language == '') {
		lObj.focus();
		return;
	}
	if (edata.translation == '') {
		alert('Please provide a translation!');
		return;
	}

	var successfunc = function(xreply) {
		document.getElementById('etrans_save').innerHTML = document.getElementById('etrans_save').getAttribute('data-savelng');
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			elx5ModalMessageShow('1', 'Could not load data!', 'elx5_error');
			return false;
		}
		if (parseInt(jsonObj.success, 10) < 1) {
			if (jsonObj.message != '') {
				var emsg = jsonObj.message;
			} else {
				var emsg = 'Action failed!';
			}
			elx5ModalMessageShow('1', emsg, 'elx5_error');
		}
		if (parseInt(jsonObj.success, 10) == 1) {
			elx5ModalClose('1');
			location.reload(true);
		}
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		document.getElementById('etrans_save').innerHTML = document.getElementById('etrans_save').getAttribute('data-savelng');
		elx5ModalMessageShow('1', 'Error! '+errorThrown, 'elx5_error');
	}

	document.getElementById('etrans_save').innerHTML = document.getElementById('etrans_save').getAttribute('data-waitlng');

	edata.rnd = Math.floor((Math.random()*100)+1);
	var eurl = document.getElementById('etrans_saveurl').value;
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}


/* SWITCH TEXT DIRECTION */
function etransSwitchDir() {
	var longtext = parseInt(document.getElementById('etrans_longtext').value, 10);
	if (longtext == 1) {
		var trObj = document.getElementById('etrans_translationtext');
	} else {
		var trObj = document.getElementById('etrans_translation');
	}
	if (trObj.dir == 'ltr') { trObj.dir = 'rtl'; } else { trObj.dir = 'ltr'; }
}
