<?php
include_once ("lib/scripts/class/forms.php");
class chars extends forms 
{
	function clearUTF($s)
	{
		setlocale(LC_ALL, 'en_US.UTF8');
		$r = '';
		$s1 = iconv('UTF-8', 'ASCII//TRANSLIT', $s);
		for ($i = 0; $i < strlen($s1); $i++)
		{
			$ch1 = $s1[$i];
			$ch2 = mb_substr($s, $i, 1);
			$r .= $ch1=='?'?$ch2:$ch1;
		}
		setlocale(LC_ALL, 'lt_LT.UTF8');
		return $r;
	}

	function clear_lt($string)
	{
		$lith__chars=array('Ą','ą','Č','č','Ę','ę','Ė','ė','Į','į','Š','š','Ų','u','Ū','ū','„','“','Ž','ž');
		$latin_chars=array('A','a','C','c','E','e','E','e','I','I','S','s','U','u','U','U','"','"','Z','z');
		$return=str_replace($lith__chars, $latin_chars, $string);
		return $return;	
	}
}
?>