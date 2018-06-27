<?php
	session_start(); 
	$key_test = $_COOKIE['session_key'];
	if (empty($key_test) || ($key_test != $_SESSION['key']))
	{
		session_destroy();
		header("Location: login.php");
		header("Pragma: no-cache");
		exit;
	}
	include_once("lib/etc/site.conf");
	include_once("$cfg_class_dir/forms.php");
	include_once("$cfg_class_dir/lang.php");
	$_lang = new lang;
	$_forms = new forms;
	$_db = new db;
	$_display = new display;	
	$lang_file = $_lang -> get_lang_file();
	if (file_exists("$cfg_translations_dir/$lang_file") && ($lang_file)) {
			include ("$cfg_translations_dir/$lang_file");
	} 
	$_SESSION['messages'] = "";
	$_SESSION['errors'] = "";
	$_SESSION['pre_submit'] = true;
	$_SESSION['post_submit'] = true;
	$tselect_table = SELECT_TABLE;
	$tsave = SAVE;
	$tdelete = DELETE;
	if (!empty($_COOKIE['related_table']))
	{
		if (!empty($_COOKIE['related_recordset_action']))
		{
			$submit = $_POST['OK'];
			if (($submit == $tsave) || ($submit == $tdelete))
			{
				switch ($_COOKIE['related_recordset_action'])
				{
					case 'insert':
					case 'copy':
					case 'delete':
					case 'update':				
					{						
						$table_string = db::get_table_string($_COOKIE['related_table']);
						$pre_submit = $post_submit = $table_string;
						if(!empty($pre_submit)) 
						{
							$pre_submit = "submit/pre_".$pre_submit.".php";
							if(file_exists($cfg_process_dir.'/'.$pre_submit)) include($cfg_process_dir.'/'.$pre_submit);
							if(file_exists($cfg_custom_process_dir.'/'.$pre_submit)) include($cfg_custom_process_dir.'/'.$pre_submit);
						}
						if ($_SESSION['pre_submit']) {
							$query = $_forms->get_related_query();
							$dbhandle = $_db->connect();
							if (!empty($dbhandle))
							{			
								//echo $query;			 				
			 					@pg_query($query);
								$pre_error = pg_last_error($dbhandle);
								$pre_message = pg_last_notice($dbhandle);
								if ($pre_message) $_SESSION['messages'] .= $pre_message;
								if ($pre_error) $_SESSION['errors'] .= $pre_error;
							}
						}
						if (!empty($post_submit))
						{
							$post_submit = "submit/post_".$post_submit.".php";
							if(file_exists($cfg_process_dir.'/'.$post_submit)) include($cfg_process_dir.'/'.$post_submit);
							if(file_exists($cfg_custom_process_dir.'/'.$post_submit)) include($cfg_custom_process_dir.'/'.$post_submit);							
						}
						if (!empty($dbhandle)) $_db->close($dbhandle);
						//echo $_SESSION['connected'];
						//print_r($_SESSION['dbhandle']);
						//print_r($_SERVER['SCRIPT_NAME']);
						if ($_SESSION['post_submit']) {
							$_display->update_selects_cache();
						}	
						$log = $_forms->get_log_string();
						$log .=  '|'.$_COOKIE['recordset_action'];
						$log .='|'.$_SESSION['using_db'];
						$_forms->write_log_string($log);
						//echo $_SESSION['sysconfig']['log_db'];
						if ($_SESSION['sysconfig']['log_db']=='Taip') {
							$log .= "|".preg_replace("/\s+/S", " ", $query);
							$records = explode('|', $log);
							$db_timestamp = $records[0];
							$db_host_ip = $records[1];
							$db_user = $records[2];
							$db_parent_table = "";
							$db_related_table = $records[4];
							$db_action = $records[5];
							$db_db = $records[6];
							$db_query = addslashes($records[7]);
							$query = "INSERT INTO reports.db_events 
												(db_event_timestamp, db_event_host_ip, db_event_user, db_event_parent_table, db_event_related_table, db_event_action, db_event_query, db_event_db) 
												VALUES ('$db_timestamp', '$db_host_ip', '$db_user', '$db_parent_table', '$db_related_table', '$db_action', '$db_query', '$db_db')";
							//echo $query;
							$dbhandle = $_db->connect();
							if (!empty($dbhandle)) {
								@pg_query($query);
								$_db->close($dbhandle);
							}
						}
						break;
					}
				}
			}

		} else $_SESSION['errors'] = MISSING_COOKIE;
	}	else $_SESSION['errors'] = $tselect_table;
	//var_dump($cfg_process_dir);
	//var_dump($cfg_custom_process_dir);
	//var_dump($pre_submit);
	//var_dump($post_submit);
	setcookie("related",true);
	header ("Location: main.php");
?>
