var nextId=1000;
function addRole(id,roleName,index,identation){
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
	var field=document.createElement("input");
	field.type="text";
	field.className="text";
	field.id="description_"+id;
	field.name="description_"+id;
	field.value=roleName;
	td.appendChild(field);
	
	//add a exclude button
	td = row.insertCell(3);
	td.noWrap=true;
	div=field=document.createElement("div");
	div.innerHTML="<input type='button' class='button' value='X' onclick=deleteRole('"+row.id+"','"+id+"')>";
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