function scheduleDevelopment(){
    var message=document.getElementById("schedule_development_prompt_message").innerHTML;
    if(window.confirm(message)){
        document.form_cpm.submit();
    }
}

function openEstimationReport(){
    document.getElementById("minute_id").value="-1";
    document.getElementById("action_estimation").value="read";
    document.getElementById("minute_form").submit();
}

function closeReport(){
    var members_field=document.getElementById("selected_members"); 
    var all_members_field=document.minute_form.all_members;
    while(members_field.options.length>0){
        var option= new Option(members_field.options[0].text,members_field.options[0].value);
        all_members_field.options[all_members_field.options.length]=option;
        members_field.options[0]=null;
    }
    document.getElementById("date").value="";
    tinyMCE.get('description_edit').setContent("");
    document.getElementById("action_estimation").value="";
    var div=document.getElementById("ata_div");
    div.style.display="none";
}

function openReport(record){
    document.getElementById("action_estimation").value="read";
    document.getElementById("minute_id").value=record;
    document.getElementById("minute_form").submit();
}


function Trim(str){
    return str.replace(/^\s+|\s+$/g,"");
}


function saveReport(){
    var date=document.getElementById("date_edit").value;
    var value="";
    if(date==""){
        alert("Favor informar a data da reuniao.");
        return;
    }
    var dateParts=date.split("/");
    date=dateParts[2]+"-"+dateParts[1]+"-"+dateParts[0];
    document.getElementById("date").value=date;
    var description=tinyMCE.get('description_edit').getContent();
    if(description==""){
        alert("Favor informar a descri��o da reuniao.");
        return;
    }
    document.getElementById("description").value=description;
    var members_field=document.getElementById("selected_members");
    var list="";
    for(i=0; i < members_field.options.length ; i++){
        value=members_field.options[i].value;
        if(list==""){
            list=value;
        }else{
            list+= "," + value;
        }
    }
    document.getElementById("membersIds").value=list;
    document.getElementById("minute_form").submit();
}


function deleteReport(minute_id){
    document.getElementById("minute_id").value=minute_id;
    document.getElementById("action_estimation").value="delete";
    document.getElementById("minute_form").submit();
}

function updateMinutesList(){
    var list_field= document.getElementById("reports_effort");
    if(minute_id==document.getElementById("minute_id").value=="-1"){
        list_field.options[list_field.length]= new Option(document.getElementById("date").value,requestResult);
    }else{
        for(i=0;i<list_field.options.length;i++){
            if(list_field.options[i].value==document.getElementById("minute_id").value){
                list_field.options[i].text=document.getElementById("date").value;
            }
        }		
    }
    closeReport();
}

function addMemberComputed(form,source,target,id){
    var field_name_source=source;
    var field_name_target=target;
    var form_name=form;
    var _form=document.forms[form_name];
    var source_field=_form.elements[field_name_source];
    var target_field=_form.elements[field_name_target];

    for(i=0;i<source_field.options.length;i++){
		
        if(source_field.options[i].value==id){
            var text=source_field.options[i].text;
            var option= new Option(text,id);
            target_field.options[target_field.length]=option;
            source_field.options[i]=null;
            break;
        }
    }
	
}

function addMember(form,source,target){
    var field_name_source=source;
    var field_name_target=target;
    var form_name=form;
    var _form=document.forms[form_name];
    var source_field=_form.elements[field_name_source];
    var target_field=_form.elements[field_name_target];
	
    if(source_field.selectedIndex!=-1){
        var selectedText=source_field.options[source_field.selectedIndex].text;
        var selectedValue=source_field.options[source_field.selectedIndex].value;
        var option= new Option(selectedText,selectedValue);
        target_field.options[target_field.length]=option;
        source_field.options[source_field.selectedIndex]=null;
    }
}

function removeMember(form,source,target){
    var field_name_source=source;
    var field_name_target=target;
    var form_name=form;
    var _form=document.forms[form_name];
    var source_field=_form.elements[field_name_source];
    var target_field=_form.elements[field_name_target];
	
    if(target_field.selectedIndex!=-1){
        var selectedText=target_field.options[target_field.selectedIndex].text;
        var selectedValue=target_field.options[target_field.selectedIndex].value;
        var option= new Option(selectedText,selectedValue);
        source_field.options[source_field.length]=option;
        target_field.options[target_field.selectedIndex]=null;
    }
}



function getCheckedValue(radioObj) {
    if(!radioObj)
        return "";
    var radioLength = radioObj.length;
    if(radioLength == undefined)
        if(radioObj.checked)
            return radioObj.value;
        else
            return "";
    for(var i = 0; i < radioLength; i++) {
        if(radioObj[i].checked) {
            return radioObj[i].value;
        }
    }
    return "";
}


function setCheckedValue(radioObj, newValue) {
    if(!radioObj)
        return;
    var radioLength = radioObj.length;
    if(radioLength == undefined) {
        radioObj.checked = (radioObj.value == newValue.toString());
        return;
    }
    for(var i = 0; i < radioLength; i++) {
        radioObj[i].checked = false;
        if(radioObj[i].value == newValue.toString()) {
            radioObj[i].checked = true;
        }
    }
}

