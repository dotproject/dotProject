<?php 
class tasks {
	var $table = 'tasks';
	var $search_fields = array ("task_name","task_description","task_related_url","task_departments",
								"task_contacts","task_custom");
	var $keyword = null;
	
	function ctasks (){
		return new tasks();
	}
	
	function fetchResults(&$permissions){
		global $AppUI;
		$sql = $this->_buildQuery();
		$results = db_loadList($sql);
		$outstring = "<th nowrap='nowrap' >".$AppUI->_('Task')."</th>\n";
		if($results){
			foreach($results as $records){
			    if ($permissions->checkModuleItem($this->table, "view", $records["task_id"])) {
    				$outstring .= "<tr>";
    				$outstring .= "<td>";
    				$outstring .= "<a href = \"index.php?m=tasks&a=view&task_id=".$records["task_id"]."\">".highlight($records["task_name"], $this->keyword)."</a>\n";
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
                $q->addQuery('task_id');
                $q->addQuery('task_name');
		$q->addWhere('task_project != 0');

                $sql = '';
                foreach($this->search_fields as $field){
                        $sql.=" $field LIKE '%$this->keyword%' or ";
                }
                $sql = substr($sql,0,-4);
                $q->addWhere("($sql)");
                return $q->prepare(true);
	}
}
?>
