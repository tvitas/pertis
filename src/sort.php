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
		$_display = new display;
	if (!empty($_COOKIE['current_table']))
	{
		$msg="";

		if ($_SESSION['current_table'] != $_COOKIE['current_table'])  
		{
			$_SESSION['current_table'] = $_COOKIE['current_table'];
		}
		$table = $_SESSION['current_table'];
		$table_name = db::get_table_string($table);
		if (!empty($_COOKIE['recordset_action']))
		{
			$msg=$msgs[$_COOKIE['recordset_action']];
			$_display->disp_msg($_display->get_table_title(), "back");
		 	$_display->disp_form("");
		 	$_display->disp_msg($msg,"");
		} else echo "<b>Missing or invalid recordset_action cookie...</b>";
	}	else 	{
		echo "<div id=\"messages\" class=\"messages\" OnmouseOver=\"HideElement('messages')\">Pasirinkite lentelę...</div>";
		$_display->disp_dashboard($cfg_dashboard_columns);
	} 
?> 

