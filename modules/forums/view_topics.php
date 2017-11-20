<?php /* FORUMS $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

$AppUI->savePlace();
$valid_ordering = array('watch_user', 'message_title', 'user_username', 'replies', 'latest_reply');

// retrieve any state parameters
if (isset($_GET['orderby']) && in_array($_GET['orderby'], $valid_ordering)) {
	$orderdir = ($AppUI->getState('ForumVwOrderDir') 
	             ? (($AppUI->getState('ForumVwOrderDir') == 'asc') ? 'desc' : 'asc') : 'desc');
	$AppUI->setState('ForumVwOrderBy', $_GET['orderby']);
    $AppUI->setState('ForumVwOrderDir', $orderdir);
}
$orderby = $AppUI->getState('ForumVwOrderBy') ? $AppUI->getState('ForumVwOrderBy') : 'latest_reply';
$orderdir = $AppUI->getState('ForumVwOrderDir') ? $AppUI->getState('ForumVwOrderDir') : 'desc';

//Pull All Messages
$q = new DBQuery;
$q->addTable('forum_messages', 'fm1');
$q->addJoin('forum_watch', 'fw', 'fw.watch_topic = fm1.message_id AND fw.watch_user = ' 
            . $AppUI->user_id);
$q->addJoin('users', 'u', 'u.user_id = fm1.message_author');
$q->addJoin('contacts', 'con', 'con.contact_id = u.user_contact');
$q->addJoin('forum_messages', 'fm2', 'fm2.message_parent = fm1.message_id');
$q->addJoin('forum_visits', 'v1', 'v1.visit_message = fm1.message_id AND v1.visit_user = ' 
            . $AppUI->user_id);
$q->addJoin('forum_visits', 'v2', 'v2.visit_message = fm2.message_id AND v2.visit_user = ' 
            . $AppUI->user_id);
$q->addQuery('fm1.*');
$q->addQuery('COUNT(DISTINCT fm2.message_id) AS replies');
$q->addQuery('MAX(fm2.message_date) AS latest_reply');
$q->addQuery('user_username, contact_first_name, watch_user');
$q->addQuery('COUNT(DISTINCT v1.visit_message) as topic_visits');
$q->addQuery('COUNT(DISTINCT v2.visit_message) as reply_visits');
$q->addWhere('fm1.message_forum = ' . $forum_id);

switch ($f) {
	case 1:
		$q->addWhere('watch_user IS NOT NULL');
		break;
	case 2:
		$q->addWhere('(NOW() < DATE_ADD(fm2.message_date, INTERVAL 30 DAY)' 
		             . ' OR NOW() < DATE_ADD(fm1.message_date, INTERVAL 30 DAY))');
		break;
}
$q->addGroup('fm1.message_id, fm1.message_parent');
$q->addOrder($orderby . ' ' . $orderdir);
$topics = $q->loadList();



$crumbs = array();
$crumbs['?m=forums'] = 'forums list';
?>
<table width="100%" cellspacing="1" cellpadding="2" border="0" summary="breadcrumbs">
<tr>
	<td><?php echo breadCrumbs($crumbs);?></td>
	<td align="right">
	<?php 
if ($canEdit) { 
?>
		<input type="button" class=button style="width:120;" value="<?php 
echo $AppUI->_('start a new topic'); 
?>" onclick="javascript:window.location='?m=forums&amp;a=viewer&amp;forum_id=<?php 
echo $forum_id; ?>&amp;post_message=1';" />
	<?php 
}
?>
	</td>
</tr>
</table>

<form name="watcher" action="?m=forums&a=viewer&forum_id=<?php echo $forum_id; ?>&f=<?php 
echo $f; ?>" method="post">
<table width="100%" cellspacing="1" cellpadding="2" border="0" class="tbl" summary="forum topics">
<tr>
	<th><a href="?m=forums&amp;a=viewer&amp;forum_id=<?php 
echo $forum_id; ?>&amp;orderby=watch_user" class="hdr"><?php echo $AppUI->_('Watch'); ?></a></th>
	<th><a href="?m=forums&amp;a=viewer&amp;forum_id=<?php 
echo $forum_id; ?>&amp;orderby=message_title" class="hdr"><?php echo $AppUI->_('Topics'); ?></a></th>
	<th><a href="?m=forums&amp;a=viewer&amp;forum_id=<?php 
echo $forum_id; ?>&amp;orderby=user_username" class="hdr"><?php echo $AppUI->_('Author'); ?></a></th>
	<th><a href="?m=forums&amp;a=viewer&amp;forum_id=<?php 
echo $forum_id; ?>&amp;orderby=replies" class="hdr"><?php echo $AppUI->_('Replies'); ?></a></th>
	<th><a href="?m=forums&amp;a=viewer&amp;forum_id=<?php 
echo $forum_id; ?>&amp;orderby=latest_reply" class="hdr"><?php echo $AppUI->_('Last Post'); ?></a></th>

</tr>
<?php
$now = new CDate();

foreach ($topics as $row) {
	$last = (intval($row['latest_reply']) ? new CDate($row['latest_reply']) : null);
	
	//JBF limit displayed messages to first-in-thread
	if ($row['message_parent'] < 0) { 
?>
<tr>
	<td nowrap="nowrap" align="center" width="1%">
		<input type="checkbox" name="forum_<?php echo $row['message_id']; ?>" <?php 
		echo ($row['watch_user'] ? 'checked="checked"' : ''); ?> />
	</td>
	<td>
		<?php
		if (!($row['topic_visits']) || $row['reply_visits'] != $row['replies']) {
			echo dPshowImage('images/icons/stock_new_small.png', false, false, 
			                 $AppUI->_('You have unread posts in this topic'));
		}
		?>
		<span style="font-size:10pt;">
		<a href="?m=forums&amp;a=viewer&amp;forum_id=<?php 
		echo $forum_id . '&amp;message_id=' . $row['message_id']; ?>"><?php 
		echo $AppUI->___($row['message_title']); ?></a>
		</span>
	</td>
	<td bgcolor="#dddddd" width="10%"><?php echo $AppUI->___($row['user_username']); ?></td>
	<td align="center" width="10%"><?php echo  $row['replies']; ?></td>
	<td bgcolor="#dddddd" width="150" nowrap="nowrap">
		<?php 
		if ($row['latest_reply']) {
			echo $last->format($df . ' ' . $tf).'<br /><font color=#999966>(';
			
			$span = new Date_Span();
			$span->setFromDateDiff($now, $last);
			
			printf('%.1f', $span->format('%d'));
			echo (' ' .$AppUI->_('days ago') . ')</font>');
		} else {
			echo $AppUI->_('No replies');
		}
?>
	</td>
</tr>
<?php
	}
}
?>
</table>

<input type="hidden" name="dosql" value="do_watch_forum" />
<input type="hidden" name="watch" value="topic" />
<table width="100%" border="0" cellpadding="0" cellspacing="1">
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td align="left">
		<input type="submit" class="button" value="<?php echo $AppUI->_('update watches'); ?>" />
	</td>
</tr>
</table>
</form>
