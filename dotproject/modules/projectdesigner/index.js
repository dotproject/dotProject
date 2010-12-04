navigator.family="ie";
if (window.navigator.userAgent.toLowerCase().match(/gecko/)) {navigator.family = "gecko"}
if (navigator.userAgent.toLowerCase().indexOf('opera') + 1 || window.opera) {navigator.family = "opera"}

function expand_colapse(id, table_name, option) {
      
      var expand = (option == 'expand' ? 1 : 0);
      var collapse = (option == 'collapse' ? 1 : 0);

      var trs = document.getElementsByTagName('tr');

      for (var i=0;i < trs.length;i++) 
      {
      var tr_name = trs.item(i).id;

      if (tr_name.indexOf(id) >= 0)
      {
            var tr = document.getElementById(tr_name);
            if (collapse || expand) {
                  if (collapse) {
                        if (navigator.family == "gecko" || navigator.family == "opera"){            
                              tr.style.visibility = "collapse";
                              tr.style.display = "none";
                              var img_expand = document.getElementById(id+'_expand');
                              var img_collapse = document.getElementById(id+'_collapse');
                              img_collapse.style.display = "none";
                              img_expand.style.display = "inline";
                        } else {
                              tr.style.display = "none";
                              var img_expand = document.getElementById(id+'_expand');
                              var img_collapse = document.getElementById(id+'_collapse');
                              img_collapse.style.display = "none";
                              img_expand.style.display = "inline";
                        }
                        if (id.indexOf('component')==-1) {      
                              (tr.style.display == "none") ? eval('document.frmWorkspace.opt_view_'+id+'.value=0') : eval('document.frmWorkspace.opt_view_'+id+'.value=1');      
                              (tr.style.display == "none") ? eval('document.editFrm.opt_view_'+id+'.value=0') : eval('document.editFrm.opt_view_'+id+'.value=1');      
                              (tr.style.display == "none") ? eval('document.frm_bulk.opt_view_'+id+'.value=0') : eval('document.frm_bulk.opt_view_'+id+'.value=1');      
                        }                  
                  } else {
                        if (navigator.family == "gecko" || navigator.family == "opera"){            
                              tr.style.visibility = "visible";
                              tr.style.display = "";
                              var img_expand = document.getElementById(id+'_expand');
                              var img_collapse = document.getElementById(id+'_collapse');
                              img_collapse.style.display = "inline";
                              img_expand.style.display = "none";
                        } else {
                              tr.style.display = "";
                              var img_expand = document.getElementById(id+'_expand');
                              var img_collapse = document.getElementById(id+'_collapse');
                              img_collapse.style.display = "inline";
                              img_expand.style.display = "none";
                        }
                        if (id.indexOf('component')==-1) {      
                              (tr.style.display == "none") ? eval('document.frmWorkspace.opt_view_'+id+'.value=0') : eval('document.frmWorkspace.opt_view_'+id+'.value=1');      
                              (tr.style.display == "none") ? eval('document.editFrm.opt_view_'+id+'.value=0') : eval('document.editFrm.opt_view_'+id+'.value=1');      
                              (tr.style.display == "none") ? eval('document.frm_bulk.opt_view_'+id+'.value=0') : eval('document.frm_bulk.opt_view_'+id+'.value=1');      
                        }                                    
                  }
            } else {
                  if (navigator.family == "gecko" || navigator.family == "opera"){            
                        tr.style.visibility = (tr.style.visibility == '' || tr.style.visibility == "collapse") ? "visible" : "collapse";
                        tr.style.display = (tr.style.display == "none") ? "" : "none";
                        var img_expand = document.getElementById(id+'_expand');
                        var img_collapse = document.getElementById(id+'_collapse');
                        img_collapse.style.display = (tr.style.visibility == 'visible') ? "inline" : "none";
                        img_expand.style.display = (tr.style.visibility == '' || tr.style.visibility == "collapse") ? "inline" : "none";
                  } else {
                        tr.style.display = (tr.style.display == "none") ? "" : "none";
                        var img_expand = document.getElementById(id+'_expand');
                        var img_collapse = document.getElementById(id+'_collapse');
                        img_collapse.style.display = (tr.style.display == '') ? "inline" : "none";
                        img_expand.style.display = (tr.style.display == 'none') ? "inline" : "none";
                  }
                  if (id.indexOf('component')==-1) {      
                        (tr.style.display == "none") ? eval('document.frmWorkspace.opt_view_'+id+'.value=0') : eval('document.frmWorkspace.opt_view_'+id+'.value=1');      
                        (tr.style.display == "none") ? eval('document.editFrm.opt_view_'+id+'.value=0') : eval('document.editFrm.opt_view_'+id+'.value=1');      
                        (tr.style.display == "none") ? eval('document.frm_bulk.opt_view_'+id+'.value=0') : eval('document.frm_bulk.opt_view_'+id+'.value=1');      
                  }
            }      
      }
      }
}

