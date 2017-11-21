<?php
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}
$AppUI->savePlace();
// setup the title block
$titleBlock = new CTitleBlock('Monitoring and Control', 'graph-up.png', $m, $m . '.' . $a);
$titleBlock->show();
?>