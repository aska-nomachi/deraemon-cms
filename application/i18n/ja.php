<?php

defined('SYSPATH') or die('No direct script access.');
// Todo::4 これもsettings？ データベース？
return array
	(
	'attendance' => '参加',
	'absence' => '不参加',
	// validation
	':field must contain only letters' => ':field には半角文字のみ入力できます。',
	':field must contain only numbers, letters and dashes' => ':field には半角文字、半角数字、ダッシュ (-)のみ入力できます。',
	':field must contain only letters and numbers' => ':field には半角文字、半角数字のみ入力できます。',
	':field must be a color' => ':field には色の名前を入力してください。',
	':field must be a credit card number' => ':field にはクレジットカード番号を入力してください。',
	':field must be a date' => ':field には日付を入力してください。',
	':field must be a decimal with :param2 places' => ':field には小数点以下:param2桁までの数字を入力してください。',
	':field must be a digit' => ':field には半角数字のみ入力できます。',
	':field must be an email address' => ':field にはメールアドレス入力してください。',
	':field must contain a valid email domain' => ':field 有効なメールのドメイン名を入力してください。',
	':field must equal :param2' => ':field は:param2 と同じ必要があります。',
	':field must be exactly :param2 characters long' => ':field には:param2文字入力してください。',
	':field must be one of the available options' => ':field はオプションから選択してください。',
	':field must be an ip address' => ':field にはIPアドレスを入力してください。',
	':field must be the same as :param3' => ':field は:param3 と同じ必要があります。',
	':field must be at least :param2 characters long' => ':field には:param2文字以上を入力してください。',
	':field must not exceed :param2 characters long' => ':field には最大:param2文字までです。',
	':field must not be empty' => ':field は必須項目です。',
	':field must be numeric' => ':field には半角数字のみ入力できます。',
	':field must be a phone number' => ':field には電話番号を入力してください。',
	':field must be within the range of :param2 to :param3' => ':field には:param2から:param3までの範囲で入力してください。',
	':field does not match the required format' => ':field は書式が異なっています。',
	':field must be a url' => ':field にはURLアドレスを入力してください。',
	':field type is not match.' => ':field は書式が異なっています。',
	':field size is too large.' => ':field は大きすぎます。',
	':field file is corrupted.' => ' :field ファイルが壊れています。',
	':field does not exist' => ':field は存在しません。',
	':field incorrect' => ':field は正しくありません。',

	':field is not valid'=> ':field の値が正しくありません。',
	':field The field is :param2 or lower.'=> ':field は :param2 以下です。',
	':field The field is :param2 or higher.'=> 'field は :param2 以上です。',


	':field must be unique' => ':field はすでに使用されています。',
	':field is not registered.' => 'この :field は登録されていません。',
	':field is different.' => ':field が違います。',
);