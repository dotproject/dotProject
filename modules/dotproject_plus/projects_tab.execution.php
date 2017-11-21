<!-- include libraries for right click menu -->
<script type="text/javascript" src="./modules/dotproject_plus/milonic_right_click_menu/milonic_src.js"></script> 
<script type="text/javascript" src="./modules/dotproject_plus/milonic_right_click_menu/mmenudom.js"></script> 
<script type="text/javascript" src="./modules/dotproject_plus/milonic_right_click_menu/menu_data_activities.js"></script>

<!-- calendar goodies -->
<link type="text/css" rel="stylesheet" href="./modules/timeplanning/js/jsLibraries/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen"></link>
<script type="text/javascript" src="./modules/timeplanning/js/jsLibraries/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>

<a href="http://www.milonic.com/" style="display: none">DHTML JavaScript Menu By Milonic.com</a>

<script>

function displayActivityLogPanel(activityId){
    //document.getElementById("new_activity_log_div_"+activityId).style.display="block";
    $("#new_activity_log_div_"+activityId).slideDown();
}

function closeActivityLogPanel(activityId){
    //document.getElementById("new_activity_log_div_"+activityId).style.display="none";
    $("#new_activity_log_div_"+activityId).slideUp();
}

function displayActivityLogList(activityId){
    $("#list_activity_logs_"+activityId).slideToggle();
}

function validateActivityLog(activityId){
    var result=false;
    var dateField=document.getElementById("task_log_date_"+activityId);
    var dateValue=dateField.value;
    if(validateDateField(dateValue)){
        result=true;
    }else{
        dateField.style.backgroundColor="#ffcccc";
        result=false;
    }
    if(result){
        document.getElementById("activity_form_"+activityId).submit();
    }
}


    function filterActivitiesByUser() {
        document.select_human_resource_filter_form.submit();
    }
    
 /**
 * This function verifies if the value inputted in a date field is numericaly correct.
 * Empty values are consideted false.
 * This function return a boolean value.
 */
function validateDateField(value){
    var result=false;
    if(value!=""){
        var dateParts=value.split("/");
        if(dateParts.length==3){
            var day=dateParts[0];
            var month=dateParts[1];
            var year=dateParts[2];
            if (!isNaN(parseInt(day)) && !isNaN(parseInt(month)) && !isNaN(parseInt(year)) ){
                result=true;
            }
        }
    }
    return result;
}

   function expandControlWorkpackageActivities(id) {
        if (document.getElementById("collapse_icon_" + id).style.display == "none") {
            expandActivities(id);
        } else {
            collapseActivities(id);
        }
    }
    
      function expandActivities(id) {
        var table = document.getElementById("tb_eap");
        for (var i = 0; i < table.rows.length; i++) {
            row = table.rows[i];
            if (row.id.indexOf("wbs_id_" + id) != -1) {
                row.style.display = "none";
            }
        }
        document.getElementById("collapse_icon_" + id).style.display = "inline";
        document.getElementById("expand_icon_" + id).style.display = "none";
    }

    function collapseActivities(id) {
        var table = document.getElementById("tb_eap");
        for (var i = 0; i < table.rows.length; i++) {
            row = table.rows[i];
            if (row.id.indexOf("wbs_id_" + id) != -1 && row.id.indexOf("activity_details_id_") == -1) {
                row.style.display = "table-row";
            }
        }
        document.getElementById("collapse_icon_" + id).style.display = "none";
        document.getElementById("expand_icon_" + id).style.display = "inline";
    }

</script>

<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/model/project_task_estimation.class.php");
require_once (DP_BASE_DIR . "/modules/dotproject_plus/model/ActivityLog.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/model/wbs_item_estimation.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_items.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_item_activity_relationship.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_company_role.class.php");
require_once (DP_BASE_DIR . "/modules/tasks/tasks.class.php");
require_once (DP_BASE_DIR . "/modules/projects/projects.class.php");

$projectId = dPgetParam($_GET, 'project_id', 0);
$activitiesIdsForDisplay; //updated by /modules/timeplanning/view/export_project_plan/time_planning_initializing_logical_ids.php
require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/time_planning_initializing_logical_ids.php");



