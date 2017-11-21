<?php

require_once (DP_BASE_DIR . "/modules/dotproject_plus/copy_project/ProjectSelector.php");

class CopyProjectViewHelper {
    public function getProjectsCombo(){
        $projectSelector= new ProjectSelector();
        $projects=$projectSelector->getProjectsUserAcess();
        $output="<select name='project_to_copy'>";
        $currentProject=$_GET["project_id"];
        foreach($projects as $row){
            if($currentProject!=$row['project_id']){
                $option="<option value='".$row['project_id']."'>";
                $option.= $row['project_name'];
                $option.="</option>";
                $output.=$option;
            }
        }
        $output.="</select>";
        return $output;
    }
    
}
