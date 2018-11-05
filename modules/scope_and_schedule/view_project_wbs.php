<?php
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}
GLOBAL $AppUI;
$userDateFormat=$AppUI->user_prefs["SHDATEFORMAT"]; 
$_SESSION["dateFormatPHP"]=$userDateFormat;
$userDateFormat=str_replace("%d", "dd", $userDateFormat);
$userDateFormat=str_replace("%m", "mm", $userDateFormat); 
$userDateFormat=str_replace("%Y", "YY", $userDateFormat);
$userDateFormat=strtolower($userDateFormat); 
$_SESSION["dateFormat"]=$userDateFormat;
$AppUI->savePlace();


require_once DP_BASE_DIR . "/modules/scope_and_schedule/task_user_assigment.class.php";
require_once DP_BASE_DIR . "/modules/scope_and_schedule/wbs_item.class.php";
require_once($AppUI->getModuleClass('projects'));
$projectObj = new CProject();
$project_id = dPgetParam($_GET, "project_id", 0);
$projectObj->load($project_id);
$company_id=$projectObj->project_company;
$_SESSION["company_id"]=$company_id;
$_SESSION["wbsItemsArray"]= array();
function printWBSItem($wbsItem){
		global $project_id,$AppUI;
		//get children
		$listChildren=$wbsItem->loadWBSItems($project_id, $wbsItem->id);
		//update is leaf/work package attribute
		$wbsItem->is_leaf=sizeof($listChildren)==0?1:0;
		$wbsItem->store();
		//update global list: for dropdown and other on screen functions
		array_push($_SESSION["wbsItemsArray"], $wbsItem);
		$tasks=array();
		if($wbsItem->is_leaf){
			$tasks=$wbsItem->loadActivities();
		}
		?>
		<br />
			<li>  <?php echo $wbsItem->number ?> &nbsp;			
			<form action="?m=scope_and_schedule" name="wbs_add_child_<?php echo $wbsItem->id ?>" method="post" style="display:inline">
				<input type="hidden" name="dosql" value="do_wbs_item_aed">
				<input type="hidden" name="project_id" value="<?php echo $project_id ?>" /> 
				<input type="hidden" name="sort_order" value="1" />
				<input type="hidden" name="number" value="1" />
				<input type="hidden" name="is_leaf" value="0" />  	
				<input type="hidden" name="id_wbs_item_parent" value="<?php echo $wbsItem->id ?>" />
				<input type="hidden" name="item_name" placeholder="Input item description..." /> 
				<?php if (count($tasks)==0){ ?>
					<img src="modules/scope_and_schedule/images/add_button_icon.png" style="cursor:pointer;height:18px;width:18px" onclick="saveScrollPosition();document.wbs_add_child_<?php echo $wbsItem->id ?>.submit();" />
				<?php } ?>
			</form>
			<?php if ($wbsItem->id_wbs_item_parent!=0){?> 
				<img src="modules/scope_and_schedule/images/reorder_icon.png" style="cursor:pointer;height:18px;width:18px"  id="wbs_move_<?php echo $wbsItem->id ?>"   onclick="saveScrollPosition();openMoveWBSItem(<?php echo $wbsItem->id ?>, '<?php echo $wbsItem->number . ' - ' . $wbsItem->item_name ?>', <?php echo $project_id; ?>);" />
			<?php  } ?> 
			<?php 
			if ($wbsItem->is_leaf==1){
			?>
				<ul id="menu" style="width:0px; border: 0px; display:inline-block; ">
				  <li style="display: inline-block" > 
				  <img src="modules/scope_and_schedule/images/work_package_icon.png" style="height:15px;width:15px"  />
					 <ul>
					  <li onclick='openDialogWBSDictionary(<?php echo $wbsItem->id; ?>, "<?php echo $wbsItem->number . " ". addslashes($wbsItem->item_name); ?>", "<?php echo addslashes($wbsItem->wbs_dictionary); ?>")' style="cursor:pointer">
						<div><?php echo $AppUI->_("WBS dictionary") ?></div>
					  </li>
					  <li onclick="saveScrollPosition();document.wbs_new_activity_<?php echo $wbsItem->id ?>.submit();">
						<div><?php echo $AppUI->_("New activity") ?></div>
							<form action="?m=scope_and_schedule" name="wbs_new_activity_<?php echo $wbsItem->id ?>" method="post" style="display:none">
								<input type="hidden" name="dosql" value="do_new_activity">
								<input type="hidden" name="project_id" value="<?php echo $project_id ?>" /> 
								<input type="hidden" name="wbs_item_id" value="<?php echo $wbsItem->id ?>" /> 
							</form>
						</li>
					  
					  
					</ul>
				  </li>
				</ul>
			<?php } ?>
			<br />
			<form action="?m=scope_and_schedule" name="wbs_delete_<?php echo $wbsItem->id ?>" method="post" style="display:inline">
				<input type="hidden" name="dosql" value="do_wbs_item_deletion">
				<input type="hidden" name="project_id" value="<?php echo $project_id ?>" /> 
				<input type="hidden" name="id" value="<?php echo $wbsItem->id ?>" /> 
				<img src="modules/scope_and_schedule/images/trash-icon.png" style="cursor:pointer;height:15px;width:12px" onclick="saveScrollPosition();document.wbs_delete_<?php echo $wbsItem->id ?>.submit();" />
			</form>
			
			
			
				<form action="?m=scope_and_schedule" name="wbs_update_<?php echo $wbsItem->id ?>" id="wbs_update_<?php echo $wbsItem->id ?>" method="post" style="display:inline">
					<input type="hidden" name="dosql" value="do_wbs_item_aed">
					<input type="hidden" name="project_id" value="<?php echo $project_id ?>" /> 
					<input type="hidden" name="id" value="<?php echo $wbsItem->id ?>" /> 
					<input type="hidden" name="sort_order" value="1" />
					<input type="hidden" name="number" value="1" />
					<input type="hidden" name="is_leaf" value="0" />  	
					<input type="hidden" name="id_wbs_item_parent" value="<?php echo $wbsItem->id_wbs_item_parent ?>" />
					<input type="text" name="item_name" placeholder="Input item description..." value="<?php echo $wbsItem->item_name; ?>" onchange="saveScrollPosition();ajaxFormSubmit('wbs_update_<?php echo $wbsItem->id ?>');" style="width:40%" maxlength="100" title="<?php echo addslashes($wbsItem->wbs_dictionary); ?>" /> 	
					
				</form>
				
				
				<ol>
				<?php if (count($tasks)>0){ ?>
				<b><i><?php echo $AppUI->_("Activities"); ?> </i></b>
				<?php
					$taskOrder=1;
					foreach($tasks as $task){
						?>
						<li>
							<br />
							<div>
								<form action="?m=scope_and_schedule" name="activity_delete_<?php echo $task->task_id ?>" method="post" style="display:inline">
									<input type="hidden" name="dosql" value="do_activity_deletion">
									<input type="hidden" name="task_id" value="<?php echo $task->task_id ?>" /> 
									<img src="modules/scope_and_schedule/images/trash-icon.png" style="cursor:pointer;height:15px;width:12px" onclick="saveScrollPosition();document.activity_delete_<?php echo $task->task_id ?>.submit();" />
								</form>
								<form action="?m=scope_and_schedule" name="task_update_<?php echo $task->task_id  ?>" id="task_update_<?php echo $task->task_id  ?>" method="post" style="display:inline">
								A.<?php echo $wbsItem->number ?>.<?php echo $taskOrder++; ?>
									<input type="hidden" name="dosql" value="do_update_task" />
									<input type="hidden" name="task_id" value="<?php echo $task->task_id ?>" />
									<input type="text" value="<?php echo $task->task_name ?>" name="task_name" style="width:550px;" maxlength="100"  onchange="saveScrollPosition();ajaxFormSubmit('task_update_<?php echo $task->task_id ?>');" />	
									<img src="modules/scope_and_schedule/images/reorder_icon.png" style="cursor:pointer;height:18px;width:18px"  id="wbs_move_<?php echo $wbsItem->id ?>"   onclick="saveScrollPosition();openMoveActivity(<?php echo $wbsItem->id ?>, <?php echo $task->task_id ?>, '<?php echo addslashes($task->task_name) ?>');" />
									<table style="width:550px">
										<tr>
											<td style="vertical-align:top">
												<div style="margin-top:5px;margin-left:15px">
													
													<label><?php echo $AppUI->_("Planned dates") ?>:</label>
													<input size="8" type="text" value="<?php $date = new CDate($task->task_start_date ); echo $date->format($_SESSION["dateFormatPHP"]);?>" name="start_date" onchange="saveScrollPosition();ajaxFormSubmit('task_update_<?php echo $task->task_id ?>');" />
													&nbsp;<label><?php echo $AppUI->_("to") ?></label>&nbsp;
													<input size="8" type="text" value="<?php $date = new CDate($task->task_end_date ); echo $date->format($_SESSION["dateFormatPHP"]);?>" name="end_date" onchange="saveScrollPosition();ajaxFormSubmit('task_update_<?php echo $task->task_id ?>');" />									
													<script>
														var form=$("#task_update_<?php echo $task->task_id ?>"); 
														var startDate=form.find("[name='start_date']");
														var endDate=form.find("[name='end_date']");
														startDate.datepicker({dateFormat: "<?php echo $_SESSION["dateFormat"] ?>",showButtonPanel: true, firstDay: 1, changeYear:true, changeMonth:true} );
														endDate.datepicker({dateFormat: "<?php echo $_SESSION["dateFormat"] ?>",showButtonPanel: true, firstDay: 1, changeYear:true, changeMonth:true});
													</script>
												</div>	
								</form>
											</td>
											<td style="vertical-align:top;text-align:right">
												<div id="assigned_users_comp">
												<?php
													$taskAssigment= new CTaskAssignement();
													$users=$taskAssigment->getAllUsersFromCompany($_SESSION["company_id"]);
													$assigned_users=$taskAssigment->getAssignedUsersToTask($task->task_id);
												?>
													<form action="?m=scope_and_schedule" name="activity_add_user_<?php echo $task->task_id ?>" method="post" style="display:inline" >
														<input type="hidden" name="dosql" value="do_add_user_to_task" />
														<input type="hidden" name="task_id" value="<?php echo $task->task_id ?>" />
														<label><?php echo $AppUI->_("Resources") ?>:</label>
														<select name="user_id" style="height:22px">
															<option value="-1"><?php echo $AppUI->_("-- Select a resource --") ?></option> 
														<?php
														foreach($users as $user){
														?>
														<option value="<?php echo  $user['user_id'] ?>"> 
															<?php echo  $user['contact_first_name'] ." " . $user['contact_last_name'] ?>
														</option>
														<?php
															}
														?>
														</select>
														<img src="modules/scope_and_schedule/images/add_button_icon.png" style="cursor:pointer;height:18px;width:18px" onclick="saveScrollPosition();if(verifyResourceSelection(document.activity_add_user_<?php echo $task->task_id ?>.user_id)){document.activity_add_user_<?php echo $task->task_id ?>.submit();}" />
													</form>
													
														<?php
														foreach ($assigned_users as $user){
														?>
															
															<form action="?m=scope_and_schedule" name="activity_delete_user_<?php echo $task->task_id ."_".$user['user_id'];?>" method="post" style="margin-top:7px">
																<input type="hidden" name="dosql" value="do_delete_user_to_task" />
																<input type="hidden" name="task_id" value="<?php echo $task->task_id ?>" />
																<input type="hidden" name="user_id" value="<?php echo  $user['user_id'] ?>" />
																<?php echo  $user['contact_first_name'] ." " . $user['contact_last_name'] ?>
																<img src="modules/scope_and_schedule/images/trash-icon.png" style="cursor:pointer;height:15px;width:12px" onclick="saveScrollPosition();document.activity_delete_user_<?php echo $task->task_id ."_".$user['user_id']; ?>.submit();" />
																&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
															</form>
															
														<?php
														}
														?>
													
													</div>
											</td>
										</tr>
									</table>
							</div>	
													
						</li>					
						<?php
						
					}
				}
				?>
				</ol>
				<ol>
					<?php
						$order=1;
						foreach ($listChildren as $child){
							$child->sort_order=$order;
							$child->number=$wbsItem->number.".".$order;
							$child->store();
							printWBSItem($child);
							$order++;
						}
					?>
				</ol>
				
			</li>
			
<?php
}
?>
<script src="modules/scope_and_schedule/js/jquery-3.2.1.min.js"></script>
<script src="modules/scope_and_schedule/js/jquery-ui.js"></script>
<script src="modules/scope_and_schedule/js/wbs-functions.js"></script>
<script src="modules/scope_and_schedule/js/jquery-datepicker-customizations.js"></script>
<link rel="stylesheet" href="modules/scope_and_schedule/css/jquery-ui.css">
<!-- include libraries for lightweight messages -->
<link type="text/css" rel="stylesheet" href="modules/scope_and_schedule/js/alertify/alertify.css" media="screen"></link>
<script type="text/javascript" src="modules/scope_and_schedule/js/alertify/alertify.js"></script>