if ($_GET["show_external_page"] != "") {
    include_once DP_BASE_DIR . $_GET["show_external_page"];
} else {
    $project_id = dPgetParam($_GET, "project_id", 0);
    $project = new CProject();
    $project->load($project_id);
    global $pstatus;
    $controllerWBSItem = new ControllerWBSItem();
    $ControllerWBSItemActivityRelationship = new ControllerWBSItemActivityRelationship();
    $controllerCompanyRole = new ControllerCompanyRole();
    $items = $controllerWBSItem->getWorkPackages($project_id);
//start: build the roles list

    $roles = $controllerCompanyRole->getCompanyRoles($project->project_company);
    $i = 0;
    foreach ($roles as $role) {
        $roles[$role->getId()] = $role->getDescription();
        //start: build human resources list per role
        $q = new DBQuery();
        $q->addTable('contacts', 'c');
        $q->addQuery('user_id, h.human_resource_id, contact_id,u.user_username');
        $q->innerJoin('users', 'u', 'u.user_contact = c.contact_id');
        $q->innerJoin('human_resource', 'h', 'h.human_resource_user_id = u.user_id');
        $q->innerJoin('human_resource_roles', 'hr_roles', 'hr_roles.human_resource_id =h.human_resource_id and hr_roles.human_resources_role_id=' . $role->getId());
        $q->addWhere('c.contact_company = ' . $project->project_company);
        $q->addOrder("u.user_username");
        $sql = $q->prepare();
        $records = db_loadList($sql);
        $j = 0;
        foreach ($records as $record) {
            $userNameByHRid[$record[1]] = $record[3];
            $j++;
        }
        //end: build human resources list per role
        $i++;
    }
//end: build the roles list
//start: build hr list
    $q = new DBQuery();
    $q->addTable('contacts', 'c');
    $q->addQuery('user_id, human_resource_id, contact_id,u.user_username');
    $q->innerJoin('users', 'u', 'u.user_contact = c.contact_id');
    $q->innerJoin('human_resource', 'h', 'h.human_resource_user_id = u.user_id');
    $q->addWhere('c.contact_company = ' . $project->project_company);
    $q->addOrder("u.user_username");
    $sql = $q->prepare();
    $records = db_loadList($sql);
    $i = 0;
    $userNameByHRid = array();
    foreach ($records as $record) {
        $userNameByHRid[$record[1]] = $record[3];
        $i++;
    }
//end: build hr list
//start: buld hr list per role
//end: buld hr list per role 
    ?>
    </script>
    <?php
    $currentPage = substr($_SERVER["REQUEST_URI"], strpos($_SERVER["REQUEST_URI"], "index.php") + 9);
    ?>
    <br />
    <div style="text-align:right">
        <form name="select_human_resource_filter_form" action="<?php echo $currentPage ?>" method="post">
            <span style="color:#000000"><?php echo $AppUI->_("LBL_FILTER"); ?>:</span>
            <select id="project_resources_filter" name="project_resources_filter" onchange="filterActivitiesByUser()"> <!-- Filter to select activities for just a resource -->
                <option <?php echo $_POST["project_resources_filter"] == "" ? "selected" : "" ?>   value=""><?php echo $AppUI->_("All"); ?></option>
                <?php
                foreach ($records as $record) {
                    ?>
                    <option <?php echo $_POST["project_resources_filter"] == $record[1] ? "selected" : "" ?> value="<?php echo $record[1] ?>"> 
                    <?php echo $record[3] ?>
                    </option>
                    <?php
                }
                ?>
            </select>

            <?php
            //verify if the is some activity defined in the entire project
            $q = new DBQuery();
            $q->addQuery("t.task_id");
            $q->addTable("tasks", "t");
            $q->addWhere("t.task_project=" . $project_id);
            $sql = $q->prepare();
            $records = db_loadList($sql);
            $activitiesCount = count($records);
            ?>
            <input type="hidden" name="activities_count" id="activities_count" value="<?php echo $activitiesCount ?>" />
            
            <!-- <input class="button" type="button" value="<?php echo $AppUI->_("LBL_PROJECT_PROJECT_SEQUENCING") ?>" onclick="viewSequenceActivities()" />  -->
        </form>
    </div>
    <br/>

    <div id="estimation_form_error_message"> </div>
    
    <table id="tb_eap" class="tbl" align="center" width="100%" style="border-width: 0px">
        <tr>
            <th style="width:40%" colspan="2">
                <?php echo $AppUI->_("LBL_ACTIVITY"); ?>
            </th>
            <th nowrap>
                <?php echo $AppUI->_("LBL_BEGIN"); ?>
            </th>
            <th nowrap>
                <?php echo $AppUI->_("LBL_END"); ?>
            </th>
            <th nowrap>
                <?php echo $AppUI->_("LBL_DURATION"); ?>
            </th>
            <th nowrap>
                <?php echo $AppUI->_("Human Resources"); ?>
            </th>
            <th nowrap>
    <?php echo $AppUI->_("Status"); ?>
            </th>
        </tr>

        <?php
        $project = new CProject();
        $project->load($project_id);
        $company_id = $project->project_company;
        $showFirstActivityCreation = false; //this variable make the controlling to showing of the message to create the first activity 
        $items = $controllerWBSItem->getWBSItems($project_id);

        if (count($items) >0) {
            $wbs_items_order = 0;
            ?>
            <script>
                var wbsHasActivity = new Array();//stores WBS item id, and a boolean informing if it have or not activities
            </script>
            <?php
            foreach ($items as $item) {
                $id = $item->getId();
                $name = $item->getName();
                $identation = $item->getIdentation();
                $number = $item->getNumber();
                /* verify if wbs item is leaf: above item
                 * 1. Above Item less identation and below more identation
                 * 2. First and last: no above and no below
                 * 3. Last item: no below
                 */

                $i = $wbs_items_order;
                $is_leaf = "0";
                if ($i == 0 && count($items) == 1) { // there is just an item
                    $is_leaf = "1";
                } else if ($i == (count($items) - 1)) { //is the last item
                    $is_leaf = "1";
                } else if (($i > 0 && $i < (count($items) - 1)) && (strlen($items[$i + 1]->getIdentation()) <= strlen($identation))) { // is not the first and not the last and is a leaf
                    $is_leaf = "1";
                }

                $wbs_items_order++;
                $order = $wbs_items_order;        
                if ($is_leaf == "1") {
                    $eapItem = new WBSItemEstimation();
                    $eapItem->load($id);
                    //start: code to filter workpakage activities
                    $tasks = $ControllerWBSItemActivityRelationship->getActivitiesByWorkPackage($id);
                    $hasActivities=sizeof($tasks)>0?true:false;
                    ?>
                    <tr id="row_<?php echo $id ?>"  ondblclick="expandControlWorkpackageActivities(<?php echo $id ?>)" style="cursor:pointer">                       

                        <td style="background-color: #E8E8E8;height:  35px;min-width:50px; " colspan="7">
                            <span id="read_workpackage_id_<?php echo $id ?>">
                                <span id="identation_<?php echo $id ?>" style="color: #E8E8E8"><?php echo $identation; ?></span>
                                <!-- To enlarge the identation space -->
                <?php echo strlen($identation) > 0 ? "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" : ""; ?>
                                <span style="margin-left: 21px" id="div_numbering_<?php echo $id ?>">
                                <?php echo $number ?>
                                </span>
                                <?php echo $name ?>
                                &nbsp;
                                <span style="display:<?php echo $hasActivities?"inline":"none" ?>" >
                                    (<?php echo sizeof($tasks) ?>)
                                    &nbsp;&nbsp;&nbsp;
                                    <img src="./modules/dotproject_plus/images/icone_seta.png" onclick="collapseActivities(<?php echo $id ?>)" id="collapse_icon_<?php echo $id ?>" style="cursor:pointer;display:none" />
                                    <img src="./modules/dotproject_plus/images/icone_seta_cima.png" onclick="expandActivities(<?php echo $id ?>)" id="expand_icon_<?php echo $id ?>" style="cursor:pointer" />
                                </span>
                            </span>
                           
                        </td>
                    <script>
                        wbsHasActivity[<?php echo $id ?>] = <?php echo sizeof($tasks) > 0 ? "true" : "false" ?>;
                    </script>
                    <?php
                    if ($activitiesCount == 0 && !$showFirstActivityCreation) {
                        $showFirstActivityCreation = true;
                    } else {
                        foreach ($tasks as $obj) {
                            $task_id = $obj->task_id;
                            $taskDescription = $obj->task_name;
                            $projectTaskEstimation = new ProjectTaskEstimation();
                            $projectTaskEstimation->load($task_id);

                            
                            //actual dates
                            $activityLog=new ActivityLog();
                            $actualDates=$activityLog->getActivitiesActualDates($task_id);
                            $actualDuration=$activityLog->getActivityActualDuration();
                            $startDateActualTxt = "";
                            $endDateActualTxt = "";
                            if(sizeof($actualDates)==2) {
                               if($actualDates[0]!=""){
                                    $startDateActualTxt  = date("d/m/Y", strtotime($actualDates[0]));
                               }
                               if($actualDates[1]!=""){
                                   $endDateActualTxt = date("d/m/Y", strtotime($actualDates[1]));
                               }
                               
                            }
                            //duration and start/end dates.
                            $obj = new CTask();
                            $obj->load($task_id);
                            $startDateTxt = "";
                            $endDateTxt = "";
                            if (isset($obj->task_start_date) && isset($obj->task_end_date)) {
                                $startDateTxt = date("d/m/Y", strtotime($obj->task_start_date));
                                $endDateTxt = date("d/m/Y", strtotime($obj->task_end_date));
                            }
                            $duration = "";
                            if ($projectTaskEstimation->getDuration() != "") {
                                $duration = "" . $projectTaskEstimation->getDuration() . " " . $AppUI->_("LBL_PROJECT_DAYS_MULT");
                            }

                            if ($taskDescription != "") { //start: build line for task
                                // Get estimate roles if have some
                                $estimatedRolesTxt = "";
                                foreach ($projectTaskEstimation->getRoles() as $role) {
                                    $roleId = $role->getRoleId();
                                    $roleName = $roles[$roleId];
                                    $roleQuantity = $role->getQuantity();
                                    $estimatedRolesTxt .= $roleName . " (" . $roleQuantity . ") <br />";
                                }
                                //metric index is db key
                                $effortMetrics = array();
                                $effortMetrics[0] = $AppUI->_("LBL_EFFORT_HOURS");
                                $effortMetrics[1] = $AppUI->_("LBL_EFFORT_MINUTES");
                                $effortMetrics[2] = $AppUI->_("LBL_EFFORT_DAYS");

                                //Get activity RHs
                                $q = new DBQuery();
                                $q->addQuery("u.user_username,u.user_id");
                                $q->addTable("project_tasks_estimated_roles", "tr");
                                $q->addJoin("human_resource_allocation", "hr_al ", "hr_al.project_tasks_estimated_roles_id=tr.id");
                                $q->addJoin("human_resource", "hr ", " hr_al.human_resource_id=hr.human_resource_id");
                                $q->addJoin("users", "u", " hr.human_resource_user_id=u.user_id");
                                $q->addWhere("tr.task_id=" . $task_id);
                                $sql = $q->prepare();
                                $RHrecords = db_loadList($sql);

                                //Define text for read Human resources
                                $estimatedRolesTxt = "";
                                $rolesNonGrouped = $projectTaskEstimation->getRolesNonGrouped($task_id);
                                $totalRoles = count($rolesNonGrouped);
                                $i = 1; //It avoid the inclusion of a comma in the text to display the human resources
                                if ($_POST["project_resources_filter"] == "") {
                                    $hasFilteredRH = true; //control if will be some filter in based on human resource
                                } else {
                                    $hasFilteredRH = false;
                                }
                                foreach ($rolesNonGrouped as $role) {
                                    $role_estimated_id = $role->getQuantity(); // the quantity field is been used to store the estimated role id
                                    $allocated_hr_id = ""; //Get the allocated HR  (maybe there is just the role without allocation, in this case write the role name)          
                                    //Get id of a possible old allocation to delete it
                                    $q = new DBQuery();
                                    $q->addTable("human_resource_allocation");
                                    $q->addQuery("human_resource_id");
                                    $q->addWhere("project_tasks_estimated_roles_id=" . $role_estimated_id);
                                    $sql = $q->prepare();
                                    $records = db_loadList($sql);
                                    foreach ($records as $record) {
                                        $allocated_hr_id = $record[0];
                                    }
                                    if ($allocated_hr_id != "") {
                                        $estimatedRolesTxt.=$userNameByHRid[$allocated_hr_id]; //write user name
                                    } else {
                                        $estimatedRolesTxt.="<i style='color:red'>" . $roles[$role->getRoleId()] . "</i>";
                                    }
                                    if ($totalRoles > $i) {
                                        $estimatedRolesTxt.=", ";
                                    }
                                    $i++;
                                    if (!$hasFilteredRH && $_POST["project_resources_filter"] == $allocated_hr_id) {
                                        $hasFilteredRH = true;
                                    }
                                }

                                if ($hasFilteredRH) {
                                    $rowId = "wbs_id_" . $id . "_activity_id_" . $obj->task_id;
                                    $rowDetailsId = "activity_details_id_" . $obj->task_id . "_wbs_id_" . $id;
                                    ?>
                                        <tr id="<?php echo $rowId; ?>" style="cursor:pointer;height:  30px">
                                            <td style="width:200px;min-height: 30px;height: 30px;vertical-align: top" colspan="2">
                                                <span style="color:#FFFFFF">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><!-- Identation between eap items and activities -->
                                                <span style="color:#FFFFFF"><?php echo $identation; ?></span>
                                              
                                                <span style="margin-left: 21px" id="activity_code_id_<?php echo $task_id ?>"> A.<?php echo $activitiesIdsForDisplay[$task_id] ?></span>
                                                 
                                                <div id="activity_description_read_id_<?php echo $task_id ?>" style="width:160px;float:right">          
                                                        <?php echo $taskDescription ?>         
                                                </div>
                                                <span id="activity_description_edit_id_<?php echo $task_id ?>" style="display:none;" nowrap="nowrap">
                                                    <input name="activity_description_id_<?php echo $task_id ?>" id="activity_description_id_<?php echo $task_id ?>" class="text" style="width:200px;margin-left: 4px;margin-bottom: 4px;" type="text" value="<?php echo $taskDescription ?>" />
                                                </span>
                                                <br />
                                                
                                                
                                                  <span style="color:#008000">+</span> <span style="font-size: 8px;font-style: italic;cursor:pointer"  id="new_activity_log_link_<?php echo $task_id ?>" onclick="displayActivityLogPanel(<?php echo $task_id; ?>)"><?php echo $AppUI->_("LBL_NEW_ACTVITY_LOG"); ?></span>                                                
                                                <div id="new_activity_log_div_<?php echo $task_id ?>" style="border:1px solid #f0f5f5;width: fit-content;display:none">
                                                    
                                                    <form name="activity_form_<?php echo $obj->task_id ?>" id="activity_form_<?php echo $obj->task_id ?>" method="post" action="?m=dotproject_plus">
                                                        <input name="dosql" type="hidden" value="do_new_activity_log" />
                                                        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
                                                        <input type="hidden" name="activity_id" value="<?php echo $obj->task_id ?>" />
                                                        <input type="hidden" name="task_log_creator" value="<?php echo $AppUI->user_id ?>" />
                                                        <input type="hidden" name="tab" value="<?php echo $_GET["tab"] ?>" />
                                                    
                                                    <div align="center"><b><?php echo $AppUI->_("LBL_NEW_ACTVITY_LOG"); ?></b><br /><br /></div>
                                                    <table >
                                                                                     <tr>
                                                                                        <td style="text-align: center" nowrap="nowrap">
                                                                                            <?php $dateFieldId="task_log_date_" .$task_id ?>
                                                                                            <input type="text" class="text" name="<?php echo $dateFieldId; ?>" id="<?php echo $dateFieldId; ?>" placeholder="dd/mm/yyyy" size="12" maxlength="10" value=""  />                                                                
                                                                                        </td>                           
                                                                                        <td style="text-align: center" nowrap="nowrap"> 
                                                                                            <select name="task_log_hours">
                                                                                                <option value="0.5">0:30</option>
                                                                                                <option value="1">1:00</option>
                                                                                                <option value="1.5">1:30</option>
                                                                                                <option value="2">2:00</option>
                                                                                                <option value="2.5">2:30</option>
                                                                                                <option value="3">3:00</option>
                                                                                                <option value="3.5">3:30</option>
                                                                                                <option value="4">4:00</option>
                                                                                            </select>

                                                                                        </td>
                                                                                    </tr>

                                                                                    <tr>
                                                                                        <td colspan="2">
                                                                                            <textarea style="width: 100%;height:60px;resize: none;" name="task_log_description"></textarea>
                                                                                        </td>    
                                                                                    </tr>
                                                                                    <tr>

                                                                                        <td colspan="2">
                                                                                            <input type="checkbox" value="1" name="activity_concluded" /> <?php echo $AppUI->_("LBL_ACTIVITY_CONCLUDED") ?>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td colspan="2" style="text-align: right">
                                                                                            <br />
                                                                                            <input class="button" type="button" value="<?php echo $AppUI->_("LBL_SAVE"); ?>" onclick="validateActivityLog(<?php echo $task_id ?>);" />
                                                                                            <input class="button" type="button" value="<?php echo ucfirst($AppUI->_("LBL_CANCEL")); ?>" onclick="closeActivityLogPanel(<?php echo $task_id ?>);"  />
                                                                                        </td>
                                                                                    </tr>
                                                                    </table>
                                                    </form>
                                                    
                                                </div>
                                                <br />
                                                  
                                                 <?php
                                                    $activityLog= new ActivityLog();
                                                    $resultSet= $activityLog->getActivityLogs($task_id);
                                                    if(sizeof($resultSet)>0){
                                                        ?>
                                                <br />
                                                <span style="color:#008000">*</span> <span style="font-size: 8px;font-style: italic;cursor:pointer" onclick="displayActivityLogList(<?php echo $task_id; ?>)" ><?php echo $AppUI->_("LBL_ACTIVITY_LOG_LIST") ?> (<?php echo sizeof($resultSet) ?>)</span>
                                                 
                                                <div id="list_activity_logs_<?php echo $task_id?>" style="display:none">
                                                  <table class="tbl"  >
                                                      <tr>
                                                           <th><?php echo $AppUI->_("LBL_DATE") ?></th>
                                                           <th><?php echo $AppUI->_("LBL_DESCRIPTION") ?></th>
                                                           <th><?php echo $AppUI->_("Owner") ?></th>
                                                           <th>&nbsp;</th>
                                                          
                                                      </tr>
                                                        <?php
                                                        foreach($resultSet as $record){
                                                  ?>
                                                     <tr>
                                                         <td>
                                                             <?php echo $record[2] ?>
                                                         </td>
                                                         <td>
                                                             <?php echo $record[1] ?>
                                                         </td>
                                                          <td>
                                                             <?php echo $record[4] ?>&nbsp;<?php echo $record[5] ?>
                                                         </td>
                                                         <td>
                                                             <form name="form_activity_log_delete_<?php echo $record[0] ?>" method="post" action="?m=dotproject_plus">
                                                                <input name="dosql" type="hidden" value="do_delete_activity_log" />
                                                                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
                                                                <input type="hidden" name="activity_id" value="<?php echo $obj->task_id ?>" />
                                                                <input type="hidden" name="tab" value="<?php echo $_GET["tab"] ?>" /> 
                                                                <input type="hidden" name="task_log_id" value="<?php echo $record[0] ?>">
                                                                <img src="./modules/dotproject_plus/images/trash_small.gif" onclick="document.form_activity_log_delete_<?php echo $record[0] ?>.submit();" style="cursor:pointer" />
                                                             </form>
                                                         </td>
                                                      </tr>
                                                  <?php
                                                        }
                                                        ?>
                                                      </table>
                                                    </div>
                                                      <?php
                                                    }
                                                  ?>
                                                  
                                            </td>
                                            <td style="text-align: center; vertical-align: top" nowrap="nowrap"> 
                                                <?php echo $AppUI->_("LBL_PLANNED"); ?><br />
                                                <span id="activity_date_start_read_id_<?php echo $task_id ?>">
                                                    <?php echo $startDateTxt ?> 
                                                </span>
                                                <br /><br />
                                                <?php echo $AppUI->_("LBL_ACTUAL") . "<br />"; ?>
                                                <?php echo $startDateActualTxt; ?> 
                                                
                                                
                                            </td>                           
                                            <td style="text-align: center;vertical-align: top" nowrap="nowrap">
                                                <?php echo $AppUI->_("LBL_PLANNED"); ?><br />
                                                <span id="activity_date_end_read_id_<?php echo $task_id ?>">
                                                    <?php echo $endDateTxt ?>
                                                </span>
                                                <br /><br />
                                                    <?php 
                                                if($obj->task_percent_complete==100){
                                                    echo $AppUI->_("LBL_ACTUAL");
                                                    echo "<br />";
                                                    echo $endDateActualTxt ;
                                                }
                                                ?> 
                                            </td>
                                            <td style="text-align: center;width:100px; vertical-align: top">
                                                <?php echo $AppUI->_("LBL_PLANNED"); ?><br />
                                                <?php echo $duration ?><br /><br />
                                                <?php 
                                                if($actualDuration!=""){
                                                    echo $AppUI->_("LBL_ACTUAL")."<br />";
                                                    echo $actualDuration . " " . $AppUI->_("LBL_PROJECT_DAYS_MULT");
                                                }
                                                ?>
                                            </td>
                                            <td nowrap="nowrap" style="width:200px;vertical-align: top">
                                                <span id="activity_rh_read_id_<?php echo $task_id ?>">
                                                    <?php
                                                    echo $estimatedRolesTxt;
                                                    ?> 
                                                </span>
                                                
                                            </td>
                                            <td style="text-align: center;vertical-align: top;width:100px">
                                                
                                                    <input type="hidden" name="task_percent_complete" value="<?php echo $obj->task_percent_complete ?>" />

                                                    <?php
                                                    $activity_status = "";
                                                    switch ($obj->task_percent_complete) {
                                                        case 0:
                                                            $activity_status = $AppUI->_("LBL_ACTIVITY_STATUS_NOT_INITIATED");
                                                            break;
                                                        case 100:
                                                            $activity_status = $AppUI->_("LBL_ACTIVITY_STATUS_CONCLUDED");
                                                            break;
                                                        default:
                                                            $activity_status = $AppUI->_("LBL_ACTIVITY_STATUS_WORKING_ON_IT");
                                                            ;
                                                            break;
                                                    }
                                                    echo $activity_status;
                                                
                                ?>         
                                            </td>
                                        </tr> 
                                        
                                             
                                    <?php
                                    //end: build line for task
                                }
                            }
                        }
                    }
                    //end: code to filter workpackages activities
                } else {
                    ?>
                    <tr id="row_<?php echo $id ?>" title="non_leaf">

                        <td colspan="7" style="background-color: #E8E8E8;height: 30px">    
                            <span id="read_workpackage_id_<?php echo $id ?>">
                                <span id="identation_<?php echo $id ?>" style="color:#E8E8E8" ><?php echo $identation; ?></span>
                                <!-- To enlarge the identation space -->
                <?php echo strlen($identation) > 0 ? "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" : ""; ?>
                               
                                <span style="margin-left: 21px" id="div_numbering_<?php echo $id ?>">
                <?php echo $number ?>
                                </span>
                <?php echo $name ?>
                            </span>
                             
                        </td>
                    </tr>
                    <?php
                }
            }
        }
        ?>
    </table>

    <br/>
    <?php
}
?>