/*
Elxis CMS - module Search
Copyright (c) 2006-2014 elxis.org
*/

function msearchPick(rnd) {
	if (!document.getElementById('elx_modsearch_eng'+rnd)) { return; }
	var fmObj = document.getElementById('fmmodsearch'+rnd);
	var selObj = document.getElementById('elx_modsearch_eng'+rnd);
	var selidx = selObj.selectedIndex;
	var img = selObj.options[selidx].getAttribute('data-image');
	fmObj.setAttribute('action', selObj.options[selidx].getAttribute('data-act'));
	selObj.style.backgroundImage = "url('"+img+"')";
}
