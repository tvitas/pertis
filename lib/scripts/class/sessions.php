<?php
class session
{

	function get_key()
	{
		$str = '';
		for ($i=1; $i<=10; $i++)
		{
			$set = array(rand (65,90),rand(97,122));
			$str .= chr($set[rand(0,1)]);
		}	
		return $str; 
	} 
}
?>