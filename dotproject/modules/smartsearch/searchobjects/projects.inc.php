<?php 

require_once( $AppUI->getSystemClass( 'CustomFields' ) );

class projects {
	var $table = 'projects';
	var $search_fields = array ("p.project_name","p.project_short_name","p.project_description","p.project_url","p.project_demo_url", 'con.contact_last_name', 'con.contact_first_name', 'con.contact_email', 'con.contact_title', 'con.contact_email2', 'con.contact_phone', 'con.contact_phone2', 'con.contact_address1', 'con.contact_notes');
	var $keyword = null;
		
	function cprojects (){
		return new projects();
	}

	function fetchResults(&$permissions){
		global $AppUI, $outstring;
	 	$results = $this->_buildQuery();
		$cf = new CustomFields('projects', 'addedit');
		$resultsCf = $cf->search( 'projects', 'project_id', 'project_name', $this->keyword );
		$outstring = "<th nowrap='nowrap' >".$AppUI->_('Projects')."</th>\n";
		require_once($AppUI->getModuleClass("projects"));
		$res = false;
		/* parse both arrays - the normal and the one for the custom fields - linearly separately
		** for performance reasons. 
		** Remove double entries in a linear way. Track processed record id
		*/
		if($results){	
			$res = true;
			$recordIds = array();
			foreach($results as $records){
					// add current record id to the list
					$recordIds[] = $records["project_id"];					
					if ($permissions->checkModuleItem($this->table, "view", $records["project_id"]))
						$this->showResult($records);
			}	
		}

		if($resultsCf){
			$res = true;
			foreach($resultsCf as $records){
				if ($permissions->checkModuleItem($this->table, "view", $records["project_id"])) 
						$this->showResult($records, true);
			}
		}
		if (!$res) {
			$outstring .= "<tr>"."<td>".$AppUI->_('Empty')."</td>"."</tr>";
		}
		return $outstring;
	}
	
	function showResult($records, $cf=false){
		global $obj, $AppUI, $outstring;
    $obj = new CProject();
    if (!in_array($records["project_id"], $obj->getDeniedRecords($AppUI->user_id))) {
			$outstring .= "<tr>";
			$outstring .= "<td>";
			$outstring .= "<a href = \"index.php?m=projects&a=view&project_id=".$records["project_id"]."\">".highlight($records["project_name"], $this->keyword);

			if ($cf)
				$outstring .= ' -- '.highlight($records['value_charvalue'], $this->keyword);
			
			$outstring .= "</a>\n";

			$outstring .= "</td>\n";
			$outstring .= "</tr>";
		}	
	}

	function setKeyword($keyword){
		$this->keyword = $keyword;
	}
	
	function _buildQuery(){
                $q  = new DBQuery;
                $q->addTable($this->table, 'p');
                $q->addQuery('p.project_id');
                $q->addQuery('p.project_name');
								$q->addJoin('project_contacts', 'pc', 'p.project_id = pc.project_id');
								$q->addJoin('contacts', 'con', 'pc.contact_id = con.contact_id');

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
