<?php /* HISTORY $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

##
## History module
## (c) Copyright
## J. Christopher Pereira (kripper@imatronix.cl)
## IMATRONIX
## 

$AppUI->savePlace();
$titleBlock = new CTitleBlock('History', 'stock_book_blue_48.png', $m, "$m.$a");
$titleBlock->show();

function show_history($history) {
	GLOBAL $AppUI;
	$id = $history['history_item'];
	$table = $history['history_table'];
	$q = new DBQuery;
	
	$q->addTable('modules', 'm');
	$q->addQuery('m.*');
	$q->addWhere("m.`permissions_item_table` LIKE '" . $table . "'");
	$module_result = $q->fetchRow();
	$q->clear();
	
	$table_id = $module_result['permissions_item_field'];
	$module = $module_result['mod_directory'];
		  
	if (!($table_id || $module)) { //valid "modules" w/o item level permissions
		switch ($table) {
			case 'history': //table name does not match with module name
				$table_id = 'history_id';
				$module = 'history';
				break;
		}
	}
	
	if ($table == 'login') {
		return ($AppUI->_('User') . ' \'' . $history['history_description'] . '\' ' 
		        . $AppUI->_($history['history_action']));
	}
	
	if ($history['history_action'] == 'add') {
		$msg = $AppUI->_('Added new').' ';
	} else if ($history['history_action'] == 'update') {
		$msg = $AppUI->_('Modified').' ';
	} else if ($history['history_action'] == 'delete') {
		return ($AppUI->_('Deleted').' \'' . $history['history_description'] . '\' ' 
		        . $AppUI->_('from').' ' . $AppUI->_($table) . ' ' . $AppUI->_('table'));
	}
	
	if ($table_id && $module) {
		$q->addTable($table);
		$q->addQuery($table_id);
		$q->addWhere($table_id . '=' . $id);
		$is_table_result = $q->loadResult();
		$q->clear();
	}
	if ($is_table_result) {
		switch ($table) {
			case 'history':
			case 'files':
			case 'links':
				$link = '&a=addedit&' . $table_id . '=';
				break;
			case 'tasks':
			case 'projects':
			case 'companies':
			case 'departments':
			case 'events':
			case 'contacts':
				$link = '&a=view&' . $table_id . '=';
				break;
			case 'forums':
				$link = '&a=viewer&' . $table_id . '=';
				break;
			case 'task_log':
				$module = 'tasks';
				$q->addTable('task_log');
				$q->addQuery('task_log_task');
				$q->addWhere('task_log_id = ' . $id);
				$task_log_task = $q->loadResult();
				$q->clear();
				$link = '&a=view&task_id=' . $task_log_task . '&tab=1&' . $table_id . '=';
				$in_page_anchor = '#log';
				break;
		}
	}
	
	$link = ((!empty($link)) 
	         ? ('<a href="?m=' . $module . $link . $id . $in_page_anchor . '">' 
	            . $history['history_description'] . '</a>') 
	         : $history['history_description']);
	
	$msg .= ($AppUI->_('item') . ' "' . $link . '" ' . $AppUI->_('in') . ' "' 
	         . $AppUI->_($table) . '" ' . $AppUI->_('table'));
	
	return $msg;
}


$q = new DBQuery;
$filter = '';
$page = ((isset($_REQUEST['pg'])) ? (int)$_REQUEST['pg'] : 1);
$limit = ((isset($_REQUEST['limit'])) ? (int)$_REQUEST['limit'] : 100);
$offset = ($page-1) * $limit;
$in_filter = ((!empty($_REQUEST['filter'])) ? $_REQUEST['filter'] : '');

$q->addTable('modules', 'm');
$q->addQuery('mod_directory, mod_name, permissions_item_table, permissions_item_field');
$q->addWhere('permissions_item_table is not null');
$q->addWhere("permissions_item_table <> ''");
$available_modules = $q->loadHashList('mod_directory');
$q->clear();

$filter_options = array();
$filter_module_tables = array();
$denied_tables = '';
foreach ($available_modules as $my_mod => $my_mod_data) {
	$my_mod_table = $my_mod_data['permissions_item_table'];
	
	$filter_options[$my_mod]['Name'] = $my_mod_data['mod_name'];
	$filter_options[$my_mod]['Table'] = $my_mod_table;
	$filter_options[$my_mod]['Table_ID'] = $my_mod_data['permissions_item_field'];
	$filter_options[$my_mod]['Table_ID_Name'] = $my_mod_data['permissions_item_label'];
	
	$filter_module_tables[$my_mod] = $my_mod_table;
	if ($my_mod_table && !(getPermission($my_mod, 'view'))) {
		$denied_tables .= ((($denied_module_list) ? "','" : '') . $my_mod_table);
	}
}


$q->includeCount();
$q->addTable('history', 'h');
$q->leftJoin('users', 'u', 'u.user_id = h.history_user');
$q->addQuery('h.*, u.*');
if ($in_filter) {
	$filter .= (($filter) ? ' AND ' : '') . "(h.`history_table` LIKE '" . $in_filter . "%')";
}
if ($denied_tables) {
	$filter .= (($filter) ? ' AND ' : '') . "(NOT h.`history_table` IN ('" . $denied_tables . "'))";
}
if (!empty($_REQUEST['project_id'])) {
	$project_id = $_REQUEST['project_id'];
	$r = new DBQuery;
	
	$r->addTable('tasks');
	$r->addQuery('task_id');
	$r->addWhere('task_project = ' . $project_id);
	$project_tasks = implode(',', $r->loadColumn());
	$r->clear();
	
	$r->addTable('files');
	$r->addQuery('file_id');
	$r->addWhere('file_project = ' . $project_id);
	$project_files = implode(',', $r->loadColumn());
	$r->clear();
	
	if (!empty($project_tasks)) {
		$project_tasks = " OR (history_table = 'tasks' AND history_item IN ($project_tasks)) ";
	}
	if (!empty($project_files)) {
		$project_files = " OR (history_table = 'files' AND history_item IN ($project_files)) ";
	}
	
	$filter .= (($filter) ? ' AND ' : '') . ("((history_table = 'projects' AND history_item = " 
	                                         . $project_id . ')' . $project_tasks . $project_files 
	                                         . ')');
}
if ($filter) {
	$q->addWhere($filter);
}

$q->addOrder('history_date DESC');
$q->setLimit($limit, $offset);
$my_history = $q->loadList();
$history = $my_history;
$count = $q->foundRows();

$pages = (int)($count / $limit) + 1;
$max_pages = 20;

$first_page = (($pages > $max_pages) ? max(($page - (int)($max_pages/2)), 1) : 1);
$last_page = (($pages > $max_pages) ? min(($first_page + $max_pages - 1), $pages) : $pages);

?>

<table width="100%" cellspacing="1" cellpadding="0" border="0">
  <tr>
	<td nowrap align="right">
	  <form name="filter" action="?m=history" method="post" >
	  <?php echo $AppUI->_('Changes to'); ?>:
		<select name="filter" onChange="document.filter.submit()">
		  <option value=""><?php echo $AppUI->_('Show all'); ?></option>
<?php
foreach ($filter_options as $mod => $mod_data) {
	if (getPermission($mod, 'access') && $mod_data['Table']) {
		echo ('		  <option value="' . $mod_data['Table'] . '"' 
		      . (($in_filter == $mod) ? ' selected="selected"' : '') . '>'
		      . $AppUI->_($mod_data['Name']) . '</option>' . "\n");
	}
}
?>
		  <option value="login"<?php 
echo (($in_filter == 'login') ? ' selected="selected"' : ''); 
?>><?php echo $AppUI->_('Login/Logouts'); ?></option>
		  <option value="history"<?php 
echo (($in_filter == 'history') ? ' selected="selected"' : ''); 
?>><?php echo $AppUI->_('History'); ?></option>
		</select>
	  <?php
if ($pages > 1) {
	for ($i = $first_page; $i <= $last_page; $i++) {
		echo '&nbsp;';
		if ($i == $page) {
			echo '<b>'.$i.'</b>';
		} else {
			echo '<a href="?m=history&filter=' . $in_filter . '&pg=' . $i . '">' . $i . '</a>';
		}
	}
}
?>
	  </form>
	</td>
	<td align="right"><input class="button" type="button" value="<?php 
echo $AppUI->_('Add history'); ?>" onclick="window.location='?m=history&a=addedit'"></td>
  </tr>
</table>

<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
  <tr>
	<th width="10">&nbsp;</th>
	<th width="200"><?php echo $AppUI->_('Date'); ?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_('Description'); ?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_('User'); ?>&nbsp;&nbsp;</th>
  </tr>
<?php
foreach ($history as $row) {
	$mod_table = $row['history_table'];
	$module = (($mod_table == 'task_log') ? 'tasks' : $filter_module_tables[$mod_table]);
	
	$df = $AppUI->getPref('SHDATEFORMAT');
	$tf = $AppUI->getPref('TIMEFORMAT');
	$hd = new Date($row['history_date']);
	// Checking permissions.
	// TODO: Enable the lines below to activate new permissions.
	if ($mod_table == 'login' || $mod_table == 'history' 
		|| !(in_array($mod_table, $filter_module_tables))
	    || getPermission($module, 'access', $row['history_item'])) {
?>
  <tr>	
	<td><a href="?m=history&a=addedit&history_id=<?php echo ($row['history_id']); ?>">
	  <img src="./images/icons/pencil.gif" alt="<?php 
echo $AppUI->_('Edit History') ?>" border="0" width="12" height="12">
	</a></td>
	<td align="center"><?php echo ($hd->format($df) . ' ' . $hd->format($tf)); ?></td>
	<td><?php echo show_history($row); ?></td>
	<td align="center"><?php echo $row['user_username']; ?></td>
</tr>
<?php
	}
}
?>
</table>
