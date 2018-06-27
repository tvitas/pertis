<div id="filter_div" name="filter_div" style="display: none;">
<?php 
$this -> disp_msg(TO_FILTER, 'toogle', '');
$search = array('inistruct_structure_fullname', 
'effstruct_structure_fullname', 
'initiator_user_attribute_value', 
'effector_user_attribute_value', 
'changers_user_login');

$replace = array('inistruct.structure_fullname', 
'effstruct.structure_fullname', 
'initiator.user_attribute_value', 
'effector.user_attribute_value', 
'changers.user_login');
$select_filter = str_replace($search, $replace, $select_filter);
?>
<form id="<?php echo $table_name;?>_filter" action="submit.php" method="POST">
<table class="table-table">
<tr>
<td class="td-align-top-auto">
<?php echo A_COLUMN; ?>
</td>

<td class="td-align-top-auto">
<?php echo A_VALUE; ?>
</td>

<td class="td-align-top-auto">
<?php echo AN_OPERATOR; ?>
</td>
</tr>

<tr>
<td class="td-align-top-auto">
<select class="filter-select" id="filter_column" 
OnChange="javascript: prep_sql({'tag_to_show': 'filter_sql', 'tag_to_glue': 'sql', 'glue_value': this.value, 
'glue_text': this.options[this.selectedIndex].text, 'option_type': 1}); this.value = '';">
<option value="">...</option>
<option value=""><?php echo SELECT_COLON;?></option>
<?php echo $select_filter;?>
</select>
</td>

<td class="td-align-top-auto">
<input type="text" class="filter-input" id="filter_value" name="filter_value" OnChange="javascript: prep_sql({'tag_to_show': 'filter_sql', 'tag_to_glue': 'sql', 'glue_value': this.value, 
'glue_text': '', 'option_type': 2}); this.value = '';">
</td>

<td class="td-align-top-auto">
<select class="filter-select" id="filter_operator" OnChange="javascript: prep_sql({'tag_to_show': 'filter_sql', 'tag_to_glue': 'sql', 'glue_value': this.value, 
'glue_text': this.options[this.selectedIndex].text, 'option_type': 3}); this.value = '';">
<option value="">...</option>
<option value="AND"><?php echo L_AND;?></option>
<option value="OR"><?php echo L_OR;?></option>
</select>
</td>
</tr>

<tr>
<td colspan="3">
<div id="filter_sql" name="filter_sql" class="div-sql">
</div>
<input type="hidden" id="sql" name="sql" value="">
</td>
</tr>

<tr>
<td class="td-align-top-auto" colspan="3">
	<input type="submit" name="OK" id="OK" value="<?php echo OK;?>" OnClick="javascript: writecookie('','recordset_action','filter')">
	<input type="button" name="clear_button" id="clear_button" value="<?php echo STARTOVER;?>" 
	OnClick="javascript: document.getElementById('filter_sql').innerHTML = ''; 
	document.getElementById('sql').value = '';">
</td>
</tr>
</table>
</form> 
</div>
