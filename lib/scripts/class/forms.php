<?php
include_once ("lib/scripts/class/display.php");
class forms extends display
{
	function get_table_fields()
	{
		$table_name = db::get_table_string($_SESSION['current_table']);
		foreach ($_SESSION[$table_name][$table_name] as $field)
		{
			$str = explode(":", $field);
			$fields[] = $str[0];
		}
		return $fields;
	}

	private function check_is_aliased($str) {
		$ts = ".";
		if 	(($position = strpos($str,$ts)) == false) return false;
		return true;			
	}

	function get_form_vals()
	{
		$vals = array();		
		$table = $_SESSION['current_table'];
		$table_name = db::get_table_string($table);
		$comments = display::get_fields_comments();
		foreach ($comments[$table_name] as $keys => $value)
		{
			$s = explode('|', $comments[$table_name][$keys]);
			if ($s[7] == 1) {
				$vals[$keys] = $_POST[$keys];
			}
		}
		return $vals;
	}	

	public function clear_lt($string) {
		$lith__chars = array('Ą','ą','Č','č','Ę','ę','Ė','ė','Į','į','Š','š','Ų','u','Ū','ū','„','“','Ž','ž');
		$latin_chars = array('A','a','C','c','E','e','E','e','I','i','S','s','U','u','U','U','"','"','Z','z');
		$return=str_replace($lith__chars, $latin_chars, $string);
		return $return;	
	}
	
	public function transliterate($string) {
		$string = $this->clear_lt(strtolower($string));
    $replace = array(' ', '!', '@', '#', '$', '%', '^', '&', '(', ')', '<', '>', ':', ';', ',','=', '+');
		$replacements = array('-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-','-', '-');
		return str_replace($replace,$replacements,$string);
	}
	
	function is_pg_crypted($string) {
		$attrib_len = false;
		$attrib_dollar = false;
		$attrib_one = false;
		$attrib_exclam = false;
		$return = false;
		if (strlen($string) === 34) $attrib_len = true;
		if (substr($string, 0, 1) === '$') $attrib_dollar = true;
		if ((substr($string,1,1) === '1') || (substr($string,2,1) === '1')) $attrib_one = true;
		if (substr($string,0,1) === '!') $attrib_exclam = true;
		if ($attrib_len && ($attrib_dollar || $attrib_exclam) && $attrib_one) $return = true;
		return $return;
	}

	function validate_form() {
		
	}
	
	function get_log_string() {
		$log_string = NULL;
		$time_stamp=getdate();
		$user = $_SESSION['user']['user'];
		$log_string = $time_stamp['year'].'-'.$time_stamp['mon'].'-'.$time_stamp['mday'].' '.$time_stamp['hours'].':'.$time_stamp['minutes'].':'.$time_stamp['seconds'];
		$log_string .= '|'.$_SERVER['REMOTE_ADDR'];
		$log_string .='|'.$user.'|'.$_COOKIE['current_table'].'|'.$_COOKIE['related_table'];
		return $log_string; 		
	}
	
	function write_log_string($log_string) {
		global $cfg_log_flat;
		if ($cfg_log_flat) {
			$table = $_SESSION['current_table']; 	
			$file_name = db::get_table_string($table).".log";
			$log_string .="\n";
			if (file_exists("log/$file_name"))
				$file_handle = fopen("log/$file_name", 'a');
			else 
				$file_handle = fopen("log/$file_name",'w');
			fwrite($file_handle, $log_string);
			fclose($file_handle);
		}
		return NULL;
	}
	
