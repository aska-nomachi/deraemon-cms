$(function() {
//-----------------------------------

	/**
	 * example: <label class="btn">file<input type="file" name=""></label>
	 */
	$('input[type="file"]').each(function() {

		var label = $(this).closest('label');
		var clone_file = $(this).clone().hide();
		label.addClass('inputfile').after(clone_file);
		$(this).remove();

	});

	$('label.inputfile').click(function() {

		var label = $(this);
		var inputfile = label.next('input[type="file"]');

		inputfile.click();

		inputfile.change(function() {
			label.text(inputfile.val());
		});

	});

//-----------------------------------
});