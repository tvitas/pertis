<?php
	session_start();
	include('lib/etc/site.conf');
	include('lib/scripts/class/display.php');
	$msg = $_GET['msg'];
	if (empty($msg)) $msg = 'Minutę...';
	$_display = new display;
	$msg = $_display -> translate($msg);
	if ($_SESSION['instance']) $_SESSION['instance'] = false;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html lang="lt">
<head>
<title>Užduotys LSMU</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
<link href="lib/css/layout.css" rel="stylesheet" type="text/css">
<link href="lib/css/engine.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="lib/scripts/spin.min.js"></script>
</head>
<body>
<div class="overlay" style="visibility: visible;"></div>
<div id="wait" class="wait">
<script type="text/javascript">
var opts = {
  lines: 20, // The number of lines to draw
  length: 4, // The length of each line
  width: 5, // The line thickness
  radius: 22, // The radius of the inner circle
  corners: 1, // Corner roundness (0..1)
  rotate: 30, // The rotation offset
  direction: 1, // 1: clockwise, -1: counterclockwise
  color: '#666', // #rgb or #rrggbb or array of colors
  speed: 1.2, // Rounds per second
  trail: 90, // Afterglow percentage 48
  shadow: false, // Whether to render a shadow
  hwaccel: false, // Whether to use hardware acceleration
  className: 'spinner', // The CSS class to assign to the spinner
  zIndex: 2e9, // The z-index (defaults to 2000000000)
  top: 'auto', // Top position relative to parent in px
  left: 'auto' // Left position relative to parent in px
};
var target = document.getElementById('wait');
var spinner = new Spinner(opts).spin(target);
</script>
<p class="wait"><?php echo $msg;?></p>
</div>
</body>
</html>
<script type="text/javascript">
  window.location="main.php";
</script>