function addEstimatedRole(taskId,roleId,quantity){
    var div= document.getElementById("div_res_"+taskId);
    var roleField=document.createElement("select");
    var roleQuantityField=document.createElement("input");
    var removeButton=document.createElement("img");
    var br=document.createElement("br");
    var nextIdField=document.getElementById("roles_num_"+taskId);
    var nextId=nextIdField.value;
    roleQuantityField.name="estimated_role_quantity_"+taskId+"_"+nextId;
    roleField.name="estimated_role_"+taskId+"_"+nextId;
    roleQuantityField.id="estimated_role_quantity_"+taskId+"_"+nextId;
    roleField.id="estimated_role_"+taskId+"_"+nextId;
    roleField.className="text";
    roleQuantityField.className="text";
    roleQuantityField.size=5;
    roleQuantityField.value=quantity;
    
    removeButton.src="./images/trash_small.gif";
    removeButton.id=nextId;
    removeButton.title=roleId;
    removeButton.name=taskId;
    removeButton.style.cursor="pointer";
    removeButton.onclick=removeEstimatedRole; 
    
    for(i=0;i<roleIds.length;i++){
        roleField.options[i] = new Option(roleNames[i],roleIds[i]);
        if(roleId==roleIds[i]){
            roleField.options[i].selected=true;
            roleField.selectedIndex=i;
        }
    }
    
    div.appendChild(roleField);
    div.appendChild(roleQuantityField);
    div.appendChild(removeButton);
    div.appendChild(br);
    nextIdField.value=parseInt(nextIdField.value)+1;
} 

function removeEstimatedRole(){
    var field=document.getElementById("estimatedRolesExcluded_"+this.name); 
    var fieldRemovedRolesIds=document.getElementById("estimatedRolesExcludedIds_"+this.name);
    var idQuantity="estimated_role_quantity_"+this.name+"_"+this.id;
    var idRole="estimated_role_"+this.name+"_"+this.id;
    document.getElementById(idQuantity).style.display="none";
    document.getElementById(idRole).style.display="none";
    this.style.display="none";
    var idRoleDb=this.title;
    field.value+=field.value==""?this.id:","+this.id;
    fieldRemovedRolesIds.value+=fieldRemovedRolesIds.value==""?idRoleDb:","+idRoleDb;
}

function saveEstimationsData(){
    if (validateEstimatedDates()){
        document.getElementById("action_estimation").value="saveEstimationsData";
        document.getElementById("minute_form").submit();
    }
} 

/**
 * This method is called before the form is submited.
 * It will ensure all the date fields have valid values to be stored.
 * Also is verified whether the begin date is before the after date.
 * It will return a boolean value to say whether dates are or not correct.
 */
function validateEstimatedDates(){
    var fields=document.getElementsByTagName("input");
    var dateFields=new Array();
    var arrayIndex=0;
    //get all date fields
    for(var i=0;i< fields.length;i++){
        if(fields[i].name.substring(0,28)=="planned_start_date_activity_" || fields[i].name.substring(0,26)=="planned_end_date_activity_"){
            dateFields[arrayIndex]=fields[i];
            arrayIndex++;
        }
    }
    //verify fields values
    for(i=0; i<dateFields.length;i++){
        var fieldValue=dateFields[i].value;
        var fieldName=dateFields[i].name;
        if(fieldValue==""){        
            formatFieldAsNormal(dateFields[i]);
            dateFields[i]=null;//value is OK
        }else{
            if(validateDateField(fieldValue)){
                formatFieldAsNormal(dateFields[i]);
                dateFields[i]=null; //value is ok
            }      
        }
        //check whether end date is after the begin date.
        if( dateFields[i]==null){
            var activityId= fieldName.substring(fieldName.lastIndexOf("_")+1, fieldName.length);
            var dateBeginField=document.getElementsByName("planned_start_date_activity_"+activityId)[0];
            var dateEndField=document.getElementsByName("planned_end_date_activity_"+activityId)[0];
            if(validateDateField(dateBeginField.value)  && validateDateField(dateEndField.value)){
                var dateBegin=convertToDate(dateBeginField.value);
                var dateEnd=convertToDate(dateEndField.value);
                var diference= dateEnd.getTime()- dateBegin.getTime();// return the diference on milleseconds between two dates.
                if(diference<0){
                    dateFields[i]=dateBeginField;
                    formatFieldAsProblematic(dateBeginField);
                    formatFieldAsProblematic(dateEndField);
                }
            }
        }
    }
    var hasNoProblem=true;
    //mark fields with problems
    for(i=0; i<dateFields.length;i++){
        if(dateFields[i]!=null){
            hasNoProblem=false;
            formatFieldAsProblematic(dateFields[i]);
        }
    }
    document.getElementById("estimation_form_error_message").style.display=hasNoProblem?"none":"block";
    window.location.hash="estimation_form_anchor";
    return hasNoProblem;
}

/**
 * This function verifies if the value inputted in a date field is numericaly correct.
 * Empty values are consideted false.
 * This function return a boolean value.
 */
function validateDateField(value){
    var result=false;
    if(value!=""){
        var dateParts=value.split("/");
        if(dateParts.length==3){
            var day=dateParts[0];
            var month=dateParts[1];
            var year=dateParts[2];
            if (!isNaN(parseInt(day)) && !isNaN(parseInt(month)) && !isNaN(parseInt(year)) ){
                result=true;
            }
        }
    }
    return result;
}

/**
 * This function converts a valid date inserted by user in a javascript date object
 * It retuns a Date object.
 */
function convertToDate(value){
    var object= null;
    if(value!=""){
        var dateParts=value.split("/");
        if(dateParts.length==3){
            var day=dateParts[0];
            var month=dateParts[1];
            var year=dateParts[2];
            object = new Date(year-1970, month, day, 0,0,0,0);
        }
    }
    return object;
}

function formatFieldAsProblematic(field){
    field.style.background="#FFCC99";
}

function formatFieldAsNormal(field){
    field.style.background="#FFFFFF";
}
