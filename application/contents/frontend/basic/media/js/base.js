$(function () {

	//	scroll to
	$('.totop').on('click', function () {
		var target_str = $(this).prop('href');
		var target_pos = target_str.lastIndexOf('#');
		var target = target_str.substring(target_pos);

		if (target === '#') {
			pos = 0;
		} else {
			pos = $(target).offset().top;
		}

		$('html, body').animate({
			scrollTop: pos
		}, 'slow', 'swing');
		return false;
	});

});