	function get_filter_expression() {
		$expression = "";
		$field = $_COOKIE['search_field'];
		$is_aliased = $this -> check_is_aliased($field);
		if ($is_aliased) $field = str_replace('.', '_', $field);
		$value = $_POST[$field];
		$glue = "";
		$s = explode(',', $value);
		$sizeof_s = sizeof($s);
		if ($sizeof_s > 1) $glue = "OR"; 
		$field = substr($field, strpos($field,'_')+1, strlen($field));
		//echo "field: $field is_aliased: $is_aliased"; //die;
		if ($is_aliased) {
			$field = $_COOKIE['search_field'];
			$field = substr($field, strpos($field,'_')+1, strlen($field));
		}
		//echo "debug: val - $value, field - $field cookie - ".$_COOKIE['search_field'];		
		$operator = "";
		if (!empty($field)) {
			$types = display::get_field_types();
			$type = $types[$field];
			//echo " debug: type - $type";
			switch ($type) {
				case 'boolean':
				case 'bool': {
					if (empty($value)) {
						$value = "'%f%'";
						$operator = "::text LIKE ";
					} else {
						if ((strtolower($value) == 'ne') || (strtolower($value) == 'n')) {
							$value = "'f%'";
							//echo "debug: $value ";
						}
						if ((strtolower($value) == 'taip') || (strtolower($value) == 't')) {
							$value = "'t%'";
							//echo "debug: $value ";
						}
						$operator = "::text LIKE ";
					}
					break;			
				}
				case 'integer':
				case 'int4':
				case 'bigint':
				case 'date':
				case 'timestamp without time zone':
				case 'time without time zone':
				case 'timestamp':
				case 'time': {
					$field .= "::text";
					$operator = " LIKE ";
					$value = trim($value);
					$value = "'%$value%'";
					break;
				}
				default: {
					$field = 'lower('.$field.'::text)';
//					$field .= "::text";
					$operator = " LIKE ";
					$value = trim($value);
					$value = strtolower($value);
					$value = "'%$value%'";
					break;
				}
			}
		}
		$expression = "";
		//$value = strtolower($value);
		if ($sizeof_s > 1) {
			$i = 1;
			foreach ($s as $v) {
				if ($i == $sizeof_s) $glue = '';
				if (!empty($v)) {
					$v = trim($v);
					$expression .= "$field $operator '%$v%' $glue " ;
					//echo "debug: $expression \n";
				}
				$i ++;
			}
		} else {
			$expression .= "$field $operator $value"; 
		}
		if (!empty($_COOKIE['sql']))
		{
			$operator = "";
			$field = "";
			$expression = "$value";
		}			
		$value = stripslashes($value);
		return $expression;	
	}
	
