globalPrevDtEnd = new Array();
globalPositionData = new Array();

function upValidate(){	
var upform = document.getElementById('form_updateRow');
var data_i = upform.dt_begin.value;
var data_f = upform.dt_end.value;
var prevPos =	globalPositionData[0];
var nextPos = globalPositionData[1];

		if (upform.dt_begin.value==""){
			window.alert('Fill the field');
			upform.dt_begin.focus();
			return false;
		}

var ano_f = parseInt( data_f.split( "/" )[2]);
var mes_f = parseInt( data_f.split( "/" )[1]);
var dia_f = parseInt( data_f.split( "/" )[0]);
var dt_f = ano_f.toString()+mes_f.toString()+dia_f.toString();

var ano_i = parseInt( data_i.split( "/" )[2]);
var mes_i = parseInt( data_i.split( "/" )[1]);
var dia_i = parseInt( data_i.split( "/" )[0]);
var dt_i = ano_i.toString()+mes_i.toString()+dia_i.toString();

		if(dt_f !=""){
			if (dt_i >= dt_f){
					 window.alert('Date end must be after date begin');
					 upform.dt_begin.focus();
		  			return false;
			}
		}

var ano_prev = parseInt(prevPos.split( "/" )[2]);
var mes_prev =  parseInt(prevPos.split( "/" )[1]);
var dia_prev =  parseInt(prevPos.split( "/" )[0]);
var dtFim_prev = ano_prev.toString()+mes_prev.toString()+dia_prev.toString();

var ano_pos =  parseInt(nextPos.split( "/" )[2]);
var mes_pos =  parseInt(nextPos.split( "/" )[1]);
var dia_pos =  parseInt(nextPos.split( "/" )[0]);
var dtIni_next = ano_pos.toString()+mes_pos.toString()+dia_pos.toString();

		if(dtFim_prev !=""){
			if (dt_i <= dtFim_prev){
				  	window.alert('Date begin must be after earlier date end');
					 upform.dt_begin.focus();
		  			return false;
			}
		}

		if(dtIni_next != ""){  // proxi data inicial ta vindo como data atual como default qnd não existe  prox linha
			if (dt_f >= dtIni_next){
				  	window.alert('Date end must be before next date begin');
					 upform.dt_begin.focus();
		  			return false;
			}
		}
		
		upform.submit();
		return true;
}