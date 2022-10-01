function modAArtToggle() {
	if (document.getElementById('modaart_selmonths').classList.contains('elx5_datanotcurrent')) { return false; }
	elx5Toggle('modaart_options');
}

function modAArtLoad(popular) {
	if (isNaN(popular)) { var popular = 0; }
	popular = parseInt(popular, 10);
	var edata = {};
	edata.months = 0;
	edata.popular = popular;
	edata.format = 'json';
	edata.rnd = Math.floor((Math.random()*100)+1);
	if (popular == 1) {
		var sObj = document.getElementById('modaart_months');
		let months = sObj.options[sObj.selectedIndex].value;
		edata.months = parseInt(months, 10);
	}
	edata.f = 'modules/mod_adminarticles/req.php';

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
			var tblObj = document.getElementById('modaart_tbl');
			var rows = tblObj.getElementsByTagName('tr');
			var x = '';
			if (rows.length > 1) {
				for (var i = rows.length - 1; i > 0; i--) {
					tblObj.deleteRow(i);
					x += i+', ';
				}
			}

			document.getElementById('modaart_title').innerHTML = jsonObj.listtitle;
			document.getElementById('modaart_listdesc').innerHTML = jsonObj.listdesc;

			if (jsonObj.articles.length > 0) {
				let can_edit_art = document.getElementById('modaart_dataeditart').getAttribute('data-canedit'); 
				can_edit_art = parseInt(can_edit_art, 10);
				let edit_link_art = document.getElementById('modaart_dataeditart').innerHTML;
				let can_edit_ctg = document.getElementById('modaart_dataeditctg').getAttribute('data-canedit'); 
				can_edit_ctg = parseInt(can_edit_ctg, 10);
				let edit_link_ctg = document.getElementById('modaart_dataeditctg').innerHTML;

				var tbodObj = document.getElementById('modaart_tbody');

				for (var q=0; q < jsonObj.articles.length; q++) {
					var artObj = jsonObj.articles[q];

					var trObj = document.createElement('tr');
					trObj.id = 'datarow'+artObj.artid;

					var tdObj = document.createElement('td');
					tdObj.className = 'elx5_center';
					var ptext = document.createTextNode(artObj.hits);
					tdObj.appendChild(ptext);
					trObj.appendChild(tdObj);

					if (can_edit_art > 0) {
						var txt = '<i class="'+artObj.iconclass+'"></i> <a href="'+edit_link_art+'?id='+artObj.artid+'" title="'+artObj.fulltitle+'">'+artObj.title+'</a>';
					} else {
						var txt = '<span title="'+artObj.fulltitle+'"><i class="'+artObj.iconclass+'"></i> '+artObj.title+'</span>';
					}
					if (artObj.catid > 0) {
						if (can_edit_ctg > 0) {
							txt += '<div class="elx5_tip elx5_lmobhide">'+tblObj.getAttribute('data-lngin')+' <a href="'+edit_link_ctg+'?catid='+artObj.catid+'">'+artObj.cattitle+'</a></div>';
						} else {
							txt += '<div class="elx5_tip elx5_lmobhide">'+tblObj.getAttribute('data-lngin')+' '+artObj.cattitle+'</div>';
						}
					}
					var td2Obj = document.createElement('td');
					td2Obj.innerHTML = txt;
					trObj.appendChild(td2Obj);

					var td3Obj = document.createElement('td');
					td3Obj.className = 'elx5_tabhide';
					td3Obj.innerHTML = artObj.created_by_name;
					trObj.appendChild(td3Obj);

					var td4Obj = document.createElement('td');
					td4Obj.className = 'elx5_lmobhide';
					td4Obj.innerHTML = artObj.fdate;
					trObj.appendChild(td4Obj);

					tbodObj.appendChild(trObj);
				}
			} else {
				var tbodObj = document.getElementById('modaart_tbody');
				var trObj = document.createElement('tr');
				trObj.id = 'datarow0';
				trObj.className = 'elx5_rowwarn';
				var tdObj = document.createElement('td');
				tdObj.className = 'elx5_center';
				tdObj.setAttribute('colspan', '4');
				var ptext = document.createTextNode(tblObj.getAttribute('data-lngnores'));
				tdObj.appendChild(ptext);
				trObj.appendChild(tdObj);
				tbodObj.appendChild(trObj);
			}
		}
	};

	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader();
		alert('Action failed! '+errorThrown);
	};

	if (edata.popular == 1) {
		document.getElementById('modaart_latest').className = 'elx5_dataaction elx5_datanotcurrent';
		document.getElementById('modaart_popular').className = 'elx5_dataaction elx5_datahighlight';
		document.getElementById('modaart_selmonths').className = 'elx5_dataaction elx5_datahighlight elx5_lmobhide';
	} else {
		document.getElementById('modaart_latest').className = 'elx5_dataaction elx5_datahighlight';
		document.getElementById('modaart_popular').className = 'elx5_dataaction elx5_datanotcurrent';
		document.getElementById('modaart_selmonths').className = 'elx5_dataaction elx5_datanotcurrent elx5_lmobhide';
		if (document.getElementById('modaart_options').className != 'elx5_invisible') { elx5Toggle('modaart_options'); }
	}

	var eurl = document.getElementById('modaart_tbl').getAttribute('data-inpage');
	elx5StartPageLoader();
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}

