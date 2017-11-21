<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
require_once (DP_BASE_DIR . "/modules/instructor_admin/class.class.php");
$AppUI->savePlace();
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

    function rightClickMenuViewClass() {
        if (contextObject.parentNode.id.indexOf("class_id_") != -1 || contextObject.parentNode.parentNode.id.indexOf("class_id_") != -1) {
            var parentId = contextObject.parentNode.id.indexOf("class_id_") != -1 ? contextObject.parentNode.id : contextObject.parentNode.parentNode.id;
            var classId = parentId.split("class_id_")[1];
            window.location = "?m=instructor_admin&a=addedit&class_id=" + classId;
        }
    }

    function rightClickMenuDeleteClass() {
        if (contextObject.parentNode.id.indexOf("class_id_") != -1 || contextObject.parentNode.parentNode.id.indexOf("class_id_") != -1) {
            alertify.confirm("<?php echo $AppUI->_("LBL_DELETE_MSG") ?>", function () {

                var parentId = contextObject.parentNode.id.indexOf("class_id_") != -1 ? contextObject.parentNode.id : contextObject.parentNode.parentNode.id;
                var classId = parentId.split("class_id_")[1];
                document.delete_class.class_id.value = classId;
                document.delete_class.submit();
            });
        }
    }

    function rightClickMenuPrintCredentials() {
        if (contextObject.parentNode.id.indexOf("class_id_") != -1 || contextObject.parentNode.parentNode.id.indexOf("class_id_") != -1) {
            var parentId = contextObject.parentNode.id.indexOf("class_id_") != -1 ? contextObject.parentNode.id : contextObject.parentNode.parentNode.id;
            var classId = parentId.split("class_id_")[1];
            window.location = "?m=instructor_admin&a=print_credentials&class_id=" + classId;
        }
    }

    with (milonic = new menuname("contextMenu")) {
        margin = 9;
        style = contextStyle;
        top = "offset=5";
        aI("image=./images/icons/stock_edit-16.png;text=<?php echo $AppUI->_("LBL_VIEW") ?>;url=javascript:rightClickMenuViewClass();");
        aI("image=./images/icons/stock_print-16.png;text=<?php echo $AppUI->_("LBL_PRINT_CREDENTIALS"); ?>;url=javascript:rightClickMenuPrintCredentials();");
        aI("image=./modules/dotproject_plus/images/trash_small.gif;text=<?php echo $AppUI->_("LBL_DELETE_CLASS"); ?>;url=javascript:rightClickMenuDeleteClass();");
    }

    drawMenus();
</script>

<div style="display:none">
    <form name="delete_class" action="?m=instructor_admin" method="post">
        <input type="hidden" name="dosql" value="do_delete_class" />	
        <input type="hidden" name="class_id" value="" />
    </form>
</div>    


<form action="?m=instructor_admin&a=addedit" method="post">
    <div style="float:left">
        <input type="submit" value="<?php echo $AppUI->_("LBL_ADD_NEW_CLASS"); ?>" />
    </div>
    <div style="float:right">
        <a href="index.php?m=instructor_admin&a=view_student_feedback_evaluation">
            <input type="button" value="<?php echo $AppUI->_("LBL_VIEW_FEEDBACK_EVALUATIONS"); ?>" style="cursor:pointer " />
        </a> 
    </div>
</form>

<br />
<br />
<table class="tbl" style="width:100%" cellpadding="5" >
    <summary style="line-height: 150%;font-weight: bold">
        <?php echo $AppUI->_("LBL_CLASSES_USING_DPP"); ?>
    </summary>
    <tr>
        <th><?php echo $AppUI->_("LBL_YEAR"); ?></th>
        <th><?php echo $AppUI->_("LBL_SEMESTER"); ?></th>
        <th><?php echo $AppUI->_("LBL_EDUCATIONAL_INSTITUTION"); ?></th>
        <th><?php echo $AppUI->_("LBL_COURSE"); ?></th>
        <th><?php echo $AppUI->_("LBL_DISCIPLIN"); ?></th>
        <th><?php echo $AppUI->_("LBL_INSTRUCTOR"); ?></th>
        <th><?php echo $AppUI->_("LBL_NUMBER_OF_STUDENTS"); ?></th>
    </tr>
    <?php
    $classes = CClass::getAllClasses();
    foreach ($classes as $class) {
        ?>        

        <tr id="class_id_<?php echo $class->class_id ?>" onmouseover="setContextDisabled(false)" onmouseout="setContextDisabled(true)">
            <td>
                <?php echo $class->year ?>
            </td>
            <td style="text-align: center">
                <?php echo $class->semester ?>
            </td>
            <td> 
                <?php echo $class->educational_institution ?>
            </td>
            <td> 
                <?php echo $class->course ?>
            </td>
            <td> 
                <?php echo $class->disciplin ?>
            </td>
            <td>   
                <?php echo $class->instructor ?>
            </td>
            <td style="text-align: center">
                <?php echo $class->number_of_students ?>
            </td>

        </tr>
        <?php
    }
    ?>
</table>
<br />
<span style="color:#f00">*</span> <?php echo $AppUI->_("LBL_RIGHT_CLICK_TO_ACTIONS") ?>