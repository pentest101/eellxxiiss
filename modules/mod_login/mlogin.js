
var loginwait = 0;

function modlogin(sfx, avatar, displayname, textdir) {
	if (loginwait == 1) { return false; } //prevent double click

	var login_uname = document.getElementById('uname'+sfx).value;
	var login_pword = document.getElementById('pword'+sfx).value;
	var login_token = document.getElementById('modtoken'+sfx).value;
	var login_action = document.getElementById('loginform'+sfx).action;
	if ((login_uname == '') || (login_pword == '')) {
		alert('Please provide login credetials!');
		return false;
	}

	var rnd = Math.floor((Math.random()*100)+1);
	var edata = { 'uname': login_uname, 'pword': login_pword, 'modtoken': login_token, 'rnd': rnd };

	var successfunc = function(xreply) {
		loginwait = 0;
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
			if (displayname == 2) {
				var username_txt = jsonObj.uname;
			} else if (jsonObj.firstname != '') {
				var username_txt = jsonObj.firstname+' '+jsonObj.lastname;
			} else if (jsonObj.email != '') {
				var username_txt = jsonObj.email;
			} else {
				var username_txt = jsonObj.uname;
			}

			var logout_txt = document.getElementById('mlogin_logout'+sfx).innerHTML;
			var profile_link = document.getElementById('mlogin_profile'+sfx).innerHTML;
			var logout_redir = '';
			if (document.getElementById('mlogout_redir'+sfx)) {
				logout_redir = document.getElementById('mlogout_redir'+sfx).innerHTML;
			}

			var wrapObj = document.getElementById('modlogin_wrapper'+sfx);
			wrapObj.innerHTML = '';
			if (avatar == 1) {
				var avObj = document.createElement('DIV');
				avObj.className = 'elx5_modlogin_avatarbox';
				avObj.innerHTML = '<a href="'+profile_link+'" title="'+jsonObj.uname+'"><img src="'+jsonObj.avatar+'" alt="avatar" /></a>';
				wrapObj.appendChild(avObj);
				var infoObj = document.createElement('DIV');
				infoObj.className = 'elx5_modlogin_mainbox';
			} else {
				var infoObj = document.createElement('DIV');
				infoObj.className = 'elx5_zero';
			}

			var profObj = document.createElement('A');
			var profText = document.createTextNode(username_txt);
			profObj.appendChild(profText);
			profObj.title = jsonObj.uname;
			profObj.className = 'elx5_modlogin_profile';
			profObj.href = profile_link;
			infoObj.appendChild(profObj);

			var logoutlink = login_action.replace('ilogin', 'ilogout');
			var outObj = document.createElement('A');
			var outText = document.createTextNode(logout_txt);
			outObj.appendChild(outText);
			outObj.title = logout_txt;
			outObj.className = 'elx5_modlogin_logout';
			outObj.href = 'javascript:void(null);';
			outObj.onclick = function(){ modlogout(logoutlink, logout_redir); };
			infoObj.appendChild(outObj);
			wrapObj.appendChild(infoObj);
		}
	};
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		loginwait = 0;
		alert('Login failed with error message '+errorThrown);
	};
	loginwait = 1;
	elxAjax('POST', login_action, edata, null, null, successfunc, errorfunc);
}


function modlogout(logoutlink, redirect_link) {
	if (loginwait == 1) { return false; } //prevent double click
	var rnd = Math.floor((Math.random()*100)+1);
	var edata = { 'rnd': rnd };
	var successfunc = function(xreply) {
		loginwait = 0;
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
			if (redirect_link == '') {
				location.reload();
			} else {
				window.location.href = redirect_link;
			}
		}
	};
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		loginwait = 0;
		alert('Logout failed with error message '+errorThrown);
	};
	loginwait = 1;
	elxAjax('POST', logoutlink, edata, null, null, successfunc, errorfunc);
}
