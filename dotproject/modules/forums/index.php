<?php /* FORUMS $Id$ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly');
}

$AppUI->savePlace();

// retrieve any state parameters
if (isset( $_GET['orderby'] )) {
    $orderdir = $AppUI->getState( 'ForumIdxOrderDir' ) ? ($AppUI->getState( 'ForumIdxOrderDir' )== 'asc' ? 'desc' : 'asc' ) : 'desc';
	$AppUI->setState( 'ForumIdxOrderBy', $_GET['orderby'] );
    $AppUI->setState( 'ForumIdxOrderDir', $orderdir);
}
$orderby         = $AppUI->getState( 'ForumIdxOrderBy' ) ? $AppUI->getState( 'ForumIdxOrderBy' ) : 'forum_name';
$orderdir        = $AppUI->getState( 'ForumIdxOrderDir' ) ? $AppUI->getState( 'ForumIdxOrderDir' ) : 'asc';

$perms =& $AppUI->acl();

$df = $AppUI->getPref( 'SHDATEFORMAT' );
$tf = $AppUI->getPref( 'TIMEFORMAT' );

$f = dPgetParam( $_POST, 'f', 0 );


$forum =& new CForum;
require_once $AppUI->getModuleClass('projects');
$project =& new CProject;

$max_msg_length = 30;

/* Query modified by Fergus McDonald 2005/08/12 to address slow join issue */

$q  = new DBQuery;
$q->addTable('forums');
$q->addTable('projects', 'p');
$q->addTable('users', 'u');
$q->addQuery("forum_id, forum_project, forum_description, forum_owner, forum_name");
$q->addQuery("forum_moderated, forum_create_date, forum_last_date");
$q->addQuery("sum(if(c.message_parent=-1,1,0)) as forum_topics, sum(if(c.message_parent>0,1,0)) as forum_replies");
$q->addQuery("user_username, project_name, project_color_identifier");
$q->addQuery("SUBSTRING(l.message_body,1,$max_msg_length) message_body");
$q->addQuery("LENGTH(l.message_body) message_length, watch_user, l.message_parent, l.message_id");
$q->addQuery("count(distinct v.visit_message) as visit_count, count(distinct c.message_id) as message_count");
$q->addJoin('forum_messages', 'l', 'l.message_id = forum_last_id');
$q->addJoin('forum_messages', 'c', 'c.message_forum = forum_id');
$q->addJoin('forum_watch', 'w', "watch_user = $AppUI->user_id AND watch_forum = forum_id");
$q->addJoin('forum_visits', 'v', "visit_user = $AppUI->user_id AND visit_forum = forum_id and visit_message = c.message_id");

$project->setAllowedSQL($AppUI->user_id, $q);
$forum->setAllowedSQL($AppUI->user_id, $q);


$q->addWhere("user_id = forum_owner AND project_id = forum_project");

switch ($f) {
	case 1:
		$q->addWhere("project_status <> 7 AND forum_owner = $AppUI->user_id");
		break;
	case 2:
		$q->addWhere("project_status <> 7 AND watch_user IS NOT NULL");
		break;
	case 3:
		$q->addWhere("project_status <> 7 AND project_owner = $AppUI->user_id");
		break;
	case 4:
		$q->addWhere("project_status <> 7 AND project_company = $AppUI->user_company");
		break;
	case 5:
		$q->addWhere("project_status = 7");
		break;
	default:
		$q->addWhere("project_status <> 7");
		break;
}

$q->addGroup('forum_id');
$q->addOrder("$orderby $orderdir");
$forums = $q->loadList();

// setup the title block
$titleBlock = new CTitleBlock( 'Forums', 'support.png', $m, "$m.$a" );
$titleBlock->addCell(
	arraySelect( $filters, 'f', 'size="1" class="text" onChange="document.forum_filter.submit();"', $f , true ), '',
	'<form name="forum_filter" action="?m=forums" method="post">', '</form>'
);

if ($canAuthor) {
	$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('new forum').'">', '',
		'<form action="index.php" method="get"><input type="hidden" name="m" value="forums" /><input type="hidden" name="a" value="addedit" />', '</form>'
	);
}
$titleBlock->show();
?>

