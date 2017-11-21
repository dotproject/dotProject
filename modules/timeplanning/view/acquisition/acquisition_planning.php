<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/control/acquisition/controller_acquisition_planning.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/model/acquisition/acquisition_planning.class.php");
$controller = new ControllerAcquisitionPlanning();
?>
<a name="acquisition_planning"></a>
<style>
    #aquisition_list tr td{
        vertical-align: top;
    }
</style>

<br />
<table class="tbl" align="center" width="95%" id="aquisition_list">
    <caption><?php echo $AppUI->_("LBL_ACQUISITION_PLANNING"); ?></caption>
    <tr>
        <th><?php echo $AppUI->_("LBL_ITEM_TO_ACQUIRE"); ?></th>
        <th><?php echo $AppUI->_("LBL_CONTRACT_TYPE"); ?></th>
        <th><?php echo $AppUI->_("LBL_DOCUMENTS_TO_ACQUIRE"); ?></th>
        <th><?php echo $AppUI->_("LBL_CRITERIA_TO_SUPPLIERS_SELECTION"); ?></th>
        <th><?php echo $AppUI->_("LBL_ACQUISITION_ADDITIONAL_REQUIRIMENTS"); ?>:</th>
        <th><?php echo $AppUI->_("LBL_SUPPLIERS_PROCESSES_MANAGEMENT"); ?>:</th>
        <th><?php echo $AppUI->_("LBL_ACQUISITION_ROLES_RESPONSABILITIES"); ?>:</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
    </tr>
    <?php
    $list = $controller->getAcquisitionPlanningsPerProject($_GET["project_id"]);
    foreach ($list as $object) {
        ?>
        <tr>
            <td>
                <?php echo $object->getItemsToBeAcquired() ?>
            </td>	
            <td>
                <?php echo $object->getContractType() ?>
            </td>	

            <td>
                <?php echo $object->getDocumentsToAcquisition() ?>
            </td>	

            <td> 
                <?php echo $object->getCriteriaForSelection() ?>
            </td>	

            <td>
                <?php echo $object->getAdditionalRequirements() ?>
            </td>	
            <td>
                <?php echo $object->getSupplierManagementProcess() ?>
            </td>	
            <td>
                <?php echo $object->getAcquisitionRoles() ?>
            </td>
            <td width="20" valign="top">
                <a href="index.php?m=projects&a=view&acquisition_planning_id=<?php echo $object->getId() ?>&project_id=<?php echo $_GET["project_id"] ?>&targetScreenOnProject=/modules/timeplanning/view/acquisition/acquisition_planning.php#acquisition_planning_edit">
                    <img alt="<?php echo $AppUI->_("LBL_EDIT"); ?>" src="modules/timeplanning/images/stock_edit-16.png" border="0" />
                </a>
            </td>	
            <td width="20" valign="top">
                <form method="post" action="?m=timeplanning">
                    <input name="dosql" type="hidden" value="do_project_acquisition_deletion" />
                    <input name="project_id" type="hidden" value="<?php echo $_GET["project_id"] ?>" />
                    <input name="tab" type="hidden" value="<?php echo $_GET["tab"]; ?>" />
                    <input name="id" type="hidden" value="<?php echo $object->getId() ?>" />
                    <button type="submit" style="text-decoration: none;border: 0px;cursor: pointer;background-color: #FFFFFF">
                        <img src="modules/dotproject_plus/images/trash_small.gif" border="0" />
                    </button>
                </form>
            </td>	
        </tr>
    <?php } ?>
</table>

<style>
    textarea{
        width:80%;
        height: 80px;
        text-align: left;
    }
</style>
<link href="modules/timeplanning/css/table_form.css" type="text/css" rel="stylesheet" />
<!--
<script type="text/javascript" src="./modules/timeplanning/js/jsLibraries/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
    tinyMCE.init({
        mode : "textareas",
        theme : "advanced",
        width : "90%",
        height: "240",
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons3 : "hr,removeformat,sub,sup,charmap"
    });
