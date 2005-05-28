<?php 
class companies {
	var $table = 'companies';
	var $search_fields = array ("company_name", "company_address1", "company_address2", "company_city", "company_state", "company_zip", "company_primary_url",
	 							"company_description", "company_email");
	var $keyword = null;
	
	function ccompanies (){
		return new companies();
	}
	
	function fetchResults(&$permissions){
		global $AppUI;
		$sql = $this->_buildQuery();
		$results = db_loadList($sql);
		$outstring = "<th nowrap='nowrap' >".$AppUI->_('Companies'). "</th>\n";
		if($results){
			foreach($results as $records){
			    if ($permissions->checkModuleItem($this->table, "view", $records["company_id"])) {
    				$outstring .= "<tr>";
    				$outstring .= "<td>";
    				$outstring .= "<a href = \"index.php?m=companies&a=view&company_id=".$records["company_id"]."\">".highlight($records["company_name"], $this->keyword)."</a>\n";
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
		$q->addQuery('company_id');
		$q->addQuery('company_name');

		$sql = array();
		foreach($this->search_fields as $field){
			$sql[] = "$field LIKE '%$this->keyword%'";
		}
		if (count($sql))
			$q->addWhere(implode(' OR ', $sql));
		$result =  $q->prepare();
		$q->clear();
		return $result;
	}
}
?>
