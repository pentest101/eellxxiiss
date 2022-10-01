/* LINK TO A FOLDER */
function egal5toFolder() {
	var selObj = document.getElementById('egalleryctg');
 	var fpath = selObj.options[selObj.selectedIndex].value;
	if (fpath != '') {
		var relp = selObj.getAttribute('data-relpath');
		var code = '{gallery}'+relp+fpath+'{/gallery}';
		addPluginCode(code);
	} else {
		return;
	}
}

/* LIST FOLDER IMAGES */
function egal5FolderImages(plugid, fn) {
	var selObj = document.getElementById('egalleryctg');
	var fpath = selObj.options[selObj.selectedIndex].value;
	if (fpath == '') {
		document.getElementById('egalimages').innerHTML = '';
		return;
	}
	var edata = { 'id':plugid, 'fn':fn, 'fpath':fpath, 'task':'handler', 'act':'list' };
	var eurl = document.getElementById('plugbase').innerHTML;
	var successfunc = function(xreply) {
		elx5StopPageLoader();
		document.getElementById('egalimages').innerHTML = xreply;
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader();
		alert('Action failed! '+errorThrown);
	};
	elx5StartPageLoader();
	elxAjax('POST', eurl, edata, 'egalimages', null, successfunc, errorfunc);
}

/* SUBMIT UPLOAD GALLERY FORM */
function plugGallerySubmit() {
	var selObj = document.getElementById('galfolder');
 	var folder = selObj.options[selObj.selectedIndex].value;
	var newfolder = document.getElementById('galnewfolder').value;
	if (folder == '') {
		if (newfolder == '') { alert('Select a folder to upload images!'); return false; }
	}
	if (newfolder != '') {
		var reg = /^([a-zA-Z0-9_-]+)$/;
		if (!reg.test(newfolder)) { alert('Invalid folder name!'); return false; }
	}
	document.pluggalform.submit();
	return true;
}

/* ADD IMAGE CAPTION */
function plugGalleryAddCaption(elid, act, pluginid, fn) {
	var initvalue = '';
	var prompttxt = 'Caption';
	if (act == 'edit') { initvalue = document.getElementById(elid).innerHTML; }
	if (document.getElementById('plugal_lng_cap')) { prompttxt = document.getElementById('plugal_lng_cap').innerHTML; }

	var successfunc = function(xreply) {
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			return false;
		}
		if (parseInt(jsonObj.success, 10) == 1) {
			var newonlick = 'plugGalleryAddCaption(\''+elid+'\', \'edit\', '+pluginid+', '+fn+');';
			document.getElementById(elid).setAttribute('onclick', newonlick);
			document.getElementById(elid).className = 'plug_gallery_editcap';
			document.getElementById(elid).innerHTML = caption;
		}
	}

	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {}

	var caption = prompt(prompttxt, initvalue);
	if (caption != null) {
		var rnd = Math.floor((Math.random()*100)+1);
		var eurl = document.getElementById('plugal_url').innerHTML;
		var image = document.getElementById(elid).getAttribute('data-img');
		var fpath = document.getElementById(elid).getAttribute('data-path');
		var edata = { 'task': 'handler', 'act':'setcaption', 'id':pluginid, 'fn': fn, 'caption': caption, 'image':image, 'fpath':fpath, 'rnd':rnd };
		elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
	}
}

/* DELETE GALERY IMAGE */
function plugGalleryDeleteImage(grnd, gi, pluginid, fn) {
	var confirmtxt = document.getElementById('plugal_lng_sure').innerHTML;

	var successfunc = function(xreply) {
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			return false;
		}
		if (parseInt(jsonObj.success, 10) == 1) {
			var objTbl = document.getElementById('plug_gallery'+grnd);
			var rowid = 'plugal'+grnd+'_row'+gi;
			if (objTbl.rows.length > 0) {
				for (var i = 0; i < objTbl.rows.length; i++) {
					if (objTbl.rows[i].id == rowid) {
						objTbl.deleteRow(i);
						break;
					}
				}
			}
		}
	}

	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {}

	if (confirm(confirmtxt)) {
		var rnd = Math.floor((Math.random()*100)+1);
		var eurl = document.getElementById('plugal_url').innerHTML;
		var elid = 'plugal'+grnd+'_dimg'+gi;
		var image = document.getElementById(elid).getAttribute('data-img');
		var fpath = document.getElementById(elid).getAttribute('data-path');
		var edata = { 'task': 'handler', 'act':'delimage', 'id':pluginid, 'fn': fn, 'image':image, 'fpath':fpath, 'rnd':rnd };
		elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
	}
}
