<?php /* SMARTSEARCH$Id$ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

/**
* task_log Class
*/
class task_log extends smartsearch 
{
	var $table = 'task_log';
	var $table_module = 'task_log';
	var $table_key = 'task_log_task';
	var $table_key2 = 'task_log_id';
	var $table_extra = 'task_log_task != 0';
	/*('index.php?m=tasks&a=view&task_id=' . $records['task_log_task'] . '&tab=1&task_log_id=' 
	 . $records['task_log_id'])*/
	var $table_link = 'index.php?m=tasks&a=view&task_id=';
	var $table_link2 = '&tab=1&task_log_id=';
	var $table_title = 'Task logs';
	var $table_orderby = 'task_log_name';
	var $search_fields = array('task_log_name', 'task_log_description', 'task_log_task');
	var $display_fields = array('task_log_name', 'task_log_description', 'task_log_task');
	
	function ctask_log() {
		return new task_log();
	}
}
?>
