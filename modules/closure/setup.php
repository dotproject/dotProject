<?php
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly');
}

$config = array(
	'mod_name' => 'Closure',
	'mod_version' => '1.0.1',
	'mod_directory' => 'closure',
	'mod_setup_class' => 'SClosure',
	'mod_type' => 'user',
	'mod_ui_name' => 'Closure',
	'mod_ui_icon' => 'helpdesk.png',
	'mod_description' => '',
	'permissions_item_table' => 'post_mortem_analysis',
	'permissions_item_field' => 'pma_id',
	'permissions_item_label' => 'project_name'
);

if (@$a == 'setup') {
	echo dPshowModuleConfig($config);
}

class SClosure {
	function install() {
		$ok = true;
		$q = new DBQuery;
		$sql = "(
			pma_id integer not null auto_increment,
			project_name varchar(255) not null default '',
			project_start_date datetime default null,
			project_end_date datetime default null,
			project_planned_start_date datetime default null,
			project_planned_end_date datetime default null,
			project_meeting_date datetime default null,
			planned_budget text,
			budget text,
      participants text,
			project_strength text,
			project_weaknesses text,
			improvement_suggestions text,
			conclusions text,
			primary key (pma_id)
   )";
		
		$q->createTable('post_mortem_analysis');
		$q->createDefinition($sql);
		$ok = $ok && $q->exec();
		$q->clear();

		if (!$ok)
			return false;
		return null;
	}

	function remove() {
		$q = new DBQuery;
		$q->dropTable('post_mortem_analysis');
		$q->exec();
		$q->clear();

		return null;
	}

	function upgrade($old_version) {
		return true;
	}
}
?>