function expandAll() {
      expand_colapse('project', 'tblProjects', 'expand');
      expand_colapse('gantt', 'tblProjects', 'expand');
      expand_colapse('tasks', 'tblProjects', 'expand');
      expand_colapse('actions', 'tblProjects', 'expand');
      expand_colapse('addtsks', 'tblProjects', 'expand');
      expand_colapse('files', 'tblProjects', 'expand');
}

function collapseAll() {
      expand_colapse('project', 'tblProjects', 'collapse');
      expand_colapse('gantt', 'tblProjects', 'collapse');
      expand_colapse('tasks', 'tblProjects', 'collapse');
      expand_colapse('actions', 'tblProjects', 'collapse');
      expand_colapse('addtsks', 'tblProjects', 'collapse');
      expand_colapse('files', 'tblProjects', 'collapse');
}

var hourMSecs = 3600*1000;

/**
* no comment needed
*/
function isInArray(myArray, intValue) {

	for (var i = 0; i < myArray.length; i++) {
		if (myArray[i] == intValue) {
			return true;
		}
	}		
	return false;
}

/**
* @modify_reason calculating duration does not include time information and cal_working_days stored in config.php
*/
function calcDuration(form,start_date,end_date,duration_fld,durntype_fld) {

	var int_st_date = new String(start_date.value);
	var int_en_date = new String(end_date.value);

	var sDate = new Date(int_st_date.substring(0,4),(int_st_date.substring(4,6)-1),int_st_date.substring(6,8), int_st_date.substring(8,10), int_st_date.substring(10,12));
	var eDate = new Date(int_en_date.substring(0,4),(int_en_date.substring(4,6)-1),int_en_date.substring(6,8), int_en_date.substring(8,10), int_en_date.substring(10,12));
	var s = Date.UTC(int_st_date.substring(0,4),(int_st_date.substring(4,6)-1),int_st_date.substring(6,8), int_st_date.substring(8,10), int_st_date.substring(10,12));
	var e = Date.UTC(int_en_date.substring(0,4),(int_en_date.substring(4,6)-1),int_en_date.substring(6,8), int_en_date.substring(8,10), int_en_date.substring(10,12));
	var durn = (e - s) / hourMSecs; //hours absolute diff start and end
	var durn_abs = durn;	

	//now we should subtract non-working days from durn variable
	var duration = durn  / 24;
	var weekendDays = 0;
		var myDate = new Date(int_st_date.substring(0,4), (int_st_date.substring(4,6)-1),int_st_date.substring(6,8), int_st_date.substring(8,10));
	for (var i = 0; i < duration; i++) {
		//var myDate = new Date(int_st_date.substring(0,4), (int_st_date.substring(4,6)-1),int_st_date.substring(6,8), int_st_date.substring(8,10));
		var myDay = myDate.getDate();
		if ( !isInArray(working_days, myDate.getDay()) ) {
			weekendDays++;
		}
		myDate.setDate(myDay + 1);
	}
	
	//calculating correct durn value
	durn = durn - weekendDays*24;	// total hours minus non-working days (work day hours)

	// check if the last day is a weekendDay
	// if so we subtracted some hours too much before, 
	// we have to fill up the last working day until cal_day_start + daily_working_hours
	if ( !isInArray(working_days, eDate.getDay()) && eDate.getHours() != cal_day_start) {
		durn = durn + Math.max(0, (cal_day_start + daily_working_hours - eDate.getHours()));
	}
	
	//could be 1 or 24 (based on TaskDurationType value) - We'll consider it 1 = hours
	//var durnType = parseFloat(f.task_duration_type.value);	
	var durnType = parseFloat(durntype_fld.value);	
	durn /= durnType;
	//alert(durn);
	if (durnType == 1){
		// durn is absolute weekday hours
		
		//if first day equals last day we're already done
		if( durn_abs < daily_working_hours ) {

			durn = durn_abs;

		} else { //otherwise we need to process first and end day different;
	
			// Hours worked on the first day
			var first_day_hours = cal_day_end - sDate.getHours();
			if (first_day_hours > daily_working_hours)
				first_day_hours = daily_working_hours;

			// Hours worked on the last day
			var last_day_hours = eDate.getHours() - cal_day_start;
			if (last_day_hours > daily_working_hours)
				last_day_hours = daily_working_hours;

			// Total partial day hours
			var partial_day_hours = first_day_hours + last_day_hours;

			// Full work days
			var full_work_days = (durn - partial_day_hours) / 24;

			// Total working hours
			durn = Math.floor(full_work_days) * daily_working_hours + partial_day_hours;
			
			// check if the last day is a weekendDay
			// if so we subtracted some hours too much before, 
			// we have to fill up the last working day until cal_day_start + daily_working_hours
			if ( !isInArray(working_days, eDate.getDay()) && eDate.getHours() != cal_day_start) {
				durn = durn + Math.max(0, (cal_day_start + daily_working_hours - eDate.getHours()));
			}
		}

	} else if (durnType == 24 ) {
		//we should talk about working days so a task duration of 41 hrs means 6 (NOT 5) days!!!
		if (durn > Math.round(durn))
			durn++;
	}

	if ( s > e ) {
		//alert( 'End date is before start date!');
	} else {
		duration_fld.value = Math.round(durn);
	}
}
/**
* Get the end of the previous working day 
*/
function prev_working_day( dateObj ) {
	while ( ! isInArray(working_days, dateObj.getDay()) || dateObj.getHours() < cal_day_start ||
	      (	dateObj.getHours() == cal_day_start && dateObj.getMinutes() == 0 ) ){

		dateObj.setDate(dateObj.getDate()-1);
		dateObj.setHours( cal_day_end );
		dateObj.setMinutes( 0 );
	}

	return dateObj;
}
/**
* Get the start of the next working day 
*/
function next_working_day( dateObj ) {
	while ( ! isInArray(working_days, dateObj.getDay()) || dateObj.getHours() >= cal_day_end ) {
		dateObj.setDate(dateObj.getDate()+1);
		dateObj.setHours( cal_day_start );
		dateObj.setMinutes( 0 );
	}

	return dateObj;
}

