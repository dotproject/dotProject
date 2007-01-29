<?php /* SMARTSEARCH$Id$ */
class smartsearch  {

	var $table = null;
	var $table_alias = null;
	var $table_module	= null;
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
//	$search_options['display_all_flds']=="on"		display all fields
//	$search_options['display_all_flds']==""		display only first 2 fields from display_fields array
//	$search_opts['keywords']==array() 			array of searched keywords
//	$search_options['ignore_specchar']=="on"	enable  ignoring special eastern national characters / czech and slovak /
//	$search_options['ignore_specchar']==""		disable ignoring  special eastern national characters / czech and slovak /
//	$search_options['ignore_case']==""			match case	
//	$search_options['ignore_case']=="on"		ignore case 	/default/
//	$search_options['show_empty']==""			hide modules with empty results	/default/
//	$search_options['show_empty']=="on"			show modules with empty results	 	
//	$search_options['all_words']==""			any of the words	/default/
//	$search_options['all_words']=="on"			match all words	 	

	function smartsearch(){
		return null;
	}
	
	function createlink() {
	$tmplink="";
	if (isset($this->table_link) && isset($this->table_key)) 
	$tmplink=$this->table_link.$records[preg_replace('/^.*\.([^\.]+)$/','$1',$this->table_key)];
	
	if (isset($this->table_link2) && isset($this->table_key2)) 
	$tmplink=$this->table_link.$records[$this->table_key].$this->table_link2.$records[preg_replace('/^.*\.([^\.]+)$/','$1',$this->table_key2)];
	
	return $tmplink;
	}
	function fetchResults(&$permissions, &$record_count){
		global $AppUI;
		$sql = $this->_buildQuery();
		$results = db_loadList($sql);
		if($results){
			$record_count += count($results);
			$outstring= "<tr><th><b>".$AppUI->_($this->table_title). ' (' . count($results) . ')'."</b></th></tr> \n";
			foreach($results as $records){
			    if ($permissions->checkModuleItem($this->table_module, "view", $records[preg_replace('/^.*\.([^\.]+)$/','$1',$this->table_key)])) {
// --MSy-				
					$ii=0;
					$display_val = "";
					foreach($this->display_fields as $fld){
						$ii++;
						if (!($this->search_options['display_all_flds']=="on") && ($ii>2))
							break;
						$display_val=$display_val." ".$records[preg_replace('/^.*\.([^\.]+)$/','$1',$fld)];
					}	
//--MSy-				
					$tmplink="";
					if (isset($this->table_link) && isset($this->table_key)) 
						$tmplink=$this->table_link.$records[preg_replace('/^.*\.([^\.]+)$/','$1',$this->table_key)];
					if (isset($this->table_link2) && isset($this->table_key2)) 
						$tmplink=$this->table_link.$records[preg_replace('/^.*\.([^\.]+)$/','$1',$this->table_key)].$this->table_link2.$records[preg_replace('/^.*\.([^\.]+)$/','$1',$this->table_key2)];
//--MSy--
					$outstring .= "<tr>";
    				$outstring .= "<td>";
    				$outstring .= "<a href = \" ".$tmplink."\">".highlight($display_val,$this->keywords)."</a>\n";
    				$outstring .= "</td>\n";
                        $outstring .= "</tr>";
			    }
			}
		}
		else {
			if ($this->search_options['show_empty']=="on") {
				$outstring= "<tr><th><b>".$AppUI->_($this->table_title). ' (' . count($results) . ')'."</b></th></tr> \n";
				$outstring .= "<tr>"."<td>".$AppUI->_('Empty')."</td>"."</tr>";
			}
		}
		return $outstring;
	}
	
	function setKeyword($keyw){
		$this->keyword= $keyw;
	}
	function setAdvanced($search_opts){
		$this->search_options = $search_opts;
		$this->keywords= $search_opts['keywords'];
	}
	
