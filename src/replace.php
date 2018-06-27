<?php
$row = array('task_id' => 7, 'task_reg_id' => 9);
$query = "SELECT bun from bum WHERE num = %task_id% and fum = %task_reg_id%";
echo "$query <br />";
foreach ($row as $key => $value) {
	$query = str_replace("%$key%", "$value", $query);
}
echo "$query<br />";
?>