// This function gets called when the end-user clicks on some date.
function selected(cal, date) {
//    cal.sel.value = date; // just update the date in the input field.
// Pedro A. : Lets pass the date in a Ymd format and let our formatDate function to the rest.
    cal.sel.value = (cal.date.print("%Y%m%d%H%M"));    
    setDate(cal.form, cal.sel.name);
  if (cal.dateClicked && (cal.sel.id == "ini_date" || cal.sel.id == "end_date"))
    // if we add this call we close the calendar on single-click.
    cal.callCloseHandler();
}

// And this gets called when the end-user clicks on the _selected_ date,
// or clicks on the "Close" button.  It just hides the calendar without
// destroying it.
function closeHandler(cal) {
  cal.hide();                        // hide the calendar
//  cal.destroy();
  _dynarch_popupCalendar = null;
}

// This function shows the calendar under the element having the given id.
// It takes care of catching "mousedown" signals on document and hiding the
// calendar if the click was outside.
function showCalendar(id, format, form_name, showsTime, showsOtherMonths) {
  var el = document.getElementById(id);
  if (_dynarch_popupCalendar != null) {
    // we already have some calendar created
    _dynarch_popupCalendar.hide();                 // so we hide it first.
  } else {
    // first-time call, create the calendar.
    var cal = new Calendar(1, null, selected, closeHandler);
    // uncomment the following line to hide the week numbers
    // cal.weekNumbers = false;
    if (typeof showsTime == "string") {
      cal.showsTime = true;
      cal.time24 = (showsTime == "24");
    }
    if (showsOtherMonths) {
      cal.showsOtherMonths = true;
    }
    _dynarch_popupCalendar = cal;                  // remember it in the global var
    cal.setRange(1900, 2070);        // min/max year allowed.
    cal.create();
  }
  _dynarch_popupCalendar.setDateFormat(format);    // set the specified date format
  _dynarch_popupCalendar.parseDate(el.value);      // try to parse the text in field
  _dynarch_popupCalendar.sel = el;                 // inform it what input field we use
  _dynarch_popupCalendar.form = form_name;         // inform it what form we use

  // the reference element that we pass to showAtElement is the button that
  // triggers the calendar.  In this example we align the calendar bottom-right
  // to the button.
  _dynarch_popupCalendar.showAtElement(el, "Bl");        // show the calendar

  return false;
}

