<?php
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}
//header('Location: index.php?m=projects');
GLOBAL $AppUI, $projects, $company_id, $pstatus, $project_types, $currentTabId, $currentTabName;
require_once($AppUI->getModuleClass('projects'));
require_once($AppUI->getModuleClass('companies'));
$extra = array();
$projectObj = new CProject();
$projects = $projectObj->getAllowedRecords($AppUI->user_id, 'project_name, project_id', 'project_id', null, $extra);
//print_r($projects);
//$projects = arrayMerge(array('0'=>$AppUI->_('All', UI_OUTPUT_JS)), $projects);
?>
<h1>Scope and Schedule</h1>
<table class="tbl" width="100%">
<th><?php echo $AppUI->_("Project"); ?></th>
<th><?php echo $AppUI->_("Company"); ?></th>
<?php
foreach($projects as $project_id){
	$projectObj->load($project_id);	
	?>
	<tr>
		<td>
			<a href="index.php?m=scope_and_schedule&a=view_project_wbs&project_id=<?php echo $projectObj->project_id ?>"><?php echo $projectObj->project_name ?></a>
		</td>
		<td>
			<?php
			$companyObj= new CCompany();
			$companyObj->load($projectObj->project_company);
			echo $companyObj->company_name;
			
			?>
		</td>
	</tr>
	<br /><br />
	<?php
}
?>
</table>