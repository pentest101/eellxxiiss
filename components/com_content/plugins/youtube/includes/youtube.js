/*
Elxis CMS - plugin Youtube Video
Created by Stavros Stratakis / Ioannis Sannos
Copyright (c) 2006-2019 elxis.org
http://www.elxis.org
*/

/* ADD VIDEO ID */
function addYTVideoID() {
	var videoid = document.getElementById('youtube_videoid').value;
    if (videoid == '') { return false; }
	var pcode = '{youtube}'+videoid+'{/youtube}';
	addPluginCode(pcode);
}
