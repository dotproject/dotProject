<?php 
class events {
	var $table = 'events';
	var $search_fields = array ("event_title","event_description");
	var $keyword= null;
	
	function cevents (){
		return new events();
	}
	
	function fetchResults(&$permissions){
		global $AppUI;
		$sql = $this->_buildQuery();
		$results = db_loadList($sql);
		$outstring = "<th nowrap='nowrap' >".$AppUI->_('Events')."</th>\n";
		if($results){
			foreach($results as $records){
			    if ($permissions->checkModuleItem($this->table, "view", $records["event_id"])) {
    				$outstring .= "<tr>";
    				$outstring .= "<td>";
    				$outstring .= "<a href = \"index.php?m=calendar&a=view&event_id=".$records["event_id"]."\">".highlight($records["event_title"], $this->keyword)."</a>\n";
    				$outstring .= "</td>\n";
			    }
			}
		$outstring .= "</tr>";
		}
		else {
			$outstring .= "<tr>"."<td>".$AppUI->_('Empty')."</td>"."</tr>";
		}
		return $outstring;
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
                return $q->prepare(true);
	}
}
?>
