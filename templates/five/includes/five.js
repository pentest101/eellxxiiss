/** 
Template Five by Is Open Source
Author: Ioannis Sannos
https://www.isopensource.com
*/

var tpl5loginwait = 0;

function tpl5SwitchLang() {
	if (!document.getElementById('tpl5langfm')) { return; }
	var fmObj = document.getElementById('tpl5langfm');
	var selObj = document.getElementById('tpl5_select_lang');
	var tpl5URL = selObj.options[selObj.selectedIndex].getAttribute('data-act');
	window.location.href = tpl5URL;
}

function tpl5ScrollTop() {
	document.body.scrollTop = 0;
	document.documentElement.scrollTop = 0;
}

function tpl5OnLoad() {
	document.getElementById('tpl5_to_top').style.display = 'none';
	var tpl5StickyOffset = document.getElementById('tpl5_header_menu_line').offsetTop;
	var tpl5Height = document.getElementById('tpl5_header_all').offsetHeight;
	tpl5Height = parseInt(tpl5Height, 10);
	if (tpl5Height > 100) {
		var tpl5FixedLimit = tpl5Height;
	} else {
		var tpl5Width = document.getElementById('tpl5_header_menu_line').offsetWidth;
		if (tpl5Width < 651) {
			var tpl5FixedLimit = 160;
		} else {
			var tpl5FixedLimit = 300;
		}
	}

	window.onscroll = function() {
		var sticky = document.getElementById('tpl5_header_menu_line');
		if (window.pageYOffset !== undefined) {
			var scroll = window.pageYOffset;
		} else if ((document.compatMode || '') === 'CSS1Compat') {
			var scroll = document.documentElement.scrollTop;
		} else {
			var scroll = document.body.scrollTop;
		}
		if (scroll >= tpl5FixedLimit) {
			sticky.classList.remove('tpl5_fixedmenu');
			sticky.classList.add('tpl5_fixedmenudark');
		} else if (scroll >= tpl5StickyOffset) {
			sticky.classList.remove('tpl5_fixedmenudark');
			sticky.classList.add('tpl5_fixedmenu');
		} else {
			sticky.classList.remove('tpl5_fixedmenu');
			sticky.classList.remove('tpl5_fixedmenudark');
		}
		if (scroll > 500) {
			document.getElementById('tpl5_to_top').style.display = 'block';
		} else {
			document.getElementById('tpl5_to_top').style.display = 'none';
		}
	};
}

function tpl5OpenMenu() {
	document.getElementById('tpl5_menu').style.width = '100%';
	document.getElementById('tpl5_maincontainer').style.display = 'none';
	document.getElementById('tpl5_footer').style.display = 'none';
}

function tpl5CloseMenu() {
	document.getElementById('tpl5_maincontainer').style.display = 'block';
	document.getElementById('tpl5_footer').style.display = 'block';
	document.getElementById('tpl5_menu').style.width = '0px';
}

function tpl5Login() {
	if (tpl5loginwait == 1) { return false; } //prevent double click
	var login_uname = document.getElementById('tploguname').value;
	var login_pword = document.getElementById('tplogpword').value;
	var login_token = document.getElementById('tplogmodtoken').value;
	var login_action = document.getElementById('tpl5loginform').action;
	if ((login_uname == '') || (login_pword == '')) {
		elx5ModalMessageShow('tplog', 'Please provide login credetials!', 'elx5_error');
		return false;
	}

	elx5ModalMessageHide('tplog');
	var rnd = Math.floor((Math.random()*100)+1);
	var edata = { 'uname': login_uname, 'pword': login_pword, 'modtoken': login_token, 'rnd': rnd };

	var successfunc = function(xreply) {
		tpl5loginwait = 0;
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			document.getElementById('tplogsublogin').innerHTML = document.getElementById('tplogsublogin').getAttribute('data-loginlng');
			return false;
		}
		if (parseInt(jsonObj.success, 10) < 1) {
			document.getElementById('tplogsublogin').innerHTML = document.getElementById('tplogsublogin').getAttribute('data-loginlng');
			if (jsonObj.errormsg != '') {
				elx5ModalMessageShow('tplog', jsonObj.errormsg, 'elx5_error');
			} else {
				elx5ModalMessageShow('tplog', 'Action failed!', 'elx5_error');
			}
			return false;
		} else {
			location.reload();
		}
	};
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		tpl5loginwait = 0;
		document.getElementById('tplogsublogin').innerHTML = document.getElementById('tplogsublogin').getAttribute('data-loginlng');
		elx5ModalMessageShow('tplog', 'Login failed with error message '+errorThrown, 'elx5_error');
	};
	tpl5loginwait = 1;
	document.getElementById('tplogsublogin').innerHTML = document.getElementById('tplogsublogin').getAttribute('data-waitlng');
	elxAjax('POST', login_action, edata, null, null, successfunc, errorfunc);
}

function tpl5Logout(logoutlink, redirect_link) {
	if (tpl5loginwait == 1) { return false; } //prevent double click
	var rnd = Math.floor((Math.random()*100)+1);
	var edata = { 'rnd': rnd };
	var successfunc = function(xreply) {
		tpl5loginwait = 0;
		try {
			var jsonObj = JSON.parse(xreply);
		} catch(e) {
			return false;
		}
		if (parseInt(jsonObj.success, 10) < 1) {
			if (jsonObj.errormsg != '') {
				alert(jsonObj.errormsg);
			} else {
				alert('Action failed!');
			}
		} else {
			window.location.href = redirect_link;
		}
	};
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		tpl5loginwait = 0;
		alert('Logout failed with error message '+errorThrown);
	};
	tpl5loginwait = 1;
	elxAjax('POST', logoutlink, edata, null, null, successfunc, errorfunc);
}
