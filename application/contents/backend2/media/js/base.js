$(function() {
	//--------------------------------------------------------------------------
	// CodeMirror
	//--------------------------------------------------------------------------
	var optipn = {
		lineNumbers: true,
		matchBrackets: true,
		mode: "application/x-httpd-php",
		indentUnit: 4,
		indentWithTabs: true,
		enterMode: "keep",
		tabMode: "shift",
		theme: "ambiance"
	};

	if ($('#content_textarea1').length !== 0)
	{
		var editor1 = CodeMirror.fromTextArea($('#content_textarea1').get(0), optipn);
		editor1.setSize(null, 800);
	}
	//--------------------------------------------------------------------------
	// slide box
	//--------------------------------------------------------------------------
	$('.sld').next().css('display', 'none');
	$('.sld').click(function() {
		$(this).next().slideToggle('fast');
		return false;
	});
	//--------------------------------------------------------------------------
	// scroll to
	//--------------------------------------------------------------------------
	$('[data-scroll_to]').on('click', function() {

		var target = $(this).data('scroll_to');

		$('html, body').animate({
			scrollTop: $(target).offset().top
		}, 'slow', 'swing');

		return false;
	});
	//--------------------------------------------------------------------------
	// notice close
	//--------------------------------------------------------------------------
	$('#notice .close').on('click', function() {
		$(this).parent().parent().slideUp(400, function() {
			$(this).remove();
		});
	});
	//--------------------------------------------------------------------------
	// tabs
	//--------------------------------------------------------------------------
	$('.tabs .tab_name').click(function() {

		var active = 'active';
		var hide = 'hid';

		var target = $(this);

		var parent = target.parents('.tabs');

		$('.tab_name', parent).removeClass(active);
		target.addClass(active);

		var index = $(this).index();

		$('.tab_box', parent).addClass(hide);
		$('.tab_box', parent).eq(index).removeClass(hide);
	});
	//--------------------------------------------------------------------------
	// delete confirm
	//--------------------------------------------------------------------------
	$('.delete').click(function() {

		var button = $(this);

		if (button.data('delete_ready') === 1)
		{
			return;
		}

		button.data('delete_ready', 1);

		var html = [
			'<div id="confirm_overlay">',
			'<div id="confirm_box" class="gla2 fix">',
			'<div class="p2">',
			'<h3>delete</h3>',
			'<p class="mb1">Will you delete?</p>',
			'<div id="confirm_buttons" class="txr">',
			'<span class="delete_answer btn red" data-answer="yes">yes</span>',
			'<span class="delete_answer btn ml1" data-answer="no">no</span>',
			'</div>',
			'</div>',
			'</div>',
			'</div>'
		].join('');

		$(html).hide().appendTo('body').fadeIn(100);

		$('#confirm_overlay .delete_answer').on('click', function() {

			if ($(this).data('answer') === 'no')
			{
				$('#confirm_overlay').fadeOut(100, function() {
					$(this).remove();
				});
				button.data('delete_ready', 0);
			}
			else
			{
				var this_href = $(button).prop('href');
				if (typeof this_href === "undefined")
				{
					button.click();
				}
				else
				{
					window.location.href = this_href;
				}
			}
		});

		return false;
	});

	//--------------------------------------------------------------------------
	// check_all
	//--------------------------------------------------------------------------
	$('.check_off_all').click(function() {
		var name = $(this).data('name');
		$('input[name^="' + name + '"]').prop('checked', false);
		return false;
	});
	//--------------------------------------------------------------------------
	// snippets
	//--------------------------------------------------------------------------
	var space_h = 60;
	var space_w = 60;
	var top_pos = 0;
	var boxes = $('#snippets .snippet_boxes > div');
	var tabs = $('#snippets .snippet_tabs > li');

	function snippets_set(space_h, space_w)
	{
		window_h = $(window).height();
		content_w = $('#wrapper').width() - space_w;

		top_pos = (space_h / 2) + 'px';

		boxes.css({
			'height': (window_h - space_h) + 'px',
			'top': '-100%',
			'width': content_w + 'px',
			'left': '50%',
			'margin-left': '-' + (content_w / 2) + 'px',
			'position': 'fixed',
			'z-index': '100'
		});

		tabs.removeClass('active');
	}

	snippets_set(space_h, space_w);

	$(window).resize(function() {
		snippets_set(space_h, space_w);
	});

	// 外クリックで閉じる
	$('body').on('click', function() {
		tabs.removeClass('active');
		boxes.css('top', '-100%');
	});

	$('#snippets li').click(function() {
		var tab = $(this);
		var box = $('.' + $(this).data('name') + '_box');

		if (tab.hasClass('active'))
		{
			tabs.removeClass('active');
			boxes.css('top', '-100%');
		}
		else
		{
			tabs.not(tab).removeClass('active');
			tab.addClass('active');

			boxes.not(box).css('top', '-100%');
			box.css('top', top_pos);
		}
	});

	$('#snippets').click(function(event) {
		event.stopPropagation();
	});

	//test
	//$('#snippets .syntax_box').css('top', top_pos);

//	$('.snippet').click(function() {
//		$(this).select().focus();
//		return false;
//	});
	//--------------------------------------------------------------------------
});