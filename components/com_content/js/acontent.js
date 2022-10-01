/* Component Content javascript */
/* Elxis CMS - http://www.elxis.org */
/* Ioannis Sannos (datahell) */
 
 
function con5MoveCategory(moveup) {
	if (typeof moveup === 'undefined') { moveup = 1; }
	moveup = parseInt(moveup, 10);
	var catid = elx5SelectedTableItem('categoriestbl', false);
	if (catid === false) { return; }
	catid = parseInt(catid, 10);
	if (catid < 1) { return; }

	var successfunc = function(xreply) {
		elx5StopPageLoader();
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
		} else {
			location.reload(true);
		}
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader();
		alert('Error! '+errorThrown);
	}

	var rnd = Math.floor((Math.random()*100)+1);
	var edata = { 'catid':catid, 'moveup':moveup, 'rnd':rnd };
	var eurl = document.getElementById('categoriestbl').getAttribute('data-inpage')+'move';

	elx5StartPageLoader();
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

function con5CategoryTrans(tbl) {
	var elid = elx5SelectedTableItem(tbl, false);
	if (elid === false) { return; }
	elid = parseInt(elid, 10);
	if (elid < 1) { return; }
	var eurl = document.getElementById('con5categorytranslations').innerHTML+'&id='+elid;
	eurl = eurl.replace(/&amp;/g, '&');
	elxPopup(eurl, 650, 400, 'ctgtranspreview', 'yes');
}

function con5ArticleTrans(tbl) {
	var elid = elx5SelectedTableItem(tbl, false);
	if (elid === false) { return; }
	elid = parseInt(elid, 10);
	if (elid < 1) { return; }
	var eurl = document.getElementById('con5articletranslations').innerHTML+'&id='+elid;
	eurl = eurl.replace(/&amp;/g, '&');
	elxPopup(eurl, 650, 400, 'arttranspreview', 'yes');
}

function con5Filter(catid, rest_options) {
	var eurl = document.getElementById('articlestbl').getAttribute('data-listpage')+'?catid='+catid;
	if (rest_options != '') { eurl += '&'+rest_options; }
	window.location.href = eurl;
}


function con5UnFilter(rest_options) {
	var eurl = document.getElementById('articlestbl').getAttribute('data-listpage');
	if (rest_options != '') { eurl += '?'+rest_options; }
	window.location.href = eurl;
}


function con5CopyMoveArticles(itemaction) {
	if (typeof itemaction === 'undefined') { return; }
	if (itemaction != 'copy') { itemaction = 'move'; }
	var ids = elx5SelectedTableItem('articlestbl', true);
	if (ids === false) { return; }
	if (ids.length == 0) { return; }
	document.getElementById('cpmvartids').value = ids.join(',');
	document.getElementById('cpmvtask').value = itemaction;
	document.getElementById('cpmvartcategory').selectedIndex = 0;
	if (itemaction == 'copy') {
		document.getElementById('elx5_modaltitleeartcp').innerHTML = document.getElementById('fmcpmvarticle').getAttribute('data-lngcopy');
		document.getElementById('eartcpmvsave').innerHTML = document.getElementById('fmcpmvarticle').getAttribute('data-lngcopy');
	} else {
		document.getElementById('elx5_modaltitleeartcp').innerHTML = document.getElementById('fmcpmvarticle').getAttribute('data-lngmove');
		document.getElementById('eartcpmvsave').innerHTML = document.getElementById('fmcpmvarticle').getAttribute('data-lngmove');
	}
	document.getElementById('elx5_modalmessageeartcp').className = 'elx5_invisible';
	elx5ModalOpen('eartcp');
}


