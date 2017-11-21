var nextId =1000;
globalUserNameList= new Array();
globalUserIdList= new Array();

function addRow(id){
	nextId++;
	if(id==''){
		id=nextId;
	}
	var table = document.getElementById("tb_row");
	var lastRow= table.rows.length;
	var row = table.insertRow(lastRow);	
	row.id=id;
	
		//Description
	var td = row.insertCell(0);
	td.noWrap=true;	

	
	var field=document.createElement("input");
	field.type="hidden";
	field.name="index[]";
	field.value="index"+(parseInt(id)-1000);
	td.appendChild(field);	
	
	var field=document.createElement("input");
	field.type="text";
	field.id="description_"+id;
	field.name="description[]";
	field.style.width="98%";
	field.style.margin="0px";
	td.appendChild(field);
	
		//Select consultation
	var td = row.insertCell(1);
	td.noWrap=true;
	var field=document.createElement("select");		
	field.id="consultation"+id;
	field.name="consultation[]"; 
	td.appendChild(field);
	field.options[0]= new Option("Select...","0")					 					 
	var opt;
		for(i=0; i<globalUserIdList.length; i++){							
			opt=  new Option(globalUserNameList[i],globalUserIdList[i])
			field.options[i+1]=opt;			
	}
	
		//Select execut
	var td = row.insertCell(2);
	td.noWrap=true;
	var field=document.createElement("select");		
	field.id="execut"+id;
	field.name="execut[]";
	td.appendChild(field);	
	field.options[0]= new Option("Selecione...","0")	
		var opt;
		for(i=0; i<globalUserIdList.length; i++){							
			opt=  new Option(globalUserNameList[i],globalUserIdList[i])
			field.options[i+1]=opt;		
	}	
	
		//Select support
	var td = row.insertCell(3);
	td.noWrap=true;
	var field=document.createElement("select");		
	field.id="support"+id;
	field.name="support[]";
	td.appendChild(field);	
	field.options[0]= new Option("Selecione...","0")	
	var opt;
		for(i=0; i<globalUserIdList.length; i++){							
			opt=  new Option(globalUserNameList[i],globalUserIdList[i])
			field.options[i+1]=opt;			}	
	
		//Select approve
	var td = row.insertCell(4);
	td.noWrap=true;
	var field=document.createElement("select");		
	field.id="approve"+id;
	field.name="approve[]";
	td.appendChild(field);
	field.options[0]= new Option("Select...","0")	
	var opt;
		for(i=0; i<globalUserIdList.length; i++){							
			opt=  new Option(globalUserNameList[i],globalUserIdList[i])
			field.options[i+1]=opt;				
	}	
	
}


function saveRecords(){
		if ((document.form_resp.description.value=="") && (document.form_resp.consultation.value=="0") && (document.form_resp.execut.value==0) && (document.form_resp.support.value==0) && (document.form_resp.approve.value==0) ){
		return false;	
	}else{	
	  var elem = document.getElementById('form_resp');
	  	elem.submit();
	}
}

//*** Generic functions for update and delete ***//


function deleteRow(excluir){	
var opcao = excluir.value ;
		if(confirm('Deseja excluir os dados selecionados?')){
 			var elem = document.getElementById('form_delete');
			switch(opcao){
				case "acao_corretiva":
		  			elem.action = "?m=monitoringandcontrol&a=do_acao_corretiva_aed&task_id=<?php echo $task_id; ?>"		  				
			    break;
				case "ata":
		  			elem.action = "?m=monitoringandcontrol&a=do_ata_aed&project_id=<?php echo $project_id; ?>"		  				
			    break;				
				case "baseline":
		  			elem.action = "?m=monitoringandcontrol&a=addedit_update_baseline&project_id=<?php echo $project_id; ?>"		  							
			    break;	
				case "costs":
		  			elem.action = "?m=monitoringandcontrol&a=do_costs_aed&user_id=<?php echo $user_id;?>"		  				
			    break;	
				case "respons":
		  			elem.action = "?m=monitoringandcontrol&a=do_respons_aed&project_id=<?php echo $project_id;?>"		  							
			    break;
			
			}
			elem.submit();			
			return true;
		}else
			return false;			
}

function updateRow(){	
	  var elem = document.getElementById('form_update');
	  	elem.submit();
}

function updateRecords(){
	
	  var elem = document.getElementById('form_updateRow');
	  	elem.submit();
}

