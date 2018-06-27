<?php
	$password = $_POST['user_passwd'];
	$is_pg_crypted = $_forms -> is_pg_crypted($password);
	if (!$is_pg_crypted) {
		$dbhandle = db::connect();
		if ($dbhandle) {
			$passwd_row = pg_fetch_assoc(@pg_query("SELECT crypt('$password', gen_salt('MD5')) AS crypted"));
			db::close($dbhandle);
			$_POST['user_passwd'] = $passwd_row['crypted'];
		}
	}
?>