</script>
-->
<script>
    
    function saveAcquisition(){
        var result=true;
        
        var criteriaId=null;
        var criteria=null;
        var weight=null;
        
        var requirementId=null;
        var requirement=null;
        
        var roleId=null;
        var role=null;
        var responsability=null;
                
        if(document.acquisition_item_planned.items_to_be_acquired.value==""){
            setAppMessage("Favor informe o nome do item a ser adquirido.", APP_MESSAGE_TYPE_WARNING);
            result=false;
            document.acquisition_item_planned.items_to_be_acquired.focus();
        }
              
        //Collect all ids and other date to be stored after for submission.
        //Before include a value, it is observed whether it is within the deletion array.
        document.acquisition_item_planned.criteria_for_supplier_selection.value="";
        for(i=0;i<critereaIdsToSave.length;i++){
            criteriaId=critereaIdsToSave[i];
            if(critereaIdsToRemove.indexOf(criteriaId)==-1){
                criteria=document.getElementById("criteria_" +criteriaId).value;
                weight=document.getElementById("weight_" +criteriaId).options[document.getElementById("weight_" +criteriaId).selectedIndex].value;
                document.acquisition_item_planned.criteria_to_save.value+=criteriaId+"#!"+criteria+"#!"+weight+"#$";
                document.acquisition_item_planned.criteria_for_supplier_selection.value+=criteria+" (<?php echo $AppUI->_("LBL_PROCUREMENT_WEIGHT") ;?>: "+weight+") <br />";
            }
        }
       
        document.acquisition_item_planned.additional_requirements.value="";
        for(i=0;i<requirementsIdsToSave.length;i++){
            requirementId=requirementsIdsToSave[i];
            if(requirementsIdsToRemove.indexOf(requirementId)==-1){
                requirement=document.getElementById("requirement_" +requirementId).value;
                document.acquisition_item_planned.requirements_to_save.value+=requirementId+"#!"+requirement+"#$";
                document.acquisition_item_planned.additional_requirements.value+=requirement+"<br />";
            }
        }
        
        document.acquisition_item_planned.acquisition_roles.value="";
        for(i=0;i<rolesIdsToSave.length;i++){
            roleId=rolesIdsToSave[i];
            if(rolesIdsToRemove.indexOf(roleId)==-1){
                role=document.getElementById("role_" +roleId).value;
                responsability=document.getElementById("responsability_" +roleId).value;
                document.acquisition_item_planned.roles_to_save.value+=rolesIdsToSave[i]+"#!"+role+"#!"+ responsability + "#$";
                document.acquisition_item_planned.acquisition_roles.value+=role+": "+ responsability + "<br />";      
            }
        }
                 
        //Collect all ids to be deleted after for submission
        for(i=0;i<critereaIdsToRemove.length;i++){
            document.acquisition_item_planned.criteria_to_delete.value+=critereaIdsToRemove[i]+"#$";
        }
        
        for(i=0;i<requirementsIdsToRemove.length;i++){
            document.acquisition_item_planned.requirements_to_delete.value+=requirementsIdsToRemove[i]+"#$";
        }
        for(i=0;i<rolesIdsToRemove.length;i++){
            document.acquisition_item_planned.roles_to_delete.value+=rolesIdsToRemove[i]+"#$";
        }
        
        return result;
    }   
    
    //Criteria functions  
    var nextIdForNewCriteria=900000;//a huge number to not enter in conflict with any database id.
    var critereaIdsToRemove= new Array();
    var critereaIdsToSave= new Array();
    function addCriteria(criteriaId, name, weight) {
        var div = document.getElementById("div_citeria");
        var weightField = document.createElement("select");
        var nameField = document.createElement("input");
        var removeButton = document.createElement("img");
        var br = document.createElement("br");
               
        
        if(criteriaId==0){
            criteriaId= nextIdForNewCriteria;
            nextIdForNewCriteria++;
        }
        critereaIdsToSave[critereaIdsToSave.length]=criteriaId;
        br.id="criteria_br_"+criteriaId;
        
        nameField.name = "criteria_" +criteriaId;
        nameField.id = "criteria_" +criteriaId;
        nameField.value=name;
        nameField.size=60;
        nameField.className = "text";

        weightField.name = "weight_" +criteriaId;
        weightField.id = "weight_" +criteriaId;
        weightField.className = "text";
        
        for(i=1;i<=10;i++){
            weightField.options[i]= new Option(i,i);
            if(i==weight){
                weightField.selectedIndex=i;
            }
        }

        removeButton.src = "./modules/dotproject_plus/images/trash_small.gif";
        removeButton.id = criteriaId;
        removeButton.title = criteriaId;
        removeButton.name = criteriaId;
        removeButton.style.cursor = "pointer";
        
        removeButton.onclick = removeCriteria;
                 
        //add all created fields in the screen
        div.appendChild(nameField);
        div.appendChild(weightField);
        div.appendChild(removeButton);
        div.appendChild(br);  
    }

    function removeCriteria() {
        var div = document.getElementById("div_citeria");
        var fieldName = document.getElementById("criteria_" + this.name);
        var fieldWeight = document.getElementById("weight_" + this.name);
        var fieldBr = document.getElementById("criteria_br_" + this.name);
        critereaIdsToRemove[critereaIdsToRemove.length]=parseInt(this.name);
        
        div.removeChild(fieldName);
        div.removeChild(fieldWeight);
        div.removeChild(fieldBr);
        div.removeChild(this);      
    }
    
    //Requirement functions  
    var nextIdForNewRequirement=900000;//a huge number to not enter in conflict with any database id.
    var requirementsIdsToRemove= new Array();
    var requirementsIdsToSave= new Array();
    function addRequirement(requirementId, name) {
        var div = document.getElementById("div_requirements");
        var nameField = document.createElement("input");
        var removeButton = document.createElement("img");
        var br = document.createElement("br");
               
        
        if(requirementId==0){
            requirementId= nextIdForNewRequirement;
            nextIdForNewRequirement++;
        }
        requirementsIdsToSave[requirementsIdsToSave.length]=requirementId;
        
        br.id="requirement_br_"+requirementId;
        
        nameField.name = "requirement_" +requirementId;
        nameField.id = "requirement_" +requirementId;
        nameField.value=name;
        nameField.className = "text";
        nameField.size=120;
        


        removeButton.src = "./modules/dotproject_plus/images/trash_small.gif";
        removeButton.id = requirementId;
        removeButton.title = requirementId;
        removeButton.name = requirementId;
        removeButton.style.cursor = "pointer";
        
        removeButton.onclick = removeRequirement;
                 
        //add all created fields in the screen
        div.appendChild(nameField);
        div.appendChild(removeButton);
        div.appendChild(br);  
    }

    function removeRequirement() {
        var div = document.getElementById("div_requirements");
        var fieldName = document.getElementById("requirement_" + this.name);
        var fieldBr = document.getElementById("requirement_br_" + this.name);
        requirementsIdsToRemove[requirementsIdsToRemove.length]=parseInt(this.name);
        
        div.removeChild(fieldName);
        div.removeChild(fieldBr);
        div.removeChild(this);      
    }
    
    //Roles and responsabilities function :: 
    
    var nextIdForNewRole=900000;//a huge number to not enter in conflict with any database id.
    var rolesIdsToRemove= new Array();
    var rolesIdsToSave= new Array();
    function addRoles(roleId, role, responsability) {
        var div = document.getElementById("div_roles");
        var roleField = document.createElement("input");
        var resposabilityField = document.createElement("input");
        var removeButton = document.createElement("img");
        var br = document.createElement("br");
               
        
        if(roleId==0){
            roleId= nextIdForNewRole;
            nextIdForNewRole++;
        }
        rolesIdsToSave[rolesIdsToSave.length]=roleId;
        
        br.id="role_br_"+roleId;
        
        roleField.name = "role_" +roleId;
        roleField.id = "role_" +roleId;
        roleField.value=role;
        roleField.size=60;
        roleField.className = "text";

        resposabilityField.name = "responsability_" +roleId;
        resposabilityField.id = "responsability_" +roleId;
        resposabilityField.value=responsability;
        resposabilityField.size=100;
        resposabilityField.className = "text";

        removeButton.src = "./modules/dotproject_plus/images/trash_small.gif";
        removeButton.id = roleId;
        removeButton.title = roleId;
        removeButton.name = roleId;
        removeButton.style.cursor = "pointer";
        
        removeButton.onclick = removeRole;
                 
        //add all created fields in the screen
        div.appendChild(roleField);
        div.appendChild(resposabilityField);
        div.appendChild(removeButton);
        div.appendChild(br);  
    }

    function removeRole() {
        var div = document.getElementById("div_roles");
        var role = document.getElementById("role_" + this.name);
        var responsability = document.getElementById("responsability_" + this.name);
        var fieldBr = document.getElementById("role_br_" + this.name);
        rolesIdsToRemove[rolesIdsToRemove.length]=parseInt(this.name);
        
        div.removeChild(role);
        div.removeChild(responsability); 
        div.removeChild(fieldBr);
        div.removeChild(this);
             
    }
