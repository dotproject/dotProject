<script src="./modules/timeplanning/js/estimations.js"></script>
<script src="./modules/timeplanning/js/eap.js"></script>
<script src="./modules/timeplanning/js/ajax_service_activator.js"></script>

<!-- include libraries for right click menu -->
<script type="text/javascript" src="./modules/dotproject_plus/milonic_right_click_menu/milonic_src.js"></script> 
<script type="text/javascript" src="./modules/dotproject_plus/milonic_right_click_menu/mmenudom.js"></script> 
<script type="text/javascript" src="./modules/dotproject_plus/milonic_right_click_menu/contextmenu_activities_wbs.js"></script>
<script type="text/javascript" src="./modules/dotproject_plus/milonic_right_click_menu/menu_data_activities.js"></script>
<script type="text/javascript" src="./modules/dotproject_plus/milonic_right_click_menu/menu_data_wbs.js"></script>
<script type="text/javascript" src="./modules/dotproject_plus/milonic_right_click_menu/menu_data_wbs_new_activity.js"></script>
<script type="text/javascript" src="./modules/dotproject_plus/milonic_right_click_menu/menu_data_wbs_non_workpackage.js"></script>

<!-- This script code  was moved to here due to translation reasons -->
<script>
    with (milonic = new menuname("contextMenu")) {
        margin = 7;
        style = contextStyle;
        top = "offset=8";
        aI("image=./modules/dotproject_plus/images/activities_details.png;text=<?php echo $AppUI->_("Details"); ?>;url=javascript:rightClickMenuShowActivity();");
        aI("image=./modules/dotproject_plus/images/lapis.png;text=<?php echo $AppUI->_("Edit"); ?>;url=javascript:rightClickMenuEditActivity();");
        aI("image=./modules/dotproject_plus/images/trash_small.gif;text=<?php echo $AppUI->_("LBL_EXCLUSION"); ?>;url=javascript:rightClickMenuExcludeActivity();");
    }
    drawMenus();

    with (milonic = new menuname("contextMenuWBS")) {
        margin = 9;
        style = contextStyle;
        top = "offset=5";
        aI("image=./modules/dotproject_plus/images/mais_azul.png;text=<?php echo $AppUI->_("LBL_MENU_NEW_WBS_ITEM"); ?>;url=javascript:rightClickMenuNewEAPItem();");
        aI("image=./modules/dotproject_plus/images/mais_verde.png;text=<?php echo $AppUI->_("LBL_MENU_NEW_ACTIVITY"); ?>;url=javascript:rightClickMenuNewActivity();");
        aI("image=./modules/dotproject_plus/images/lapis.png;text=<?php echo $AppUI->_("Edit"); ?>;url=javascript:rightClickMenuEditEAPItem();");
        aI("image=./modules/dotproject_plus/images/trash_small.gif;text=<?php echo $AppUI->_("LBL_EXCLUSION"); ?>;url=javascript:rightClickMenuDeleteWBSItem();");
        aI("image=./modules/dotproject_plus/images/scope_declaration.jpg?eee=5;text=<?php echo $AppUI->_("LBL_PROJECT_SCOPE_DECLARATION"); ?>;url=javascript:rightClickMenuShowScopeDeclaration();");
        aI("image=./modules/dotproject_plus/images/dicionario.png?eee=5;text=<?php echo $AppUI->_("LBL_WBS_DICTIONARY"); ?>;url=javascript:rightClickMenuShowWBSDictionary();");
    }

    drawMenus();

    with (milonic = new menuname("contextMenuWBSNewActivity")) {
        margin = 9;
        style = contextStyle;
        top = "offset=5";
        aI("image=./modules/dotproject_plus/images/mais_verde.png;text=<?php echo $AppUI->_("LBL_MENU_NEW_ACTIVITY"); ?>;url=javascript:rightClickMenuFirstActivity();");
    }
    drawMenus();

    with (milonic = new menuname("contextMenuWBSNonWorkPackage")) {
        margin = 9;
        style = contextStyle;
        top = "offset=5";
        aI("image=./modules/dotproject_plus/images/mais_azul.png;text=<?php echo $AppUI->_("LBL_MENU_NEW_WBS_ITEM"); ?>;url=javascript:rightClickMenuNewEAPItem();");
        aI("image=./modules/dotproject_plus/images/lapis.png;text=<?php echo $AppUI->_("Edit"); ?>;url=javascript:rightClickMenuEditEAPItem();");
        aI("image=./modules/dotproject_plus/images/trash_small.gif;text=<?php echo $AppUI->_("LBL_EXCLUSION"); ?>;url=javascript:rightClickMenuDeleteWBSItem();");
        aI("image=./modules/dotproject_plus/images/scope_declaration.jpg?eee=5;text=<?php echo $AppUI->_("LBL_PROJECT_SCOPE_DECLARATION"); ?>;url=javascript:rightClickMenuShowScopeDeclaration();");
        aI("image=./modules/dotproject_plus/images/dicionario.png?eee=5;text=<?php echo $AppUI->_("LBL_WBS_DICTIONARY"); ?>;url=javascript:rightClickMenuShowWBSDictionary();");
    }
    drawMenus();

</script>


<a href="http://www.milonic.com/" style="display: none">DHTML JavaScript Menu By Milonic.com</a>
<!-- include libraries for calendar goodies -->
<link type="text/css" rel="stylesheet" href="./modules/timeplanning/js/jsLibraries/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen"></link>
<script type="text/javascript" src="./modules/timeplanning/js/jsLibraries/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
<!-- include libraries for lightweight messages -->
<link type="text/css" rel="stylesheet" href="./modules/timeplanning/js/jsLibraries/alertify/alertify.css" media="screen"></link>
<script type="text/javascript" src="./modules/timeplanning/js/jsLibraries/alertify/alertify.js"></script>


<?php
$project_id = dPgetParam($_GET, "project_id", 0);
?>


