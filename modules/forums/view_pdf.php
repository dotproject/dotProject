<?php  /* FORUMS $Id$ */
if (! defined('DP_BASE_DIR')) {
	die('You should not call this file directly.');
}
$AppUI->savePlace();
$sort = dPgetCleanParam($_REQUEST, 'sort', 'asc');
$forum_id = (int)dPgetParam($_REQUEST, 'forum_id', 0);
$message_id = (int)dPgetParam($_REQUEST, 'message_id', 0);

if (! getPermission('forums', 'view', $message_id))
	$AppUI->redirect("m=public&a=access_denied");

$q  = new DBQuery;
$q->addTable('forums');
$q->addTable('forum_messages', 'msg');
$q->addQuery('msg.*, contact_first_name, contact_last_name, contact_email, user_username,
			forum_moderated, visit_user');
$q->addJoin('forum_visits', 'v', "visit_user = " . $AppUI->user_id . " AND visit_forum = " . $forum_id . " AND visit_message = msg.message_id");
$q->addJoin('users', 'u', 'message_author = u.user_id');
$q->addJoin('contacts', 'con', 'contact_id = user_contact');
$q->addWhere("forum_id = message_forum AND (message_id = " . $message_id . " OR message_parent = " . $message_id . ")");
if (dPgetConfig('forum_descendent_order') || dPgetCleanParam($_REQUEST,'sort',0)) { $q->addOrder("message_date " . $sort); }

$messages = $q->loadList();

$x = false;

$date = new CDate();
$pdfdata = array();
$pdfhead = array('Date', 'User', 'Message');

$new_messages = array();

foreach ($messages as $row) {
        // Find the parent message - the topic.
        if ($row['message_id'] == $message_id)
                $topic = $row['message_title'];

	$q  = new DBQuery;
	$q->addTable('forum_messages');
	$q->addTable('users', 'u');
	$q->addQuery('DISTINCT contact_email, contact_first_name, contact_last_name, user_username');
	$q->addJoin('contacts', 'con', 'contact_id = user_contact');
	$q->addWhere('u.user_id = '.$row["message_editor"]);
	$editor = $q->loadList();

	$date = intval($row["message_date"]) ? new CDate($row["message_date"]) : null;

	$pdfdata[] = array($row['message_date'],
		$row['contact_first_name'] . ' ' . $row['contact_last_name'],
		'<b>' . $row['message_title'] . '</b>
		' . $row['message_body']);
}

$font_dir = DP_BASE_DIR.'/lib/ezpdf/fonts';
$temp_dir = DP_BASE_DIR.'/files/temp';
require($AppUI->getLibraryClass('ezpdf/class.ezpdf'));

$pdf = new Cezpdf($paper='A4',$orientation='portrait');  // PHP 8 dislikes &new (gwyneth 20210430)
$pdf->ezSetCmMargins(1, 2, 1.5, 1.5);
$pdf->selectFont("$font_dir/Helvetica.afm");
$pdf->ezText('Project: ' . $forum['project_name']. '   Forum: '.$forum['forum_name']);
$pdf->ezText('Topic: ' . $topic);
$pdf->ezText('');
                $options = array(
                        'showLines' => 1,
                        'showHeadings' => 1,
                        'fontSize' => 8,
                        'rowGap' => 2,
                        'colGap' => 5,
                        'xPos' => 50,
                        'xOrientation' => 'right',
                        'width'=>'500'
               );

$pdf->ezTable($pdfdata, $pdfhead, NULL, $options);

$pdf->ezStream();
?>
