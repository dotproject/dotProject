<?php /* SMARTSEARCH$Id$ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

require_once $AppUI->getSystemClass('dp');

class smartsearch  {

	var $table = null;
	var $table_alias = null;
	var $table_module = null;
	var $table_key = null;	// primary key in searched table
	var $table_key2 = null; // primary key in parent table
	var $table_link = null;	// first part of link
	var $table_link2 = null; // second part of link
	var $table_title = null;
	var $table_orderby = null;
	var $table_extra = null;
	var $search_fields = array ();
	var $display_fields = array ();
	var $table_joins = array ();
	var $keyword = null;
	var $keywords = null;
	var $tmppattern = "";
	var $display_val = "";
	var $search_options = null;
//	$search_options['keywords']==array() 		array of searched keywords
//	$search_options['display_all_flds']=="on"	display all fields
//	$search_options['display_all_flds']==""		display only first 2 fields in display_fields array
//	$search_options['ignore_specchar']=="on"	ignore "special" characters
//	$search_options['ignore_specchar']==""		don't ignore "special" characters /default/
//	$search_options['ignore_case']==""			match case
//	$search_options['ignore_case']=="on"		ignore case /default/
//	$search_options['show_empty']==""			hide modules with empty results /default/
//	$search_options['show_empty']=="on"			show modules with empty results
//	$search_options['all_words']==""			match any of the words /default/
//	$search_options['all_words']=="on"			match all words

	function smartsearch(){
		return null;
	}
	
	function createlink($records) {
		$tmplink = "";
		if (isset($this->table_link) && isset($this->table_key)) {
			$tmplink = ($this->table_link 
			            . $records[preg_replace('/^.*\.([^\.]+)$/', '$1', $this->table_key)]);
		}
		
		if (isset($this->table_link2) && isset($this->table_key2)) {
			$tmplink = ($this->table_link . $records[$this->table_key] . $this->table_link2 
			            . $records[preg_replace('/^.*\.([^\.]+)$/', '$1', $this->table_key2)]);
		}
		return $tmplink;
	}
	function fetchResults(&$record_count){
		global $AppUI;
		
		$results = $this->_searchResults();
		if ($results && getPermission($this->table_module, 'access')) {
			$record_count += count($results);
			
			$outstring = ('<tr><th><b>' . $AppUI->_($this->table_title) 
						  . ' (' . count($results) . ')' . '</b></th></tr>' . "\n");
			foreach ($results as $records) {
				if (getPermission($this->table_module, 'view', $records[$this->table_key])) {
					$ii = 0;
					$display_val = '';
					foreach ($this->display_fields as $fld) {
						$ii++;
						if (!($this->search_options['display_all_flds'] == 'on') && ($ii > 2)) {
							break;
						}
						$display_val .= ((($display_val) ? ' ' : '') 
						                 . $records[preg_replace('/^.*\.([^\.]+)$/', '$1', $fld)]);
					}
					
					$tmplink = $this->createlink($records); 
					$outstring .= ('<tr><td>'."\n" . '<a href="' . $tmplink . '">' 
					               . highlight($display_val, $this->keywords) . '</a>' . "\n" 
					               . '</td></tr>' . "\n");
			    }
			}
		}
		else if ($this->search_options['show_empty'] == 'on') {
			$outstring = ('<tr><th><b>' . $AppUI->_($this->table_title) . ' (' . count($results) 
			              . ')' . '</b></th></tr>' . "\n" .'<tr><td>' . $AppUI->_('Empty') 
			              . '</td></tr>' . "\n");
		}
		return $outstring;
	}
	
	function setKeyword($keyw){
		$this->keyword = $keyw;
	}
	function setAdvanced($search_opts){
		$this->search_options = $search_opts;
		$this->keywords = $search_opts['keywords'];
	}
	
	function _searchResults() {
		global $AppUI, $locale_char_set;
		
		$q = new DBQuery;
		$dPObj = new CDpObject($this->table, $this->table_key);
		
		$q->addTable($this->table, $this->table_alias);
		foreach($this->table_joins as $join){
			$q->addJoin($join['table'],$join['alias'],$join['join']);
		}
		
		$q->addQuery($this->table_key);
		if (isset($this->table_key2)) {
			$q->addQuery($this->table_key2);
		}
		foreach ($this->display_fields as $fld) {
			$q->addQuery($fld);
		}
		
		$q->addOrder($this->table_orderby);
		$dPObj->setAllowedSQL($AppUI->user_id, $q, null, null, $this->table_module);
		if ($this->table_extra) {
			$q->addWhere($this->table_extra);
		}
        
		$keys = '';
		$keys2 = '';
		foreach (array_keys($this->keywords) as $keyword) {
			if ($keys2) {
				$keys .= (($this->search_options['all_words'] == 'on') ? ' AND ' : ' OR ');
				$keys2 = '';
			}
			foreach ($this->search_fields as $field) {
				// OR treatment to each keyword
				// Search for semi-colons, commas or spaces and allow any to be separators
				$or_keywords = preg_split('/[\s,;]+/', $keyword);
				foreach ($or_keywords as $or_keyword) {
					$search_pattern = (($this->search_options['ignore_specchar'] == 'on') 
					                   ? recode2regexp_utf8($or_keyword) 
									   : ('(' . $or_keyword . ')'));
					$keys2 .= (($keys2) ? ' OR ' : '');
					$keys2 .= ('(' . $field . ' REGEXP ' 
					           . (($this->search_options['ignore_case'] == 'on') 
					              ? '' : ' BINARY ') . "'.*" . $search_pattern . ".*'" 
							   . ((($this->search_options['ignore_specchar'] == 'on')) 
								  ? ' COLLATE utf8_general_ci' : '') . ')');
				}
			}
			$keys .= (($keys2) ? ('(' . $keys2 . ')') : '');
		}
		
		if ($keys) {
			$q->addWhere($keys);
		}
		
		$results = $q->loadList();
		return ($results);
	}
}

function highlight($text, $keyval) {
	global $ssearch;
	
	$txt = $text;
	$hicolor = array('#FFFF66','#ADD8E6','#90EE8A','#FF99FF');
	$keys=array();
	$keys = ((!(is_array($keyval))) ? array($keyval) : $keyval);
	
	foreach ($keys as $key) {
		if (strlen($key[0])>0) {
			 $key[0] = stripslashes($key[0]);
			if (isset($ssearch['ignore_specchar']) && $ssearch['ignore_specchar'] == 'on') {
				$rep_func = (($ssearch['ignore_case']=='on') ? 'eregi_replace' : 'ereg_replace');
				$txt = $rep_func(recode2regexp_utf8($key[0]), 
								 ('<span style="background:' . $hicolor[$key[1]] .'" >\0</span>'), 
								 $txt );
			} elseif (!(isset($ssearch['ignore_specchar'])) || $ssearch['ignore_specchar'] == '') {
				$rep_func = (($ssearch['ignore_case']=='on') ? 'eregi_replace' : 'ereg_replace');
				$txt = $rep_func($key[0], 
								 ('<span style="background:' . $hicolor[$key[1]] .'" >\0</span>'), 
								 $txt );
			} else {
				$rep_func = 'eregi_replace';
				$txt = $rep_func(sql_regcase($key[0]), 
								 ('<span style="background:' . $hicolor[$key[1]] .'" >\0</span>'), 
								 $txt );
			}
		}
	}
	return $txt; 
}

function recode2regexp_utf8($input) {
      $result="";
      for($i=0, $ln=strlen($input); $i<$ln; ++$i)
      switch($input[$i]) {
          case 'A':
          case 'a':
      		$result.='(' . $input[$i] . "|A!|A¤|A?|A„)";
      		break;
          case 'C':
          case 'c':
      		$result .= '(' . $input[$i] . "|Ä?|ÄO)";
      		break;
          case 'D':
          case 'd':
      		$result .= '(' . $input[$i] . "|Ä?|ÄŽ)";
      		break;
          case 'E':
          case 'e':
      		$result .= '(' . $input[$i] . "|A©|Ä›|A‰|Äš)";
      		break;
          case 'I':
          case 'i':
      		$result .= '(' . $input[$i] . "|A­|A?)";
      		break;
          case 'L':
          case 'l':
      		$result .= '(' . $input[$i] . "|Äo|Ä3|Ä1|Ä1)";
      		break;
          case 'N':
          case 'n':
      		$result .= '(' . $input[$i] . "|A^|A‡)";
      		break;
          case 'O':
          case 'o':
      		$result .= '(' . $input[$i] . "|A3|A´|A“|A”)";
      		break;
          case 'R':
          case 'r':
      		$result .= '(' . $input[$i] . "|A•|A™|A”|A~)";
      		break;
          case 'S':
          case 's':
      		$result .= '(' . $input[$i] . "|A!|A )";
      		break;
          case 'T':
          case 't':
      		$result .= '(' . $input[$i] . "|AY|A¤)";
      		break;
          case 'U':
          case 'u':
      		$result .= '(' . $input[$i] . "|Ao|A—|Aš|A®)";
      		break;
          case 'Y':
          case 'y':
      		$result .= '(' . $input[$i] . "|A1|A?)";
      		break;
          case 'Z':
          case 'z':
      		$result .= '(' . $input[$i] . "|A3|A1)";
      		break;
          default:
      		$result .= $input[$i];
      }
      return $result;
}	
?>
