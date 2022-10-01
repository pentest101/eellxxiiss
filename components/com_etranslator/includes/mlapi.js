/*
Elxis Translator
Author: Ioannis Sannos (a.k.a. datahell)
http://www.elxis.org
*/

/* GET MULTILINGUAL FORM INSTANCE */
function getMLIstance(mlinstance) {
	mlinstance = parseInt(mlinstance, 10);
	if (mlinstance == 1) {
		if (typeof mldata1 == "undefined") { return false; }
		var mldata = mldata1;
	} else if (mlinstance == 2) {
		if (typeof mldata2 == "undefined") { return false; }
		var mldata = mldata2;
	} else if (mlinstance == 3) {
		if (typeof mldata3 == "undefined") { return false; }
		var mldata = mldata3;
	} else if (mlinstance == 4) {
		if (typeof mldata4 == "undefined") { return false; }
		var mldata = mldata4;
	} else if (mlinstance == 5) {
		if (typeof mldata5 == "undefined") { return false; }
		var mldata = mldata5;
	} else {
		if (typeof mldata1 == "undefined") { return false; }
		var mldata = mldata1;
	}
	return mldata;
}

/* CHECK IF LANGUAGE IS AN RTL ONE */
function isRightToLeft(rtllangs, lng) {
	for (var i=0; i < rtllangs.length; i++) {
		if (rtllangs[i] == lng) { return true; }
	}
	return false;
}

/* GET MULTILINGUAL ITEM */
function getMLItem(mlitems, elemid) {
	for (var i=0; i < mlitems.length; i++) {
		if (mlitems[i].item == elemid) { return mlitems[i]; }
	}
	return false;
}

/* SWITCH TRANSLATE ELEMENT LANGUAGE */
function translang_switch(mlinstance, elemid) {
	var mldata = getMLIstance(mlinstance);
	if (mldata === false) { return false; }
	var mlitem = getMLItem(mldata.items, elemid);
	if (mlitem === false) { return false; }
	if (typeof mlapibase == "undefined") { return false; }

	var selObj = document.getElementById('transl_'+elemid);
	var cur_lang = selObj.options[selObj.selectedIndex].value;
	var origObj = document.getElementById(elemid);
	var transObj = document.getElementById('trans_'+elemid);
	var wrapObj = document.getElementById('transwrap_'+elemid);
	var msgObj = document.getElementById('transmsg_'+elemid);

	selObj.className = 'elx5_select elx5_mlflag'+cur_lang;
	msgObj.innerHTML = '';
	msgObj.className = 'ml_message';
	if (cur_lang == mldata.lang) {
		origObj.style.display = '';
		wrapObj.className = 'elx5_invisible';
		msgObj.className = 'elx5_invisible';
	} else {
		if (isRightToLeft(mldata.rtllangs, cur_lang) === true) {
			transObj.dir = 'rtl';
		} else {
			transObj.dir = 'ltr';
		}
		transObj.className = 'elx5_text elx5_mlflag'+cur_lang;

		transObj.value = '';
		msgObj.innerHTML = mldata.waitmsg;
		msgObj.className = 'ml_message';
		origObj.style.display = 'none';
		wrapObj.className = 'elx5_elx4_trwrap';

		var edata = { 'category': mlitem.ctg, 'element': mlitem.elem, 'elid': mlitem.elid, 'language': cur_lang };
		var eurl = mlapibase+'load';
		var successfunc = function(xreply) {
			var jsonObj = JSON.parse(xreply);
			if (parseInt(jsonObj.error, 10) > 0) {
				msgObj.className = 'ml_message ml_error';
				document.getElementById('transid_'+elemid).value = 0;
				if (jsonObj.errormsg != '') {
					msgObj.innerHTML = jsonObj.errormsg;
				} else {
					msgObj.innerHTML = 'Action failed!';
				}
				return false;
			} else {
				msgObj.className = 'elx5_invisible';
				msgObj.innerHTML = '';
				document.getElementById('transid_'+elemid).value = jsonObj.trid;
				if (jsonObj.trid > 0) {
					trans_marksaved(transObj);
				} else {
					trans_markunsaved(transObj);
				}
				transObj.value = jsonObj.translation;
			}
		}
		elxAjax('POST', eurl, edata, null, null, successfunc, null);
	}
}


