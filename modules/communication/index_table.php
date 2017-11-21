<?php

$q = new DBQuery();
$q->addQuery('c.communication_id, c.communication_title, c.communication_information, ch.*, fr.*, p.project_name');
$q->addTable('communication', 'c');
$q->addJoin('communication_channel', 'ch', 'ch.communication_channel_id=c.communication_channel_id');
$q->addJoin('communication_frequency', 'fr', 'fr.communication_frequency_id=c.communication_frequency_id');
$q->addJoin('projects', 'p', 'p.project_id=c.communication_project_id');
if(isset($_POST['project_id']) && $_POST['project_id'] != '0'){    
    $q->addwhere('c.communication_project_id='.$_POST['project_id']);
    $list = $q->loadList();
}else{
    $q->setLimit(100);
    $list = $q->loadList();
}

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

<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<tr>
    <th nowrap="nowrap"><?php echo $AppUI->_("LBL_PROJECT");?></th>
    <th nowrap="nowrap"><?php echo $AppUI->_("LBL_TITLE");?></th>
    <!-- <th nowrap="nowrap"><//?php echo $AppUI->_('Issuing');?></th> -->
    <!--<th nowrap="nowrap"><//?php echo $AppUI->_('Receptor');?></th>-->
    <th nowrap="nowrap"><?php echo $AppUI->_("LBL_COMMUNICATION");?></th>
    <th nowrap="nowrap"><?php echo $AppUI->_("LBL_CHANNEL");?></th>
    <th nowrap="nowrap"><?php echo $AppUI->_("LBL_FREQUENCY");?></th>        
    <th nowrap="nowrap"> </th>
</tr>

<?php foreach ($list as $row) {?>
<tr>
    <td><?php echo $row['project_name'] ?></td>
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
    <td><a href="index.php?m=communication&a=addedit&communication_id=<?php echo $row['communication_id'] ?>"><?php echo $AppUI->_("LBL_EDIT")?></a></td>
</tr>
<?php } ?>
</table>