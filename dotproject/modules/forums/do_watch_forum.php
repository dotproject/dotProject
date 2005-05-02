<?php /* FORUMS $Id$ */
##
## Change forum watches
##
$watch = isset( $_POST['watch'] ) ? $_POST['watch'] : 0;

if ($watch) {
	// clear existing watches
	$q  = new DBQuery;
	$q->setDelete('forum_watch');
	$q->addWhere('watch_user = '.$AppUI->user_id);
	$q->addWhere("watch_$watch IS NOT NULL");
	if (!$q->exec()) {
		$AppUI->setMsg( db_error(), UI_MSG_ERROR );
		$q->clear();
	} else {
		$sql = '';
		$q->clear();
		foreach ($_POST as $k => $v) {
			if (strpos($k, 'forum_') !== FALSE) {
				$q->addTable('forum_watch');
				$q->addInsert('watch_user', $AppUI->user_id);
				$q->addInsert('watch_'.$watch, substr( $k, 6 ));
				if (!$q->exec()) {
					$AppUI->setMsg( db_error(), UI_MSG_ERROR );
				} else {
					$AppUI->setMsg( "Watch updated", UI_MSG_OK );
				}
				$q->clear();
			}
		}
	}
} else {
	$AppUI->setMsg( 'Incorrect watch type passed to sql handler.', UI_MSG_ERROR );
}
?>
