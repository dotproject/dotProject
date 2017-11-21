<!-- include libraries for right click menu -->
<script type="text/javascript" src="./modules/dotproject_plus/milonic_right_click_menu/milonic_src.js"></script> 
<script type="text/javascript" src="./modules/dotproject_plus/milonic_right_click_menu/mmenudom.js"></script> 
<script type="text/javascript" src="./modules/dotproject_plus/milonic_right_click_menu/menu_data_projects.js"></script>
<script type="text/javascript" src="./modules/dotproject_plus/milonic_right_click_menu/contextmenu.js"></script>
<a href="http://www.milonic.com/" style="display: none">DHTML JavaScript Menu By Milonic.com</a>
<script>
with (milonic = new menuname("contextMenu")) {
    margin = 3;
    style = contextStyle;
    top = "offset=5";
    aI("image=./images/icons/stock_ok-16.png;text=<?php echo $AppUI->_("LBL_MENU_ADD_PROJECT") ?>;url=javascript:rightClickMenuAddProject();");
    aI("image=./images/icons/stock_edit-16.png;text=<?php echo $AppUI->_("LBL_MENU_EDIT_PROJECT") ?>;url=javascript:rightClickMenuEditProject();");
}
drawMenus();


</script>
<?php
if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}

//Get global variables
include_once ($AppUI->getModuleClass('projects'));
session_start();
$AppUI->savePlace();
global $AppUI, $company_id, $pstatus, $dPconfig;
$df = $AppUI->getPref('SHDATEFORMAT');
?>

<script>
    function rightClickMenuAddProject(){
        window.location="index.php?m=projects&a=addedit&company_id=<?php echo $company_id; ?>";
    }
    
    function rightClickMenuEditProject(){
        if (contextObject.tagName=="TD" && contextObject.parentNode.tagName=="TR"){
            var parentId=contextObject.parentNode.id;
            if (parentId.indexOf("project_id_")!=-1){
                var project_id=parentId.split("project_id_")[1];
                window.location="index.php?m=projects&a=addedit&project_id="+project_id;
            }
        }
    }
</script>

<?php
//Retrieve data to be presented
$q = new DBQuery();
$q->addTable('projects', 'prj');
$q->addQuery('project_id, project_name, project_start_date, project_status, project_target_budget, project_end_date, project_priority, contact_first_name, contact_last_name, project_percent_complete');
$q->addJoin('users', 'u', 'u.user_id = prj.project_owner');
$q->addJoin('contacts', 'con', 'u.user_contact = con.contact_id');
$q->addWhere('prj.project_company = ' . $company_id);
$projObj = new CProject();
$projList = $projObj->getDeniedRecords($AppUI->user_id);
if (count($projList)) {
    $q->addWhere('NOT (project_id IN (' . implode(',', $projList) . '))');
}
$q->addWhere('prj.project_status <> 7');
$q->addOrder($sort);


//Draw screen
   
if (!($rows = $q->loadList())) {
    ?>
    <div style="text-align: center">
        <br />
        <?php echo $AppUI->_("LBL_THERE_IS_NO_PROJECT") ?>
        <?php echo $AppUI->_("LBL_CLICK"); ?> <a href="index.php?m=projects&a=addedit&company_id=<?php echo $company_id ?>"><b><u><?php echo $AppUI->_("LBL_HERE"); ?></u></b></a> <?php echo $AppUI->_("to create a project"); ?>.
    </div>
    <br />
    <?php
   // $s .= '<tr><td>' . $AppUI->_('No data available') . '<br />' . $AppUI->getMsg() . '</td></tr>';
} else {
    ?>
    <br />
    <span>
        * <?php echo $AppUI->_("LBL_RIGHT_CLICK_CREATE_PROJECT"); ?><br />
    </span>
    
    <table class="tbl" width="100%" align="center" onmouseover="setContextDisabled(false)" onmouseout="setContextDisabled(true)">   
        <tr>
            <th> % <?php echo $AppUI->_("LBL_COMPLETE"); ?></th>
            <th> <?php echo $AppUI->_("Project"); ?> </th>
            <th> <?php echo $AppUI->_("LBL_BEGIN"); ?> </th>
            <th> <?php echo $AppUI->_("LBL_END"); ?> </th>
            <th> <?php echo $AppUI->_("LBL_OWNER"); ?> </th>
            <th> Status </th>
        </tr>
        <?php
        foreach ($rows as $row) {
            $project_id=$row["project_id"];
            $start_date = new CDate($row["project_start_date"]);
            $start_date_formated = $start_date->format($df);
            $end_date = new CDate($row["project_end_date"]);
            $end_date_formated = $end_date->format($df);
            $project_name = htmlspecialchars($row["project_name"]);
            $responsible = htmlspecialchars($row["contact_first_name"]) . ' ' . htmlspecialchars($row['contact_last_name']);
            $status = $AppUI->_($pstatus[$row["project_status"]]);
            $project_percent_complete = $row["project_percent_complete"]
            ?>
            <tr id="project_id_<?php echo $project_id?>">
                <td style="text-align: center">
                    <?php echo $project_percent_complete ?> %
                </td>
                <td>
                    <a href="index.php?m=projects&a=view&project_id=<?php echo $project_id; ?>">
                        <?php echo $project_name ?>
                    </a>
                </td>
                <td style="text-align: center">
                    <?php echo $start_date_formated ?>
                </td>
                <td style="text-align: center">
                    <?php echo $end_date_formated ?>
                </td>
                <td style="text-align: center">
                    <?php echo $responsible ?>
                </td>
                <td style="text-align: center">
                    <?php echo $status ?>
                </td>
            </tr>

            <?php
        }
        ?>
            </table>
    <?php
    }
    ?>