<style>
.wbs {}
.wbs OL { list-style-type: none; }
.wbs OL LI { display: inline;}
</style>
 
<script>
//function should be called after any submit in wbs page
function saveScrollPosition(){
		var y= window.scrollY;
	    window.sessionStorage.setItem('wbsScrollY',y);
}
</script>
<div style="text-align: right;margin-top:5px;margin-right:5px">
	<input type="button" class="button" value="<?php echo $AppUI->_("Sequence activities"); ?>" onclick="window.location='index.php?m=scope_and_schedule&a=projects_activities_sequencing&project_id=<?php echo $projectObj->project_id ?>';" />
</div>
<span style="margin-left: 20px">
	<br />
	<b><?php echo $AppUI->_("Work Breakdown Structure - WBS"); ?></b> - 
	<a href="index.php?m=projects&a=view&project_id=<?php echo $projectObj->project_id ?>"><?php echo $projectObj->project_name ?></a>
	
</span>
<span class="wbs">
	<ol>
	<?php
	$wbsItem= new WBSItem ();
	$list=$wbsItem->loadWBSItems($project_id, 0);//seed of recursive procedure, the root WBS Item

	if(sizeof($list)==0){
		// there is no WBS, start creating the root one - automatically utilizing the project name
		$wbsItem->id_wbs_item_parent=0;
		$wbsItem->project_id = $project_id;
		$wbsItem->sort_order = 0;
		$wbsItem->item_name  = "";
		$wbsItem->number  = 1;
		$wbsItem->is_leaf = 1;
		$wbsItem->store();
		$list[0]=$wbsItem;
	}
	printWBSItem($list[0]);
	
	?>
	</ol>
