<?php 
session_start();
include_once("lib/etc/site.conf");
include_once("$cfg_class_dir/db.php");
include_once("$cfg_class_dir/display.php");

$_display = new display;
$_db = new db;

$connstring='host='.'db.lva.lt'.' port='.'5432'.' dbname='.'lsmuva'.' user='.'vitas'.' password='.'kertinisakmuo';
$_SESSION["connstring"] = $connstring;

$dbhandle = $_db -> connect();


if($dbhandle) 
{
	$i = 1 ;
	$base_ip = '192.168.54.';
	while ($i<=253) {
		$ip = $base_ip.$i;
		$query = "INSERT INTO system.reserved_ip (reserved_id, reserved_ip, reserved_inuse) VALUES (DEFAULT, '$ip', 0)";
		pg_query($query);
		echo "$query <br /> \n";
		$i++;			
	}
	$_db -> close($dbhandle);
} 	
?>
