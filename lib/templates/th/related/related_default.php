<!-- <form id="<?php echo $table_name;?>">  --> 
<table class="table-table">
<tr>
<td class="td-table-head td-table-head-left">&nbsp;</td>
<td class="td-table-head">#</td>
<?php 
//print_r($comments[$table_name]);
asort($comments[$table_name]);
foreach ($comments[$table_name] as $keys => $value)
{
	$s = explode('|', $comments[$table_name][$keys]);
	if (sizeof($view_columns) > 1) {
		foreach($view_columns as $view_column) {
			if (($s[1] == 1) && ($view_column == $keys)) {
				echo "<td class=\"td-table-head $keys\">".$this -> translate($s[2])."</td>";
			}  
		}	
	}	else { 
		if ($s[1] == 1) echo "<td class=\"td-table-head $keys\">".$this -> translate($s[2])."</td>";
	} 
}
?>
</tr>