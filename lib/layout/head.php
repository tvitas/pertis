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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
<title><?php echo IS_TITLE; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
<link href="lib/css/engine.css" rel="stylesheet" type="text/css"> 
<link href="lib/css/menu.css" rel="stylesheet" type="text/css">
<link href="lib/css/links.css" rel="stylesheet" type="text/css">
<link href="lib/css/layout.css" rel="stylesheet" type="text/css">
<link href="lib/css/forms.css" rel="stylesheet" type="text/css">
<link href="lib/css/tabs.css" rel="stylesheet" type="text/css">
<link href="lib/css/ibox.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="lib/scripts/common.js"></script>
<script type="text/javascript" src="lib/scripts/ibox.js"></script>
<script type="text/javascript">
	iBox.tags_to_hide = [];
	iBox.close_label = '<?php echo BACK; ?>';
</script>
</head>
<body>
<div id="main-frame">