<table width="100%" cellspacing="1" cellpadding="2" border="0" class="tbl">
<form name="watcher" action="./index.php?m=forums&f=<?php echo $f;?>" method="post">
<tr>
	<th nowrap="nowrap">&nbsp;</th>
	<th nowrap="nowrap" width="25"><a href="?m=forums&orderby=watch_user" class="hdr"><?php echo $AppUI->_( 'Watch' );?></a></th>
	<th nowrap="nowrap"><a href="?m=forums&orderby=forum_name" class="hdr"><?php echo $AppUI->_( 'Forum Name' );?></a></th>
	<th nowrap="nowrap" width="50" align="center"><a href="?m=forums&orderby=forum_topics" class="hdr"><?php echo $AppUI->_( 'Topics' );?></a></th>
	<th nowrap="nowrap" width="50" align="center"><a href="?m=forums&orderby=forum_replies" class="hdr"><?php echo $AppUI->_( 'Replies' );?></a></th>
	<th nowrap="nowrap" width="200"><a href="?m=forums&orderby=forum_last_date" class="hdr"><?php echo $AppUI->_( 'Last Post Info' );?></a></th>
</tr>
<?php
$p ="";
$now = new CDate();
foreach ($forums as $row) {
	$message_date = intval( $row['forum_last_date'] ) ? new CDate( $row['forum_last_date'] ) : null;

	if($p != $row["forum_project"]) {
		$create_date = intval( $row['forum_create_date'] ) ? new CDate( $row['forum_create_date'] ) : null;
?>
<tr>
	<td colspan="6" style="background-color:#<?php echo $row["project_color_identifier"];?>">
		<a href="?m=projects&a=view&project_id=<?php echo $row["forum_project"];?>">
			<font color=<?php echo bestColor( $row["project_color_identifier"] );?>>
			<strong><?php echo $row["project_name"];?></strong>
			</font>
		</a>
	</td>
</tr>
	<?php
		$p = $row["forum_project"];
	}?>
<tr>
	<td nowrap="nowrap" align="center">
	<?php if ( $row["forum_owner"] == $AppUI->user_id || $perms->checkModule('forums', 'add') ) { ?>
		<a href="?m=forums&a=addedit&forum_id=<?php echo $row["forum_id"];?>" title="<?php echo $AppUI->_('edit');?>">
		<?php echo dPshowImage( './images/icons/stock_edit-16.png', 16, 16, '' );?>
		</a>
	<?php } 
		if ( $row['visit_count'] != $row['message_count'] ) {
			echo "&nbsp;" . dPshowImage('./images/icons/stock_new_small.png',false,false, "You have unread messages in this forum");
		}
	?>
	</td>

	<td nowrap="nowrap" align="center">
		<input type="checkbox" name="forum_<?php echo $row['forum_id'];?>" <?php echo $row['watch_user'] ? 'checked="checked"' : '';?> />
	</td>

	<td>
		<span style="font-size:10pt;font-weight:bold">
			<a href="?m=forums&a=viewer&forum_id=<?php echo $row["forum_id"];?>"><?php echo $row["forum_name"];?></a>
		</span>
		<br /><?php echo $row["forum_description"];?>
		<br /><font color="#777777"><?php echo $AppUI->_( 'Owner' ).' '.$row["user_username"];?>,
		<?php echo $AppUI->_( 'Started' ).' '.$create_date->format( $df );?>
		</font>
	</td>
	<td nowrap="nowrap" align="center"><?php echo $row["forum_topics"];?></td>
	<td nowrap="nowrap" align="center"><?php echo $row["forum_replies"];?></td>
	<td width="225">
<?php
	if ($message_date !== null) {
		echo $message_date->format( "$df $tf" );

		$last = new Date_Span();
		$last->setFromDateDiff( $now, $message_date );

		echo '<br /><font color=#999966>(' . $AppUI->_('Last post').' ';
		printf( "%.1f", $last->format( "%d" ) );
		echo ' '.$AppUI->_('days ago') . ') </font>';

		$id = $row['message_parent'] < 0 ? $row['message_id'] : $row['message_parent'];

		echo '<br />&gt;&nbsp;<a href="?m=forums&a=viewer&forum_id='.$row['forum_id'].'&message_id='.$id.'">';
		echo '<font color=#777777>'.$row['message_body'];
		echo $row['message_length'] > $max_msg_length ? '...' : '';
		echo '</font></a>';
	} else {
		echo $AppUI->_('No posts');
	}
?>
	</td>
</tr>

<?php } ?>
</table>

<table width="100%" cellspacing="1" cellpadding="0" border="0">
	<input type="hidden" name="dosql" value="do_watch_forum" />
	<input type="hidden" name="watch" value="forum" />
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td align="left">
		<input type="submit" class="button" value="<?php echo $AppUI->_( 'update watches' );?>" />
	</td>
</tr>
</form>
</table>
