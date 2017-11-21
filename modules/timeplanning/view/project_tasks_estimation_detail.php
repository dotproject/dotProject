	<?php
	if (!defined('DP_BASE_DIR')) {
	  die('You should not access this file directly.');
	}
	global $AppUI, $task_id, $obj;
	require_once (DP_BASE_DIR . "/modules/timeplanning/model/project_task_estimation.class.php");
	require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_items.class.php");
	require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_company_role.class.php");
	$controllerWBSItem= new ControllerWBSItem();
	$controllerCompanyRole= new ControllerCompanyRole();

	$wpName="";
	$estimatedEffort="";
	$estimatedDuration="";
	$estimatedRoles="";
	$activities=array();
	$WBSItem=$controllerWBSItem->getWBSItemByTask($task_id);
	$wpName=$WBSItem->getName();
	$projectTaskEstimation = new ProjectTaskEstimation();
	$projectTaskEstimation->load($task_id);
	$estimatedEffort=$projectTaskEstimation->getEffort() . " " . $projectTaskEstimation->getEffortUnit();
	$estimatedDuration=$projectTaskEstimation->getDuration();
	
	//roles list
	$rolesObj = $controllerCompanyRole->getCompanyRoles($company_id);
	$roles=array();
        foreach ($rolesObj as $role) {
		$roles[$role->getId()]=$role->getDescription();
	}
	foreach($projectTaskEstimation->getRoles() as $role){
            if($roles[$role->roleId] !=""){
                $estimatedRoles.=$roles[$role->getRoleId()] . " (".$role->getQuantity().")<br>";
            }
	}
?>
<table class="std" width="100%">
	<tr>
		<td align='right' width='200'>
			<?php echo $AppUI->_('LBL_WORK_PACKAGE'); ?>:
		</td>
		<td>
			<?php echo $wpName; ?>
		</td>
	</tr>
	<tr>
		<td align='right'>
			<?php echo $AppUI->_('LBL_EFFORT'); ?>:
		</td>
		<td>
			<?php echo $estimatedEffort; ?>
		</td>
	</tr>
	<tr>
		<td align='right'>
			<?php echo $AppUI->_('LBL_DURATION'). " ".  $AppUI->_('LBL_IN_DAYS'); ?>:
		</td>
		<td>
			<?php echo $estimatedDuration; ?>
		</td>
	</tr>
	<tr>
		<td align='right' valign='top'>
			<?php echo $AppUI->_('LBL_RESOURCES'); ?>:
		</td>
		<td>
			<?php echo $estimatedRoles; ?>
		</td>
	</tr>
</table>