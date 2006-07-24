<?php 

require_once( $AppUI->getSystemClass( 'CustomFields' ) );

class events {
	var $table = 'events';
	var $search_fields = array ("event_title","event_description");
	var $keyword= null;
	
	function cevents (){
		return new events();
	}
	
	function fetchResults(&$permissions){
		global $AppUI, $outstring;
		$results = $this->_buildQuery();
		$cf = new CustomFields('events', 'addedit');
		$resultsCf = $cf->search( 'events', 'event_id', 'event_title', $this->keyword );
		$outstring = "<th nowrap='nowrap' >".$AppUI->_('Events')."</th>\n";
		$res = false;
		/* parse both arrays - the normal and the one for the custom fields - linearly separately
		** for performance reasons. 
		** Remove double entries in a linear way. Track processed object id
		*/
		if($results && !empty($results)){
			$res = true;
			$recordIds = array();
			foreach($results as $records){
					// add current record id to the list
					$recordIds[] = $records["event_id"];	
			    if ($permissions->checkModuleItem($this->table, "view", $records["event_id"]))
    					$this->showResult($records);
			}
		}
		if($resultsCf && !empty($resultsCf)){	
			$res = true;
			foreach($resultsCf as $records){
				if ($permissions->checkModuleItem($this->table, "view", $records["event_id"])) {
					if (is_array($recordIds) && !in_array($records["event_id"], $recordIds))
						$this->showResult($records);
				}
			}
		}
		if (!$res) {
			$outstring .= "<tr>"."<td>".$AppUI->_('Empty')."</td>"."</tr>";
		}
		return $outstring;
	}

	function showResult($records){
		global $AppUI, $outstring;
		$outstring .= "<tr>";
		$outstring .= "<td>";
		$outstring .= "<a href = \"index.php?m=calendar&a=view&event_id=".$records["event_id"]."\">".highlight($records["event_title"], $this->keyword)."</a>\n";
		$outstring .= "</td>\n";
		$outstring .= "</tr>";
	}

	function setKeyword($keyword){
		$this->keyword = $keyword;
	}
	
	function _buildQuery(){
                $q  = new DBQuery;
                $q->addTable($this->table);
                $q->addQuery('event_id');
                $q->addQuery('event_title');

                $sql = '';
                foreach($this->search_fields as $field){
                        $sql.=" $field LIKE '%$this->keyword%' or ";
                }
                $sql = substr($sql,0,-4);
                $q->addWhere($sql);
                return $q->loadList();
	}
}
?>
