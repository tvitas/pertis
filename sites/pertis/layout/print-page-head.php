<?php
$dbhandle = NULL;
$was_connected = TRUE;
if ($repo_id) {
	if (!$_SESSION['connected']) {
		$dbhandle = db::connect();
		$was_connected = FALSE;
	} else {
		$dbhandle = $_SESSION['dbhandle'];
	}
	if($dbhandle) {
		$query = "SELECT report_css FROM reports.reports WHERE report_id = $repo_id";
		$css_file = pg_fetch_assoc(pg_query($query));
	}
	if (!$was_connected) {
		db::close($dbhandle);
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html lang="lt">
<head>
<title>PERTIS</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="sites/pertis/css/print.css" rel="stylesheet" type="text/css">
<?php
if ($css_file['report_css']) {
	echo "<link href=\"sites/pertis/css/{$css_file['report_css']}\" rel=\"stylesheet\" type=\"text/css\"> \n";
}
?>
</head>
<body>
