<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
require_once (DP_BASE_DIR . "/modules/instructor_admin/class.class.php");
$AppUI->savePlace();
$class_id = "";
$class = new CClass();
if (isset($_GET["class_id"])) {
    $class_id = $_GET["class_id"];
    $class->load($class_id);
}
?>

<!-- include libraries for right click menu -->
<script type="text/javascript" src="./modules/dotproject_plus/milonic_right_click_menu/milonic_src.js"></script> 
<script type="text/javascript" src="./modules/dotproject_plus/milonic_right_click_menu/mmenudom.js"></script> 
<script type="text/javascript" src="./modules/dotproject_plus/milonic_right_click_menu/contextmenu.js"></script> 
<script type="text/javascript" src="./modules/dotproject_plus/milonic_right_click_menu/menu_data_activities.js"></script> 
<a href="http://www.milonic.com/" style="display: none">DHTML JavaScript Menu By Milonic.com</a>
<!-- include libraries for lightweight messages -->
<link type="text/css" rel="stylesheet" href="./modules/timeplanning/js/jsLibraries/alertify/alertify.css" media="screen"></link>
<script type="text/javascript" src="./modules/timeplanning/js/jsLibraries/alertify/alertify.js"></script>


<script>

    function rightClickMenuExcludeGroup() {
        if (contextObject.parentNode.id.indexOf("group_id_") != -1 || contextObject.parentNode.parentNode.id.indexOf("group_id_") != -1) {
            alertify.confirm("<?php echo $AppUI->_("LBL_DELETE_MSG") ?>", function () {
                var parentId = contextObject.parentNode.id.indexOf("group_id_") != -1 ? contextObject.parentNode.id : contextObject.parentNode.parentNode.id;
                var groupId = parentId.split("group_id_")[1];
                document.group_deletion_form.group_id_for_deletion.value = groupId;
                document.group_deletion_form.submit();
            });
        }
    }

    with (milonic = new menuname("contextMenu")) {
        margin = 9;
        style = contextStyle;
        top = "offset=5";
        aI("image=./modules/dotproject_plus/images/trash_small.gif;text=<?php echo $AppUI->_("LBL_EXCLUSION"); ?>;url=javascript:rightClickMenuExcludeGroup();");
    }

    drawMenus();

    function validateForm() {
        var isValid = true;
        if (document.class.educational_institution.value === "") {
            isValid = false;
        }
        if (document.class.course.value === "") {
            isValid = false;
        }
        if (document.class.instructor.value === "") {
            isValid = false;
        }
        if (document.class.disciplin.value === "") {
            isValid = false;
        }
        if (!isValid) {
            setAppMessage("<?php echo $AppUI->_("LBL_FORM_VALIDATION") ?>", APP_MESSAGE_TYPE_WARNING);
        }
        return isValid;
    }
