<?php
if (($flag_query_src_select_all) || ($flag_query_src_array)) {
	$tr_class = "print-row-bordered";
} else {
	$tr_class = "print-row";
} 
echo "<tr class=\"$tr_class\">\n";
if (($flag_query_src_select_all) || ($flag_query_src_array) || ($flag_enum_rows)) {
	echo "<td class=\"td-table-print-row-first\">$row_no</td>\n";
}
//print_r($row);
$i=1;
foreach ($comments[$table_name] as $keys => $value)
{
	if (($flag_query_src_select_all) || ($flag_query_src_array)) {
		$item_class = '';
		$s = explode('|', $comments[$table_name][$keys]);
		if ($s[1] == 1)
		{		
			if (!empty($s[3])) {
				if (substr($row[$s[3]],0,1)=='*') $item_class = "option-bold";
			//$row[$s[3]] = str_replace('-','&nbsp;', $row[$s[3]]);
				$row[$s[3]] = str_replace('*','', $row[$s[3]]);
				$si = explode(';', $s[3]);
				$val = trim($si[0]);
			//echo "debug: $si[0] ".$row[$val];
			 	echo "<td class=\"td-table-print-row $keys $si[0] $item_class\">".$row[$val]."</td> \n";
			} else {
				$val = $row[$keys];
				if (($types[$keys] == 'boolean') || ($types[$keys]=='bool')) {
					if ($row[$keys]==='f') {
						$row[$keys] = 0;
						$val = 'Ne';
					} else {
						$row[$keys] = 1;
						$val = 'Taip';
					}				
				}
				echo "<td class=\"td-table-print-row $keys\">".nl2br($val)."</td> \n";
			}
		}	
	} else {
		$item_class = '';
		if ($i == 1) $td_class = "td-table-print-row-first"; else $td_class = "td-table-print-row";
		if (substr($row[$keys],0,1) == '*') {
			$item_class = 'option-bold';
			$row[$keys] = str_replace('*','',$row[$keys]);
		}
		echo "<td class=\"$td_class $keys $item_class\">".nl2br($row[$keys])."</td> \n";
	}
$i++;
}
?>
</tr>

