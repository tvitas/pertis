<?php
include_once("lib/scripts/class/display.php");
class printrepo extends display
{	
	public function report_select() {
		$table_name = db::get_table_string($_SESSION['current_table']);
		$table = $_SESSION['current_table'];
		$dbhandle = db::connect();
		if ($dbhandle) {
			if (file_exists("lib/forms/report/$table_name.php")) {						
				include("lib/forms/report/$table_name.php");
			}	elseif (file_exists("lib/forms/report/default.php")) {
				include("lib/forms/report/default.php");
			}
			db::close($dbhandle);
		}
		return NULL;
	}			 

	public function report_generate($repo_id, $record_id)
	{
		global $view_queries;
		global $cfg_custom_report_th_dir;
		global $cfg_custom_report_tr_dir;
		$table = $_SESSION['current_table'];
		$user_id = $_SESSION['user']['id'];
		$is_filterable = $_SESSION['user_rights'][$table]['filter'];
		$filter_column = "";
		if ($is_filterable == 't') {
			$filter_column = $_SESSION['user_rights'][$table]['filter_column'];
		}
		$table_name = db::get_table_string($table);
		$key = display::get_key_field();
		$comments = display::get_fields_comments();
		$order = display::get_default_order($comments[$table_name]);
		$types = display::get_field_types();
		$perm_filter = display::get_perm_filter($table);
		$key_val = NULL;
		if (isset($record_id)) {
				$key_val = display::format_key_val($record_id);
		}
		//$msg=""; 
		$flag_query_src_array = false;
		$flag_query_src_db = false;
		$flag_query_src_select_all = false;
		$report_title = '';
		$dbhandle = db::connect();
		if (!empty($dbhandle))
		{
			if (!empty($_COOKIE['select_order']))
			{
				$select_order = $_COOKIE['select_order'];
				$order_dir = $_SESSION['order_dir'];
				$order_mark = 'A-Z';
				if ($order_dir=='DESC') $order_mark = 'Z-A';
				$order = "ORDER BY $select_order $order_dir";
				//if (($order=="ORDER BY ASC") || ($order=="ORDER BY DESC")) $order="ORDER BY $key ASC"; 
				$order_msg = "$select_order $order_mark";
			}
			$filter = ""; 
			//jeigu yra filtras 
			if (isset($_COOKIE['select_filter']))
			{			
				//echo "debug: isset select_filter <br />"; 
				$filter = "WHERE ".urldecode($_COOKIE['select_filter']);
				$filter_msg = urldecode($_COOKIE['select_filter']);
				//ir tik userio irasai 
				if (!empty($filter_column)) { 
					$filter .= " AND $filter_column = $user_id";
				}
				//ir pazymetas vienas irasas 
				if (!empty($_COOKIE['key']))
				{
					//echo "debug not empty cookie key <br />"; 
					$filter .= " AND $key = $key_val";
				}
				//ir pastovus filtras 
				if (!empty($perm_filter)) {
					$filter .= " AND $perm_filter";				
				}

			}
			//jeigu nera filtro, bet tik userio irasai 
			if ((!isset($_COOKIE['select_filter'])) && (!empty($filter_column))) {			
				$filter = " WHERE $filter_column = $user_id";
				// ir pazymetas vienas irasas 
				if (!empty($record_id))
				{
					$filter .= " AND $key = $key_val";
				}
				//ir pastovus filtras 
				if (!empty($perm_filter)) {
					$filter .= " AND $perm_filter";				
				}
			}			
			//jeigu nera filtro, bet pazymetas irasas ir ne tik userio irasai 
			if ((!isset($_COOKIE['select_filter'])) && (!empty($record_id)) && (empty($filter_column))) {			
				//echo "debug: not isset select_filter and not empty record_id<br />"; 
				$filter .= " WHERE $key = $key_val";
				//ir pastovus filtras 
				if (!empty($perm_filter)) {
					$filter .= " AND $perm_filter";				
				}
			}
			//jeigu tik pastovus filtras 
			if ((!isset($_COOKIE['select_filter'])) && (empty($record_id)) && (empty($filter_column))) {			
				//echo "debug: not isset select_filter and not empty record_id<br />";
				//ir pastovus filtras 
				if (!empty($perm_filter)) {
					$filter .= " WHERE $perm_filter";				
				}
			}		
			//setcookie('key', NULL); 
			$report_title_row = pg_fetch_assoc(pg_query("SELECT report_title 
																						FROM reports.reports 
																						WHERE report_id = $repo_id"));

			$report_title = $report_title_row['report_title'];

			$header_query_row = pg_fetch_assoc(pg_query("SELECT report_details_parameter_value 
																						FROM reports.reports
																						LEFT JOIN reports.reports_details ON report_id = report_details_report_id
																						WHERE report_details_report_id = $repo_id 
																						AND report_details_parameter_type = 'header_query'"));
//report heading query 
			$report_heading_row = pg_fetch_assoc(pg_query("SELECT report_details_parameter_value
																						FROM reports.reports
																						LEFT JOIN reports.reports_details ON report_id = report_details_report_id
																						WHERE report_details_report_id = $repo_id 
																						AND report_details_parameter_type = 'heading_query'"));
//report body query 
			$query_row = pg_fetch_assoc(pg_query("SELECT report_details_parameter_value 
																						FROM reports.reports
																						LEFT JOIN reports.reports_details ON report_id = report_details_report_id
																						WHERE report_details_report_id = $repo_id 
																						AND report_details_parameter_type = 'query'"));
//report footer query 
			$report_heading_row = pg_fetch_assoc(pg_query("SELECT report_details_parameter_value
																							FROM reports.reports
																							LEFT JOIN reports.reports_details ON report_id = report_details_report_id
																							WHERE report_details_report_id = $repo_id
																							AND report_details_parameter_type = 'footer_query'"));
//report heading columns  			
 			$heading_columns_row = pg_fetch_assoc(pg_query("SELECT report_details_parameter_value 
																						FROM reports.reports 
																						LEFT JOIN reports.reports_details ON report_id = report_details_report_id 
																						WHERE report_details_report_id = $repo_id 
																						AND report_details_parameter_type = 'heading_columns'"));
//report body columns 
 			$columns_row = pg_fetch_assoc(pg_query("SELECT report_details_parameter_value 
																						FROM reports.reports 
																						LEFT JOIN reports.reports_details ON report_id = report_details_report_id 
																						WHERE report_details_report_id = $repo_id 
																						AND report_details_parameter_type = 'columns'"));
			$records_row = pg_fetch_assoc(pg_query("SELECT report_details_parameter_value 
																						FROM reports.reports 
																						LEFT JOIN reports.reports_details ON report_id = report_details_report_id 
																						WHERE report_details_report_id = $repo_id 
																						AND report_details_parameter_type = 'count_records'"));
			$enum_row = pg_fetch_assoc(pg_query("SELECT report_details_parameter_value 
																						FROM reports.reports 
																						LEFT JOIN reports.reports_details ON report_id = report_details_report_id 
																						WHERE report_details_report_id = $repo_id 
																						AND report_details_parameter_type = 'enum_rows'"));
			$own_style_row = pg_fetch_assoc(pg_query("SELECT report_details_parameter_value 
																						FROM reports.reports 
																						LEFT JOIN reports.reports_details ON report_id = report_details_report_id 
																						WHERE report_details_report_id = $repo_id 
																						AND report_details_parameter_type = 'own_style'"));
			$own_style_row_heading = pg_fetch_assoc(pg_query("SELECT report_details_parameter_value 																						FROM reports.reports 
																						LEFT JOIN reports.reports_details ON report_id = report_details_report_id 
																						WHERE report_details_report_id = $repo_id 
																						AND report_details_parameter_type = 'own_style_heading'"));																						
			$finishing_query_row = pg_fetch_assoc(pg_query("SELECT report_details_parameter_value 
																						FROM reports.reports 
																						LEFT JOIN reports.reports_details ON report_id = report_details_report_id 
																						WHERE report_details_report_id = $repo_id 
																						AND report_details_parameter_type = 'finishing_query'"));
			$report_header_row = pg_fetch_assoc(pg_query("SELECT report_header 
																								FROM reports.reports 
																								WHERE report_id = $repo_id"));
			$report_footer_row = pg_fetch_assoc(pg_query("SELECT report_footer 
																									FROM reports.reports 
																									WHERE report_id = $repo_id"));
			$company_row = pg_fetch_assoc(pg_query("SELECT 
																						array_to_string(array (SELECT properties_value 
																						FROM system_schema.properties 
																						WHERE properties_name = 'company_name'), '\n')||'\n'||
																						array_to_string(array (SELECT properties_value 
																						FROM system_schema.properties 
																						WHERE properties_name = 'company_address'),'\n') AS company_title"));
			$app_row = pg_fetch_assoc(pg_query("SELECT properties_value 
																					FROM system_schema.properties 
																					WHERE properties_name = 'program_title'"));
			$username_row = pg_fetch_assoc(pg_query("SELECT user_attribute_value FROM administration.users_attributes
			WHERE user_attribute_user_id = {$_SESSION['user']['id']} AND user_attribute_name = 'cn'"));

			if (empty($query_row['report_details_parameter_value'])) {
				switch (empty($view_queries[$table_name])) {
					case true: {
						$query = "SELECT * FROM $table";
						$flag_query_src_select_all = true;
						break;
					}
					case false: {
						$query = $view_queries[$table_name];
						$flag_query_src_array = true;
						break;
					}
				}	
			} else {
				$query = str_replace('%filter%', $filter, $query_row['report_details_parameter_value']);
				$query = str_replace('%order%', $order, $query);
				$records_query = str_replace('%filter%', $filter, $records_row['report_details_parameter_value']);	
				$comments[$table_name] = array();
				parse_str($columns_row['report_details_parameter_value'],$comments[$table_name]);
				$total_rows = @pg_num_rows(@pg_query($records_query));
				$flag_query_src_db = true;
			}	 		
			if (($flag_query_src_select_all) || ($flag_query_src_array)) {
				
				$query .= " $filter $order";
				$total_rows = @pg_num_rows(@pg_query($query));
				asort($comments[$table_name]);
			}
		//%company%, %date%, %time%, %records%, %filter%, %order%, %user%
			$flag_enum_rows = $enum_row['report_details_parameter_value'];
			if ($flag_enum_rows == 1) $flag_enum_rows = true; else $flag_enum_rows = false;
			$flag_own_style = $own_style_row['report_details_parameter_value'];
			$flag_own_style_heading = $own_style_row_heading['report_details_parameter_value'];
			$now = getdate();
			$date = $now['year'].'-'.sprintf("%02d",$now['mon']).'-'.sprintf("%02d", $now['mday']);
			$time = sprintf("%02d", $now['hours']).':'.sprintf("%02d", $now['minutes']).':'.sprintf("%02d", $now['seconds']);
			$args = array('%company%', '%records%', '%filter%', '%order%','%user%', '%date%', '%time%', '%username%','%application%');
			$vals = array($company_row['company_title'], $total_rows, $filter_msg, $order_msg, $_SESSION['user']['user'], $date, $time, $username_row['user_attribute_value'],$app_row['properties_value']);
			$header = str_replace($args, $vals, $report_header_row['report_header']);
			$footer = str_replace($args, $vals, $report_footer_row['report_footer']);
			$title = str_replace($args, $vals, $report_title_row['report_title']);
			$subtitle = str_replace($args, $vals, $_POST['subtitle']);
			$subfooter = str_replace($args, $vals, $_POST['subfooter']);
			$heading_query = str_replace('%filter%', $filter, $heading_query_row['report_details_parameter_value']);
			$heading_query = str_replace('%order%', $order, $heading_query);
			$footer_query = str_replace('%filter', $filter, $footer_query_row['report_details_parameter_value']);
			$footer_query = str_replace('%order', $order, $footer_query);
			$finishing_query = str_replace('%filter%', $filter, $finishing_query_row['report_details_parameter_value']);
			$finishing_query = str_replace('%order%', $order, $finishing_query);
			if ($heading_query) {
				$result = pg_query($heading_query);
			//echo "debug: ".nl2br($query)." <br />";
				if (file_exists("$cfg_custom_report_th_dir/$table_name.php")) include ("$cfg_custom_report_th_dir/$table_name.php"); 
				elseif (file_exists("lib/templates/reports/th/default.php")) include ("lib/templates/reports/th/default.php");
				$row_no = 1;
				while ($row = @pg_fetch_assoc($result))
				{
					if (file_exists("$cfg_custom_report_tr_dir/$table_name.php")) include ("cfg_custom_report_tr_dir/$table_name.php");			
					elseif(file_exists("lib/templates/reports/tr/default.php")) include ("lib/templates/reports/tr/default.php");
					$row_no++;
				}		
			echo "</tbody>\n";
			echo "</table>\n";
			}
			$result = pg_query($query);
			//echo "debug: ".nl2br($query)." <br />";
			if (file_exists("lib/templates/reports/th/$table_name.php")) include ("lib/templates/reports/th/$table_name.php");
			elseif(file_exists("lib/templates/reports/th/default.php")) include ("lib/templates/reports/th/default.php");
			$row_no = 1;
			while ($row = @pg_fetch_assoc($result))
			{
				if (file_exists("lib/templates/reports/tr/$table_name.php")) include ("lib/templates/reports/tr/$table_name.php");			
				elseif(file_exists("lib/templates/reports/tr/default.php")) include ("lib/templates/reports/tr/default.php");
				$row_no++;
			}		
			if ($finishing_query) {
				$result = pg_query($finishing_query);
				//echo "debug: $finishing_query";
				while ($row = @pg_fetch_assoc($result))
				{
					if (file_exists("lib/templates/reports/tr/$table_name_finish.php")) include ("lib/templates/reports/tr/$table_name_finish.php");			
					elseif(file_exists("lib/templates/reports/tr/default_finish.php")) include ("lib/templates/reports/tr/default_finish.php");
					$row_no++;
				}		
			}
			db::close($dbhandle);
			if ($flag_own_style != 'Taip') {
			  echo "<tr class = \"print-row-last\"></tr>\n";
			}
			echo "</tbody>\n";
			echo "</table>\n";
			echo "<div class = \"report-footer\">".nl2br($footer)."</div>\n";
			echo "<div class = \"report-footer\">".nl2br($subfooter)."</div>\n";
		} //if dbhandle 
		return NULL;
	}	//function
} //class
?>