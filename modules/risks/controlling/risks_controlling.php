<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RisksControlling
 *
 * @author Rafael
 */
class RisksControlling {

    function getRisksEARCategories($project_id) {
        $options = array();

            $q = new DBQuery();

            $q->addQuery("id, item_name");
            $q->addTable("project_ear_items");
            $q->addWhere("project_id= $project_id and is_leaf=1");
            $sql = $q->prepare();
            $records = db_loadList($sql);

            foreach ($records as $record) {
                $options[$record["id"]] = $record["item_name"];
            }

            if (sizeof($options) == 0) {
                $options[0] = "Sem categoria (A EAR ainda não foi configurada para este projetos)";
            }
        
        return $options;
    }

}

?>