</span>

<div id="dialog_move_wbs" title="Move WBS item" style="background-color:FFF">
<form name="move_wbs_item" action="?m=scope_and_schedule" method="post">
	<input type="hidden" name="dosql" value="do_wbs_item_move" />
	<input type="hidden" name="project_id" value="<?php echo $project_id ?>" />
	<input type="hidden" name="id" value="" /> 
	 <b><?php echo $AppUI->_("Moving item"); ?>:</b> <i><span id="move_wbs_item_name"></span></i>
	 <br /><br />
	  <?php echo $AppUI->_("Move to position"); ?>:<br />
	  <select name="wbs_id_position"> 
	  <?php
	  foreach ($_SESSION["wbsItemsArray"] as $wbsItem){
		  if($wbsItem->number != 1){
		  ?>
		  <option value="<?php echo $wbsItem->id  ?>"><?php echo $wbsItem->number  ?></option>
	  <?php
		  }
	  }
	  ?>
	  </select>
	  <br /><br />
	  <?php echo $AppUI->_("Order"); ?>: <br />
	  <select name="order">
		<option value="-0.1"><?php echo $AppUI->_("Before") ?></option> 
		<option value="0.1"><?php echo $AppUI->_("After") ?> </option>
	  </select> 
	  <br /><br />
	  <input type="button" onclick="submitMoveItem()" value="<?php echo $AppUI->_("Confirm"); ?>" />
	  <input type="button" value="<?php echo $AppUI->_("Cancel"); ?>" onclick="closeMoveWBSItem()" />
