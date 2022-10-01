function modAMenuToggleSubmenu(idx) {
	if (!document.getElementById('amenu_submenu'+idx)) { return; }
	var smObj = document.getElementById('amenu_submenu'+idx);
	if (smObj.className == 'amenu_submenu amenu_submenuexp') {
		smObj.className = 'amenu_submenu';
		document.getElementById('amenu_title_'+idx).className = 'amenu_title amenu_down';
	} else {
		var uls = document.querySelectorAll('ul.amenu_submenu.amenu_submenuexp');
		if (uls.length > 0) {//Collapse other opened submenus
			for (var c=0; c < uls.length; c++) {
				var smenuname = uls[c].id.replace('amenu_submenu', '');
				uls[c].className = 'amenu_submenu';
				document.getElementById('amenu_title_'+smenuname).className = 'amenu_title amenu_down';
			}
			setTimeout(function() {
				smObj.className = 'amenu_submenu amenu_submenuexp';
				document.getElementById('amenu_title_'+idx).className = 'amenu_title amenu_up';
			}, 500);
		} else {
			smObj.className = 'amenu_submenu amenu_submenuexp';
			document.getElementById('amenu_title_'+idx).className = 'amenu_title amenu_up';
		}
	}

	if (document.getElementById('onyx_sidescroll')) {//needed on screens with small height
		if (typeof SimpleBar != 'undefined') {
			const el = new SimpleBar(document.getElementById('onyx_sidescroll'));
			//el.forceVisible = 'x';
			el.recalculate();
		}
	}
}
