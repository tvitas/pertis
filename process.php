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
	if (!empty($_SESSION['current_table']))
	{
		$table = $_SESSION['current_table'];
		$rights = $_SESSION['user_rights'][$table]['write'];
		if ($_SESSION['db_admin']) $rights = 't';		
		if (!empty($_COOKIE['key'])) {
			$table_name = db::get_table_string($_COOKIE['current_table']);
			$custom_process_filename = "$cfg_custom_process_dir/display/pre_$table_name.php";
			$process_filename = "lib/scripts/process/display/pre_".$table_name.".php";
			if ($rights === 't') {
				if (file_exists($process_filename)) include($process_filename);
				if (file_exists($custom_process_filename)) include($custom_process_filename);
			} else {
				$_SESSION['messages'] = "Naudotojas „".$_SESSION['user']['user']."“ neagali redaguoti šios lentelės.";
			}
			if ($_SESSION['current_table'] != $_COOKIE['current_table']) {
				$_SESSION['current_table'] = $_COOKIE['current_table'];
			}	
			setcookie('recordset_action', NULL);
			setcookie('key', NULL);
			setcookie('next_table', NULL);
		} else $_SESSION['messages'] = 'Pasirinkite įrašą...';
	}
	header ("Location: main.php");
?>