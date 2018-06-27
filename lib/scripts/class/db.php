<?php
class db
{
	public static function connect()
	{
		$dbhandle=pg_connect($_SESSION["connstring"]); 
		if ($dbhandle)
		{
			$_SESSION['connected'] = true;
			$_SESSION['dbhandle'] = $dbhandle;
			return $dbhandle;
		} else 
		{
			$_SESSION['connected'] = false;
			$_SESSION['dbhandle'] = NULL;
			return false;
		}			
	}

	public static function close($dbhandle)
	{
		pg_close($dbhandle);
		$_SESSION['connected'] = false;
		$_SESSION['dbhandle'] = NULL;
	}
	
	public static function get_table_string($table)
	{
		$table = explode(":",$table);
		return preg_replace("/\./", "_", $table[0]);
	}
	
	public static function get_table_only($table)
	{
		$table = explode(":",$table);
		return trim(substr($table[0], strpos($table[0], ".")+1, strlen($table[0])));
	}

	public static function get_table_name($table)
	{
		$table = explode(":",$table);
		return preg_replace("/_/", ".", $table[0], 0);
	}

	public static function get_schemas()
	{
		$schemas = array();
		$dbhandle=db::connect();
		if ($dbhandle)
		{
			$query = "SELECT nspname FROM  pg_catalog.pg_namespace 
						WHERE nspname NOT LIKE '%pg_%' AND nspname 
						NOT LIKE 'information_%' AND nspname !='system' AND nspname !='public'";
			$result = pg_query($query);
			if ($result)
			{
				$i=0;			
				while ($row = pg_fetch_row($result))
				{
					$schemas[$i] = $row[0];
					$i++;
				}
			}
		}
		db::close($dbhandle);
		return $schemas;
	}
	
	
	public static function get_schema_comment($schema)
	{
		$comment="";
		$dbhandle=db::connect();
		if ($dbhandle) {
			$query="SELECT * FROM pg_catalog.pg_namespace a
							LEFT JOIN pg_catalog.pg_description b ON (b.objoid=a.oid) 
							WHERE a.nspname='$schema'";
			$result=pg_query($query);
			if ($result) {
				$row=pg_fetch_assoc($result);
				$comment=$row['description'];
			}
			db::close($dbhandle);
		}	
		return $comment;
	}

	public static function get_tables($schema)
	{
		$tables = array("");
		$queries = array("SELECT n.nspname, c.relname, c.oid, 
								pg_catalog.obj_description(c.oid, 'pg_class') 
								FROM pg_catalog.pg_class c LEFT JOIN pg_catalog.pg_namespace n 
								ON n.oid=c.relnamespace WHERE c.relkind IN ('r','') 
								AND n.nspname = '$schema' ORDER by obj_description");
		$dbhandle = db::connect();
		if ($dbhandle)
		{		
			$i=0;
			for ($ii = 0; $ii < 1; $ii++)
			{
				$result = pg_query($queries[$ii]);
				//echo "debug: $ii ".nl2br($queries[$ii])."<br />";
				if ($result)
				{
					while ($row = pg_fetch_row($result))
					{
						$tables[$i] = "$row[0].$row[1]:$row[2]:$row[3]:";
						if ($ii==1) $tables[$i].="v:";
						if ($ii==0) $tables[$i].="t:";
						$str=explode(":",$tables[$i]);
						$query="SELECT count(*) from $str[0]";
						$rslt=pg_query($query);
						if ($rslt)
						{
							$rw=pg_fetch_row($rslt);
							$tables[$i].="$rw[0]";
						}						
						$i++;				
					}			
				}
			}
		} 
		db::close($dbhandle);
		//echo "debug: <br />"; nl2br(print_r($tables)); echo "<br />";
		return $tables;
	}

	public static function get_table_struct($table)
	{
		$oid_str=explode(":", $table);
		$table = db::get_table_string($table);
		$oid=$oid_str[1];		
		if (empty($oid)) $oid=0;
		$table_struct=array($table=>array(""));
		$dbhandle=db::connect();
		if ($dbhandle)
		{
			$query="SELECT a.attname, pg_catalog.format_type(a.atttypid, a.atttypmod), 
						(SELECT substring(d.adsrc for 128) FROM pg_catalog.pg_attrdef d 
						WHERE d.adrelid = a.attrelid AND d.adnum = a.attnum AND a.atthasdef), 
						a.attnotnull, a.attnum, pg_catalog.col_description(a.attrelid, a.attnum) 
						FROM pg_catalog.pg_attribute a 
						WHERE a.attrelid = $oid AND a.attnum > 0 AND NOT a.attisdropped 
						ORDER BY a.attnum";
			$result=@pg_query($query);
			if ($result)
			{
				$i=0;
				while ($row = pg_fetch_row($result))
				{
					$table_struct[$table][$i]="$row[0]:$row[1]:$row[5]:";
					$i++;									
				}
			}
			$queries=array("SELECT conname, 
						pg_catalog.pg_get_constraintdef(oid) as condef
						FROM pg_catalog.pg_constraint r
						WHERE r.conrelid = $oid AND r.contype = 'p'", 
						"SELECT conname, 
						pg_catalog.pg_get_constraintdef(oid) as condef
						FROM pg_catalog.pg_constraint r
						WHERE r.conrelid = $oid AND r.contype = 'f'");
			$count = count($table_struct[$table]);
			foreach ($queries as $query)
			{
				$result=pg_query($query);
				if ($result)
				{
					while ($row = pg_fetch_row($result))
					{
						for ($i=0; $i < $count; $i++)
						{ 
							$str=explode(":",$table_struct[$table][$i]);
							if ($str[0] == $row[0]) $table_struct[$table][$i].="$row[1]";
						}
					}		
				}
			}
		}		
		db::close($dbhandle);
		return $table_struct;	
	}
}
?>