var MONTH_NAMES=new Array('January','February','March','April','May','June','July','August','September','October','November','December','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
var DAY_NAMES=new Array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sun','Mon','Tue','Wed','Thu','Fri','Sat');
function LZ(x) {return(x<0||x>9?"":"0")+x}

function formatDate(date,format){
      format=format+"";
      var result="";
      var i_format=0;
      var c="";
      var token="";
      var y=date.getYear()+"";
      var M=date.getMonth()+1;
      var d=date.getDate();
      var E=date.getDay();
      var H=date.getHours();
      var m=date.getMinutes();
      var s=date.getSeconds();
      var Y, yyyy,yy,MMM,MM,dd,hh,h,mm,ss,ampm,HH,H,KK,K,kk,k;
      var value=new Object();
      if(y.length < 4){
            y=""+(y-0+1900);
      }
      value["y"]=""+y;
      value["yyyy"]=y;
      value["Y"]=y;
      value["yy"]=y.substring(2,4);
      value["M"]=M;
      value["MM"]=LZ(M);
      value["MMM"]=MONTH_NAMES[M-1];
      value["NNN"]=MONTH_NAMES[M+11];
      value["b"]=MONTH_NAMES[M+11];
      value["d"]=d;
      value["dd"]=LZ(d);
      value["E"]=DAY_NAMES[E+7];
      value["EE"]=DAY_NAMES[E];
      value["H"]=H;
      value["HH"]=LZ(H);
      if(H==0){
            value["h"]=12;
      }else if(H>12){
            value["h"]=H-12;
      }else{
            value["h"]=H;
      }
      value["hh"]=LZ(value["h"]);
      if(H>11){
            value["K"]=H-12;
      }else{
            value["K"]=H;
      }
      value["k"]=H+1;
      value["KK"]=LZ(value["K"]);
      value["kk"]=LZ(value["k"]);
      if(H > 11){
            value["a"]="pm";
      }else{
            value["a"]="am";
      }
      value["m"]=m;
      value["mm"]=LZ(m);
      value["s"]=s;
      value["ss"]=LZ(s);
      while(i_format < format.length){
            c=format.charAt(i_format);
            token="";
            while((format.charAt(i_format)==c) &&(i_format < format.length)){
                  token += format.charAt(i_format++);
            }
            if(value[token] != null){
                  result=result + value[token];
            }else{
                  result=result + token;
            }
      }
      return result;
}

// ------------------------------------------------------------------
// Utility functions for parsing in getDateFromFormat()
// ------------------------------------------------------------------
function _isInteger(val) {
	var digits="1234567890";
	for (var i=0; i < val.length; i++) {
		if (digits.indexOf(val.charAt(i))==-1) { return false; }
		}
	return true;
	}
function _getInt(str,i,minlength,maxlength) {
	for (var x=maxlength; x>=minlength; x--) {
		var token=str.substring(i,i+x);
		if (token.length < minlength) { return null; }
		if (_isInteger(token)) { return token; }
		}
	return null;
	}

// ------------------------------------------------------------------
// getDateFromFormat( date_string , format_string )
//
// This function takes a date string and a format string. It matches
// If the date string matches the format string, it returns the 
// getTime() of the date. If it does not match, it returns 0.
// ------------------------------------------------------------------
function getDateFromFormat(val,format) {
	val=val+"";
	format=format+"";
	var i_val=0;
	var i_format=0;
	var c="";
	var token="";
	var token2="";
	var x,y;
	var now=new Date();
	var year=now.getYear();
	var month=now.getMonth()+1;
	var date=1;
	var hh=now.getHours();
	var mm=now.getMinutes();
	var ss=now.getSeconds();
	var ampm="";
	
	while (i_format < format.length) {
		// Get next token from format string
		c=format.charAt(i_format);
		token="";
		while ((format.charAt(i_format)==c) && (i_format < format.length)) {
			token += format.charAt(i_format++);
			}
		// Extract contents of value based on format token
		if (token=="yyyy" || token=="yy" || token=="y" || token=="Y") {
			if (token=="Y") { x=4;y=4; }
			if (token=="yyyy") { x=4;y=4; }
			if (token=="yy")   { x=2;y=2; }
			if (token=="y")    { x=2;y=4; }
			year=_getInt(val,i_val,x,y);
			if (year==null) { return 0; }
			i_val += year.length;
			if (year.length==2) {
				if (year > 70) { year=1900+(year-0); }
				else { year=2000+(year-0); }
				}
			}
		else if (token=="MMM"||token=="NNN"){
			month=0;
			for (var i=0; i<MONTH_NAMES.length; i++) {
				var month_name=MONTH_NAMES[i];
				if (val.substring(i_val,i_val+month_name.length).toLowerCase()==month_name.toLowerCase()) {
					if (token=="MMM"||(token=="NNN"&&i>11)) {
						month=i+1;
						if (month>12) { month -= 12; }
						i_val += month_name.length;
						break;
						}
					}
				}
			if ((month < 1)||(month>12)){return 0;}
			}
		else if (token=="EE"||token=="E"){
			for (var i=0; i<DAY_NAMES.length; i++) {
				var day_name=DAY_NAMES[i];
				if (val.substring(i_val,i_val+day_name.length).toLowerCase()==day_name.toLowerCase()) {
					i_val += day_name.length;
					break;
					}
				}
			}
		else if (token=="MM"||token=="M") {
			month=_getInt(val,i_val,token.length,2);
			if(month==null||(month<1)||(month>12)){return 0;}
			i_val+=month.length;}
		else if (token=="dd"||token=="d") {
			date=_getInt(val,i_val,token.length,2);
			if(date==null||(date<1)||(date>31)){return 0;}
			i_val+=date.length;}
		else if (token=="hh"||token=="h") {
			hh=_getInt(val,i_val,token.length,2);
			if(hh==null||(hh<1)||(hh>12)){return 0;}
			i_val+=hh.length;}
		else if (token=="HH"||token=="H") {
			hh=_getInt(val,i_val,token.length,2);
			if(hh==null||(hh<0)||(hh>23)){return 0;}
			i_val+=hh.length;}
		else if (token=="KK"||token=="K") {
			hh=_getInt(val,i_val,token.length,2);
			if(hh==null||(hh<0)||(hh>11)){return 0;}
			i_val+=hh.length;}
		else if (token=="kk"||token=="k") {
			hh=_getInt(val,i_val,token.length,2);
			if(hh==null||(hh<1)||(hh>24)){return 0;}
			i_val+=hh.length;hh--;}
		else if (token=="mm"||token=="m") {
			mm=_getInt(val,i_val,token.length,2);
			if(mm==null||(mm<0)||(mm>59)){return 0;}
			i_val+=mm.length;}
		else if (token=="ss"||token=="s") {
			ss=_getInt(val,i_val,token.length,2);
			if(ss==null||(ss<0)||(ss>59)){return 0;}
			i_val+=ss.length;}
		else if (token=="a") {
			if (val.substring(i_val,i_val+2).toLowerCase()=="am") {ampm="am";}
			else if (val.substring(i_val,i_val+2).toLowerCase()=="pm") {ampm="pm";}
			else {return 0;}
			i_val+=2;}
		else {
			if (val.substring(i_val,i_val+token.length)!=token) {return 0;}
			else {i_val+=token.length;}
			}
		}
	// If there are any trailing characters left in the value, it doesn't match
	if (i_val != val.length) { return 0; }
	// Is date valid for month?
	if (month==2) {
		// Check for leap year
		if ( ( (year%4==0)&&(year%100 != 0) ) || (year%400==0) ) { // leap year
			if (date > 29){ return 0; }
			}
		else { if (date > 28) { return 0; } }
		}
	if ((month==4)||(month==6)||(month==9)||(month==11)) {
		if (date > 30) { return 0; }
		}
	// Correct hours value
	if (hh<12 && ampm=="pm") { hh=hh-0+12; }
	else if (hh>11 && ampm=="am") { hh-=12; }
	var newdate=new Date(year,month-1,date,hh,mm,ss);
	return newdate.getTime();
	}
	
function addBulkComponent(li) {
//IE
  if (document.all || navigator.appName == "Microsoft Internet Explorer") {
	var form = document.frm_bulk;
      var ni = document.getElementById('tbl_bulk');
      var newitem = document.createElement('input');
      var htmltxt = "";
      newitem.id = 'bulk_selected_task['+li+']';
      newitem.name = 'bulk_selected_task['+li+']';
      newitem.type = 'hidden';
      ni.appendChild(newitem);
  } else {
//Non IE
	var form = document.frm_bulk;
      var ni = document.getElementById('tbl_bulk');
      var newitem = document.createElement('input');
      newitem.setAttribute("id",'bulk_selected_task['+li+']');
      newitem.setAttribute("name",'bulk_selected_task['+li+']');
      newitem.setAttribute("type",'hidden');
      ni.appendChild(newitem);
  }
}

function removeBulkComponent(li) {
      var t = document.getElementById('tbl_bulk');
      var old = document.getElementById('bulk_selected_task['+li+']');
      t.removeChild(old);
}


function getStyle(nodeName, sStyle, iStyle) {
      var element = document.getElementById(nodeName);
      if (window.getComputedStyle) {
            var style=document.defaultView.getComputedStyle(element,null);
      	var value = style.getPropertyValue(sStyle);
      } else {
            var value = eval("element.currentStyle." + iStyle);
      }
      return value;      
}

function mult_sel(cmbObj, box_name, form_name) {
	var f = eval('document.'+form_name);
	var check = cmbObj.checked;

      for (var i=0;i < f.length;i++) 
      {
      fldObj = f.elements[i];
      var field_name = fldObj.id;
      if (fldObj.type == 'checkbox' && field_name.indexOf(box_name) >= 0)
      {
            id = field_name.replace('selected_task_','');
            row = document.getElementById('row'+id);
	      var oldcheck = fldObj.checked;
            fldObj.checked = (check) ? true : false;
            if (check) {
                  highlight_tds(row, 2, id)
                  //Only add the component if it didn't exist or else we get JS trouble
                  if (!oldcheck)
                        addBulkComponent(id);
            } else {
                  highlight_tds(row, 0, id);
                  //Only remove the component if it exists or else we get JS trouble
                  if (oldcheck)
                        removeBulkComponent(id);
            }
      }
      }
}

function highlight_tds(row, high, id) {
//high = 0 or false => remove highlight
//high = 1 or true => highlight
//high = 2 => select
//high = 3 => deselect
      if (document.getElementsByTagName) {
            var tcs = row.getElementsByTagName('td');
            var cell_name = '';
            if (!id) {
                  check = false;
            } else {
                  var f = eval('document.frm_tasks');
                  var check = eval('f.selected_task_'+id+'.checked');
            }
            for (var j = 0; j < tcs.length; j+=1) {
                  cell_name = eval('tcs['+j+'].id');
                  if(!(cell_name.indexOf('ignore_td_') >= 0)) {
                        if (high == 3)
                              tcs[j].style.background = '#FFFFCC';
                        else if (high == 2 || check)
                              tcs[j].style.background = '#FFCCCC';
                        else if (high == 1)
                              tcs[j].style.background = '#FFFFCC';
                        else
                              tcs[j].style.background = original_bgc;
                  }
            }
      }
}

var is_check;
function select_box(box, id, form_name){
	var f = eval('document.'+form_name);
	var check = eval('f.'+box+'_'+id+'.checked');
      boxObj = eval('f.elements["'+box+'_'+id+'"]');
      if ((is_check && boxObj.checked && !boxObj.disabled) || (!is_check && !boxObj.checked && !boxObj.disabled)) {
            row = document.getElementById('row'+id);
            boxObj.checked = true;
            highlight_tds(row, 2, id);
            addBulkComponent(id);
      } else if ((is_check && !boxObj.checked && !boxObj.disabled) || (!is_check && boxObj.checked && !boxObj.disabled)) {
            row = document.getElementById('row'+id);
            highlight_tds(row, 3, id);
            boxObj.checked = false;      
            removeBulkComponent(id);
      }
}

function toggle_users(id){
  var element = document.getElementById(id);
  element.style.display = (element.style.display == '' || element.style.display == "none") ? "inline" : "none";
}