<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
$AppUI->savePlace();
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_company_role.class.php");
$controllerCompanyRole = new ControllerCompanyRole();
$company_id = intval(dPgetParam($_GET, 'company_id', 0));

require_once DP_BASE_DIR . "/modules/system/roles/roles.class.php";
$crole = new CRole;
$query = new DBQuery;
$query->addTable('human_resources_role', 'r');
$query->addQuery('human_resources_role_name as name');
$query->addWhere('r.human_resources_role_company_id = ' . $company_id);
$sql = $query->prepare();
$query->clear();
$roles = db_loadList($sql);
//$roles = $crole->getRoles();
?>
<script language="javascript">
    var nextId=1000;

    function addRole(id,roleName,index,identation,canDelete){
        nextId++;
        if(id==''){
            id=nextId;
        }	
   
        var table = document.getElementById("tb_orgonogram");
        var lastRow= table.rows.length;
        var row = table.insertRow(lastRow);
        row.id="row_"+id;
        //sorter
        var td = row.insertCell(0);
        td.noWrap=true;
        var div=document.createElement("div");
        div.innerHTML="<input type='button' value='&uarr;' class='button' onclick=moveRow(-1,'"+ row.id +"')>";
        div.innerHTML=div.innerHTML+"<input type='button' class='button' value='&darr;' onclick=moveRow(1,'"+ row.id +"')>";
        td.appendChild(div);
        //Identation
        var td = row.insertCell(1);
        td.noWrap=true;
        var div=document.createElement("div");
        div.innerHTML="<input type='button' value='&#8592;' class='button' onclick=identation('"+id+"',-1)>";
        div.innerHTML=div.innerHTML+"<input type='button' class='button' value='&#8594;' onclick=identation("+id+",1)>";
        td.appendChild(div);
        //Identation inputs
        var td = row.insertCell(2);
        td.noWrap=true;
        var span=document.createElement("span");
        span.id="identation_"+id;
        span.innerHTML=identation;
        td.appendChild(span);
        var identation_field=document.createElement("input");
        identation_field.id="identation_field_"+id;
        identation_field.name="identation_field_"+id;
        identation_field.type="hidden";
        identation_field.value=identation;
        td.appendChild(identation_field);
        //Description
	
        var combo = document.createElement("select");
        combo.id = "description_"+id;
        combo.name="description_"+id;
        var selected = 0;
        var counter = 0;
<?php
foreach ($roles as $role) {
    ?>
                    var thisRoleName = "<?php echo $role["name"]; ?>";
                    if (roleName == thisRoleName) {
                        selected = counter;
                    }
                    var option = document.createElement("option");
                    option.text = thisRoleName;
                    option.value = thisRoleName;
                    try {
                        combo.add(option, null); //Standard
                    }catch(error) {
                        combo.add(option); // IE only
                    }
                    ++counter;
    <?php
}
?>
                combo.selectedIndex = selected;
                td.appendChild(combo);
	
                //var field=document.createElement("input");
                //field.type="text";
                //field.className="text";
                //field.id="description_"+id;
                //field.name="description_"+id;
                //field.value=roleName;
                //td.appendChild(field);
	
                //add a exclude button
                td = row.insertCell(3);
                td.noWrap=false;
                div=field=document.createElement("div");
                if(canDelete){
                    div.innerHTML="<img src='modules/timeplanning/images/stock_delete-16.png' border='0'  style='cursor:pointer' onclick=deleteRole('"+row.id+"','"+id+"') />";
                }else{
                    div.innerHTML="<span style='color: green'><?php echo $AppUI->_("LBL_ROLE_ORGANIZATIONAL_DIAGRAM_CANT_DELETE"); ?></span>";
                }
                td.appendChild(div);
            }

            function identation(i,type){
                var div=document.getElementById("identation_"+i);
                var field = document.getElementById("identation_field_"+i); 
                if(type==1){
                    div.innerHTML+="&nbsp;&nbsp;&nbsp;";
                    field.value+= "&nbsp;&nbsp;&nbsp;";
                }else{
                    div.innerHTML=div.innerHTML.replace("&nbsp;&nbsp;&nbsp;","");
                    field.value = div.innerHTML.replace("&nbsp;&nbsp;&nbsp;","");;
                }
            }

            function deleteRole(rowId,id){
                var i=document.getElementById(rowId).rowIndex;
                document.getElementById('tb_orgonogram').deleteRow(i);
                var field=document.getElementById("roles_ids_to_delete");
                field.value+=field.value==""?id:","+id;
            }

            function moveRow(direction,rowId){
                var oTable = document.getElementById('tb_orgonogram');
                var trs = oTable.tBodies[0].getElementsByTagName("tr");
                var i = document.getElementById(rowId).rowIndex;
                var j = i+direction;
	
                if(j==1){
                    return false;
                }
                if(i >= 0 && j >= 0 && i < trs.length && j < trs.length){
                    if(i == j+1) {
                        oTable.tBodies[0].insertBefore(trs[i], trs[j]);
                    } else if(j == i+1) {
                        oTable.tBodies[0].insertBefore(trs[j], trs[i]);
                    } else {
                        var tmpNode = oTable.tBodies[0].replaceChild(trs[i], trs[j]);
                        if(typeof(trs[i]) != "undefined") {
                            oTable.tBodies[0].insertBefore(tmpNode, trs[i]);
                        } else {
                            oTable.appendChild(tmpNode);
                        }
                    }		
                }else{
                    //alert("Invalid Values!");
                    return false;
                }

            }

            function saveOrgonogram(){
                var idsField=document.getElementById("roles_ids");
                idsField.value="";
                var table = document.getElementById("tb_orgonogram");
                var lastRow= table.rows.length;
                for(i=0;i<lastRow;i++){
                    var row_id=table.rows[i].id;
                    var id=row_id.substring(4,row_id.length);
                    if(idsField.value==""){
                        idsField.value=id;
                    }else{
                        idsField.value +=","+id;
                    }
                }

                document.getElementById("form_orgonogram").submit();
            }
