 function openModalWindow(strURL, strArgument, intWidth, intHeight){

   var intTop = ((screen.height - intHeight) / 2);
   var intLeft = ((screen.width - intWidth) / 2);
   var strEnderec=strURL;
   var strAjustesIE='status=0; help=0; center:yes; dialogWidth:'+intWidth+'px; dialogHeight:'+intHeight+'px';
   var strAjustesNS='width='+intWidth+', height='+intHeight+', status=0, scrollbars=1, menubar=0, dependent=1, left='+intLeft+', top='+intTop; 

   with (window.navigator){

		switch (appName){
	 	case 'Microsoft Internet Explorer':
 	 		var x = window.showModalDialog(strEnderec, strArgument ,strAjustesIE);
 	 	break;
 		case 'Netscape':
 			var x = window.open(strEnderec, 'Default', strAjustesNS);
 		break;
		}
   }

 }