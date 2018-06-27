<?php
$dbhandle = NULL;
$was_connected = TRUE;
if (!$_SESSION['connected']) {
	$dbhandle = db::connect();
	$was_connected = FALSE;	
} else {
	$dbhandle = $_SESSION['dbhandle'];		
}
if ($dbhandle) {
	pg_query("SELECT pert.task_recources_2_store_docs({$_COOKIE['key']})");
	$pre_error = pg_last_error($dbhandle);
	$pre_message = pg_last_notice($dbhandle);
	if ($pre_message) $_SESSION['messages'] .= $pre_message;
	if ($pre_error) $_SESSION['errors'] .= $pre_error;
}
if (!$was_connected) {
	db::close($dbhandle);		
} 
?>