function formatadata(Campo, teclapres)
{
    var tecla = teclapres.keyCode;
    var vr = new String(Campo.value);
    vr = vr.replace("/", "");
    vr = vr.replace("/", "");
    vr = vr.replace("/", "");
    tam = vr.length + 1;
    if (tecla != 8 && tecla != 8)
    {
        if (tam > 0 && tam < 2)
            Campo.value = vr.substr(0, 2) ;
        if (tam > 2 && tam < 4)
            Campo.value = vr.substr(0, 2) + '/' + vr.substr(2, 2);
        if (tam > 4 && tam < 7)
            Campo.value = vr.substr(0, 2) + '/' + vr.substr(2, 2) + '/' + vr.substr(4, 7);
    }
}

function validarData(pObj) {
    var expReg = /^((0[1-9]|[12]\d)\/(0[1-9]|1[0-2])|30\/(0[13-9]|1[0-2])|31\/(0[13578]|1[02]))\/(19|20)?\d{2}$/;
    var aRet = true;
    if (pObj.value.match(expReg) && pObj.value != '') {
        var dia = pObj.value.substring(0,2);
        var mes = pObj.value.substring(3,5);
        var ano = pObj.value.substring(6,10);
        if(dia.charAt(0)=="0" && dia.length>1){
           dia=dia.charAt(1); 
        }
        if(mes.charAt(0)=="0" && mes.length>1){
           mes=mes.charAt(1); 
        }
        dia=parseInt(dia);
        mes=parseInt(mes);
        ano=parseInt(ano);
        if ( (mes == 4 || mes == 6 || mes == 9 || mes == 11) && dia > 30) 
            aRet = false;
        else 
        if ((ano % 4) != 0 && mes == 2 && dia > 28) 
            aRet = false;
        else
        if ((ano%4) == 0 && mes == 2 && dia > 29)
            aRet = false;
    } else
        aRet = false;  
    return aRet;
}