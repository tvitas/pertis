<?php
/*
DEFAULT ROW
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
?>
<tr class="row">
<td class="td-table-row td-table-row-left">
<input title="<?php echo TO_MARK; ?>" style="float: left;" type="radio" name="table_radio" value="<?php echo trim($row[$key]);?>" OnClick="javascript:document.cookie='related_key='+escape(this.value)">
<?php
if ($mode=='rw') {
?>
<div title="<?php echo ACTIONS; ?>" style="min-height: 37px; padding-top: 3px; vertical-align: top;" 
OnMouseOver="javascript:document.getElementById('icon-box-rel-<?php echo $row_no; ?>').style.display = 'block'"  
OnMouseOut="javascript:document.getElementById('icon-box-rel-<?php echo $row_no; ?>').style.display = 'none';">
&nbsp;&#9661;
<div class="break-float"></div>
<div>
	<div id="icon-box-rel-<?php echo $row_no;?>" style="display: none; float: left;">
		<a href="#" OnClick="javascript:document.cookie='related_key='+escape(<?php echo trim($row[$key]);?>); writecookie('','related_table','<?php echo $_SESSION["related_table"]?>'); writecookie('','related_recordset_action','update'); AjaxShow(':::edit_related.php:related:'); ShowElement('related-overlay'); ShowElement('related')">
			<img src="lib/img/recordset/table_edit.png" title="<?php echo EDIT_ROW;?>" alt="<?php echo EDIT_ROW;?>" class="icon-box">
		</a>
		<a href="#" OnClick="javascript:document.cookie='related_key='+escape(<?php echo trim($row[$key]);?>); writecookie('','related_table','<?php echo $_SESSION["related_table"]?>'); writecookie('','related_recordset_action','delete'); AjaxShow(':::edit_related.php:related:'); ShowElement('related-overlay'); ShowElement('related')">
			<img src="lib/img/recordset/table_row_delete.png" title="<?php echo DELETE_ROW;?>" alt="<?php echo DELETE_ROW;?>" class="icon-box">
		</a>
	</div>
</div>
</div>
<?php }?>
</td>
<td class="td-table-row">
<?php echo $row_no;?>
</td>
<?php
$size_of_view_columns = sizeof($view_columns);
asort($comments[$table_name]);
foreach ($comments[$table_name] as $keys => $value)
{
	$item_class = '';
	$s = explode('|', $comments[$table_name][$keys]);
	if ($s[1] == 1)
	{	
		$row[$keys] = strip_tags($row[$keys]);
		$types = $this->get_related_field_types();
		switch ($types[$keys])
		{
			case 'boolean':
			{
				if ($row[$keys]==='f') {
					$row[$keys] = 0;
					$val = 'Ne';
				} else {
					$row[$keys] = 1;
					$val = 'Taip';
				}
				if ($size_of_view_columns > 1) {
					foreach($view_columns as $view_column) {
						if ($view_column==$keys)
							echo "<td class=\"td-table-row $keys\">".$val."</td> \n";
					}
				} else {
					echo "<td class=\"td-table-row $keys\">".$val."</td> \n";
				}
				break;
			}	
			case 'xml':
			{
					$link = $row['file_path'].$row[$keys];
					$suffix = "</a>";
					$prefix = "<a href=\"$link\" target=\"_new\">";
				//echo "debug: $cfg_files_dir";
				if ($size_of_view_columns > 1) {
					foreach($view_columns as $view_column) {
						if ($view_column==$keys)
							echo "<td class=\"td-table-row $keys\">".$prefix.$row[$keys].$suffix."</td> \n";
					}
				} else {
					echo "<td class=\"td-table-row $keys\">".$prefix.$row[$keys].$suffix."</td> \n";
				}
				break;				
			}
			case 'text': {
				$val = $row[$keys];
				$title = htmlspecialchars($val);
				$words = str_word_count($val, 0,',.":;ĄąČčĘęĖėĮįŠšŲųŪūŽž1234567890');
				//echo "debug: words - $words";
				if ($words > $cfg_max_text_words) {
					$words = str_word_count($val, 1, ',.":;ĄąČčĘęĖėĮįŠšŲųŪūŽž1234567890');	
					$val = "";
					for ($i = 0; $i <= $cfg_max_text_words; $i++ ) {
						$val .= " ".$words[$i];					
					}
					$div_id = "box$row_no$keys";
 					$val .= "<a href=\"#\" OnClick=\"javascript:iBox.showURL('#$div_id');\" title=\"Plačiau...\"> &raquo;&raquo;&raquo;</a> \n";
					echo "<div id=\"$div_id\" style=\"display: none;\"> \n";
					echo nl2br($title);
					echo "</div> \n";
				}
				if ($size_of_view_columns > 1) {
					foreach($view_columns as $view_column) {
						if ($view_column == $keys)
							echo "<td class=\"td-table-row $keys\" title=\"$title\">".nl2br($val)."</td> \n";
					}
				} else echo "<td class=\"td-table-row $keys\" title=\"$title\">".nl2br($val)."</td> \n";
				break;			
			}
			default:
			if (!empty($s[3])) {
//				$si = explode(';', $s[3]);  
				if (substr($row[$s[3]],0,1)=='*') $item_class = "option-bold";
			//$row[$s[3]] = str_replace('-','&nbsp;', $row[$s[3]]);
				$row[$s[3]] = str_replace('*','', $row[$s[3]]);
				$si = explode(';', $s[3]);
				if (empty($row[$si[0]])) $row[$si[0]] = $row[$si[1]]; 
				if ($size_of_view_columns > 1) {
					foreach ($view_columns as $view_column) {
						if ($view_column == $keys)
			 				echo "<td class=\"td-table-row $keys $si[0] $item_class\">".nl2br($row[$si[0]])."</td> \n";
			 		}
			 	} else echo "<td class=\"td-table-row $keys $si[0] $item_class\">".nl2br($row[$si[0]])."</td> \n";
			}	else { 
				if ($size_of_view_columns > 1) {
					foreach($view_columns as $view_column) {
						if ($view_column == $keys)
							echo "<td class=\"td-table-row $keys\">".nl2br($row[$keys])."</td> \n";
					}
				} else echo "<td class=\"td-table-row $keys\">".nl2br($row[$keys])."</td> \n";
			}
		}						
	}	
}
?>
</tr>