</form>
</div>


<div id="dialog_move_project_activity" title="Move WBS item" style="background-color:FFF">
<form name="move_project_activity" action="?m=scope_and_schedule" method="post">
	<input type="hidden" name="dosql" value="do_move_activity_to_wbs" />
	<input type="hidden" name="project_id" value="<?php echo $project_id ?>" />
	<input type="hidden" name="task_id" value="" /> 
	 <b><?php echo $AppUI->_("Moving activity") ?>:</b> <i><span id="move_activity_description"></span></i>
	 <br /><br />
	  <?php echo $AppUI->_("Work package") ?>:<br />
	  <select name="wbs_item_id"> 
	  <?php
	  $wps=$wbsItem->loadWorkpackages($project_id);
	  foreach ($wps as $wbsItem){
		  ?>
		  <option value="<?php echo $wbsItem->id  ?>"><?php echo $wbsItem->number  ?></option>
	  <?php
	  }
	  ?>
	  </select> 
	  <br /><br />
	  <input type="submit" value="<?php echo $AppUI->_("Confirm"); ?>" />
	  <input type="button" value="<?php echo $AppUI->_("Cancel"); ?>" onclick="closeMoveActivity()" />
</form>
</div>

<div id="dialog_wbs_dictionary" title="WBS Dictionary" style="background-color:FFF">
	<form name="wbs_dictionary" action="?m=scope_and_schedule" method="post">
		<input type="hidden" name="dosql" value="do_wbs_dictionary" />
		<input type="hidden" name="id" value="<?php echo $wbsItem->id ?>" /> 
		<b><?php echo $AppUI->_("Dictionary for item"); ?>:</b> <i><span id="dictionary_wbs_item_name"></span></i><br /><br />
		<textarea name="dictionary" maxlength="250" cols="40" rows="4"></textarea>
		  <br /><br />
		  <input type="submit" value="<?php echo $AppUI->_("Confirm"); ?>" />
		  <input type="button" value="<?php echo $AppUI->_("Cancel"); ?>" onclick="closeDialogWBSDictionary()" />
	</form>
