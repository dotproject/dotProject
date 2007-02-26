<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

$percent = array(0=>'0',5=>'5',10=>'10',15=>'15',20=>'20',25=>'25',30=>'30',35=>'35',40=>'40',45=>'45',50=>'50',55=>'55',60=>'60',65=>'65',70=>'70',75=>'75',80=>'80',85=>'85',90=>'90',95=>'95',100=>'100');

// patch 2.12.04 add all finished last 7 days, my finished last 7 days
$filters = array(
	'my'           => 'My Tasks',
	'myunfinished' => 'My Unfinished Tasks',
	'allunfinished' => 'All Unfinished Tasks',
	'myproj'       => 'My Projects',
	'mycomp'       => 'All Tasks for my Company',
	'unassigned'   => 'All Tasks (unassigned)',
	'taskcreated'  => 'All Tasks I Have Created',
	'all'          => 'All Tasks',
	'allfinished7days' => 'All Tasks Finished Last 7 Days',
	'myfinished7days'  => 'My Tasks Finished Last 7 Days'
);

$status = dPgetSysVal( 'TaskStatus' );

$priority = dPgetSysVal( 'TaskPriority' );

?>
