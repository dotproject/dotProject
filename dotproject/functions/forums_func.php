<?php /* FUNCTIONS $Id$ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

$filters = array( '- Filters -' );

if ($a == 'viewer') {
	array_push( $filters,
		'My Watched',
		'Last 30 days'
	);
} else {
	array_push( $filters,
		'My Forums',
		'My Watched',
		'My Projects',
		'My Company',
		'Inactive Projects'
	);
}
?>