<?php 
class contacts {
	var $table = 'contacts';
	var $search_fields = array ("contact_first_name","contact_last_name","contact_title","contact_company","contact_type","contact_email",
								"contact_email2","contact_address1", "contact_address2", "contact_city", "contact_state", "contact_zip",
								"contact_country","contact_notes");
	var $keyword = null;
	
	function ccontacts (){
		return new contacts();
	}
	
	function fetchResults(&$permissions){
		global $AppUI;
		$sql = $this->_buildQuery();
		$results = db_loadList($sql);
		$outstring = "<th nowrap='nowrap' STYLE='background: #08245b' >".$AppUI->_('Contacts')."</th>\n";
		if($results){
			foreach($results as $records){
			    if ($permissions->checkModuleItem($this->table, "view", $records["contact_id"])) {
    				$outstring .= "<tr>";
    				$outstring .= "<td>";
    				$outstring .= "<a href = \"index.php?m=contacts&a=addedit&contact_id=".$records["contact_id"]."\">".highlight($records["contact_first_name"]." ".$records["contact_last_name"], $this->keyword)."</a>\n";
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
                $q->addQuery('contact_id');
                $q->addQuery('contact_first_name');
                $q->addQuery('contact_last_name');

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
