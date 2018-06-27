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
	include_once("$cfg_class_dir/lang.php");
	include_once("$cfg_class_dir/display.php");
	$_lang = new lang;
	$_display = new display;
	$lang_file = $_lang -> get_lang_file();
	if (file_exists("$cfg_translations_dir/$lang_file") && ($lang_file)) {
			include ("$cfg_translations_dir/$lang_file");
	} 
	if (!empty($_COOKIE['current_table']))
	{
		$table = $_COOKIE['current_table'];
		$rights = $_SESSION['user_rights'][$table]['view'];
		if ($_SESSION['db_admin']) $rights = 't';
		$_SESSION['recordset_action'] = $_COOKIE['recordset_action'];
		if ($_SESSION['current_table'] != $_COOKIE['current_table']) {
			$_SESSION['current_table'] = $_COOKIE['current_table'];
		}	
		if ($rights === 't') {
			$_display->disp_table(); 
		} else {
			echo "<div id=\"messages\" class=\"messages\" OnmouseOver=\"HideElement('messages');\">Naudotojas „".$_SESSION['user']['user']."“ negali žiūrėti šios lentelės.</div>";
			$_display->disp_dashboard($cfg_dashboard_columns);
		}
	} else $_display->disp_dashboard($cfg_dashboard_columns);
?>
