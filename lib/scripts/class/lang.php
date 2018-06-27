<?php
include_once("lib/scripts/class/dirs.php");
class lang {	

	public function get_lang_file() 
	{
		global $cfg_class_dir;
		global $cfg_translations_dir;
		$_dirs = new dirs;
		$files = $_dirs -> lsdir($cfg_translations_dir);
		$selected = NULL;
		$lang_file = NULL;
		$curr_lang = $_COOKIE['lang'];
		if ($files) {
			foreach ($files as $file) {
				$lang_id = substr($file, 0, 1);
				$lang_abbr = substr($file, 2, 2);
				if ($lang_id == $curr_lang) {
					$lang_file = $file;
					break;
				}
			}	
		}
		return $lang_file;
	}
}
?>