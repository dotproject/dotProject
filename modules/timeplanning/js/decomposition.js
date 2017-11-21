var nextId=1000;
function addLine(description,effort,dateBegin,dateEnd,humansResources,othersResurces,id,hour_b,minute_b,hour_e,minute_e,eap_item_id){
	var htmlOptions="";
	var index=document.getElementById("id_"+eap_item_id).rowIndex+1;
	var table = document.getElementById("table_decomposition");
	var lastRow= table.rows.length;
	var row=null;
	//get index of next wbs item
	var i=index;
	var found=false;
	while(i<table.rows.length && !found){
            if(table.rows[i].title=="is_wbs_item"){
                found=true;
                index=i;
            }
            i++;
	}
	
	if(!found){
            index=lastRow;
	}
	
	row = table.insertRow(index);
	
	nextId++;
	if(id==''){
		id=nextId;
	}
		
	row.id="row_"+id;
	//Description
	var td = row.insertCell(-1);
        td.colSpan="2";
	var span=document.createElement("span");
	span.id="identation_"+id;
	span.innerHTML="";
	td.appendChild(span);
	var field=document.createElement("input");
	field.type="text";
	field.className="text";
	field.style.width="99%";
	field.id="description_"+id;
	field.name="description_"+id;
	field.value=description;
	td.appendChild(field);
	
	//eap item id
	field=document.createElement("input");
	field.type="hidden";
	field.id="eap_item_"+id;
	field.name="eap_item_"+id;
	field.value=eap_item_id;
	td.appendChild(field);
	
	/*
	//Effort
	td = row.insertCell(2);
	td.noWrap=true;
	field=document.createElement("input");
	field.id="effort_"+id;
	field.name="effort_"+id;
	field.type="text";
	field.size="6";
	field.className="text";
	td.appendChild(field);
	field=document.createElement("select");
	field.options[0]= new Option("Horas","Horas");
	field.options[1]= new Option("Dias","Dias");
	field.className="text";
	field.id="effortUnit_"+id;
	field.name="effortUnit_"+id;
	td.appendChild(field);
	//Date begin
	td = row.insertCell(3);
	td.noWrap=true;
	var dateFieldName="dateB_"+id;
	td.innerHTML="<input type='text' class='text' id='"+dateFieldName+"' name='"+dateFieldName+"' size='11' disabled value='"+dateBegin+"'>";
	td.innerHTML+="<img src='/dotproject/modules/time_planning_UBPS/images/img.gif' style='cursor:pointer' onclick=displayCalendar(document.getElementById('"+dateFieldName+"'),'dd/mm/yyyy',this)>";
	
	
	field=createHourField("hour_begin_"+id);
	field.className="text";
	if(hour_b != ''){
		field.selectedIndex=hour_b;
		field.options[hour_b].selected=true;
	}
	td.appendChild(field);
	var span=document.createElement("span");
	span.innerHTML=":";
	td.appendChild(span);
	field=createMinuteField("minute_begin_"+id);
	td.appendChild(field);
	if(minute_b != ''){
		field.selectedIndex=minute_b;
		field.options[minute_b].selected=true;
	}
	//Date End
	td = row.insertCell(4);
	td.noWrap=true;
	var dateFieldName="dateE_"+id;
	td.innerHTML="<input type='text' class='text' id='"+dateFieldName+"' name='"+dateFieldName+"' size='11' disabled value='"+dateBegin+"'>";
	td.innerHTML+="<img src='/dotproject/modules/time_planning_UBPS/images/img.gif' style='cursor:pointer' onclick=displayCalendar(document.getElementById('"+dateFieldName+"'),'dd/mm/yyyy',this)>";
	
	field=createHourField("hour_end_"+id);
	td.appendChild(field);
	if(hour_e != ''){
		field.selectedIndex=hour_e;
		field.options[hour_e].selected=true;
	}
	span=document.createElement("span");
	span.innerHTML=":";
	td.appendChild(span);
	field=createMinuteField("minute_end_"+id);
	td.appendChild(field);
	field.className="text";
	if(minute_b != ''){
		field.selectedIndex=minute_b;
		field.options[minute_b].selected=true;
	}
	
	
	//Humans resources
	td = row.insertCell(5);
	td.noWrap=true;
	field=document.createElement("input");
	field.className="text";
	field.type="text";
	field.id="hummansResources_"+id;
	field.name="hummansResources_"+id;
	field.disabled=true;
	td.appendChild(field);
	button=document.createElement("input");
	button.type="button";
	button.className="button";
	button.value="...";
	td.appendChild(button);
	
	//Others resources
	td = row.insertCell(6);
	td.noWrap=true;
	field=document.createElement("input");
	field.className="text";
	field.type="text";
	field.size="11";
	field.id="othersResources_"+id;
	field.name="othersResources_"+id;
	field.disabled=true;
	td.appendChild(field);
	button=document.createElement("input");
	button.type="button";
	button.className="button";
	button.value="...";
	td.appendChild(button);
	
	*/
	//add a exclude button
	
	td = row.insertCell(-1);
	td.noWrap=true;
        td.className="shortTD";
        td.style.textAlign="center";
	div=field=document.createElement("div");
	div.innerHTML="<img src='modules/timeplanning/images/stock_delete-16.png' border='0' style='cursor:pointer' onclick=deleteActivity('"+row.id+"')>";
	td.appendChild(div);
	
	//add a move button
        
	td = row.insertCell(-1);
	td.noWrap=true;
        td.className="shortTD";
        td.style.textAlign="center";
	div=document.createElement("div");
        
	htmlOptions=document.getElementById("work_packages_combo").innerHTML;
	div.innerHTML="<select class='text' id='new_workpackage_"+id+"' onchange=moveToSelectedWorkpackage('"+ id +"')>"+htmlOptions+"</select>";
        td.appendChild(div);	
}

