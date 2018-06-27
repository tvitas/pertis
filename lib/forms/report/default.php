<?php 
$query = "SELECT report_id, report_title FROM reports.reports WHERE report_table = '$table' ORDER BY report_title ASC";
$select_reports = $this -> get_select_options($query);
?>
<div class="select-report-form">
<form id="<?php echo $table_name;?>" method="POST" action="report.php">
<table class="select-report-form">
<tr>
<td class="select-report-form" style="text-align: right;">
<strong>Ataskaita: </strong>
</td>
<td class="select-report-form">
<select class="form-select" OnChange = "javascript: document.cookie='repo_id = '+this.value">
<option value="">Parinkti:</option>
<?php echo "$select_reports \n"; ?>
</select>
</td>
</tr>
<tr>
<td class="select-report-form" style="text-align: right;">
<strong>Viršus: </strong>
</td>
<td class="select-report-form"> 
<textarea name="topmost" class="select-report-form"></textarea>
</td>
</tr>
<tr>
<td class="select-report-form" style="text-align: right;">
<strong>Pabaiga: </strong>
</td>
<td class="select-report-form"> 
<textarea name="undermost" class="select-report-form"></textarea>
</td>
</tr>

<tr>
<td class="select-report-form">&nbsp;</td>
<td class="select-report-form">
<input type="submit" name="OK" id="OK" value="Gerai">
<input type="button" value="Atgal" OnClick="javascript: window.close(); document.cookie='key=';">
</td>
</tr>
</table>
</form>
</div>
