<?php
include_once("lib/etc/connections.conf");
include_once("lib/scripts/class/display.php");
class auth extends display {

/*
Nustato sesijos user rights is administration.users 
*/
public function set_user_role_rights() {
	if (!$_SESSION['db_admin']) {
		$user = $_SESSION['user']['user'];
		$dbhandle = db::connect();
		if (!empty($dbhandle)) {
			if ($_SESSION['ad_user']) {
				$user_id = $this -> ad_user_exists($user);
				if (!$user_id) {
					$_SESSION['user']['id'] = $this -> add_ad_user($user,2);
				} else {
					$_SESSION['user']['id'] = $user_id;
				}
			}
      $result = pg_query("SELECT 
      a.user_role_id,
      b.role_id, 
      c.role_rights_table, 
      c.role_rights_view, 
      c.role_rights_write,
      c.role_rights_filter, 
      c.role_rights_filter_column, 
      c.role_rights_manage_accepts, 
      c.role_rights_accepts_column    
      FROM administration.users a 
      LEFT JOIN administration.roles b ON a.user_role_id = b.role_id 
      LEFT JOIN administration.role_rights c ON b.role_id = c.role_rights_role_id  
      WHERE a.user_login = '$user'");
      while ($rights_row = pg_fetch_assoc($result)) {
        $table = $rights_row['role_rights_table'];
        $view = $rights_row['role_rights_view'];
        $write = $rights_row['role_rights_write'];
        $filter = $rights_row['role_rights_filter'];
        $filter_column = $rights_row['role_rights_filter_column'];
        $accepts = $rights_row['role_rights_manage_accepts'];
        $accepts_column = $rights_row['role_rights_accepts_column'];
        $_SESSION['user_rights'][$table]['view'] = $view;
        $_SESSION['user_rights'][$table]['write'] = $write;
        $_SESSION['user_rights'][$table]['filter'] = $filter;
        $_SESSION['user_rights'][$table]['filter_column'] = $filter_column;
        $_SESSION['user_rights'][$table]['accepts'] = $accepts;
        $_SESSION['user_rights'][$table]['accepts_column'] = $accepts_column;
      }
      db::close($dbhandle);					
    }
  }
} //end function set_user_role_rights

/*
Nustato sys kintamuju reiksmes is system_schema.properties, type = sysconfig.
*/
public function set_sys_params(){
	$dbhandle = db::connect();
	if (!empty($dbhandle)) {
		$result = pg_query("SELECT properties_name, properties_value 
							FROM system_schema.properties 
							WHERE properties_type = 'sysconfig'");
		while ($properties_row = pg_fetch_assoc($result)) {
			$properties_name = $properties_row['properties_name'];
			$properties_value = $properties_row['properties_value']; 
			$_SESSION['sysconfig'][$properties_name] = $properties_value;
		}
		db::close($dbhandle);
	}
}//end function set_sys_params

//jeigu keiciasi AD atributai cfg_ad_attributes, reikia kiekviena karta keisti 
//atributu select'a, gaunama is system_schema.ad_attributes
public function set_ad_attributes_table() {
	global $cfg_ad_attributes;
	if (!empty($cfg_ad_attributes)) {
		$dbhandle = db::connect();
		if ($dbhandle) {
			@pg_query("TRUNCATE administration.ad_attributes");
			foreach ($cfg_ad_attributes as $key => $value) {
				@pg_query("INSERT INTO administration.ad_attributes (ad_attribute_name, ad_attribute_description) 
				VALUES ('$key', '$value')");
			}
			db::close($dbhandle);
		}
	}
	return NULL;
} //end func set_ad_Attributes_table

public function user_is_active() {
	$return = FALSE;	
	$user_id = $_SESSION['user']['id'];
	$dbhandle = db::connect();
	if ($dbhandle) {
		$result = @pg_query("SELECT user_active FROM administration.users WHERE user_id = $user_id");
		$active_row = pg_fetch_assoc($result);
		if ($active_row['user_active'] === 't') {
			$active_row['user_active'] = TRUE;
		}
		else {		
			$active_row['user_active'] = FALSE;
		}
		db::close($dbhandle);
	}
	if (isset($active_row['user_active'])) { 
		$return = $active_row['user_active'];
	}
	return $return;
}

/* 

ad_user_exists($user)

Jeigu useris pataikė į ad_user_exists, tada arba tai naujas useris iš AD, arba local useris su nauju AD passwd
Jeigu naujas iš AD - grąžinam false, ir žinokitės... :)
Jeigu local iš administration.users, bet su nauju passwd, tada tikrinam, ar lokaliai aktyvuotas ir keičiam passwd
Jeigu local iš administration.users, bet neaktyvuotas, paliekam viską kaip yra: grąžinam false, 
sekančiu žingsniu bus bandyta add_ad_user su tuo pačiu user_login, duombazės unique indexas išmes 
exception, useris nepasikeis.
*/
private function ad_user_exists($user) {
	$return = FALSE;
	$password = $_SESSION['passwd'];
	$result = pg_query("SELECT user_id, user_active
	FROM administration.users 
	WHERE user_login = '$user'");
//Jeigu tai local useris su nauju AD passwd
	if (pg_num_rows($result) > 0) {
		$row = pg_fetch_assoc($result);
//Jeigu useris aktyvus, bet pasikeitęs passwd
		if ($row['user_active'] === true) {
			$user_id = $row['user_id'];
			$passwd_row = pg_fetch_assoc(@pg_query("SELECT crypt('$password', gen_salt('MD5')) AS crypted"));
			$password = $passwd_row['crypted'];
			pg_query("UPDATE administration.users 
			SET user_passwd = '$password' 
			WHERE user_id = $user_id AND user_login = '$user'");
			$return = $user_id;			 
			$this -> update_ad_user_info($user_id, $user);
		}
	}
	return $return;
}

private function add_ad_user($user,$role) {
	global $cfg_ad_attributes;
	$password = $_SESSION['passwd'];
	$passwd_row = pg_fetch_assoc(@pg_query("SELECT crypt('$password', gen_salt('MD5')) AS crypted"));
	$password = $passwd_row['crypted'];
	pg_query("INSERT INTO administration.users (user_role_id, user_login, user_passwd, user_active) VALUES ($role, '$user', '$password', FALSE)");
	$row = pg_fetch_assoc(pg_query("SELECT user_id FROM administration.users WHERE user_login = '$user'"));
	pg_query("DELETE FROM administration.users_attributes WHERE user_attribute_user_id = {$row['user_id']}");
	foreach ($cfg_ad_attributes as $key => $value) {
		pg_query("INSERT INTO administration.users_attributes 
		(user_attribute_user_id, user_attribute_name)
		VALUES ({$row['user_id']}, '$key')");
	}
	$this -> update_ad_user_info($row['user_id'], $user);
	$_SESSION['passwd'] = NULL;
	return $row['user_id'];
}

private function update_ad_user_info ($user_id, $user) {
	global $ad_server;
	global $ad_dn;
	global $ad_domain;
	global $cfg_ad_attributes;
	$password = $_SESSION['passwd'];
	if((!empty($ad_server)) && (!empty($ad_dn)) && (!empty($ad_domain)) && (!empty($user)) && (!empty($password))) {
    	$ldap = ldap_connect($ad_server);
    	if($ldap) {
		ldap_set_option ($ldap, LDAP_OPT_REFERRALS, 0);
	        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
    	    $bind = @ldap_bind($ldap, $user.$ad_domain, $password);
	    	if ($bind) {
			$filter = "(samaccountname=$user)";
			$result = ldap_search($ldap, $ad_dn, $filter);
			$info = ldap_get_entries($ldap, $result);
			foreach ($cfg_ad_attributes as $key => $value) {
				$attribute_value = $info[0][$key][0];
				pg_query("UPDATE administration.users_attributes SET 
				user_attribute_value = '$attribute_value' 
				WHERE user_attribute_user_id = $user_id AND user_attribute_name = '$key'");
		}
	      }
    	} 
	}	
}
} //end class auth
	
?>