</script>
<a href="index.php?m=instructor_admin"><?php echo $AppUI->_("Instructor Admin") ?> </a> 
<br /><br />
<fieldset>
    <legend><b><?php echo $AppUI->_("LBL_CLASS_DATA"); ?></b></legend>
    <form name="class" action="?m=instructor_admin" method="post" onsubmit="return validateForm()">
        <input type="hidden" name="dosql" value="do_register_class" />	
        <input type="hidden" name="class_id" value="<?php echo $class->class_id ?>" />

        <table class="std" >
            <tr>
                <td><?php echo $AppUI->_("LBL_EDUCATIONAL_INSTITUTION"); ?><span style="color:red">*</span></td>
                <td><input style="width:300px" type="text" value="<?php echo $class->educational_institution ?>" name="educational_institution" /></td>
            </tr>
            <tr>
                <td><?php echo $AppUI->_("LBL_COURSE"); ?><span style="color:red">*</span></td>
                <td><input style="width:300px" type="text" value="<?php echo $class->course ?>" name="course" /></td>
            </tr>
            <tr>
                <td><?php echo $AppUI->_("LBL_DISCIPLIN"); ?><span style="color:red">*</span></td>
                <td><input style="width:300px" type="text" value="<?php echo $class->disciplin ?>" name="disciplin" /></td>
            </tr>
            <tr>
                <td><?php echo $AppUI->_("LBL_INSTRUCTOR"); ?><span style="color:red">*</span></td>
                <td><input style="width:300px" type="text" value="<?php echo $class->instructor ?>" name="instructor" /></td>
            </tr>
            <tr>
                <td><?php echo $AppUI->_("LBL_NUMBER_OF_STUDENTS"); ?></td>
                <td><input style="width:300px" type="text" value="<?php echo $class->number_of_students ?>" name="number_of_students" /></td>
            </tr>
            <tr>
                <td colspan="2" style="font-weight: bold"><?php echo $AppUI->_("LBL_LECTIVE_PERIOD"); ?></td>
            </tr>
            <tr>
                <td><?php echo $AppUI->_("LBL_YEAR"); ?></td>
                <td>
                    <select name="year">
                        <?php
                        $year = date("Y");
                        for ($i = 2012; $i <= $year + 1; $i++) {
                            ?>
                            <option value="<?php echo $i ?>" <?php echo $class->year == $i ? "selected" : "" ?>  <?php echo ($year == $i && $class->year == 0) ? "selected" : "" ?>>
                                <?php echo $i ?>
                            </option>
                            <?php
                        }
                        ?>
                    </select>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php echo $AppUI->_("LBL_SEMESTER"); ?>
                    <select name="semester">
                        <option value="1" default <?php echo $class->semester == 1 ? "selected" : "" ?> >1</option>
                        <option value="2" default <?php echo $class->semester == 2 ? "selected" : "" ?>>2</option>
                    </select>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input  type="submit" value="<?php echo $AppUI->_("LBL_REGISTER_CLASS"); ?>" />
                </td>

            </tr>
        </table>
    </form>
