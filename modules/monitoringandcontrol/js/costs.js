globalPrevDtEnd = new Array();
globalPositionData = new Array();
globalUltimaData = new Array();

function valida(){	

	var elem = document.getElementById('form_costs');

	if (!validarDataForm(elem)) {
		return false;
	}

	elem.submit();
	return true;
}

function upValidate(){	
	var elem = document.getElementById('form_updateRow');

	if (!validarDataForm(elem)) {
		return false;
	}

	elem.submit();
	return true;
}


function validarDataForm(elem){
        var empty_msg=elem.msg_mandatory_empty.value;
        var incorrect_date_msg=elem.msg_date_incorrect.value;
        var msg_date_begin_before_end_date=elem.msg_date_begin_before_end_date.value;
        var msg_invalid_range=elem.msg_invalid_range.value;
	if (elem.dt_begin.value==""){			
		window.alert(empty_msg);
		elem.dt_begin.focus();
		return false;
	}
	
	if (!validarData(elem.dt_begin)){
		window.alert(incorrect_date_msg);
		elem.dt_begin.focus();
		return false;	
	}
	
	if (elem.dt_end.value==""){			
		window.alert(empty_msg);
		elem.dt_end.focus();
		return false;
	}
	
	if (!validarData(elem.dt_end)){
		window.alert(incorrect_date_msg);
		elem.dt_begin.focus();
		return false;	
	}
        
	
	if (elem.tx_pad.value==""){			
		window.alert(empty_msg);
		elem.tx_pad.focus();
		return false;
	}

        
	var dtInicioTela = parseInt(elem.dt_begin.value.split("/")[2].toString() + elem.dt_begin.value.split("/")[1].toString() + elem.dt_begin.value.split("/")[0].toString());
	var dtFimTela = parseInt(elem.dt_end.value.split("/")[2].toString() +  elem.dt_end.value.split("/")[1].toString() +  elem.dt_end.value.split("/")[0].toString());
	
	if (dtInicioTela >= dtFimTela){
		window.alert(msg_date_begin_before_end_date);
		elem.dt_begin.focus();
		return false;		
	}
		
	var dtInicioReg;
	var dtFimReg;
	
	for (var i = 0; i < globalData.length; i++) {
		var regIntervalo = globalData[i];
		var dtInicioReg = parseInt(regIntervalo[0].split("/")[2].toString() + regIntervalo[0].split("/")[1].toString() + regIntervalo[0].split("/")[0].toString());
		var dtFimReg = parseInt(regIntervalo[1].split("/")[2].toString() +  regIntervalo[1].split("/")[1].toString() +  regIntervalo[1].split("/")[0].toString());		
		
		if((dtInicioTela >= dtInicioReg &&
		    dtInicioTela <= dtFimReg) ||
		   (dtFimTela >= dtInicioReg &&
		    dtFimTela <= dtFimReg)){
			window.alert(msg_invalid_range);
			elem.dt_begin.focus();		
			return false;			
		}
	}
        
	return true;

}