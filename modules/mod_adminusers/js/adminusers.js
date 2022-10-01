function modAUsersUpdate(pg, srt) {
	var tblObj = document.getElementById('modadmusers');
	let page = tblObj.getAttribute('data-page');
	page = parseInt(page, 10);
	if (page < 0) { page = 1; }
	let maxpage = tblObj.getAttribute('data-maxpage');
	maxpage = parseInt(maxpage, 10);
	let cursort = tblObj.getAttribute('data-sort');
	if (cursort == '') { cursort = 'timeasc'; }

	var userData = {};
	userData.page = page;
	userData.sort = cursort;

	pg = parseInt(pg, 10);
	if (pg == -1) {//prev page
		if (page == 1) { return; }
		userData.page = page - 1;
	} else if (pg == 1) {//next page
		if (page >= maxpage) { return; }
		userData.page = page + 1;
	} else {//re-sort
		userData.page = 1;
		if (srt == '') { return; }
		if (srt == 'user') {
			if (cursort == 'userasc') {
				userData.sort = 'userdesc';
			} else {
				userData.sort = 'userasc';
			}
		}
		if (srt == 'time') {
			if (cursort == 'timeasc') {
				userData.sort = 'timedesc';
			} else {
				userData.sort = 'timeasc';
			}
		}
		if (srt == 'clicks') {
			if (cursort == 'clicksasc') {
				userData.sort = 'clicksdesc';
			} else {
				userData.sort = 'clicksasc';
			}
		}
	}

	modAUsersLoad(userData.page, userData.sort, tblObj);
}


function modAUsersLoad(page, sort, tblObj) {
	var edata = {};
	edata.page = page;
	edata.sort = sort;
	edata.format = 'json';
	edata.rnd = Math.floor((Math.random()*100)+1);
	edata.f = 'modules/mod_adminusers/req.php';

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
			tblObj.setAttribute('data-page', jsonObj.page);
			tblObj.setAttribute('data-maxpage', jsonObj.maxpage);
			tblObj.setAttribute('data-sort', jsonObj.sort);

			document.getElementById('modAUsersTHuser').classList.remove('elx5_nosorting', 'elx5_sorting_asc', 'elx5_sorting_desc');
			document.getElementById('modAUsersTHutime').classList.remove('elx5_nosorting', 'elx5_sorting_asc', 'elx5_sorting_desc');
			document.getElementById('modAUsersTHclicks').classList.remove('elx5_nosorting', 'elx5_sorting_asc', 'elx5_sorting_desc');
			switch (jsonObj.sort) {
				case 'userasc':
					document.getElementById('modAUsersTHuser').classList.add('elx5_sorting_asc');
					document.getElementById('modAUsersTHutime').classList.add('elx5_nosorting');
					document.getElementById('modAUsersTHclicks').classList.add('elx5_nosorting');
				break;
				case 'userdesc':
					document.getElementById('modAUsersTHuser').classList.add('elx5_sorting_desc');
					document.getElementById('modAUsersTHutime').classList.add('elx5_nosorting');
					document.getElementById('modAUsersTHclicks').classList.add('elx5_nosorting');
				break;
				case 'clicksasc':
					document.getElementById('modAUsersTHuser').classList.add('elx5_nosorting');
					document.getElementById('modAUsersTHutime').classList.add('elx5_nosorting');
					document.getElementById('modAUsersTHclicks').classList.add('elx5_sorting_asc');
				break;
				case 'clicksdesc':
					document.getElementById('modAUsersTHuser').classList.add('elx5_nosorting');
					document.getElementById('modAUsersTHutime').classList.add('elx5_nosorting');
					document.getElementById('modAUsersTHclicks').classList.add('elx5_sorting_desc');
				break;
				case 'timedesc':
					document.getElementById('modAUsersTHuser').classList.add('elx5_nosorting');
					document.getElementById('modAUsersTHutime').classList.add('elx5_sorting_desc');
					document.getElementById('modAUsersTHclicks').classList.add('elx5_nosorting');
				break;
				case 'timeasc': default: 
					document.getElementById('modAUsersTHuser').classList.add('elx5_nosorting');
					document.getElementById('modAUsersTHutime').classList.add('elx5_sorting_asc');
					document.getElementById('modAUsersTHclicks').classList.add('elx5_nosorting');
				break;
			}

			var rows = tblObj.getElementsByTagName('tr');
			if (rows.length > 1) {
				for (var i = rows.length - 1; i > 0; i--) { tblObj.deleteRow(i); }
			}

			document.getElementById('modAUsersTotal').innerHTML = '('+jsonObj.total+')';
			document.getElementById('modAUsersPageSum').innerHTML = jsonObj.lngpageof;

			if (jsonObj.visitors.length > 0) {
				var tbodObj = document.getElementById('modausers_tbody');
				var txt = '', tip = '';
				for (var q=0; q < jsonObj.visitors.length; q++) {
					var visObj = jsonObj.visitors[q];

					var trObj = document.createElement('tr');
					tip = '';
					if (visObj.ip_address != '') { tip += 'IP: '+visObj.ip_address+"\n"; }
					if (visObj.os != '') { tip += jsonObj.lngos+': '+visObj.os+"\n"; }
					if (visObj.gid != 7) { tip += jsonObj.lngauth+': '+visObj.login_method+"\n"; }
					if (visObj.device != '') { tip += 'Device: '+visObj.device+"\n"; }
					if (visObj.browser != '') { tip += jsonObj.lngbrowser+': '+visObj.browser+"\n"; }
					if (visObj.current_page != '') { tip += jsonObj.lngpage+': '+visObj.current_page+"\n"; }
					tip += jsonObj.lngonlinetime+': '+visObj.time_online+"\n";
					tip += jsonObj.lngidletime+': '+visObj.time_idle+"\n";
					tip += jsonObj.lngclicks+': '+visObj.clicks;

					txt = '<div title="'+tip+'">';
					if (visObj.deviceicon != '') { txt += '<i class="'+visObj.deviceicon+'"></i> '; }
					txt += visObj.visitorname;
					if (visObj.gid != 7) {
						if (visObj.browser != '') {
							txt += '<div class="elx5_smallnote">'+visObj.browser+'<span class="elx5_lmobhide"> | '+visObj.groupname+'</span></div>';
						} else {
							txt += '<div class="elx5_smallnote">'+visObj.groupname+'</div>';
						}
					} else {
						if ((visObj.browser != '') && (visObj.ip_address != '')) {
							txt += '<div class="elx5_smallnote">'+visObj.browser+'<span class="elx5_lmobhide"> | '+visObj.ip_address+'</span></div>';
						} else if (visObj.browser != '') {
							txt += '<div class="elx5_smallnote">'+visObj.browser+'</div>';
						} else if (visObj.ip_address != '') {
							txt += '<div class="elx5_smallnote">'+visObj.ip_address+'</div>';
						}
					}
					txt += '</div>';

					var tdObj = document.createElement('td');
					tdObj.innerHTML = txt;
					trObj.appendChild(tdObj);

					var td2Obj = document.createElement('td');
					td2Obj.className = 'elx5_center elx5_lmobhide';
					td2Obj.innerHTML = visObj.time_idle;
					trObj.appendChild(td2Obj);

					var td3Obj = document.createElement('td');
					td3Obj.className = 'elx5_center elx5_tabhide';
					td3Obj.innerHTML = visObj.clicks;
					trObj.appendChild(td3Obj);

					if (visObj.canlogout == 1) {
						txt = '<a href="javascript:void(null);" class="elx5_dataaction elx5_datawarn" onclick="modAUsersLogout('+visObj.uid+', '+visObj.gid+', \''+visObj.login_method+'\', \''+visObj.ip_address+'\', \''+visObj.first_activity+'\');" title="'+jsonObj.lngflogout+'"><i class="fas fa-power-off"></i></a>';
					} else {
						txt = '<a href="javascript:void(null);" class="elx5_dataaction elx5_datanotallowed"><i class="fas fa-power-off"></i></a>';
					}
					if (visObj.canban == 1) {
						txt += ' <a href="javascript:void(null);" class="elx5_dataaction elx5_datawarn" onclick="modAUsersBanIP(\''+visObj.ip_address+'\');" title="'+jsonObj.lngbanip+'"><i class="fas fa-user-slash"></i></a>';
					} else {
						txt += ' <a href="javascript:void(null);" class="elx5_dataaction elx5_datanotallowed"><i class="fas fa-user-slash"></i></a>';
					}
					var td4Obj = document.createElement('td');
					td4Obj.className = 'elx5_center';
					td4Obj.innerHTML = txt;
					trObj.appendChild(td4Obj);

					tbodObj.appendChild(trObj);
				}
			} else {
				var tbodObj = document.getElementById('modausers_tbody');
				var trObj = document.createElement('tr');
				trObj.id = 'datarow0';
				trObj.className = 'elx5_rowwarn';
				var tdObj = document.createElement('td');
				tdObj.className = 'elx5_center';
				tdObj.setAttribute('colspan', '4');
				tdObj.appendChild(jsonObj.lngnores);
				trObj.appendChild(tdObj);
				tbodObj.appendChild(trObj);
			}
		}
	};

	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader();
		alert('Action failed! '+errorThrown);
	};

	var eurl = document.getElementById('modadmusers').getAttribute('data-inpage')+'ajax';
	elx5StartPageLoader();
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}


