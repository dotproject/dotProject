<?php
/**
 * Scope Planning Module
 * @author Danilo Felicio Jr danilofjr@hotmail.com
 * May/2013
 */
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

$project_name = dPgetParam($_GET, 'project_name', 0);
$project_id = dPgetParam( $_GET, 'project_id', 0 );

$titleBlock = new CTitleBlock('LBL_SP_EDIT_WBSDIC', 'scope.png', $m, "$m.$a");
$titleBlock->addCrumb("?m=$m", "LBL_SP_SCOPEPLANNING");
$href = "?m=$m";
$titleBlock->show();

require_once($AppUI->getModuleClass('timeplanning'));

$projects = new DBQuery();
if ($project_id == null) {
    $projects->addQuery('project_id, project_name');
    $projects->addTable('projects');
    $projects = $projects->loadHashList();
} else {
    $projects->addQuery('project_id, project_name');
    $projects->addTable('projects', 'p');
    $projects->addWhere('project_id = ' . $project_id);
    $projects->addOrder('project_id');
    $projects = $projects->loadHashList();
}

?>

<table align="left" border="0" cellpadding="10" cellspacing="5" style="width: 100%" class="std" name="threads" charset=UTF-8>    
    <tr align="left"><td align="left" width="55px"><?php echo $AppUI->_("LBL_SP_PROJECT"); ?>:</td>
        <td>
            <?php
            echo arraySelect($projects, 'project_id', 'size="1" class="text"', (@$obj->project_id ? dPformSafe(@$obj->project_id) : $project_id));
            ?>
        </td>
    </tr>
    <tr>
        <td colspan="2"><?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/projects_wbs_dictionary.php"); ?></td>
    </tr>
    <tr>
        <td><input name="btn_back" class="button" type="button" value="<?php echo $AppUI->_('LBL_SP_RETURN'); ?>"
                   onclick="{location.href = './index.php?m=scopeplanning';}"/></td>
        <td></td>
    </tr>    
</table>