	function get_query() 
	{
		global $view_queries; 
		$table = $_SESSION['current_table']; 	
		$table_name = db::get_table_string($table);
		$query="";
		$key_field = display::get_key_field(); 
		$types = display::get_field_types();
		//print_r($types);
		$key_val = $_POST[$key_field];
		$old_key_val = $_COOKIE['old_key'];
		//echo "forms.php – keyfield: $key_field, keyval: $key_val";
		switch($_COOKIE['recordset_action']) 
		{
			case "delete":
			{
				if (!empty($key_val))  
				{				
					$key_val = display::format_key_val($key_val);
					$query = "DELETE FROM $table WHERE $key_field=$key_val";
				} 
		 		break; 	
			}
			case "copy":
 			{				
				$vals=$this->get_form_vals();
				$vals[$key_field]=0;
				$counter=count($vals);					
				if (!empty($counter)) 
				{					 
					$query = "insert into $table (";					
					$i=1;
					$endsign=","; 
					foreach ($vals as $key=>$val)
					{
						if ($counter == $i) $endsign="";
						$query.="$key$endsign";
						$i++; 
					} 
					$query.=") values ("; 
					$i=1;
					$endsign=",";
					foreach ($vals as $key=>$val)
					{
						if ($counter == $i) $endsign="";
						$type=$types[$key];
						switch ($type)  
						{
							case 'text':
							case (substr($type, 0, 9)=="character"):
							case 'varchar':
							case 'bpchar':
							case 'xml':
							case 'ltree': 
							{
								if (empty($val)) 
								{
									$val="DEFAULT"; 
									$query.="$val$endsign"; 
								} else
								{
									//$val=preg_replace("/<br \/>/", "", $val);
									//$val=preg_replace("/\n/", "<br />", $val); 								
									$val = addslashes($val);
									$query.="'$val'$endsign";
								}
								break; 
							}
							case 'date':
							case 'timestamp without time zone':
							case 'time without time zone':
							case 'timestamp':
							case 'time':    
							{						
								if (empty($val))  
								{
									$val="DEFAULT";
									$query.="$val$endsign"; 
								} else $query.="'$val'$endsign";
								break; 
							}							
							case 'integer':
							case 'bigint':
							case 'int4':  
							{								
								if (empty($val)) $val = "DEFAULT";
								if ($val==-1) $val=0;
								$query.="$val$endsign";  
								break; 
							} 								
							case 'macaddr': 
							{
								if (empty($val)) 
								{
									$val="DEFAULT";
									$query.="$val$endsign"; 
								} else $query.="'$val'$endsign";
								break;
							}
							case 'inet': 
							{
								if (empty($val)) 
								{
									$val="DEFAULT";
									$query.="$val$endsign"; 
								} else $query.="'$val'$endsign";
								break; 							
							}
							case 'boolean':
							case 'bool': 
							{
								if (empty($val)) $val="0::boolean"; else $val = "1::boolean";  
								$query.="$val$endsign";
								break;
							}							
							default: 
							{
								if (empty($val)) $val="DEFAULT";  
								$query.="$val$endsign";
								break;
							}									
						}		
						$i++; 
					} 
					$query.=")"; 
					break; 					
				}	
			}
			case "insert":    
 			{				
				$vals=$this->get_form_vals();
				$counter=count($vals);					
				if (!empty($counter)) 
				{					 
					$query = "insert into $table (";					
					$i=1;
					$endsign=","; 
					foreach ($vals as $key=>$val)
					{
						if ($counter == $i) $endsign="";
						$query.="$key$endsign";
						$i++; 
					} 
					$query.=") values ("; 
					$i=1;
					$endsign=",";
					foreach ($vals as $key=>$val)
					{
						if ($counter == $i) $endsign="";
						$type=$types[$key];
						switch ($type)  
						{
							case 'text':
							case (substr($type, 0, 9)=="character"):
							case 'varchar':
							case 'bpchar':
							case 'xml': 
							case 'ltree': 
							{
								if (empty($val)) 
								{
									$val="DEFAULT"; 
									$query.="$val$endsign"; 
								} else 
								{
									//$val=preg_replace("/<br \/>/", "", $val);
									//$val=preg_replace("/\n/", "<br />", $val); 								
									$val = addslashes($val);
									$query.="'$val'$endsign";
								}
								break; 
							}
							case 'date':
							case 'timestamp without time zone':
							case 'time without time zone':
							case 'timestamp':
							case 'time':      
							{						
								if (empty($val))  
								{
									$val="DEFAULT";
									$query.="$val$endsign"; 
								} else $query.="'$val'$endsign";
								break; 
							}							
							case 'integer':
							case 'bigint':
							case 'int4':  
							{								
								if (empty($val)) $val = "DEFAULT";
								if ($val==-1) $val=0;
								$query.="$val$endsign";  
								break; 
							} 								
							case 'macaddr': 
							{
								if (empty($val))
								{
									$val="DEFAULT";
									$query.="$val$endsign"; 
								} else $query.="'$val'$endsign";
								break;
							}
							case 'inet': 
							{
								if (empty($val)) 
								{
									$val="DEFAULT";
									$query.="$val$endsign"; 
								} else $query.="'$val'$endsign";
								break; 							
							}
							case 'boolean':
							case 'bool':  
							{
								if (empty($val)) $val="0::boolean"; else $val = "1::boolean";  
								$query.="$val$endsign";
								break;
							}	
							default: 
							{
								if (empty($val)) $val="DEFAULT";  
								$query.="$val$endsign";
								break;
							}									
						}		
						$i++; 
					} 
					$query.=")"; 
					break; 					
				}	
			}
			case 'update':  
			{
				$endsign=","; 
				$query="update $table set ";
				$i=1; 
				$vals=$this->get_form_vals();				
				$counter=count($vals);
				foreach ($vals as $key=>$val)
				{
					if ($i == $counter) $endsign =""; 
					$type=$types[$key];
					switch ($type)
					{
						case 'text':
						case (substr($type, 0, 9)=="character"):
						case 'varchar':
						case 'bpchar':
						case 'xml': 
						case 'ltree': 
						{
							if (empty($val))   
							{
								$val="DEFAULT";
								$query.="$key=$val$endsign"; 
							} else 
							{
								$val = addslashes($val);
								$query.="$key='$val'$endsign";
							}
							break;
						}							
						case 'integer':
						case 'bigint':
						case 'int4':  
						{								
							if (empty($val)) $val = "DEFAULT";
							if ($val==-1) $val=0;
							$query.="$key=$val$endsign";  
							break; 
						} 								
						case 'date':
						case 'timestamp without time zone':
						case 'time without time zone':
						case 'timestamp':
						case 'time':    
						{						
							if (empty($val)) 
							{
								$val="DEFAULT";
								$query.="$key=$val$endsign"; 
							} else $query.="$key='$val'$endsign";
							break;  							
						}							
						case 'macaddr': 
						{
							if (empty($val)) 
							{
								$val="DEFAULT";
								$query.="$key=$val$endsign"; 
							} else $query.="$key='$val'$endsign";
							break;  							
						}
						case 'inet': 
						{
							if (empty($val)) 
							{
								$val="DEFAULT"; 
								$query.="$key=$val$endsign"; 
							} else $query.="$key='$val'$endsign";
							break;							
						}
						case 'boolean':
						case 'bool':  
						{
							if (empty($val)) $val="0::boolean"; else $val = "1::boolean";  
							$query.="$key = $val$endsign";
							break;
						}	
						default: 
						{
							if (empty($val)) $val="DEFAULT";  
							$query.="$key = $val$endsign";
							break;
						}	 								
					}		 
					$i++; 
				}
//2011 09 10 
    $key_val=display::format_key_val($key_val);
				if (!empty($old_key_val))
				{
					$old_key_val = display::format_key_val($old_key_val);
					if($old_key_val!=$key_val) 
						$query .=" where $key_field = $old_key_val"; 
					else 
						$query.=" where $key_field = $key_val";
				} else $query.=" where $key_field = $key_val";					 
// end 2011 09 10
				break;
			}
			case 'print':
			{
				$key_val=display::format_key_val($_COOKIE['key']);
				$query=$view_queries[$table_name];	
				$query.=" where $key_field=$key_val";
				break;
			}
		}
		return $query;   
	}

// related -- reikia perrasyti, 
// teisingiau nereikalingas kodas -- viską galima padaryti arba polimorfinemis f-jomis, arba parametrais, etc. 
// bet kol kas sueis...

