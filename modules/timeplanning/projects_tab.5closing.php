<?php 
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly');
}

$project_id = $_GET['project_id'];
$q  = new DBQuery;
$q->addTable('post_mortem_analysis', 'pma');
$q->addQuery('pma_id, pma.project_name, project_meeting_date, participants');
$q->addJoin('projects', 'p','p.project_name = pma.project_name');
$q->addWhere('p.project_id = '.$project_id);
$rows = $q->loadList();
$pma_id=0;
foreach ($rows as $p) {
 $pma_id=$p['pma_id'];
} 
require_once DP_BASE_DIR ."/modules/closure/addedit.php" ?>