</div>

<div id="dialog_move_activity" title="<?php echo $AppUI->_("Move activity") ?>" style="background-color:FFF">
	<form name="move_activity" action="?m=scope_and_schedule" method="post">
		<input type="hidden" name="dosql" value="do_move_activity" />
		<input type="hidden" name="task_id" value="" />
		<?php echo $AppUI->_("Moving activity") ?>:
		<span id="move_activity_name"></span>
		<br /><br />
		 <?php echo $AppUI->_("Work package") ?>:<br />
		  <select name="wbs_item_id" id="move_activity_wbs_item_id"> 
			  <?php
			  $wps=$wbsItem->loadWorkpackages($project_id);
			  foreach ($wps as $wbsItem){
				  ?>
				  <option value="<?php echo $wbsItem->id  ?>"><?php echo $wbsItem->number  ?> - <?php echo $wbsItem->item_name ?></option>
			  <?php
			  }
			  ?>
		  </select> 
		  <br /><br />
		  <?php echo $AppUI->_("Order") ?>: <br />
		  <select name="order" />
		  <?php for ($i=0;$i<=10;$i++){ ?>
			<option value="<?php echo $i ?>"><?php echo $i ?></option>
		  <?php } ?>
		  </select>
		  <br /><br />
		  <input type="submit" value="<?php echo $AppUI->_("Confirm"); ?>" />
		  <input type="button" value="<?php echo $AppUI->_("Cancel"); ?>" onclick="closeMoveActivityOrder()" />
	</form>
</div>

<div id="non_wbs_tasks">
	<?php
	$wbsItem= new WBSItem ();
	$tasksOrpan=$wbsItem->loadAcivitiesNonInWBS($project_id);
	if( sizeof($tasksOrpan) >0){
	?>
	<br />
	<b>
		<?php echo $AppUI->_("Project activities out of WBS"); ?>
	</b>
	<br />
		<ul>
			<?php
			foreach($tasksOrpan as $task){
				?>
					<li onclick="openDialogMoveActivity(<?php echo $task->task_id; ?>, '<?php echo addslashes($task->task_name); ?>')" style="cursor:pointer">
						<?php echo $task->task_name; ?> (<i><u><?php echo $AppUI->_("click here to move it to WBS") ?></u></i>)
					</li>
					<?php
				}
				?>
		</ul>
			<?php
			}
			?>
</div>

<script>
  $( function() {
    $( "[id=menu]" ).menu();
    $( document ).tooltip();
  } );
  </script>
<script>
	//keep the scroll position in the same position after user perform submit action in wbs
	var y=window.sessionStorage.getItem('wbsScrollY');
	if(y!=null && y !=""){
		window.scrollTo(0, y)
	}
</script>