function modAUsersBanIP(uip) {
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
			var tblObj = document.getElementById('modadmusers');
			let page = tblObj.getAttribute('data-page');
			page = parseInt(page, 10);
			if (page < 0) { page = 1; }
			let cursort = tblObj.getAttribute('data-sort');
			if (cursort == '') { cursort = 'timeasc'; }
			modAUsersLoad(page, cursort, tblObj);
		}
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader();
		alert('Error! '+errorThrown);
	}
	var eurl = document.getElementById('modadmusers').getAttribute('data-inpage')+'banip';
	var rnd = Math.floor((Math.random()*100)+1);
	var edata = { 'ip':uip, 'rnd':rnd };
	elx5StartPageLoader();
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}


function modAUsersLogout(uid, gid, lmethod, uip, fact) {
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
			var tblObj = document.getElementById('modadmusers');
			let page = tblObj.getAttribute('data-page');
			page = parseInt(page, 10);
			if (page < 0) { page = 1; }
			let cursort = tblObj.getAttribute('data-sort');
			if (cursort == '') { cursort = 'timeasc'; }
			modAUsersLoad(page, cursort, tblObj);
		}
	}
	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader();
		alert('Error! '+errorThrown);
	}
	var eurl = document.getElementById('modadmusers').getAttribute('data-inpage')+'forcelogout';
	var rnd = Math.floor((Math.random()*100)+1);
	var edata = { 'uid': uid, 'gid': gid, 'lmethod': lmethod, 'ip': uip, 'fact': fact, 'rnd':rnd };
	elx5StartPageLoader();
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}
