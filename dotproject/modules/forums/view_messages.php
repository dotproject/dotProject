<?php  /* FORUMS $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

$AppUI->savePlace();
$sort = dPgetParam($_REQUEST, 'sort', 'asc');
$viewtype = dPgetParam($_REQUEST, 'viewtype', 'normal');
$hideEmail = dPgetConfig('hide_email_addresses', false);

$q = new DBQuery;
$q->addTable('forums', 'f');
$q->addTable('forum_messages', 'fm');
$q->addQuery('fm.*,	contact_first_name, contact_last_name, contact_email, user_username, forum_moderated, visit_user');
$q->addJoin('forum_visits', 'v', "visit_user = {$AppUI->user_id} AND visit_forum = $forum_id AND visit_message = fm.message_id");
$q->addJoin('users', 'u', 'message_author = u.user_id');
$q->addJoin('contacts', 'con', 'contact_id = user_contact');
$q->addWhere("forum_id = message_forum AND (message_id = $message_id OR message_parent = $message_id)");
$q->addOrder("message_date $sort"); 

$messages = $q->loadList();

$crumbs = array();
$crumbs['?m=forums'] = "forums list";
$crumbs["?m=forums&amp;a=viewer&amp;forum_id=$forum_id"] = "topics for this forum";
$crumbs["?m=forums&amp;a=view_pdf&amp;forum_id=$forum_id&amp;message_id=$message_id&amp;sort=$sort&amp;suppressHeaders=1"] = "view PDF file";
?>
<script type="text/javascript" language="javascript">
<?php
if ($viewtype != 'normal') {
?>
function toggle(id) {
<?php
	if ($viewtype == 'single') {
?>
		var elems = document.getElementsByTagName("div");
		for (var i=0; i<elems.length; i++) {
			if (elems[i].className == 'message') {
				elems[i].style.display = 'none';
			}
		}
		document.getElementById(id).style.display = 'block';

<?php 
	} else if ($viewtype=='short') {
?>
	vista = (document.getElementById(id).style.display == 'none') ? 'block' : 'none';
	document.getElementById(id).style.display = vista;
<?php
	}
?>
}
<?php 
}
// security improvement:
// some javascript functions may not appear on client side in case of user not having write permissions
// else users would be able to arbitrarily run 'bad' functions
if ($canEdit) {
?>
function delIt(id) {
	var form = document.messageForm;
	if (confirm("<?php echo $AppUI->_('forumsDelete');?>")) {
		form.del.value = 1;
		form.message_id.value = id;
		form.submit();
	}
}
<?php } ?>
</script>
<?php
$thispage = "?m=$m&amp;a=viewer&amp;forum_id=$forum_id&amp;message_id=$message_id&amp;sort=$sort";
// $thispage = $_PHP['self'];
?>

<table width="98%" cellspacing="1" cellpadding="2" border="0" align="center">
<tr>
	<td><?php echo breadCrumbs($crumbs);?></td>
	<td>
		<form action="<?php echo $thispage; ?>" method="post">
		<?php echo $AppUI->_('View') ?>: 
		<input type="radio" name="viewtype" value="normal" <?php echo ($viewtype == 'normal')?'checked="checked"':'';?> onclick="javascript:this.form.submit();" /><?php echo $AppUI->_('Normal') ?>
		<input type="radio" name="viewtype" value="short" <?php echo ($viewtype == 'short')?'checked="checked"':'';?> onclick="javascript:this.form.submit();" /><?php echo $AppUI->_('Collapsed') ?>
		<input type="radio" name="viewtype" value="single" <?php echo ($viewtype == 'single')?'checked="checked"':'';?> onclick="javascript:this.form.submit();" /><?php echo $AppUI->_('Single Message at a time') ?>
		</form>
	</td>
	<td align="right">
		<?php $sort = ($sort == 'asc')?'desc':'asc'; ?>
		<input type="button" class="button" value="<?php echo $AppUI->_('Sort By Date') . ' (' . $AppUI->_($sort) . ')'; ?>" onclick="javascript:window.location='./index.php?m=forums&amp;a=viewer&amp;forum_id=<?php echo $forum_id;?>&amp;message_id=<?php echo $message_id;?>&amp;sort=<?php echo $sort; ?>'" />
	<?php 
if ($canEdit && ($AppUI->user_id == $row['forum_moderated'] 
                 || $AppUI->user_id == $row['message_author'] 
                 || getPermission('project', 'edit', $forum_info['project_id']) 
                 || !($forum_info['project_id']))) { 
?>
		<input type="button" class="button" value="<?php echo $AppUI->_('Post Reply');?>" onclick="javascript:window.location='./index.php?m=forums&amp;a=viewer&amp;forum_id=<?php echo $forum_id;?>&amp;message_parent=<?php echo $message_id;?>&amp;post_message=1';" />
		<input type="button" class="button" value="<?php echo $AppUI->_('New Topic');?>" onclick="javascript:window.location='./index.php?m=forums&amp;a=viewer&amp;forum_id=<?php echo $forum_id;?>&amp;message_id=0&amp;post_message=1';" />
	<?php 
} 
?>
	</td>
</tr>
</table>

<form name="messageForm" method="POST" action="?m=forums&amp;forum_id=<?php echo $forum_id;?>">
	<input type="hidden" name="dosql" value="do_post_aed" />
	<input type="hidden" name="del" value="0" />
	<input type="hidden" name="message_id" value="0" />
</form>
<table border="0" cellpadding="4" cellspacing="1" width="98%" class="tbl" align="center">
<tr>
<?php 
if ($viewtype != 'short') {
	echo '<th nowrap>' .$AppUI->_('Author') . ':</th>';
}
echo '<th width="' . (($viewtype=='single')?'60':'100') . '%">' .  $AppUI->_('Message') . ':</th>';
?>
</tr>

<?php 
$x = false;

$date = new CDate();

if ($viewtype == 'single') {
	$s = '';
	$first = true;
}

$new_messages = array();

foreach ($messages as $row) {
	// Find the parent message - the topic.
	if ($row['message_id'] == $message_id) {
		$topic = $row['message_title'];
	}
	
	$q = new DBQuery;
	$q->addTable('forum_messages', 'fm');
	$q->addTable('users', 'u');
	$q->addQuery('DISTINCT contact_email, contact_first_name, contact_last_name, user_username');
	$q->addJoin('contacts', 'con', 'contact_id = user_contact');
	$q->addWhere('u.user_id = ' . $row['message_editor']);
	$editor = $q->loadList();
	
	$date = intval($row['message_date']) ? new CDate($row['message_date']) : null;
	if ($viewtype != 'single') {
		$s = '';
	}
	$style = $x ? 'background-color:#eeeeee' : '';
	
	//!!! Different table building for the three different views
	// To be cleaned up, and reuse common code at later stage.
	if ($viewtype =='normal') {
		$s .= "<tr>";
		
		$s .= '<td valign="top" style="' . $style . '" nowrap="nowrap">';
		if (!($hideEmail)) {
			$s .= '<a href="mailto:' . $row['contact_email'] . '">';
		}
		$s .= '<font size="2">' . $row['contact_first_name'] . ' ' . $row['contact_last_name'] . '</font>';
		if (! $hideEmail) {
			$s .= '</a>';
		}
		if (sizeof($editor)>0) {
			$s .= '<br/>&nbsp;<br/>' . $AppUI->_('last edited by');
			$s .= ':<br/>';
			if (!$hideEmail) {
				$s .= '<a href="mailto:' . $editor[0]['contact_email'] . '">';
			}
			$s .= ('<font size="1">' . $editor[0]['contact_first_name'] . ' ' . $editor[0]['contact_last_name'] 
			       . '</font>');
			if (! $hideEmail) {
				$s .= '</a>';
			}
		}
		if ($row['visit_user'] != $AppUI->user_id) {
			$s .= "<br/>&nbsp;".dPshowImage('images/icons/stock_new_small.png');
			$new_messages[] = $row['message_id'];
		}
		$s .= '</td>';
		$s .= '<td valign="top" style="' . $style . '">';
		$s .= '<font size="2"><strong>' . $row['message_title'] . '</strong><hr size=1>';
		$s .= str_replace(chr(13), "&nbsp;<br />", $row['message_body']);
		$s .= '</font></td>';
		
		$s .= '</tr><tr>';
		
		$s .= '<td valign="top" style="' . $style . '" nowrap="nowrap">';
		$s .= ('<img src="./images/icons/posticon.gif" alt="date posted" border="0" width="14" height="11" />'
		       .$date->format("$df $tf") . '</td>');
		$s .= '<td valign="top" align="right" style="' . $style . '">';
		
		//the following users are allowed to edit/delete a forum message: 
		//1. the forum creator  2. a superuser with read-write access to 'all' 3. the message author
		$canEdit = getPermission('forums', 'edit', $row['message_id']);
		if ($canEdit && ($AppUI->user_id == $row['forum_moderated'] 
		                 || $AppUI->user_id == $row['message_author'] 
		                 || getPermission('admin', 'edit'))) {
			$s .= '<table cellspacing="0" cellpadding="0" border="0"><tr>';
			// edit message
			$s .= ('<td><a href="?m=forums&amp;a=viewer&amp;post_message=1&amp;forum_id=' . $row['message_forum'] 
			       . '&amp;message_parent=' . $row['message_parent'] . '&amp;message_id=' . $row['message_id'] . '" title="' 
			       . $AppUI->_('Edit') . ' ' . $AppUI->_('Message') . '">');
			$s .= dPshowImage('./images/icons/stock_edit-16.png', '16', '16');
			$s .= '</td><td>';
			// delete message
			$s .= '<a href="javascript:delIt(' . $row['message_id'] . ')" title="' . $AppUI->_('delete') . '">';
			$s .= dPshowImage('./images/icons/stock_delete-16.png', '16', '16');
			$s .= '</a>';
			$s .= '</td></tr></table>';
		}
		$s .= '</td>';
		$s .= '</tr>';
	} else if ($viewtype == 'short') {
		$s .= "<tr>";
		
        $s .= '<td valign="top" style="' . $style . '" >';
        $s .= '<a href="mailto:' . $row['contact_email'] . '">';
        $s .= '<font size="2">' . $row['contact_first_name'] . ' ' . $row['contact_last_name'] . '</font></a>';
        $s .= ' (' . $date->format("$df $tf") . ') ';
        if (sizeof($editor)>0) {
			$s .= '<br/>&nbsp;<br/>' . $AppUI->_('last edited by');
			$s .= ':<br/><a href="mailto:' . $editor[0]['contact_email'] . '">';
			$s .= ('<font size="1">' . $editor[0]['contact_first_name'] . ' ' . $editor[0]['contact_last_name'] 
			       . '</font></a>');
        }
		$s .= ('<a name="' . $row['message_id'] . '" href="#' . $row['message_id'] . '" onclick="javascript:toggle(' 
		       . $row['message_id'] . ')">');
        $s .= '<span size="2"><strong>' . $row['message_title'] . '</strong></span></a>';
        $s .= '<div class="message" id="' . $row['message_id'] . '" style="display: none">';
        $s .= str_replace(chr(13), "&nbsp;<br />", $row['message_body']);
        $s .= '</div></td>';
		
        $s .= '</tr>';
	} else if ($viewtype == 'single') {
		$s .= "<tr>";
		
        $s .= '<td valign="top" style="' . $style . '">';
        $s .= $date->format("$df $tf") . ' - ';
        $s .= '<a href="mailto:' . $row['contact_email'] . '">';
        $s .= '<font size="2">' . $row['contact_first_name'] . ' ' . $row['contact_last_name'] . '</font></a>';
        $s .= '<br />';
        if (sizeof($editor)>0) {
			$s .= '<br/>&nbsp;<br/>' . $AppUI->_('last edited by');
			$s .= ':<br/><a href="mailto:' . $editor[0]['contact_email'] . '">';
			$s .= ('<font size="1">' . $editor[0]['contact_first_name'] . ' ' . $editor[0]['contact_last_name'] 
			       . '</font></a>');
        }
		$s .= '<a href="#" onclick="javascript:toggle(' . $row['message_id'] . ')">';
        $s .= '<span size="2"><strong>' . $row['message_title'] . '</strong></span></a>';
        $side .= '<div class="message" id="' . $row['message_id'] . '" style="display: none">';
        $side .= str_replace(chr(13), "&nbsp;<br />", $row['message_body']);
        $side .= '</div>';
        $s .= '</td>';
        if ($first) {
			$s .= '<td rowspan="' . count($messages) . '" valign="top">';
			echo $s;
			$s = '';
			$first = false;
        }
		
        $s .= '</tr>';
	}

	if ($viewtype != 'single') {
		echo $s;
	}
	$x = !$x;
}
if ($viewtype == 'single') {
	echo $side . '</td>' . $s;
}
?>
</table>
<table border=0 cellpadding=2 cellspacing=1 width="98%" summary="">
<tr>
	<td><?php echo breadCrumbs($crumbs);?></td>
	<td align="right">
		<input type="button" class="button" value="<?php echo $AppUI->_('Sort By Date') . ' (' . $AppUI->_($sort) . ')'; ?>" onclick="javascript:window.location='./index.php?m=forums&amp;a=viewer&amp;forum_id=<?php echo $forum_id;?>&amp;message_id=<?php echo $message_id;?>&amp;sort=<?php echo $sort; ?>'" />
		<?php 
if ($canEdit && ($AppUI->user_id == $row['forum_moderated'] 
                 || $AppUI->user_id == $row['message_author'] 
                 || getPermission('project', 'edit', $forum_info['project_id']) 
                 || !($forum_info['project_id']))) { 
?>
		<input type="button" class="button" value="<?php echo $AppUI->_('Post Reply');?>" onclick="javascript:window.location='./index.php?m=forums&amp;a=viewer&amp;forum_id=<?php echo $forum_id;?>&amp;message_parent=<?php echo $message_id;?>&amp;post_message=1';" />
		<input type="button" class="button" value="<?php echo $AppUI->_('New Topic');?>" onclick="javascript:window.location='./index.php?m=forums&amp;a=viewer&amp;forum_id=<?php echo $forum_id;?>&amp;message_id=0&amp;post_message=1';" />
	<?php } ?>
	</td>
</tr>
</table>
<?php
// Now we need to update the forum visits with the new messages so they don't show again.
foreach ($new_messages as $msg_id) {
	$q = new DBQuery;
	$q->addTable('forum_visits');
	$q->addInsert('visit_user', $AppUI->user_id);
	$q->addInsert('visit_forum', $forum_id);
	$q->addInsert('visit_message', $msg_id);
	$q->addInsert('visit_date', $date->getDate());
	$q->exec();
	$q->clear();
}
?>
