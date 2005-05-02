<?php /* DEPARTMENTS $Id$ */
$titleBlock = new CTitleBlock( 'Departments', 'users.gif', $m, '' );
$titleBlock->addCrumb( "?m=companies", "companies list" );
$titleBlock->show();

echo $AppUI->_( 'deptIndexPage' );
?>