	function _buildQuery(){
                $q  = new DBQuery;
                
                if ($this->table_alias) {
                        $q->addTable($this->table, $this->table_alias);
                } else {
                        $q->addTable($this->table);                
                }
                $q->addQuery($this->table_key);
                if (isset($this->table_key2)) {
                        $q->addQuery($this->table_key2);
                }
//--MSy--
                foreach($this->table_joins as $join){
                  	$q->addJoin($join['table'],$join['alias'],$join['join']);
                }

                foreach($this->display_fields as $fld){
                  	$q->addQuery($fld);
                }
                
                $q->addOrder($this->table_orderby);
				
                if ($this->table_extra) {
                	      $q->addWhere($this->table_extra);
   	          }
                
                $sql = '';
                foreach(array_keys($this->keywords) as $keyword) {
				$sql.= '(';

				foreach($this->search_fields as $field){
//OR treatment to each keyword
// Search for semi-colons, commas or spaces and allow any to be separators
						$or_keywords = preg_split('/[\s,;]+/', $keyword);
						foreach ($or_keywords as $or_keyword) {
							if ($this->search_options['ignore_specchar']=="on"){
								$tmppattern = recode2regexp_utf8($or_keyword);
								if ($this->search_options['ignore_case']=="on")
									$sql.=" $field REGEXP '$tmppattern' or ";
								else
									$sql.=" $field REGEXP BINARY '$tmppattern' or ";
							}	
							else
								if ($this->search_options['ignore_case']=="on")
									$sql.=" $field LIKE '%$or_keyword%' or ";
								else
									$sql.=" $field LIKE BINARY '%$or_keyword%' or ";
						}
				} // foreach $field
				$sql = substr($sql,0,-4);

				if ($this->search_options['all_words']=="on") {
						$sql.= ') and ';
				} else {
						$sql.= ') or ';
				}

                } // foreach $keyword
//--MSy--					
                $sql = substr($sql,0,-4);
                if ($sql) {
                	$q->addWhere($sql);
                	return $q->prepare(true);
                } else {
                	return '/* */';
                }
	}
}

function highlight($text, $keyval) {
      global $ssearch;
      
      $txt = $text;
      $hicolor = array("#FFFF66","#ADD8E6","#90EE8A","#FF99FF");
      $keys=array();
      if (!is_array($keyval)) 
      	$keys=array($keyval);
      else
      	$keys=$keyval;
      	
      foreach ($keys as $key) {
      	if (strlen($key[0])>0) {
      	      $key[0] = stripslashes($key[0]);
      		if (isset ($ssearch['ignore_specchar']) && ($ssearch['ignore_specchar']=="on") ) {
      			if ($ssearch['ignore_case']=='on')
            			$txt= eregi_replace ( (recode2regexp_utf8($key[0])), "<span style=\"background:".$hicolor[$key[1]]."\" >\\0</span>", $txt ); 
      			else
            			$txt= ereg_replace ( (recode2regexp_utf8($key[0])), "<span style=\"background:".$hicolor[$key[1]]."\" >\\0</span>", $txt ); 
     			} elseif (!isset($ssearch['ignore_specchar']) || ($ssearch['ignore_specchar']=="") ) {
      			if ($ssearch['ignore_case']=='on')
            			$txt= eregi_replace ( $key[0], "<span style=\"background:".$hicolor[$key[1]]."\" >\\0</span>", $txt ); 
      			else
            			$txt= ereg_replace ( $key[0], "<span style=\"background:".$hicolor[$key[1]]."\" >\\0</span>", $txt );      			
     			} else {
     			    $txt= eregi_replace ( (sql_regcase($key[0])), "<span style=\"background:".$hicolor[$key[1]]."\" >\\0</span>", $txt ); 
		      }
      	}
      }
      return $txt; 
}

function recode2regexp_utf8($input) {
      $result="";
      for($i=0; $i<strlen($input); ++$i)
      switch($input[$i]) {
          case 'A':
          case 'a':
      		$result.="(a|A!|A¤|A?|A„)";
      		break;
          case 'C':
          case 'c':
      		$result.="(c|Ä?|ÄO)";
      		break;
          case 'D':
          case 'd':
      		$result.="(d|Ä?|ÄŽ)";
      		break;
          case 'E':
          case 'e':
      		$result.="(e|A©|Ä›|A‰|Äš)";
      		break;
          case 'I':
          case 'i':
      		$result.="(i|A­|A?)";
      		break;
          case 'L':
          case 'l':
      		$result.="(l|Äo|Ä3|Ä1|Ä1)";
      		break;
          case 'N':
          case 'n':
      		$result.="(n|A^|A‡)";
      		break;
          case 'O':
          case 'o':
      		$result.="(o|A3|A´|A“|A”)";
      		break;
          case 'R':
          case 'r':
      		$result.="(r|A•|A™|A”|A~)";
      		break;
          case 'S':
          case 's':
      		$result.="(s|A!|A )";
      		break;
          case 'T':
          case 't':
      		$result.="(t|AY|A¤)";
      		break;
          case 'U':
          case 'u':
      		$result.="(u|Ao|A—|Aš|A®)";
      		break;
          case 'Y':
          case 'y':
      		$result.="(y|A1|A?)";
      		break;
          case 'Z':
          case 'z':
      		$result.="(z|A3|A1)";
      		break;
          default:
      		$result.=$input[$i];
      }
      return $result;
}	
?>
