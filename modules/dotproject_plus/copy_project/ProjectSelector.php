<?php

/**
 * Description of ProjectSelector
 * Class gets the selection of projects which a certain user has access.
 * @author Rafael Queiroz Gonçalves
 */
class ProjectSelector {
    
    public function getProjectsUserAcess(){
        $result=array();
        $q = new DBQuery();
        $q->addQuery("project_id, project_name");
        $q->addTable("projects", "p");
        $q->addOrder("project_name");
        $sql = $q->prepare();
        $projects= db_loadList($sql);
        $i=0;
        foreach ($projects as $row) {
            $editProjectsAllowed = (($editProjectsAllowed) || getPermission('projects', 'edit', $row['project_id']));
            if ( getPermission('projects', 'view', $row['project_id']) ){
                $result[$i++]=$row;
            }      
        }
        return $result;
    }
}
