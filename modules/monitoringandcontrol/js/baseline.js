function validateBaseline(){
	
	if(document.form_ata.nmBaseline.value==""){
		document.form_ata.nmBaseline.focus();
		alert('Fill the field');
		return false;	
	}
	if(document.form_ata.nmVersao.value==""){
		document.form_ata.nmVersao.focus();	
		alert('Fill the field');
		return false;	
	}			
	var elem = document.getElementById('form_ata');
	elem.submit();
}