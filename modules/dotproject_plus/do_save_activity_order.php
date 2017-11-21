<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

require_once (DP_BASE_DIR . '/modules/tasks/tasks.class.php');

//save
$project_id = $_POST["project_id"];
$task_id= $_POST["activity_id"];
$task_order= $_POST["task_order"];
$direction= $_POST["direction"];
$wbs_item_id= $_POST["wbs_item_id"];//fazer o envio pela tela do projeto


$q = new DBQuery();
$q->addQuery("task_id, activity_order");
$q->addTable("tasks_workpackages", "t");
$q->addWhere("eap_item_id=" . $wbs_item_id);
$q->addOrder("activity_order");
$sql = $q->prepare();
$records = db_loadList($sql);


$indexMovedTask=-1;
$i=0;
//Get activity order (index)
foreach($records as $record){
    if($record[0]==$task_id){
        $indexMovedTask=$i;
    }
    $i++;
}
$newIndex=$indexMovedTask+$direction;
if( sizeof($records)>$newIndex && $newIndex>=0){//valid bounderies
        
    //switch activities
    $temp=$records[$indexMovedTask];
    $records[$indexMovedTask]=$records[$newIndex];
    $records[$newIndex]= $temp;
    //update order values
    $i=0;
    foreach($records as $record){
        $q = new DBQuery();
        $q->addTable("tasks_workpackages");							
        $q->addUpdate('activity_order', $i);	
        $q->addWhere('task_id = '.$record[0]);		
        $q->exec();
        $i++;
    }
}
    

$AppUI->redirect('m=projects&a=view&project_id='.$project_id);
?>