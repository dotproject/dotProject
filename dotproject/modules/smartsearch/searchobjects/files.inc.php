<?php 
class files {
	var $table = 'files';
	var $search_fields = array ("file_real_filename","file_name","file_description","file_type");
	var $follow_up_link = 'fileviewer.php?file_id=';
	var $keyword = null;
	
	function cfiles (){
		return new files();
	}
	function fetchResults(&$permissions){
		global $AppUI;
		$sql = $this->_buildQuery();
		$results = db_loadList($sql);
		$outstring = "<th nowrap='nowrap' >".$AppUI->_('Files')."</th>\n";
		if($results){
			foreach($results as $records){
			    if ($permissions->checkModuleItem($this->table, "edit", $records["file_id"])) {
    				$outstring .= "<tr>";
    				$outstring .= "<td>";
    				$outstring .= "<a href = \"index.php?m=files&a=addedit&file_id=".$records["file_id"]."\">".$records["file_name"]."</a>".' &nbsp -- &nbsp '.highlight($records["file_description"], $this->keyword);
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
                $q->addQuery('*');

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
