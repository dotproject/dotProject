<?php
/**
 * This page was designed to display which feedback messages already have being read by a certain user.
 */

if(!isset($_GET["user_id"])){
  die("This page may not be accessed without parameters");   
}
require_once (DP_BASE_DIR . "/modules/projects/projects.class.php");
require_once (DP_BASE_DIR . "/modules/admin/admin.class.php");
require_once (DP_BASE_DIR . "/modules/instructor_admin/class.class.php");
$userId=$_GET["user_id"];
$projectId= $_GET["project_id"];
$classId=$_GET["class_id"];
$project= new CProject();
$user = new CUser();
$class= new CClass();
$project->load($projectId);
$user->load($userId);
$class->load($classId);
$feedbackManager=new InstructionalFeebackManager();
$readFeeback=$feedbackManager->getUserReadFeedback($userId);
$kaList=$feedbackManager->getKaList();
?>
<style>
    .feedback_report td{
         vertical-align: top;
         border-spacing: 15px;
    }            
</style>

<a href="index.php?m=instructor_admin"><?php echo $AppUI->_("Instructor Admin") ?> </a> > <a href="index.php?m=instructor_admin&a=addedit&class_id=<?php echo $class->class_id ?>"><?php echo $class->toString(); ?></a>
<br /><br />

<p style="text-align: center">
    <?php echo $AppUI->_("LBL_FEEDBACK_REPORT") ?>
    <br />
    <b><?php echo $AppUI->_("LBL_PROJECT"); ?>: </b>
    <?php echo $project->project_name; ?>
    | 
    <b><?php echo $AppUI->_("LBL_LOGIN"); ?>: </b> 
    <?php echo $user->user_username; ?>
</p>

<?php
foreach ($kaList as $ka) {
    $feedbackListKa = $feedbackManager->getAllFeedbackPerKnoledgeArea($ka);
    ?>

        <br />
        <br />
        <table class="feedback_report tbl" >
        <tr>
            <th colspan="5">
    <?php echo $ka ?>
            </th>
        </tr>
        <tr>
            <th width="8%"><?php echo $AppUI->_("Id"); ?></th>
            <th width="32%"><?php echo $AppUI->_("LBL_FEEDBACK_SHORT_FORMAT"); ?></th>
            <th width="40%"><?php echo $AppUI->_("Feedback"); ?></th>
            <th width="10%"><?php echo $AppUI->_("LBL_TRIGGER"); ?></th>
            <th width="10%"><?php echo $AppUI->_("LBL_FEEDBACK_READ_ON"); ?></th>
        </tr>
        <?php
        foreach ($feedbackListKa as $feedback) {
            $id = $feedback->getId();
            $short = $feedback->getShort();
            $description = $feedback->getDescription();
            $wasTriggered= $feedback->getIsTriggerFiredForCurrentUser();
            $wasRead = isset($readFeeback[$id])?$readFeeback[$id]:"-----";
            ?>
            <tr>
                
                <td><?php echo $id ?></td>
                <td><?php echo $short ?></td>
                <td><?php echo $description ?></td>
                <td style="text-align: center;<?php echo $wasTriggered?"background-color:#ff9999":"" ?>">
                    <?php echo $AppUI->_($wasTriggered?"LBL_YES":"LBL_NO") ?>
                </td>
                <td style="text-align: center"><?php echo $wasRead ?></td>
                
            </tr>
        <?php
    }
    ?>
    </table>        
    <?php
}
?>