<script>
    //Definition of messages as global variables  
    var mensagem_1 = "<?php echo $AppUI->_("LBL_CREATE_ACTIVITIES_BEFORE_SEQUENCING", UI_OUTPUT_JS); ?>";
    var mensagem_2 = "<?php echo $AppUI->_("LBL_CONFIRM_WBS_ITEM_EXCLUSION", UI_OUTPUT_JS); ?>";
    var mensagem_2_activity = "<?php echo $AppUI->_("LBL_CONFIRM_ACTIVITY_EXCLUSION", UI_OUTPUT_JS); ?>";
    var message_3= "<?php echo $AppUI->_("LBL_DATA_SUCCESSFULLY_DELETED", UI_OUTPUT_JS); ?>";
    var message_4 = "<?php echo $AppUI->_("LBL_DATA_SUCCESSFULLY_PROCESSED", UI_OUTPUT_JS); ?>";
    var mensagem_5_WBS = "<?php echo $AppUI->_("LBL_CONFIRM_ITEM_CANCEL", UI_OUTPUT_JS); ?>";
    var mensagem_5 = "<?php echo $AppUI->_("LBL_CONFIRM_ITEM_CANCEL", UI_OUTPUT_JS); ?>";

    function showMessage(msg) {
        setAppMessage(msg, APP_MESSAGE_TYPE_INFO);
        //window.alert(msg);
    }     
  
    function saveWBSItem(wbsItemId) {
        //window.alert(mensagem_4);
        //document.getElementById("save_wbs_" + wbsItemId).submit();  
        var form = $("#save_wbs_" + wbsItemId);
        $.ajax(
                {
                    type: "POST",
                    url: form.attr("action"),
                    data: form.serialize(),
                    success: function (data) {
                        showWBSItemEdit(wbsItemId);//this call will change to read mode the WBS item
                        reload();
                        //alertify.alert(message_4);
                        alertify.success(message_4);
                        //alertify.error(message_4);
                    },
                    dataType: "text"
                });

    }

    function cancelSaveWBSItem(wbsItemId) {
        
        alertify.confirm(mensagem_5_WBS, function () {
            reload();
        }, function() {
            // user clicked "cancel"
        });
        
    }

    function newWBSItem(wbsItemId) {
        document.getElementById("new_wbs_item_" + wbsItemId).submit();
        //showWBSItemEdit(wbsItemId);//this call will change to read mode the WBS item
    }

    function createFirstActivity(wbsItemId) {
        document.getElementById("new_first_activity_for_an_wbs_id_" + wbsItemId).submit();
    }

    function rightClickMenuFirstActivity() {
        if (contextObject.id.indexOf("new_activity_wbs_item_id_") != -1) {
            var wbsItemId = contextObject.id.split("new_activity_wbs_item_id_")[1];
            createFirstActivity(wbsItemId);
        }
    }

    function rightClickMenuShowActivity() {
        if (contextObject.parentNode.id.indexOf("_activity_id_") != -1 || contextObject.parentNode.parentNode.id.indexOf("_activity_id_") != -1) {
            var parentId = contextObject.parentNode.id.indexOf("_activity_id_") != -1 ? contextObject.parentNode.id : contextObject.parentNode.parentNode.id;
            var activityId = parentId.split("_activity_id_")[1];
            var wbsItemId = parentId.split("wbs_id_")[1].split("_activity_id_")[0];
            showActivityDetails(activityId, wbsItemId);
        }
    }

    function rightClickMenuShowWBSDictionary() {
        showWBSDictionary();
    }

    function rightClickMenuShowScopeDeclaration() {
        var url = replaceAll("#gqs_anchor", "", window.location.href);
        window.location = url + "&show_external_page=/modules/timeplanning/view/scope_declaration.php";
    }

    function rightClickMenuEditActivity() {
        if (contextObject.parentNode.id.indexOf("_activity_id_") != -1 || contextObject.parentNode.parentNode.id.indexOf("_activity_id_") != -1) {
            var parentId = contextObject.parentNode.id.indexOf("_activity_id_") != -1 ? contextObject.parentNode.id : contextObject.parentNode.parentNode.id;
            var activityId = parentId.split("_activity_id_")[1];
            var wbsItemId = parentId.split("wbs_id_")[1].split("_activity_id_")[0];
            showActivityEdit(activityId, wbsItemId);
        }
    }

    function rightClickMenuExcludeActivity() {
        if (contextObject.parentNode.id.indexOf("_activity_id_") != -1 || contextObject.parentNode.parentNode.id.indexOf("_activity_id_") != -1) {
            var parentId = contextObject.parentNode.id.indexOf("_activity_id_") != -1 ? contextObject.parentNode.id : contextObject.parentNode.parentNode.id;
            var activityId = parentId.split("_activity_id_")[1];
            var wbsItemId = parentId.split("wbs_id_")[1].split("_activity_id_")[0];
            alertify.confirm(mensagem_2_activity, function () {excludeActivity(activityId, wbsItemId);}, function() {});
        }
    }

    function prepareRolesToSave(taskId) {
        var quant = parseInt(document.getElementById("roles_num_" + taskId).value);//this field provides the amount of estimated roles
        var rolesIds = document.getElementById("estimatedRolesIds_" + taskId);//this field stores all selected roles separed by ";"
        var rolesHRs = document.getElementById("estimatedRolesHR_" + taskId);//this field stores all selected hr separed by ";"
        var resultado = true;
        var feedback_message = "";
        //verifica se algum usuário já foi alocado mais de uma vez
        for (i = 0; i < quant; i++) {
            var selectField = document.getElementById("allocated_hr_role_" + taskId + "_" + i);
            var testRHId = selectField.options[selectField.selectedIndex].value;
            if (testRHId != "") {
                var count = 0;
                for (j = 0; j < quant; j++) {
                    selectField = document.getElementById("allocated_hr_role_" + taskId + "_" + j);
                    var tempRHid = selectField.options[selectField.selectedIndex].value;
                    if (testRHId == tempRHid && selectField.style.display != "none") {
                        count++;
                    }
                }

                if (count > 1 && resultado) {//resultado is used just to ensure the message will be presented once
                    selectField = document.getElementById("allocated_hr_role_" + taskId + "_" + i);
                    feedback_message += selectField.options[selectField.selectedIndex].text + " já está alocado para esta atividade, escolha outra pessoa.<br />";
                    resultado = false; // Do not allow to save: A user is allocated twice to the same activity
                }
            }
        }

        for (i = 0; i < quant; i++) {
            var nextId = i;
            selectField = document.getElementById("estimated_role_" + taskId + "_" + nextId);
            var role_id = selectField.options[selectField.selectedIndex].value;
            rolesIds.value += role_id + ";";
            selectField = document.getElementById("allocated_hr_role_" + taskId + "_" + nextId);
            var hr_id = selectField.options[selectField.selectedIndex].value;
            var hr_name = selectField.options[selectField.selectedIndex].text;
            rolesHRs.value += hr_id + ";";
            //alert("Role: "+ document.getElementById("estimated_role_" + taskId + "_" + nextId).value + " | HR: "+document.getElementById("allocated_hr_role_" + taskId + "_" + nextId).value);
            if (hr_id != "" && role_id == "") {// A human resource has been selected, but a role has not been estimated.
                feedback_message += "Por favor, selecione o papel antes de alocar o RH. <br />Detalhes: O recurso humano (" + hr_name + ") foi alocado sem que um papel fosse estimado.<br />";
                resultado = false; // Do not allow to save: A human resource was allocated without a role estimated
            }
        }
        if (!resultado) {
            setAppMessage(feedback_message, APP_MESSAGE_TYPE_WARNING);
        }
        return resultado;
    }


    function reload() {
        window.location.reload();
        //$("#div_eap_activities").load(window.location.href + " #div_eap_activities");
    }

    function rightClickMenuNewEAPItem() {
        if (contextObject.parentNode.id.indexOf("row_") != -1 || contextObject.parentNode.parentNode.id.indexOf("row_") != -1) {
            var parentId = contextObject.parentNode.id.indexOf("row_") != -1 ? contextObject.parentNode.id : contextObject.parentNode.parentNode.id;
            var wbsItemId = parentId.split("row_")[1];
            if (!wbsHasActivity[wbsItemId]) {
                newWBSItem(wbsItemId);
            } else {
                window.alert("<?php echo $AppUI->_("LBL_WBS_ITEM_HAS_ACTIVITY", UI_OUTPUT_JS); ?>");
            }
        }
    }

    function rightClickMenuDeleteWBSItem() {
        if (contextObject.parentNode.id.indexOf("row_") != -1 || contextObject.parentNode.parentNode.id.indexOf("row_") != -1) {
            var parentId = contextObject.parentNode.id.indexOf("row_") != -1 ? contextObject.parentNode.id : contextObject.parentNode.parentNode.id;
            var wbsItemId = parentId.split("row_")[1];
            deleteWBSItem(wbsItemId);
        }
    }

    function deleteWBSItem(wbsItemId) {
        alertify.confirm(mensagem_2, function () {
            //document.getElementById("delete_wbs_" + wbsItemId).submit();
            
        var form = $("#delete_wbs_" + wbsItemId);
                $.ajax(
                {
                    type: "POST",
                    url: form.attr("action"),
                    data: form.serialize(),
                    success: function (data) {
                        reload();
                        alertify.error(message_3);
                    },
                    dataType: "text"
                });
        }, 
        function() {});
    }

    function createFirstWBSItem() {
        document.getElementById("new_wbs_item_first").submit();
    }

    function rightClickMenuNewActivity() {
        if (contextObject.parentNode.id.indexOf("row_") != -1 || contextObject.parentNode.parentNode.id.indexOf("row_") != -1) {
            var parentId = contextObject.parentNode.id.indexOf("row_") != -1 ? contextObject.parentNode.id : contextObject.parentNode.parentNode.id;
            var wbsItemId = parentId.split("row_")[1];
            newActivity(wbsItemId);
        }
    }

    function rightClickMenuEditEAPItem() {
        if (contextObject.parentNode.id.indexOf("row_") != -1 || contextObject.parentNode.parentNode.id.indexOf("row_") != -1) {
            var parentId = contextObject.parentNode.id.indexOf("row_") != -1 ? contextObject.parentNode.id : contextObject.parentNode.parentNode.id;
            var wbsItemId = parentId.split("row_")[1];
            showWBSItemEdit(wbsItemId);
        }
    }

    function newActivity(wbsItemId) {
        if (document.getElementById("wbs_item_is_leaf_id_" + wbsItemId).value == "1") {
            document.getElementById("new_activity_for_wbs_" + wbsItemId).submit();
        } else {
            window.alert("Ação não permitida!\nApenas pacotes de trabalho (folhas da EAP) podem ter atividades derivadas.");
        }
    }


    function expandControlWorkpackageActivities(id) {
        if (document.getElementById("collapse_icon_" + id).style.display == "none") {
            expandActivities(id);
        } else {
            collapseActivities(id);
        }
    }

    function expandActivities(id) {
        var table = document.getElementById("tb_eap");
        for (var i = 0; i < table.rows.length; i++) {
            row = table.rows[i];
            if (row.id.indexOf("wbs_id_" + id) != -1) {
                row.style.display = "none";
            }
        }
        document.getElementById("collapse_icon_" + id).style.display = "inline";
        document.getElementById("expand_icon_" + id).style.display = "none";
    }

    function collapseActivities(id) {
        var table = document.getElementById("tb_eap");
        for (var i = 0; i < table.rows.length; i++) {
            row = table.rows[i];
            if (row.id.indexOf("wbs_id_" + id) != -1 && row.id.indexOf("activity_details_id_") == -1) {
                row.style.display = "table-row";
            }
        }
        document.getElementById("collapse_icon_" + id).style.display = "none";
        document.getElementById("expand_icon_" + id).style.display = "inline";
    }

    function filterActivitiesByUser() {
        document.select_human_resource_filter_form.submit();
    }

    function viewSequenceActivities() {
        if (parseInt(document.getElementById("activities_count").value) == 0) {
            window.alert(mensagem_1);
        } else {
            window.location = "index.php?m=projects&a=view&project_id=<?php echo $project_id; ?>&show_external_page=/modules/timeplanning/view/projects_mdp.php";
        }
    }

    function showWBSDictionary() {
        var url = replaceAll("#gqs_anchor", "", window.location.href);
        window.location = url + "&show_external_page=/modules/timeplanning/view/projects_wbs_dictionary.php";
    }

    function showActivityDetails(activityId, wbsItemId) {

        if (document.getElementById("activity_effort_edit_" + activityId).style.display != "block") {//expand/collapse just if in read mode
            var el = document.getElementById("activity_details_id_" + activityId + "_wbs_id_" + wbsItemId);
            document.getElementById("activity_responsible_edit_" + activityId).style.display = "none";
            document.getElementById("activity_effort_edit_" + activityId).style.display = "none";

            document.getElementById("activity_resources_edit_" + activityId).style.display = "none";
            document.getElementById("activity_responsible_read_" + activityId).style.display = "inline-block";
            document.getElementById("activity_effort_read_" + activityId).style.display = "inline-block";
            document.getElementById("activity_resources_read_" + activityId).style.display = "inline-block";
            document.getElementById("activity_edit_actions_" + activityId).style.display = "none";
            document.getElementById("activity_sort_id_" + activityId).style.display = "inline-block";
            document.getElementById("activity_code_id_" + activityId).style.display = "inline-block";

            //The set of elements below are always displayed. So the edit/read mode show be controled.
            document.getElementById("activity_rh_edit_id_" + activityId).style.display = "none";
            document.getElementById("activity_date_end_edit_id_" + activityId).style.display = "none";
            document.getElementById("activity_date_start_edit_id_" + activityId).style.display = "none";
            document.getElementById("activity_description_edit_id_" + activityId).style.display = "none";
            document.getElementById("activity_rh_read_id_" + activityId).style.display = "inline-block";
            document.getElementById("activity_date_end_read_id_" + activityId).style.display = "inline-block";
            document.getElementById("activity_date_start_read_id_" + activityId).style.display = "inline-block";
            document.getElementById("activity_description_read_id_" + activityId).style.display = "inline-block";


            if (el.style.display != "none") {
                el.style.display = "none";
            } else {
                el.style.display = "table-row";
            }
        }
    }

    function showWBSItemEdit(wbsItemId) {
        var el = document.getElementById("edit_workpackage_id_" + wbsItemId);
        var el_read = document.getElementById("read_workpackage_id_" + wbsItemId);
        if (el.style.display != "none") {
            el.style.display = "none";
            el_read.style.display = "table-row";
        } else {
            el.style.display = "table-row";
            el_read.style.display = "none";
        }
        document.getElementById("wbs_item_description_" + wbsItemId).focus();


    }


    function showActivityEdit(activityId, wbsItemId) {
        var el = document.getElementById("activity_details_id_" + activityId + "_wbs_id_" + wbsItemId);
        document.getElementById("activity_responsible_edit_" + activityId).style.display = "inline-block";
        document.getElementById("activity_effort_edit_" + activityId).style.display = "inline-block";
        document.getElementById("activity_resources_edit_" + activityId).style.display = "inline-block";
        document.getElementById("activity_responsible_read_" + activityId).style.display = "none";
        document.getElementById("activity_effort_read_" + activityId).style.display = "none";
        document.getElementById("activity_resources_read_" + activityId).style.display = "none";
        document.getElementById("activity_edit_actions_" + activityId).style.display = "inline-block";

        document.getElementById("activity_sort_id_" + activityId).style.display = "none";
        document.getElementById("activity_code_id_" + activityId).style.display = "inline-block";

        //The set of elements below are always displayed. So the edit/read mode show be controled.
        document.getElementById("activity_rh_edit_id_" + activityId).style.display = "inline-block";
        document.getElementById("activity_date_end_edit_id_" + activityId).style.display = "inline-block";
        document.getElementById("activity_date_start_edit_id_" + activityId).style.display = "inline-block";
        document.getElementById("activity_description_edit_id_" + activityId).style.display = "inline-block";

        document.getElementById("activity_rh_read_id_" + activityId).style.display = "none";
        document.getElementById("activity_date_end_read_id_" + activityId).style.display = "none";
        document.getElementById("activity_date_start_read_id_" + activityId).style.display = "none";
        document.getElementById("activity_description_read_id_" + activityId).style.display = "none";

        // if (el.style.display != "none") {
        //      el.style.display = "none";
        // } else {
        el.style.display = "table-row";
        document.getElementById("activity_description_id_" + activityId).focus();
        // }
    }


    function saveActivity(activityId, wbsItemId) {
        var activityDescription=document.getElementById("activity_description_id_"+activityId);
        if(activityDescription !== null && activityDescription.value === "" ){
            alertify.error("<?php echo $AppUI->_("LBL_ACTIVITY_DESCRIPTION_VALIDATION_MESSAGE"); ?>",5);
            activityDescription.style.borderColor="#ff1a1a";
            return false;
         }
        if (validateEstimatedDates()) {
            //showMessage(mensagem_4);
            if (prepareRolesToSave(activityId)) {
               
                //document.getElementById("activity_form_" + activityId).submit();
                 var form = $("#activity_form_" + activityId);
                 $.ajax(
                {
                    type: "POST",
                    url: form.attr("action"),
                    data: form.serialize(),
                    success: function (data) {
                        //alert(data);
                        //form.empty();
                        reload();
                        alertify.success(message_4);
                    },
                    dataType: "text"
                });
               
            }                     
                
        }
        
    }
    

    function cancelSaveActivity(activityId, wbsItemId) {    
         alertify.confirm(mensagem_5, function () {
            reload();
        }, function() {
            // user clicked "cancel"
        });
    }

    function excludeActivity(activityId, wbsItemId) {        
        //document.getElementById("delete_activity_" + activityId).submit();
        var form = $("#delete_activity_" + activityId);
        $.ajax(
        {
            type: "POST",
            url: form.attr("action"),
            data: form.serialize(),
            success: function (data) {
                alertify.error(message_3);
                reload();
            },
            dataType: "text"
        });
        /*
         var row = document.getElementById("wbs_id_" + wbsItemId + "_activity_id_" + activityId);
         var rowDetails = document.getElementById("activity_details_id_" + activityId + "_wbs_id_" + wbsItemId);
         document.getElementById("tb_eap").deleteRow(row.rowIndex);
         document.getElementById("tb_eap").deleteRow(rowDetails.rowIndex);
         */
    }


    function sortWBSItem(direction, wbsItemId) {
        var form = document.getElementById("sort_wbs_" + wbsItemId);
        form.direction.value = direction;
        var formJQ = $("#sort_wbs_" + wbsItemId);
        $.ajax(
        {
            type: "POST",
            url: formJQ.attr("action"),
            data: formJQ.serialize(),
            success: function (data) {
                alertify.success(message_4);
                reload();
            },
            dataType: "text"
        });
        //form.submit();
    }


    function moveRow(direction, rowId, rowDetailsId, taskId) {
        var oTable = document.getElementById("tb_eap");
        var trs = oTable.tBodies[0].getElementsByTagName("tr");
        var i = document.getElementById(rowId).rowIndex;
        var j = i + direction + direction;
        if (j == 0) {
            return false;
        }
        if (i >= 0 && j >= 0 && i < trs.length && j < trs.length) {
            //logical hook:just allow switch beetween activities

            if (oTable.rows[j].id.indexOf("activity_id_") == -1) {
                //alert("Failed hook:"+oTable.rows[j].id);
                return false;
            }

            var form = document.getElementById("sort_activity_" + taskId);
            form.direction.value = direction;
             var formJQ = $("#sort_activity_" + taskId);
            $.ajax(
            {
                type: "POST",
                url: formJQ.attr("action"),
                data: formJQ.serialize(),
                success: function (data) {
                    alertify.success(message_4);
                    reload();
                },
                dataType: "text"
            });
//form.submit();
            //As linhas não serão movimentadas pois
            /*
             if (i == j + 1) {
             oTable.tBodies[0].insertBefore(trs[i], trs[j]);
             } else if (j == i + 1) {
             oTable.tBodies[0].insertBefore(trs[j], trs[i]);
             } else {
             var tmpNode = oTable.tBodies[0].replaceChild(trs[i], trs[j]);
             if (typeof(trs[i]) != "undefined") {
             oTable.tBodies[0].insertBefore(tmpNode, trs[i]);
             } else {
             oTable.appendChild(tmpNode);
             }
             }
             moveRowDetails(direction, rowDetailsId);
             */
        } else {
            //alert("Invalid Values!");
            return false;
        }

    }

    function moveRowDetails(direction, rowId) {
        var oTable = document.getElementById("tbl_project_activities");
        var trs = oTable.tBodies[0].getElementsByTagName("tr");
        var i = document.getElementById(rowId).rowIndex;
        var j = i + direction + direction;
        if (j == 0) {
            return false;
        }
        if (i >= 0 && j >= 0 && i < trs.length && j < trs.length) {
            if (i == j + 1) {
                oTable.tBodies[0].insertBefore(trs[i], trs[j]);
            } else if (j == i + 1) {
                oTable.tBodies[0].insertBefore(trs[j], trs[i]);
            } else {
                var tmpNode = oTable.tBodies[0].replaceChild(trs[i], trs[j]);
                if (typeof (trs[i]) != "undefined") {
                    oTable.tBodies[0].insertBefore(tmpNode, trs[i]);
                } else {
                    oTable.appendChild(tmpNode);
                }
            }
        } else {
            //alert("Invalid Values!");
            return false;
        }
    }

    function addEstimatedRoleHR(taskId, roleId, hrId, quantity) {
        var div = document.getElementById("div_res_" + taskId);
        var roleField = document.createElement("select");
        var hrField = document.createElement("select");
        var removeButton = document.createElement("img");
        var br = document.createElement("br");
        var nextIdField = document.getElementById("roles_num_" + taskId);
        var nextId = nextIdField.value;

        roleField.name = "estimated_role_" + taskId + "_" + nextId;
        roleField.id = "estimated_role_" + taskId + "_" + nextId;
        roleField.className = "text";

        hrField.name = "allocated_hr_role_" + taskId + "_" + nextId;
        hrField.id = "allocated_hr_role_" + taskId + "_" + nextId;
        hrField.className = "text";

        br.id = "br_" + taskId + "_" + nextId;

        removeButton.src = "./modules/dotproject_plus/images/trash_small.gif";
        removeButton.id = nextId;
        removeButton.title = roleId;
        removeButton.name = taskId;
        removeButton.style.cursor = "pointer";
        removeButton.onclick = removeEstimatedRoleHR;

        roleField.options[0] = new Option("<?php echo $AppUI->_("LBL_ROLE"); ?>", "");
        for (i = 0; i < roleIds.length; i++) {
            roleField.options[i + 1] = new Option(roleNames[i], roleIds[i]);
            if (roleId == roleIds[i]) {
                roleField.options[i + 1].selected = true;
                roleField.selectedIndex = i + 1;
            }
        }

        hrField.options[0] = new Option("<?php echo $AppUI->_("Human Resource"); ?>", "");

        //set onchange evento to role select field
        roleField.onchange = function () {
            updateHROptionsBasedOnRole(roleField.id, hrField.id);
        };

        //add all created fields in the screen
        div.appendChild(roleField);
        div.appendChild(hrField);
        div.appendChild(removeButton);
        div.appendChild(br);
        nextIdField.value = parseInt(nextIdField.value) + 1;

        //Set default values on HR field, just if a previusly role had been selected
        if (roleField.selectedIndex > 0) {
            updateHROptionsBasedOnRole(roleField.id, hrField.id);//add hr obtions based on selected role.
            //set the default value based in an previous selected hrId
            for (i = 0; i < hrField.options.length; i++) {
                if (hrId == hrField.options[i].value) {
                    hrField.options[i].selected = true;
                    hrField.selectedIndex = i;
                }
            }
        }

    }

    function removeEstimatedRoleHR() {
        var field = document.getElementById("estimatedRolesExcluded_" + this.name);
        var fieldRemovedRolesIds = document.getElementById("estimatedRolesExcludedIds_" + this.name);
        var idHR = "allocated_hr_role_" + this.name + "_" + this.id;
        var idRole = "estimated_role_" + this.name + "_" + this.id;
        var idBr = "br_" + this.name + "_" + this.id;
        document.getElementById(idHR).style.display = "none";
        document.getElementById(idRole).style.display = "none";
        //document.getElementById(idBr).style.display="none";
        this.style.display = "none";
        var idRoleDb = this.title;
        field.value += field.value == "" ? this.id : "," + this.id;
        fieldRemovedRolesIds.value += fieldRemovedRolesIds.value == "" ? idRoleDb : "," + idRoleDb;
    }

    /**
     * This function is called after the user select a option of an estimated role (onchange, onselect events).
     * It receive as parameter the index of the role in the select options list, and then get the list of names available for this role.
     * A new set of options is built based on this list. 
     */
    function updateHROptionsBasedOnRole(roleSelectFieldId, hrSelectFieldId) {
        var roleSelectField = document.getElementById(roleSelectFieldId);
        var roleIndex = roleSelectField.selectedIndex - 1;//-1 because the first option of this field is invalid (the field label)
        var hrSelectField = document.getElementById(hrSelectFieldId);
        var hrs = hrPerRole[roleIndex];
        var hr = null;
        var hr_id = null;
        var hr_name = null;
        var option = null;
        var optGroup = null;

        if (roleIndex > -1) {
            //remove all options from the select field
            while (hrSelectField.options.length > 0) {
                hrSelectField.options.remove(0)
            }
            //remove all optgroups from the select field
            var ogl = hrSelectField.getElementsByTagName('optgroup');
            for (var i = ogl.length - 1; i >= 0; i--) {
                hrSelectField.removeChild(ogl[i])
            }

            //include options and optgroups
            option = document.createElement("option");
            option.text = "<?php echo $AppUI->_("Human Resource") ?>";
            option.value = "";
            hrSelectField.add(option);
            optGroup = document.createElement("optgroup");
            optGroup.label = roleSelectField.options[roleSelectField.selectedIndex].text;
            hrSelectField.add(optGroup);
            for (i = 0; i < hrs.length; i++) {
                hr = hrs[i].split("#!");
                if (hr.length == 2) {
                    hr_id = hr[0];
                    hr_name = hr[1];
                    option = document.createElement("option");
                    option.text = hr_name;
                    option.value = hr_id;
                    hrSelectField.add(option);
                }
            }
            optGroup = document.createElement("optgroup");
            //include all other hr that still does not contain the role
            optGroup.label = "Outros";
            hrSelectField.add(optGroup);
            for (i = 0; i < hrIds.length; i++) {
                if (hrs.indexOf(hrIds[i] + "#!" + hrNames[i]) == -1) {//include just HR that do not are in the hrs(hr per role) array
                    option = new Option(hrNames[i], hrIds[i]);
                    hrSelectField.add(option);
                }
            }
        }
    }
    /**
     * Function to assist the filling of activity dates.
     * When the user select the start date, if it is after the current end date, or if it is empty, the it is filled with the same value as the start date.
     
     * @returns {void}     */
    function updateActivityDateOnChange(activityId, dateSeparator) {
        try {
            var start_field = document.getElementById("planned_start_date_activity_" + activityId);
            var end_field = document.getElementById("planned_end_date_activity_" + activityId);
            var date_parts = null;
            var end_date_parts = null;
            var startDate = null;
            var endDate = null;
            var updateEndDateField = false;
            if (start_field.value.length == 10) {
                date_parts = start_field.value.split(dateSeparator);
                startDate = new Date(date_parts[2], date_parts[1], date_parts[0], 0, 0, 0, 0);
                if (end_field.value.length == 10) {
                    end_date_parts = end_field.value.split(dateSeparator);
                    endDate = new Date(end_date_parts[2], end_date_parts[1], end_date_parts[0], 0, 0, 0, 0);
                    if (startDate.getTime() > endDate.getTime()) {
                        updateEndDateField = true;
                    }
                } else {
                    updateEndDateField = true;

                }
                if (updateEndDateField) {
                    end_field.value = date_parts[0] + dateSeparator + date_parts[1] + dateSeparator + date_parts[2];
                }
            }
        } catch (e) {
            console.log("Erro on function - updateActivityDateOnChange:" + e);
        }
    }

    /*
     * Funtion for disable/enable activity duration fields based on activity effort.
     * It means,to enable the activity duration, the effort must be estimated
     * This function is called after the rendering of these fields, and onchange of effort field.
     */
    function enableDurationBasedOnEffort(activityId) {
        if (activityId != "") {
            var startDateInput = document.getElementById("planned_start_date_activity_" + activityId);
            var endDateInput = document.getElementById("planned_end_date_activity_" + activityId);
            var effortInput = document.getElementById("planned_effort_" + activityId);
            var calendarIcon1 = document.getElementById("calendar_trigger_1_" + activityId);
            var calendarIcon2 = document.getElementById("calendar_trigger_2_" + activityId);
            var messagePanel = document.getElementById("message_effort_for_duration_" + activityId);
            var effort = effortInput.value;


            if (effort != "" && effort != 0 && !isNaN(parseInt(effort))) {
                endDateInput.disabled = false;
                startDateInput.disabled = false;
                calendarIcon1.style.visibility = "visible";
                calendarIcon2.style.visibility = "visible";
                messagePanel.style.visibility = "hidden";
            } else {
                endDateInput.disabled = true;
                startDateInput.disabled = true;
                calendarIcon1.style.visibility = "hidden";
                calendarIcon2.style.visibility = "hidden";
                messagePanel.style.visibility = "visible";
            }
        }
    }

