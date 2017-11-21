var nextId = 1000;
var opt = ["Selecione...", "Aberto","Fechado","Em desenvolvimento","Cancelado"];
function retorna(id){	
	nextId++;
	if(id==''){
		id=nextId;
	}	
var impacto = document.formPendencia.impact;
var status = document.formPendencia.status;
var user = document.formPendencia.user;
var date_limit = document.formPendencia.date_limit;
var desvio = document.formPendencia.description;
var causa = document.formPendencia.cause;
var acao_corretiva = document.formPendencia.acao_corretiva;
  
  	if((impacto.value=="") && (status.value== 0) && (user.value==0) &&(date_limit.value=="") &&(desvio.value=="") && (causa.value=="") && (acao_corretiva.value=="")){
		alert('Preencha os campos  corretamente');
		return false;	
		}

	if(impacto==""){
		impacto.focus();
		alert('Fill the field');
		return false;	
	}

	if(status== 0){
		status.focus();
		alert('Fill the field');
		return false;	
	}	
	if(user==0){
	    user.focus();
		alert('Fill the field');
		return false;			
	}
	if(date_limit==""){
	    date_limit.focus();
		alert('Fill the field');
		return false;	
	}		
	if(desvio==""){
	    desvio.focus();
		alert('Fill the field');
		return false;	
	}
	if(causa==""){
	    causa.focus();
		alert('Fill the field');
		return false;	
	}
	if(acao_corretiva==""){
	    acao_corretiva.focus();
		alert('Fill the field');
		return false;	
	}

	var table = window.opener.document.getElementById("tbl_pendencias");
	var lastRow= table.rows.length;
	var row = table.insertRow(lastRow);	
	row.id=id;
	
	var td = row.insertCell(0);
	td.noWrap=true;		
	var field=document.createElement("input");
	field.type="hidden";
	field.name="index[]";
	field.value="index"+(parseInt(id)-1000);
	td.appendChild(field);	
	
	var field=document.createElement("input");
	field.type="hidden";
	field.name="impact[]";
	field.value=impacto.value;
	td.appendChild(field);	
	
	var field=document.createElement("input");
	field.type="hidden";
	field.name="description[]";
	field.value= desvio.value;
	td.appendChild(field);
	
	var field=document.createElement("input");
	field.type="hidden";
	field.name="cause[]";
	field.value= causa.value;
	td.appendChild(field);
	
	var field=document.createElement("input");
	field.type="hidden";
	field.name="status[]";
	field.value= status.value;
	td.appendChild(field);	
	
	var field=document.createElement("input");
	field.type="text";
	field.id="acao_corretiva"+id;
	field.name="acao_corretiva[]";
	field.style.width="98%";
	field.readOnly=true;
	field.value= acao_corretiva.value ;
	td.appendChild(field);
	
	var td = row.insertCell(1);
	td.noWrap=true;	
	var field=document.createElement("input");
	field.type="hidden";
	field.name="user[]";
	field.value= user.value ;
	td.appendChild(field);	
	
	var field=document.createElement("input");
	field.type="text";
	field.id="username"+id;
	field.name="username[]";	
	field.style.width="98%";
	field.readOnly=true;
	    for(i=0; i<globalUserIdList.length; i++){
                        if(user == globalUserIdList[i] ){	
                        field.value = globalUserNameList[i];
                        }
		}	
	td.appendChild(field);	
	
	var td = row.insertCell(2);
	td.noWrap=true;	
	var field=document.createElement("input");
	field.type="text";
	field.id="date_limit"+id;
	field.name="date_limit[]";
	field.style.width="98%";
	field.readOnly=true;
	field.value= date_limit ;
	td.appendChild(field);
	
	var td = row.insertCell(3);
	td.noWrap=true;
	var field=document.createElement("input");
	field.type="text";
	field.id="stts"+id;
	field.name="stts[]";
	field.style.width="98%";
	field.readOnly=true;
			for(i=0; i < opt.length; i++){
				 if(status.value ==  i){								 
						field.value = opt[i];
				 }
			}	
	td.appendChild(field);	
	
	//add a exclude button
	td = row.insertCell(4);
	td.noWrap=true;
	div=field=document.createElement("div");
	div.style.textAlign ="center";
	div.innerHTML="<input type='image' alt='./images/icons/stock_delete-16.png'  src='./images/icons/stock_delete-16.png' title='Deletar' name='deletar' value='deletar' onclick=deleteRole('"+row.id+"')>";
  	td.appendChild(div);	
	
	window.opener.document.tbl_pendencias;
	window.close();
}