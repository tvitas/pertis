<?php
class mime_types
{
	function get_mime_type($file)  
	{
//		echo $file;
		if (file_exists($file)) return @mime_content_type($file); else return false;
	}
}
?>