/* SAVE TEXT STRING TRANSLATION */
function translang_save(mlinstance, elemid) {
	var mldata = getMLIstance(mlinstance);
	if (mldata === false) { return false; }
	var mlitem = getMLItem(mldata.items, elemid);
	if (mlitem === false) { return false; }
	if (typeof mlapibase == "undefined") { return false; }

	var selObj = document.getElementById('transl_'+elemid);
	var cur_lang = selObj.options[selObj.selectedIndex].value;
	var transObj = document.getElementById('trans_'+elemid);
	var msgObj = document.getElementById('transmsg_'+elemid);
	var transidObj = document.getElementById('transid_'+elemid);

	var trtext = transObj.value;
	if (trtext == '') {
		msgObj.className = 'ml_message ml_error';
		msgObj.innerHTML = mldata.prtransmsg;
		return false;
	}

	msgObj.className = 'ml_message';
	msgObj.innerHTML = mldata.waitmsg;

	var trid = parseInt(transidObj.value, 10);
	var edata = {'trid': trid, 'category': mlitem.ctg, 'element': mlitem.elem, 'elid': mlitem.elid, 'language': cur_lang, 'translation': trtext };
	var eurl = mlapibase+'save';
	var successfunc = function(xreply) {
		var jsonObj = JSON.parse(xreply);
		if (parseInt(jsonObj.error, 10) > 0) {
			msgObj.className = 'ml_message ml_error';
			document.getElementById('transid_'+elemid).value = 0;
			if (jsonObj.errormsg != '') {
				msgObj.innerHTML = jsonObj.errormsg;
			} else {
				msgObj.innerHTML = 'Action failed!';
			}
			return false;
		} else {
			msgObj.className = 'ml_message ml_success';
			msgObj.innerHTML = jsonObj.successmsg;

			trans_marksaved(transObj);
			if (trid < 1) { transidObj.value = jsonObj.trid; }
		}
	}
	elxAjax('POST', eurl, edata, null, null, successfunc, null);
}

/* DELETE TRANSLATION */
function translang_delete(mlinstance, elemid) {
	var mldata = getMLIstance(mlinstance);
	if (mldata === false) { return false; }
	var mlitem = getMLItem(mldata.items, elemid);
	if (mlitem === false) { return false; }
	if (typeof mlapibase == "undefined") { return false; }

	var selObj = document.getElementById('transl_'+elemid);
	var cur_lang = selObj.options[selObj.selectedIndex].value;
	var transObj = document.getElementById('trans_'+elemid);
	var msgObj = document.getElementById('transmsg_'+elemid);
	var transidObj = document.getElementById('transid_'+elemid);
	var origObj = document.getElementById(elemid);
	var wrapObj = document.getElementById('transwrap_'+elemid);

	var trid = parseInt(transidObj.value, 10);
	if (trid < 1) {
		transObj.value = '';
		wrapObj.className = 'elx5_invisible';
		origObj.style.display = '';
		msgObj.className = 'elx5_invisible';
		translang_switchSelector(selObj, mldata.lang);
		trans_marksaved(transObj);
		return;
	}

	msgObj.className = 'ml_message';
	msgObj.innerHTML = mldata.waitmsg;

	var edata = {'trid': trid, 'category': mlitem.ctg, 'element': mlitem.elem, 'elid': mlitem.elid, 'language': cur_lang };
	var eurl = mlapibase+'delete';
	var successfunc = function(xreply) {
		var jsonObj = JSON.parse(xreply);
		if (parseInt(jsonObj.error, 10) > 0) {
			msgObj.className = 'ml_message ml_error';
			if (jsonObj.errormsg != '') {
				msgObj.innerHTML = jsonObj.errormsg;
			} else {
				msgObj.innerHTML = 'Action failed!';
			}
			return false;
		} else {
			msgObj.className = 'elx5_invisible';
			transidObj.value = 0;
			transObj.value = '';
			wrapObj.className = 'elx5_invisible';
			origObj.style.display = '';
			translang_switchSelector(selObj, mldata.lang);
		}
	}
	elxAjax('POST', eurl, edata, null, null, successfunc, null);
}


/* GET AUTO TRANSLATION FROM MICROSOFT */
function translang_bing(mlinstance, elemid) {	
}


/* SET LANGUAGE SELECTOR */
function translang_switchSelector(selObj, lng) {
	for (var i=0; i < selObj.options.length; i++) {
		if (selObj.options[i].value == lng) {
			if (selObj.selectedIndex != i) {
				selObj.selectedIndex = i;
			}
			break;
		}
	}
	selObj.className = 'elx5_select elx5_mlflag'+lng;
}


/* MARK ELEMENT AS UNSAVED */
function trans_markunsaved(obj) {
	obj.style.borderColor = '#FF0000';
}


/* MARK ELEMENT AS SAVED */
function trans_marksaved(obj) {
	obj.style.borderColor = '#AAAAAA';
}

