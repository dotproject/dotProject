<?php
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

$AppUI->savePlace();

$obj = new CResource;

$titleBlock = new CTitleBlock('Resources', 'helpdesk.png', $m, "$m.$a");
if ($canAuthor) {
    $titleBlock->addCell(('<input type="submit" class="button" value="' . $AppUI->_('new resource') 
	                      . '" />'), '', '<form action="?m=resources&amp;a=addedit" method="post">', 
	                     '</form>');
}
$titleBlock->show();

if (isset($_GET['tab'])) {
    $AppUI->setState('ResourcesIdxTab', $_GET['tab']);
}
$resourceTab = $AppUI->getState('ResourcesIdxTab', 0);
$tabBox = new CTabBox("?m=resources", DP_BASE_DIR.'/modules/resources/', $resourceTab);
$tabbed = $tabBox->isTabbed();
foreach ($obj->loadTypes() as $type) {
    if ($type['resource_type_id'] == 0 && ! $tabbed) {
		continue;
	}
	$tabBox->add('vw_resources', $type['resource_type_name']);
}

$tabBox->show();
?>
