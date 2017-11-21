function move(MenuOrigem, MenuDestino){
    var arrMenuOrigem = new Array();
    var arrMenuDestino = new Array();
    var arrLookup = new Array();
    for (var i = 0; i < MenuDestino.options.length; i++){
                arrLookup[MenuDestino.options[i].text] = MenuDestino.options[i].value;
                arrMenuDestino[i] = MenuDestino.options[i].text;
    }
    var fLength = 0;
    var tLength = arrMenuDestino.length;
    for(i = 0; i < MenuOrigem.options.length; i++){
        arrLookup[MenuOrigem.options[i].text] = MenuOrigem.options[i].value;
                if (MenuOrigem.options[i].selected && MenuOrigem.options[i].value != ""){
                        arrMenuDestino[tLength] = MenuOrigem.options[i].text;
            tLength++;
        }
        else{
            arrMenuOrigem[fLength] = MenuOrigem.options[i].text;
            fLength++;
        }
    }
    arrMenuOrigem.sort();
    arrMenuDestino.sort();
    MenuOrigem.length = 0;
        MenuDestino.length = 0;
    var c;
        for(c = 0; c < arrMenuOrigem.length; c++){
        var no = new Option()
        no.value = arrLookup[arrMenuOrigem[c]];
        no.text = arrMenuOrigem[c];
        MenuOrigem[c] = no;
    }
    for(c = 0; c < arrMenuDestino.length; c++){
        var no = new Option();
        no.value = arrLookup[arrMenuDestino[c]];
        no.text = arrMenuDestino[c];
        MenuDestino[c] = no;            
   }
}

function validateMeeting(){		

	if (document.form_ata.date_edit.value=="") {
        document.form_ata.date_edit.focus();	
		alert('Fill the field');
		return false;	
	}	

	if (parseInt(document.form_ata.hr_begin.value)>= parseInt(document.form_ata.hr_end.value)) {
                document.form_ata.hr_begin.focus();
		alert('Hour begin must be before than hour end');
		return false;	
	}
	if (document.form_ata.title.value=="") {
        document.form_ata.title.focus();    
		alert('Fill the field');
		return false;	
	}
	var selec = document.getElementById("participants");	
 	
    for(i=0; i < selec.length; i++){
		selec.options[i].selected = true;	
	}	
	
    if (document.form_ata.participants.value=="") {
        document.form_ata.participants.focus();   
		alert('Fill the field');
		return false;	
	}

	if(document.form_ata.meeting_type.value==""){
        document.form_ata.meeting_type.focus();
        alert('Fill the field');
        return false;			
    }	

	if(document.form_ata.subject.value==""){
        document.form_ata.subject.focus();
		alert('Fill the fieldHou');
		return false;			
	}	

	document.form_ata.submit();
    
}