/* SWITCH TRANSLATE TEXTAREA ELEMENT LANGUAGE */
function translang_edswitch(mlinstance, elemid, iseditor) {
	var mldata = getMLIstance(mlinstance);
	if (mldata === false) { return false; }
	var mlitem = getMLItem(mldata.items, elemid);
	if (mlitem === false) { return false; }
	if (typeof mlapibase == "undefined") { return false; }

	var selObj = document.getElementById('transl_'+elemid);
	var cur_lang = selObj.options[selObj.selectedIndex].value;
	var wrapObj = document.getElementById('transwrap_'+elemid);
	var msgObj = document.getElementById('transmsg_'+elemid);
	var beforeObj = document.getElementById('transbef_'+elemid);

	if (isRightToLeft(mldata.rtllangs, cur_lang) === true) {
		var editordir = 'rtl';
	} else {
		var editordir = 'ltr';
	}

	selObj.className = 'elx5_select elx5_mlflag'+cur_lang;
	msgObj.innerHTML = '';
	msgObj.className = 'ml_message';

	if (cur_lang == mldata.lang) {
		wrapObj.className = 'elx5_invisible';
		msgObj.className= 'elx5_invisible';
		trans_marksaved(selObj);
		if (iseditor == 1) {
			document.getElementById(elemid).dir = editordir;
			var editor_inst = Jodit.instances[elemid];
			editor_inst.options.direction = editordir;
			var editor_data = document.getElementById('transorig_'+elemid).value;
			editor_inst.setEditorValue(editor_data);
			document.getElementById('transorig_'+elemid).value = '';
		} else {
			var editor_inst = document.getElementById(elemid);
			editor_inst.dir = editordir;
			editor_inst.value = document.getElementById('transorig_'+elemid).value;
			document.getElementById('transorig_'+elemid).value = '';
		}
		beforeObj.value = cur_lang;
	} else {
		msgObj.innerHTML = mldata.waitmsg;
		wrapObj.className = 'elx5_elx4_trwrap';
		msgObj.classname = 'ml_message';

		if (iseditor == 1) {
			document.getElementById(elemid).dir = editordir;
			var editor_inst = Jodit.instances[elemid];
			editor_inst.options.direction = editordir;
			if (beforeObj.value == mldata.lang) {
				var editor_data = editor_inst.getEditorValue();
				document.getElementById('transorig_'+elemid).value = editor_data;
			}
			editor_inst.setEditorValue('');
		} else {
			var editor_inst = document.getElementById(elemid);
			editor_inst.dir = editordir;
			if (beforeObj.value == mldata.lang) {
				document.getElementById('transorig_'+elemid).value = editor_inst.value;
			}
			editor_inst.value = '';
		}
		
		beforeObj.value = cur_lang;
		var edata = { 'category': mlitem.ctg, 'element': mlitem.elem, 'elid': mlitem.elid, 'language': cur_lang };
		var eurl = mlapibase+'load';
		var successfunc = function(xreply) {
			var jsonObj = JSON.parse(xreply);
			if (parseInt(jsonObj.error, 10) > 0) {
				msgObj.className = 'ml_message ml_error';
				document.getElementById('transid_'+elemid).value = 0;
				if (jsonObj.errormsg != '') {
					msgObj.innerHTML = jsonObj.errormsg;
				} else {
					msgObj.innerHTML = 'Action failed!';
				}
				return false;
			} else {
				msgObj.className = 'elx5_invisible';
				msgObj.innerHTML = '';
				document.getElementById('transid_'+elemid).value = jsonObj.trid;
				if (jsonObj.trid > 0) {
					trans_marksaved(selObj);
				} else {
					trans_markunsaved(selObj);
				}

				if (iseditor == 1) {
					editor_inst.setEditorValue(jsonObj.translation);
				} else {
					editor_inst.value = jsonObj.translation;
				}
			}
		}
		elxAjax('POST', eurl, edata, null, null, successfunc, null);
	}
}


