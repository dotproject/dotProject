/*
   Milonic DHTML Menu Context/Right Click Menu Module contextmenu.js version 1.2 - September 1st 2004
   This module is only compatible with the Milonic DHTML Menu version 5.16 or higher

   Copyright 2004 (c) Milonic Solutions Limited. All Rights Reserved.
   This is a commercial software product, please visit http://www.milonic.com/ for more information.
*/

contextDisabled=true;     // Set this parameter to disable or enable right click, context menu at runtime.
contextMenu="contextMenu"; // Default name for the contextMenu
contextObject="";          // This is the object the right click occured on, could be an image, link whatever was under the mouse at the point of right click.

function rclick(e){
	if(contextDisabled)
	{
		_d.oncontextmenu=null
		return true;
	}
	if(_d.all)
	{
		ev=event.button;
		contextObject=event.srcElement;
	}
	else 
	{
		ev=e.which;
		contextObject=e.target;
	}
	if(ev==2||ev==3){
		_gm=getMenuByName(contextMenu)
		if(_gm!=null)popup(contextMenu,1)
		return false
	}
	else{
		if(ev==1)closeAllMenus();
	}
	return true;
}

if(ns4){
	_d.captureEvents(Event.MOUSEDOWN);
	_d.onmousedown=rclick;
}
else{
	_d.onmouseup=rclick
	_d.oncontextmenu=new Function("return false")
}