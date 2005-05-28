<?php 
class projects {
	var $table = 'projects';
	var $search_fields = array ("project_name","project_short_name","project_description","project_url","project_demo_url");
	var $keyword = null;
		
	function cprojects (){
		return new projects();
	}


	function fetchResults(&$permissions){
		global $AppUI;
		$sql = $this->_buildQuery();
		$results = db_loadList($sql);
		$outstring = "<th nowrap='nowrap' >".$AppUI->_('Projects')."</th>\n";
		require_once($AppUI->getModuleClass("projects"));
		if($results){
			foreach($results as $records){
			    if ($permissions->checkModuleItem($this->table, "view", $records["project_id"])) {
			        $obj = new CProject();
                    if (!in_array($records["project_id"], $obj->getDeniedRecords($AppUI->user_id))) {
        				$outstring .= "<tr>";
        				$outstring .= "<td>";
        				$outstring .= "<a href = \"index.php?m=projects&a=view&project_id=".$records["project_id"]."\">".highlight($records["project_name"], $this->keyword)."</a>\n";
        				$outstring .= "</td>\n";
                    }
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
                $q->addQuery('project_id');
                $q->addQuery('project_name');

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
