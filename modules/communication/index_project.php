<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
//limpar a sessão
unset($_SESSION['receptors']);
unset($_SESSION['emitters']);
$project_id = $_GET["project_id"];
?>

<?php
$q = new DBQuery();
$q->addQuery('c.communication_id, c.communication_title, c.communication_information, ch.*, fr.*, p.project_name');
$q->addTable('communication', 'c');
$q->addJoin('communication_channel', 'ch', 'ch.communication_channel_id=c.communication_channel_id');
$q->addJoin('communication_frequency', 'fr', 'fr.communication_frequency_id=c.communication_frequency_id');
$q->addJoin('projects', 'p', 'p.project_id=c.communication_project_id');
$q->addwhere('c.communication_project_id=' . $project_id);
$list = $q->loadList();
$q->clear();

$q = new DBQuery();
$q->addQuery('c.communication_id, co.contact_first_name as emissor_first_name, co.contact_last_name as emissor_last_name');
$q->addTable('communication', 'c');
$q->addJoin('communication_issuing', 'ci', 'ci.communication_id=c.communication_id');
$q->addJoin('initiating_stakeholder', 'st', 'st.initiating_stakeholder_id=ci.communication_stakeholder_id');
$q->addJoin('contacts', 'co', 'co.contact_id=ci.communication_stakeholder_id');
$list_Emissor = $q->loadList();

$q->clear();

$q = new DBQuery();
$q->addQuery('c.communication_id, cor.contact_first_name as receptor_first_name, cor.contact_last_name as receptor_last_name');
$q->addTable('communication', 'c');
$q->addJoin('communication_receptor', 'cr', 'cr.communication_id=c.communication_id');
$q->addJoin('initiating_stakeholder', 'str', 'str.initiating_stakeholder_id=cr.communication_stakeholder_id');
$q->addJoin('contacts', 'cor', 'cor.contact_id=cr.communication_stakeholder_id');
$list_Receptor = $q->loadList();

$q->clear();
?>

<br />
<table width="95%" align="center">
    <tr>
        <td style="width:150px">
            <form action="?m=communication&a=addedit&project_id=<?php echo $project_id; ?>" method="post">
                <input type="submit" class="button" style="font-weight:bold" value="<?php echo ucfirst($AppUI->_("LBL_NEW_COMMUNICATION")) ?>" /> 
            </form>
        </td>
        <td style="width:150px">
            <form action="?m=communication&a=addedit_channel&project_id=<?php echo $project_id; ?>" method="post">
                <input type="submit" class="button" value="<?php echo ucfirst($AppUI->_("LBL_NEW_CCHANNEL")) ?>" />
            </form>
        </td>
        <td style="width: fit-content">
            <form action="?m=communication&a=addedit_frequency&project_id=<?php echo $project_id; ?>" method="post">
                <input type="submit" class="button" value="<?php echo ucfirst($AppUI->_("LBL_NEW_CFREQUENCY")) ?>" /> 
            </form>
        </td>
    </tr>
</table>
<table width="95%" align="center" border="0" cellpadding="2" cellspacing="1" class="tbl">
    <tr>
        <!--<th nowrap="nowrap"><?php echo $AppUI->_("LBL_PROJECT"); ?></th>-->
        <th nowrap="nowrap"><?php echo $AppUI->_("LBL_TITLE"); ?></th>
        <!-- <th nowrap="nowrap"><//?php echo $AppUI->_('Issuing');?></th> -->
        <!--<th nowrap="nowrap"><//?php echo $AppUI->_('Receptor');?></th>-->
        <th nowrap="nowrap"><?php echo $AppUI->_("LBL_COMMUNICATION"); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_("LBL_CHANNEL"); ?></th>
        <th nowrap="nowrap"><?php echo $AppUI->_("LBL_FREQUENCY"); ?></th>        
        <th nowrap="nowrap"> </th>
    </tr>

    <?php foreach ($list as $row) { ?>
        <tr>
           <!-- <td><?php echo $row['project_name'] ?></td>-->
            <td><?php echo $row['communication_title'] ?></td>
            <!--<td>
            <//?php foreach($list_Emissor as $emissor){
                if($emissor['communication_id']==$row['communication_id'])
                {echo $emissor['emissor_first_name'].' '.$emissor['emissor_last_name'].' - '; }
            }?> 
            </td>
        
            <td>
            <//?php foreach($list_Receptor as $receptor){
                if($receptor['communication_id']==$row['communication_id'])
                {echo $receptor['receptor_first_name'].' '.$receptor['receptor_last_name'].' - '; }
            }?>
            </td> -->      
            <td><?php echo $row['communication_information'] ?></td>
            <td><?php echo $row['communication_channel'] ?></td>
            <td><?php echo $row['communication_frequency'] ?></td>
            <td>
                <a href="index.php?m=communication&a=addedit&communication_id=<?php echo $row['communication_id'] ?>&project_id=<?php echo $project_id; ?>">
                    <img src="modules/communication/images/stock_edit-16.png" alt="<?php echo $AppUI->_("LBL_EDIT") ?>" border="0" />
                </a>
            </td>
        </tr>
    <?php } ?>
</table>
