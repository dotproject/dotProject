<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_baseline.class.php");
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_util.class.php");

$titulo = $AppUI->_('LBL_EDITAR') . " " . $AppUI->_('LBL_BASELINE');
$titleBlock = new CTitleBlock("$titulo", 'graph-up.png', $m, $m . '.' . $a);
$titleBlock->show();

$AppUI->savePlace();
$idBaseline = dPgetParam($_GET, 'idBaseline', 0);
$project_id = dPgetParam($_GET, 'project_id', 0);

$controllerBaseline = new ControllerBaseline();
$controllerUtil = new ControllerUtil();
$record = $controllerBaseline->getBaselineRequestById($idBaseline);
?>
<script type="text/javascript" src="./modules/monitoringandcontrol/js/ata.js"   ></script>
<!--  calendar  -->
<link type="text/css" rel="stylesheet" href="./modules/monitoringandcontrol/js/jsLibraries/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css" media="screen" />
<script type="text/javascript" src="./modules/monitoringandcontrol/js/jsLibraries/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js"   ></script>
<!-- end calendar  -->  
<table width="100%" cellspacing="1" cellpadding="1" border="0">
    <tr>
        <td align="left" colspan="8"><b>  <?php echo $AppUI->_('LBL_MONITORACAO'); ?> : <?php echo $AppUI->_('LBL_BASELINE'); ?> </b></td>

    </tr>
</table>

<form action="?m=monitoringandcontrol&a=do_baseline_aed&project_id=<?php echo $project_id; ?>" method="post" name="form_baseline" id="form_form_baseline" enctype="multipart/form-data">

    <input name="dosql" type="hidden" value="do_baseline_aed" />
    <input name="project_id" type="hidden" id="project_id" value="<?php echo $project_id; ?>">
    <input type="hidden" name="idBaseline"  id="idBaseline" value="<?php echo $idBaseline; ?>">  
    <input  type="hidden" name="acao" value="update"  />

    <br/>

    <table class="std" width="100%" cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td colspan="8">&nbsp;</td>
        </tr>
        <tr>
            <td align="right"><?php echo $AppUI->_('LBL_PROJETO'); ?>:</td>
            <td>
                <?php $project_name = $controllerUtil->getProjectName($project_id); ?>
                <a href="?m=projects&a=view&project_id=<?php echo $project_id; ?>" target="blank"><?php echo $project_name[0][0] ?></a>					

            </td>
        </tr>
        <tr>
        <tr>
            <td align="right"><?php echo $AppUI->_('LBL_NOME'); ?>:</td>
            <td>
                <input type="text" name="nmBaseline" size="25" id="nmBaseline" value='<?php echo $record[0]['baseline_name']; ?>'  />					
            </td>
        </tr>
        <tr>
        <tr>
            <td align="right"><?php echo $AppUI->_('LBL_VERSAO'); ?>:</td>
            <td>
                <input type="text" name="nmVersao" size="25" id="nmVersao" value='<?php echo $record[0]['baseline_version']; ?>' />					
            </td>
        </tr>
        <tr>
            <td align="right"><?php echo $AppUI->_('LBL_OBSERVACAO'); ?>:</td>
            <td>
                <textarea  name="dsObservacao" cols="106" rows="4"><?php echo $record[0]['baseline_observation']; ?></textarea>					
            </td>
        </tr>		
        <tr>	  
        <tr>
            <td colspan="8">
                <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/subform_back_button_project.php"); ?>
            </td>
            <td align="right"><input type="submit" value="<?php echo $AppUI->_('Gravar'); ?>" class="button"  /></td>

        </tr>
    </table>
