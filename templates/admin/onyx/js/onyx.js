function onyxIsRTL() {
	var hObj = document.getElementsByTagName('html');
	if (!hObj) { return false; }
	if (!hObj[0].hasAttribute('dir')) { return false; } 
	if (hObj[0].getAttribute('dir') == 'rtl') { return true; }
	return false;
}

function onyxToggleSidebar() {
	var logObj = document.getElementById('onyx_cplogo');
	var ismobile = false;
	if (window.innerWidth < 768) { ismobile = true; }

	if (ismobile == false) {
		if ((logObj.style.width != '48px') && (logObj.style.width != '230px') && (logObj.style.width != '0px') && (logObj.style.width != '0') && (logObj.style.width != '')) { return; } /* not yet fully opened/closed */
	}

	var sdObj = document.getElementById('onyx_sidenav');
	var menustatus = sdObj.getAttribute('data-status');//PC: closed(special)/mini/open, Mobile: closed/fullopen
	if (ismobile == true) {
		if (logObj.style.width == '') {
			if (menustatus == 'open') { menustatus = 'closed'; }//fix for 1st time click on mobiles
		}
	}
	var smObj = document.getElementById('amenu_menu');
	var uls = document.querySelectorAll('ul.amenu_submenu.amenu_submenuexp');


	if (menustatus == 'mini') {//48px
		var newmenustatus = ismobile ? 'fullopen' : 'open';
	} else if (menustatus == 'open') {//230px
		var newmenustatus = ismobile ? 'closed' : 'mini';
	} else if (menustatus == 'closed') {//0px
		var newmenustatus = ismobile ? 'fullopen' : 'open';
	} else if (menustatus == 'fullopen') {//100%
		var newmenustatus = ismobile ? 'closed' : 'mini';
	} else {
		return false;
	}

	var isRTL = onyxIsRTL();

	if (newmenustatus == 'mini') {
		document.getElementById('onyx_largelogo').style.display = 'none';
		document.getElementById('onyx_sitename').style.display = 'none';
		var smg_display = 'none';
		var smm_display = 'block';
		logObj.style.width = '48px';
		sdObj.style.width = '48px';
		if (isRTL) {
			document.getElementById('onyx_contentwrap').style.marginRight = '48px';
		} else {
			document.getElementById('onyx_contentwrap').style.marginLeft = '48px';
		}
	} else if (newmenustatus == 'open') {
		logObj.style.width = '230px';
		sdObj.style.width = '230px';
		if (isRTL) {
			document.getElementById('onyx_contentwrap').style.marginRight = '230px';
		} else {
			document.getElementById('onyx_contentwrap').style.marginLeft = '230px';
		}
		var smg_display = 'block';
		var smm_display = 'none';
		document.getElementById('onyx_largelogo').style.display = 'block';
		document.getElementById('onyx_sitename').style.display = 'block';
	} else if (newmenustatus == 'closed') {
		document.getElementById('onyx_largelogo').style.display = 'none';
		document.getElementById('onyx_sitename').style.display = 'none';
		var smg_display = 'none';
		var smm_display = 'block';
		logObj.style.width = '0px';
		sdObj.style.width = '0px';
		if (isRTL) {
			document.getElementById('onyx_contentwrap').style.marginRight = '0px';
		} else {
			document.getElementById('onyx_contentwrap').style.marginLeft = '0px';
		}
	} else if (newmenustatus == 'fullopen') {
		logObj.style.width = '230px';
		sdObj.style.width = '100%';
		if (isRTL) {
			document.getElementById('onyx_contentwrap').style.marginRight = '100%';
		} else {
			document.getElementById('onyx_contentwrap').style.marginLeft = '100%';
		}
		var smg_display = 'block';
		var smm_display = 'none';
		document.getElementById('onyx_largelogo').style.display = 'block';
		document.getElementById('onyx_sitename').style.display = 'block';
	}

	sdObj.setAttribute('data-status', newmenustatus);
	if (uls.length > 0) {
		for (var ux=0; ux < uls.length; ux++) {
			uls[ux].style.display = smg_display;
		}
	}
	if (!ismobile) {
		if (smg_display == 'none') {
			document.getElementById('onyx_sidenav').addEventListener('mouseover', onyxToggleSidebar);
		} else {
			document.getElementById('onyx_sidenav').removeEventListener('mouseover', onyxToggleSidebar);
		}
	}
}
