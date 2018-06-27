<?php
include("lib/etc/site.conf");
include("lib/etc/connections.conf");
include_once("$cfg_class_dir/sessions.php");
session_start();
/* ----------  actions ----------  */
$action=$_COOKIE['action'];
if (empty($action)) {
  $action="login";
}
if (empty($_COOKIE['lang'])) {
  setcookie('lang', $cfg_default_lang)	;
}
if ($action=="logout") {
  $_SESSION['auth'] = false;
  $_SESSION['user'] = "";
  $_SESSION['passwd'] = "";
  $_SESSION['connstring']= "";
  $_SESSION['instance']=false;
  $_SESSION['key']="";
  $_SESSION['offset']=0;
  $_SESSION['current_table']="";
  $_SESSION['messages'] = "";
  $_SESSION['errors'] = "";
  $_SESSION['pre_submit'] = "";
  $_SESSION['post_submit'] = "";
  $_SESSION['db_admin'] = false;
  $_SESSION['ad_auth'] = false;
  $_SESSION['user_rights'] = array();
  $_SESSION['using_db'] = "";
  $_SESSION['start'] = true;
  $rows = $_COOKIE['select_rows'];
  setcookie("recordset_action","",time()-3600);
  setcookie("related_action","",time()-3600);
  setcookie("action","",time()-3600);
  setcookie("current_table","",time()-3600);
  setcookie("e_id","",time()-3600);
  setcookie("session_key","",time()-3600);
  setcookie("select_rows",$rows,time()+360000);
  setcookie("related_table","",time()-3600);
  setcookie("parent_key","",time()-3600);
  setcookie("related_recordset_action","",time()-3600);
  setcookie("select_page","",time()-3600);
  session_destroy();
}
if ($action == "login") {
	$user=$_POST['u_id'];
	$password=$_POST['u_passwd'];
	$database=$_POST['u_db'];
	if ((!empty($user)) && (!empty($password)) && (!empty($database))) {
		$connstring='host='.$host.' port='.$port.' dbname='.$database.' user='.$user.' password='.$password;
		$dbhandle=@pg_connect($connstring);
//pirmas bandymas - ar tai PostgreSQL user?
		if ($dbhandle) {
			$_SESSION['auth'] = true;
			$_SESSION['user']['user'] = $user;
			//$_SESSION['passwd'] = $password;
				  $_SESSION['connstring']=$connstring;
				  $_session = new session;
				  $_SESSION['key']=$_session->get_key();
				  $_SESSION['instance']=false;
				  $_SESSION['order_dir'] = 'ASC';
				  $_SESSION['db_admin'] = true;
				  $_SESSION['ad_auth'] = false;
				  $_SESSION['user']['id'] = 1;
				  $_SESSION['using_db'] = $database;
				//setcookie("lang", $cfg_default_lang);
				  setcookie("action","");
				  setcookie('select_page',1);
				  setcookie("session_key",$_SESSION['key']);
			 } else 	{
			   $connstring='host='.$host.' port='.$port.' dbname='.$database.' user='.$db_user.' password='.$db_password;
			   $dbhandle=pg_connect($connstring);
//antras bandymas - ar tai local (useris, esantis administration.users) user?
			   if ($dbhandle) {
				    $row = @pg_fetch_assoc(@pg_query("SELECT
				    user_passwd = crypt('$password', user_passwd) AS check_passwd,
				    user_id, user_preffered_lang_id
				    FROM  administration.users
				    WHERE user_login = '$user' AND user_active = true"));
				    @pg_close($dbhandle);
				    if ($row['check_passwd']==='t') {
					     $_SESSION['auth'] = true;
					     $_SESSION['user']['id'] = $row['user_id'];
					     $_SESSION['user']['user'] = $user;
					     //$_SESSION['passwd'] = $password;
					     $_SESSION['connstring']=$connstring;
					     $_session = new session;
					     $_SESSION['key']=$_session->get_key();
					     $_SESSION['instance']=false;
					     $_SESSION['order_dir'] = 'ASC';
					     $_SESSION['db_admin'] = false;
					     $_SESSION['ad_auth'] = false;
					     $_SESSION['using_db'] = $database;
					     if (!empty($row['user_preffered_lang_id'])) setcookie("lang", $row['user_preffered_lang_id']);
					     setcookie("action","");
					     setcookie('select_page',1);
					     setcookie("session_key",$_SESSION['key']);
				    } else {
//trecias bandymas – ar tai AD user?
       if(!empty($ad_server) && !empty($ad_dn) && !empty($ad_domain)) {
		 $ldap = ldap_connect($ad_server);
         if ($ldap) {
           ldap_set_option ($ldap, LDAP_OPT_REFERRALS, 0);
           ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
           $bind = @ldap_bind($ldap, $user.$ad_domain, $password);
           if ($bind) {
             $_SESSION['auth'] = true;
             $_SESSION['user']['id'] = false;
		     $_SESSION['user']['user'] = $user;
		     $_SESSION['passwd'] = $password;
		     $_SESSION['connstring'] = $connstring;
		     $_session = new session;
		     $_SESSION['key']=$_session->get_key();
		     $_SESSION['instance']=false;
		     $_SESSION['order_dir'] = 'ASC';
		     $_SESSION['db_admin'] = false;
             $_SESSION['using_db'] = $database;
			 $_SESSION['ad_user'] = true;
     	     if (!empty($row['user_preffered_lang_id'])) setcookie("lang", $row['user_preffered_lang_id']);
             setcookie("action","");
             setcookie('select_page',1);
             setcookie("session_key",$_SESSION['key']);

           }
         }
       } else {
         $_SESSION['auth'] = false;
	        setcookie('action','login');
	        setcookie("lang", $cfg_default_lang);
       }
     }
   }
 }
  @pg_close($dbhandle);
  setcookie('action','login');
} else {
			$_SESSION['auth'] = false;
			setcookie('action','login');
		}
}
/* ---------- authenticate ----------  */
	if ($_SESSION['auth'] == true)
	{
		header('Location: splash.php');
		exit;
	} else
	{
		$view = 'login';
	}
	if (file_exists("$cfg_custom_layout_dir/head.php")) {
		include("$cfg_custom_layout_dir/head.php");
	} else {
		include("$cfg_layout_dir/head.php");
	}
	if ($view == 'login')
	{
		if (file_exists("$cfg_custom_forms_dir/passwd.php")) {
			include("$cfg_custom_forms_dir/passwd.php");
		} else {
			include("$cfg_forms_dir/passwd.php");
		}
	}

	if (file_exists("$cfg_custom_layout_dir/page-foot.php")) {
		include("$cfg_custom_layout_dir/page-foot.php");
	} else {
		include("$cfg_layout_dir/page-foot.php");
	}
?>
