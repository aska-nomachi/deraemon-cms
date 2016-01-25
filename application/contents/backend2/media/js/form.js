$(function() {
	//-----------------------------------

	// select
	// <select name="sel1" data-value="1">
	//		<option value="1">1</option>
	//		<option value="2">2</option>
	//		<option value="3">3</option>
	// </select>
	$('select:not([multiple=multiple])').each(function() {

		// もしdata属性なかったらリターン
		if ($(this).data('value') === undefined)
			return;

		// 値を取得
		var data_value = $(this).data('value').toString(10);

		$('option', $(this)).each(function() {
			if ($(this).attr('value') === data_value) {
				$(this).prop('selected', true);
			}
			else {
				$(this).prop('selected', false);
			}
		});
	});

	// select multiple
	//	<select name="selm[]" multiple="multiple" data-value="{{AAA, BBB}}">
	//		<option value="AAA">AAA</option>
	//		<option value="BBB">BBB</option>
	//		<option value="CCC">CCC</option>
	//	</select>
	$('select[multiple=multiple]').each(function() {

		// もしdata属性なかったらリターン
		if ($(this).data('value') === undefined)
			return;

		// splitしてるから空文字のときのリターンは空文字配列
		var data_value = $(this).data('value').toString(10).replace(/\s+/g, "").split(',');

		$('option', $(this)).each(function() {
			if ($.inArray($(this).val(), data_value) !== -1) {
				$(this).prop('selected', true);
			}
			else {
				$(this).prop('selected', false);
			}
		});
	});

	// radio
	// non<input type="radio" name="num" value="" data-value="0, 2">
	// 0<input type="radio" name="num" value="0" data-value="0, 2">
	// 1<input type="radio" name="num" value="1" data-value="0, 2"">
	// 2<input type="radio" name="num" value="2" data-value="0, 2"">
	// 3<input type="radio" name="num" value="3" data-value="0, 2"">
	// これでもでもOK
	// <input type="radio" name="num" value="4" data-value="0, 2"">
	$('input[type="radio"]').each(function() {

		// もしdata属性なかったらリターン
		if ($(this).data('value') === undefined)
			return;

		// 値を取得
		var value = $(this).val();
		var data_value = $(this).data('value').toString(10);

		if (data_value === value) {
			$(this).prop('checked', true);
		}
		else {
			$(this).prop('checked', false);
		}

	});

	// checkbox
	// non<input type="checkbox" name="ani[]" value="" data-value="neko, inu">
	// neko<input type="checkbox" name="ani[]" value="neko" data-value="neko, inu">
	// risu<input type="checkbox" name="ani[]" value="risu" data-value="neko, inu">
	// nu<input type="checkbox" name="ani[]" value="inu" data-value="neko, inu">
	// 1つの時はこれでもでもOK
	// <input type="radio" name="ok" value="1" data-value="neko">
	$('input[type="checkbox"]').each(function() {

		// もしdata属性なかったらリターン
		if ($(this).data('value') === undefined)
			return;

		// 値を取得
		var value = $(this).val();

		// splitしてるから空文字のときのリターンは空文字配列
		var data_value = $(this).data('value').toString(10).replace(/\s+/g, "").split(',');

		if ($.inArray(value, data_value) !== -1) {
			$(this).prop('checked', true);
		}
		else {
			$(this).prop('checked', false);
		}
	});

//-----------------------------------
});

//	<form action="" method="GET">
//		<hr>
//		<p>num:{{get.num}}</p>
//		non<input type="radio" name="num" value="" data-value="{{~data_value(get.num)}}">
//		0<input type="radio" name="num" value="0" data-value="{{~data_value(get.num)}}">
//		1<input type="radio" name="num" value="1" data-value="{{~data_value(get.num)}}">
//		2<input type="radio" name="num" value="2" data-value="{{~data_value(get.num)}}">
//		3<input type="radio" name="num" value="3" data-value="{{~data_value(get.num)}}"><br>
//		<hr>
//		<p>str:{{get.str}}</p>
//		aaa<input type="radio" name="str" value="aaa" data-value="{{get.str}}" checked>
//		bbb<input type="radio" name="str" value="bbb" data-value="{{get.str}}">
//		ccc<input type="radio" name="str" value="ccc" data-value="{{get.str}}"><br>
//		<hr>
//		<p>ani:{{~data_value(get.ani)}}</p>
//		non<input type="checkbox" name="ani[]" value="" data-value="{{~data_value(get.ani)}}">
//		neko<input type="checkbox" name="ani[]" value="neko" data-value="{{~data_value(get.ani)}}">
//		risu<input type="checkbox" name="ani[]" value="risu" data-value="{{~data_value(get.ani)}}">
//		inu<input type="checkbox" name="ani[]" value="inu" data-value="{{~data_value(get.ani)}}"><br>
//		<hr>
//		<p>ok:{{~data_value(get.ok)}}</p>
//		ok<input type="checkbox" name="ok" value="1" data-value="{{~data_value(get.ok)}}">
//		<p>no:{{~data_value(get.no)}}</p>
//		no<input type="checkbox" name="no" value="1" data-value="{{get.no}}">
//		<hr>
//		<p>nnn:{{~data_value(get.nnn)}}</p>
//		non<input type="checkbox" name="nnn[]" value="" data-value="{{~data_value(get.nnn)}}">
//		0<input type="checkbox" name="nnn[]" value="0" data-value="{{~data_value(get.nnn)}}">
//		1<input type="checkbox" name="nnn[]" value="1" data-value="{{~data_value(get.nnn)}}">
//		2<input type="checkbox" name="nnn[]" value="2" data-value="{{~data_value(get.nnn)}}">
//		3<input type="checkbox" name="nnn[]" value="3" data-value="{{~data_value(get.nnn)}}"><br>
//		<hr>
//		<p>nnn:{{~data_value(get.sel1)}}</p>
//		<select name="sel1" data-value="{{~data_value(get.sel1)}}">
//			<option value="aaa">aaa</option>
//			<option value="bbb">bbb</option>
//			<option value="ccc">ccc</option>
//		</select>
//		<hr>
//		<p>nnn:{{~data_value(get.sel2)}}</p>
//		<select name="sel2" data-value="{{~data_value(get.sel2)}}">
//			<option value="1">1</option>
//			<option value="2">2</option>
//			<option value="3">3</option>
//		</select>
//		<hr>
//		<select name="selm[]" multiple="multiple" data-value="{{~data_value(get.selm)}}">
//			<option value="AAA">AAA</option>
//			<option value="BBB">BBB</option>
//			<option value="CCC">CCC</option>
//		</select>
//		<button type="submit" value="1">go</button>
//	</form>