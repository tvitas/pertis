<?php
/*
DEFAULT FORM
$comments[$table_name] -- lentelės laukų komentarai, kuriuose

weight (int)									| $s[0]
display/hide (1/0) in table view				| $s[1]
label (string)									| $s[2]
this is value from list [name=$s[3]] (string)	| $s[3]
mode (r/rw)										| $s[4]
break if next is related table (s)				| $s[5]
(any not empty or false string)
default order									| $s[6]
display/hide in form view						| $s[7]
is it password (0/1)?							| $s[8]
is it user_id (0/1)?							| $s[9]
*/

asort($comments[$table_name]);
//setcookie('old_key', trim($row[$key]));
//$tbl = $_SESSION['related_table'];
$relation_val = $_COOKIE['key'];
$dbhandle = db::connect();
$tbl = $_SESSION['current_table'];
//echo "debug table: $table, tbl: $tbl";
if ($dbhandle) {
	$rslt = pg_query("SELECT relations_relates_on_field FROM system_schema.relations WHERE relations_table = '$tbl' AND relations_relates_on_table = '$table'");
	$tyes = YES;
	$tno = NOU;
	$tselect = SELECT_COLON;
	$tchange = CHANGE_TO_COLON;
	$tnotassigned = NOT_ASSIGNED;
	$tsave = SAVE;
	$tdelete = DELETE;
	$tback = BACK;
	db::close($dbhandle);
	if ($rslt) {
		$rw = pg_fetch_assoc($rslt);
		$parent_field = $rw['relations_relates_on_field'];
	}
$pre_file = $cfg_forms_dir."/".$dir."/related_pre_".$file;
$post_file = $cfg_forms_dir."/".$dir."/related_post_".$file;
$custom_pre_file = $cfg_custom_forms_dir."/".$dir."/related_pre_".$file;
$custom_post_file = $cfg_custom_forms_dir."/".$dir."/related_post_".$file;

//echo "debug: $custom_pre_file; $custom_post_file";
if (file_exists($pre_file)) include($pre_file);
if (file_exists($custom_pre_file)) include($custom_pre_file);
}
?>
<div class="form-wrapper">
<form id="<?php echo $table_name;?>" method="POST" enctype="multipart/form-data" action="submit_related.php">
<table class="table-table">
<?php
foreach ($comments[$table_name] as $keys => $value)
{
	$s = explode('|', $comments[$table_name][$keys]);

	if ($s[7] == 1)
	{
		echo "<tr> \n";
		echo "<td class=\"td-align-right\"> \n". $this -> translate($s[2]).": </td> \n";
		echo "<td class=\"td-align-top\"> \n";
		$types = $this->get_related_field_types();
		switch ($types[$keys])
		{
			case 'boolean':
			case 'bool':
			{
				if ($row[$keys]==='f') {
					$row[$keys] = 0;
					$val = $tno;
				} else {
					$row[$keys] = 1;
					$val = $tyes;
				}
				echo "<div id=\"$keys-yes-no\" class=\"div-input div-yes-no\">$val</div>\n";
				echo "<div class=\"div-select\">\n";
				echo "<select class=\"form-select\" OnChange=\"ShowVal('$keys','$keys-yes-no',this.value, this.options[selectedIndex].text)\"> \n";
				echo "<option value=\"$row[$keys]\">$tselect</option> \n";
				echo "<option value=\"0\">$tno</option> \n";
				echo "<option value=\"1\">$tyes</option> \n";
				echo "</select> \n";
				echo "</div> \n<div class=\"break-float\"></div>\n";
				echo "<input type=\"hidden\" id=\"$keys\" name=\"$keys\" value=\"$row[$keys]\"> \n";
				break;
			}
			case 'text':
			{
				if ($s[4]=='r')
					echo "$row[$keys]";
				if ($s[4]=='rw')
					echo "<textarea name=\"$keys\" id=\"$keys\" class=\"form-textarea\">$row[$keys]</textarea> \n";
				break;
			}
			case 'xml':
			{
				$nsbp = "&nbsp;";
				$prefix = "";
				$suffix = "";
				if (!empty($row[$keys])) {
					$nsbp .= $tchange."&nbsp;";
					//$prefix = "<a href=\"$cfg_files_dir/$row[$keys]\" target=\"_new\">";
					$link = $row['file_path'].$row[$keys];
					$suffix = "</a>";
					$prefix = "<a href=\"$link\" target=\"_new\">";
				}
				echo "<div class=\"div-input $keys\">$prefix$row[$keys]$suffix</div>\n";
				echo "<span class=\"form-input-file\">$nbsp</span>";
				echo "<input class=\"form-input-file\" type=\"file\" id=\"$keys\" name=\"$keys\" value=\"$row[$keys]\">\n";
				echo "<input type=\"hidden\" id=\"file_name_control\" name=\"file_name_control\" value=\"$row[$keys]\"> \n";
				break;
			}
			default:
			{
				if ($s[4]=='r') {
						if (!empty($s[3])) {
							$si = explode(';',$s[3]);
							$val = $si[0];
							echo "<div id=\"$si[0]\" class=\"div-input $si[0]\">$row[$val] &nbsp;</div>\n";
						} else {
							echo "<div class=\"div-input\">$row[$keys] &nbsp;</div>\n";
						}
      			if (!empty($s[9])) $row[$keys] = $_SESSION['user']['id'];
						echo "<input id=\"$keys\" name=\"$keys\" class=\"form-input\" type=\"hidden\" value=\"$row[$keys]\"> \n";
					}
				if ($s[4]=='rw')
				{
					if (!empty($s[3]))
					{
/*
						$dbhandle = db::connect();
						if ($dbhandle) {
							$rt = pg_query("SELECT selects_contents FROM system_schema.selects WHERE selects_field = '$keys'");
							db::close($dbhandle);
						}
						if ($rt) {
							$rr = pg_fetch_assoc($rt);
							$selects[$keys] = ($rr['selects_contents']);
						}
*/
						$si = explode(';',$s[3]);
						$val = $si[0];
						//echo "<input class=\"form-input\" type=\"text\" id=\"$s[3]\" value=\"$row[$val]\"> \n";
						echo "<div id=\"$si[0]\" class=\"div-input $si[0]\">$row[$val] &nbsp;</div>\n";
						echo "<div class=\"div-select\">\n";
						echo "<select class=\"form-select\" OnChange=\"ShowVal('$keys','$val',this.value, this.options[selectedIndex].text)\"> \n";
						echo "<option value=\"\">Pasirinkti:</option> \n";
						echo "<option value=\"\">Nepriskirta</option> \n";
						echo "$_SESSION[$keys] \n";
						echo "</select> \n";
						echo "</div> \n<div class=\"div-select\">";
						$dbhandle = db::connect();
						if ($dbhandle) {
							$curr_table = $_SESSION['related_table'];
							$query = "SELECT selects_related_table FROM system_schema.selects WHERE selects_table = '$curr_table' AND selects_field = '$keys'";
							$MyRow = pg_fetch_assoc($result = pg_query($query));
							$MyTable = $MyRow['selects_related_table'];
							if (!empty($MyTable)) {
								echo "<a href=\"#\"	 title=\"$tadd\" OnClick = \"writecookie('','related_table','$MyTable'); writecookie('','related_recordset_action','insert'); AjaxShow(':::edit_related.php:related:'); ShowElement('related-overlay'); ShowElement('related')\"><img src=\"lib/img/recordset/table_row_insert.png\"></a>";
							}
							db::close($dbhandle);
						}
						echo "</div> \n<div class=\"break-float\"></div>\n";
						echo "<input type=\"hidden\" id=\"$keys\" name=\"$keys\" value=\"$row[$keys]\"> \n";
					} else
					{
                        $onChange = '';
                        if ($keys == 'order_recource_product_no') {
                            $onChange = 'onChange="getProductById(\'' . $keys . '\')"';
                        }
                        echo '<input id="' . $keys . '" name="' . $keys . '" class="form-input" type="text" value="' . htmlspecialchars($row[$keys]) . '"' . $onChange . '>' . "\n";
//						echo "<input id=\"$keys\" name=\"$keys\" class=\"form-input\" type=\"text\" value=\"".htmlspecialchars($row[$keys]). "\"> \n";
					}
				}
				break;
			}
		}
 		echo "</td> \n </tr> \n";
	}	else {
		if ($keys == $key) {
			echo "<input id=\"$keys\" name=\"$keys\" class=\"form-input\" type=\"hidden\" value=\"$row[$keys]\"> \n";
		}
		if ($keys == $parent_field) {
			echo "<input id=\"$keys\" name=\"$keys\" type=\"hidden\" value=\"$relation_val\"> \n";
		}
	}
}
?>
<tr>
<td class="td-align-right">
&nbsp;
</td>

<td class="td-align-top">
<?php
$sv = $tsave;
if ($_COOKIE['related_recordset_action'] == 'delete') $sv = $tdelete;
if (file_exists($post_file)) include($post_file);
if (file_exists($custom_post_file)) include($custom_post_file);
?>

<input type="submit" name="OK" id="OK" value="<?php echo $sv;?>">&nbsp;
<input type="button" value="<?php echo $tback;?>" OnClick="deletecookie('related_key'); HideElement('related'); HideElement('related-overlay'); AjaxShow(':::edit.php:forms:');">
</td>
</tr>
</table>
</form>
</div>
