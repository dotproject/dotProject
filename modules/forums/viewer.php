<?php /* $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

//view posts
$forum_id = isset($_GET["forum_id"]) ? (int)$_GET["forum_id"] : 0;

$message_id = isset($_GET["message_id"]) ? (int)$_GET["message_id"] : 0;
$post_message = isset($_GET["post_message"]) ? $_GET["post_message"] : 0;
$f = dpGetParam($_POST, 'f', 0);

// check permissions
$canRead = getPermission($m, 'view', $forum_id);
$canEdit = getPermission($m, 'edit', $forum_id);

if (!$canRead || ($post_message & !$canEdit)) {
	$AppUI->redirect("m=public&a=access_denied");
}

$df = $AppUI->getPref('SHDATEFORMAT');
$tf = $AppUI->getPref('TIMEFORMAT');

$q  = new DBQuery;
$q->addTable('forums', 'f');
$q->leftJoin('projects', 'p', 'f.forum_project = p.project_id');
$q->leftJoin('users', 'u', 'u.user_id = f.forum_owner');
$q->leftJoin('contacts', 'con', 'con.contact_id = u.user_contact');
$q->addQuery('f.forum_id, f.forum_project, f.forum_description, f.forum_owner, f.forum_name,' 
			 . ' f.forum_create_date, f.forum_last_date, f.forum_message_count, f.forum_moderated,' 
			 . ' u.user_username, con.contact_first_name, con.contact_last_name, p.project_name,' 
			 . ' p.project_color_identifier');
$q->addWhere("forum_id = $forum_id");
$q->exec(ADODB_FETCH_ASSOC);
$forum = $q->fetchRow();
$forum_name = $forum["forum_name"];
echo db_error();
$q->clear();

$start_date = intval($forum["forum_create_date"]) ? new CDate($forum["forum_create_date"]) : null;

// setup the title block
$titleBlock = new CTitleBlock('Forum', 'support.png', $m, "$m.$a");
$titleBlock->addCell(
	arraySelect($filters, 'f', 'size="1" class="text" onchange="javascript:document.filterFrm.submit();"', $f , true), '',
	'<form action="?m=forums&amp;a=viewer&amp;forum_id='.$forum_id.'" method="post" name="filterFrm">', '</form>'
);
$titleBlock->show();
?>
<table width="100%" cellspacing="0" cellpadding="2" border="0" class="std">
<tr>
	<td height="20" colspan="3" style="border: outset #D1D1CD 1px;background-color:<?php echo $forum["project_color_identifier"];?>">
		<font size="2" color=<?php echo bestColor($forum["project_color_identifier"]);?>><strong><?php echo $AppUI->___(@$forum["forum_name"]);?></strong></font>
	</td>
</tr>
<tr>
	<td align="left" nowrap><?php echo $AppUI->_('Related Project');?>:</td>
	<td nowrap="nowrap"><strong><a href="?m=projects&amp;a=view&amp;project_id=<?php echo $forum['forum_project']; ?>"><strong><?php echo $AppUI->___($forum["project_name"]);?></a></strong></td>
	<td valign="top" width="50%" rowspan="99">
		<strong><?php echo $AppUI->_('Description');?>:</strong>
		<br /><?php echo $AppUI->___(@str_replace(chr(13), "&nbsp;<br />",$forum["forum_description"]));?>
	</td>
</tr>
<tr>
	<td align="left"><?php echo $AppUI->_('Owner');?>:</td>
	<td nowrap><?php
		echo $AppUI->___($forum['contact_first_name'] . ' ' . $forum['contact_last_name']);
		if (intval($forum["forum_id"]) <> 0) {
			echo " (".$AppUI->_('moderated').") ";
		}?>
	</td>
</tr>
<tr>
	<td align="left"><?php echo $AppUI->_('Created On');?>:</td>
	<td nowrap><?php echo $start_date ? $start_date->format($df) : '-';?></td>
</tr>
</table>

<?php
if ($post_message) {
	include(DP_BASE_DIR . '/modules/forums/post_message.php');
} else if ($message_id == 0) {
	include(DP_BASE_DIR . '/modules/forums/view_topics.php');
} else {
	include(DP_BASE_DIR . '/modules/forums/view_messages.php');
}
?>
