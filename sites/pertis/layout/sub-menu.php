<?php 
	
?>
<table class="table-related-menu">
<tr>

<td class="td-menu" style="text-align: center; width: 20px;">
	<a href="#" OnClick="writecookie('','related_table','<?php echo $_SESSION["related_table"]?>'); writecookie('','related_recordset_action','delete'); AjaxShow(':::edit_related.php:related:'); ShowElement('related-overlay'); ShowElement('related')">
		<img src="lib/img/recordset/table_row_delete.png" title="<?php echo DELETE_ROW; ?>" alt="<?php echo DELETE_ROW; ?>" class="menu">
	</a>
</td>


<td class="td-menu" style="text-align: center; width: 20px;">
	<a href="#" OnClick="writecookie('','related_table','<?php echo $_SESSION["related_table"]?>'); writecookie('','related_recordset_action','insert'); AjaxShow(':::edit_related.php:related:'); ShowElement('related-overlay'); ShowElement('related')">
	 	<img src="lib/img/recordset/table_row_insert.png" title="<?php echo INSERT_ROW; ?>" alt="<?php echo INSERT_ROW; ?>" class="menu">
	</a>
</td>


<td class="td-menu" style="text-align: center; width: 20px; ">
	<a href="#" OnClick="writecookie('','related_table','<?php echo $_SESSION["related_table"]?>'); writecookie('','related_recordset_action','update'); AjaxShow(':::edit_related.php:related:'); ShowElement('related-overlay'); ShowElement('related')">
		<img src="lib/img/recordset/table_edit.png" title="<?php echo EDIT_ROW; ?>" alt="<?php echo EDIT_ROW; ?>" class="menu">
	</a>
</td>


<td class="td-menu" style="text-aign: center; width: 20px;">
	<a target="_blank" href="user_guide_related_tables.php" title="<?php echo HELP; ?>">
		<img src="lib/img/recordset/help.png" title="Pagalba" alt="Pagalba" class="menu">
	</a>
</td>
</tr>
</table>

