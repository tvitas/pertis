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
	include_once("$cfg_class_dir/printrepo.php");
	$repo_id = $_COOKIE['repo_id'];
	$record_id = $_COOKIE['key'];
	$recordset_action  = $_COOKIE['recordset_action'];
	$_printrepo = new printrepo;
	if (!empty($repo_id)) {
		setcookie('repo_id', NULL);
	}
	if (!empty($recordset_action)) {
		setcookie('recordset_action', NULL);
	}
	if (file_exists("$cfg_custom_layout_dir/print-page-head.php")) {
		include("$cfg_custom_layout_dir/print-page-head.php");
	} else {
		include("$cfg_layout_dir/print-page-head.php");	
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
			//$repo_id = $_COOKIE['repo_id'];   
			//echo "debug: record_id $record_id";
			if (!empty($repo_id)) {
				if (!empty($record_id)) {
?>
					<script type="text/javascript">
						document.cookie = "key=";
					</script>
<?php
				}
				$_printrepo -> report_generate($repo_id, $record_id);
			} else {
				$_printrepo -> report_select();
			}
		} else {
			echo "<div id=\"messages\" class=\"messages\" OnmouseOver=\"HideElement('messages');\">Naudotojas „".$_SESSION['user']['user']."“ negali žiūrėti/spausdinti šios lentelės.</div>";
		}
	}
	if (file_exists("$cfg_custom_layout_dir/print-page-foot.php")) {
		include("$cfg_custom_layout_dir/print-page-foot.php");
	} else {
		include("$cfg_layout_dir/print-page-foot.php");	
	}
?>

