function validateChangeRequest(){
        var msg=document.getElementById("validation_massage").innerHTML;
	if(document.form_ata.impact.value==""){
		document.form_ata.impact.focus();
		alert(msg);
		return false;	
	}
	if(document.form_ata.status.value==0){
		document.form_ata.status.focus();	
		alert(msg);
		return false;	
	}		
	if(document.form_ata.description.value==""){
		document.form_ata.description.focus();
		alert(msg);
		return false;	
	}
	if(document.form_ata.cause.value==""){
		document.form_ata.cause.focus();
		alert(msg);
		return false;	
	}
	if(document.form_ata.acao_corretiva.value==""){
		document.form_ata.acao_corretiva.focus();
		alert(msg);
		return false;	
	}	
	if(document.form_ata.user.value=="Selecione..."){
		document.form_ata.user.focus();
		alert(msg);
		return false;	
	}
	if(document.getElementById("date_edit").value==""){
		document.form_ata.date_limit.focus();
		alert(msg);
		return false;	
	}
	document.form_ata.submit();
} 