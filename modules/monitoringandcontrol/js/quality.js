function validateQualityFields(id){
	  
	if (document.form_resp.typpe.value==0)
	{
		document.form_resp.typpe.focus();
		window.alert('Fill the field');
		return false;
	}
	if (document.form_resp.description.value=="")
	{
	    document.form_resp.description.focus();
		window.alert('Fill the field');
		return false;
	}
	if (document.form_resp.responsable.value==0)
	{
	    document.form_resp.responsable.focus();
		window.alert('Fill the field');
		return false;
	}
	if (document.form_resp.status.value==0)
	{
	    document.form_resp.status.focus();
		window.alert('Fill the field');
		return false;
	}
	if (document.form_resp.date_edit.value=="")
	{
	    document.form_resp.date_edit.focus();
		window.alert('Fill the field');
		return false;
	}
}

function validateUpdateFields(){
	if (document.form_updateRow.typpe.value==0)
	{
	    document.form_updateRow.typpe.focus();
		window.alert('Fill is invalid');
		return false;
	}
	if (document.form_updateRow.description.value=="")
	{
	    document.form_resp.description.focus();
		window.alert('Fill the field');
		return false;
	}
	if (document.form_updateRow.responsable.value==0)
	{
	    document.form_resp.responsable.focus();
		window.alert('Fill is invalid');
		return false;
	}
	if (document.form_updateRow.status.value==0)
	{
		document.form_resp.status.focus();
		window.alert('Fill is invalid');
		return false;
	}
	if (document.form_updateRow.date_edit.value=="")
	{
		document.form_resp.date_edit.focus();
		window.alert('Fill the field');
		return false;
	}
}

