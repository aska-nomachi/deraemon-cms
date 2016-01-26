<?php

$dsn = 'mysql:host=localhost;dbname=default_emon';
$username = 'root';
$password = '';
		setlocale(LC_ALL, 'ja_JP.utf-8');

$db = new PDO($dsn, $username, $password);
//$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = "update settings set value = :value where `key` = 'site_title'";
$stmt = $db->prepare($sql);
$stmt->bindValue(':value', 'あああ');
$stmt->execute();