<?php
include("lib/scripts/class/display.php");
class printrepo extends display
{
	public function report_select() {
		$table_name = db::get_table_string($_SESSION['current_table']);
		$table = $_SESSION['current_table'];
		$dbhandle = db::connect();
		if ($dbhandle) {
			if (file_exists("lib/forms/report/default.php")) {
				include("lib/forms/report/default.php");
			}
			db::close($dbhandle);
		}
		return NULL;
	}

	public function report_generate($repo_id, $record_id) {
		global $view_queries;
		global $cfg_custom_report_th_dir;
		global $cfg_custom_report_tr_dir;
		$table = $_SESSION['current_table'];
		$user_id = $_SESSION['user']['id'];
		$is_filterable = $_SESSION['user_rights'][$table]['filter'];
		$filter_column = "";
		if ($is_filterable == 't') {
			$filter_column = $_SESSION['user_rights'][$table]['filter_column'];
			$filter_share = $_SESSION['user_rights'][$table]['filter_share'];
			if (!empty($filter_share)) {
				$filter_share = explode(',',$filter_share);
			}
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
		$dbhandle = db::connect();
		if (!empty($dbhandle)) {
			if (!empty($_COOKIE['select_order'])) {
				$select_order = $_COOKIE['select_order'];
				$order_dir = $_SESSION['order_dir'];
				$order_mark = 'A-Z';
				if ($order_dir=='DESC') $order_mark = 'Z-A';
				$order = "ORDER BY $select_order $order_dir";
				$order_msg = "$select_order $order_mark";
			}
			$filter = "";
			if (isset($_COOKIE['select_filter'])) {
				$filter = "WHERE ".urldecode($_COOKIE['select_filter']);
				$filter_msg = urldecode($_COOKIE['select_filter']);
//ir tik userio irasai
				if (!empty($filter_column)) {
					if (!empty($filter_share)) {
						$filter .= " AND ($filter_column = $user_id";
						foreach ($filter_share as $share) {
							$filter .= " OR $filter_column = $share";
						}
						$filter .= ")";
					} else {
						$filter .= " AND $filter_column = $user_id";
					}
				}
//ir pazymetas vienas irasas
				if (!empty($_COOKIE['key'])) {
//echo "debug not empty cookie key <br />";
					$filter .= " AND $key = $key_val";
				}
//ir pastovus filtras//
				if (!empty($perm_filter)) {
					$filter .= " AND $perm_filter";
				}
			}
//jeigu nera filtro, bet tik userio irasai
			if ((!isset($_COOKIE['select_filter'])) && (!empty($filter_column))) {
				if (!empty($filter_share)) {
					$filter = " WHERE ($filter_column = $user_id";
					foreach ($filter_share as $share) {
						$filter .= " OR $filter_column = $share";
					}
					$filter .= ")";
				} else {
					$filter = " WHERE $filter_column = $user_id";
				}
// ir pazymetas vienas irasas
				if (!empty($record_id)) {
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
//ir pastovus filtras
				if (!empty($perm_filter)) {
					$filter .= " WHERE $perm_filter";
				}
			}

			$company_row = pg_fetch_assoc(pg_query(
			"SELECT array_to_string(array(
			SELECT properties_value
			FROM system_schema.properties
			WHERE properties_name = 'company_name'
			UNION ALL
			SELECT properties_value
			FROM system_schema.properties
			WHERE properties_name = 'company_address'),'\n')
			AS company_title"));

			$company_full_row = pg_fetch_assoc(pg_query(
			"SELECT array_to_string (ARRAY(
			SELECT properties_value FROM system_schema.properties
			WHERE properties_name = 'company_title' AND properties_type = 'company_properties'
			UNION ALL
			SELECT properties_value FROM system_schema.properties
			WHERE properties_name = 'company_address' AND properties_type = 'company_properties'
			UNION ALL
			SELECT properties_title||' '||properties_value FROM system_schema.properties
			WHERE properties_name = 'company_account' AND properties_type = 'company_properties'
			UNION ALL
			SELECT properties_title||' '||properties_value FROM system_schema.properties
			WHERE properties_name = 'company_bank' AND properties_type = 'company_properties'
			UNION ALL
			SELECT properties_title||' '||properties_value FROM system_schema.properties
			WHERE properties_name = 'company_code' AND properties_type = 'company_properties'
			UNION ALL
			SELECT properties_title||' '||properties_value FROM system_schema.properties
			WHERE properties_name = 'company_vat_code' AND properties_type = 'company_properties'), '\n')
			AS company_properties"));

			$app_row = pg_fetch_assoc(pg_query("SELECT properties_value
			FROM system_schema.properties
			WHERE properties_name = 'app_name'"));

			$username_row = pg_fetch_assoc(pg_query("SELECT user_attribute_value
			FROM administration.users_attributes
			WHERE user_attribute_user_id = {$_SESSION['user']['id']} AND user_attribute_name = 'cn'"));

			$report_sections = pg_query("SELECT *
			FROM reports.reports_sections
			WHERE report_section_report_id = $repo_id
			ORDER BY report_section_weight");


			$now = getdate();
			$date = $now['year'].'-'.sprintf("%02d",$now['mon']).'-'.sprintf("%02d", $now['mday']);
			$time = sprintf("%02d", $now['hours']).':'.sprintf("%02d", $now['minutes']).':'.sprintf("%02d", $now['seconds']);

			$public_args = array('%company%', '%companyfull%', '%records%', '%filtermsg%', '%order%','%user%', '%date%', '%time%', '%username%','%application%');
			$public_vals = array($company_row['company_title'], $company_full_row['company_properties'], $total_rows, $filter_msg, $order_msg, $_SESSION['user']['user'], $date, $time, $username_row['user_add_user_title'],$app_row['properties_value']);

			$topmost = str_replace($public_args, $public_vals, $_POST['topmost']);
			$undermost = str_replace($public_args, $public_vals, $_POST['undermost']);

			$flag_query_src_default = 0;

			if ($topmost) {
				echo "<div id=\"topmost\"> \n";
				echo $topmost;
				echo "\n</div> \n";
			}

			while ($section = pg_fetch_assoc($report_sections)) {
				$flag_enum_rows = $section['report_section_enum'];
				$records_query = "";
				if (!empty($section['report_section_records'])) {
					$records_query = str_replace('%filter%', $filter, $section['report_section_records']);
				}
				elseif ($section['report_section_type'] == 4) {
					if (empty($view_queries[$table_name])) {
						$records_query = "SELECT * FROM $table";
					} else {
						$records_query = $view_queries[$table_name];
					}
					$records_query .= " $filter";
				}
				if ($records_query) {
					$total_rows = pg_num_rows(pg_query($records_query));
				}
				$public_vals = array($company_row['company_title'], $company_full_row['company_properties'], $total_rows, $filter_msg, $order_msg, $_SESSION['user']['user'], $date, $time, $username_row['user_add_user_title'],$app_row['properties_value']);
				echo "<div id=\"{$section['report_section_no']}\">\n";
				switch ($section['report_section_type']) {
					case 1: {
						$section['report_section_content'] = str_replace($public_args, $public_vals, $section['report_section_content']);
						echo nl2br($section['report_section_content']);
						break;
					}
					case 2: {
						$section['report_section_content'] = str_replace($public_args, $public_vals, $section['report_section_content']);
						$content = $section['report_section_content'];
						$query = str_replace('%filter%', $filter, $section['report_section_query']);
						$query = str_replace('%order%', $order, $query);
						$result = @pg_query($query);
						if ($result) {
							while ($row = @pg_fetch_assoc($result)) {
								$search = array_keys($row);
								$replace = array_values($row);
								$c = str_replace($search, $replace, $content);
								echo nl2br($c);
							}
						}
						break;
					}
					case 3: {
						$query = str_replace($public_args, $public_vals, $section['report_section_query']);
						$query = str_replace('%filter%', $filter, $query);
						$query = str_replace('%order%', $order, $query);
						$comments = array();
						parse_str($section['report_section_columns'],$comments[$table_name]);
						//var_dump($comments[$table_name]);
						$flag_query_src_db = true;
						//echo "$query <br />";
						$result = pg_query($query);
						if (file_exists("$cfg_custom_report_th_dir/$table_name.php")) include ("$cfg_custom_report_th_dir/$table_name.php/$table_name.php");
						elseif(file_exists("$cfg_custom_reports_th_dir/default.php")) include ("$cfg_custom_reports_th_dir/default.php");
						elseif(file_exists("lib/templates/reports/th/default.php")) include ("lib/templates/reports/th/default.php");
						$row_no = 1;
						while ($row = @pg_fetch_assoc($result))
						{
							if (file_exists("$cfg_custom_report_tr_dir/$table_name.php")) include ("$cfg_custom_report_tr_dir/$table_name.php");
							elseif (file_exists("$cfg_custom_report_tr_dir/default.php")) include ("$cfg_custom_report_tr_dir/default.php");
							elseif(file_exists("lib/templates/reports/tr/default.php")) include ("lib/templates/reports/tr/default.php");
							$row_no++;
						}
						echo "</tbody>\n</table>\n";
						$flag_query_src_db = false;
						break;
					}
					case 4: {
						$flag_query_src_default = 1;
						if (empty($view_queries[$table_name])) {
							$query = "SELECT * FROM $table";
						} else {
							$query = $view_queries[$table_name];
						}
						$query .= " $filter $order";
						//var_dump($query);
						$result = pg_query($query);
						$comments = array();
						$comments = display::get_fields_comments();
						asort($comments[$table_name]);
						//var_dump($comments[$table_name]);
						if (file_exists("$custom_report_th_dir/$table_name.php")) include ("$custom_report_th_dir/$table_name.php/$table_name.php");
						elseif(file_exists("$custom_report_th_dir/default.php")) include ("$custom_report_th_dir/default.php");
						elseif(file_exists("lib/templates/reports/th/default.php")) include ("lib/templates/reports/th/default.php");
						$row_no = 1;
						while ($row = @pg_fetch_assoc($result))
						{
							if (file_exists("$custom_report_tr_dir/$table_name.php")) include ("$custom_report_tr_dir/$table_name.php");
							elseif (file_exists("$custom_report_tr_dir/default.php")) include ("$custom_report_tr_dir/default.php");
							elseif(file_exists("lib/templates/reports/tr/default.php")) include ("lib/templates/reports/tr/default.php");
							$row_no++;
						}
						echo "</tbody>\n</table>\n";
						$flag_query_src_default = 0;
						break;
					}
				} //switch
				echo "\n</div>\n<div class=\"break-float\"></div>\n";
			} //while section
			if ($undermost) {
				echo "<div id=\"undermost\"> \n";
				echo $undermost;
				echo "\n</div> \n";
			}
		} //if dbhandle
		return NULL;
	}//function
}//class
?>