function con5CopyMoveArtSave() {
	var edata = {};
	edata.ids = document.getElementById('cpmvartids').value;
	if (edata.ids == '') { return; }
	var sObj = document.getElementById('cpmvartcategory');
	edata.catid = sObj.options[sObj.selectedIndex].value;
	edata.catid = parseInt(edata.catid, 10);
	if (edata.catid < 0) { return; }

	edata.rnd = Math.floor((Math.random()*100)+1);
	var eurl = document.getElementById('fmcpmvarticle').action+''+document.getElementById('cpmvtask').value;

	document.getElementById('elx5_modalmessageeartcp').className = 'elx5_invisible';

	var successfunc = function(xreply) {
		elx5StopPageLoader();
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			document.getElementById('elx5_modalmessageeartcp').innerHTML = 'Could not save articles!';
			document.getElementById('elx5_modalmessageeartcp').className = 'elx5_error';
			return false;
		}
		if (parseInt(jsonObj.success, 10) < 1) {
			if (jsonObj.message != '') {
				document.getElementById('elx5_modalmessageeartcp').innerHTML = jsonObj.message;
			} else {
				document.getElementById('elx5_modalmessageeartcp').innerHTML = 'Action failed!';
			}
			document.getElementById('elx5_modalmessageeartcp').className = 'elx5_error';
		} else {
			elx5ModalClose('eartcp');
			location.reload(true);
		}
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader();
		document.getElementById('elx5_modalmessageeartcp').innerHTML = 'Error! '+errorThrown; 
		document.getElementById('elx5_modalmessageeartcp').className = 'elx5_error';
	}

	elx5StartPageLoader();
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}


function con5ArticleShare(socialmedia) {
	var elid = elx5SelectedTableItem('articlestbl', false);
	if (elid === false) { return; }
	elid = parseInt(elid, 10);
	if (elid < 1) { return; }
	var sharelink = document.getElementById('articlestbl').getAttribute('data-inpage')+'share.html?type='+socialmedia+'&id='+elid;
	elxPopup(sharelink, 700, 450, 'share', 'yes');
}


function con5CronJobs() {
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
		} else {//error
			if (jsonObj.errormsg != '') {
				alert(jsonObj.errormsg);
			} else {
				alert('Action failed!');
			}
		}
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader();
		alert('Error! '+errorThrown); 
	}

	var edata = {};
	edata.rnd = Math.floor((Math.random()*100)+1);
	var eurl = document.getElementById('con5articlecron').innerHTML;
	elx5StartPageLoader();
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}


/* PUBLISH COMMENT (AJAX) */
function con5PublishComment(cid) {
	if (isNaN(cid)) { return false; }
	cid = parseInt(cid, 10);
	if (cid < 1) { return false; }

	var successfunc = function(xreply) {
		elx5StopPageLoader();
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			alert(e.message);
			return false;
		}
		if (parseInt(jsonObj.success, 10) == 1) {
			document.getElementById('con5pubcombox'+cid).className = 'elx5_invisible';
		} else {//error
			if (jsonObj.errormsg != '') {
				alert(jsonObj.errormsg);
			} else {
				alert('Action failed!');
			}
		}
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader();
		alert('Error! '+errorThrown); 
	}

	let rnd = Math.floor((Math.random()*100)+1);
	var edata = {'id': cid, 'rnd': rnd };
	var eurl = document.getElementById('commentstbl').getAttribute('data-inpage')+'publishcomment';
	elx5StartPageLoader();
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}


/* DELETE COMMENT (AJAX) */
function con5DeleteComment(cid) {
	if (isNaN(cid)) { return false; }
	cid = parseInt(cid, 10);
	if (cid < 1) { return false; }
	var tblObj = document.getElementById('commentstbl');
	var prompttxt = tblObj.getAttribute('data-deletelng');
	if (confirm(prompttxt)) {
		let rnd = Math.floor((Math.random()*100)+1);
		var edata = {'id': cid, 'rnd': rnd };
		var eurl = tblObj.getAttribute('data-inpage')+'deletecomment';
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
				if (document.getElementById('datarow'+cid)) {
					var rowObj = document.getElementById('datarow'+cid);
					tblObj.deleteRow(rowObj.rowIndex);
				}
			}
		};
		var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
			elx5StopPageLoader();
			alert('Action failed! '+errorThrown);
		};
		elx5StartPageLoader();
		elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
	}
}
