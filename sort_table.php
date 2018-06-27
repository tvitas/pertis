<?php
	session_start(); 
	$key_test = $_COOKIE['session_key'];
	if (empty($key_test) || ($key_test != $_SESSION['key']))
	{
		session_destroy();
		header("Location: login.php");
		header("Pragma: no-cache");
		exit;
	}
	include_once("lib/etc/site.conf");
	include_once("$cfg_class_dir/display.php");
	//	$_display = new display;
	if (!empty($_COOKIE['current_table'])) {
		if ($_SESSION['current_table'] != $_COOKIE['current_table']) {
			$_SESSION['current_table'] = $_COOKIE['current_table'];
		}
		if ($_COOKIE['recordset_action']=='sort') {
			if ($_SESSION['select_order']==$_COOKIE['select_order']) {
				if ($_SESSION['order_dir']=='ASC') { 
					$_SESSION['order_dir']='DESC';
				} elseif ($_SESSION['order_dir']=='DESC') { 
					$_SESSION['order_dir']='ASC';
				}
			}
		} 
	}
header('Location: main.php');
?> 

