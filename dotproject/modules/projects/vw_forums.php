<?php /* PROJECTS $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

GLOBAL $AppUI, $project_id;
// Forums mini-table in project view action
$q  = new DBQuery;
$q->addTable('forums');
$q->addQuery("forum_id, forum_project, forum_description, forum_owner, forum_name, forum_message_count,
	DATE_FORMAT(forum_last_date, '%d-%b-%Y %H:%i') forum_last_date,
	project_name, project_color_identifier, project_id");
$q->addJoin('projects', 'p', 'project_id = forum_project');
$q->addWhere("forum_project = $project_id");
$q->addOrder('forum_project, forum_name');
$rc = $q->exec();
?>

<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<tr>
	<th nowrap>&nbsp;</th>
	<th nowrap width="100%"><?php echo $AppUI->_('Forum Name');?></th>
	<th nowrap><?php echo $AppUI->_('Messages');?></th>
	<th nowrap><?php echo $AppUI->_('Last Post');?></th>
</tr>
<?php
while ($row = db_fetch_assoc($rc)) { ?>
<tr>
	<td nowrap align=center>
<?php
	if ($row["forum_owner"] == $AppUI->user_id) { ?>
		<A href="./index.php?m=forums&a=addedit&forum_id=<?php echo $row["forum_id"];?>"><img src="./images/icons/pencil.gif" alt="expand forum" border="0" width=12 height=12></a>
<?php } ?>
	</td>
	<td nowrap><A href="./index.php?m=forums&a=viewer&forum_id=<?php echo $row["forum_id"];?>"><?php echo $row["forum_name"];?></a></td>
	<td nowrap><?php echo $row["forum_message_count"];?></td>
	<td nowrap>
		<?php echo (intval($row["forum_last_date"]) > 0) ? $row["forum_last_date"] : 'n/a'; ?>
	</td>
</tr>
<tr>
	<td></td>
	<td colspan=3><?php echo $row["forum_description"];?></td>
</tr>
<?php }
$q->clear();
?>
</table>
<form method="get" action="./index.php">
<input type="hidden" name="m" value="forums" />
<input type="hidden" name="a" value="addedit" />
<input type="hidden" name="forum_project" value="<?php echo $project_id; ?>" />
<input type="submit" value="<?php echo $AppUI->_('new forum'); ?>" class="button" />
</form>
