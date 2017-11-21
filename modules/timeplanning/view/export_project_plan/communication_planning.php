<?php
$q = new DBQuery();
$q->addQuery("c.communication_id, c.communication_title, c.communication_information, ch.*, fr.*, p.project_name,  c.communication_restrictions as communication_restrictions");
$q->addTable("communication", "c");
$q->addJoin("communication_channel", "ch", "ch.communication_channel_id=c.communication_channel_id");
$q->addJoin("communication_frequency", "fr", "fr.communication_frequency_id=c.communication_frequency_id");
$q->addJoin("projects", "p", "p.project_id=c.communication_project_id");

$q->addwhere("c.communication_project_id=" . $projectId);
$list = $q->loadList();
$q->clear();

$q = new DBQuery();
$q->addQuery("c.communication_id, co.contact_first_name as emissor_first_name, co.contact_last_name as emissor_last_name");
$q->addTable("communication", "c");
$q->addJoin("communication_issuing", "ci", "ci.communication_id=c.communication_id");
$q->addJoin("initiating_stakeholder", "st", "st.initiating_stakeholder_id=ci.communication_stakeholder_id");
$q->addJoin("contacts", "co", "co.contact_id=st.contact_id");
$list_Emissor = $q->loadList();
$q->clear();


$q = new DBQuery();
$q->addQuery("c.communication_id, cor.contact_first_name as receptor_first_name, cor.contact_last_name as receptor_last_name");
$q->addTable("communication", "c");
$q->addJoin("communication_receptor", "cr", "cr.communication_id=c.communication_id");
$q->addJoin("initiating_stakeholder", "str", "str.initiating_stakeholder_id=cr.communication_stakeholder_id");
$q->addJoin("contacts", "cor", "cor.contact_id=str.contact_id");
$list_Receptor = $q->loadList();
$q->clear();
?>



<?php
foreach ($list as $row) {
    ?>
    <table class="printTable">
        <tr>
            <td width="30%" class="labelCell"><?php echo $AppUI->_("LBL_PROJECT_COMMUNICATION_TITLE",UI_OUTPUT_HTML); ?> </td>
            <td width="70%"><?php echo $row["communication_title"] ?></td>
        </tr>
        <tr>    
            <td class="labelCell"><?php echo $AppUI->_("LBL_PROJECT_COMMUNICATION_TO",UI_OUTPUT_HTML); ?></td>
            <td>

                <?php
                foreach ($list_Receptor as $receptor) {
                    if ($receptor["communication_id"] == $row["communication_id"]) {
                        echo " - " . $receptor["receptor_first_name"] . " " . $receptor["receptor_last_name"] ;
                    }
                }
                ?>
                &nbsp;
            </td>   
        </tr>

        <tr>
            <td class="labelCell"><?php echo $AppUI->_("LBL_PROJECT_COMUNICATION_FROM",UI_OUTPUT_HTML); ?></td>
            <td>
                <?php
                foreach ($list_Emissor as $emissor) {
                    if ($emissor["communication_id"] == $row["communication_id"]) {
                        echo  " - ". $emissor["emissor_first_name"] . " " . $emissor["emissor_last_name"] ;
                    }
                }
                ?> 
                &nbsp;

            </td>
        </tr>

        <tr>
            <td class="labelCell"><?php echo $AppUI->_("LBL_COMMUNICATION",UI_OUTPUT_HTML); ?></td>
            <td><?php echo $row["communication_information"] ?>&nbsp;</td></tr>
        <tr>
            <td class="labelCell"><?php echo $AppUI->_("LBL_PROJECT_COMMUNICATION_MODE",UI_OUTPUT_HTML); ?></td>
            <td><?php echo $row["communication_channel"] ?>&nbsp;</td></tr>
        <tr>
            <td class="labelCell"><?php echo $AppUI->_("LBL_PROJECT_COMMUNICATION_FREQUENCY",UI_OUTPUT_HTML); ?></td> 
            <td><?php echo $row["communication_frequency"] ?>&nbsp;</td></tr>
        <tr>
            <td class="labelCell"><?php echo $AppUI->_("LBL_PROJECT_COMMUNICATION_CONSTRAINTS",UI_OUTPUT_HTML); ?></td>
            <td>

                <?php echo $row["communication_restrictions"]; ?>
            </td>
        </tr>
    </table>
    <br/>
<?php } ?>
