<?php
	session_start();
	$key_test = $_COOKIE['session_key'];
	if (empty($key_test) || ($key_test != $_SESSION['key']))
	{
		session_destroy();
		header("Location: login.php");
		header("Pragma: no-cache");
		exit;
	}
	include_once("lib/etc/site.conf");
	include_once("$cfg_class_dir/db.php");
	$lang = $_COOKIE['lang'];
	if (empty($lang)) {
		$lang = $cfg_default_lang;		
	}
	$link = $cfg_help_dir."/user_guide_$lang.html";
	if (!empty($_COOKIE['current_table'])) {
		$link = $cfg_help_dir."/".db::get_table_string($_COOKIE['current_table'])."_$lang.html";
	}
	header("Location: $link");
?>