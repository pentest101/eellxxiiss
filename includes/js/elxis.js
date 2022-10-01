/** 
Elxis CMS generic javascript
Created by Ioannis Sannos / Elxis Team
https://www.elxis.org
*/

/* CHECK IF VALUE IS IN ARRAY */
function elxInArray(val, arr) {
	if (arr instanceof Array) {
		for (var i in arr) { if (val == arr[i]) { return true; } }
	}
	return false;
}

/* SHOW ELEMENT OBJECT */
function elxShow(obj) {
	if (!obj) { return; }
	var tag = obj.tagName;
	tag = tag.toLowerCase();
	var blockElements = new Array('address', 'blockquote', 'div', 'dl', 'fieldset', 'form', 
	'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'hr', 'noscript', 'ol', 'p', 'pre', 'table', 'ul');
	if (tag == 'table') {
		var ieversion = elxIEVersion();
		if (ieversion > 0) {
			if (ieversion >= 8) { obj.style.display = 'table'; } else { obj.style.display = 'block'; }
		} else {
			obj.style.display = 'table';
		}
		obj.style.visibility = 'visible';
	} else if (elxInArray(tag, blockElements) == true) {
		obj.style.display = 'block';
		obj.style.visibility = 'visible';
	} else {
		obj.style.display = 'inline';
		obj.style.visibility = 'visible';
	}
}

/* HIDE ELEMENT OBJECT */
function elxHide(obj) {
	if (!obj) { return; }
	obj.style.display = 'none';
	obj.style.visibility = 'hidden';
}

/* GET INTERNET EXPLORER VERSION */
function elxIEVersion() {
	if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)) {
		var ieversion=new Number(RegExp.$1);
		if (ieversion >= 5) { var version = parseFloat(ieversion); version.toFixed(2); return version; }
	}
	return 0;
}

/* RESIZE IFRAME TO FIT CONTENT'S WIDTH (SAME DOMAIN PAGES ONLY) */
function elxResizeIframe(frameid) {
    var myIFrame = document.getElementById(frameid);
    if (!myIFrame) { return false; }
    var h = 0;
	if (myIFrame.contentDocument && myIFrame.contentDocument.body.offsetHeight) {
		h = myIFrame.contentDocument.body.offsetHeight; 
	} else if (myIFrame.Document && myIFrame.Document.body.scrollHeight) {
		h = myIFrame.Document.body.scrollHeight;
	} else if (myIFrame.contentWindow.document && myIFrame.contentWindow.document.documentElement) {
		h = myIFrame.contentWindow.document.documentElement.offsetHeight;
	} else if (myIFrame.contentWindow.document && myIFrame.contentWindow.document.body) {
		h = myIFrame.contentWindow.document.body.offsetHeight;
	} else if (myIFrame.contentDocument.document && myIFrame.contentDocument.document.body) {
		h = myIFrame.contentDocument.document.body.offsetHeight;
	} else if (myIFrame.contentDocument.document && myIFrame.contentDocument.documentElement.body) {
		h = myIFrame.contentDocument.document.documentElement.offsetHeight;
	}
    h = parseInt(h);
    if (h > 0) {
    	h = parseInt(h * 1.065);
    	var getFFVersion = navigator.userAgent.substring(navigator.userAgent.indexOf("Firefox")).split("/")[1];
    	if (parseFloat(getFFVersion) >= 0.1) { h += 30; }
		myIFrame.style.height = h+'px';
		if (myIFrame.addEventListener) {
			myIFrame.addEventListener("load", elxReResizeIframe, false);
		} else if (myIFrame.attachEvent) {
			myIFrame.detachEvent("onload", elxReResizeIframe);
			myIFrame.attachEvent("onload", elxReResizeIframe);
		}
	}
}

/* RE-RESIZE IFRAME */
function elxReResizeIframe(loadevt) {
	var crossevt=(window.event)? event : loadevt;
	var iframeroot = (crossevt.currentTarget) ? crossevt.currentTarget : crossevt.srcElement;
	if (iframeroot) {
		elxResizeIframe(iframeroot.id);
	}
}

/* VALIDATE EMAIL ADDRESS */
function elxValidateEmail(str, allowempty) {
	if (str == '') { if (allowempty == true) { return true; } else { return false; } }
	var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z0-9-]{2,10}$/;
	if (str.search(emailPattern) == -1) { return false; }
    return true;
}

/* VALIDATE EMAIL INPUT FIELD */
function elxValidateEmailBox(fel, allowempty) {
	if (!document.getElementById(fel)) { return true; }
	var fstr = document.getElementById(fel).value;
	return elxValidateEmail(fstr, allowempty);
}

/* VALIDATE DATE */
function elxValidateDate(str, format, allowempty) {
	if (str == '') { if (allowempty == true) { return true; } else { return false; } }
	var m = 0; var d = 0; var y = 0; var h = 0; var i = 0; var s = 0; var daytime = false;
	if (format == 'Y-m-d') {
		if (str.search(/^\d{4}[\-]\d{1,2}[\-]\d{1,2}/g) != 0) { return false; }
		var parts = str.split('-'); if (parts.length != 3) { return false; }
		d = parseInt(parts[2], 10); m = parseInt(parts[1], 10); y = parseInt(parts[0], 10);
	} else if (format == 'Y/m/d') {
		if (str.search(/^\d{4}[\/]\d{1,2}[\/]\d{1,2}/g) != 0) { return false; }
		var parts = str.split('/'); if (parts.length != 3) { return false; }
		d = parseInt(parts[2], 10); m = parseInt(parts[1], 10); y = parseInt(parts[0], 10);
	} else if (format == 'd-m-Y') {
		if (str.search(/^\d{1,2}[\-]\d{1,2}[\-]\d{4}/g) != 0) { return false; }
		var parts = str.split('-'); if (parts.length != 3) { return false; }
		d = parseInt(parts[0], 10); m = parseInt(parts[1], 10); y = parseInt(parts[2], 10);
	} else if (format == 'd/m/Y') {
		if (str.search(/^\d{1,2}[\/]\d{1,2}[\/]\d{4}/g) != 0) { return false; }
		var parts = str.split('/'); if (parts.length != 3) { return false; }
		d = parseInt(parts[0], 10); m = parseInt(parts[1], 10); y = parseInt(parts[2], 10);
	} else if (format == 'm/d/Y') {
		if (str.search(/^\d{1,2}[\/]\d{1,2}[\/]\d{4}/g) != 0) { return false; }
		var parts = str.split('/'); if (parts.length != 3) { return false; }
		d = parseInt(parts[1], 10); m = parseInt(parts[0], 10); y = parseInt(parts[2], 10);
	} else if (format == 'm-d-Y') {
		if (str.search(/^\d{1,2}[\-]\d{1,2}[\-]\d{4}/g) != 0) { return false; }
		var parts = str.split('-'); if (parts.length != 3) { return false; }
		d = parseInt(parts[1], 10); m = parseInt(parts[0], 10); y = parseInt(parts[2], 10);
	} else if (format == 'Y-m-d H:i:s') {
		if (str.search(/^\d{4}[\-]\d{1,2}[\-]\d{1,2}[\s]\d{1,2}[\:]\d{1,2}[\:]\d{1,2}/g) != 0) { return false; }
		var parts = str.split('-'); if (parts.length != 3) { return false; }
		y = parseInt(parts[0], 10);	m = parseInt(parts[1], 10);
		var out = extractTime(parts[2]); if (out === false) { return false; }
		d = out[0]; h = out[1]; i = out[2]; s = out[3]; daytime = true;
	} else if (format == 'Y/m/d H:i:s') {
		if (str.search(/^\d{4}[\/]\d{1,2}[\/]\d{1,2}[\s]\d{1,2}[\:]\d{1,2}[\:]\d{1,2}/g) != 0) { return false; }
		var parts = str.split('/'); if (parts.length != 3) { return false; }
		y = parseInt(parts[0], 10);	m = parseInt(parts[1], 10);
		var out = extractTime(parts[2]); if (out === false) { return false; }
		d = out[0]; h = out[1]; i = out[2]; s = out[3]; daytime = true;
	} else if (format == 'd-m-Y H:i:s') {
		if (str.search(/^\d{1,2}[\-]\d{1,2}[\-]\d{4}[\s]\d{1,2}[\:]\d{1,2}[\:]\d{1,2}/g) != 0) { return false; }
		var parts = str.split('-'); if (parts.length != 3) { return false; }
		d = parseInt(parts[0], 10); m = parseInt(parts[1], 10);
		var out = extractTime(parts[2]); if (out === false) { return false; }
		y = out[0]; h = out[1]; i = out[2]; s = out[3]; daytime = true;
	} else if (format == 'd/m/Y H:i:s') {
		if (str.search(/^\d{1,2}[\/]\d{1,2}[\/]\d{4}[\s]\d{1,2}[\:]\d{1,2}[\:]\d{1,2}/g) != 0) { return false; }
		var parts = str.split('/'); if (parts.length != 3) { return false; }
		d = parseInt(parts[0], 10); m = parseInt(parts[1], 10);
		var out = extractTime(parts[2]); if (out === false) { return false; }
		y = out[0]; h = out[1]; i = out[2]; s = out[3]; daytime = true;
	} else if (format == 'm-d-Y H:i:s') {
		if (str.search(/^\d{1,2}[\-]\d{1,2}[\-]\d{4}[\s]\d{1,2}[\:]\d{1,2}[\:]\d{1,2}/g) != 0) { return false; }
		var parts = str.split('-'); if (parts.length != 3) { return false; }
		d = parseInt(parts[1], 10); m = parseInt(parts[0], 10);
		var out = extractTime(parts[2]); if (out === false) { return false; }
		y = out[0]; h = out[1]; i = out[2]; s = out[3]; daytime = true;
	} else if (format == 'm/d/Y H:i:s') {
		if (str.search(/^\d{1,2}[\/]\d{1,2}[\/]\d{4}[\s]\d{1,2}[\:]\d{1,2}[\:]\d{1,2}/g) != 0) { return false; }
		var parts = str.split('/'); if (parts.length != 3) { return false; }
		d = parseInt(parts[1], 10); m = parseInt(parts[0], 10);
		var out = extractTime(parts[2]); if (out === false) { return false; }
		y = out[0]; h = out[1]; i = out[2]; s = out[3]; daytime = true;
	} else {
		return false; //not supported format
	}

	if (daytime === true) {
		var dt = new Date(y, m - 1, d, h, i, s);
		if (dt.getMonth() + 1 != m) { return false; }
		if (dt.getDate() != d) { return false; }
		if (dt.getFullYear() != y) { return false; }
		if (dt.getHours() != h) { return false; }
		if (dt.getMinutes() != i) { return false; }
		if (dt.getSeconds() != s) { return false; }
		return true;
	} else {
		var dt = new Date(y, m - 1, d, h, i, s);
		if (dt.getMonth() + 1 != m) { return false; }
		if (dt.getDate() != d) { return false; }
		if (dt.getFullYear() != y) { return false; }
		return true;		
	}
}

