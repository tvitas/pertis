<?php 
$tnr = No;
$tsort = SORT;
$ttitle = NOT_SORT;
$tttitle = FIND;
?>
<table class="table-table">
<tr>
<td class="td-table-head td-table-head-left">
<a href="unfilter.php" title="<?php echo UNFILTER ;?>"><img src="lib/img/recordset/zoom_out.png" alt="<?php echo UNFILTER ;?>" class="menu"></a>
</td>
<td class="td-table-head" style="vertical-align: middle;"><a href="main.php" class="link-table-head" title="<?php echo $ttitle;?>" OnClick="javascript: deletecookie('select_order'); deletecookie('order_dir')"><?php echo $tnr;?></a></td>
<?php 
asort($comments[$table_name]);
$_SESSION['select_order'] = $_COOKIE['select_order'];
foreach ($comments[$table_name] as $keys => $value)
{
	$s = explode('|', $comments[$table_name][$keys]);
	if ($s[1] === '1') {
		$val = $keys;
		if (!empty($s[3])) {
			$si = explode(';',$s[3]);
			$val = $si[0];
		}
		$is_timestamp = $this -> check_is_timestamp($types[$val]);
		//echo "debug: $is_timestamp ";
		if ($is_timestamp) $sort_column = trim($val)."::timestamp(0)"; else $sort_column = $val;
		echo "<td class=\"td-table-head $val\"><a href=\"sort_table.php\" title=\"$tsort\" class=\"link-table-head\" OnClick=\"javascript:document.cookie='select_order = $sort_column'; document.cookie='recordset_action=sort'\">".$this->translate($s[2])."</a><br /> \n";
		echo "<form id=\"form_$val\" method=\"POST\" action=\"submit.php\"> \n";
		echo "<input type=\"text\" name=\"filter_$val\" class=\"form-input-table-head\" title=\"$tttitle\" OnChange=\"javascript:document.cookie='search_field='+escape(this.name); document.cookie='recordset_action=find'; this.form.submit()\"> \n";
		echo "</form> \n </td> \n"; 
	}	
}
?>
</tr>