</script>

<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/model/project_task_estimation.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/model/wbs_item_estimation.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_items.class.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_wbs_item_activity_relationship.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/control/controller_company_role.class.php");
require_once (DP_BASE_DIR . "/modules/tasks/tasks.class.php");
require_once (DP_BASE_DIR . "/modules/projects/projects.class.php");

$projectId = dPgetParam($_GET, 'project_id', 0);
$activitiesIdsForDisplay; //updated by /modules/timeplanning/view/export_project_plan/time_planning_initializing_logical_ids.php
require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/time_planning_initializing_logical_ids.php");



if ($_GET["show_external_page"] != "") {
    include_once DP_BASE_DIR . $_GET["show_external_page"];
} else {
    
    $project = new CProject();
    $project->load($project_id);
    global $pstatus;
    $controllerWBSItem = new ControllerWBSItem();
    $ControllerWBSItemActivityRelationship = new ControllerWBSItemActivityRelationship();
    $controllerCompanyRole = new ControllerCompanyRole();
    $items = $controllerWBSItem->getWorkPackages($project_id);
//start: build the roles list

    $roles = $controllerCompanyRole->getCompanyRoles($project->project_company);
    ?>
    <script>
        var roleIds = new Array();
        var roleNames = new Array();
        var hrIds = new Array();
        var hrNames = new Array();
        var hrPerRole = new Array();//array to display the hr that have the role assigned.
    <?php
    $i = 0;
    foreach ($roles as $role) {
        $roles[$role->getId()] = $role->getDescription();
        ?>
            roleNames[<?php echo $i ?>] = "<?php echo $role->getDescription() ?>";
            roleIds[<?php echo $i ?>] = "<?php echo $role->getId() ?>";
            hrPerRole[<?php echo $i ?>] = new Array();
        <?php
        //start: build human resources list per role

        $q = new DBQuery();
        $q->addTable('contacts', 'c');
        $q->addQuery('user_id, h.human_resource_id, contact_id,u.user_username, contact_last_name, contact_first_name');
        $q->innerJoin('users', 'u', 'u.user_contact = c.contact_id');
        $q->innerJoin('human_resource', 'h', 'h.human_resource_user_id = u.user_id');
        $q->innerJoin('human_resource_roles', 'hr_roles', 'hr_roles.human_resource_id =h.human_resource_id and hr_roles.human_resources_role_id=' . $role->getId());
        $q->addWhere('c.contact_company = ' . $project->project_company);
        $q->addOrder("u.user_username");
        $sql = $q->prepare();
        $records = db_loadList($sql);
        $j = 0;
        foreach ($records as $record) {
            $userNameByHRid[$record[1]] = $record[5]. " ". $record[4];
            ?>
                hrPerRole[<?php echo $i ?>][<?php echo $j ?>] = "<?php echo $record[1] . "#!" . $record[5]. " ". $record[4] ?>";
            <?php
            $j++;
        }
        //end: build human resources list per role
        $i++;
    }
//end: build the roles list
//start: build hr list
    $q = new DBQuery();
    $q->addTable('contacts', 'c');
    $q->addQuery('user_id, human_resource_id, contact_id,u.user_username, contact_last_name, contact_first_name');
    $q->innerJoin('users', 'u', 'u.user_contact = c.contact_id');
    $q->innerJoin('human_resource', 'h', 'h.human_resource_user_id = u.user_id');
    $q->addWhere('c.contact_company = ' . $project->project_company);
    $q->addOrder("u.user_username");
    $sql = $q->prepare();
    $records = db_loadList($sql);
    $i = 0;
    $userNameByHRid = array();
    foreach ($records as $record) {
        $userNameByHRid[$record[1]] = $record[5]. " ". $record[4];
        ?>
            hrNames[<?php echo $i ?>] = "<?php echo $record[5]. " ". $record[4] ?>";
            hrIds[<?php echo $i ?>] = "<?php echo $record[1] ?>";
        <?php
        $i++;
    }
//end: build hr list
//start: buld hr list per role
//end: buld hr list per role 
    ?>
    </script>
    <?php
    $currentPage = substr($_SERVER["REQUEST_URI"], strpos($_SERVER["REQUEST_URI"], "index.php") + 9);
    ?>
    <br />
    <div style="text-align:right">
        <form name="select_human_resource_filter_form" action="<?php echo $currentPage ?>" method="post">
            <span style="color:#000000"><?php echo $AppUI->_("LBL_FILTER"); ?>:</span>
            <select id="project_resources_filter" name="project_resources_filter" onchange="filterActivitiesByUser()"> <!-- Filter to select activities for just a resource -->
                <option <?php echo $_POST["project_resources_filter"] == "" ? "selected" : "" ?>   value=""><?php echo $AppUI->_("All"); ?></option>
                <?php
                foreach ($records as $record) {
                    ?>
                    <option <?php echo $_POST["project_resources_filter"] == $record[1] ? "selected" : "" ?> value="<?php echo $record[1] ?>"> 
                        <?php echo $record[3] ?>
                    </option>
                    <?php
                }
                ?>
            </select>

            <?php
            //verify if there is some activity defined in the entire project
            $q = new DBQuery();
            $q->addQuery("t.task_id");
            $q->addTable("tasks", "t");
            $q->addWhere("t.task_project=" . $project_id);
            $sql = $q->prepare();
            $records = db_loadList($sql);
            $activitiesCount = count($records);
            ?>
            <input type="hidden" name="activities_count" id="activities_count" value="<?php echo $activitiesCount ?>" />
            <input class="button" type="button" value="<?php echo $AppUI->_("LBL_PROJECT_PROJECT_SEQUENCING") ?>" onclick="viewSequenceActivities()" /> <!-- Link to screen for sequencing activities -->
           <!-- <input class="button" type="button" value="<?php echo $AppUI->_("LBL_WBS_DICTIONARY") ?>" onclick="window.location = 'index.php?a=view&m=projects&project_id=<?php echo $project_id ?>&tab=1&show_external_page=/modules/timeplanning/view/projects_wbs_dictionary.php#gqs_anchor';" /> -->
            <input class="button" type="button" value="<?php echo $AppUI->_("LBL_NEED_FOR_TRAINING") ?>" onclick="window.location = 'index.php?a=view&m=projects&project_id=<?php echo $project_id ?>&tab=1&show_external_page=/modules/timeplanning/view/need_for_training.php#gqs_anchor';" /> 
            <input class="button" type="button" value="<?php echo $AppUI->_("LBL_MINUTES_ESTIMATION_MEETINGS") ?>" onclick="window.location = 'index.php?a=view&m=projects&project_id=<?php echo $project_id ?>&tab=1&show_external_page=/modules/timeplanning/view/projects_estimations_minutes.php#gqs_anchor';" /> 
            <input class="button" type="button" value="<?php echo $AppUI->_("LBL_COPY_FROM_TEMPLATE") ?>" onclick="dialogCopyProject.dialog('open')" />            
        </form>
    </div>
    <?php require_once (DP_BASE_DIR . "/modules/dotproject_plus/copy_project/copy_project_view.php"); ?>
    <br />

    <div id="estimation_form_error_message"> </div>
    <span style="color:red">*</span>&nbsp;<span style="color:#000000"> <?php echo $AppUI->_("LBL_RIGHT_CLICK_ADD_ACTIVITY"); ?> </span>
    <div id="div_eap_activities">
        <table id="tb_eap"  class="tbl" align="center" width="100%" border="0" onmouseover="setContextDisabled(false)" onmouseout="setContextDisabled(true)">
            <tr>
                <th style="width:40%" colspan="2">
                    <?php echo $AppUI->_("LBL_ACTIVITY"); ?>
                </th>
                <th nowrap>
                    <?php echo $AppUI->_("LBL_BEGIN"); ?>
                </th>
                <th nowrap>
                    <?php echo $AppUI->_("LBL_END"); ?>
                </th>
                <th nowrap>
                    <?php echo $AppUI->_("LBL_DURATION"); ?>
                </th>
                <th nowrap>
                    <?php echo $AppUI->_("Human Resources"); ?>
                </th>
                <th nowrap>
                    <?php echo $AppUI->_("Status"); ?>
                </th>
            </tr>

            <?php
            $project = new CProject();
            $project->load($project_id);
            $company_id = $project->project_company;
            $showFirstActivityCreation = false; //this variable make the controlling to showing of the message to create the first activity 
            $items = $controllerWBSItem->getWBSItems($project_id);

            if (count($items) == 0) {
                ?>
                <tr>
                    <td colspan="9" onmouseover="setContextDisabled(false)">
                        * <?php echo $AppUI->_("LBL_CLICK") ?> <a href="#" onclick="createFirstWBSItem()"><b><u><?php echo $AppUI->_("LBL_HERE") ?></u></b></a> <?php echo $AppUI->_("LBL_CREATE_NEW_WBS_ITEM") ?>
                        <form name="new_wbs_item_first" id="new_wbs_item_first" method="post" action="?m=dotproject_plus">
                            <input name="dosql" type="hidden" value="do_new_wbs_item" />
                            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
                            <input type="hidden" name="parent_id" value="" />
                            <input type="hidden" name="sort_order" value="1" />         
                            <input type="hidden" id="identation_field" name="identation_field" value="" />
                            <input type="hidden" id="number_field" name="number_field" value="1" />
                            <input type="hidden" id="leaf_field" name="leaf_field" value="1" />
                        </form>
                    </td>
                </tr>

                <?php
            } else {
                $wbs_items_order = 0;
                ?>
                <script>
                    var wbsHasActivity = new Array();//stores WBS item id, and a boolean informing if it have or not activities
                </script>
                <?php
                foreach ($items as $item) {
                    $id = $item->getId();
                    $name = $item->getName();
                    $identation = $item->getIdentation();
                    $number = $item->getNumber();
                    /* verify if wbs item is leaf: above item
                     * 1. Above Item less identation and below more identation
                     * 2. First and last: no above and no below
                     * 3. Last item: no below
                     */

                    $i = $wbs_items_order;
                    $is_leaf = "0";


                    if (count($ControllerWBSItemActivityRelationship->getActivitiesByWorkPackage($id)) > 0) {
                        $is_leaf = "1"; //bug fix: ensure wound not exist lost activities.
                    } else if ($i == 0 && count($items) == 1) { // there is just an item
                        $is_leaf = "1";
                    } else if ($i == (count($items) - 1)) { //is the last item
                        $is_leaf = "1";
                    } else if (($i > 0 && $i < (count($items) - 1)) && (strlen($items[$i + 1]->getIdentation()) <= strlen($identation))) { // is not the first and not the last and is a leaf
                        $is_leaf = "1";
                    }

                    $wbs_items_order++;
                    $order = $wbs_items_order;
                    //update the sort order and is leaf attributes of WBS items.
                    $q = new DBQuery();
                    $q->addTable("project_eap_items");
                    $q->addUpdate("sort_order", $order);
                    $q->addUpdate("is_leaf", $is_leaf);
                    $q->addWhere("id= $id");
                    $q->exec();
                    ?>

                    <!-- define forms for wbs actions -->
                    <form name="new_wbs_item_<?php echo $id ?>" id="new_wbs_item_<?php echo $id ?>" method="post" action="?m=dotproject_plus">
                        <input name="dosql" type="hidden" value="do_new_wbs_item" />
                        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
                        <input type="hidden" name="parent_id" value="<?php echo $id; ?>" />
                        <!-- The order is increased in 1 and the identation is increased to characterize the new item is a child of the current one -->
                        <input type="hidden" name="sort_order" value="<?php echo ($order); ?>" />    
                        <input type="hidden" id="number_parent" name="number_parent" value="<?php echo $number; ?>" />   
                        <input type="hidden" id="identation_field" name="identation_field" value="<?php echo $identation; ?>&nbsp;&nbsp;&nbsp;" />
                        <input type="hidden" id="number_field_<?php echo $id ?>" name="number_field_<?php echo $id ?>" />
                        <input type="hidden" id="leaf_field_<?php echo $id ?>" name="leaf_field_<?php echo $id ?>" />
                    </form>

                    <form name="new_activity_for_wbs_<?php echo $id ?>" id="new_activity_for_wbs_<?php echo $id ?>" method="post" action="?m=dotproject_plus">
                        <input name="dosql" type="hidden" value="do_new_activity" />
                        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
                        <input type="hidden" name="wbs_item_id" value="<?php echo $id; ?>" />
                        <input type="hidden" name="wbs_item_is_leaf_id_<?php echo $id ?>" id="wbs_item_is_leaf_id_<?php echo $id ?>" value="<?php echo $is_leaf; ?>" />
                    </form>

                    <form name="delete_wbs_<?php echo $id ?>" id="delete_wbs_<?php echo $id ?>" method="post" action="?m=dotproject_plus">
                        <input name="dosql" type="hidden" value="do_delete_wbs_item" />
                        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
                        <input type="hidden" name="wbs_item_id" value="<?php echo $id; ?>" />
                        <input type="hidden" name="wbs_item_name" value="<?php echo $name; ?>" />
                    </form>

                    <form name="sort_wbs_<?php echo $id ?>" id="sort_wbs_<?php echo $id ?>" method="post" action="?m=dotproject_plus">
                        <input name="dosql" type="hidden" value="do_save_wbs_order" />
                        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
                        <input type="hidden" name="wbs_id" value="<?php echo $id ?>" />
                        <input type="hidden" name="direction" value="0" />
                        <input type="hidden" name="wbs_order" value="<?php echo $order ?>" />
                    </form>

                    <?php
                    if ($is_leaf == "1") {
                        $eapItem = new WBSItemEstimation();
                        $eapItem->load($id);
                        //start: code to filter workpakage activities
                        $tasks = $ControllerWBSItemActivityRelationship->getActivitiesByWorkPackage($id);
                        $hasActivities = sizeof($tasks) > 0 ? true : false;
                        ?>
                        <tr id="row_<?php echo $id ?>"  ondblclick="expandControlWorkpackageActivities(<?php echo $id ?>)" style="cursor:pointer">                       

                            <td style="background-color: #E8E8E8;height:  35px;min-width:50px; " colspan="7">
                                <span id="read_workpackage_id_<?php echo $id ?>">
                                    <span id="identation_<?php echo $id ?>" style="color: #E8E8E8"><?php echo $identation; ?></span>
                                    <!-- To enlarge the identation space -->
                                    <?php echo strlen($identation) > 0 ? "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" : ""; ?>
                                    <span style="position: absolute;margin-top: -8px;">
                                        <img src="./modules/dotproject_plus/images/sort_icon_up.bmp" onclick="sortWBSItem(-1, '<?php echo $id ?>');" id="sort_icon_<?php echo $id ?>" style="cursor:pointer;" />
                                        <br />
                                        <img src="./modules/dotproject_plus/images/sort_icon_down.bmp" onclick="sortWBSItem(1, '<?php echo $id ?>');" id="sort_icon_<?php echo $id ?>" style="cursor:pointer;" />                                  
                                    </span>
                                    <span style="margin-left: 21px" id="div_numbering_<?php echo $id ?>">
                                        <?php echo $number ?>
                                    </span>
                                    <?php echo $name ?>
                                    &nbsp;
                                    <span style="display:<?php echo $hasActivities ? "inline" : "none" ?>" >
                                        (<?php echo sizeof($tasks) ?>)
                                        &nbsp;&nbsp;&nbsp;
                                        <img src="./modules/dotproject_plus/images/icone_seta.png" onclick="collapseActivities(<?php echo $id ?>)" id="collapse_icon_<?php echo $id ?>" style="cursor:pointer;display:none" />
                                        <img src="./modules/dotproject_plus/images/icone_seta_cima.png" onclick="expandActivities(<?php echo $id ?>)" id="expand_icon_<?php echo $id ?>" style="cursor:pointer" />
                                    </span>
                                </span>
                                <span id="edit_workpackage_id_<?php echo $id ?>" style="display:none">
                                    <form name="save_wbs_<?php echo $id ?>" id="save_wbs_<?php echo $id ?>" method="post" action="?m=dotproject_plus">
                                        <input name="dosql" type="hidden" value="do_save_wbs" />
                                        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
                                        <input type="hidden" name="wbs_item_id" value="<?php echo $id; ?>" />
                                        <input type="hidden" id="identation_field_<?php echo $id ?>" name="identation_field_<?php echo $id ?>" value="<?php echo $identation; ?>" />
                                        <input type="hidden" id="number_field_<?php echo $id ?>_unique" name="number_field_<?php echo $id ?>" value="<?php echo $number ?>" />
                                        <input type="hidden" id="leaf_field_<?php echo $id ?>_unique" name="leaf_field_<?php echo $id ?>" value="<?php echo $is_leaf ?>" />
                                        <input type="hidden" name="wbs_item_order_<?php echo $id ?>" value="<?php echo $order; ?>" />
                                        <?php echo $AppUI->_("LBL_DESCRICAO") ?>: <input type="text"  class="text" value="<?php echo $name ?>" id="wbs_item_description_<?php echo $id ?>" name="wbs_item_description_<?php echo $id ?>" size="35" maxlength="50"  />
                                        &nbsp;
                                        <?php echo $AppUI->_("LBL_TAMANHO") ?>: <input type="text"  class="text" value="<?php echo $eapItem->getSize() ?>" maxlength="10" size="15"  name="estimated_size_<?php echo $id ?>" />
                                        &nbsp;
                                        <?php echo $AppUI->_("LBL_UNITY") ?>:<input type="text" class="text" name="estimated_size_unit_<?php echo $id ?>"  maxlength="30" size="25"  value="<?php echo $eapItem->getSizeUnit() ?>"  />
                                        <span style="text-align: right;width:100%">
                                            <input class="button" type="button" value="<?php echo $AppUI->_("LBL_SAVE"); ?>" onclick="saveWBSItem(<?php echo $id ?>)" />
                                            <input class="button"type="button" value="<?php echo ucfirst($AppUI->_("LBL_CANCEL")); ?>" onclick="cancelSaveWBSItem(<?php echo $id ?>)" />                  
                                        </span>
                                    </form>
                                </span>
                            </td>

                        <script>
                            wbsHasActivity[<?php echo $id ?>] = <?php echo sizeof($tasks) > 0 ? "true" : "false" ?>;
                        </script>
                        <?php
                        if ($activitiesCount == 0 && !$showFirstActivityCreation) {
                            $showFirstActivityCreation = true;
                            ?>
                            <tr>
                                <td colspan="9" onmouseover="setContextDisabled(false)" id="new_activity_wbs_item_id_<?php echo $id ?>">
                                    <span style="color:red">*</span> <?php echo $AppUI->_("LBL_RIGHT_CLICK_NEW_ACTIVITY"); ?>
                                    <!-- createFirstActivity(<?php echo $id ?>) -->
                                    <form name="new_first_activity_for_an_wbs_id_<?php echo $id ?>" id="new_first_activity_for_an_wbs_id_<?php echo $id ?>" method="post" action="?m=dotproject_plus">
                                        <input name="dosql" type="hidden" value="do_new_activity" />
                                        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
                                        <input type="hidden" name="wbs_item_id" value="<?php echo $id ?>" />
                                    </form>
                                </td>
                            </tr>
                            <?php
                        } else {
                            foreach ($tasks as $obj) {
                                $task_id = $obj->task_id;
                                $taskDescription = $obj->task_name;
                                $projectTaskEstimation = new ProjectTaskEstimation();
                                $projectTaskEstimation->load($task_id);

                                //duration and start/end dates.
                                $obj = new CTask();
                                $obj->load($task_id);
                                $startDateTxt = "";
                                $endDateTxt = "";
                                if (isset($obj->task_start_date) && isset($obj->task_end_date)) {
                                    $startDateTxt = date("d/m/Y", strtotime($obj->task_start_date));
                                    $endDateTxt = date("d/m/Y", strtotime($obj->task_end_date));
                                }
                                $duration = "";
                                if ($projectTaskEstimation->getDuration() != "") {
                                    $duration = "" . $projectTaskEstimation->getDuration() . " " . $AppUI->_("LBL_PROJECT_DAYS_MULT");
                                }

                                if ($taskDescription != "") { //start: build line for task
                                    // Get estimate roles if have some
                                    $estimatedRolesTxt = "";
                                    foreach ($projectTaskEstimation->getRoles() as $role) {
                                        $roleId = $role->getRoleId();
                                        $roleName = $roles[$roleId];
                                        $roleQuantity = $role->getQuantity();
                                        $estimatedRolesTxt .= $roleName . " (" . $roleQuantity . ") <br />";
                                    }
                                    //metric index is db key
                                    $effortMetrics = array();
                                    $effortMetrics[0] = $AppUI->_("LBL_EFFORT_HOURS");
                                    $effortMetrics[1] = $AppUI->_("LBL_EFFORT_MINUTES");
                                    $effortMetrics[2] = $AppUI->_("LBL_EFFORT_DAYS");

                                    //Get activity RHs
                                    $q = new DBQuery();
                                    $q->addQuery("u.user_username,u.user_id");
                                    $q->addTable("project_tasks_estimated_roles", "tr");
                                    $q->addJoin("human_resource_allocation", "hr_al ", "hr_al.project_tasks_estimated_roles_id=tr.id");
                                    $q->addJoin("human_resource", "hr ", " hr_al.human_resource_id=hr.human_resource_id");
                                    $q->addJoin("users", "u", " hr.human_resource_user_id=u.user_id");
                                    $q->addWhere("tr.task_id=" . $task_id);
                                    $sql = $q->prepare();
                                    $RHrecords = db_loadList($sql);

                                    //Define text for read Human resources
                                    $estimatedRolesTxt = "";
                                    $rolesNonGrouped = $projectTaskEstimation->getRolesNonGrouped($task_id);
                                    $totalRoles = count($rolesNonGrouped);
                                    $i = 1; //It avoid the inclusion of a comma in the text to display the human resources
                                    if ($_POST["project_resources_filter"] == "") {
                                        $hasFilteredRH = true; //control if will be some filter in based on human resource
                                    } else {
                                        $hasFilteredRH = false;
                                    }
                                    foreach ($rolesNonGrouped as $role) {
                                        $role_estimated_id = $role->getQuantity(); // the quantity field is been used to store the estimated role id
                                        $allocated_hr_id = ""; //Get the allocated HR  (maybe there is just the role without allocation, in this case write the role name)          
                                        //Get id of a possible old allocation to delete it
                                        $q = new DBQuery();
                                        $q->addTable("human_resource_allocation");
                                        $q->addQuery("human_resource_id");
                                        $q->addWhere("project_tasks_estimated_roles_id=" . $role_estimated_id);
                                        $sql = $q->prepare();
                                        $records = db_loadList($sql);
                                        foreach ($records as $record) {
                                            $allocated_hr_id = $record[0];
                                        }
                                        if ($allocated_hr_id != "") {
                                            $estimatedRolesTxt.=$userNameByHRid[$allocated_hr_id]; //write user name
                                        } else {
                                            $estimatedRolesTxt.="<i style='color:red'>" . $roles[$role->getRoleId()] . "</i>";
                                        }
                                        if ($totalRoles > $i) {
                                            $estimatedRolesTxt.=", ";
                                        }
                                        $i++;
                                        if (!$hasFilteredRH && $_POST["project_resources_filter"] == $allocated_hr_id) {
                                            $hasFilteredRH = true;
                                        }
                                    }

                                    if ($hasFilteredRH) {
                                        $rowId = "wbs_id_" . $id . "_activity_id_" . $obj->task_id;
                                        $rowDetailsId = "activity_details_id_" . $obj->task_id . "_wbs_id_" . $id;
                                        ?>


                                        <form name="delete_activity_<?php echo $obj->task_id ?>" id="delete_activity_<?php echo $obj->task_id ?>" method="post" action="?m=dotproject_plus">
                                            <input name="dosql" type="hidden" value="do_delete_activity" />
                                            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
                                            <input type="hidden" name="activity_id" value="<?php echo $obj->task_id ?>" />
                                            <input type="hidden" name="activity_name" value="<?php echo $taskDescription ?>" />       
                                        </form>

                                        <form name="sort_activity_<?php echo $obj->task_id ?>" id="sort_activity_<?php echo $obj->task_id ?>" method="post" action="?m=dotproject_plus">
                                            <input name="dosql" type="hidden" value="do_save_activity_order" />
                                            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
                                            <input type="hidden" name="activity_id" value="<?php echo $obj->task_id ?>" />
                                            <input type="hidden" name="direction" value="0" />
                                            <input type="hidden" name="wbs_item_id" value="<?php echo $id ?>" />                                                                                      
                                       
                                        </form>

                                        <form name="activity_form_<?php echo $obj->task_id ?>" id="activity_form_<?php echo $obj->task_id ?>" method="post" action="?m=dotproject_plus">
                                            <input name="dosql" type="hidden" value="do_save_activity_estimations" />
                                            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
                                            <input type="hidden" name="activity_id" value="<?php echo $obj->task_id ?>" />
                                            <input type="hidden" name="tab" value="<?php echo $_GET["tab"] ?>" />

                                            <tr id="<?php echo $rowId; ?>" ondblclick="showActivityDetails(<?php echo $task_id ?>,<?php echo $id ?>)" style="cursor:pointer;height:  30px">
                                                <td style="width:200px;min-height: 30px;height: 30px" colspan="2">
                                                    <span style="color:#FFFFFF">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><!-- Identation between eap items and activities -->
                                                    <span style="color:#FFFFFF"><?php echo $identation; ?></span>

                                                    <table id="activity_sort_id_<?php echo $task_id ?>" border="0" style="display:inline-block">
                                                        <tr>
                                                            <td>
                                                                <img src="./modules/dotproject_plus/images/sort_icon_up.bmp" onclick="moveRow(-1, '<?php echo $rowId; ?>', '<?php echo $rowDetailsId ?>',<?php echo $task_id ?>);" id="sort_icon_<?php echo $id ?>" style="cursor:pointer;" />                                                    
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <img src="./modules/dotproject_plus/images/sort_icon_down.bmp" onclick="moveRow(1, '<?php echo $rowId; ?>', '<?php echo $rowDetailsId ?>',<?php echo $task_id ?>);" id="sort_icon_<?php echo $id ?>" style="cursor:pointer;" />              
                                                            </td>
                                                        </tr>
                                                    </table>

                                                    <span style="margin-left: 21px; vertical-align: top;" id="activity_code_id_<?php echo $task_id ?>"> A.<?php echo $activitiesIdsForDisplay[$task_id] ?></span>

                                                    <div id="activity_description_read_id_<?php echo $task_id ?>" style="width:160px;float:right">          
                                                        <?php echo $taskDescription ?>         
                                                    </div>
                                                    <span id="activity_description_edit_id_<?php echo $task_id ?>" style="display:none;" nowrap="nowrap">
                                                        <input name="activity_description_id_<?php echo $task_id ?>" id="activity_description_id_<?php echo $task_id ?>" class="text" style="width:200px;margin-left: 4px;margin-bottom: 4px;" type="text" value="<?php echo $taskDescription ?>" />
                                                    </span>
                                                </td>
                                                <td style="text-align: center" nowrap="nowrap"> 
                                                    <span id="activity_date_start_read_id_<?php echo $task_id ?>">
                                                        <?php echo $startDateTxt ?> 
                                                    </span>
                                                    <span id="activity_date_start_edit_id_<?php echo $task_id ?>" style="display:none">
                                                        <input type="text" class="text" name="planned_start_date_activity_<?php echo $task_id ?>" id="planned_start_date_activity_<?php echo $task_id ?>" placeholder="dd/mm/yyyy" size="12" maxlength="10" value="<?php echo $startDateTxt ?>" onchange="updateActivityDateOnChange(<?php echo $task_id ?>, '/')" /> 
                                                        <img src="./modules/timeplanning/images/img.gif" id="calendar_trigger_1_<?php echo $task_id ?>" style="cursor:pointer" onclick="displayCalendar(document.getElementById('planned_start_date_activity_<?php echo $task_id ?>'), 'dd/mm/yyyy', this)" />
                                                    </span>
                                                </td>                           
                                                <td style="text-align: center" nowrap="nowrap"> 
                                                    <span id="activity_date_end_read_id_<?php echo $task_id ?>">
                                                        <?php echo $endDateTxt ?>
                                                    </span>
                                                    <span id="activity_date_end_edit_id_<?php echo $task_id ?>" style="display:none">
                                                        <input type="text" class="text" name="planned_end_date_activity_<?php echo $task_id ?>" id="planned_end_date_activity_<?php echo $task_id ?>" placeholder="dd/mm/yyyy" size="12" maxlength="10" value="<?php echo $endDateTxt ?>" /> 
                                                        <img src="./modules/timeplanning/images/img.gif" id="calendar_trigger_2_<?php echo $task_id ?>" style="cursor:pointer" onclick="displayCalendar(document.getElementById('planned_end_date_activity_<?php echo $task_id ?>'), 'dd/mm/yyyy', this)" />
                                                    </span>
                                                </td>
                                                <td style="text-align: center;width:100px"> <?php echo $duration ?> </td>
                                                <td nowrap="nowrap" style="width:200px">
                                                    <span id="activity_rh_read_id_<?php echo $task_id ?>">
                                                        <?php
                                                        echo $estimatedRolesTxt;
                                                        ?> 
                                                    </span>
                                                    <span id="activity_rh_edit_id_<?php echo $task_id ?>" style="display:none;">
                                                        &nbsp;
                                                    </span>
                                                </td>
                                                <td style="text-align: center;width:100px">
                                                    <?php
                                                    if (!isset($_GET["execution_mode"]) && $_GET["tab"] != 2) { //tab equals 3 is the parameter 
                                                        ?>
                                                        <input type="hidden" name="task_percent_complete" value="<?php echo $obj->task_percent_complete ?>" />

                                                        <?php
                                                        $activity_status = "";
                                                        switch ($obj->task_percent_complete) {
                                                            case 0:
                                                                $activity_status = $AppUI->_("LBL_ACTIVITY_STATUS_NOT_INITIATED");
                                                                break;
                                                            case 100:
                                                                $activity_status = $AppUI->_("LBL_ACTIVITY_STATUS_CONCLUDED");
                                                                break;
                                                            default:
                                                                $activity_status = $AppUI->_("LBL_ACTIVITY_STATUS_WORKING_ON_IT");
                                                                ;
                                                                break;
                                                        }
                                                        echo $activity_status;
                                                    } else {
                                                        ?>                                              
                                                        <select name="task_percent_complete">
                                                            <option <?php echo $obj->task_percent_complete == 0 ? "selected" : ""; ?> value="0"><?php echo $AppUI->_("LBL_ACTIVITY_STATUS_NOT_INITIATED"); ?></option>
                                                            <option <?php echo $obj->task_percent_complete > 0 && $obj->task_percent_complete < 100 ? "selected" : ""; ?> value="50"><?php echo $AppUI->_("LBL_ACTIVITY_STATUS_WORKING_ON_IT"); ?></option>
                                                            <option <?php echo $obj->task_percent_complete == 100 ? "selected" : ""; ?> value="100"><?php echo $AppUI->_("LBL_ACTIVITY_STATUS_CONCLUDED"); ?></option>
                                                        </select>
                                                        &nbsp;
                                                        <input class="button" type="button" value="Salvar" onclick="saveActivity(<?php echo $task_id ?>, <?php echo $id ?>)" />
                                                        <?php
                                                    }
                                                    ?>         
                                                </td>
                                            </tr> 
                                            <tr id="<?php echo $rowDetailsId ?>" style="display:none;height: 40px " > 
                                                <td colspan="7" style="vertical-align: top;padding: 6px;">
                                                    <table width="99%" align="center">
                                                        <tr>
                                                            <td nowrap="nowrap" style="vertical-align: top">

                                                                <span id="activity_responsible_edit_<?php echo $task_id ?>" style="vertical-align: top">
                                                                    <?php echo $AppUI->_("LBL_OWNER"); ?>:
                                                                    <select class="text" name="task_owner_<?php echo $obj->task_id ?>">
                                                                        <?php
                                                                        $query = new DBQuery();
                                                                        $query->addTable("users", "u");
                                                                        $query->addQuery("user_id, user_username, contact_last_name, contact_first_name, contact_id");
                                                                        $query->addJoin("contacts", "c", "u.user_contact = c.contact_id");
                                                                        $query->addWhere("c.contact_company = " . $company_id);
                                                                        $query->addOrder("contact_last_name");
                                                                        $res = & $query->exec();
                                                                        for ($res; !$res->EOF; $res->MoveNext()) {
                                                                            $user_id = $res->fields["user_id"];
                                                                            $user_name = $res->fields["contact_first_name"] . " " . $res->fields["contact_last_name"];
                                                                            ?>
                                                                            <option value="<?php echo $user_id; ?>" <?php echo $user_id == $obj->task_owner ? "selected" : "" ?>>
                                                                                <?php echo $user_name; ?>
                                                                            </option>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </select>

                                                                </span>
                                                                <span id="activity_responsible_read_<?php echo $task_id ?>" style="vertical-align: top">
                                                                    <?php echo $AppUI->_("LBL_OWNER"); ?>:
                                                                    <?php
                                                                    $query = new DBQuery();
                                                                    $query->addTable("users", "u");
                                                                    $query->addQuery("user_id, user_username, contact_last_name, contact_first_name, contact_id");
                                                                    $query->addJoin("contacts", "c", "u.user_contact = c.contact_id");
                                                                    $query->addWhere("u.user_id = " . $obj->task_owner);
                                                                    $res = & $query->exec();
                                                                    for ($res; !$res->EOF; $res->MoveNext()) {
                                                                        $user_name = $res->fields["contact_first_name"] . " " . $res->fields["contact_last_name"];
                                                                        echo $user_name;
                                                                    }
                                                                    ?>
                                                                </span>
                                                            </td>
                                                            <td nowrap="nowrap" style="vertical-align: top;width:250px">
                                                                <span id="activity_effort_edit_<?php echo $task_id ?>" style="vertical-align: top">
                                                                    <?php echo $AppUI->_("LBL_EFFORT"); ?>:
                                                                    <input type="text" class="text" name="planned_effort_<?php echo $task_id ?>" id="planned_effort_<?php echo $task_id ?>" value="<?php echo $projectTaskEstimation->getEffort() ?>" size="8" maxlength="8" onchange="enableDurationBasedOnEffort(<?php echo $task_id ?>);" />
                                                                    <select class="text" name="planned_effort_unit_<?php echo $task_id ?>" id="planned_effort_unit_<?php echo $task_id ?>" >
                                                                        <?php
                                                                        $i = 0;
                                                                        foreach ($effortMetrics as $metric) {
                                                                            $selected = $i == $projectTaskEstimation->getEffortUnit() ? "selected" : "";
                                                                            echo "<option value=\"$i\" $selected>$metric</option>";
                                                                            $i++;
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                    <br /><br />
                                                                    <div id="message_effort_for_duration_<?php echo $task_id ?>" style="font-size: 7pt;color:#777777;max-width: 200px">
                                                                        <span style="color:red">*</span> 
                                                                        <?php echo $AppUI->_("LBL_EFFORT_FOR_DURATION", UI_OUTPUT_HTML); ?>
                                                                    </div>
                                                                </span>
                                                                <span id="activity_effort_read_<?php echo $task_id ?>" style="vertical-align: top">
                                                                    <?php echo $AppUI->_("LBL_EFFORT"); ?>:
                                                                    <?php
                                                                    echo $projectTaskEstimation->getEffort();
                                                                    $i = 0;
                                                                    foreach ($effortMetrics as $metric) {
                                                                        $selected = $i == $projectTaskEstimation->getEffortUnit() ? "selected" : "";
                                                                        if ($selected) {
                                                                            echo " " . $metric;
                                                                        }
                                                                        $i++;
                                                                    }
                                                                    ?>                        
                                                                </span>
                                                                <script>enableDurationBasedOnEffort(<?php echo $task_id ?>);</script>
                                                            </td>
                                                            <td nowrap="nowrap" style="vertical-align: top">

                                                                <input type="hidden" value="0" name="roles_num_<?php echo $task_id ?>" id="roles_num_<?php echo $task_id ?>" />
                                                                <input type="hidden" value="" name="estimatedRolesExcluded_<?php echo $task_id ?>" id="estimatedRolesExcluded_<?php echo $task_id ?>" />
                                                                <input type="hidden" value="" name="estimatedRolesExcludedIds_<?php echo $task_id ?>" id="estimatedRolesExcludedIds_<?php echo $task_id ?>" />      
                                                                <input type="hidden" value="" name="estimatedRolesIds_<?php echo $task_id ?>" id="estimatedRolesIds_<?php echo $task_id ?>" />  
                                                                <input type="hidden" value="" name="estimatedRolesHR_<?php echo $task_id ?>" id="estimatedRolesHR_<?php echo $task_id ?>" />
                                                                <span id="activity_resources_edit_<?php echo $task_id ?>" style="margin-top:-6px; width:300px;vertical-align: top;" >

                                                                    <table cellpading="0" cellspacing="0">
                                                                        <tr>
                                                                            <td style="vertical-align: top" ><?php echo $AppUI->_("Resources"); ?>:</td>
                                                                            <td style="vertical-align: top"><span  id="div_res_<?php echo $task_id ?>"></span></td>
                                                                            <td style="text-align: left;vertical-align: bottom; ">
                                                                                <img src="./modules/dotproject_plus/images/mais_verde.png" style="cursor:pointer;vertical-align: bottom;text-align: right;" onclick="addEstimatedRoleHR(<?php echo $task_id; ?>, '', '', 1);" />        
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                    <script>
                                <?php
                                $activity_has_estimated_resource = false; //to inclue an resource estimation entry, when none was created yet.
                                foreach ($rolesNonGrouped as $role) {
                                    $role_estimated_id = $role->getQuantity(); // the quantity field is been used to store the estimated role id
                                    $allocated_hr_id = ""; //Get the allocated HR            
//Get id of a possible old allocation to delete it
                                    $q = new DBQuery();
                                    $q->addTable("human_resource_allocation");
                                    $q->addQuery("human_resource_id");
                                    $q->addWhere("project_tasks_estimated_roles_id=" . $role_estimated_id);
                                    $sql = $q->prepare();
                                    $records = db_loadList($sql);
                                    foreach ($records as $record) {
                                        $allocated_hr_id = $record[0];
                                    }
                                    ?>
                                                                            addEstimatedRoleHR(<?php echo $task_id ?>,<?php echo $role->getRoleId() ?>, "<?php echo $allocated_hr_id ?>",<?php echo $role->getQuantity() ?>);
                                    <?php
                                    $activity_has_estimated_resource = true;
                                }
                                if (!$activity_has_estimated_resource) {
                                    ?>
                                                                            addEstimatedRoleHR(<?php echo $task_id; ?>, '', '', 1);//inclues the first resource estimation entry, when none was created yet.
                                    <?php
                                }
                                ?>
                                                                    </script>
                                                                </span>  
                                                                <!-- It is not needed to show resources in read mode because this info already is presented in the activity summary (first line). But the span below is mainteined because the  logic already programed access it. -->
                                                                <span id="activity_resources_read_<?php echo $task_id ?>"> <?php //echo $estimatedRolesTxt;   ?> </span> 
                                                            </td>

                                                            <td nowrap="nowrap" style="vertical-align: bottom;text-align: right;width: 220px">                                                            
                                                                <span id="activity_edit_actions_<?php echo $task_id ?>" style="text-align: right;vertical-align: top;float: right">
                                                                    <br /><br />
                                                                    <input class="button" type="button" value="<?php echo $AppUI->_("LBL_SAVE"); ?>" onclick="saveActivity(<?php echo $task_id ?>, <?php echo $id ?>)" />
                                                                    <input class="button" type="button" value="<?php echo ucfirst($AppUI->_("LBL_CANCEL")); ?>" onclick="cancelSaveActivity(<?php echo $task_id ?>, <?php echo $id ?>)"  />
                                                                </span>                            
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </form>
                                        <?php
                                        //end: build line for task
                                    }
                                }
                            }
                        }
                        //end: code to filter workpackages activities
                    } else {
                        ?>
                        <tr id="row_<?php echo $id ?>" title="non_leaf">

                            <td colspan="7" style="background-color: #E8E8E8;height: 30px">    
                                <span id="read_workpackage_id_<?php echo $id ?>">
                                    <span id="identation_<?php echo $id ?>" style="color:#E8E8E8" ><?php echo $identation; ?></span>
                                    <!-- To enlarge the identation space -->
                                    <?php echo strlen($identation) > 0 ? "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" : ""; ?>
                                    <span style="position: absolute;margin-top: -8px">
                                        <?php if ($number != 1) { ?><!-- Do not include sort icons for EAP root item -->
                                            <img src="./modules/dotproject_plus/images/sort_icon_up.bmp" onclick="sortWBSItem(-1, '<?php echo $id ?>');" id="sort_icon_<?php echo $id ?>" style="cursor:pointer" />
                                            <br />
                                            <img src="./modules/dotproject_plus/images/sort_icon_down.bmp" onclick="sortWBSItem(1, '<?php echo $id ?>');" id="sort_icon_<?php echo $id ?>" style="cursor:pointer" />                                  
                                        <?php } ?>
                                    </span>
                                    <span style="margin-left: 21px" id="div_numbering_<?php echo $id ?>">
                                        <?php echo $number ?>
                                    </span>
                                    <?php echo $name ?>
                                </span>
                                <span id="edit_workpackage_id_<?php echo $id ?>" style="display:none">
                                    <form name="save_wbs_<?php echo $id ?>" id="save_wbs_<?php echo $id ?>" method="post" action="?m=dotproject_plus">
                                        <input name="dosql" type="hidden" value="do_save_wbs" />
                                        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
                                        <input type="hidden" name="wbs_item_id" value="<?php echo $id; ?>" />
                                        <input type="hidden" id="identation_field_<?php echo $id ?>" name="identation_field_<?php echo $id ?>" value="<?php echo $identation; ?>" />
                                        <input type="hidden" id="number_field_<?php echo $id ?>_unique" name="number_field_<?php echo $id ?>" value="<?php echo $number ?>" />
                                        <input type="hidden" id="leaf_field_<?php echo $id ?>_unique" name="leaf_field_<?php echo $id ?>" value="<?php echo $is_leaf ?>" />
                                        <input type="hidden" name="wbs_item_order_<?php echo $id ?>" value="<?php echo $order; ?>" />

                                        <?php echo $AppUI->_("LBL_DESCRIPTION") ?>: <input type="text"  class="text" value="<?php echo $name ?>" id="wbs_item_description_<?php echo $id ?>" name="wbs_item_description_<?php echo $id ?>" size="35" maxlength="40" />
                                        <input type="hidden"  class="text" value="0" maxlength="10" size="15"  name="estimated_size_<?php echo $id ?>" />
                                        <input type="hidden" class="text" name="estimated_size_unit_<?php echo $id ?>"  maxlength="30" size="25"  value=""  />
                                        <span style="text-align: right;width:100%" >
                                            <input class="button" type="button" value="<?php echo $AppUI->_("LBL_SAVE"); ?>" onclick="saveWBSItem(<?php echo $id ?>)" />
                                            <input class="button" type="button" value="<?php echo ucfirst($AppUI->_("LBL_CANCEL")); ?>" onclick="cancelSaveWBSItem(<?php echo $id ?>)" />                  
                                        </span>
                                    </form>
                                </span>   
                            </td>
                        </tr>
                        <?php
                    }
                }
            }
            ?>
        </table>
    </div>
    <script>
        updateEAPItemsNumbers();
    </script>
    <br/>
    <?php
    
        if (isset($_GET["id_new_eap_item"])) {
            if (!isset($_SESSION["new_eap_array"])) { //bug fix: open in edit mode new eap item
                $_SESSION["new_eap_array"] = array();
            }
            if (!isset($_SESSION["new_eap_array"][$_GET["id_new_eap_item"] . "_" . $projectId])) {
                $_SESSION["new_eap_array"][$_GET["id_new_eap_item"] . "_" . $projectId] = true;
                ?>
                <script>
                    showWBSItemEdit(<?php echo $_GET["id_new_eap_item"]; ?>);
                </script>
                <?php
            }
        } else if (isset($_GET["id_new_activity"])) { //bug fix: open in edit mode new activity item
            if (!isset($_SESSION["new_activity_array"])) {
                $_SESSION["new_activity_array"] = array();
            }
            if (!isset($_SESSION["new_activity_array"][$_GET["id_new_activity"] . "_" . $projectId])) {
                $_SESSION["new_activity_array"][$_GET["id_new_activity"] . "_" . $projectId] = true;
                ?>
                <script>
                    showActivityEdit(<?php echo $_GET["id_new_activity"] ?>,<?php echo $_GET["work_package_id"] ?>);
                    //addEstimatedRoleHR(<?php echo $_GET["id_new_activity"] ?>, '', '', 1); // show HR for the new activities. Show to be unneeded.
                </script>
                <?php
            }
        }
    }
    ?>