/* USED IN elxValidateDate */
function extractTime(str) {
	var out = new Array(0, 0, 0, 0);
	var parts = str.split(' '); if (parts.length != 2) { return false; }
	out[0] = parseInt(parts[0], 10);
	var parts2 = parts[1].split(':'); if (parts2.length != 3) { return false; }
	out[1] = parseInt(parts2[0], 10); out[2] = parseInt(parts2[1], 10); out[3] = parseInt(parts2[2], 10);
	return out;
}

/* VALIDATE DATE INPUT FIELD */
function elxValidateDateBox(fel, format, allowempty) {
	if (!document.getElementById(fel)) { return true; }
	var fstr = document.getElementById(fel).value;
	return elxValidateDate(fstr, format, allowempty);
}

/* VALIDATE NUMERIC INPUT FIELD */
function elxValidateNumericBox(fel, allowempty) {
	if (!document.getElementById(fel)) { return true; }
	var fstr = document.getElementById(fel).value;
	if (fstr == '') { if (allowempty == true) { return true; } else { return false; } }
	var strValidChars = "0123456789.-";
	var strChar;
	var blnResult = true;
	for (i = 0; i < fstr.length && blnResult == true; i++) {
		strChar = fstr.charAt(i);
		if (strValidChars.indexOf(strChar) == -1) { blnResult = false; }
	}
	return blnResult;
}

