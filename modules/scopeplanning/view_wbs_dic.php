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
$project_id = dPgetParam($_GET, 'project_id', 0);

$titleBlock = new CTitleBlock('LBL_SP_WBS_DICTIONARY', 'scope.png', $m, "$m.$a");
$titleBlock->addCrumb("?m=$m", "LBL_SP_SCOPEPLANNING");
$href = "?m=$m";
$titleBlock->show();

require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_items.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/model/wbs_dictionary_entry.class.php");
$controllerWBSItem = new ControllerWBSItem();
?>

<table align="left" border="0" cellpadding="10" cellspacing="5" style="width: 100%" class="std" name="threads" charset=UTF-8>    
    <tr>
        <td align="left" width="50"><?php echo $AppUI->_('LBL_SP_PROJECT'); ?>:</td>
        <td align="left" ><?php echo $project_name ?></td>
    </tr>
    <tr>
        <td align="center" colspan="2">
            <table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
                <tr>
                    <th align="left" width="400" nowrap="nowrap"><?php echo $AppUI->_('LBL_SP_WBSITEM'); ?></th>
                    <th align="left" nowrap="nowrap"><?php echo $AppUI->_('LBL_SP_DESCRIPTION'); ?></th>        
                </tr>

                <?php
                $items = $controllerWBSItem->getWBSItems($project_id);
                foreach ($items as $item) {
                    ?>
                    <tr>
                        <td align="left">
                            <?php echo $item->getIdentation(); ?>
                            <?php echo $item->getNumber(); ?>
                            <?php echo $item->getName(); ?>
                            <?php echo $item->isLeaf() == 1 ? "*" : ""; ?>
                        </td>                
                        <td align="left">
                            <?php
                            $obj = new WBSDictionaryEntry();
                            $obj->load($item->getId());
                            ?>
                            <?php echo $obj->getDescription(); ?>                
                        </td>            
                    </tr>
                <?php }
                ?>
            </table>

        </td>
    </tr>
    <tr>
        <td><input name="btn_back" class="button" type="button" value="<?php echo $AppUI->_('LBL_SP_RETURN'); ?>"
                   onclick="{location.href = './index.php?m=scopeplanning';}"/></td>
        <td></td>
    </tr>    
</table>