/* SAVE TEXTAREA TRANSLATION */
function translang_edsave(mlinstance, elemid, iseditor) {
	var mldata = getMLIstance(mlinstance);
	if (mldata === false) { return false; }
	var mlitem = getMLItem(mldata.items, elemid);
	if (mlitem === false) { return false; }
	if (typeof mlapibase == "undefined") { return false; }

	var selObj = document.getElementById('transl_'+elemid);
	var cur_lang = selObj.options[selObj.selectedIndex].value;
	var msgObj = document.getElementById('transmsg_'+elemid);
	var transidObj = document.getElementById('transid_'+elemid);

	if (iseditor == 1) {
		var editor_inst = Jodit.instances[elemid];
		var trtext = editor_inst.getEditorValue();
	} else {
		var editor_inst = document.getElementById(elemid);
		var trtext = editor_inst.value;
	}

	if (trtext == '') {
		msgObj.className = 'ml_message ml_error';
		msgObj.innerHTML = mldata.prtransmsg;
		return false;
	}
	trtext = trtext.replace(/'/g, '&#39;');//defender blocks single quotes

	msgObj.className = 'ml_message';
	msgObj.innerHTML = mldata.waitmsg;

	var trid = parseInt(transidObj.value, 10);
	var edata = {'trid': trid, 'category': mlitem.ctg, 'element': mlitem.elem, 'elid': mlitem.elid, 'language': cur_lang, 'translation': trtext };
	var eurl = mlapibase+'tsave';
	var successfunc = function(xreply) {
		var jsonObj = JSON.parse(xreply);
		if (parseInt(jsonObj.error, 10) > 0) {
			msgObj.className = 'ml_message ml_error';
			document.getElementById('transid_'+elemid).value = 0;
			if (jsonObj.errormsg != '') {
				msgObj.innerHTML = jsonObj.errormsg;
			} else {
				msgObj.innerHTML = 'Action failed!';
			}
			return false;
		} else {
			msgObj.className = 'ml_message ml_success';
			msgObj.innerHTML = jsonObj.successmsg;

			trans_marksaved(selObj);
			if (trid < 1) { transidObj.value = jsonObj.trid; }
		}
	}
	elxAjax('POST', eurl, edata, null, null, successfunc, null);
}


/* DELETE TRANSLATION */
function translang_eddelete(mlinstance, elemid, iseditor) {
	var mldata = getMLIstance(mlinstance);
	if (mldata === false) { return false; }
	var mlitem = getMLItem(mldata.items, elemid);
	if (mlitem === false) { return false; }
	if (typeof mlapibase == "undefined") { return false; }

	var selObj = document.getElementById('transl_'+elemid);
	var cur_lang = selObj.options[selObj.selectedIndex].value;
	var msgObj = document.getElementById('transmsg_'+elemid);
	var transidObj = document.getElementById('transid_'+elemid);
	var wrapObj = document.getElementById('transwrap_'+elemid);

	if (isRightToLeft(mldata.rtllangs, mldata.lang) === true) {
		var editordir = 'rtl';
	} else {
		var editordir = 'ltr';
	}

	var trid = parseInt(transidObj.value, 10);
	if (trid < 1) {
		if (iseditor == 1) {
			document.getElementById(elemid).dir = editordir;
			var editor_inst = Jodit.instances[elemid];
			editor_inst.options.direction = editordir;
			var editor_data = document.getElementById('transorig_'+elemid).value;
			editor_inst.setEditorValue(editor_data);
			document.getElementById('transorig_'+elemid).value = '';
		} else {
			var editor_inst = document.getElementById(elemid);
			editor_inst.dir = editordir;
			editor_inst.value = document.getElementById('transorig_'+elemid).value;
			document.getElementById('transorig_'+elemid).value = '';
		}
		wrapObj.className = 'elx5_invisible';
		msgObj.className = 'elx5_invisible';
		translang_switchSelector(selObj, mldata.lang);
		document.getElementById('transbef_'+elemid).value = mldata.lang;
		trans_marksaved(selObj);
		return;
	}

	msgObj.className = 'ml_message';
	msgObj.innerHTML = mldata.waitmsg;

	var edata = {'trid': trid, 'category': mlitem.ctg, 'element': mlitem.elem, 'elid': mlitem.elid, 'language': cur_lang };
	var eurl = mlapibase+'delete';
	var successfunc = function(xreply) {
		var jsonObj = JSON.parse(xreply);
		if (parseInt(jsonObj.error, 10) > 0) {
			msgObj.className = 'ml_message ml_error';
			if (jsonObj.errormsg != '') {
				msgObj.innerHTML = jsonObj.errormsg;
			} else {
				msgObj.innerHTML = 'Action failed!';
			}
			return false;
		} else {
			msgObj.className = 'elx5_invisible';
			transidObj.value = 0;
			wrapObj.className = 'elx5_invisible';
			if (iseditor == 1) {
				document.getElementById(elemid).dir = editordir;
				var editor_inst = Jodit.instances[elemid];
				editor_inst.options.direction = editordir;
				var editor_data = document.getElementById('transorig_'+elemid).value;
				editor_inst.setEditorValue(editor_data);
				document.getElementById('transorig_'+elemid).value = '';
			} else {
				var editor_inst = document.getElementById(elemid);
				editor_inst.dir = editordir;
				editor_inst.value = document.getElementById('transorig_'+elemid).value;
				document.getElementById('transorig_'+elemid).value = '';
			}
			translang_switchSelector(selObj, mldata.lang);
			document.getElementById('transbef_'+elemid).value = mldata.lang;
			trans_marksaved(selObj);
		}
	}
	elxAjax('POST', eurl, edata, null, null, successfunc, null);
}

/* MARK TEXTAREA UNSAVED */
function trans_marktareaunsaved(elemid) {
	var selObj = document.getElementById('transl_'+elemid);
	trans_markunsaved(selObj);
}
