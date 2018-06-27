<?php 
/*
$ldap = ldap_connect("193.219.163.100");
ldap_set_option ($ldap, LDAP_OPT_REFERRALS, 0);
ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
if (!empty($_POST['username']) && !empty($_POST['password'])) {
    $bind = @ldap_bind($ldap, $_POST['username'].'@kmu.lt', $_POST['password']);
    if($bind) {
        echo "<br /> $bind AUTH";
        $filter = "(samaccountname={$_POST['username']})";
        $searchResult = ldap_search($ldap,'dc=kmu,dc=lt',$filter);
        $sr = ldap_get_entries($ldap, $searchResult);
        echo "<br />";
        echo "{$sr[0]['cn'][0]}. {$sr[0]['title'][0]}. {$sr[0]['info'][0]} <br />";

    } else { 
        echo "<br /> $bind NO AUTH";
    }
}  
*/
$ad_server = "193.219.163.100";
$ad_dn = "dc=kmu,dc=lt";
$ad_domain = "@kmu.lt";
$db_user = $_POST['username'];
$db_password = $_POST['password'];
if((!empty($ad_server)) && (!empty($ad_dn)) && (!empty($ad_domain)) && (!empty($db_user)) && (!empty($db_password))) {
    $ldap = ldap_connect($ad_server);
    if($ldap) {
        ldap_set_option ($ldap, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        $bind = @ldap_bind($ldap, $db_user.$ad_domain, $db_password);
        if ($bind) {
			echo "AUTH <br />";
    	    $filter = "(samaccountname=$db_user)";
			$result = ldap_search($ldap, $ad_dn, $filter);
			$sr = ldap_get_entries($ldap, $result);
			$attributes_needing = array('givenname','sn','cn','mail','telephonenumber','mobile','facsimiletelephonenumber');
			foreach ($attributes_needing as $attrib) {
				echo "$attrib: {$sr[0][$attrib][0]} <br />\n";	
			}
//	        echo "{$sr[0]['cn'][0]}. 
//	        {$sr[0]['title'][0]}. {$sr[0]['info'][0]}. 
//	        {$sr[0]['telephonenumber'][0]}, 
//	        {$sr[0]['mobile'][0]} {$sr[0]['mail'][0]}<br />";
        } else { echo "NO AUTH"; }
    } 
}
?>
<form method="POST" action="ad_auth.php">
<input type="text" name="username"> </input> 
<input type="password" name="password"> </input>
<input type="submit" name="OK"></input>
<input type="reset" name="reset"></input>
</form>
<?php var_dump($sr) ?>
