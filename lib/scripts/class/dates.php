<?php
class dates
{
	function get_curr_date()
	{
		return date("Y-m-d");
	}

	function get_date_in_future($days)
	{
		$plus = mktime(0,0,0,date("m"),date("d")+$days,date("Y"));
		return date('Y-m-d',$plus);
	}
}
?>