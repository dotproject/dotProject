<?php /* FUNCTIONS $Id$ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly');
}

// project statii
$pstatus = dPgetSysVal( 'ProjectStatus' );
$ptype = dPgetSysVal( 'ProjectType' );

$ppriority_name = dPgetSysVal( 'ProjectPriority' );
$ppriority_color = dPgetSysVal( 'ProjectPriorityColor' );

$priority = array();
foreach ($ppriority_name as $key => $val) {
    $priority[$key]['name'] = $val;
}
foreach ($ppriority_color as $key => $val) {
    $priority[$key]['color'] = $val;
}

/*
// kept for reference
$priority = array(
 -1 => array(
 	'name' => 'low',
 	'color' => '#E5F7FF'
 	),
 0 => array(
 	'name' => 'normal',
 	'color' => ''//#CCFFCA
 	),
 1 => array(
 	'name' => 'high',
 	'color' => '#FFDCB3'
 	),
 2 => array(
 	'name' => 'immediate',
 	'color' => '#FF887C'
 	)
);
*/

?>
