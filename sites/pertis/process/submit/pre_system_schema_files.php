<?php
$file_name = $_FILES['file_name']['name'];
$k = $_COOKIE['key'];
$n = $_COOKIE['current_table'];
$dst_dir = $cfg_files_dir;
if (basename($_SERVER['SCRIPT_NAME'])=='submit_related.php') {
		$dbhandle = $_db -> connect();
		if ($dbhandle) {
			$q = "SELECT '$n'::regclass::oid";
			$r = pg_fetch_assoc(pg_query($q));
			$_db->close($dbhandle);
			$_POST['file_owner_table_id'] = $r['oid'];
			$_POST['file_owner_row_id'] = $k;
			$dst_dir .= "/".$n;
	}
}
		if (!empty($file_name)) {
		$file = $_forms->transliterate(basename($_FILES['file_name']['name']));
		if (!file_exists($dst_dir)) {
			mkdir($dst_dir,0777,true);		
		}
		$destination = $dst_dir ."/".$file;
		if (!file_exists($destination)) {
			if (move_uploaded_file($_FILES['file_name']['tmp_name'], $destination)) {
				$_POST['file_name'] = $file;
				$_POST['file_mime'] = $_FILES['file_name']['type'];
				$_POST['file_size'] = $_FILES['file_name']['size'];
				$_POST['file_path'] = $dst_dir."/";
				//echo " debug: $file";
			} else {
				if (!empty($_FILES['file_name']['error'])) {
					$_SESSION['errors'] = $_FILES['file_name']['error'];
					}
				}
			}		
		} else { 
			if (basename($_SERVER['SCRIPT_NAME'])=='submit_related.php') {
					switch($_COOKIE['related_recordset_action']) {
					case 'update':
					{
						$_POST['file_name'] = $_POST['file_name_control'];
						break;	
					}
					case 'delete':
					{
						$dst_dir = $_POST['file_path'];
						unlink($dst_dir.$_POST['file_name_control']);
						break;
					}
				}
			} else {
				switch ($_COOKIE['recordset_action']) {
					case 'update': 
					{
						$_POST['file_name'] = $_POST['file_name_control'];
						break;
					}
					case 'delete':
					{
						$dst_dir = $_POST['file_path'];
						unlink($dst_dir.$_POST['file_name']);
						break;
					}
				}
			}
		}	
//echo "debug: SCRIPT_NAME: ".$_SERVER['SCRIPT_NAME']."; file_name: $file_name; dst_dir: $dst_dir;";
//echo "debug: ra ".$_COOKIE['recordset_action'];
//echo " rra".$_COOKIE['related_recordset_action']." fd ".$cfg_files_dir." filen ".$_POST['file_name'];
?>