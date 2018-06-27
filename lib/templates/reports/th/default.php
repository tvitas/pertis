<table class="table-print">
<thead>
<tr>
<?php 
//print_r($comments[$table_name]);
//print_r($own_style['report_details_parameter_value']);
if ($flag_enum_rows === 't') {
	echo "<th class=\"rownum\">Eil.<br />Nr.</th>\n";
}
foreach ($comments[$table_name] as $keys => $value)
{
	if (($flag_query_src_default)) {
	$s = explode('|', $comments[$table_name][$keys]);
	if ($s[1] === '1') {
		$val = $keys;
		if (!empty($s[3])) {
			$si = explode(';',$s[3]);
			$val = $si[0];
		}
		echo "<th class=\"$keys\">".$s[2]."</th>\n";
		}	
	} else {
		echo "<th class=\"$keys\">".nl2br($value)."</th>\n";
	}
}

?>
</tr>
</thead>
<tbody>
