<?php /* HISTORY $Id$ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

##
## History module
## (c) Copyright
## J. Christopher Pereira (kripper@imatronix.cl)
## IMATRONIX
## 

$AppUI->savePlace();
$titleBlock = new CTitleBlock( 'History', 'stock_book_blue_48.png', $m, "$m.$a" );
$titleBlock->show();

function show_history($history)
{
//        return $history;
	GLOBAL $AppUI;
        $id = $history['history_item'];
        $module = $history['history_table'];        

        switch ($module)
        {
        case 'companies': // special case since the singular of companies is company and not companie
            $table_id = "company_id";
            break;
        default:
            $table_id = (substr($module, -1) == 's'?substr($module, 0, -1):$module) . '_id';
        }
        
        if ($module == 'login')
               return $AppUI->_('User') . ' \'' . $history['history_description'] . '\' ' . $AppUI->_($history['history_action']);
        
        if ($history['history_action'] == 'add')
                $msg = $AppUI->_('Added new').' ';
        else if ($history['history_action'] == 'update')
                $msg = $AppUI->_('Modified').' ';
        else if ($history['history_action'] == 'delete')
                return $AppUI->_('Deleted').' \'' . $history['history_description'] . '\' '.$AppUI->_('from').' ' . $AppUI->_($module) . ' ' . $AppUI->_('module');

	$q  = new DBQuery;
	$q->addTable($module);
	$q->addQuery($table_id);
	$q->addWhere($table_id.' ='.$id);
	$sql = $q->prepare();
	$q->clear();
	if (db_loadResult($sql))
        switch ($module)
        {
        case 'history':
                $link = '&a=addedit&history_id='; break;
        case 'files':
                $link = '&a=addedit&file_id='; break;
        case 'tasks':
                $link = '&a=view&task_id='; break;
        case 'forums':
                $link = '&a=viewer&forum_id='; break;
        case 'projects':
                $link = '&a=view&project_id='; break;
        case 'companies':
                $link = '&a=view&company_id='; break;
        case 'contacts':
                $link = '&a=view&contact_id='; break;
        case 'task_log':
                $module = 'Tasks';
                $link = '&a=view&task_id=170&tab=1&task_log_id=';
                break;
        }

	if (!empty($link))
		$link = '<a href="?m='.$module.$link.$id.'">'.$history['history_description'].'</a>';
	else
		$link = $history['history_description'];
        $msg .= $AppUI->_('item')." '$link' ".$AppUI->_('in').' '.$AppUI->_(ucfirst($module)).' '.$AppUI->_('module'); // . $history;

        return $msg;
}

$filter = array();
if (!empty($_REQUEST['filter'])) {
	$in_filter = $_REQUEST['filter'];
        $filter[] = 'history_table = \'' . $_REQUEST['filter'] . '\' ';
} else {
	$in_filter = '';
}

if (!empty($_REQUEST['project_id']))
{
	$project_id = $_REQUEST['project_id'];
	
$q  = new DBQuery;
$q->addTable('tasks');
$q->addQuery('task_id');
$q->addWhere('task_project = ' . $project_id);
$project_tasks = implode(',', $q->loadColumn());
if (!empty($project_tasks))
	$project_tasks = "OR (history_table = 'tasks' AND history_item IN ($project_tasks))";

$q->addTable('files');
$q->addQuery('file_id');
$q->addWhere('file_project = ' . $project_id);
$project_files = implode(',', $q->loadColumn());
if (!empty($project_files))
	$project_files = "OR (history_table = 'files' AND history_item IN ($project_files))";

	$filter[] = "(
	(history_table = 'projects' AND history_item = '$project_id')
	$project_tasks
	$project_files
	)";
}

$page = isset($_REQUEST['pg']) ? (int)$_REQUEST['pg'] : 1;
$limit = isset($_REQUEST['limit']) ? (int)$_REQUEST['limit'] : 100;
$offset = ($page-1) * $limit;
$q  = new DBQuery;
$q->includeCount();
$q->addTable('history');
$q->addTable('users');
$q->addWhere('history_user = user_id');
$q->addWhere($filter);
$q->addOrder('history_date DESC');
$q->setLimit($limit, $offset);
$history = $q->loadList();
$count = $q->foundRows();
$pages = (int)($count / $limit) + 1;
$max_pages = 20;
if ($pages > $max_pages) {
	$first_page = max($page - (int)($max_pages/2), 1);
	$last_page = min($first_page + $max_pages - 1, $pages);
} else {
	$first_page = 1;
	$last_page = $pages;
}
?>

<table width="100%" cellspacing="1" cellpadding="0" border="0">
<tr>
        <td nowrap align="right">
<form name="filter" action="?m=history" method="post" >
<?php echo $AppUI->_('Changes to'); ?>:
        <select name="filter" onChange="document.filter.submit()">
                <option value=""></option>
                <option value="" <?php if ($in_filter == '') echo 'selected="selected"'; ?>><?php echo $AppUI->_('Show all'); ?></option>
                <option value="projects" <?php if ($in_filter == 'projects') echo 'selected="selected"';?>><?php echo $AppUI->_('Projects'); ?></option>
                <option value="tasks" <?php if ($in_filter == 'tasks') echo 'selected="selected"';?>><?php echo $AppUI->_('Tasks'); ?></option>
                <option value="files" <?php if ($in_filter == 'files') echo 'selected="selected"';?>><?php echo $AppUI->_('Files'); ?></option>
                <option value="forums" <?php if ($in_filter == 'forums') echo 'selected="selected"';?>><?php echo $AppUI->_('Forums'); ?></option>
                <option value="login" <?php if ($in_filter == 'login') echo 'selected="selected"';?>><?php echo $AppUI->_('Login/Logouts'); ?></option>
        </select>
	<?php
		if ($pages > 1) {
			for ($i = $first_page; $i <= $last_page; $i++) {
				echo "&nbsp;";
				if ( $i == $page)  {
					echo '<b>'.$i.'</b>';
				} else {
					echo '<a href="?m=history&filter=' . $in_filter . '&pg=' . $i . '">' . $i . '</a>';
				}
			}
		}
	?>
</form>
        </td>
	<td align="right"><input class="button" type="button" value="<?php echo $AppUI->_('Add history');?>" onclick="window.location='?m=history&a=addedit'"></td>
</table>

<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<tr>
	<th width="10">&nbsp;</th>
	<th width="200"><?php echo $AppUI->_('Date');?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_('Description');?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_('User');?>&nbsp;&nbsp;</th>
</tr>
<?php
foreach($history as $row) {
  $module = $row['history_table'] == 'task_log'?'tasks':$row['history_table'];
  // Checking permissions.
  // TODO: Enable the lines below to activate new permissions.
  $perms = & $AppUI->acl();
  if ($module == 'login' || $perms->checkModuleItem($module, "access", $row['history_item']))  {
  	$df = $AppUI->getPref('SHDATEFORMAT');
	$tf = $AppUI->getPref('TIMEFORMAT');

  	$hd = new Date( $row["history_date"] );
	
?>
<tr>	
	<td><a href='<?php echo "?m=history&a=addedit&history_id=" . $row["history_id"] ?>'><img src="./images/icons/pencil.gif" alt="<?php echo $AppUI->_( 'Edit History' ) ?>" border="0" width="12" height="12"></a></td>
	<td align="center"><?php echo $hd->format ( $df ).' '.$hd->format ( $tf ); ?></td>
	<td><?php echo show_history($row) ?></td>	
	<td align="center"><?php echo $row["user_username"]?></td>
</tr>	
<?php
  }
}
?>
</table>
