<table class="table-table-menu">
<tr>

<td class="td-table-menu">
  <a href="#" OnClick = "writecookie('','recordset_action','delete'); ShowElement('overlay'); AjaxShow(':::edit.php:forms:');">
	<img src="lib/img/recordset/table_row_delete.png" title="<?php echo DELETE_ROW;?>" alt="<?php echo DELETE_ROW;?>" class="menu">
	</a>
</td>

<td class="td-table-menu">
  <a href="#" OnClick="writecookie('','recordset_action','insert'); ShowElement('overlay'); AjaxShow(':::edit.php:forms:');">
 	<img src="lib/img/recordset/table_row_insert.png" title="<?php echo INSERT_ROW;?>" alt="<?php echo INSERT_ROW;?>" class="menu" >
	</a>
</td>


<td class="td-table-menu">
  <a href="#" OnClick="javascript:writecookie('','recordset_action','copy'); ShowElement('overlay'); AjaxShow(':::edit.php:forms:');">
 	<img src="lib/img/recordset/page_copy.png" title="<?php echo COPY_ROW;?>" alt="<?php echo COPY_ROW;?>" class="menu">
	</a>
</td>

<td class="td-table-menu">
  <a href="#" OnClick="writecookie('','recordset_action','update');  ShowElement('overlay'); AjaxShow(':::edit.php:forms:');">
	<img src="lib/img/recordset/table_edit.png" title="<?php echo EDIT_ROW;?>" alt="<?php echo EDIT_ROW;?>" class="menu">
	</a>
</td>


<td class="td-table-menu">
  <a href="#" OnClick="writecookie('','recordset_action','browse');  ShowElement('overlay'); AjaxShow(':::view.php:forms:');">
	<img src="lib/img/recordset/application_view_detail.png" title="<?php echo BROWSE;?>" alt="<?php echo BROWSE;?>" class="menu">
	</a>
</td>



<td class="td-table-menu">
  <a href="#" OnClick="javascript: ToogleElement('filter_div');">
	<img src="lib/img/recordset/page_find.png" title="<?php echo TO_FILTER;?>" alt="<?php echo TO_FILTER;?>" class="menu">
	</a>
</td>

<!--
<td class="td-table-menu">
  <a href="#">
	<img src="lib/img/recordset/table_sort.png" title="Rikiuoti" alt="Rikiuoti" class="menu" OnClick="javascript:writecookie('','recordset_action','sort'); AjaxShow(':::sort.php:contents:')">
	</a>
</td>
-->
<td class="td-table-menu">
	<a href="report.php" target="_new" OnClick="javascript:writecookie('','recordset_action','print')">
	<img src="lib/img/recordset/printer.png" title="<?php echo TO_PRINT;?>" alt="<?php echo TO_PRINT;?>" class="menu">
	</a>
</td>

</tr>
</table>


