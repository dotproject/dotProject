<?php
if(isset($_POST["group_id_for_deletion"])){
    $class_id = $_POST["class_id"];
    $user_id = $_POST["group_id_for_deletion"];

    $query = new DBQuery();
    $query->addTable("dpp_classes_users");
    $query->addWhere("class_id=". $class_id ." and user_id=". $user_id);
    $sql = $query->prepareDelete();
    $return=db_exec($sql);
    if($return){
        $AppUI->setMsg($AppUI->_("LBL_DATA_SUCCESSFULLY_DELETED"), UI_OUTPUT_HTML, UI_MSG_OK, false);
    }
}
$AppUI->redirect();
?>
