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
	include_once("$cfg_class_dir/lang.php");
	$_lang = new lang;
	$_display = new display;
	$lang_file = $_lang -> get_lang_file();
	if (file_exists("$cfg_translations_dir/$lang_file") && ($lang_file)) {
			include ("$cfg_translations_dir/$lang_file");
	} 
	//if ($_COOKIE['related']) echo $_POST['quota_no']; 
	if (!empty($_COOKIE['related_table']))
	{
		$msg="";
		if ($_SESSION['related_table'] != $_COOKIE['related_table'])  
		{
			$_SESSION['related_table'] = $_COOKIE['related_table'];
		}
		$table = $_SESSION['related_table'];
		$rights = $_SESSION['user_rights'][$table]['write'];
		if ($_SESSION['db_admin']) $rights = 't';
		$table_name = db::get_table_string($table);
		if (!empty($_COOKIE['related_recordset_action']))
		{
			if (!empty($_COOKIE['related_key']))
			{
				$key_field=$_display->get_related_key_field();
				$key_val = $_display->format_related_key_val($_COOKIE['related_key']);				
				$query = "select * from $table where $key_field=$key_val";
				if (!empty($view_queries[$table_name])) $query=$view_queries[$table_name]." where $key_field = $key_val";
				//setcookie('related_key','',time()-3600);
			}
			if ($rights === 't') {
			 	$_display->disp_related_form($query);
			} else {
				//$_SESSION['messages'] = "Naudotojas „".$_SESSION['user']['user']."“ negali redaguoti šios lentelės."; 				
				//header ("Location: main.php");
				echo "<div id=\"messages\" class=\"messages\" OnmouseOver=\"HideElement('related-overlay'); HideElement('related'); HideElement('messages'); HideElement('overlay'); HideElement('forms'); window.location = 'main.php'\">".USER." „".$_SESSION['user']['user']."“ ".DENY_EDIT_THIS_TABLE."</div>";
			}
		} else echo "<div id=\"errors\" class=\"errors\" OnmouseOver=\"HideElement('errors')\">".MISSING_COOKIE."</div>";
	}	else 
	{
		echo "<div id=\"messages\" class=\"messages\" OnmouseOver=\"HideElement('messages')\">".SELECT_TABLE."</div>";
	}
?>