	function get_related_form_vals()
	{
		$vals = array();		
		$fields = $this->get_related_table_fields();
		foreach ($fields as $field)
		{
			$vals[$field] = $_POST[$field];
		}
		return $vals;
	}	

	function get_related_table_fields()
	{
		$table_name = db::get_table_string($_SESSION['related_table']);
		foreach ($_SESSION[$table_name][$table_name] as $field)
		{
			$str = explode(":", $field);
			$fields[] = $str[0];
		}
		return $fields;
	}

	function get_related_query() 
	{
		global $view_queries; 
		$table = $_SESSION['related_table']; 	
		$table_name = db::get_table_string($table);
		$query="";
		$key_field = display::get_related_key_field(); 
		$types = display::get_related_field_types();
		$key_val = $_POST[$key_field]; 
		//echo "keyfield: $key_field, keyval: $key_val";
		switch($_COOKIE['related_recordset_action']) 
		{
			case "delete":
			{
				if (!empty($key_val))  
				{				
					$key_val = display::format_related_key_val($key_val);
					$query = "DELETE FROM $table WHERE $key_field = $key_val";
				} 
		 		break; 	
			}
			case "copy":
 			{				
				$vals=$this->get_related_form_vals();
				$vals[$key_field]=0;
				$counter=count($vals);					
				if (!empty($counter)) 
				{					 
					$query = "insert into $table (";					
					$i=1;
					$endsign=","; 
					foreach ($vals as $key=>$val)
					{
						if ($counter == $i) $endsign="";
						$query.="$key$endsign";
						$i++; 
					} 
					$query.=") values ("; 
					$i=1;
					$endsign=",";
					foreach ($vals as $key=>$val)
					{
						if ($counter == $i) $endsign="";
						$type=$types[$key];
						switch ($type)  
						{
							case 'text':
							case (substr($type, 0, 9)=="character"):
							case 'xml': 
							case 'ltree': 
							{
								if (empty($val)) 
								{
									$val="DEFAULT"; 
									$query.="$val$endsign"; 
								} else
								{
									//$val=preg_replace("/<br \/>/", "", $val);
									//$val=preg_replace("/\n/", "<br />", $val); 								
									$val = addslashes($val);
									$query.="'$val'$endsign";
								}
								break; 
							}
							case 'date':   
							case 'timestamp without time zone':  
							case 'time without time zone':
							{						
								if (empty($val))  
								{
									$val="DEFAULT";
									$query.="$val$endsign"; 
								} else $query.="'$val'$endsign";
								break; 
							}							
							case 'integer':
							case 'bigint':  
							{								
								if (empty($val)) $val = 0;
								if ($val==-1) $val=0;
								$query.="$val$endsign";  
								break; 
							} 								
							case 'macaddr': 
							{
								if (empty($val)) 
								{
									$val="DEFAULT";
									$query.="$val$endsign"; 
								} else $query.="'$val'$endsign";
								break;
							}
							case 'inet': 
							{
								if (empty($val)) 
								{
									$val="DEFAULT";
									$query.="$val$endsign"; 
								} else $query.="'$val'$endsign";
								break; 							
							}
							case 'boolean': 
							{
								if (empty($val)) $val="0::boolean"; else $val = "1::boolean";  
								$query.="$val$endsign";
								break;
							}	
							default: 
							{
								if (empty($val)) $val="DEFAULT";  
								$query.="$val$endsign";
								break;
							}									
						}		
						$i++; 
					} 
					$query.=")"; 
					break; 					
				}	
			}
			case "insert":    
 			{				
				$vals=$this->get_related_form_vals();
				$counter=count($vals);					
				if (!empty($counter)) 
				{					 
					$query = "insert into $table (";					
					$i=1;
					$endsign=","; 
					foreach ($vals as $key=>$val)
					{
						if ($counter == $i) $endsign="";
						$query.="$key$endsign";
						$i++; 
					} 
					$query.=") values ("; 
					$i=1;
					$endsign=",";
					foreach ($vals as $key=>$val)
					{
						if ($counter == $i) $endsign="";
						$type=$types[$key];
						switch ($type)  
						{
							case 'text':
							case (substr($type, 0, 9)=="character"):
							case 'xml': 
							case 'ltree': 
							{
								if (empty($val)) 
								{
									$val="DEFAULT"; 
									$query.="$val$endsign"; 
								} else 
								{
									//$val=preg_replace("/<br \/>/", "", $val);
									//$val=preg_replace("/\n/", "<br />", $val); 								
									$val = addslashes($val);
									$query.="'$val'$endsign";
								}
								break; 
							}
							case 'date':    
							case 'timestamp without time zone': 
							case 'time without time zone':
							{						
								if (empty($val))  
								{
									$val="DEFAULT";
									$query.="$val$endsign"; 
								} else $query.="'$val'$endsign";
								break; 
							}							
							case 'integer':
							case 'bigint':  
							{								
								if (empty($val)) $val = 'DEFAULT';
								if ($val==-1) $val=0;
								$query.="$val$endsign";  
								break; 
							} 								
							case 'macaddr': 
							{
								if (empty($val))
								{
									$val="DEFAULT";
									$query.="$val$endsign"; 
								} else $query.="'$val'$endsign";
								break;
							}
							case 'inet': 
							{
								if (empty($val)) 
								{
									$val="DEFAULT";
									$query.="$val$endsign"; 
								} else $query.="'$val'$endsign";
								break; 							
							}
							case 'boolean': 
							{
								if (empty($val)) $val="0::boolean"; else $val = "1::boolean";  
								$query.="$val$endsign";
								break;
							}	
							default: 
							{
								if (empty($val)) $val="DEFAULT";  
								$query.="$val$endsign";
								break;
							}									
						}		
						$i++; 
					} 
					$query.=")"; 
					break; 					
				}	
			}
			case 'update':  
			{
				$endsign=","; 
				$query="update $table set ";
				$i=1; 
				$vals=$this->get_related_form_vals();				
				$counter=count($vals);
				foreach ($vals as $key=>$val)
				{
					if ($i == $counter) $endsign =""; 
					$type=$types[$key];
					switch ($type)
					{
						case 'text':
						case (substr($type, 0, 9)=="character"):
						case 'xml': 
						case 'ltree': 
						{
							if (empty($val))   
							{
								$val="DEFAULT";
								$query.="$key=$val$endsign"; 
							} else 
							{
								//$val=preg_replace("/<br \/>/", "", $val);
								//$val=preg_replace("/\n/", "<br />", $val); 								
								$val = addslashes($val);
								$query.="$key='$val'$endsign";
							}
							break;
						}							
						case 'integer':
						case 'bigint':  
						{								
							if (empty($val)) $val = 0;
							if ($val==-1) $val=0;
							$query.="$key=$val$endsign";  
							break; 
						} 								
						case 'date':   
						case 'timestamp without time zone': 
						case 'time without time zone':
						{						
							if (empty($val)) 
							{
								$val="DEFAULT";
								$query.="$key=$val$endsign"; 
							} else $query.="$key='$val'$endsign";
							break;  							
						}							
						case 'macaddr': 
						{
							if (empty($val)) 
							{
								$val="DEFAULT";
								$query.="$key=$val$endsign"; 
							} else $query.="$key='$val'$endsign";
							break;  							
						}
						case 'inet': 
						{
							if (empty($val)) 
							{
								$val="DEFAULT"; 
								$query.="$key=$val$endsign"; 
							} else $query.="$key='$val'$endsign";
							break;							
						}
						case 'boolean': 
						{
							if (empty($val)) $val="0::boolean"; else $val = "1::boolean";  
							$query.="$key = $val$endsign";
							break;
						}	
						default: 
						{
							if (empty($val)) $val="DEFAULT";  
							$query.="$key = $val$endsign";
							break;
						}	 								
					}		 
					$i++; 
				}
//2011 09 10 
        $key_val=display::format_related_key_val($key_val);				
// /2011 09 10
				$query.=" where $key_field = $key_val";
				break;
			}
			case 'print':
			{
				$key_val=display::format_key_val($_COOKIE['key']);
				$query=$view_queries[$table_name];	
				$query.=" where $key_field=$key_val";
				break;
			}
		}
		return $query;   
	}
// /related 
}
?>