</script>
<br/>
<form action="?m=timeplanning&a=view&company_id=<?php echo $company_id; ?>" method="post" name="form_orgonogram" id="form_orgonogram">
    <input name="dosql" type="hidden" value="do_company_aed" />
    <input name="roles_ids" id="roles_ids" type="hidden" value="">
    <input name="roles_ids_to_delete" id="roles_ids_to_delete" type="hidden" value="">

    <table class="tbl" id="tb_orgonogram" width="95%" align="center" >
        <caption> <b><?php echo $AppUI->_('LBL_ORGONOGRAM'); ?> </b></caption>
        <tr>
            <th width="10%"><?php echo $AppUI->_('LBL_ORDER'); ?></th>
            <th width="10%"><?php echo $AppUI->_('LBL_IDENTATION'); ?></th>
            <th width="45%"><?php echo $AppUI->_('LBL_ROLE'); ?></th>
            <th width="35%"><?php echo $AppUI->_('LBL_EXCLUSION'); ?></th>
        </tr>
    </table>
    <table width="95%" align="center">
        <tr>
            <td style="text-align:right">
                <input type="button" class="button" value="<?php echo $AppUI->_('LBL_ADD'); ?>" onclick=addRole('','','','',true);> 
                <input type="button" class="button" onclick=saveOrgonogram() value="<?php echo $AppUI->_('LBL_SAVE'); ?>" />
                <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/subform_back_button_no_ask.php"); ?>
            </td>
        </tr>
    </table>
</form>

<?php
$roles = $controllerCompanyRole->getCompanyRoles($company_id);
foreach ($roles as $role) {
    $id = $role->getId();
    $name = $role->getDescription();
    $identation = $role->getIdentation();
    $canDelete=$controllerCompanyRole->canDelete($id)?"true":"false";
    echo '<script>addRole(' . $id . ',"' . $name . '",0,"' . $identation . '",'. $canDelete.');</script>';
}
?>