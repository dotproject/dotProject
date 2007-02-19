<?php /* FILES $Id$ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly');
}

//addfile sql
$file_id = intval( dPgetParam( $_POST, 'file_id', 0 ) );

$obj = new CFile();
if ($file_id) { 
	$obj->_message = 'updated';
	$oldObj = new CFile();
	$oldObj->load( $file_id );

} else {
	$obj->_message = 'added';
}
$obj->file_category = intval( dPgetParam( $_POST, 'file_category', 0 ) );

if (!$obj->bind( $_POST )) {
	$AppUI->setMsg( $obj->getError(), UI_MSG_ERROR );
	$AppUI->redirect();
}

// prepare (and translate) the module name ready for the suffix
//$AppUI->setMsg( 'File' );

set_time_limit( 600 );
ignore_user_abort( 1 );

$q  = new DBQuery;
$q->addTable('files');
$q->addUpdate('file_checkout', "{$AppUI->user_id}");
$q->addUpdate('file_co_reason', "{$_POST['file_co_reason']}" );
$q->addWhere("file_id = $file_id");
$q->exec();
$q->clear();

// We now have to display the required page
// Destroy the post stuff, and allow the page to display index.php again.
$a = 'index';
unset($_GET['a']);

$params = 'file_id=' . $file_id;
$session_id = SID;
                                                                      
session_write_close();
// are the params empty
// Fix to handle cookieless sessions
if ($session_id != "") {
    $params .= "&" . $session_id;
}
//        header( "Refresh: 0; URL=fileviewer.php?$params" );
echo '<script type="text/javascript">
fileloader = window.open("fileviewer.php?'.$params.'", "mywindow",
"location=1,status=1,scrollbars=0,width=20,height=20");
fileloader.moveTo(0,0);
</script>';

?>
