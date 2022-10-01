function modALangSwitch() {
	if (!document.getElementById('modalang_select')) { return; }
	var selObj = document.getElementById('modalang_select');
	let baselink = selObj.getAttribute('data-baselink');
	let deflang = selObj.getAttribute('data-deflang');
	let sellang = selObj.options[selObj.selectedIndex].value;
	if (deflang == sellang) {
		var redirURL = baselink.replace('/xx/', '/');
	} else {
		var redirURL = baselink.replace('/xx/', '/'+sellang+'/');
	}
	window.location.href = redirURL;
}
