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
	if (!empty($_COOKIE['current_table']))
	{
		$msg="";
		if ($_SESSION['current_table'] != $_COOKIE['current_table'])
		{
			$_SESSION['current_table'] = $_COOKIE['current_table'];
		}
		$table = $_SESSION['current_table'];
		$rights = $_SESSION['user_rights'][$table]['write'];
		if ($_SESSION['db_admin']) $rights = 't';
		$table_name = db::get_table_string($table);
		if (!empty($_COOKIE['recordset_action']))
		{
			if (!empty($_COOKIE['key']))
			{
				$key_field=$_display->get_key_field();
				$key_val = $_display->format_key_val($_COOKIE['key']);
				$query = "SELECT * FROM $table WHERE $key_field = $key_val";
				if (!empty($view_queries[$table_name])) $query = $view_queries[$table_name]." WHERE $key_field = $key_val";
				//setcookie('key','',time()-3600);
			}
			if ($rights === 't') {
?>
<?php
			 	$_display->disp_form($query);
			}
			else {
				echo "<div id=\"messages\" class=\"messages\" OnmouseOver=\"HideElement('related-overlay'); HideElement('related'); HideElement('messages'); HideElement('overlay'); HideElement('forms');\">".USER." „".$_SESSION['user']['user']."“ ".DENY_EDIT_THIS_TABLE."</div>";
			}
		} else echo "<div id=\"errors\" class=\"errors\" OnmouseOver=\"HideElement('errors')\">".MISSING_COOKIE."</div>";
	}	else
	{
		echo "<div id=\"messages\" class=\"messages\" OnmouseOver=\"HideElement('messages'); HideElement('overlay'); HideElement('forms')\">".SELECT_TABLE."</div>";
	}
?>


