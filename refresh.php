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
	include_once("$cfg_class_dir/display.php");
	echo "<div id=\"messages\" class=\"messages\" OnmouseOver=\"HideElement('messages');\">Sąrašai atnaujinti...</div>";
	$_db = new db;
	$_display = new display;
	if (!empty($_COOKIE['current_table']))
	{
		$_SESSION['recordset_action'] = $_COOKIE['recordset_action'];
		if ($_SESSION['current_table'] != $_COOKIE['current_table']) 
		{
			$_SESSION['current_table'] = $_COOKIE['current_table'];
		}
		$_db -> refresh();
		$_display -> disp_table(); 
	} else $_display -> disp_dashboard($cfg_dashboard_columns);
?>