</fieldset>
<br />
<?php
if (isset($_GET["class_id"])) {
    ?>
    <fieldset>
        <legend><b><?php echo $AppUI->_("LBL_ADD_NEW_GROUPS_FOR_THIS_CLASS"); ?></b> </legend>
        <br /><br />
        <form action="?m=instructor_admin" method="post">

            <input type="hidden" name="dosql" value="do_groups_creation" />
            <input type="hidden" name="class_id" value="<?php echo $class->class_id ?>" />
            <label for="number_of_groups"><?php echo $AppUI->_("LBL_NUMBER_OF_GROUPS"); ?></label>
            <input type="number" name="number_of_groups" id="number_of_groups" /> 
            <input type="submit" value="<?php echo $AppUI->_("LBL_CREATE_GROUPS_ACCOUNTS"); ?>" />
            <br /><br />
            <span style="color:red">*</span> <?php echo$AppUI->_("LBL_MAXIMUM_NUMBER_OF_GROUPS") ?>
        </form>
    </fieldset>
    <!-- Hidden form to handle group deletion -->
    <form action="?m=instructor_admin" method="post" name="group_deletion_form">
        <input type="hidden" name="dosql" value="do_group_deletion" />
        <input type="hidden" name="class_id" value="<?php echo $class->class_id ?>" />
        <input type="hidden" name="group_id_for_deletion" value="" />
    </form>

    <br />
    <fieldset>
        <legend><?php echo $AppUI->_("LBL_USERS_FOR_THIS_CLASS"); ?></legend>
        <br />
        <table class="tbl" width="100%" cellpadding="5" >
            <tr>
                <th><?php echo $AppUI->_("LBL_LOGIN") ?></th>
                <th><?php echo $AppUI->_("LBL_PASSWORD") ?></th>
                <th>Link</th>
                <th><?php echo $AppUI->_("LBL_PROJECT_CHARTER") ?></th>
                <th><?php echo $AppUI->_("LBL_PROJECT_PLAN") ?></th>           
                <th><?php echo $AppUI->_("LBL_AUTO_EVALUATION") ?></th>
                <th>Feedback</th>
            </tr>
            <?php
            $q = new DBQuery();
            $q->addTable("dpp_classes_users");
            $q->addQuery("user_login,user_password,user_company,user_id");
            $q->addWhere("class_id=" . $class_id);
            $sql = $q->prepare();
            //echo $sql;
            $records = db_loadList($sql);
            require_once DP_BASE_DIR . "/modules/initiating/initiating.class.php";

            foreach ($records as $record) {
                $login = $record[0];
                $password = $record[1];
                $companyId = $record[2];
                $userId = $record[3];
                //get project and initiating Ids
                $initiating_id = -1;
                $project_id = -1;
                $q = new DBQuery();
                $q->addTable("projects");
                $q->addQuery("project_id");
                $q->addWhere("project_company=" . $companyId);
                $sql = $q->prepare();
                //echo $sql;
                $projects = db_loadList($sql);
                foreach ($projects as $project) {
                    $project_id = $project["project_id"];
                    $initiating = CInitiating::findByProjectId($project_id);
                    if (!is_null($initiating)) {
                        $initiating_id = $initiating->initiating_id;
                    }
                }
                ?>
                <tr id="group_id_<?php echo $userId ?>" onmouseover="setContextDisabled(false)" onmouseout="setContextDisabled(true)">
                    <td><?php echo $login; ?></td>
                    <td><?php echo $password; ?></td>
                    <td align="center">
                        <a href="index.php?m=companies&a=view&company_id=<?php echo $companyId; ?>">
                            <?php echo $AppUI->_("LBL_ACCESS_GROUP_PROJECT") ?>
                        </a>
                    </td>
                    <td align="center">
                        <?php
                        if ($initiating_id != -1) {
                            ?>
                            <a href='?m=initiating&a=pdf&id=<?php echo $initiating_id ?>&suppressHeaders=1'>
                                <img src="./modules/instructor_admin/images/pdf_icon.png" />
                            </a>
                            <?php
                        } else {
                            ?>
                            <img src="./modules/instructor_admin/images/pdf_icon_disable.png" />
                            <?php
                        }
                        ?>
                    </td>
                    <td align="center">
                        <?php
                        if ($project_id != -1) {
                            ?>
                            <a href = "modules/timeplanning/view/export_project_plan/export.php?project_id=<?php echo $project_id; ?>&print=0" target = "_blank" style="font-weight: bold">
                                <img src="./modules/instructor_admin/images/pdf_icon.png" />
                            </a>
                            <?php
                        } else {
                            ?>
                            <img src="./modules/instructor_admin/images/pdf_icon_disable.png" />
                            <?php
                        }
                        ?>
                    </td>


                    <td align="center">


                        <?php
                        if ($project_id != -1) {
                            ?>
                            <a href="index.php?m=instructor_admin&a=do_auto_evaluation&user_id=<?php echo $userId; ?>&project_id=<?php echo $project_id ?>&class_id=<?php echo $class_id ?>">
                                <img src="./modules/instructor_admin/images/word_icon.png" />
                            </a>
                            <?php
                        } else {
                            ?>
                            <img src="./modules/instructor_admin/images/word_icon_disable.png" />
                            <?php
                        }
                        ?>
                        </a>
                    </td>
                    <td align="center">
                        <a href="index.php?m=instructor_admin&a=view_read_feedback&user_id=<?php echo $userId; ?>&project_id=<?php echo $project_id ?>&class_id=<?php echo $class_id ?>">
                            <?php echo $AppUI->_("LBL_VIEW_READ_FEEDBACK") ?>
                        </a>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>

        <br />
        <span style="color:#f00">*</span> <?php echo $AppUI->_("LBL_RIGHT_CLICK_TO_ACTIONS") ?>
    </fieldset>
    <?php
}
?>