</script>

<?php
$id = $_GET["acquisition_planning_id"];
$object = $controller->getAcquisitionPlanning($id == "" ? -1 : $id);
?>
<br /><br />
<a name="acquisition_planning_edit"></a>
<form  name="acquisition_item_planned" method="post" action="?m=timeplanning" onsubmit="return saveAcquisition()">
    <input name="dosql" type="hidden" value="do_project_acquisition_planning" />
    <input name="project_id" type="hidden" value="<?php echo $_GET["project_id"]; ?>" />
    <input name="tab" type="hidden" value="<?php echo $_GET["tab"]; ?>" />
    <input name="acquisition_planning_id" type="hidden" value="<?php echo $object->getId() ?>" />
    <input name="requirements_to_save" type="hidden" />
    <input name="criteria_to_save" type="hidden" />
    <input name="roles_to_save" type="hidden" />
    <input name="requirements_to_delete" type="hidden" />
    <input name="criteria_to_delete" type="hidden" />
    <input name="roles_to_delete" type="hidden" />

    <table class="std" align="center" width="95%" name="table_form" border="0">
        <tr >
            <th colspan="2" align="center"><?php echo $AppUI->_("LBL_ACQUISITION_PLANNING"); ?></th>
        </tr>
        <tr >
            <td class="td_label">
                <?php echo $AppUI->_("LBL_ITEM_TO_ACQUIRE"); ?>
                <span style="color:red">*</span>:
            </td>
            <td nowrap>
                <input type="text" class="text" style="width:80%" name="items_to_be_acquired" id="items_to_be_acquired" value="<?php echo $object->getItemsToBeAcquired() ?>" />
            </td>	
        </tr>
        <tr>
            <td class="td_label"><?php echo $AppUI->_("LBL_CONTRACT_TYPE"); ?>:</td>
            <td nowrap>
                <!--<input type="text" class="text" style="width:80%" name="contract_type" value="<?php echo $object->getContractType() ?>" />-->
                <?php
                $types = array();
                $types[0] = $AppUI->_("LBL_ACQUISITION_CONTRACT_TYPE_FIXED_PRICE");
                $types[1] = $AppUI->_("LBL_ACQUISITION_CONTRACT_TYPE_TIME_MATERIAL");
                $types[2] = $AppUI->_("LBL_ACQUISITION_CONTRACT_TYPE_COST_REPAID") ;
                ?>

                <select class="text" name="contract_type" />
                <?php
                for ($i = 0; $i < sizeof($types); $i++) {
                    ?>
            <option value="<?php echo $types[$i] ?>" <?php echo $types[$i] == $object->getContractType() ? "selected" : ""; ?> >
                <?php echo $types[$i] ?>
            </option>
            <?php
        }
        ?>
        </select>

        </td>	
        </tr>
        <tr>
            <td class="td_label"><?php echo $AppUI->_("LBL_DOCUMENTS_TO_ACQUIRE"); ?>:</td>
            <td nowrap>
                <textarea class="text" name="documents_to_acquisition"><?php echo $object->getDocumentsToAcquisition() ?></textarea>
            </td>	
        </tr>
        <tr>
            <td class="td_label"><?php echo $AppUI->_("LBL_CRITERIA_TO_SUPPLIERS_SELECTION"); ?>:</td>
            <td nowrap>
                <textarea  style="display:none" class="text" name="criteria_for_supplier_selection"><?php echo $object->getCriteriaForSelection() ?></textarea>
                <input type="button" value="<?php echo $AppUI->_("LBL_ACQUISION_ADD_CRITERIA") ?>" onclick="addCriteria(0, '', 1);" /> 
                <div id="div_citeria"></div>
            </td>	
        </tr>
        <tr>
            <td class="td_label"><?php echo $AppUI->_("LBL_ACQUISITION_ADDITIONAL_REQUIRIMENTS"); ?>:</td>
            <td nowrap>
                <textarea style="display:none" class="text" name="additional_requirements"><?php echo $object->getAdditionalRequirements() ?></textarea>
                <input type="button" onclick="addRequirement(0, '');" value="<?php echo $AppUI->_("LBL_ACQUISION_ADD_REQUIREMENT") ?>" /> 
                <div id="div_requirements"></div>
            </td>	
        </tr>
        <tr>
            <td class="td_label"><?php echo $AppUI->_("LBL_SUPPLIERS_PROCESSES_MANAGEMENT"); ?>:</td>
            <td nowrap>
                <textarea class="text" name="supplier_management_process"><?php echo $object->getSupplierManagementProcess() ?></textarea>
            </td>	
        </tr>
        <tr>
            <td class="td_label"><?php echo $AppUI->_("LBL_ACQUISITION_ROLES_RESPONSABILITIES"); ?>:</td>
            <td nowrap>
                <textarea style="display:none" class="text" name="acquisition_roles"><?php echo $object->getAcquisitionRoles() ?></textarea>
                <input type="button" value="<?php echo $AppUI->_("LBL_ACQUISION_ADD_ROLE"); ?>" onclick="addRoles(0, '','');" />  
                <div id="div_roles"></div>
            </td>	
        </tr>

    </table>
    <table width="95%" align="center">
        <tr>
            <td colspan="2" align="right">
                <input type="submit"  name="Salvar" value="<?php echo $AppUI->_("LBL_SAVE"); ?>" class="button" />
                <script> var targetScreenOnProject="/modules/dotproject_plus/projects_tab.planning_and_monitoring.php";</script>
                <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/subform_back_button_project.php"); ?>
                <input type="button"  name="clean" value="<?php echo $AppUI->_("LBL_CLEAN") ?>" class="button" onclick="window.location='index.php?m=projects&a=view&project_id=<?php echo $_GET["project_id"] ?>&targetScreenOnProject=/modules/timeplanning/view/acquisition/acquisition_planning.php#acquisition_planning_edit'"/>
            </td>
        </tr>
    </table>
</form>

<script>
<?php
if($id!=""){
    $criteria=$controller->loadCriteria($id);
    for($i=0;$i<sizeof($criteria);$i++){
        $data=explode("#!",$criteria[$i]);
        echo "addCriteria(".$data[0] .",'". $data[1]."','". $data[2]."');";
    }
    
    $requirements=$controller->loadRequirements($id);
    for($i=0;$i<sizeof($requirements);$i++){
        $data=explode("#!",$requirements[$i]);
        echo "addRequirement(".$data[0] .",'". $data[1]."');";
    }
    
    $roles=$controller->loadRoles($id);
    for($i=0;$i<sizeof($roles);$i++){
        $data=explode("#!",$roles[$i]);
        echo "addRoles(".$data[0] .",'". $data[1]."','". $data[2]."');";
    }
}
?>
</script>