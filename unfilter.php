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
	if (!empty($_COOKIE['current_table'])) {
		if ($_SESSION['current_table'] != $_COOKIE['current_table']) {
			$_SESSION['current_table'] = $_COOKIE['current_table'];
		}
	}
	if ($_COOKIE['select_filter']) {
		setcookie('select_filter', NULL, time()-3600);
	}
	header('Location: main.php');
?> 

