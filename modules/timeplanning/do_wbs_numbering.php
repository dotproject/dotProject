<?php

$dataToSaveWithAjax = $_GET["dataToSaveWithAjax"];
echo $dataToSaveWithAjax ;
$list = explode("~", $dataToSaveWithAjax);
for ($i = 0; $i < sizeof($list); $i++) {
    $data = explode("=", $list[$i]);
    $id = $data [0];
    $number = $data[1];
    $q = new DBQuery();
    $q->addTable('project_eap_items');
    $q->addUpdate('number', $number);
    $q->addWhere('id = ' . $id);
    $q->exec();
    $q->clear();
}
echo "processed!";
die();
?>