<?php 
/*
DEFAULT FORM 
$comments[$table_name] -- lentelės laukų komentarai, kuriuose
                                                                                          
weight (int)										| $s[0] 
display/hide (1/0) in table view					| $s[1]  
label (string)										| $s[2]
this is value from list [name=$s[3]] (string)		| $s[3]
mode (r/rw)											| $s[4]
break if next is related table (s)					| $s[5]
(any not empty or false string)
default order										| $s[6]
display/hide in form view							| $s[7]
is it password (0/1)?								| $s[8]
is it user_id (0/1)?								| $s[9]
*/
asort($comments[$table_name]);
//setcookie('old_key', trim($row[$key])); 
$tbl = trim($_SESSION['current_table']);
$is_disabled_flag = false;
$display_submit_button = true; 
if ($row[$_SESSION['user_rights'][$tbl]['accepts_column']] == 't') $is_disabled_flag = true;
$tyes = YES;
$tno = NOU;
$tselect = SELECT_COLON;
$tchange = CHANGE_TO_COLON;
$tnotassigned = NOT_ASSIGNED;
$tsave = SAVE;
$tdelete = DELETE;
$tback = BACK;
$tadd = LIST_ADD;
$pre_file = $cfg_forms_dir."/".$dir."/pre_".$file;
$post_file = $cfg_forms_dir."/".$dir."/post_".$file;
$custom_pre_file = $cfg_custom_forms_dir."/".$dir."/pre_".$file;
$custom_post_file = $cfg_custom_forms_dir."/".$dir."/post_".$file;
//echo "debug: $pre_file; $post_file "; 
if (file_exists($pre_file)) include($pre_file);
if (file_exists($custom_pre_file)) include($custom_pre_file);
//echo "debug: ".$_SESSION['user_rights'][$tbl]['accepts_column']." $tbl"; 
?>
<div class="form-wrapper">
<form id="<?php echo $table_name;?>" method="POST" enctype="multipart/form-data" action="submit.php">
<table class="table-table">
<?php 
switch($_COOKIE['recordset_action']) {
case 'delete': {
	foreach ($comments[$table_name] as $keys => $value) {
		$s = explode('|', $comments[$table_name][$keys]);
		if ($s[7] == 1)
		{
			$si = explode(';',$s[3]);
			$val = $si[0];
			if ($s[8]) {
				$row[$keys] = str_replace($row[$keys], str_repeat('*', strlen($row[$keys])-4), $row[$keys]);
			}			
			echo "<tr> \n"; 		
			echo "<td class=\"td-align-right\"> \n". $this -> translate($s[2]).": </td> \n";
			echo "<td class=\"td-align-top\"> \n";
			echo "<div id=\"$si[0]\" class=\"div-input $si[0]\">";
			if (!empty($s[3])) {
				echo $row[$val];
				echo "<input id=\"$val\" name=\"$val\" class=\"form-input\" type=\"hidden\" value=\"$row[$val]\"> \n";
			} else { 
				echo $row[$keys];
				echo "<input id=\"$keys\" name=\"$keys\" class=\"form-input\" type=\"hidden\" value=\"$row[$keys]\"> \n";
			}
			echo "</div> \n</td>\n</tr>\n";			
	  } 
	  else {
			if ($keys == $key) {
				echo "<input id=\"$keys\" name=\"$keys\" class=\"form-input\" type=\"hidden\" value=\"$row[$keys]\"> \n";
			}	
		}
	}
	break;
}	
default: 
{
	foreach ($comments[$table_name] as $keys => $value) {
		$s = explode('|', $comments[$table_name][$keys]);
		if ($s[7] == 1)
		{
			echo "<tr> \n"; 		
			echo "<td class=\"td-align-right\"> \n". $this -> translate($s[2]).": </td> \n";
			echo "<td class=\"td-align-top\"> \n";
			$row[$keys] = trim($row[$keys]);
			$types = $this->get_field_types();
//			echo "debug: types ".$types[$keys]; 
			switch ($types[$keys])
			{
				case 'is_closed_control': {
					if ($row[$keys]==='f') {
						$row[$keys] = 0;
						$val = $tno;
					} else {
						$row[$keys] = 1;
						$val = $tyes;
					}
					$disa = 'disabled = \"disabled\"';
					if (($_SESSION['user_rights'][$tbl]['accepts'] === 't') && ($_SESSION['user_rights'][$tbl]['accepts_column']===$keys)) {
						$disa = '';
					}				
					echo "<div id=\"yes-no-$keys\" class=\"div-input div-yes-no\">$val</div>\n";
					echo "<div class=\"div-select\">\n";
					echo "<select class=\"form-select\" $disa OnChange=\"ShowVal('$keys','yes-no-$keys',this.value, this.options[selectedIndex].text)\"> \n";
					echo "<option value=\"$row[$keys]\">".$tselect."</option> \n";
					echo "<option value=\"0\">".$tno."</option> \n";
					echo "<option value=\"1\">".$tyes."</option> \n";
					echo "</select> \n";			
					echo "</div> \n<div class=\"break-float\"></div>\n";
					echo "<input type=\"hidden\" id=\"$keys\" name=\"$keys\" value=\"$row[$keys]\"> \n";			
					//echo "debug: acc ".$_SESSION['user_rights'][$tbl]['accepts'].", col ".$_SESSION['user_rights'][$tbl]['accepts_column'].", disa $disa"; 
					break;				
				}
				case 'xml':
				{
					$nbsp = "&nbsp;";
					$prefix = "";
					$suffix = "";
					if (!empty($row[$keys])) {
						$nbsp .= $tchange."&nbsp;";
						$link = $row['file_path'].$row[$keys];
						$suffix = "</a>";
						$prefix = "<a href=\"$link\" target=\"_new\">";
					}
					echo "<div class=\"div-input $keys\">$prefix$row[$keys]$suffix</div>\n";
					echo "<span class=\"form-input-file\">$nbsp</span>";
					echo "<input class=\"form-input-file\" type=\"file\" id=\"$keys\" name=\"$keys\">\n";
					echo "<input type=\"hidden\" id=\"file_name_control\" name=\"file_name_control\" value=\"$row[$keys]\"> \n";
					break;				
				}
				case 'boolean':
				case 'bool':
				{
					if (($row[$keys]=='f') || (empty($row[$keys]))) {
						$row[$keys] = 0;
						$val = $tno;
					} else {
						$row[$keys] = 1;
						$val = $tyes;
					}				
					$disa = "disabled = \"disabled\"";
					if ($_SESSION['user']['id'] == 1) $disa = '';
					if ($_SESSION['db_admin'] == true) $disa = '';
					if (($_SESSION['user_rights'][$tbl]['accepts'] == 't') && ($_SESSION['user_rights'][$tbl]['accepts_column'] == $keys))
					$disa = '';
					if (($_SESSION['user_rights'][$tbl]['accepts'] == 't') && (empty($_SESSION['user_rights'][$tbl]['accepts_column']))) 
					$disa = '';
					echo "<div id=\"yes-no-$keys\" class=\"div-input div-yes-no\">$val</div>\n";
					echo "<div class=\"div-select\">\n";
					echo "<select class=\"form-select\" $disa OnChange=\"ShowVal('$keys','yes-no-$keys',this.value, this.options[selectedIndex].text)\"> \n";
					echo "<option value=\"$row[$keys]\">$tselect</option> \n";
					echo "<option value=\"0\">$tno</option> \n";
					echo "<option value=\"1\">$tyes</option> \n";
					echo "</select> \n";			
					echo "</div> \n<div class=\"break-float\"></div>\n";
					echo "<input type=\"hidden\" id=\"$keys\" name=\"$keys\" value=\"$row[$keys]\"> \n";
					if (!empty($s[5]))
					{
						if (($_COOKIE['recordset_action'] == 'update') || ($_COOKIE['recordset_action'] == 'browse'))
						{
							$this -> disp_related_table_in_form($row);
						}
					}
					break;
				}			
				case 'text':
				{
					if ($s[4]=='r') 
						echo "<div class=\"div-input-text\">".nl2br($row[$keys])."</div>\n";
						echo "<input id=\"$keys\" name=\"$keys\" class=\"form-input\" type=\"hidden\" value=\"$row[$keys]\"> \n";
					if ($s[4]=='rw') 
						echo "<textarea name=\"$keys\" id=\"$keys\" class=\"form-textarea\">$row[$keys]</textarea> \n";
						if (!empty($s[5]))
						{
							if (($_COOKIE['recordset_action'] == 'update') || ($_COOKIE['recordset_action'] == 'browse'))
							{
								$this -> disp_related_table_in_form($row);
							}
						}
					break;
				}
				default:
				{
					if ($s[4]=='r') {
						if (!empty($s[3])) {
							$si = explode(';',$s[3]);
							$val = $si[0];
							echo "<div id=\"$si[0]\" class=\"div-input $si[0]\">$row[$val] &nbsp;</div>\n";
							if (!empty($s[5]))
							{
								echo "<div class=\"break-float\"></div>\n";
								if (($_COOKIE['recordset_action'] == 'update') || ($_COOKIE['recordset_action'] == 'browse'))
								{
									$this -> disp_related_table_in_form($row);
								}
							}
						} else {
							echo "<div id=\"$keys\" class=\"div-input $keys\">stripslashes($row[$keys]) &nbsp;</div>\n";
						} 
						if (!empty($s[9])) $row[$keys] = $_SESSION['user']['id'];
						echo "<input id=\"$keys\" name=\"$keys\" class=\"form-input\" type=\"hidden\" value=\"$row[$keys]\"> \n";
					}
					if ($s[4]=='rw') 
					{			
						if (!empty($s[3]))
						{
							$si = explode(';',$s[3]);
							$val = $si[0];
						//echo "<input class=\"form-input\" type=\"text\" id=\"$s[3]\" value=\"$row[$val]\"> \n"; 
							echo "<div id=\"$si[0]\" class=\"div-input $si[0]\">$row[$val] &nbsp;</div>\n";
							echo "<div class=\"div-select\">\n";
							echo "<select class=\"form-select\" OnChange=\"javascript:ShowVal('$keys','$val',this.value, this.options[selectedIndex].text)\"> \n";
							echo "<option value=\"\">Pasirinkti:</option> \n";
							echo "<option value=\"\">Nepriskirta</option> \n";
							//echo "$selects[$keys] \n"; 
							echo "$_SESSION[$keys] \n";
							echo "</select> \n";			
							echo "</div> \n<div class=\"div-select\"> \n";
							$dbhandle = db::connect(); 
							if ($dbhandle) {
								$curr_table = $_SESSION['current_table'];								
								$query = "SELECT selects_related_table FROM system_schema.selects WHERE selects_table = '$curr_table' AND selects_field = '$keys'";
								$MyRow = pg_fetch_assoc($result = pg_query($query));
								$MyTable = $MyRow['selects_related_table'];
								if (!empty($MyTable)) {
									echo "<a href=\"main.php\"	 
									title=\"$tadd\" 
									OnClick = \"writecookie('','related_table','$MyTable'); 
									writecookie('','related_recordset_action','insert');\">
									<img src=\"lib/img/recordset/table_row_insert.png\" class=\"emenu\">
									</a> \n"; 
								}
								db::close($dbhandle);
							}
							echo "</div> \n<div class=\"break-float\"></div>\n";
							echo "<input type=\"hidden\" id=\"$keys\" name=\"$keys\" value=\"$row[$keys]\"> \n";			
						} else
						{					 
							if ($s[8]) {
								echo "<input id=\"$keys\" name=\"$keys\" class=\"form-input\" type=\"password\" value=\"$row[$keys]\"> \n";
							} else {
								echo "<input id=\"$keys\" name=\"$keys\" class=\"form-input\" type=\"text\" value=\"".htmlspecialchars($row[$keys])."\"> \n";
							}
						}
						if (!empty($s[5]))
						{
							if (($_COOKIE['recordset_action'] == 'update') || ($_COOKIE['recordset_action'] == 'browse'))
							{
								$this -> disp_related_table_in_form($row);
							}
						}
					}
					break;
				}
			}
 			echo "</td> \n </tr> \n";
		}	
		else {
			if ($keys == $key) {
				echo "<input id=\"$keys\" name=\"$keys\" class=\"form-input\" type=\"hidden\" value=\"$row[$keys]\"> \n";
			}	
		}
	}
	break;
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
$disa = '';
if ($_COOKIE['recordset_action'] == 'delete') $sv = $tdelete;
if (($is_disabled_flag) ||  
	($_COOKIE['current_table'] == 'reports.db_events') || 
	($_SESSION['user_rights'][$table]['write'] === 'f')) {
		$display_submit_button = false;
}
//echo "debug: {$_SESSION['user_rights'][$table]['write']}";
//echo "debug: accc ".$row[$_SESSION['user_rights'][$tbl]['accepts_column']];  
if (file_exists($post_file)) include($post_file);
if (file_exists($custom_post_file)) include($custom_post_file);
if ($display_submit_button) {
?>
<input type="submit" name="OK" id="OK" value="<?php echo $sv;?>" <?php echo $disa; ?> >&nbsp;
<?php }?> 
<input type="submit" name="CANCEL" value="<?php echo $tback;?>">
 
</td>
</tr>
</table>
</form>
</div>