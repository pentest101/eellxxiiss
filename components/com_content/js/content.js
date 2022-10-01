/**
* @package		Elxis
* @copyright	Copyright (c) 2006-2019 elxis.org (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

/* POST COMMENT */
function elx5PostComment() {
	var successfunc = function(xreply) {
		elx5StopPageLoader('pgloadcomments');
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			document.getElementById('postcommentreply').innerHTML = e.message;
			document.getElementById('postcommentreply').className = 'elx5_warning';
			return false;
		}

		if (parseInt(jsonObj.success, 10) == 1) {
			document.getElementById('pcommessage').value = '';
			if (parseInt(jsonObj.waitapproval, 10) == 1) {
				if (jsonObj.message != '') {
					document.getElementById('postcommentreply').innerHTML = jsonObj.message;
				} else {
					document.getElementById('postcommentreply').innerHTML = 'Thanks for your comment. It will be published after approval.';
				}
				document.getElementById('postcommentreply').className = 'elx5_success';
			} else {
				document.getElementById('postcommentreply').className = 'elx5_invisible';

				let liObj = document.createElement('li');
				liObj.id = 'elx_comment_'+jsonObj.comid;

				let avObj = document.createElement('div');
				avObj.className = 'elx5_comment_avatar';
				avObj.innerHTML = '<img src="'+jsonObj.avatar+'" alt="'+jsonObj.author+'" title="'+jsonObj.author+'" />';
				liObj.appendChild(avObj);

				let topObj = document.createElement('div');
				topObj.className = 'elx5_comment_top';
				let auObj = document.createElement('div');
				auObj.className = 'elx5_comment_author';
				let ntext = document.createTextNode(jsonObj.author);
				auObj.appendChild(ntext);
				topObj.appendChild(auObj);
				let tiObj = document.createElement('time');
				tiObj.className = 'elx5_comment_date';
				ntext = document.createTextNode(jsonObj.created);
				tiObj.appendChild(ntext);
				topObj.appendChild(tiObj);

				let mObj = document.createElement('div');
				mObj.className = 'elx5_comment_message';
				mObj.id = 'elx_comment_message_'+jsonObj.comid;
				mObj.innerHTML = jsonObj.commessage;

				let mainObj = document.createElement('div');
				mainObj.className = 'elx5_comment_main';
				mainObj.appendChild(topObj);
				mainObj.appendChild(mObj);

				if ((jsonObj.canmail == 1) || (jsonObj.candel == 1) || ((jsonObj.canpub == 1) && (jsonObj.published == 0))) {
					let actObj = document.createElement('div');
					actObj.className = 'elx5_comment_actions';
					let acthtml = '';
					if (jsonObj.canmail == 1) {
						acthtml += '<a href="mailto:'+jsonObj.email+'" title="'+jsonObj.email+'"><i class="fas fa-envelope"></i><span class="elx5_mobhide"> e-mail</span></a>';
					}
					if ((jsonObj.canpub == 1) && (jsonObj.published == 0)) {
						acthtml += '<a href="javascript:void(null);" id="elx_comment_publish_'+jsonObj.comid+'" onclick="elx5PublishComment('+jsonObj.comid+');" title="'+jsonObj.lngpublish+'">';
						acthtml += '<i class="fas fa-check-square"></i><span class="elx5_mobhide"> '+jsonObj.lngpublish+'</span></a>';
					}
					if (jsonObj.candel == 1)  {
						acthtml += '<a href="javascript:void(null);" onclick="elx5DeleteComment('+jsonObj.comid+');" title="'+jsonObj.lngdelete+'">';
						acthtml += '<i class="fas fa-trash"></i><span class="elx5_mobhide"> '+jsonObj.lngdelete+'</span></a>';
					}
					actObj.innerHTML = acthtml;
					mainObj.appendChild(actObj);
				}
				liObj.appendChild(mainObj);
				document.getElementById('elx_comments_list').appendChild(liObj);

				if (document.getElementById('pcomcomseccode')) {
					document.getElementById('pcomcomseccode').value = '';
					if (document.getElementById('pcomcomseccodebox')) {//no robot
						document.getElementById('pcomcomseccodebox').className = 'elxnorobotbox';
						document.getElementById('pcomcomseccodebox').innerHTML = '&#160;';
					}
				}
				if (document.getElementById('elx_comment_0')) {
					var li0Obj = document.getElementById('elx_comment_0');
					document.getElementById('elx_comments_list').removeChild(li0Obj);
				}
			}
		} else {//error
			if (jsonObj.message != '') {
				document.getElementById('postcommentreply').innerHTML = jsonObj.message;
			} else {
				document.getElementById('postcommentreply').innerHTML = 'Action failed!';
			}
			document.getElementById('postcommentreply').className = 'elx5_warning';
		}
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader('pgloadcomments');
		document.getElementById('postcommentreply').innerHTML = 'Error! '+errorThrown; 
		document.getElementById('postcommentreply').className = 'elx5_warning';
	}

	var edata = {};
	edata.id = parseInt(document.getElementById('pcomid').value, 10);
	if (isNaN(edata.id) || (edata.id < 1)) { return false; }
	if (document.getElementById('pcomauthor')) {
		edata.author = document.getElementById('pcomauthor').value;
		edata.author = edata.author.replace(/['"]/g,'');
	}
	if (document.getElementById('pcomemail')) {
		edata.email = document.getElementById('pcomemail').value;
		edata.email = edata.email.replace(/['"]/g,'');
	}
	edata.message = document.getElementById('pcommessage').value;
	edata.message = edata.message.replace(/['"]/g,'');
	if (edata.message == '') {
		document.getElementById('pcommessage').focus();
		return false;
	}
	edata.act = 'postcomment';
	edata.comseccode = document.getElementById('pcomcomseccode').value;
	edata.token = document.getElementById('pcomtoken').value;
	edata.rnd = Math.floor((Math.random()*100)+1);

	document.getElementById('postcommentreply').className = 'elx5_invisible';

	var eurl = document.getElementById('fmpostcomment').action;
	elx5StartPageLoader('pgloadcomments');
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}


/* DELETE COMMENT */
function elx5DeleteComment(id) {
	if (isNaN(id)) { return false; }
	id = parseInt(id, 10);
	if (id < 1) { return false; }

	var ulObj = document.getElementById('elx_comments_list');
	var prompttxt = ulObj.getAttribute('data-lngsure');

	var successfunc = function(xreply) {
		elx5StopPageLoader('pgloadcomments');
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
			if (document.getElementById('elx_comment_'+id)) {
				let liObj = document.getElementById('elx_comment_'+id);
				ulObj.removeChild(liObj);
			}
			var lis = ulObj.querySelectorAll('li');
			let addli0 = false;
			if (!lis) {
				addli0 = true;
			} else if (lis.length == 0) {
				addli0 = true;
			}
			if (addli0) {
				let li0Obj = document.createElement('li');
				li0Obj.id = 'elx_comment_0';
				li0Obj.className = 'elx5_nocomments';
				let ntext = document.createTextNode(ulObj.getAttribute('data-lngnocomments'));
				li0Obj.appendChild(ntext);
				ulObj.appendChild(li0Obj);
			}
		}
	};
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader('pgloadcomments');
		alert('Action failed! '+errorThrown);
	};

	if (confirm(prompttxt)) {
		let rnd = Math.floor((Math.random()*100)+1);
		var edata = {'id': id, 'act': 'delcomment', 'rnd': rnd };
		var eurl = ulObj.getAttribute('data-tools');
		elx5StartPageLoader('pgloadcomments');
		elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
	}
}


/* PUBLISH COMMENT (AJAX) */
function elx5PublishComment(id) {
	if (isNaN(id)) { return false; }
	id = parseInt(id, 10);
	if (id < 1) { return false; }

	var ulObj = document.getElementById('elx_comments_list');

	var successfunc = function(xreply) {
		elx5StopPageLoader('pgloadcomments');
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
			document.getElementById('elx_comment_message_'+id).className  = 'elx5_comment_message';
			document.getElementById('elx_comment_publish_'+id).innerHTML = '';
			document.getElementById('elx_comment_publish_'+id).className = 'elx5_invisible';
			document.getElementById('elx_comment_publish_'+id).style.margin = '0px';
			document.getElementById('elx_comment_publish_'+id).style.padding = '0px';
		}
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader('pgloadcomments');
		alert('Action failed! '+errorThrown);
	}

	let rnd = Math.floor((Math.random()*100)+1);
	var edata = {'id': id, 'act': 'pubcomment', 'rnd': rnd };
	var eurl = ulObj.getAttribute('data-tools');
	elx5StartPageLoader('pgloadcomments');
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}


/* 
HIGHLIGHT WORDS 
Original JavaScript code by Chirp Internet: www.chirp.com.au
*/
function textHighlight(id) {
	var targetNode = document.getElementById(id) || document.body;
	var skipTags = new RegExp("^(?:EM|SCRIPT|FORM|SPAN)$");
	var colors = ["#ff6", "#a0ffff", "#9f9", "#f99", "#f6f"];
	var wordColor = [];
	var colorIdx = 0;
	var matchRegex = "";
	this.setRegex = function(input) {
		input = input.replace(/^\\u([^\w]+|[^\w])+$/g, "").replace(/\\u([^\w'-])+/g, "|");
		matchRegex = new RegExp("(" + input + ")","i");
	}

	this.getRegex = function() {
		return matchRegex.toString().replace(/^\/\\b\(|\)\\b\/i$/g, "").replace(/\|/g, " ");
	}

	this.hiliteWords = function(node) {
		if ((typeof node == 'undefined') || !node) { return; }
		if (!matchRegex) return;
		if (skipTags.test(node.nodeName)) return;
		if (node.hasChildNodes()) {
			for(var i=0; i < node.childNodes.length; i++) { this.hiliteWords(node.childNodes[i]); }
		}
		if(node.nodeType == 3) {
			if((nv = node.nodeValue) && (regs = matchRegex.exec(nv))) {
				if(!wordColor[regs[0].toLowerCase()]) { wordColor[regs[0].toLowerCase()] = colors[colorIdx++ % colors.length]; }
				var match = document.createElement('EM');
        		match.appendChild(document.createTextNode(regs[0]));
        		match.style.backgroundColor = wordColor[regs[0].toLowerCase()];
        		match.style.fontStyle = "inherit";
        		match.style.color = "#000";
        		var after = node.splitText(regs.index);
        		after.nodeValue = after.nodeValue.substring(regs[0].length);
        		node.parentNode.insertBefore(match, after);
			}
		}
	};

	this.remove = function() {
		var arr = document.getElementsByTagName('EM');
		while(arr.length && (el = arr[0])) { el.parentNode.replaceChild(el.firstChild, el); }
	};

	this.apply = function(input) {
		if ((typeof input == 'undefined') || !input) { return; }
    	this.remove();
    	this.setRegex(input);
    	this.hiliteWords(targetNode);
	};
}
