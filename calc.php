<?php
	session_start(); 
	$key_test=$_COOKIE['session_key'];
	if (empty($key_test) || ($key_test != $_SESSION['key']))
	{
		session_destroy();
		header("Location: login.php");
		header("Pragma: no-cache");
		exit;
	}
	include_once("lib/etc/site.conf");
	include_once("$cfg_class_dir/db.php");
	if (!empty($_COOKIE['current_table']))
	{
		$table = $_SESSION['current_table'];
		$rights = $_SESSION['user_rights'][$table]['write'];
		if ($_SESSION['db_admin']) $rights = 't';
		$table_name = db::get_table_string($_COOKIE['current_table']);
		$filename = "lib/scripts/process/calcs/$table_name".".php";
		$custom_filename = "$cfg_custom_calcs_dir/$table_name.php";
		if ($rights === 't') {
			if (file_exists($filename)) include($filename);
			if (file_exists($custom_filename)) include($custom_filename);
		} else {
			$_SESSION['messages'] = USER."„".$_SESSION['user']['user']."“ ".DENY_EDIT." ".THIS_TABLE;
		}
		if ($_SESSION['current_table'] != $_COOKIE['current_table']) {
			$_SESSION['current_table'] = $_COOKIE['current_table'];
		}
		setcookie('key', NULL);
	}
	header ("Location: main.php");
?>