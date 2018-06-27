<?php
$src_conn = 'host=localhost port=5433 dbname=web_contacts user=vitas password=kertinisakmuo';
$dst_conn = 'host=localhost port=5433 dbname=pertis user=vitas password=kertinisakmuo';
$src_db = pg_connect($src_conn);
$dst_db = pg_connect($dst_conn);

if ((!$src_db) || (!$dst_db)) {
	die ('PG_CONNECT FAILED');
}

$src_result = pg_query($src_db, "SELECT lsmu_structure_lsmu_id,
								lsmu_structure_lsmu_parent_id,
								full_title_clean,
								node_title,
								node_path
								FROM admin.lsmu_tree_full");
$src_data = array();

if ($src_result) {
	$src_data = pg_fetch_all($src_result);
}

if (!empty($src_data)) {
	pg_query($dst_db, "TRUNCATE structure.departments");
	foreach ($src_data as $record) {
		$query = "INSERT INTO structure.departments
		(department_id,
		department_parent_id,
		department_title_full,
		department_title_node,
		department_node_path)
		VALUES ({$record['lsmu_structure_lsmu_id']},
		{$record['lsmu_structure_lsmu_parent_id']},
		'{$record['full_title_clean']}',
		'{$record['node_title']}',
		'{$record['node_path']}')";
		pg_query($dst_db, $query);
	}
	echo '1312';
}
if ($src_db) {
	pg_close($src_db);
}
if ($dst_db) {
	pg_close($dst_db);
}
?>