/* VALIDATE DATE */
function elxValidateURL(str, allowempty) {
	if (str == '') { if (allowempty == true) { return true; } else { return false; } }
	var regexp = /(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
	return regexp.test(str);
}

/* VALIDATE DATE BOX */
function elxValidateURLBox(fel, allowempty) {
	if (!document.getElementById(fel)) { return true; }
	var fstr = document.getElementById(fel).value;
	return elxValidateURL(fstr, allowempty);
}

/* CHECK IF AN INPUT OR SELECT BOX IS EMPTY */
function elxIsEmpty(fel) {
	if (!document.getElementById(fel)) { return false; }
	var eObj = document.getElementById(fel);
	if (eObj.nodeName == 'SELECT') {
		var val = eObj.options[eObj.selectedIndex].value;
	} else {
		var tv = eObj.value;
		var val = tv.replace(/^\s+|\s+$/gm,'');
	}
	if (val == '') { return true; }
    return false;
}

/* FOCUS ON A FORM ELEMENT */
function elxFocus(efel) {
	var element = document.getElementById(efel);
	if (!element) { return; }
	element.style.backgroundColor = '#feeded';
	element.style.borderColor = '#f7c2c2';
	element.focus();
	setTimeout("elxRestoreBoxColor('" + efel + "')", 1500);
}

/* MOVE TO AN ELEMENT BY ID (ELXIS 4.5+) */
function elxMoveTo(efel) {
	var element = document.getElementById(efel);
	if (!element) { return; }
	var ypos = -1;
	var curtop = 0;
	if (element.offsetParent) {
		do { curtop += element.offsetTop; } while (element = element.offsetParent);
		ypos = [curtop];
	}
	if (ypos == -1) { return; }
	window.scroll(0,ypos);
}

/* RESTORE FORM ELEMENT BG COLOUR */
function elxRestoreBoxColor(efel) {
	document.getElementById(efel).style.backgroundColor = '#FFFFFF';
	document.getElementById(efel).style.borderColor = '#bbb';
}

/* PASSWORD STRENGTH METER */
function elxPasswordMeter(fname, fpword, fpuname) {
	if (!document.getElementById(fpword)) { return; }
	var fimg = fpword+'meter';
	if (!document.getElementById(fimg)) { return; }
	var password = document.getElementById(fpword).value;
	if ((fpuname != null) && (fpuname != '')) {
		if (document.getElementById(fpuname)) { var username = document.getElementById(fpuname).value; } else { var username = ''; }
	} else {
		var username = '';
	}
	var score = elxCheckStrongPassword(password, username);
	var baseurl = document.getElementById('elxisbase'+fname).value;
	if (score == -2000) {
		document.getElementById(fimg).src = baseurl+'/includes/libraries/elxis/form/level0.png';
		document.getElementById(fimg).title = 'short';
	} else if (score == -2001) {
		document.getElementById(fimg).src = baseurl+'/includes/libraries/elxis/form/level0.png';
		document.getElementById(fimg).title = 'username equals password';
	} else if (score < 20) {
		document.getElementById(fimg).src = baseurl+'/includes/libraries/elxis/form/level1.png';
		document.getElementById(fimg).title = 'very weak - '+score+'%';
	} else if (score < 40) {
		document.getElementById(fimg).src = baseurl+'/includes/libraries/elxis/form/level2.png';
		document.getElementById(fimg).title = 'weak - '+score+'%';
	} else if (score < 60) {
		document.getElementById(fimg).src = baseurl+'/includes/libraries/elxis/form/level3.png';
		document.getElementById(fimg).title = 'good - '+score+'%';
	} else if (score < 80) {
		document.getElementById(fimg).src = baseurl+'/includes/libraries/elxis/form/level4.png';
		document.getElementById(fimg).title = 'strong - '+score+'%';
	} else {
		document.getElementById(fimg).src = baseurl+'/includes/libraries/elxis/form/level5.png';
		document.getElementById(fimg).title = 'very strong - '+score+'%';
	}
}

/* CHECK PASSWORD STRENGTH */
function elxCheckStrongPassword(password, username) {
	if (password.length < 4) { return -2000; }
	if (username != '') { if (password.toLowerCase()==username.toLowerCase()) { return -2001; } }
	var score = 0;
	score += password.length * 4;
	score += (elxCheckRepetition(1,password).length - password.length) * 1;
	score += (elxCheckRepetition(2,password).length - password.length) * 1;
	score += (elxCheckRepetition(3,password).length - password.length) * 1;
	score += (elxCheckRepetition(4,password).length - password.length) * 1;
	if (password.match(/(.*[0-9].*[0-9].*[0-9])/)){ score += 5;}
	if (password.match(/(.*[!,@,#,$,%,^,&,*,?,_,~].*[!,@,#,$,%,^,&,*,?,_,~])/)){ score += 5 ;}
	if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)){ score += 10;}
	if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/)){ score += 15;}
	if (password.match(/([!,@,#,$,%,^,&,*,?,_,~])/) && password.match(/([0-9])/)){ score += 15;}
	if (password.match(/([!,@,#,$,%,^,&,*,?,_,~])/) && password.match(/([a-zA-Z])/)){score += 15;}
	if (password.match(/^\w+$/) || password.match(/^\d+$/) ){ score -= 10;}
	if (score < 0) { score = 0; }
	if (score > 100) { score = 100; }
	return parseInt(score);
}

/* CHECK STRING REPETITION */
function elxCheckRepetition(pLen,str) {
	var res = '';
	for (var i=0; i<str.length ; i++) {
		var repeated=true;
		for (var j=0;j < pLen && (j+i+pLen) < str.length;j++) {
			repeated=repeated && (str.charAt(j+i)==str.charAt(j+i+pLen));
		}
		if (j<pLen){ repeated=false; }
		if (repeated) { i+=pLen-1; repeated=false; } else { res+=str.charAt(i); }
	}
	return res;
}

/* POPUP WINDOW (optional attributes: w, h, title, scrollbars) */
function elxPopup(pageURL, w, h, pageTitle, scrbars) {
	if (!w) {
		var w = 600;
	} else {
		w = parseInt(w);
		if (w < 10) { w = 600; }
	}
	if (w >= screen.width) { w = screen.width - 40; }
	if (!h) {
		var h = 400;
	} else {
		h = parseInt(h);
		if (h < 10) { h = 400; }
	}
	if (h >= screen.height) { h = screen.height - 40; }
    if ((pageTitle === undefined) || (pageTitle === null) || (pageTitle == '')) { var pageTitle = 'popup window'; }
    if ((scrbars === undefined) || (scrbars === null) || (scrbars == '')) { var scrbars = 'yes'; }
	var pleft = (screen.width/2)-(w/2);
	var ptop = (screen.height/2)-(h/2);
	var win2 = window.open(pageURL, pageTitle, 'status=no, width='+w+', height='+h+', top='+ptop+', left='+pleft+', resizable=no, toolbar=no, menubar=no, location=no, directories=no, scrollbars='+scrbars+', copyhistory=no');
	win2.focus();
}

/* CREATE A STANDARD AJAX OBJECT */
function newStdAjax() {
    var ro;
    if (window.XMLHttpRequest) {
        ro = new XMLHttpRequest();
    } else if (window.ActiveXObject) {
        ro = new ActiveXObject("Msxml2.XMLHTTP");
        if (!ro) {
            ro = new ActiveXObject("Microsoft.XMLHTTP");
        }
    }
    return ro;
}

/* COMPATIBILITY CODE FOR JSON stringify */
var JSON = JSON || {};
JSON.stringify = JSON.stringify || function (obj) {
	var t = typeof (obj);
    if (t != "object" || obj === null) {
        if (t == "string") obj = '"'+obj+'"';
        return String(obj);
    } else {
        var n, v, json = [], arr = (obj && obj.constructor == Array);
        for (n in obj) {
            v = obj[n]; t = typeof(v);
            if (t == "string") v = '"'+v+'"';
            else if (t == "object" && v !== null) v = JSON.stringify(v);
            json.push((arr ? "" : '"' + n + '":') + String(v));
        }
        return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
    }
};


/* AJAX WRAPPER - WORKS WITH OR WITHOUT JQUERY */
function elxAjax(etype, eurl, edata, eloadelement, eloadtext, successfunc, errorfunc) {
	if ((etype == null) || (etype == '')) { etype = 'GET'; }
	if (eurl == '') { return false; }
	if (errorfunc == null) {
		errorfunc = function (XMLHttpRequest, textStatus, errorThrown) { alert('Error! '+errorThrown); }
	}

	if (successfunc == null) {
		if ((eloadelement != null) && (eloadelement != '')) {
			successfunc = function (result) { document.getElementById(eloadelement).innerHTML = result; }
		} else {
			successfunc = function (result) { }
		}
	}

	if ((eloadtext != null) && (eloadtext != '') && (eloadelement != null) && (eloadelement != '')) {
		document.getElementById(eloadelement).innerHTML = eloadtext;
		if (typeof jQuery != 'undefined') { $('#'+eloadelement).fadeIn('slow'); } else { document.getElementById(eloadelement).style.display = ''; }
	}

	if (etype == 'GET') {
		if (typeof jQuery != 'undefined') {
			if (edata && (typeof (edata) === 'object')) { edata = $.param(edata); }
        	$.ajax({
            	type: 'GET',
            	url: eurl,
            	data: edata,
            	success: successfunc,
            	error: errorfunc
        	});
		} else {
			var rhttp = newStdAjax();
			if (edata && (typeof (edata) === 'object')) {
				var sdata = '';
				for (k in edata) { sdata += k+'='+edata[k]+'&'; }
				sdata += 'rnd='+Math.random();
				edata = sdata;
			}

			try {
            	rhttp.open('GET', eurl+'?'+edata, true);
            	rhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            	rhttp.setRequestHeader('charset', 'utf-8');
				rhttp.onreadystatechange = function () {
					if (rhttp.readyState == 4) {
						if (rhttp.status != 200) {
							errorfunc(rhttp.responseText, rhttp.status, rhttp.statusText);
						} else {
							successfunc(rhttp.responseText);
						}
					}
				};
            	rhttp.send(null);
			}
			catch(e){}
			finally{}
		}

        return;
	}

	if (etype == 'JSON') {
		if (edata && (typeof(edata) === 'object')) { edata = JSON.stringify(edata); }
		if (typeof jQuery != 'undefined') {
        	$.ajax({
            	type: 'POST',
            	url: eurl,
            	data: edata,
            	dataType: 'json',
            	contentType: 'application/json; charset=utf-8',
            	success: successfunc,
				error: errorfunc
			});
			return;
		} else {
			var rhttp = newStdAjax();
			try {
            	rhttp.open('POST', eurl, true);
            	rhttp.setRequestHeader('Content-Type', 'application/json');
            	rhttp.setRequestHeader('charset', 'utf-8');
				rhttp.onreadystatechange = function () {
					if (rhttp.readyState == 4) {
						if (rhttp.status != 200) {
							errorfunc(rhttp.responseText, rhttp.status, rhttp.statusText);
						} else {
							successfunc(rhttp.responseText);
						}
					}
				};
            	rhttp.send(edata);
			}
			catch(e){}
			finally{}
		}
	}

	if (etype == 'POST') {
		if (typeof jQuery != 'undefined') {
			if (edata && (typeof (edata) === 'object')) { edata = $.param(edata); }
        	$.ajax({
            	type: 'POST',
            	url: eurl,
            	data: edata,
            	dataType: 'html',
            	contentType: 'application/x-www-form-urlencoded; charset=utf-8',
            	success: successfunc,
				error: errorfunc
			});
			return;
		} else {
			if (edata && (typeof (edata) === 'object')) {
				var sdata = '';
				for (k in edata) { sdata += k+'='+edata[k]+'&'; }
				sdata += 'rnd='+Math.random();
				edata = sdata;
			}
			var rhttp = newStdAjax();
			try {
           		rhttp.open('POST', eurl, true);
           		rhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
           		rhttp.setRequestHeader('charset', 'utf-8');
				rhttp.onreadystatechange = function () {
					if (rhttp.readyState == 4) {
						if (rhttp.status != 200) {
							errorfunc(rhttp.responseText, rhttp.status, rhttp.statusText);
						} else {
							successfunc(rhttp.responseText);
						}
					}
				};
           		rhttp.send(edata);
			}
			catch(e){}
			finally{}
		}
	}
}

/* SUBMIT ELXIS FORM */
function elxSubmit(pressbutton, formname, actionurl) {
	if (typeof pressbutton == 'undefined') { pressbutton = ''; }
	if (typeof formname == 'undefined') { formname = 'elxisform'; }
	if (formname == '') { formname = 'elxisform'; }
	if (typeof actionurl == 'undefined') { actionurl = ''; }
	if (formname == 'elxisform') {
		document.elxisform.task.value = pressbutton;
		if (actionurl != '')  { document.elxisform.action = actionurl; }
		elxformvalelxisform();
	} else {
		document[formname].task.value = pressbutton;
		if (actionurl != '')  { document[formname].action = actionurl; }
		var func = window['elxformval'+formname];
		if (typeof func === 'function') { func(); }
	}
}

/* SET AUTOCOMPLETE OFF FOR AN ELEMENT */
function elxAutocompOff(elem) {
	if (null == elem) { return; }
	if (window.addEventListener) {
		window.addEventListener('load', function() {
			document.getElementById(elem).setAttribute("autocomplete", "off");
		}, false);
	} else if (window.attachEvent) {
		window.attachEvent('onload', function() {
			document.getElementById(elem).setAttribute("autocomplete", "off");
		});
	}
}

/* SUPPORT FOR MULTIPLE WINDOW ONLOAD EVENTS */
function elxLoadEvent(func) {
	if (typeof window.onload != 'function') {
		window.onload = func;
	} else {
		var oldonload = window.onload;
		window.onload = function() {
			if (oldonload) { oldonload(); }
			func();
		}
	}
}

/* MAKE DATETIME STRING FROM PARAMETERS SELECT BOXES */
function elxMakeDatetime(dtname, dttype) {
	if (!document.getElementById('params'+dtname)) { return; }
	var str = '';
	if ((dttype == 'datetime') || (dttype == 'time')) {
		if (!document.getElementById(dtname+'_hour')) { return; }
		if (!document.getElementById(dtname+'_minute')) { return; }
		var dthour = document.getElementById(dtname+'_hour').options[document.getElementById(dtname+'_hour').selectedIndex].value;
		var dtminute = document.getElementById(dtname+'_minute').options[document.getElementById(dtname+'_minute').selectedIndex].value;
		if (isNaN(dthour)) { return; }
		if (isNaN(dtminute)) { return; }
		dthour = parseInt(dthour);
		dtminute = parseInt(dtminute);
		if ((dthour < 0) || (dthour > 23) || (dtminute < 0) || (dtminute > 59)) { return; }
		if (dthour < 10) { str += '0'; }
		str += dthour+':';
		if (dtminute < 10) { str += '0'; }
		str += dtminute;
	}

	if (dttype == 'time') { document.getElementById('params'+dtname).value = str; return; }
	if (dttype == 'datetime') { str += ':00'; }
	if (!document.getElementById(dtname+'_year')) { return; }
	if (!document.getElementById(dtname+'_month')) { return; }
	if (!document.getElementById(dtname+'_day')) { return; }
	var dtyear = document.getElementById(dtname+'_year').options[document.getElementById(dtname+'_year').selectedIndex].value;
	var dtmonth = document.getElementById(dtname+'_month').options[document.getElementById(dtname+'_month').selectedIndex].value;
	var dtday = document.getElementById(dtname+'_day').options[document.getElementById(dtname+'_day').selectedIndex].value;
	if (isNaN(dtyear)) { return; }
	if (isNaN(dtmonth)) { return; }
	if (isNaN(dtday)) { return; }
	dtyear = parseInt(dtyear);
	dtmonth = parseInt(dtmonth);
	dtday = parseInt(dtday);
	if ((dtyear < 1900) || (dtyear > 2100)) { return; }
	if ((dtmonth < 1) || (dtmonth > 12)) { return; }
	if ((dtday < 1) || (dtday > 31)) { return; }
	var objdt = new Date(dtyear, dtmonth - 1, dtday, 12, 0, 0);
	if ((objdt.getFullYear() != dtyear) || (objdt.getMonth() + 1 != dtmonth) || (objdt.getDate() != dtday)) { alert('Invalid date!'); return; }
	var str2 = dtyear+'-';
	if (dtmonth < 10) { str2 += '0'; }
	str2 += dtmonth+'-';
	if (dtday < 10) { str2 += '0'; }
	str2 += dtday;
	if (dttype == 'datetime') { str2 += ' '+str; }
	document.getElementById('params'+dtname).value = str2;
}

/* I AM NT A ROBOT KEY GENERATION */
function elxIamNotRobot(elid) {
	var boxid = elid+'box';
	if (!document.getElementById(boxid)) { return; }
	if (!document.getElementById(elid)) { return; }
	var v = document.getElementById(elid).value;
	if (v != '') { return; } //already clicked
	document.getElementById(boxid).className = 'elxnorobotbox';
	document.getElementById(boxid).innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
	var rnd = Math.floor((Math.random()*100)+1);
	var edata = { 'rnd': rnd };
	var successfunc = function(xreply) {
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			return false;
		}
		if (parseInt(jsonObj.success, 10) < 1) {
			document.getElementById(boxid).className = 'noroboterror';
			document.getElementById(boxid).innerHTML = '\uf071';//fontawesome warning icon
		} else {
			document.getElementById(elid).value = jsonObj.captchakey;
			document.getElementById(boxid).className = 'norobotok';
			document.getElementById(boxid).innerHTML = '\uf00c';//fontawesome check icon
		}
	};
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		document.getElementById(boxid).className = 'noroboterror';
		document.getElementById(boxid).innerHTML = '\uf071';
	};

	var action = document.getElementById(elid).getAttribute('data-genbase')+'captchagen';
	elxAjax('POST', action, edata, null, null, successfunc, errorfunc);
}

/* ELXIS 5.x */
function elx5DataTable(tbl, multiselect) {//ON DOC READY
	if (!document.getElementById(tbl)) { return; }
	var checkboxes = document.getElementById(tbl).querySelectorAll('td input.elx5_datacheck');
	if (!checkboxes) { return false; }
	for (var cx=0; cx < checkboxes.length; cx++) {
		checkboxes[cx].addEventListener('change', function() {
			elx5SelectTableRow(tbl, this.id, multiselect);
		});
	}
}

function elx5SelectTableRow(tbl, cboxid, multiselect) {
	var checkboxes = document.getElementById(tbl).querySelectorAll('td input.elx5_datacheck');
	if (!checkboxes) { return false; }
	var cx, rowclass;
	var haschecked = false;
	for (cx=0; cx < checkboxes.length; cx++) {
		rowclass = '';
		rowidx = checkboxes[cx].value;
		if (document.getElementById('datarow'+rowidx).className == 'elx5_invisible') { continue; }//from function "elx5FilterTable"
		if (multiselect) {
			if (checkboxes[cx].checked) { rowclass = 'elx5_rowchecked'; haschecked = true; }
		} else {
			if (checkboxes[cx].id == cboxid) {
				if (checkboxes[cx].checked) { rowclass = 'elx5_rowchecked'; haschecked = true; }
			} else {
				checkboxes[cx].checked = false;
			}
		}
		if (document.getElementById('datarow'+rowidx)) {//tr element
			document.getElementById('datarow'+rowidx).className = rowclass;
		}
	}

	var actbuttons = document.querySelectorAll('.elx5_dataaction');
	if (actbuttons) {
		for (var ax=0; ax < actbuttons.length; ax++) {//they might be select boxes too!
			if (actbuttons[ax].hasAttribute('data-selector')) {
				if (actbuttons[ax].getAttribute('data-selector') == 1) {
					var activeClass = 'elx5_dataactive';
					var other_classes = '';
					if (actbuttons[ax].classList.contains('elx5_invisible')) { other_classes += ' elx5_invisible'; }
					if (actbuttons[ax].classList.contains('elx5_mobhide')) { other_classes += ' elx5_mobhide'; }
					if (actbuttons[ax].classList.contains('elx5_lmobhide')) { other_classes += ' elx5_lmobhide'; }
					if (actbuttons[ax].classList.contains('elx5_tabhide')) { other_classes += ' elx5_tabhide'; }
					if (actbuttons[ax].classList.contains('elx5_smallscreenhide')) { other_classes += ' elx5_smallscreenhide'; }
					if (actbuttons[ax].classList.contains('elx5_midscreenhide')) { other_classes += ' elx5_midscreenhide'; }
					if (actbuttons[ax].hasAttribute('data-activeclass')) {
						activeClass  = actbuttons[ax].getAttribute('data-activeclass');
						if (activeClass == '') { activeClass = 'elx5_dataactive'; }
					}
					if (haschecked) {
						actbuttons[ax].className = 'elx5_dataaction '+activeClass+''+other_classes;
						if (actbuttons[ax].tagName == 'SELECT') {
							actbuttons[ax].disabled = false;
							actbuttons[ax].selectedIndex = 0;
						}
					} else {
						actbuttons[ax].className = 'elx5_dataaction'+other_classes;
						if (actbuttons[ax].tagName == 'SELECT') {
							actbuttons[ax].disabled = true;
							actbuttons[ax].selectedIndex = 0;
						}
					}
				}
			}
		}
	}
}


//otherdata is optional stringify json string for additional data to pass via AJAX. Example: {"city":"Athens","country":"Hellas"}
function elx5DeleteTableRows(tbl, reloadpage, otherdata) {
	var tblObj = document.getElementById(tbl);
	var checkboxes = tblObj.querySelectorAll('td input.elx5_datacheck');
	if (!checkboxes) { return false; }
	var elids = '';
	for (var cx=0; cx < checkboxes.length; cx++) {
		if (checkboxes[cx].checked) { elids += checkboxes[cx].value+','; }
	}
	elids = elids.replace(/,+$/,'');
	if (elids == '') { return; }
	var prompttxt = tblObj.getAttribute('data-deletelng');
	if (confirm(prompttxt)) {
		var rnd = Math.floor((Math.random()*100)+1);
		var edata = { 'elids':elids, 'rnd':rnd };
		if (typeof otherdata != 'undefined') {
			if (otherdata != '') {
				try {
					var jsonOther = JSON.parse(otherdata);
					for (var jk in jsonOther) {
						edata[jk] = jsonOther[jk];
					}
				} catch(e) {}
			}
		}
		if (tblObj.hasAttribute('data-deletepage')) {
			var eurl = tblObj.getAttribute('data-deletepage');
		} else if (tblObj.hasAttribute('data-inpage')) {
			var eurl = tblObj.getAttribute('data-inpage')+'delete';
		} else {
			var eurl = tblObj.getAttribute('data-listpage')+'delete';
		}
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
				//deactivate table buttons
				var actbuttons = document.querySelectorAll('.elx5_dataaction');
				if (actbuttons) {
					for (var ax=0; ax < actbuttons.length; ax++) {
						if (actbuttons[ax].hasAttribute('data-alwaysactive')) { continue; }
						actbuttons[ax].className = 'elx5_dataaction';
					}
				}
				if (reloadpage) {
					location.reload(true);
				} else {
					var itemids = elids.split(',');
					for (a in itemids) {
						if (isNaN(itemids[a])) {//if is string!
							var rid = itemids[a];
						} else {
							var rid = parseInt(itemids[a], 10);
							if (rid < 1) { break; }
						}
						if (document.getElementById('datarow'+rid)) {
							var rowObj = document.getElementById('datarow'+rid);
							tblObj.deleteRow(rowObj.rowIndex);
						}
					}
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


function elx5EditTableRow(tbl, idname) {
	var tblObj = document.getElementById(tbl);
	var checkboxes = tblObj.querySelectorAll('td input.elx5_datacheck');
	if (!checkboxes) { return false; }
	var elid = '';
	for (var cx=0; cx < checkboxes.length; cx++) {
		if (checkboxes[cx].checked) { elid = checkboxes[cx].value; break; }
	}
	if (elid == '') { return; }
	if (tblObj.hasAttribute('data-editpage')) {
		var eurl = tblObj.getAttribute('data-editpage');
	} else {
		var eurl = tblObj.getAttribute('data-listpage')+'edit.html';
	}
	if (eurl.indexOf('?') == -1) {
		eurl += '?'+idname+'='+elid;
	} else {
		eurl += '&'+idname+'='+elid;
	}
	window.location.href = eurl;
}


function elx5ActionTableRows(tbl, action, reloadpage, confirmtext) {
	if (typeof confirmtext === 'undefined') { confirmtext = ''; }
	var tblObj = document.getElementById(tbl);
	var checkboxes = tblObj.querySelectorAll('td input.elx5_datacheck');
	if (!checkboxes) { return false; }
	var elids = '';
	for (var cx=0; cx < checkboxes.length; cx++) {
		if (checkboxes[cx].checked) { elids += checkboxes[cx].value+','; }
	}
	elids = elids.replace(/,+$/,'');
	if (elids == '') { return; }
	var rnd = Math.floor((Math.random()*100)+1);
	var edata = { 'elids':elids, 'rnd':rnd };
	var eurl = tblObj.getAttribute('data-listpage')+''+action;
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
			//deactivate table buttons
			var actbuttons = document.querySelectorAll('.elx5_dataaction');
			if (actbuttons) {
				for (var ax=0; ax < actbuttons.length; ax++) {
					if (actbuttons[ax].hasAttribute('data-alwaysactive')) { continue; }
					actbuttons[ax].className = 'elx5_dataaction';
				}
			}
			if (reloadpage) { location.reload(true); }
		}
	};
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader();
		alert('Action failed! '+errorThrown);
	};

	if (confirmtext == '') {
		elx5StartPageLoader();
		elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
	} else {
		if (confirm(confirmtext)) {
			elx5StartPageLoader();
			elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
		}
	}
}


function elx5SelectedTableItem(tbl, multiselect) {
	if (typeof multiselect === 'undefined') { multiselect = false; }
	var checkboxes = document.getElementById(tbl).querySelectorAll('td input.elx5_datacheck');
	if (!checkboxes) { return false; }
	var cx;
	var selected_single = false;
	var selected_multiple = [];
	for (cx=0; cx < checkboxes.length; cx++) {
		if (multiselect) {
			if (checkboxes[cx].checked) { selected_multiple.push(checkboxes[cx].value); }
		} else {
			if (checkboxes[cx].checked) {
				selected_single = checkboxes[cx].value;
				break;
			}
		}
	}

	if (multiselect) {
		if (selected_multiple.length == 0) { return false; }
		return selected_multiple;
	}
	return selected_single;
}


function elx5SortableTable(tbl) {//ON DOC READY
	if (!document.getElementById(tbl)) { return; }
	var stable = document.getElementById(tbl);
	var srows = stable.getElementsByTagName('TR');
	for (var q=0; q < srows[0].cells.length; q++) {
		if (srows[0].cells[q].className == 'elx5_nosorting') { continue; }
		srows[0].cells[q].addEventListener('click', function() {
			elx5SortTable(tbl, this.cellIndex);
		});
	}
}


function elx5SortTable(tbl, n) {
	var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
	table = document.getElementById(tbl);
	switching = true;
	dir = "asc";
	while (switching) {
		switching = false;
		rows = table.getElementsByTagName('TR');
		for (i = 1; i < (rows.length - 1); i++) {
			shouldSwitch = false;
			if (!rows[i].getElementsByTagName("TD")[n]) { continue; }
			if (!rows[i].getElementsByTagName("TD")[n].hasAttribute('data-value')) { continue; }
			if (!rows[i + 1].getElementsByTagName("TD")[n].hasAttribute('data-value')) { continue; }

			x = rows[i].getElementsByTagName("TD")[n].getAttribute('data-value').toLowerCase();
			y = rows[i + 1].getElementsByTagName("TD")[n].getAttribute('data-value').toLowerCase();
			if (isNaN(x) || isNaN(y)) {
			} else {
				x = parseFloat(x);
				y = parseFloat(y);
			}
			if (dir == "asc") {
				if (x > y) { shouldSwitch = true; break; }
			} else if (dir == "desc") {
				if (x < y) { shouldSwitch = true; break; }
			}
		}
		if (shouldSwitch) {
			rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
			switching = true;
			switchcount ++;
		} else {
			if (switchcount == 0 && dir == "asc") {
				dir = "desc";
				switching = true;
			}
		}
	}
	var krows = table.getElementsByTagName('TR');
	for (var bx=0; bx < krows[0].cells.length; bx++) {
		if (krows[0].cells[bx].classList.contains('elx5_nosorting')) { continue; }
		krows[0].cells[bx].classList.remove('elx5_sorting', 'elx5_sorting_asc', 'elx5_sorting_desc');
		if (bx == n) {
			if (dir == 'asc') {
				krows[0].cells[bx].classList.add('elx5_sorting_asc');
			} else {
				krows[0].cells[bx].classList.add('elx5_sorting_desc');
			}
		} else {
			krows[0].cells[bx].classList.add('elx5_sorting');
		}
	}
}

function elx5CheckTableRows(tbl, sfx) {
	if (!document.getElementById('elx5_datacheckall'+sfx)) { return; }
	var checkboxes = document.getElementById(tbl).querySelectorAll('td input.elx5_datacheck');
	if (!checkboxes) { return false; }
	var checked = document.getElementById('elx5_datacheckall'+sfx).checked;
	for (var cx=0; cx < checkboxes.length; cx++) {
		var rowidx = checkboxes[cx].value;
		if (checked) {
			checkboxes[cx].checked = true;
			document.getElementById('datarow'+rowidx).className = 'elx5_rowchecked';
		} else {
			checkboxes[cx].checked = false;
			document.getElementById('datarow'+rowidx).className = '';
		}
	}

	var actbuttons = document.querySelectorAll('.elx5_dataaction');
	if (actbuttons) {
		for (var ax=0; ax < actbuttons.length; ax++) {//they might be select boxes too!
			if (actbuttons[ax].hasAttribute('data-selector')) {
				if (actbuttons[ax].getAttribute('data-selector') == 1) {
					var activeClass = 'elx5_dataactive';
					var other_classes = '';
					if (actbuttons[ax].classList.contains('elx5_invisible')) { other_classes += ' elx5_invisible'; }
					if (actbuttons[ax].classList.contains('elx5_mobhide')) { other_classes += ' elx5_mobhide'; }
					if (actbuttons[ax].classList.contains('elx5_lmobhide')) { other_classes += ' elx5_lmobhide'; }
					if (actbuttons[ax].classList.contains('elx5_tabhide')) { other_classes += ' elx5_tabhide'; }
					if (actbuttons[ax].classList.contains('elx5_smallscreenhide')) { other_classes += ' elx5_smallscreenhide'; }
					if (actbuttons[ax].classList.contains('elx5_midscreenhide')) { other_classes += ' elx5_midscreenhide'; }
					if (actbuttons[ax].hasAttribute('data-activeclass')) {
						activeClass  = actbuttons[ax].getAttribute('data-activeclass');
						if (activeClass == '') { activeClass = 'elx5_dataactive'; }
					}
					if (checked) {
						actbuttons[ax].className = 'elx5_dataaction '+activeClass+''+other_classes;
						if (actbuttons[ax].tagName == 'SELECT') {
							actbuttons[ax].disabled = false;
							actbuttons[ax].selectedIndex = 0;
						}
					} else {
						actbuttons[ax].className = 'elx5_dataaction'+other_classes;
						if (actbuttons[ax].tagName == 'SELECT') {
							actbuttons[ax].disabled = true;
							actbuttons[ax].selectedIndex = 0;
						}
					}
				}
			}
		}
	}
}

function elx5FilterPage(pname, sObj) {
	var newurl = sObj.getAttribute('data-actlink');
	if (newurl.indexOf('?') == -1) {
		newurl += '?'+pname+'='+sObj.options[sObj.selectedIndex].value;
	} else {
		newurl += '&'+pname+'='+sObj.options[sObj.selectedIndex].value;
	}
	if (sObj.hasAttribute('data-sn') && sObj.hasAttribute('data-so')) {
		newurl += '&sn='+sObj.getAttribute('data-sn')+'&so='+sObj.getAttribute('data-so');
	}
	if (sObj.hasAttribute('data-subname')) {
		var subname = sObj.getAttribute('data-subname');
		if ((subname != '') && sObj.options[sObj.selectedIndex].hasAttribute('data-subid')) {
			var subvalue = sObj.options[sObj.selectedIndex].getAttribute('data-subid');
			newurl += '&'+subname+'='+subvalue;
		}
	}
	window.location.href = newurl;
}

function elx5FilterTable(tbl, dataname, sObj, visclass) {
	if (typeof visclass === 'undefined') { visclass = ''; }
	var trrows = document.getElementById(tbl).querySelectorAll('tr');
	if (!trrows) { return false; }
	var ischecked = false;
	var sVal = sObj.options[sObj.selectedIndex].value;
	for (var cx=0; cx < trrows.length; cx++) {
		if (!trrows[cx].hasAttribute(dataname)) { continue; }
		ischecked = false;
		var cboxid = trrows[cx].id.replace('datarow', 'dataprimary');
		if (document.getElementById(cboxid)) {
			if (document.getElementById(cboxid).checked) { ischecked = true; }
		}

		if (sVal == '') {
			trrows[cx].className = ischecked ? 'elx5_rowchecked' : visclass;
			continue;
		}

		if (trrows[cx].getAttribute(dataname) == sVal) {
			trrows[cx].className = ischecked ? 'elx5_rowchecked' : visclass;
		} else {
			trrows[cx].className = 'elx5_invisible';
			if (ischecked) {
				document.getElementById(cboxid).checked = false;
			}
		}
	}
}

function elx5ModalOpen(sfx) {
	if (typeof sfx !== "undefined") {
		var modal = document.getElementById('elx5_modal'+sfx);
	} else {
		var modal = document.getElementById('elx5_modal');
	}
	modal.style.display = 'flex';
}

function elx5ModalClose(sfx) {
	if (typeof sfx !== "undefined") {
		document.getElementById('elx5_modal'+sfx).style.display = 'none';
	} else {
		document.getElementById('elx5_modal').style.display = 'none';
	}
}

function elx5ModalMessageShow(sfx, message, css_class, autohidesecs) {
	if (typeof autohidesecs !== "undefined") {
		autohidesecs = parseInt(autohidesecs, 10);
	} else {
		autohidesecs = 0;
	}
	if (!document.getElementById('elx5_modalmessage'+sfx)) { return; }
	if ((typeof css_class == 'undefined') || (css_class == '')) { cssclass = 'elx5_info'; }
	document.getElementById('elx5_modalmessage'+sfx).innerHTML = message;
	document.getElementById('elx5_modalmessage'+sfx).className = css_class;
	if (autohidesecs > 0) {
		var tsdur = autohidesecs * 1000;
		setTimeout(function() {
			document.getElementById('elx5_modalmessage'+sfx).className = 'elx5_invisible';
		}, tsdur);
	}
}

function elx5ModalMessageHide(sfx) {
	document.getElementById('elx5_modalmessage'+sfx).className = 'elx5_invisible';
}

function elx5Toggle(elid, visclass) {
	if (!document.getElementById(elid)) { return; }
	if (document.getElementById(elid).hasAttribute('class')) {
		if (typeof visclass === 'undefined') { visclass = 'elx5_zero'; }
		if (document.getElementById(elid).className == 'elx5_invisible') {
			document.getElementById(elid).className = visclass;
		} else {
			document.getElementById(elid).className = 'elx5_invisible';
		}
	} else if (document.getElementById(elid).style.display == 'none') {
		document.getElementById(elid).style.display = 'block';
	} else {
		document.getElementById(elid).style.display = 'none';
	}
}

function elx5ToggleStatus(elid, aObj) {
	if (elid == '') { return false; }
	if (isNaN(elid)) {//elid is string
		//do nothing
	} else {
		elid = parseInt(elid, 10);
		if (elid < 1) { return false; }
	}
	var eurl = aObj.getAttribute('data-actlink');
	aObj.className = 'elx5_statusicon elx5_statusload';
	var oldtitle = aObj.title;
	var successfunc = function(xreply) {
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			aObj.className = 'elx5_statusicon elx5_statuswarn';
			aObj.title = 'Action failed! Response is not a valid JSON document.';
			return false;
		}
		if (parseInt(jsonObj.success, 10) == 1) {
			if (parseInt(jsonObj.published, 10) == 1) {
				aObj.className = 'elx5_statusicon elx5_statuspub';
			} else {
				aObj.className = 'elx5_statusicon elx5_statusunpub';
			}
			if (jsonObj.hasOwnProperty('iconclass')) {
				if (jsonObj.iconclass != '') { aObj.className = jsonObj.iconclass; }
			}
			aObj.title = jsonObj.icontitle;
			if (typeof jsonObj.reloadpage !== 'undefined') {
				if (jsonObj.reloadpage == 1) {
					location.reload(true);
				}
			}
		} else {
			aObj.className = 'elx5_statusicon elx5_statuswarn';
			if (jsonObj.icontitle != '') {
				aObj.title = jsonObj.icontitle;
			} else {
				aObj.title = oldtitle;
			}
			return false;
		}
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		aObj.className = 'elx5_statusicon elx5_statuswarn';
		aObj.title = 'Error! '+errorThrown;
	}
	var rnd = Math.floor((Math.random()*100)+1);
	var edata = { 'elid':elid, 'rnd':rnd };
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

function elx5SwitchStatus(elid, aobj) {
	if(!document.getElementById(elid)) { return; }
	let iObj = document.getElementById(elid);
	let v = parseInt(document.getElementById(elid).value, 10);

	let newq = 0;
	let values = aobj.getAttribute('data-values').split('|');
	for (let q=0; q < values.length; q++) {
		let vx = parseInt(values[q], 10);
		if (v == vx) {
			let q2 = q + 1;
			if (values[q2]) {
				newq = q2;
			} else {
				newq = 0;
			}
			break;
		}
	}

	let labels = aobj.getAttribute('data-labels').split('|');
	let colors = aobj.getAttribute('data-colors').split('|');

	document.getElementById(elid).value = values[newq];
	aobj.innerHTML = '<span></span>'+labels[newq];
	aobj.className = 'elx5_itemstatus elx5_itemstatus_'+colors[newq];
}

function elx5MLSwitch(prf, elemname, is_editor) {
	if (typeof is_editor == 'undefined') { is_editor = 0; }
	if (is_editor != 1) { is_editor = 0; }
	var sObj = document.getElementById(prf+''+elemname+'_lang');
	var sitelangs = sObj.getAttribute('data-sitelangs').split(',');
	var deflang = sObj.getAttribute('data-deflang');
	var trelem = sObj.getAttribute('data-trelement');
	var curlang = sObj.options[sObj.selectedIndex].value;

	if (is_editor == 1) {
		var editoroptions = {};
		editoroptions.zIndex = 1031;
		editoroptions.height = 400;
		editoroptions.toolbarAdaptive = false;
		editoroptions.direction = 'ltr';
		editoroptions.language = 'auto';
	}

	var elid, tObj;
	for (var lx=0; lx < sitelangs.length; lx++) {
		if (sitelangs[lx] == deflang) {
			elid = prf+''+trelem;
		} else {
			elid = prf+''+trelem+'_'+sitelangs[lx];
		}
		tObj = document.getElementById(elid);
		if (is_editor == 1) {
			if (typeof Jodit.instances[elid] == "undefined") {
			} else {
				editoroptions.height = Jodit.instances[elid].options.height;
				editoroptions.language = Jodit.instances[elid].options.language;
				editoroptions.removeButtons = Jodit.instances[elid].options.removeButtons;
				editoroptions.extraButtons = Jodit.instances[elid].options.extraButtons;
				editoroptions.uploader = Jodit.instances[elid].options.uploader;
				editoroptions.filebrowser = Jodit.instances[elid].options.filebrowser;
				Jodit.instances[elid].destruct();
				document.getElementById(elid).className = 'elx5_invisible';
			}
		} else if (tObj.type == 'textarea') {//textarea
			if (sitelangs[lx] == curlang) {
				tObj.className = 'elx5_textarea elx5_mlflag'+curlang;
			} else {
				tObj.className = 'elx5_invisible';
			}
		} else {//input text
			var v = tObj.value;
			if (sitelangs[lx] == curlang) {
				tObj.className = 'elx5_text elx5_mlflag'+curlang;
			} else {
				tObj.className = 'elx5_invisible';
			}
		}
	}
	sObj.className = 'elx5_select elx5_mlflag'+curlang;

	if (is_editor == 1) {
		if ((curlang == 'ac') || (curlang == 'ah') || (curlang == 'aj') || (curlang == 'al') || (curlang == 'ap') || (curlang == 'aq') || (curlang == 'ar') || (curlang == 'at') || (curlang == 'aw') || (curlang == 'fa') || (curlang == 'he') || (curlang == 'ph')) {
			editoroptions.direction = 'rtl'; 
		}
		if (curlang == deflang) {
			var tObj = document.getElementById(prf+''+trelem);
		} else {
			var tObj = document.getElementById(prf+''+trelem+'_'+curlang);
		}
		tObj.className = 'elx5_textarea elx5_mlflag'+curlang;
		var editor = new Jodit(tObj, editoroptions);
	}
}

function elx5SwitchAddLanguage(elid) {
	var sObj = document.getElementById(elid);
	var curlang = sObj.options[sObj.selectedIndex].value;
	if (curlang == '') { curlang = 'un'; }
	if (sObj.className != '') {
		var classes = sObj.className.split(' ');
		for (var q=0; q < classes.length; q++) {
			if (classes[q].indexOf('elx5_mlflag') > -1) {
				if (sObj.classList) {
					sObj.classList.remove(classes[q]);
				} else {
					sObj.className = x.className.replace('/\b'+classes[q]+'\b/g', '');
				}
			}
		}
	}
	if (sObj.classList) {
		sObj.classList.add('elx5_mlflag'+curlang);
	} else {
		if (sObj.className != '') {
			sObj.className += ' elx5_mlflag'+curlang;
		} else {
			sObj.className = 'elx5_mlflag'+curlang;
		}
	}
}

function elx5SetOrdering(inputid, elid, reloadsuc) {
	if (inputid == '') { return false; }
	if (elid == '') { return false; }//elid normally is integer but it might also be a string
	if (!document.getElementById(inputid)) { return false; }
	let ordObj = document.getElementById(inputid);
	let ordering = ordObj.value;
	if ((ordering == '') || isNaN(ordering)) { return false; }
	ordering = parseInt(ordering, 10);
	if (ordering < 1) { return false; }
	if (typeof reloadsuc == 'undefined') { reloadsuc = 0; }
	reloadsuc = parseInt(reloadsuc, 10);

	var eurl = ordObj.getAttribute('data-ordlink');
	ordObj.classList.add('elx5_inputloading');
	ordObj.readOnly = true;

	var successfunc = function(xreply) {
		ordObj.readOnly = false;
		ordObj.classList.remove('elx5_inputloading');
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			alert('Action failed! Response is not a valid JSON document.');
			return false;
		}
		if (parseInt(jsonObj.success, 10) == 1) {
			if (reloadsuc == 1) { location.reload(true); }
		} else {
			if (jsonObj.message != '') {
				alert(jsonObj.message);
			} else {
				alert('Saving order failed!');
			}
			return false;
		}
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		ordObj.readOnly = false;
		ordObj.classList.remove('elx5_inputloading');
		alert('Error! '+errorThrown);
	}

	var rnd = Math.floor((Math.random()*100)+1);
	var edata = { 'elid': elid, 'ordering':ordering, 'rnd':rnd };
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

function elx5StartPageLoader(elid) {
	if (typeof elid == 'undefined') { elid = 'elx5_pgloading'; }
	if (elid == '') { elid = 'elx5_pgloading'; }

	if (!document.getElementById(elid)) { return; }
	document.getElementById(elid).style.display = 'block';
}

function elx5StopPageLoader(elid) {
	if (typeof elid == 'undefined') { elid = 'elx5_pgloading'; }
	if (elid == '') { elid = 'elx5_pgloading'; }
	if (!document.getElementById(elid)) { return; }
	document.getElementById(elid).style.display = 'none';
}

function elx5Tabs(tabid, tabopen_class, tabcontent_class) {//ON DOC READY
	if (!document.getElementById(tabid)) { return; }
	if (typeof tabopen_class == 'undefined') { tabopen_class = 'elx5_tab_open'; }
	if (typeof tabcontent_class == 'undefined') { tabcontent_class = 'elx5_tab_content'; }
	var atabs = document.getElementById(tabid).querySelectorAll('li a');
	if (!atabs) { return false; }
	for (var tx=0; tx < atabs.length; tx++) {
		atabs[tx].addEventListener('click', function() {
			var thisidx = this.getAttribute('data-tab');
			for (var mx=0; mx < atabs.length; mx++) {
				var aidx = atabs[mx].getAttribute('data-tab');
				if (aidx == thisidx) {
					atabs[mx].className = tabopen_class;
					document.getElementById(aidx).className = tabcontent_class;
					if (document.getElementById('tabopen'+tabid)) { document.getElementById('tabopen'+tabid).value = mx; }
				} else {
					atabs[mx].className = '';
					document.getElementById(aidx).className = 'elx5_invisible';
				}
			}
		});
	}
}

function elx5SwitchToTab(tabid, openidx, tabopen_class, tabcontent_class) {
	if (!document.getElementById(tabid)) { return; }
	if (typeof tabopen_class == 'undefined') { tabopen_class = 'elx5_tab_open'; }
	if (typeof tabcontent_class == 'undefined') { tabcontent_class = 'elx5_tab_content'; }
	var atabs = document.getElementById(tabid).querySelectorAll('li a');
	if (!atabs) { return false; }
	for (var tx=0; tx < atabs.length; tx++) {
		var aidx = atabs[tx].getAttribute('data-tab');
		if (aidx == openidx) {
			atabs[tx].className = tabopen_class;
			document.getElementById(aidx).className = tabcontent_class;
			if (document.getElementById('tabopen'+tabid)) { document.getElementById('tabopen'+tabid).value = tx; }
		} else {
			atabs[tx].className = '';
			document.getElementById(aidx).className = 'elx5_invisible';
		}
	}
}

function elx5MultiSelectAdd(elid, addallval, flagvalues) {
	var sObj = document.getElementById(elid+'_selector');
	var val = sObj.options[sObj.selectedIndex].value;
	if (val == '') { return; }

	var cssclass;
	flagvalues = parseInt(flagvalues, 10);
	if ((addallval != '') && (val == addallval)) {
		var lngremove = sObj.getAttribute('data-lngremove');
		var curvals = [];
		var i, optv;
		var ihtml = '';
		for (i = 0; i < sObj.length; i++) {
			optv = sObj.options[i].value;
			if ((optv == '') || (optv == addallval)) { continue; }
			curvals.push(optv);
			cssclass = (flagvalues == 1) ? 'elx5_msel_item elx5_mlflag'+optv : 'elx5_msel_item';
			ihtml += '<a href="javascript:void(null);" class="'+cssclass+'" onclick="elx5MultiSelectRemove(\''+elid+'\', \''+optv+'\', '+flagvalues+');" title="'+lngremove+'">'+sObj.options[i].text+' <span>x</span></a>';
		}
		if (document.getElementById(elid+'_noselitem')) {
			var nov = document.getElementById(elid+'_noselitem').innerHTML;
			var noclass = (curvals.length == 0) ? 'elx5_msel_noselitem' : 'elx5_invisible';
			ihtml += '<a href="javascript:void(null);" class="'+noclass+'" id="'+elid+'_noselitem">'+nov+'</a>';
		}
		if (curvals.length == 0) {
			document.getElementById(elid).value = '';
		} else {
			document.getElementById(elid).value = curvals.join(',');
		}
		document.getElementById(elid+'_items').innerHTML = ihtml;
		sObj.selectedIndex = 0;
		return;
	}

	var name = sObj.options[sObj.selectedIndex].text;
	var curvals_str = document.getElementById(elid).value;
	var found = false;
	if (curvals_str != '') {
		var curvals = curvals_str.split(',');
		for (var i=0; i < curvals.length; i++) {
			if (curvals[i] == val) { found = true; break; }//already exists
		}
	}
	if (found) { return; }
	if (curvals_str == '') {
		curvals_str = val;
		var curvals = [val];
	} else {
		curvals.push(val); 
		curvals_str += ','+val;
	}

	if (document.getElementById(elid+'_noselitem')) { document.getElementById(elid+'_noselitem').className = 'elx5_invisible'; }
	var lngremove = sObj.getAttribute('data-lngremove');
	document.getElementById(elid).value = curvals_str;
	cssclass = (flagvalues == 1) ? 'elx5_msel_item elx5_mlflag'+val : 'elx5_msel_item';
	document.getElementById(elid+'_items').innerHTML += '<a href="javascript:void(null);" class="'+cssclass+'" onclick="elx5MultiSelectRemove(\''+elid+'\', \''+val+'\', '+flagvalues+');" title="'+lngremove+'">'+name+' <span>x</span></a>';
}

function elx5MultiSelectRemove(elid, val, flagvalues) {
	var newvals = [];
	var found = false;
	var cssclass;
	flagvalues = parseInt(flagvalues, 10);
	var vals = document.getElementById(elid).value.split(',');
	for (var k=0; k < vals.length; k++) {
		if (vals[k] != val) { newvals.push(vals[k]); }
	}
	if (newvals.length == 0) {
		document.getElementById(elid).value = '';
		var ihtml = '';
		if (document.getElementById(elid+'_noselitem')) {
			var nov = document.getElementById(elid+'_noselitem').innerHTML;
			ihtml = '<a href="javascript:void(null);" class="elx5_msel_noselitem" id="'+elid+'_noselitem">'+nov+'</a>';
		}
		document.getElementById(elid+'_items').innerHTML = ihtml;
		return;
	}
	var ihtml = '';
	var sObj = document.getElementById(elid+'_selector');
	var i, v, k;
	var lngremove = sObj.getAttribute('data-lngremove');
	for (i=0; i < sObj.length; i++) {
		v = sObj.options[i].value;
		if (v == '') { continue; }
		for (k=0; k < newvals.length; k++) {
			if (newvals[k] == v) {
				cssclass = (flagvalues == 1) ? 'elx5_msel_item elx5_mlflag'+v : 'elx5_msel_item';
				ihtml += '<a href="javascript:void(null);" class="'+cssclass+'" onclick="elx5MultiSelectRemove(\''+elid+'\', \''+v+'\', '+flagvalues+');" title="'+lngremove+'">'+sObj.options[i].text+' <span>x</span></a> ';
				break;
			}
		}
	}

	if (document.getElementById(elid+'_noselitem')) {
		var nov = document.getElementById(elid+'_noselitem').innerHTML;
		ihtml += '<a href="javascript:void(null);" class="elx5_invisible" id="'+elid+'_noselitem">'+nov+'</a>';
	}
	document.getElementById(elid).value = newvals.join();
	document.getElementById(elid+'_items').innerHTML = ihtml;
}

function elx5SwitchSelectOther(selectid) {
	if (!document.getElementById(selectid)) { return; }
	var boxidx = selectid+'_other_box';
	var sObj = document.getElementById(selectid);
	var v = sObj.options[sObj.selectedIndex].value;
	if (v == 'OTHER') {
		document.getElementById(boxidx).className = 'elx5_tsspace';
	} else {
		var otheridx = selectid+'_other';
		document.getElementById(boxidx).className = 'elx5_invisible';
		document.getElementById(otheridx).value = '';
	}
}


function elx5PasswordMeter(elemid) {
	if (!document.getElementById(elemid)) { return; }
	var pass = document.getElementById(elemid).value;
	var score = elx5PasswordScore(pass);
	if (score > 100) { score = 100; }
	score = Math.round(score / 10); //0-10
	var pass = document.getElementById(elemid+'_passmeter').value = score;
}

function elx5PasswordScore(pass) {
	var score = 0;
	if (pass == '') { return score; }
	var letters = new Object();
	for (var i=0; i<pass.length; i++) {
		letters[pass[i]] = (letters[pass[i]] || 0) + 1;
		score += 5.0 / letters[pass[i]];
	}
	var variations = { digits: /\d/.test(pass), lower: /[a-z]/.test(pass), upper: /[A-Z]/.test(pass) };
	variationCount = 0;
	for (var check in variations) { variationCount += (variations[check] == true) ? 1 : 0; }
	score += (variationCount - 1) * 10;
	variations = { nonWords: /\W/.test(pass) };
	variationCount = 0;
	for (var check in variations) { variationCount += (variations[check] == true) ? 1 : 0; }
	score += variationCount * 15;
	return parseInt(score, 10);
}

function elx5PasswordMatch(elemid, passelemid) {
	if (!document.getElementById(elemid)) { return; }
	if (!document.getElementById(passelemid)) { return; }
	if (document.getElementById(elemid).value != document.getElementById(passelemid).value) {
		document.getElementById(elemid).classList.add('elx5_passnomatch'); 
	} else {
		document.getElementById(elemid).classList.remove('elx5_passnomatch'); 
	}
}

function elx5FileImagePreview(elemid) {
	if (!document.getElementById(elemid)) { return; }
	var fileObj = document.getElementById(elemid);
	var imgObj = document.getElementById(elemid+'_image');
	if (fileObj.files && fileObj.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {
			var filename = fileObj.files[0].name;
			var filext = filename.split('.').pop().toLowerCase();
			if ((filext == 'jpg') || (filext == 'jpeg') || (filext == 'png') || (filext == 'gif')) {
				var filesize = Math.round(fileObj.files[0].size / 1024);
				imgObj.setAttribute('src', e.target.result);
				document.getElementById(elemid+'_imagelink').setAttribute('href', e.target.result);
				document.getElementById(elemid+'_imagename').innerHTML = filename;
				document.getElementById(elemid+'_imagename').title = filename+' '+filesize+'KB';
				document.getElementById(elemid+'_deleteold').value = '1';
			} else {
				var empty_image = imgObj.getAttribute('data-empty');
				var noimagelng = imgObj.getAttribute('data-noimglng');
				imgObj.setAttribute('src', empty_image);
				document.getElementById(elemid+'_imagelink').setAttribute('href', empty_image);
				document.getElementById(elemid+'_imagename').innerHTML = noimagelng;
				document.getElementById(elemid+'_imagename').title = noimagelng;
			}
		}
		reader.readAsDataURL(fileObj.files[0]);
	}
}

function elx5FileimgDeleteImage(elemid) {
	if (!document.getElementById(elemid)) { return; }
	var imgObj = document.getElementById(elemid+'_image');
	var empty_image = imgObj.getAttribute('data-empty');
	var noimguplng = imgObj.getAttribute('data-noimguplng');
	imgObj.setAttribute('src', empty_image);
	document.getElementById(elemid+'_imagelink').setAttribute('href', empty_image);
	document.getElementById(elemid+'_imagename').innerHTML = noimguplng;
	document.getElementById(elemid+'_imagename').title = noimguplng;
	document.getElementById(elemid+'_deleteold').value = '1';
	document.getElementById(elemid).value = '';
}

function elx5SwitchPreviewImage(elemid) {
	if (!document.getElementById(elemid)) { return; }
	var sObj = document.getElementById(elemid);

	if (sObj.options[sObj.selectedIndex].hasAttribute('data-image')) {
		var v = sObj.options[sObj.selectedIndex].getAttribute('data-image');
	} else {
		var v = sObj.options[sObj.selectedIndex].value;
	}
	var iObj = document.getElementById(elemid+'_image');
	if (v == '') {
		var iurl = iObj.getAttribute('data-empty');
	} else {
		var iurl = iObj.getAttribute('data-dirurl')+''+v;
	}
	iObj.src = iurl;
	document.getElementById(elemid+'_imagelink').href = iurl;
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
function elx5PrepareUploader(cfg) {
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
				msgBox.className = 'elx5_error elx5_vsspace';
				msgBox.innerHTML = 'Unable to upload file!';
				return;
			}
			if (response.success == 1) {
				if (response.message) {
					msgBox.innerHTML = response.message;
				} else {
					msgBox.innerHTML = 'Upload success!';
				}
				msgBox.className = 'elx5_smsuccess';
			} else {
				if (response.message) {
					msgBox.innerHTML = response.message;
				} else {
					msgBox.innerHTML = 'An error occurred and the upload failed.';
				}
				msgBox.className = 'elx5_error elx5_vsspace';
			}
		},
		onError: function() {
			btn.innerHTML = btn.getAttribute('data-selfile');
			btn.classList.remove('elx5_notallowedbtn');
			progressOuter.style.display = 'none';
			msgBox.className = 'elx5_error elx5_vsspace';
			msgBox.innerHTML = 'Unable to upload file';
		}
	});
}

/* SHOW PARAMS GROUP (TRIGGERD BY FORM ELEMENT) */
function elx5ShowParams(obj, optionsstr, typ) {
	elx5ShowHideParams(obj, optionsstr, typ, 1);
}


/* HIDE PARAMS GROUP (TRIGGERD BY FORM ELEMENT) */
function elx5HideParams(obj, optionsstr, typ) {
	elx5ShowHideParams(obj, optionsstr, typ, 0);
}

/* SHOW OR HIDE PARAMS GROUP (TRIGGERD BY FORM ELEMENT) */
function elx5ShowHideParams(obj, optionsstr, typ, show) {
	if (optionsstr == '') { return false; }
	var selIndex = 0;
	typ = parseInt(typ);
	if (typ == 1) { //select
		selIndex = obj.selectedIndex;
	} else if (typ == 2) { //radio
		var objname = obj.name;
		objname = objname.replace('[', '');
		objname = objname.replace(']', '');
		selIndex = obj.id.replace(objname, '');
		selIndex = parseInt(selIndex);
	} else {
		return false;
	}

	var par_aids = optionsstr.split(';');
	if (par_aids instanceof Array) {
		for (var i in par_aids) {
			var optstr = par_aids[i];
			if (optstr != '') {
				var par_bids = optstr.split(':');
				var curIndex = parseInt(par_bids[0]);
				if (curIndex == selIndex) {
					var trigstr = par_bids[1];
					if (trigstr != '') {
						var par_cids = trigstr.split(',');
						if (par_cids instanceof Array) {
							for (var x in par_cids) {
								var par_cid = 'params_group_'+parseInt(par_cids[x]);
								if (!document.getElementById(par_cid)) {
									continue;
								} else {
									var pObj = document.getElementById(par_cid);
									if (show == 0) {
										pObj.className = 'elx5_invisible';
									} else {
										pObj.className = 'elx5_zero';
									}
								}
							}
						}
					}
				}
			}
		}
	}
}

function elx5SuggestSEO(titleid, seotitleid, idelem, updateid, svbaseurl) {
	if (!document.getElementById(idelem)) { return; }
	if (!document.getElementById(titleid)) { return; }
	if (document.getElementById(updateid+'sug').classList.contains('fa-spin')) { return; }//already clicked
	document.getElementById(updateid).className = 'elx5_invisible';
	var title = document.getElementById(titleid).value;
	if (title == '') {
		document.getElementById(titleid).focus();
		return false;
	}

	var successfunc = function(xreply) {
		document.getElementById(updateid+'sug').className = 'fas fa-cog';
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			document.getElementById(updateid).innerHTML = 'Action failed!';
			document.getElementById(updateid).className = 'elx5_smwarning';
			return;
		}
		if (parseInt(jsonObj.success, 10) < 1) {
			if (jsonObj.message != '') {
				document.getElementById(updateid).innerHTML = jsonObj.message;
			} else {
				document.getElementById(updateid).innerHTML = 'Action failed!';
			}
			document.getElementById(updateid).className = 'elx5_smwarning';
		} else {
			document.getElementById(seotitleid).value = jsonObj.seotitle;
		}
	};

	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		document.getElementById(updateid+'sug').className = 'fas fa-cog';
		document.getElementById(updateid).innerHTML = errorThrown;
		document.getElementById(updateid).className = 'elx5_smwarning';
	}

	let elid = document.getElementById(idelem).value;
	elid = parseInt(elid, 10);
	let rnd = Math.floor((Math.random()*100)+1);
	var edata = { 'elid': elid, 'title': title, 'rnd': rnd };
	var eurl = svbaseurl+'suggest';
	document.getElementById(updateid+'sug').className = 'fas fa-spinner fa-spin';
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

function elx5ValidateSEO(seotitleid, idelem, updateid, svbaseurl) {
	var seotitle = document.getElementById(seotitleid).value;
	if (seotitle == '') {
		document.getElementById(seotitleid).focus();
		return false;
	}
	var successfunc = function(xreply) {
		document.getElementById(updateid+'val').className = 'fas fa-check';
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			document.getElementById(updateid).innerHTML = 'Action failed!';
			document.getElementById(updateid).className = 'elx5_smwarning';
			return;
		}
		if (parseInt(jsonObj.success, 10) < 1) {
			if (jsonObj.message != '') {
				document.getElementById(updateid).innerHTML = jsonObj.message;
			} else {
				document.getElementById(updateid).innerHTML = 'Action failed!';
			}
			document.getElementById(updateid).className = 'elx5_smwarning';
		} else {
			if (jsonObj.message != '') {
				document.getElementById(updateid).innerHTML = jsonObj.message;
			} else {
				document.getElementById(updateid).innerHTML = 'Valid!';
			}
			document.getElementById(updateid).className = 'elx5_smsuccess';
		}
		setTimeout(function(){
			document.getElementById(updateid).className = 'elx5_invisible';
		}, 2000);
	};

	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		document.getElementById(updateid+'val').className = 'fas fa-check';
		document.getElementById(updateid).innerHTML = errorThrown;
		document.getElementById(updateid).className = 'elx5_smwarning';
		setTimeout(function(){
			document.getElementById(updateid).className = 'elx5_invisible';
		}, 2000);
	}

	let elid = document.getElementById(idelem).value;
	elid = parseInt(elid, 10);
	let rnd = Math.floor((Math.random()*100)+1);
	var edata = { 'elid': elid, 'seotitle': seotitle, 'rnd': rnd };
	var eurl = svbaseurl+'validate';
	document.getElementById(updateid+'val').className = 'fas fa-spinner fa-spin';
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

function elx5Submit(pressbutton, formid, taskid) {
	if (!document.getElementById(formid)) { return; }
	if (!document.getElementById(taskid)) { return; }
	var frm = document.getElementById(formid);
	if (typeof frm.reportValidity == 'function') {
		if (!frm.reportValidity()) {
			return false;
		}		
	}
	document.getElementById(taskid).value = pressbutton;
	frm.submit();
}

function elx5CopyToClipboard(elid, showok) {
	if (!document.getElementById(elid)) { return; }
	if ((showok === undefined) || (showok === null) || (showok === '')) { showok = 0; }
	if (window.getSelection) {//clear previous text selections
		if (window.getSelection().empty) {//Chrome
			window.getSelection().empty();
		} else if (window.getSelection().removeAllRanges) {//Firefox
			window.getSelection().removeAllRanges();
		}
	} else if (document.selection) {//IE
		document.selection.empty();
	}
	var obj = document.getElementById(elid);
	if ((obj.tagName == 'INPUT') || (obj.tagName == 'TEXTAREA')) {
		//obj.focus();//needed?
		obj.select();
	} else if (document.selection) {
		var range = document.body.createTextRange();
		range.moveToElementText(obj);
		range.select().createTextRange();
	} else if (window.getSelection) {
		var range = document.createRange();
		range.selectNode(obj);
		window.getSelection().addRange(range);
	} else {
		return false;
	}

	try {
		document.execCommand('copy');
		if (showok == 1) { alert('Copied to clipboard!'); }
	} catch (err) {}
}

function elx5NoExpirePing(pinglink) {
	var edata = {};
	edata.rnd = Math.floor((Math.random()*100)+1);
	var successfunc = function(xreply) {};
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {};
	elxAjax('POST', pinglink, edata, null, null, successfunc, errorfunc);
}
