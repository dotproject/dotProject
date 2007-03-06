<?php
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}


require_once DP_BASE_DIR . '/modules/ticketsmith/config.inc.php';
require_once $AppUI->getSystemClass( 'query');
$font_dir = DP_BASE_DIR.'/lib/ezpdf/fonts';
$temp_dir = DP_BASE_DIR.'/files/temp';
require( $AppUI->getLibraryClass( 'ezpdf/class.ezpdf' ) );

$type = dPgetParam($_GET, 'type', '');
$column = dPgetParam($_GET, 'column', 'timestamp');
$direction = dPgetParam($_GET, 'direction', 'DESC');
$q =& new DBQuery;
$q->addQuery(array(
	'ticket',
	'author',
	'subject',
	'timestamp',
	'type',
	'assignment',
	'contact_first_name',
	'contact_last_name',
	'activity',
	'priority'
));
$q->addTable('tickets', 'a');
$q->leftJoin('users', 'b', 'a.assignment = b.user_id');
$q->leftJoin('contacts', 'c', 'b.user_id = c.contact_id');
if ($type == 'My') {
	$q->addWhere("type = 'Open'");
	$q->addWhere("(assignment = '$AppUI->user_id' OR assignment = '0')");
} else if ($type != 'All') {
	$q->addWhere("type = '$type'");
}
$q->addWhere("parent = '0'");
$q->addOrder(urlencode($column) . " " . $direction);

$ticketlist = $q->loadHashList('ticket');
if ($err = db_error()) {
	$AppUI->setMsg($err, UI_MSG_ERR);
	$AppUI->redirect();
}

$df = $AppUI->getPref('SHDATEFORMAT');

$pdf =& new Cezpdf($paper='A4',$orientation='landscape');
$pdf->ezSetCmMargins( 1, 2, 1.5, 1.5 );
$pdf->selectFont( "$font_dir/Helvetica.afm" );

$pdf->ezText( dPgetConfig( 'company_name' ), 12 );

$date = new CDate();
$pdf->ezText( "\n" . $date->format( $df) , 8 );
$next_week = new CDate($date);
$next_week->addSpan(new Date_Span(array(7,0,0,0)));

$pdf->selectFont( "$font_dir/Helvetica-Bold.afm" );
$pdf->ezText( "\n" . $AppUI->_("$type Ticket Report"), 12 );
$pdf->ezText( "$pname", 15 );
$pdf->ezText( "\n" );
$pdf->selectFont( "$font_dir/Helvetica.afm" );
$title = "$type Tickets";
$options = array(
	'showLines' => 2,
	'showHeadings' => 1,
	'fontSize' => 9,
	'rowGap' => 4,
	'colGap' => 5,
	'xPos' => 50,
	'xOrientation' => 'right',
	'width'=>'750',
	'shaded'=> 0,
	'cols'=>array(
	 	0=>array('justification'=>'center','width'=>250),
		1=>array('justification'=>'center','width'=>250),
		2=>array('justification'=>'center','width'=>45),
		3=>array('justification'=>'center','width'=>45),
		4=>array('justification'=>'center','width'=>45),
		5=>array('justification'=>'center','width'=>45),
		6=>array('justification'=>'center','width'=>45),
		)
);

$pdfdata = array();
$columns = array(
	'<b>' . $AppUI->_('Author') . '</b>',
	'<b>' . $AppUI->_('Subject') . '</b>',
	'<b>' . $AppUI->_('Date') . '</b>',
	'<b>' . $AppUI->_('Followup') . '</b>',
	'<b>' . $AppUI->_('Status') . '</b>',
	'<b>' . $AppUI->_('Priority') . '</b>',
	'<b>' . $AppUI->_('Owner') . '</b>',
);

foreach ($ticketlist as $ticket) {
	$row =& $pdfdata[];
	$row[] = $ticket['author'];
	$row[] = $ticket['subject'];
	$row[] = date($CONFIG['date_format'], $ticket['timestamp']);
	if ($ticket['activity'])
		$row[] = date($CONFIG['date_format'], $ticket['activity']);
	else
		$row[] = '-';
	$row[] = $ticket['type'];
	$row[] = $CONFIG['priority_names'][$ticket['priority']];
	$row[] = $ticket['contact_firt_name'] . ' ' . $ticket['contact_last_name'];
}

$pdf->ezTable( $pdfdata, $columns, $title, $options );
$pdf->ezStream();
?>
