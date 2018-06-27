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
			$filename = "lib/scripts/process/display/pre_".$table_name.".php";
			if ($rights === 't') {
				if (file_exists($filename)) include($filename);
			} else {
				$_SESSION['messages'] = "Naudotojas „".$_SESSION['user']['user']."“ neagali redaguoti šios lentelės.";
			}
			if ($_SESSION['current_table'] != $_COOKIE['current_table']) {
				$_SESSION['current_table'] = $_COOKIE['current_table'];
			}	
			setcookie('key', NULL);
			setcookie('next_table', NULL);
		} else $_SESSION['messages'] = 'Pasirinkite įrašą...';
	}
	header ("Location: main.php");
?>