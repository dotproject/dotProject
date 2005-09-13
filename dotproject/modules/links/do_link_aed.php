<?php /* FILES $Id$ */
//addlink sql
$link_id = intval( dPgetParam( $_POST, 'link_id', 0 ) );
$del = intval( dPgetParam( $_POST, 'del', 0 ) );

$not = dPgetParam( $_POST, 'notify', '0' );
if ($not!='0') $not='1';

$obj = new CLink();
if ($link_id) { 
	$obj->_message = 'updated';
} else {
	$obj->_message = 'added';
}
$obj->link_category = intval( dPgetParam( $_POST, 'link_category', 0 ) );

if (!$obj->bind( $_POST )) {
	$AppUI->setMsg( $obj->getError(), UI_MSG_ERROR );
	$AppUI->redirect();
}

// prepare (and translate) the module name ready for the suffix
$AppUI->setMsg( 'Link' );
// delete the link
if ($del) {
	$obj->load( $link_id );
	if (($msg = $obj->delete())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
		$AppUI->redirect();
	} else {
		if ($not=='1') $obj->notify();
		$AppUI->setMsg( "deleted", UI_MSG_ALERT, true );
		$AppUI->redirect( "m=links" );
	}
}

if (!$link_id) {
	$obj->link_owner = $AppUI->user_id;
}

if (($msg = $obj->store())) {
        $AppUI->setMsg( $msg, UI_MSG_ERROR );
} else {
        $obj->load($obj->file_id);
        if ($not=='1') $obj->notify();
        $AppUI->setMsg( $file_id ? 'updated' : 'added', UI_MSG_OK, true );
}

$AppUI->redirect();
?>
