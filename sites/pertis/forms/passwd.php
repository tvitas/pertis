<?php 
	//echo $_COOKIE['lang'];
	$_dirs = new dirs;
	$files = $_dirs -> lsdir($cfg_translations_dir);
	$html = NULL;
	$selected = NULL;
	$lang_file = NULL;
	$curr_lang = $_COOKIE['lang'];
	if ($files) {
		foreach ($files as $file) {
			$lang_id = substr($file, 0, 1);
			$lang_abbr = substr($file, 2, 2);
			if (($lang_id == $cfg_default_lang) || ($lang_id == $curr_lang)) {
				$selected = "selected=\"SELECTED\"";
				$lang_file = $file;
			}
			$html .= "<option value=\"".$lang_id."\" $selected>".$lang_abbr."</option> \n";
			$selected = NULL;
			//$lang_file = NULL;
		}	

		if (file_exists("$cfg_translations_dir/$lang_file")) {
			include ("$cfg_translations_dir/$lang_file");
			//echo "$cfg_translations_dir/$lang_file";
		} 
	}

?>
<div style="margin: 15% 25%; text-align: left; padding: 5px;">
	<form action="login.php" method="post">
	<fieldset style="border: solid 1px #ccc;">
	<legend><h1><?php echo IS_TITLE; ?></h1></legend>
	<label for="u_lang"><?php echo THE_LANG_REVERSE; ?>:</label> <br />
	<select class="form-select" name="u_lang" OnChange = "javascript:document.cookie='lang='+this.value; window.location='login.php'">
	<option value=""><?php echo SELECT_COLON; ?></option>
	<?php echo $html; ?>
	</select> <br />
	<label for="u_id"><?php echo LOGIN; ?>:</label> <br />
	<input type="text" name="u_id" id="u_id" class="form-input">  <br />
	<label for="u_passwd"><?php  echo PASSWD; ?>:</label> <br />
	<input type="password" name="u_passwd" id="u_passwd" class="form-input">  <br />
	<input name="action" type="hidden" id="action" value="login">
	<label for="u_db_label">DB:</label> <br />
	<select class="form-select" OnChange = "javascript:getElementById('u_db').value = this.value;">
	<option value=""><?php echo SELECT_COLON; ?></option>
	<?php
		foreach ($cfg_dbs as $key => $value) {
			echo "<option value=\"$key\">$value</option> \n";						
		}
	?>
	</select>
	<input type="hidden" id="u_db" name="u_db" value="">
	<div id="login-error">
	<?php
		if ($_COOKIE['action'] == 'login') {
			echo "<strong>".BAD_CREDENTIALS."</strong>";
		}
	?>
	</div>
	<br />
	<input type="submit" name="Submit" value="<?php echo OK;?>" class="form-button">
	</fieldset>
	</form>
</div>