function identation(i,type){
	div=document.getElementById("identation_"+i);
	if(type==1){
		div.innerHTML=div.innerHTML+"&nbsp;&nbsp;&nbsp;";
	}else{
		div.innerHTML=div.innerHTML.replace("&nbsp;&nbsp;&nbsp;","");
	}
}

function deleteActivity(rowId){
	var i=document.getElementById(rowId).rowIndex;
	document.getElementById('table_decomposition').deleteRow(i);
	var activity_id=rowId.substring(4,rowId.length);
	var field=document.getElementById("activities_ids_to_delete");
	field.value+=field.value==""?activity_id:","+activity_id;
}

function moveToSelectedWorkpackage(id){
	//update logical eap item field related to activity
	
	var combo=document.getElementById("new_workpackage_"+id);
	var new_work_package_id=combo.options[combo.selectedIndex].value;
	if(new_work_package_id==-1){
		return false;
	}
	document.getElementById("eap_item_"+id).value=new_work_package_id;
	
	//move activity on table
	var currentIndex=document.getElementById("row_"+id).rowIndex;
	var workpackageIndex=document.getElementById("id_"+new_work_package_id).rowIndex;
	var j=workpackageIndex+1;
	var i=currentIndex;
	i+=j>i?0:1;
		
	var table = document.getElementById("table_decomposition");
	table.insertRow(j);
	moveRow(i,j);
	table.deleteRow(i);
	
}

function moveRow(i,j){
	var oTable = document.getElementById('table_decomposition');
	var trs = oTable.tBodies[0].getElementsByTagName("tr");
	if(j==0){
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

function createHourField(fieldName){
	var field=document.createElement('select');
	field.name=fieldName;
	field.className="text";
	field.id=fieldName;
	for(i=0;i<=23;i++){
		field.options[i]= new Option(i,i);
	}
	return field;
}

function createMinuteField(fieldName){
	var field=document.createElement('select');
	field.name=fieldName;
	field.className="text";
	field.id=fieldName;
	for(i=0;i<=59;i++){
		field.options[i]= new Option(i,i);
	}
	return field;
}

function salvarCronograma(){
	var table = document.getElementById("table_decomposition");
	var field_ids = document.getElementById("activities_ids");
	var form=document.decomposition_form;
	var lastRow= table.rows.length;
	var row=null;
	var id="";
	field_ids.value="";
	for(i=1;i<lastRow;i++){
		row=table.rows[i];
		var id=row.id;
		if(id.indexOf("row_")!=-1){
			id=id.substring(4,row.id.length);
			field_ids.value+= field_ids.value==""?id:","+id;
		}
	}
	form.submit();
}