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
<div title="<?php echo ACTIONS; ?>" style="min-height: 50px; height: 50px; max-height: 50px; padding-top: 3px; vertical-align: top;"
OnMouseOver="javascript:document.getElementById('icon-box-<?php echo $row_no; ?>').style.display = 'block'"
OnMouseOut="javascript:document.getElementById('icon-box-<?php echo $row_no; ?>').style.display = 'none';">
<input title="<?php echo TO_MARK; ?>" type="radio" name="table_radio" value="<?php echo trim($row[$key]);?>" OnClick="javascript:document.cookie='key='+escape(this.value)" style="float: left;">
&nbsp;&#9661;
<div class="break-float"></div>
<div>
	<div id="icon-box-<?php echo $row_no;?>" style="display: none; float: left;">
		<a href="#" OnClick="javascript:document.cookie='key='+escape(<?php echo trim($row[$key]);?>); writecookie('','recordset_action','update');  ShowElement('overlay'); AjaxShow(':::edit.php:forms:');">
		<img src="lib/img/recordset/table_edit.png" title="<?php echo EDIT_ROW;?>" alt="<?php echo EDIT_ROW;?>" class="icon-box">
		</a>
		<a href="#" OnClick="javascript:document.cookie='key='+escape(<?php echo trim($row[$key]);?>); writecookie('','recordset_action','delete');  ShowElement('overlay'); AjaxShow(':::edit.php:forms:');">
		<img src="lib/img/recordset/table_row_delete.png" title="<?php echo DELETE_ROW;?>" alt="<?php echo DELETE_ROW;?>" class="icon-box">
		</a>
		<a href="report.php" target="_new" OnClick="javascript:document.cookie='key='+escape(<?php echo trim($row[$key]);?>); writecookie('','recordset_action','print')">
			<img src="lib/img/recordset/printer.png" title="<?php echo TO_PRINT;?>" alt="<?php echo TO_PRINT;?>" class="icon-box">
		</a>
	</div>
</div>
</div>
</td>

<td class="td-table-row">
<?php echo $row_no;?>
</td>
<?php
asort($comments[$table_name]);
//print_r($row);
foreach ($comments[$table_name] as $keys => $value)
{
	$row[$keys] = strip_tags($row[$keys]);
	$item_class = '';
	$prefix = "";
	$suffix = "";
	$title = "";
	$s = explode('|', $comments[$table_name][$keys]);
	if ($s[1] == 1)
	{
		if (!empty($s[3])) {
			if (substr($row[$s[3]],0,1)=='*') $item_class = "option-bold";
			//$row[$s[3]] = str_replace('-','&nbsp;', $row[$s[3]]);
			$row[$s[3]] = str_replace('*','', $row[$s[3]]);
			$si = explode(';', $s[3]);
			$val = trim($si[0]);
			//$val = $this -> translate($val);
			//echo "debug: $si[0] ".$row[$val];
			 echo "<td class=\"td-table-row $keys $si[0] $item_class\">".nl2br($row[$val])."</td> \n";
		} else {
			$val = $row[$keys];
			//$val = $this -> translate($val);
			if (($types[$keys] == 'boolean') || ($types[$keys]=='bool')) {
				if ($row[$keys]==='f') {
					$row[$keys] = 0;
					$val = $this -> translate('Ne');
				} else {
					$row[$keys] = 1;
					$val = $this -> translate('Taip');
				}
			}
			if ($types[$keys]=='xml') {
					$link = $row['file_path'].$row[$keys];
					$suffix = "</a>";
					$prefix = "<a href=\"$link\" target=\"_new\">";
			}
			if ($types[$keys]=='text') {
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
			}
			echo "<td class=\"td-table-row $keys\" title=\"$title\">".$prefix.nl2br($val).$suffix."</td> \n";
		}
	}
}
?>
</tr>

