<?php
	if (empty($_dirs))	 {
		include ("$cfg_class_dir/dirs.php");
		$_dirs = new dirs;
	}
	$files = $_dirs -> lsdir($cfg_translations_dir);
	$selected = NULL;
	$lang_file = NULL;
	$curr_lang = $_COOKIE['lang'];
	if ($files) {
		foreach ($files as $file) {
			$lang_id = substr($file, 0, 1);
			$lang_abbr = substr($file, 2, 2);
			if (($lang_id == $cfg_default_lang) || ($lang_id == $curr_lang)) {
				$lang_file = $file;
			}
		}
		if (file_exists("$cfg_translations_dir/$lang_file")) {
			include ("$cfg_translations_dir/$lang_file");
		}
	}
$title = "PERTIS"
?>
<!DOCTYPE html">
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="sites/pertis/css/engine.css" rel="stylesheet" type="text/css">
<link href="sites/pertis/css/menu.css" rel="stylesheet" type="text/css">
<link href="sites/pertis/css/links.css" rel="stylesheet" type="text/css">
<link href="sites/pertis/css/layout.css" rel="stylesheet" type="text/css">
<link href="sites/pertis/css/forms.css" rel="stylesheet" type="text/css">
<link href="sites/pertis/css/tabs.css" rel="stylesheet" type="text/css">
<link href="sites/pertis/css/ibox.css" rel="stylesheet" type="text/css">
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<script type="text/javascript" src="lib/scripts/common.js"></script>
<script type="text/javascript" src="lib/scripts/ibox.js"></script>
<script type="text/javascript">
	iBox.tags_to_hide = [];
	iBox.close_label = '<?php echo BACK; ?>';
</script>
</head>
<body onunload="">
<div id="main-frame">
