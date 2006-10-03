<?php 

require_once( $AppUI->getSystemClass( 'CustomFields' ) );

class tasks {
	var $table = 'tasks';
	var $search_fields = array ("task_name","task_description","task_related_url","task_departments",
								"task_contacts","task_custom");
	var $keyword = null;
	
	function ctasks (){
		return new tasks();
	}
	
	function fetchResults(&$permissions){
		global $AppUI, $outstring;
		$results = $this->_buildQuery();
		$cf = new CustomFields('tasks', 'addedit');
		$resultsCf = $cf->search( 'tasks', 'task_id', 'task_name', $this->keyword );
		$outstring = "<th nowrap='nowrap' >".$AppUI->_('Tasks')."</th>\n";
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
					$recordIds[] = $records["task_id"];	
			    if ($permissions->checkModuleItem($this->table, "view", $records["task_id"])) 
    					$this->showResult($records);   
			}
		}
		if($resultsCf && !empty($resultsCf)){
			$res = true;
			foreach($resultsCf as $records){
					if ($permissions->checkModuleItem($this->table, "view", $records["task_id"])) 
							$this->showResult($records, true);
			}
		}
		if (!$res) {
			$outstring .= "<tr>"."<td>".$AppUI->_('Empty')."</td>"."</tr>";
		}
		return $outstring;
	}
	
	function showResult($records, $cf=false){
		global $AppUI, $outstring;
		$outstring .= "<tr>";
		$outstring .= "<td>";
		$outstring .= "<a href = \"index.php?m=tasks&a=view&task_id=".$records["task_id"]."\">".highlight($records["task_name"], $this->keyword)."</a>\n";

		if ($cf)
			$outstring .= ' -- '.highlight($records['value_charvalue'], $this->keyword);

		$outstring .= "</a>\n";
		$outstring .= "</td>\n";
		$outstring .= "</tr>";
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
                return $q->loadList();
	}
}
?>
