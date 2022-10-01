function modAStatsUpdate(step) {
	step = parseInt(step, 10);
	if ((step < -1) || (step > 1)) { return; }

	let chartok = true;
	if (typeof astatsChart === 'undefined') {
		chartok = false;
		Chart.helpers.each(Chart.instances, function(instance) {
			if (instance.chart.canvas.id == 'modastatsgraph') {
				var astatsChart = instance;
				chartok = true;
			}
		});
	}
	if (!chartok) { return; }

	var boxObj = document.getElementById('modastats_box');
	var dataObj = document.getElementById('modastats_data');
	var descObj = document.getElementById('modastat_desc');

	var statsData = {};
	statsData.year = parseInt(boxObj.getAttribute('data-year'), 10);
	statsData.month = parseInt(boxObj.getAttribute('data-month'), 10);

	if (step == 0) {//switch
		statsData.days = parseInt(boxObj.getAttribute('data-days'), 10);
		if (boxObj.getAttribute('data-type') == 'clicks') {
			statsData.type = 'visits';
			statsData.data = dataObj.getAttribute('data-visits').split(',');
			astatsChart.data.datasets[0].label = descObj.getAttribute('data-visitslng');
		} else {
			statsData.type = 'clicks';
			statsData.data = dataObj.getAttribute('data-clicks').split(',');
			astatsChart.data.datasets[0].label = descObj.getAttribute('data-clickslng');
		}
		boxObj.setAttribute('data-type', statsData.type);

		let k = 0, v = 0;
		for (k=0; k < statsData.days; k++) {
			v = parseInt(statsData.data[k], 10);
			astatsChart.data.datasets[0].data[k] = v;
		}

		astatsChart.update();
		if (statsData.type == 'clicks') {
			descObj.innerHTML = descObj.getAttribute('data-cdesc');
			document.getElementById('modastats_btn').innerHTML = '<i class="fas fa-mouse-pointer"></i><span class="elx5_lmobhide"> '+descObj.getAttribute('data-clickslng')+'</span>';
		} else {
			descObj.innerHTML = descObj.getAttribute('data-vdesc');
			document.getElementById('modastats_btn').innerHTML = '<i class="fas fa-user"></i><span class="elx5_lmobhide"> '+descObj.getAttribute('data-visitslng')+'</span>';
		}
		return;
	}

	statsData.month = statsData.month + step;
	if (statsData.month == 0) {
		statsData.month = 12;
		statsData.year--;
	} else if (statsData.month == 13) {
		statsData.month = 1;
		statsData.year++;
	}

	modAStatsLoad(statsData.year, statsData.month, boxObj, dataObj, astatsChart);
}


function modAStatsLoad(year, month, boxObj, dataObj, astatsChart) {
	var edata = {};
	edata.year = year;
	edata.month = month;
	edata.format = 'json';
	edata.rnd = Math.floor((Math.random()*100)+1);
	edata.f = 'modules/mod_adminstats/req.php';

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
			boxObj.setAttribute('data-year', jsonObj.year);
			boxObj.setAttribute('data-month', jsonObj.month);
			boxObj.setAttribute('data-days', jsonObj.daysnum);
			dataObj.setAttribute('data-clicks', jsonObj.clicks);
			dataObj.setAttribute('data-visits', jsonObj.visits);
			document.getElementById('modastat_desc').setAttribute('data-cdesc', jsonObj.clicksdesc);
			document.getElementById('modastat_desc').setAttribute('data-vdesc', jsonObj.visitsdesc);

			var statsData = {};
			if (boxObj.getAttribute('data-type') == 'clicks') {
				statsData.type = 'clicks';
				statsData.data = dataObj.getAttribute('data-clicks').split(',');

				document.getElementById('modastat_desc').innerHTML = jsonObj.clicksdesc;
			} else {
				statsData.type = 'visits';
				statsData.data = dataObj.getAttribute('data-visits').split(',');
				document.getElementById('modastat_desc').innerHTML = jsonObj.visitsdesc;
			}
			statsData.days = parseInt(jsonObj.daysnum, 10);

			let newlabels = [], k = 0, v = 0;
			for (k=0; k < statsData.days; k++) {
				let w = k + 1;
				v = parseInt(statsData.data[k], 10);
				astatsChart.data.datasets[0].data[k] = v;
				newlabels.push(w);
			}
			astatsChart.data.labels = newlabels;
			astatsChart.update();
		}
	};

	var errorfunc = function (XMLHttpRequest, textStatus, errorThrown) {
		elx5StopPageLoader();
		alert('Action failed! '+errorThrown);
	};

	var eurl = document.getElementById('modastats_box').getAttribute('data-inpage');
	elx5StartPageLoader();
	elxAjax('POST', eurl, edata, null, null, successfunc, errorfunc);
}
