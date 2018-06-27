<?php 
global $cfg_default_rows_page;
$title = $_display -> get_page_title();

$select_rows = $_COOKIE['select_rows'];
if (empty($select_rows)) {
	$select_rows = $cfg_default_rows_page;
}
?>
<div class="menu-container">
<div class="div-page-title">
<a href="#" title="<?php echo QUIT;?>" OnClick="javascript:writecookie('menu','action','logout'); window.location='login.php'"><img src="lib/img/recordset/cross.png" title="<?php echo QUIT;?>" alt="<?php echo QUIT;?>" class="menu" style="vertical-align: middle;"></a>
<?php  echo "$title";?>
</div>

<div class="div-menu-bar">
<?php echo THE_LANG_REVERSE."&nbsp;";?>
<select id="select_lang" title="<?php echo THE_LANG_REVERSE;?>" OnChange="document.cookie='lang='+getElementById('select_lang').value; window.location='splash.php?msg=<?php echo CHANGE_THE_LANG_REVERSE; ?>'">
<option value="">...</option>
<?php
	echo $_SESSION['select_lang'];
?>
</select>

<?php echo THE_TABLE."&nbsp;";?>
<select id="select_table" title="<?php echo THE_TABLE;?>" OnChange="AjaxShow('menu:current_table:'+this.value+':recordset.php:contents:select_rows:'+getElementById('select_rows').value)">
<option value="">...</option>
<?php
	echo $_SESSION['select_tables'];
?>
</select>
<?php echo SHOW."&nbsp;";?>
<select id="select_rows" title="<?php echo SHOW_ROWS;?>" OnChange="document.cookie='select_rows='+escape(this.value); AjaxShow(':::recordset.php:contents:::')">
<option value="">...</option>
<?php
foreach (range(2, 64, 2) as $i) {
	$SELECTED = '';
	if ($i == $select_rows) {
		$SELECTED = " selected=\"SELECTED\"";
	}
	echo "<option value=\"$i\"$SELECTED>$i</option>\n";
};
?>
</select>
<?php  echo ROWS_IN_PAGE."&nbsp;";?>
<a href="splash.php?msg=<?php echo REFRESHING;?>" title="<?php echo REFRESH;?>"><img src="lib/img/recordset/table_refresh.png" title="<?php echo REFRESH;?>" alt="<?php echo REFRESH;?>" class="menu"><?php echo REFRESH;?></a>
<a target="_blank" href="user_guide.php" title="<?php echo HELP;?>"><img src="lib/img/recordset/help.png" title="<?php echo HELP;?>" alt="<?php echo HELP;?>" class="menu"><?php echo HELP;?></a>
<a href="#" title="<?php echo QUIT;?>" OnClick="javascript:writecookie('menu','action','logout'); window.location='login.php'"><img src="lib/img/recordset/door_out.png" title="<?php echo QUIT;?>" alt="<?php echo QUIT;?>" class="menu"><?php echo QUIT;?></a>
</div>
</div>