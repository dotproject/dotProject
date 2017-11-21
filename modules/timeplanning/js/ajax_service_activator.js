var requestResult="";
function serviceCall(requestedURL,functionReturn){
    requestResult="";
    ajaxCaller(requestedURL,functionReturn);
}

function ajaxCaller(requestedURL,functionReturn){
    var target=requestedURL+"&rnd="+Math.random();
    var ajaxRequest;  // The variable that makes Ajax possible!
    try{
        ajaxRequest = new XMLHttpRequest(); // Opera 8.0+, Firefox, Safari
    }catch(e){
        try{
            ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP"); // Internet Explorer Browsers
        }catch(e) {
            try{
                ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
            }catch(e){
                alert("Your browser broke!"); // Something went wrong
                return false;
            }
        }
    }

    ajaxRequest.onreadystatechange = function(){
        if(ajaxRequest.readyState == 4){
            var result=ajaxRequest.responseText;
            requestResult=result;
            eval(functionReturn);
        }
    }
    //send data
    try{
       var parameters=false;
        var url=target;
        if(target.indexOf("?")!=-1){
            var data=target.split("?");
            url=data[0];
            parameters=data[1];
        }
        ajaxRequest.open("POST",url, true);
        ajaxRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        //ajaxRequest.setRequestHeader("Content-length", parameters.length);
        //ajaxRequest.setRequestHeader("Connection", "close");
        ajaxRequest.send(parameters);
        return ajaxRequest;
    }catch(e){
      //  window.alert(e);
    }
}

