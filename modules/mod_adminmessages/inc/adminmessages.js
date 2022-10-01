/**
Module Admin messages
Written by Ioannis Sannos for Elxis CMS
http://www.elxis.org
Last updated: Thirsday, 25 March 2019
*/

function aMsgsReply(toid, threadid) {
	if (isNaN(toid)) { return; }
	toid = parseInt(toid, 10);
	if (toid < 1) { return; }
	threadid = parseInt(threadid, 10);
	if (threadid < 1) { return; }
	var sObj = document.getElementById('amsgsftoid');
	let v = 0, idx = 0;
	for (var i = 0; i < sObj.length; i++) {
		v = sObj.options[i].value;
		if (v == toid) { idx = i; break; }
	}
	sObj.selectedIndex = idx;
	document.getElementById('amsgsfthread').value = threadid;
	document.getElementById('amsgsfmessage').focus();
}

function aMsgsDeleteThread(threadid) {
	if (isNaN(threadid)) { return; }
	threadid = parseInt(threadid, 10);
	if (threadid < 1) { return; }
	var rnd = Math.floor((Math.random()*100)+1);
	var eurl = document.getElementById('amsgsform').action;
	var edata = { 'f': 'modules/mod_adminmessages/inc/rq.php', 'task': 'delete', 'id': threadid, 'rnd': rnd };
	var successfunc = function(xreply) {
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
			var ulObj = document.getElementById('amsgs_messages');
			var listItems = ulObj.getElementsByTagName('li');
			for (var i = listItems.length; i > 0; i--) {
				var k = i - 1;
				if (!listItems[k].hasAttribute('data-thread')) { continue; }
				if (listItems[k].getAttribute('data-thread') == threadid) {
					ulObj.removeChild(listItems[k]);
				}
			}
			var len = ulObj.getElementsByTagName('li').length;
			if (len <= 1) {
				if (document.getElementById('amsgs_message0')) { document.getElementById('amsgs_message0').className = 'amsgs_nomessages'; }
				document.getElementById('amsgsMarkLink').className = 'amsgs_icon';
				document.getElementById('amsgsMarkNumber').className = 'amsgs_nomark';
				document.getElementById('amsgsMarkNumber').innerHTML = '0';
			} else {
				var nlen = len - 1;
				document.getElementById('amsgsMarkNumber').innerHTML = nlen;
			}
		}
	};
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		alert('Error! '+errorThrown);
	};
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

var amsgswait = 0;

function aMsgsSendMessage() {
	if (amsgswait == 1) { return; }

	var sObj = document.getElementById('amsgsftoid');
	var edata = {};
	edata.toid = sObj.options[sObj.selectedIndex].value;
	edata.toid = parseInt(edata.toid, 10);
	edata.replyto = parseInt(document.getElementById('amsgsfthread').value, 10);
	if ((edata.toid == 0) || (edata.toid < -1)) {
		elx5ModalMessageShow('uc', 'Select a recipient!', 'elx5_error');
		alert('Select a recipient!');
		return;
	}

	edata.msg = document.getElementById('amsgsfmessage').value;
	if (edata.msg == '') {
		document.getElementById('amsgsfmessage').focus();
		return;
	}

	var successfunc = function(xreply) {
		amsgswait = 0;
		document.getElementById('amsgsfsendmsg').innerHTML = document.getElementById('amsgsfsendmsg').getAttribute('data-sendlng');
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			alert('Could not send message');
			return false;
		}
		if (parseInt(jsonObj.success, 10) < 1) {
			if (jsonObj.message != '') {
				alert(jsonObj.message);
			} else {
				alert('Action failed!');
			}
		} else {
			document.getElementById('amsgsftoid').selectedIndex = 0;
			document.getElementById('amsgsfmessage').value = '';
			elx5ModalClose('amsgs');
		}
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		amsgswait = 0;
		document.getElementById('amsgsfsendmsg').innerHTML = document.getElementById('amsgsfsendmsg').getAttribute('data-sendlng');
		alert('Error! '+errorThrown);
	}

	document.getElementById('amsgsfsendmsg').innerHTML = document.getElementById('amsgsfsendmsg').getAttribute('data-waitlng');
	edata.f = 'modules/mod_adminmessages/inc/rq.php';
	edata.task = 'send';
	edata.rnd = Math.floor((Math.random()*100)+1);
	var eurl = document.getElementById('amsgsform').action;
	amsgswait = 1;
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}
