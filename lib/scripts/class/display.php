<?php
include_once("lib/scripts/class/db.php");
class display
{
	private function check_hidden($str) {
		$hidden = "Hidden";
		if 	(($position = strpos($str,$hidden))===false) return true;
		return false;
	}

	private function check_is_timestamp($str) {
		$ts = "timestamp";
		if 	(($position = strpos($str,$ts))===false) return false;
		return true;
	}

	public function translate($str) {
		$lang = $_COOKIE['lang'];
		//echo "lang: $lang";
		$dbhandle = NULL;
		$was_connected = TRUE;
		if (empty($lang)) {
			$lang = $cfg_default_lang;
			return $str;
		}
		if (!$_SESSION['connected']) {
			$dbhandle = db::connect();
			$was_connected = FALSE;
		} else {
			$dbhandle = $_SESSION['dbhandle'];
		}
		if ($dbhandle) {
			$row = pg_fetch_assoc(pg_query("SELECT translate_string FROM system_schema.strings
																			LEFT JOIN system_schema.translates ON string_id = translate_string_id AND translate_lang_id = '$lang'
																			WHERE string_string = '$str'"));
			$translated = $row['translate_string'];
			if (empty($translated)) {
				$translated = $str;
			}
		}
		if (!$was_connected) {
			db::close($dbhandle);
		}
		//echo "lang: $translated";
		return $translated;
	}

	private function get_dashboard_foot () {
		$html = "";
		$r = pg_query("SELECT properties_value FROM system_schema.properties WHERE properties_type = 'app_info' ORDER BY properties_weight");
		while ($record = pg_fetch_assoc($r)) {
			$html .= $this->translate($record['properties_value']);
		}
		return $html;
	}

	public function refresh()
	{
		$_SESSION['schemas'] = "";
		$_SESSION['tables'] = "";
		$_SESSION['schemas'] = db::get_schemas();
		$table_name = "";
		foreach ($_SESSION['schemas'] as $schema)
		{
			$_SESSION['tables'][$schema] = db::get_tables($schema);
			foreach ($_SESSION['tables'][$schema] as $table)
			{
				$table_name = db::get_table_string($table);
				$_SESSION[$table_name] = db::get_table_struct($table);
			}
		}
		$this -> update_selects_cache();
		setcookie('recordset_action', NULL);
		//$this -> update_lang_cache();
		//$this->fill_fields_types();
		return NULL;
	}

	public function get_page_title () {
		$text = "";
		$dbhandle = db::connect();
		if ($dbhandle) {
			$result = pg_query("SELECT properties_value FROM system_schema.properties WHERE properties_type = 'page_title' ORDER BY properties_weight");
			while ($row = pg_fetch_assoc($result)) {
				$text .= $this->translate($row['properties_value']);
			}
			db::close($dbhandle);
			$text .= $_SESSION['user']['user'];
		}
		return $text;
	}


	public function get_select_options($query)
	{
		$html="";
		if (!empty($query)) {
			$result = pg_query($query);
		}
		if (!empty($result) && isset($result))
		{
			$counter = 0;
			while ($row = pg_fetch_row($result))
			{
				if(substr($row[1],0,1)=='*') {
					if ($counter > 0) $html .= "</ optgroup> \n";
						$row[1] = str_replace(array('*','*'), array("",""), $row[1]);
						$row[1] = trim($row[1]);
						$row[1] = $this->translate($row[1]);
          				$html .= "<optgroup label=\"{$row[1]}\"> \n";
						$html .= "<option value=\"$row[0]\">$row[1]</option> \n";
						$counter ++;
						continue;
          			}
					$row[1] = str_replace('-','', $row[1]);
					$row[1] = trim($row[1]);
					$row[1] = $this->translate($row[1]);
					$html .= "<option $html_class value=\"$row[0]\">$row[1]</option> \n";
			}
		}
		return $html;
	}

	public function update_selects_cache()
	{
		$dbhandle = db::connect();
		if ($dbhandle)
		{
			$query = "SELECT selects_field, select_sql_sql FROM system_schema.selects LEFT JOIN system_schema.selects_sql ON selects_select_sql_id = select_sql_id";
			$result = pg_query($query);
			if ($result)
			{
				while ($row = pg_fetch_assoc($result))
				{
					$a = $row['selects_field'];
					$html = $this -> get_select_options($row['select_sql_sql']);
					//pg_query("UPDATE system_schema.selects SET selects_contents = '$html' WHERE selects_field =  '$a'");
					$_SESSION[$a] = $html;
				}
			}
			db::close($dbhandle);
		}
		return NULL;
	}

/* Nenaudojama
	public function disp_contents($topic)
	{
		global $cfg_topics_dir;
		$dir="$cfg_topics_dir/$topic";
		$files=dirs::lsdir($dir);
		if (!empty($files))
		{
			sort($files);
			foreach($files as $file)
			{
				if (file_exists("$dir/$file")) include("$dir/$file");
			}
		}
	}
*/

	public function make_select_tables() {
		$str="";
		$html="";
		$schema_comment = "";
		$html_class="class = \"option-bold\"";
		asort($_SESSION['schemas']);
		foreach ($_SESSION['schemas'] as $schema) {
			asort($_SESSION['tables'][$schema]);
		 	$schema_comment = db::get_schema_comment($schema);
			$schema_comment = $this -> translate($schema_comment);
			//$html .= "<option $html_class value=\"\">$schema_comment</option> \n";
			$html .= "<optgroup label=\"$schema_comment\"> \n";
			foreach ($_SESSION['tables'][$schema] as $table) {
				$str = explode(":",$table);
				$str[2] = $this -> translate($str[2]);
				if (!($this -> check_hidden($str[2]))) $str[2] = str_replace(array('Hidden', '.'), array('',''), $str[2]);
				$html .= "<option value=\"$str[0]\">$str[2]</option> \n";
			}
			$html .= "</optgroup> \n";
		}
		return $html;
	}

	public function make_select_lang()
	{
		$dbhandle = db::connect();
		$html = "";
		$selected = "";
		$lang = $_COOKIE['lang'];
		if (empty($lang)) {
			$lang = $cfg_default_lang;
		}
		if ($dbhandle)
		{
			$query = "SELECT lang_id, lang_title FROM system_schema.languages";
			$result = pg_query($query);
			if ($result)
			{
				while ($row = pg_fetch_assoc($result))
				{
					if ($lang == $row['lang_id']) $selected = "selected=\"SELECTED\"";
					$html .= "<option value=\"".$row['lang_id']."\" $selected>".$row['lang_title']."</option> \n";
					$selected = "";
					//pg_query("UPDATE system_schema.selects SET selects_contents = '$html' WHERE selects_field =  '$a'");
					//$_SESSION['select_lang'] = $html;
				}
			}
			db::close($dbhandle);
		}
		return $html;
	}

	public function make_select_order()
	{
		$str="";
		$html="";
		$table_name = db::get_table_string($_SESSION['current_table']);
		foreach ($_SESSION[$table_name][$table_name] as $field)
		{
			$str = explode(":", $field);
			$html .= "<option value=\"$str[0]\"";
			if ($str[0] == $_COOKIE['select_order']) $html .= " SELECTED";
			$html .= ">$str[2]</option> \n";
		}
		return $html;
	}

	public function make_select_filter()
	{
		$str="";
		$html="";
		$table_name = db::get_table_string($_SESSION['current_table']);
		foreach ($_SESSION[$table_name][$table_name] as $field)
		{
			$str = explode(":", $field);
			$html .= "<option value=\"$str[0]\">$str[2]</option> \n";
		}
		return $html;
	}

/* Nenaudojama
	public function make_select_columns()
	{
		global $view_queries;
		$str="";
		$html="";
		$table_name=db::get_table_string($_SESSION['current_table']);
		$query=$view_queries[$table_name];
		if (empty($query)) $query="SELECT * FROM ".$_SESSION['current_table']." limit 1";
		$dbhandle=db::connect();
		if ($dbhandle)
		{
			$result=@pg_query($query);
			if (@pg_num_rows($result))
			{
				for ($i=0; $i<@pg_num_fields($result); $i++)
				{
					$html.="<option value=\"".pg_field_name($result, $i)."\">".pg_field_name($result, $i)."</option>";
				}
			}
			db::close($dbhandle);
		}
		return $html;
	}
*/

	public function get_fields_comments()
	{
		$table_name = db::get_table_string($_SESSION['current_table']);
		foreach ($_SESSION[$table_name][$table_name] as $field)
		{
			$str = explode(":", $field);
			$comments[$table_name][$str[0]] = $str[2];
		}
		return $comments;
	}

	public function get_key_field()
	{
		$table_name = db::get_table_string($_SESSION['current_table']);
		$key_field="";
		foreach ($_SESSION[$table_name][$table_name] as $field)
		{
			$str = explode(":", $field);
			$test_str = explode(" ",$str[3]);
			if ($test_str[0]=="PRIMARY")
			{
 				$key_field=$str[0];
 				break;
			}
		}
		return $key_field;
	}

	public function get_field_types() {
		global $view_queries;
		$types = array();
		$i = 0 ;
		$table_name = db::get_table_string($_SESSION['current_table']);
		if ($view_queries[$table_name]) {
			$dbhandle = db::connect();
			if ($dbhandle) {
				$query = $view_queries[$table_name]." LIMIT 1";
				$result = pg_query($query);
				$cols = pg_num_fields($result);
				for ($i = 0; $i < $cols; $i++) {
					$field_name = pg_field_name($result, $i);
					$field_type = pg_field_type($result, $i);
					$types[$field_name] = $field_type;
				}
				db::close($dbhandle);
			}
		} else {
			foreach ($_SESSION[$table_name][$table_name] as $field) {
				$str = explode(":", $field);
				$types[$str[0]] = $str[1];
			}
		}
		return $types;
	}

	function format_key_val($val) {
		$key_field = $this->get_key_field();
		$types = $this->get_field_types();
		$type = $types[$key_field];
    //print_r($types);
		switch($type) {
			case 'text':
			case 'ltree':
			case (substr($type, 0, 9)=="character"): {
				$val="'".$val."'";
				break;
			}
		}
		//echo "debug: $type";
		return $val;
	}

	function _format_key_val($val, $key_field) {
		$types = $this -> get_field_types();
		$type = $types[$key_field];
		switch($type) {
			case 'text':
			case 'ltree':
			case (substr($type, 0, 9)=="character"): {
				$val="'".$val."'";
				break;
			}
		}
		//echo "debug: $type";
		return $val;

	}

	function get_table_title() {
		$str="";
		$title="";
		foreach ($_SESSION['schemas'] as $schema) {
			foreach ($_SESSION['tables'][$schema] as $table) {
				$str = explode(":",$table);
				if ($str[0] == $_SESSION['current_table']) {
					$str[2] = str_replace(array('Hidden', '.'), array('',''), $str[2]);
					$title = $this->translate($str[2]);
					break;
				}
			}
		}
		return $title;
	}

	function get_table_oid() {
		$str=array();
		$oid=NULL;
		foreach ($_SESSION['schemas'] as $schema) {
			foreach ($_SESSION['tables'][$schema] as $table) {
				$str = explode(":",$table);
				if ($str[0] == $_SESSION['current_table']) {
					$oid = $str[1];
					break;
				}
			}
		}
		return $oid;
	}


	function get_perm_filter($table) {
		$dbhandle = db::connect();
		$return = '';
		if ($dbhandle) {
			$query = "SELECT properties_value
			FROM system_schema.properties
			WHERE properties_name = '$table'
			AND properties_type = 'permanent_filter' AND properties_enabled = true";
			$result = pg_query($query);
			$row = pg_fetch_assoc($result);
			$return = $row['properties_value'];
			db::close($dbhandle);
		}
		return $return;
	}

	function get_default_order($comment) {
		$default_order = array();
		$return = "";
		foreach ($comment as $key => $value) {
			$s = explode('|', $value);
			if ($s[6]) {
				$ss = explode('-', $s[6]);
				if (!empty($ss[1])) {
					$default_order["{$ss[0]}"] = "$key {$ss[1]}";
				} else {
					$default_order["{$ss[0]}"] = "$key {$ss[0]}";
				}
			}
		}
		$sizeof = sizeof($default_order);
		if ( $sizeof > 0) {
			ksort($default_order);
			$return = "ORDER BY ".implode(', ', $default_order);
		}
//		var_dump($default_order);
//		echo $return;
		return $return;
	}

	private function get_schema_user_rights($dashboard_id, $schema) {
		$str = array();
		if ($_SESSION['db_admin']) {
			$rights_write = 't';
			$rights_view = 't';
			return TRUE;
		}
		$result_tables = pg_query("SELECT * FROM system_schema.dashboard_items
								WHERE dashboard_item_dashboard_id = $dashboard_id
								ORDER BY dashboard_item_weight");
		if ($result_tables) {
			$return = FALSE;
			while ($tables_row = pg_fetch_assoc($result_tables)) {
				$link = $tables_row['dashboard_item_link'];
				$table = $schema.".".$tables_row['dashboard_item_table'];
				$rights_write = $_SESSION['user_rights'][$table]['write'];
				$rights_view = $_SESSION['user_rights'][$table]['view'];
				if (($rights_view === 't') || ($rights_write === 't')) {
					foreach ($_SESSION['tables'][$schema] as $s_table) {
						$str = explode(":", $s_table);
						if ($str[0] == $table) {
							if ($this -> check_hidden($s_table)) {
								$return = TRUE;
							}
						}
					}
				}
			}
		}
		return $return;
	}

	function disp_dashboard($columns) {
		$width_percent = intval(100/$columns);
		$width_percent .= "%";
		//$columns=$columns+1;
		$str="";
		$dbhandle = db::connect();
		if ($dbhandle) {
			$result = pg_query("SELECT * FROM system_schema.dashboard ORDER BY dashboard_schema_weight");
			if ($result) {
				$i = 0;
				echo "<table class=\"dashboard\">\n<tr>\n";
				while ($schemas_row = pg_fetch_assoc($result)) {
					$dashboard_id = $schemas_row['dashboard_id'];
					$schema_title = $this->translate($schemas_row['dashboard_schema_title']);
					$schema = $schemas_row['dashboard_schema'];
					$schema_notes = $this->translate($schemas_row['dashboard_schema_notes']);
					$schema_icon = $schemas_row['dashboard_schema_icon'];
					$user_has_schema_rights = $this -> get_schema_user_rights($dashboard_id, $schema);
					//echo "ur: $user_has_schema_rights";
					if ($user_has_schema_rights) {
						echo "<td class=\"dashboard\" style=\"width: $width_percent;\"> \n";
						echo "<div class = \"schema-icon\"><img src = \"lib/img/icons/$schema_icon\" class = \"schema-icon\"></div>";
						echo "<h1 title = \"$schema_notes\">$schema_title</h1>\n";
						echo "<p class=\"dashboard-notes\">".nl2br($schema_notes)."</p>\n";
						$result_tables = pg_query("SELECT * FROM system_schema.dashboard_items
												WHERE dashboard_item_dashboard_id = $dashboard_id
												ORDER BY dashboard_item_weight");
						echo "<ul>\n";
						$html="";
						while ($tables_row = pg_fetch_assoc($result_tables)) {
							$link = $tables_row['dashboard_item_link'];
							$table = $schema.".".$tables_row['dashboard_item_table'];
							$table_title = $this -> translate($tables_row['dashboard_item_title']);
							$table_notes = $this -> translate($tables_row['dashboard_item_notes']);
							$rights_write = $_SESSION['user_rights'][$table]['write'];
							$rights_view = $_SESSION['user_rights'][$table]['view'];
							if ($_SESSION['db_admin']) {
								$rights_write = 't';
								$rights_view = 't';
							}
							if (($rights_view === 't') || ($rights_write === 't')) {
								$html .= "<li class = \"dashboard-li\">";
								$html .= "<a class=\"dashboard-link\" href=\"#\" title = \"$table_notes\" OnClick=\"javascript:AjaxShow('menu:current_table:$table:recordset.php:contents:select_rows:'+getElementById('select_rows').value)\">$table_title</a> \n";
								$html .= "<p class = \"dashboard-notes\">".nl2br($table_notes)."</p> \n";
								$html .= "</li> \n";
							}
						}
						echo "$html </ul></td> \n";
						$i++;
						if ( (($i) % $columns) == 0 ) echo "</tr>\n<tr>\n";
					}
				}
				echo "</tr>\n</table>\n";
			}
			echo "<div class = \"div-page-foot-container\"> \n <div class=\"div-page-foot\"> \n";
			echo $this -> get_dashboard_foot();
			echo "</div> \n </div> \n";
			db::close($dbhandle);
		}
//		var_dump($_SESSION['tables']);
		return NULL;
	}

	function disp_toolbar() {
		global $cfg_custom_toolbars_dir;
		$table_name = db::get_table_string($_SESSION['current_table']);
		$custom_toolbar = $cfg_custom_toolbars_dir."/$table_name.php";
//		echo "debug: $custom_toolbar";
		if (file_exists($custom_toolbar)) {
			include($custom_toolbar);
		}
		$toolbar = "lib/layout/toolbars/$table_name.php";
		if (file_exists($toolbar)) {
			include($toolbar);
		}
	}


	function disp_msg($txt, $options, $parent) {
		//$txt.="";
		//$txt = $this -> translate($txt);
		global $cfg_custom_layout_dir;
		global $cfg_layout_dir;
		$cmd_back = BACK;
		$cmd_close = CLOSE;
		echo "\n<div class=\"div-title\">\n<div style=\"position: relative; float: left;\">";
		if (!empty($options)) {
			switch ($options) {
				case 'back': {
					echo "<input type=\"image\" src=\"lib/img/recordset/cross.png\" title=\"$cmd_back\" value=\"Atgal\" align=\"top\" OnClick=\"deletecookie('key'); HideElement('forms'); HideElement('overlay'); AjaxShow(':::recordset.php:contents:')\">";
					break;
				}
				case 'close': {
					echo "<input type=\"image\" src=\"lib/img/recordset/cross.png\" title=\"$cmd_close\" value=\"Uždaryti\" align=\"top\" OnClick=\"deletecookie('key'); AjaxShow('menu:current_table::recordset.php:contents:')\">";
					break;
				}
				case 'toogle': {
					echo "<input type=\"image\" src=\"lib/img/recordset/cross.png\" title=\"$cmd_close\" value=\"Uždaryti\" align=\"top\" OnClick=\"javascript: ToogleElement('filter_div')\">";
					break;
				}
			}
		}
		echo " $txt \n</div>";
		if ($parent == 'table') {
			if (file_exists("$cfg_custom_layout_dir/menu.php")) {
				include("$cfg_custom_layout_dir/menu.php");
			} else {
				include("$cfg_layout_dir/menu.php");
			}
		}
		echo "<div class=\"break-float\"></div></div>\n";
	}

	function disp_filter_form($table_name, $comments, $types) {
		global $cfg_forms_dir;
		global $cfg_custom_forms_dir;
		$select_filter = "";
		asort($comments[$table_name]);
		foreach ($comments[$table_name] as $fn => $c) {
			$str = explode('|', $c);
			if (!empty($str[3])) {
				$fn = $str[3];
			}
			//$fn .= ":{$types[$fn]}";
			//var_dump($fn);
			$str[2] = $this -> translate($str[2]);
			$select_filter .= "<option value=\"$fn\">{$str[2]}</option> \n";
		}
		//var_dump($types);
		if (file_exists("$cfg_custom_forms_dir/filter/$table_name.php")) {
			include("$cfg_custom_forms_dir/filter/$table_name.php");
		} elseif (file_exists("$cfg_forms_dir/filter/default.php")) {
			include("$cfg_forms_dir/filter/default.php");
		}
	}


	function disp_pager($pages)
	{
		echo "<table> \n <tr> \n <td>";
		$ptitle = PAGE;
		$of = OF;
		if (($_COOKIE['select_page'])&&($_COOKIE['select_page']>1))
		{
			$pg = $_COOKIE['select_page']-1;
			echo "<a href=\"#\" OnClick=\"AjaxShow(':::recordset.php:contents:select_page:$pg')\">&laquo;</a>";
		}	else echo "&nbsp;";
		echo "</td> \n";
		echo "<td><strong>$ptitle </strong></td> \n <td> \n";
		echo "<select id=\"select_page\" title=\"$ptitle\" OnChange=\"AjaxShow(':::recordset.php:contents:select_page:'+this.value)\">";
		$options = "";

		for ($i = 1; $i <= $pages; $i++)
		{
			$options .= "<option value='$i' ";
			if ($i == (int)$_COOKIE['select_page']) $options .= "SELECTED";
			$options .= ">$i</option> \n";
		}
		echo $options;
		echo "</select> \n </td> \n <td><strong> $of $pages</strong></td> \n <td> \n";
		if (($_COOKIE['select_page'])&&((int)$_COOKIE['select_page']<$pages))
		{
			$pg = $_COOKIE['select_page']+1;
			echo "<a href=\"#\" OnClick=\"AjaxShow(':::recordset.php:contents:select_page:$pg')\">&raquo;</a>";
		}	else echo "&nbsp;";
		$this->disp_toolbar();
		echo "</td> \n </tr> \n </table> \n";
	}

	function disp_table()
	{
		global $view_queries;
		global $cfg_files_dir;
		global $cfg_max_text_words;
		global $cfg_custom_th_dir;
		global $cfg_custom_tr_dir;
		$table = $_SESSION['current_table'];
		$user_id = $_SESSION['user']['id'];
		$is_filterable = $_SESSION['user_rights'][$table]['filter'];
		$filter_column = "";
		$omsg = SORTED_BY;
		$tmsg = TOTAL_RECORDS;
		$fmsg = FILTERED_BY;
		if ($is_filterable == 't') {
			$filter_column = $_SESSION['user_rights'][$table]['filter_column'];
			$filter_share = $_SESSION['user_rights'][$table]['filter_share'];
			if (!empty($filter_share)) {
				$filter_share = explode(',',$filter_share);
			}
		}
		$table_name = db::get_table_string($table);
		$key = $this -> get_key_field();
		$comments = $this -> get_fields_comments();
		$order = $this -> get_default_order($comments[$table_name]);
		$types = $this -> get_field_types();
		$perm_filter = $this -> get_perm_filter($table);
		//if (!$order) $order = "ORDER BY $key ASC";
		$msg="";
		$dbhandle = db::connect();
		if (!empty($dbhandle))
		{
			$limit="";
			if (!empty($_COOKIE['select_rows'])) $limit = "limit ".$_COOKIE['select_rows'];
			if (!empty($_COOKIE['select_order']))
			{
				$select_order = $_COOKIE['select_order'];
				$order_dir = $_SESSION['order_dir'];
				$order_mark = 'A-Z';
				if ($order_dir=='DESC') $order_mark = 'Z-A';
				$order = "ORDER BY $select_order $order_dir";
				//if (($order=="ORDER BY ASC") || ($order=="ORDER BY DESC")) $order="ORDER BY $key ASC";
				$msg .= "$omsg $select_order $order_mark|";
			}
			$filter = "";
			if (isset($_COOKIE['select_filter']))
			{
				$filter = "WHERE ".urldecode($_COOKIE['select_filter']);
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
				if (!empty($perm_filter)) {
					$filter .= " AND $perm_filter";
				}
				$msg .= "$fmsg $filter| ";
			}
			if ((!isset($_COOKIE['select_filter'])) && (!empty($filter_column))) {
				if (!empty($filter_share)) {
					$filter = " WHERE ($filter_column = $user_id";
					foreach($filter_share as $share) {
						$filter .= " OR $filter_column = $share";
					}
					$filter .= ")";
				} else {
					$filter = " WHERE $filter_column = $user_id";
				}
				if (!empty($perm_filter)) {
					$filter .= " AND $perm_filter";
				}
			}
			if ((!isset($_COOKIE['select_filter'])) && (!empty($perm_filter))) {
				$filter = " WHERE $perm_filter";
			}
			$query = $view_queries[$table_name];
			if (empty($query)) $query="SELECT * FROM $table";
			$query .= " $filter";
			$result = @pg_query($query);
			$total_rows = @pg_num_rows($result);
			$msg .= "$tmsg $total_rows | ";
			if (!empty($_COOKIE['select_rows']))
			{
				$pages = ceil($total_rows/((int)$_COOKIE['select_rows']));
			}
			$offset = "offset 0";
			$row_no = 1;
			if (!empty($_COOKIE['select_page']))
			{
				$offset = "offset ".(((int)$_COOKIE['select_page'] - 1)*((int)$_COOKIE['select_rows']));
				$row_no = (((int)$_COOKIE['select_page'] - 1)*((int)$_COOKIE['select_rows']))+1;
			}
			$query = $view_queries[$table_name];
			if (empty($query)) $query="SELECT * FROM $table";
			$query .= " $filter $order $offset $limit";
			//echo "debug: ".$_SESSION['recordset_action']." <br />\n";
			//echo "debug: "; print_r($_SESSION['user_rights']); echo "<br />";
			//echo "debug: $query <br />";
			$result = pg_query($query);
			$title = $this->get_table_title();
			$this->disp_msg($title,"close","table");
			$this -> disp_filter_form($table_name, $comments, $types);
			if (!empty($pages))
			{
				$this->disp_pager($pages);
			}
			//var_dump($cfg_custom_th_dir);
			if (file_exists("$cfg_custom_th_dir/$table_name.php")) include ("$cfg_custom_th_dir/$table_name.php");
			elseif (file_exists("$cfg_custom_th_dir/default.php")) include ("$cfg_custom_th_dir/default.php");
			elseif(file_exists("lib/templates/th/default.php")) include ("lib/templates/th/default.php");
			while ($row = @pg_fetch_assoc($result))
			{
				if (file_exists("$cfg_custom_tr_dir/$table_name.php")) include ("$cfg_custom_tr_dir/$table_name.php");
				elseif (file_exists("$cfg_custom_tr_dir/default.php")) include ("$cfg_custom_tr_dir/default.php");
				elseif(file_exists("lib/templates/tr/default.php")) include ("lib/templates/tr/default.php");
				$row_no++;
			}
			db::close($dbhandle);
			echo "</table> \n </form> \n";
			if (!empty($pages))
			{
				$this->disp_pager($pages);
			}
		}
	$this->disp_msg($msg,"","");
	return 0;
	}


	function disp_form($query)
	{
		global $cfg_files_dir;
		global $cfg_forms_dir;
		global $cfg_custom_files_dir;
		global $cfg_custom_forms_dir;
		switch ($_COOKIE['recordset_action']) {
			case 'insert': {
				$msg = INSERT;
				$dir = 'edit';
				break;
			}
			case 'delete': {
				$msg = ARE_YOU_SHURE_TO_DELETE;
				$dir = 'edit';
				break;
			}
			case 'update': {
				$msg = UPDATE;
				$dir = 'edit';
				break;
			}
			case 'find': {
				$msg = FIND;
				$dir = 'filter';
				break;
			}
			case 'copy': {
				$msg = COPY;
				$dir = 'edit';
				break;
			}
			case 'sort': {
				$msg = SORT;
				$dir = 'sort';
				break;
			}
			case 'browse': {
				$msg = BROWSE;
				$dir = 'edit';
				break;
			}
		}
		//$msg=$this -> translate($msgs[$_COOKIE['recordset_action']]);
		$txt="";
		$table_name = db::get_table_string($_SESSION['current_table']);
		$file = $table_name.".php";
		$comments = $this->get_fields_comments();
		$select_order = $this->make_select_order();
		$select_filter = $this->make_select_filter();
		$row=array();
//		$dir="edit";
		$key = $this->get_key_field();
		switch ($_COOKIE['recordset_action'])
		{
			case 'update':
			case 'delete':
			case 'copy':
			case 'browse':
			{
				if (!empty($_COOKIE['key']))
				{
					$dbhandle = db::connect();
					if ($dbhandle)
					{
						$result = @pg_query($query);
						$g = pg_last_error();
						db::close($dbhandle);
						if (!empty($g))
						{
							echo "<div id=\"errors\" class=\"errors\" OnmouseOver=\"HideElement('errors')\">$g</div>";
						}
						$row = @pg_fetch_assoc($result);
					}
					//var_dump($cfg_custom_forms_dir);
					if (file_exists("$cfg_custom_forms_dir/$dir/$file"))
					{

						$this->disp_msg($this->get_table_title(), "back","form");
						include("$cfg_custom_forms_dir/$dir/$file");
						$this->disp_msg($msg,"","form");
					} else
					{
						if (file_exists("$cfg_forms_dir/$dir/default.php"))
						{
							$this->disp_msg($this->get_table_title(), "back","form");
							include("$cfg_forms_dir/$dir/default.php");
							$this->disp_msg($msg,"","form");
						} else echo "<div id=\"messages\" class=\"messages\" OnmouseOver=\"HideElement('messages'); HideElement('overlay'); HideElement('forms')\">".NO_FORM."</div>";
					}
				} else
				{
					echo "<div id=\"messages\" class=\"messages\" OnmouseOver=\"HideElement('messages'); HideElement('overlay'); HideElement('forms')\">".SELECT_RECORD."</div>";
				}
				break;
			}
			case 'insert':
			{
				if (file_exists("$cfg_custom_forms_dir/$dir/$file"))
				{
					$this->disp_msg($this->get_table_title(), "back","form");
					include("$cfg_custom_forms_dir/$dir/$file");
					$this->disp_msg($msg,"","form");
				} else
				{
					if (file_exists("$cfg_forms_dir/$dir/default.php"))
					{
						$this->disp_msg($this->get_table_title(), "back","form");
						include("$cfg_forms_dir/$dir/default.php");
						$this->disp_msg($msg,"","form");
					} else echo "<div id=\"messages\" class=\"messages\" OnmouseOver=\"HideElement('messages'); HideElement('overlay'); HideElement('forms')\">".NO_FORM."</div>";
				}
				break;
			}
		}

		return 0;
	}

//related table. čia viska reikia perdaryti.)


	function get_related_field_types()
	{
		$types = array();
		$i = 0 ;
		$table_name = db::get_table_string($_SESSION['related_table']);
		foreach ($_SESSION[$table_name][$table_name] as $field)
		{
			$str = explode(":", $field);
			$types[$str[0]] = $str[1];
		}
		return $types;
	}

	function format_related_key_val($val)
	{
		$key_field = $this->get_related_key_field();
		$types = $this->get_related_field_types();
		$type = $types[$key_field];
		switch($type)
		{
			case 'text':
			case (substr($type, 0, 9)=="character"):
			{
				$val="'".$val."'";
				break;
			}
		}
		return $val;
	}

	function get_related_key_field()
	{
		$table_name = db::get_table_string($_SESSION['related_table']);
		$key_field="";
//		print_r($_SESSION[$table_name][$table_name]); echo "<br />";
//		print_r($table_name);
		foreach ($_SESSION[$table_name][$table_name] as $field)
		{
			$str = explode(":", $field);
			$test_str = explode(" ",$str[3]);
			if ($test_str[0]=="PRIMARY")
			{
 				$key_field=$str[0];
 				break;
			}
		}
		return $key_field;
	}

	function get_related_table_title()
	{
		$str="";
		$title="";
		foreach ($_SESSION['schemas'] as $schema)
		{
			foreach ($_SESSION['tables'][$schema] as $table)
			{
				$str = explode(":",$table);
				if ($str[0] == $_SESSION['related_table'])
				{
					$title = $this -> translate($str[2]);
					break;
				}
			}
		}
		if (!($this->check_hidden($title))) $title = str_replace('. Hidden.', '', $title);
		return $title;
	}

	function get_related_fields_comments()
	{
		$table_name = db::get_table_string($_SESSION['related_table']);
		foreach ($_SESSION[$table_name][$table_name] as $field)
		{
			$str = explode(":", $field);
			$comments[$table_name][$str[0]] = $str[2];
		}
		return $comments;
	}

	function disp_related_table_head($mode,$view_columns)
	{
		global $cfg_custom_layout_dir;
		global $cfg_layout_dir;
		global $view_queries;
		global $cfg_files_dir;


		$table_name = db::get_table_string($_SESSION['related_table']);
		//$topic=$_SESSION['topic'];
		$comments=$this->get_related_fields_comments();
		if ($mode=='rw') {
			if (file_exists("$cfg_custom_layout_dir/sub-menu.php")) {
				if ($_COOKIE['recordset_action'] !== 'browse') include("$cfg_custom_layout_dir/sub-menu.php");
			} else {
	    		if (file_exists("$cfg_layout_dir/sub-menu.php")) {
					if ($_COOKIE['recordset_action'] !== 'browse') include("$cfg_layout_dir/sub-menu.php");
    			}
    		}
    	}
		if (file_exists("$cfg_custom_th_dir/related/$table_name.php")) {
			include("$cfg_custom_th_dir/related/$table_name.php");
		} else {
		if (file_exists("lib/templates/th/related/related_default.php"))
			include ("lib/templates/th/related/related_default.php");
		}
		return NULL;
	}

public function disp_related_table_in_form($row) {
$tbl = trim($_SESSION['current_table']);
$dbhandle = db::connect();
if ($dbhandle)
{
	$rslt = pg_query("SELECT * FROM system_schema.relations WHERE relations_table = '$tbl' ORDER BY relations_weight ASC");
	db::close($dbhandle);
	$tabs = pg_num_rows($rslt);
	if ($tabs) {
		echo "<div class=\"tabs-container\" id=\"tabs-container\">\n";
		echo "<a href=\"#\" OnClick = \"javascript: hidetabs()\"><img src = \"lib/img/recordset/tab_delete.png\" class=\"tabs-hide\"></a>";
		echo "<ul class=\"tabs-ul\">\n";
		$tab_no = 1;
		$li_active = '';
		while ($rw = pg_fetch_assoc($rslt)) {
			$_SESSION['related_table'] = $rw['relations_relates_on_table'];
			$tab_title = $this -> translate($this -> get_related_table_title());
			if ($_COOKIE['tab'] == $tab_no) $li_active = 'tabs-li-active';
			echo "<li id=\"tabs-li-$tab_no\" class=\"tabs-li $li_active\"><a href=\"#\" class=\"tabs-a\" OnClick=\"javascript: document.cookie='tab='+$tab_no; changetab('$tab_no');\">$tab_title</a></li>\n";
			$li_active = '';
			$tab_no ++;
		}
		echo "</ul>\n <div class=\"break-float\"></div>\n";
		$tab_no = 1;
		$div_style = '';
		pg_result_seek($rslt,0);
		while ($rw = pg_fetch_assoc($rslt)) {
			$_SESSION['related_table'] = $rw['relations_relates_on_table'];
			if (empty($rw['relations_operator'])) $rw['relations_operator'] = '=';
			$query_end = $rw['relations_relates_on_field']." {$rw['relations_operator']} ".$this->_format_key_val($row[($rw['relations_field'])], $rw['relations_field']);
			if (!empty($rw['relations_add_sql'])) {
				$query_end .= " {$rw['relations_add_sql']}";
			}
		 	$mode = $rw['relations_display_mode'];
			$group_by = $rw['relations_group_by'];
			if ($is_disabled_flag) $mode = 'r';
			if ($_COOKIE['tab'] == $tab_no) $div_style = "style = \"display: block;\"";
			echo "<div id=\"tab-$tab_no\" class=\"tab-div\" $div_style>\n";
			$this -> disp_related_table($query_end, $group_by, $mode, $row);
			$tab_no ++;
			$div_style = '';
			//echo "debug: $query_end";
			echo "</div>\n";
		}
		echo "</div>\n";
		}
	}
	return NULL;
}


	function disp_related_table($where, $group_by, $mode, $row)
	{
		global $view_queries;
		global $cfg_files_dir;
		global $cfg_max_text_words;
		global $cfg_custom_th_dir;
		global $cfg_custom_tr_dir;
		//$topic = $_SESSION['topic'];
        $mother_table = $_SESSION['current_table'];
        $mother_table_name = db::get_table_string($mother_table);
		$table = $_SESSION['related_table'];
		$table_name = db::get_table_string($table);
		//$test=$view_queries[$table_name];
		$key = $this->get_related_key_field();
		$msg = "";
		$comments = $this->get_related_fields_comments();
		$key = $this->get_related_key_field();
		$order = $this->get_default_order($comments[$table_name]);
		//$my_table = $_SESSION['current_table'];
		//$my_related_table = $_SESSION['related_table'];
		$view_columns = array();
		$dbhandle = db::connect();
		if (!empty($dbhandle))
		{
			$query = $view_queries[$mother_table_name."_".$table_name];
		    if (empty($query)) $query = $view_queries[$table_name];
			if (empty($query)) $query="SELECT * FROM $table";
			$query.=" WHERE $where";
			if (!empty($group_by)) $query .=" GROUP BY $group_by";
			if (!empty($order)) $query.=" $order";
			foreach ($row as $rkey => $rvalue) {
				$query = str_replace("%$rkey%", $rvalue, $query);
			}
			$query = stripslashes($query);
			//echo "debug: $query <br />";
			$result = pg_query($query);
			echo "<div class=\"div-related-title\">$title</div>";
			$rrr = pg_query("SELECT relations_view_columns FROM system_schema.relations WHERE relations_table = '$mother_table' AND relations_relates_on_table = '$table'");
			if ($rrr) {
				$rr = pg_fetch_assoc($rrr);
				$view_columns = explode(',',$rr['relations_view_columns']);
			}
			$this->disp_related_table_head($mode, $view_columns);
			$row_no=1;
			//echo "key: $key";
			while ($row = pg_fetch_assoc($result))
			{
				if (file_exists("$cfg_custom_tr_dir/related/$table_name.php")) {
					include ("$cfg_custom_tr_dir/related/$table_name.php");
				} else {
					if (file_exists("lib/templates/tr/related/related_default.php")) {
						include ("lib/templates/tr/related/related_default.php");
					}
				}
				$row_no++;
			}
			echo "\n</table>"; //echo "debug: $table_name $query";
			db::close($dbhandle);
		}
		return 0;
	}


	function disp_related_form($query) {
		global $cfg_custom_forms_dir;
		global $cfg_forms_dir;
		switch ($_COOKIE['related_recordset_action']) {
			case 'insert': {
				$msg = INSERT;
				$dir = 'edit/related';
				break;
			}
			case 'delete': {
				$msg = ARE_YOU_SHURE_TO_DELETE;
				$dir = 'edit/related';
				break;
			}
			case 'update': {
				$msg = UPDATE;
				$dir = 'edit/related';
				break;
			}
			case 'find': {
				$msg = FIND;
				$dir = 'search/related';
				break;
			}
			case 'copy': {
				$msg = COPY;
				$dir = 'edit/related';
				break;
			}
			case 'sort': {
				$msg = SORT;
				$dir = 'sort/related';
				break;
			}
			case 'browse': {
				$msg = BROWSE;
				$dir = 'edit/related';
				break;
			}
		}
		$txt="";
		$table = $_SESSION['related_table'];
		$table_name = db::get_table_string($table);
		$file = $table_name.".php";
		$comments = $this->get_related_fields_comments();
		$row=array();
		//$dir="edit/related";
		$key = $this->get_related_key_field();
		switch ($_COOKIE['related_recordset_action'])
		{
			case 'update':
			case 'delete':
			case 'copy':
			{
				if (!empty($_COOKIE['related_key']))
				{
					$dbhandle = db::connect();
					if (!empty($dbhandle))
					{
						$result = @pg_query($query);

						$g = pg_last_error();
						db::close($dbhandle);
						if (!empty($g))
						{
							echo "<div style='text-align: center; font-weight: bold; color: red;'>Klaida!<br></div>";
							echo "$g <br />\n"; echo "$query <br /> \n";
						}
						$row = @pg_fetch_assoc($result);
					}
					if (file_exists("$cfg_custom_forms_dir/$dir/$file"))
					{
						$this->disp_related_msg($this->get_related_table_title());
						include("$cfg_custom_forms_dir/$dir/$file");
						$this->disp_msg($msg,"","form");
					} else {
						if (file_exists("$cfg_forms_dir/$dir/related_default.php"))
						{
							$this->disp_related_msg($this->get_related_table_title());
							include("$cfg_forms_dir/$dir/related_default.php");
							$this->disp_msg($msg,"","form");
						} else {
							echo "<div id=\"messages\" class=\"messages\" OnmouseOver=\"HideElement('messages');\">".NO_FORM."</div>";
						}
					}
				} else
				{
					echo "<div id=\"messages\" class=\"messages\" OnmouseOver=\"HideElement('messages'); HideElement('related'); HideElement('related-overlay')\">".SELECT_RECORD."</div>";
				}
				break;
			}
			case 'insert':
			{
				if (file_exists("$cfg_custom_forms_dir/$dir/$file")) {
					$this->disp_related_msg($this->get_related_table_title());
					include("$cfg_custom_forms_dir/$dir/$file");
					$this->disp_msg($msg,"","form");
				} else {
					if (file_exists("$cfg_forms_dir/$dir/related_default.php"))
					{
						$this->disp_related_msg($this->get_related_table_title());
						include("$cfg_forms_dir/$dir/related_default.php");
						$this->disp_msg($msg,"","form");
					} else {
							echo "<div id=\"messages\" class=\"messages\" OnmouseOver=\"HideElement('messages');\">".NO_FORM."</div>";
					}
				}
				break;
			}
		}
		return 0;
	}

	function disp_related_msg($txt)
	{
		$txt.="&nbsp;";
		echo "<div class=\"div-title\">";
		echo "<a href=\"#\"  OnClick=\"deletecookie('related_key'); HideElement('related'); HideElement('related-overlay'); AjaxShow(':::edit.php:forms:')\"><img src=\"lib/img/recordset/cross.png\" title=\"Atgal\"></a>";
		echo " $txt</div>";
	}
// /related table
}
?>
