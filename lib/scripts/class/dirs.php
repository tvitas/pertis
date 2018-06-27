<?php
class dirs  
{
	function lsdir($dir)  
	{
		if (file_exists($dir))
		{
			if ($handle=opendir($dir))  
			{
				while (false!==($file=readdir($handle))) 
				{
					if ($file!='.'&&$file!='..')
					{
						$files[]=$file; 
					}
				}	
  			closedir($handle);
			} 
		}
		if (isset($files)) return $files; else return false;   
	}
}
?>