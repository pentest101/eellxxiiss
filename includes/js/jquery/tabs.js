$(document).ready(function() {
	$('.tab_content').hide();
	var tabidx = -1;
	$('ul.tabs li').each(function(idx) {
		if ($(this).hasClass('tabopen') == true) { tabidx = idx; $(this).addClass('active').show(); }
	});
	if (tabidx > -1) {
		$('.tab_content').each(function(idx) { if (idx == tabidx) { $(this).show(); } });
	} else {
		$('ul.tabs li:first').addClass("active").show();
		$('.tab_content:first').show();
	}

	$('ul.tabs li').click(function() {
		$('ul.tabs li').removeClass('active');
		$(this).addClass('active');
		$('.tab_content').hide();
		var activeTab = $(this).find('a').attr('href');
		if (document.getElementById('tabopen')){
			var tabidx = $(this).index() + 1;
			document.getElementById('tabopen').value = tabidx;
		}
		$(activeTab).fadeIn();
		return false;
	});
});
