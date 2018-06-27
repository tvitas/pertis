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
	if ($cfg_maintenance_mode) {
		session_destroy();
		header("Location: maintenance-page.php");
		header("Pragma: no-cache");
		exit;
	}
	include_once("$cfg_class_dir/db.php");
	include_once("$cfg_class_dir/display.php");
	include_once("$cfg_class_dir/auth.php");
	//include_once("$cfg_class_dir/inputfilter.php");
	$_display = new display;
	$_db = new db;
	$_auth = new auth;
	if (!$_SESSION['instance'])
	{
		$_auth -> set_user_role_rights();
		$_auth -> set_sys_params();
		$_auth -> set_ad_attributes_table();
		$user_active = $_auth -> user_is_active();
		if (!$user_active) {
			session_destroy();
			header("Location: login.php");
			header("Pragma: no-cache");
			exit;
		}
		$_display -> refresh();
		$_SESSION['instance'] = true;
		$_SESSION['select_tables'] = $_display -> make_select_tables();
		$_SESSION['select_lang'] = $_display -> make_select_lang();
	}
	if (file_exists("$cfg_custom_layout_dir/head.php") && file_exists("$cfg_custom_layout_dir/page-top.php")) {
		include("$cfg_custom_layout_dir/head.php");
		include("$cfg_custom_layout_dir/page-top.php");
	} else {
		include("$cfg_layout_dir/head.php");
		include("$cfg_layout_dir/page-top.php");
	}
?>
<?php if (!empty($_SESSION['messages'])):?>
	<div id="messages" class="messages" onmouseover="HideElement('messages')"><?php print $_SESSION['messages']; $_SESSION['messages']="";?></div>
<?php endif;?>
<?php if (!empty($_SESSION['errors'])):?>
	<div id="errors" class="errors" onmouseover="HideElement('errors')"><?php print $_SESSION['errors']; $_SESSION['errors']="";?></div>
<?php endif;?>
<div id="forms" class="forms"> </div>
<div id="related-overlay" class="related-overlay"> </div>
<div id="related" class="related"> </div>
<div id="overlay" class="overlay"> </div>
<div id="ajaxer" class="wait"></div>
<div id="contents" class="contents">
<?php
	if (!empty($_COOKIE['current_table'])) {
		$_SESSION['recordset_action'] = $_COOKIE['recordset_action'];
		if ($_SESSION['current_table'] != $_COOKIE['current_table']) {
			$_SESSION['current_table'] = $_COOKIE['current_table'];
		}
		$table = $_SESSION['current_table'];
		$rights = $_SESSION['user_rights'][$table]['view'];
		if ($_SESSION['db_admin']) $rights = 't';
 		if ($rights == 't') {
			$_display->disp_table();
		} else {
			echo "<div id=\"messages\" class=\"messages\" OnmouseOver=\"HideElement('messages');\">Naudotojas „".$_SESSION['user']."“ negali žiūrėti šios lentelės.</div>";
		}
	} else {
		$_display -> disp_dashboard($cfg_dashboard_columns);
	}
?>
</div>

<script type="text/javascript">
var related = GetCookie('related');
if (related) {
	deletecookie('related_recordset_action');
	deletecookie('related_key');
	deletecookie('related');
	HideElement('related');
	HideElement('related-overlay');
	ShowElement('overlay');
	ShowElement('forms');
	AjaxShow(':::edit.php:forms::');
}
</script>

<?php
	if (file_exists("$cfg_custom_layout_dir/page-foot.php")) {
		include("$cfg_custom_layout_dir/page-foot.php");
	} else {
		include("$cfg_layout_dir/page-foot.php");
	}
?>
