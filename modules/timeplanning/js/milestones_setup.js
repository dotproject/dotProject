

function milestoneupdate(){
	
	
	if(document.getElementById("task_milestone").checked){
		getTable().style.display="none";
		getSaveBt().style.display="none";
		getCancelBt().style.display="none";
		getTabOptionsTd().style.display="none";
		getStatus().disabled=true;
		getProgress().disabled=true;
		getPriority().disabled=true;
		var row=document.getElementById("milestone_date_row");
		if(row==null){
			var table=getMainTable();
			var lastRow= table.rows.length;
			var row = table.insertRow(lastRow);
			row.id="milestone_date_row";
			td = row.insertCell(0);
			td.colspan=10;
			td.noWrap=true;
			var value=document.getElementById("end_date").value;
			var dateFieldName="milestone_date";
			td.innerHTML="Data *<br><input type='text' class='text' id='"+dateFieldName+"' name='"+dateFieldName+"' size='11' disabled value='"+value+"'>";
			td.innerHTML+="<img src='/dotproject/modules/time_planning_UBPS/images/img.gif' style='cursor:pointer' onclick=displayCalendar(document.getElementById('"+dateFieldName+"'),'dd/mm/yyyy',this)>";
			row.insertCell(1);
		}else{
			row.style.display="";
		}
		
		
	}else{
		getTable().style.display="block";
		getSaveBt().style.display="block";
		getCancelBt().style.display="block";
		getTabOptionsTd().display="block";
	    getProgress().disabled=false;
		getStatus().disabled=false;
		getPriority().disabled=false;
		var row=document.getElementById("milestone_date_row");
		if(row!=null){
			row.style.display="none";
		}
	}
}

function getSaveBt(){
	var tables=document.getElementsByTagName("input");
	for(i=0;i<tables.length;i++){
			if(tables[i].name=="btnFuseAction2"){
				return tables[i];
			}
		}
	return null;
}

function getCancelBt(){
	var tables=document.getElementsByTagName("input");
	for(i=0;i<tables.length;i++){
			if(tables[i].name=="cancel2"){
				return tables[i];
			}
		}
	return null;
}

function getTabOptionsTd(){
	var tds=document.getElementsByTagName("td");
	for(i=0;i<tds.length;i++){
			if(tds[i].innerHTML.indexOf("tab=0")!=-1 && tds[i].innerHTML.indexOf("tab=-1")!=-1 && tds[i].innerHTML.indexOf("<td")==-1){
				return tds[i];
			}
		}
	return null;
}

function getTable(){
	var tables=document.getElementsByTagName("table");
	for(i=0;i<tables.length;i++){
			if(tables[i].summary=="tabbed view" ){
				return tables[i];
			}
		}
	return null;
}





function getPriority(){
	var tables=document.getElementsByTagName("select");
	for(i=0;i<tables.length;i++){
			if(tables[i].name=="task_priority"){
				return tables[i];
			}
		}
	return null;
	
}

function getStatus(){
	var tables=document.getElementsByTagName("select");
	for(i=0;i<tables.length;i++){
			if(tables[i].name=="task_status"){
				return tables[i];
			}
		}
	return null;
	
}

function getProgress(){
	var tables=document.getElementsByTagName("select");
	for(i=0;i<tables.length;i++){
			if(tables[i].name=="task_percent_complete"){
				return tables[i];
			}
		}
	return null;	
}

function getMainTable(){
	var tables=document.getElementsByTagName("table");
	for(i=0;i<tables.length;i++){
			
			if(tables[i].className=="std" && tables[i].parentNode.name=="editFrm"){
				
				return tables[i];
			}
		}
	return null;
}

function getMainSaveButton(){
	var tables=document.getElementsByTagName("input");
	for(i=0;i<tables.length;i++){
			if(tables[i].name=="btnFuseAction"){
				return tables[i];
			}
		}
	return null;
}

function customSave(){
	if(document.getElementById("task_milestone").checked){
		var date=document.getElementById("milestone_date").value;
		var endDate=document.getElementById("task_end_date");
		var startDate=document.getElementById("task_start_date");
		
		if(date==""){
			endDate.value="20111117";
		}else{
			dateParts=date.split("/");
			endDate.value=(parseInt(dateParts[2])+1)+dateParts[1]+dateParts[0];
			startDate.value=dateParts[2]+dateParts[1]+dateParts[0];
		}
	}
	submitIt(document.editFrm);
}

document.getElementById("task_milestone").onchange=milestoneupdate;
document.getElementById("task_milestone").onclick=milestoneupdate;
getMainSaveButton().onclick=customSave;
//setInterval("milestoneupdate()",2000);
setTimeout("milestoneupdate()",500);


