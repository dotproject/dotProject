<?php 
class files_content {
	var $table = 'files_index';
	var $search_fields = array ("word");
	var $follow_up_link = 'fileviewer.php?file_id=';
	var $keyword = null;
	
	function cfiles_content (){
		return new files_content();
	}
	function fetchResults(&$permissions){
		global $AppUI;
		if (!$permissions->checkModule('files', 'view'))
			return "<tr>"."<td>".$AppUI->_('Empty')."</td>"."</tr>";
		$sql = $this->_buildQuery();
		$results = db_loadList($sql);
		$outstring = "<tr><th nowrap='nowrap' STYLE='background: #08245b' >".$AppUI->_('Files Content')."</th></tr>\n";
		if($results){
			foreach($results as $records){
   			$outstring .= "<tr>";
   			$outstring .= "<td>";
				if ($permissions->checkModuleItem('files', "edit", $records["file_id"]))
    			$outstring .= "<a href = \"index.php?m=files&a=addedit&file_id=".$records["file_id"]."\">". dPshowImage( './images/icons/stock_edit-16.png', '16', '16' ) .'</a>';
				$outstring .= '<a href="'.$this->follow_up_link.$records['file_id'].'">'.$records["file_name"].' v.' . $records['file_version'] ."</a> (word {$records['word_placement']})".' &nbsp -- &nbsp '.highlight($records["file_description"], $this->keyword);
   			$outstring .= "</td>\n";
				$outstring .= "</tr>";
			}
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
		$q->addTable('files');
                $q->addQuery('*');
		$q->addWhere("files.file_id = $this->table.file_id");

                $sql = '';
                foreach($this->search_fields as $field){
                        $sql.=" $field LIKE '%$this->keyword%' or ";
                }
                $sql = substr($sql,0,-4);
                $q->addWhere("($sql)");
		$q->addGroup('files.file_id');
                return $q->prepare(true);
	}
}
?>
