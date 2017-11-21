<?php
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

$AppUI->savePlace();

$tab = $AppUI->getState('InitiatingStakeholderIdxTab') !== NULL ? $AppUI->getState('InitiatingStakeholderIdxTab') : 0;

// setup the title block
$titleBlock = new CTitleBlock('Stakeholder', 'applet3-48.png', $m, "$m.$a");
//if ($canEdit) {
	$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('new stakeholder').'">', '',
		'<form action="?m=stakeholder&a=addedit" method="post">', '</form>'
	);
//}
$titleBlock->show();

$tabBox = new CTabBox('?m=stakeholder', DP_BASE_DIR.'/modules/stakeholder/', $tab);
$tabBox->add('index_table', 'All');
$tabBox->show();