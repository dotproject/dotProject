// $Id$
var calendarField = '';
var calWin = null;

function setMilestoneEndDate(checked){
    if(checked){
        document.datesFrm.end_date.value      = document.datesFrm.start_date.value;
        document.datesFrm.task_end_date.value = document.datesFrm.task_start_date.value;
    } 
}

/**
setTasksStartDate sets new task's start date value which is maximum end date of all dependend tasks
to do: date format should be taken from config
*/
function setTasksStartDate(form, datesForm) {

	var td = form.task_dependencies.length -1;
	var max_date = new Date("1970", "01", "01");
	var max_id = -1;
	
	if (form.set_task_start_date.checked == true) {	
		//build array of task dependencies
		for (td; td > -1; td--) {
			var i = form.task_dependencies.options[td].value;
			var val = projTasksWithEndDates[i][0]; //format 05/03/2004
			var sdate = new Date(val.substring(6,10),val.substring(3,5)-1, val.substring(0,2));
			if (sdate > max_date) {
				max_date = sdate;
				max_id = i;
			}
		}
		
		//check end date of parent task 
		// Why? Parent task is for updating dynamics or angle icon
		if ( 0 && form.task_parent.options.selectedIndex!=0) {
			var i = form.task_parent.options[form.task_parent.options.selectedIndex].value;	
			var val = projTasksWithEndDates[i][0]; //format 05/03/2004	
			var sdate = new Date(val.substring(6,10),val.substring(3,5)-1, val.substring(0,2));
			if (sdate > max_date) {
				max_date = sdate;
				max_id = i;		
			}
		}
		
		if (max_id != -1) {
			var hour  = projTasksWithEndDates[max_id][1];
			var minute = projTasksWithEndDates[max_id][2];
		
			datesForm.start_date.value = projTasksWithEndDates[max_id][0];
			datesForm.start_hour.value = hour;
			datesForm.start_minute.value = minute;
			
			 var d = projTasksWithEndDates[max_id][0];
			 //hardcoded date format Ymd
			 datesForm.task_start_date.value = d.substring(6,10) + "" + d.substring(3,5) + "" + d.substring(0,2);	 
		}	
		setAMPM(datesForm.start_hour);
	}
}

function popContacts() {
	window.open('./index.php?m=public&a=contact_selector&dialog=1&call_back=setContacts&selected_contacts_id='+selected_contacts_id, 'contacts','height=600,width=400,resizable,scrollbars=yes');
}

function popCalendar( field ){
	calendarField = field;
	task_cal = document.getElementById('task_' + field.name);
	idate = task_cal.value;
	window.open( 'index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'top=250,left=250,width=251, height=220, scrollbars=false' );
}

/**
 *	@param string Input date in the format YYYYMMDD
 *	@param string Formatted date
 */
function setCalendar( idate, fdate ) {
	fld_date = document.getElementById('task_' + calendarField.name);
	calendarField.value = fdate;
	fld_date.value = idate;

	// set end date automatically with start date if start date is after end date
	e_date = document.getElementById('task_' + 'end_date');
	e_fdate = document.getElementById('end_date');
	if (calendarField.name == 'start_date') {
		if( e_date.value < idate) {
			e_date.value = idate;
			e_fdate.value = fdate;
		}
	}
}

function setContacts(contact_id_string){
	if(!contact_id_string){
		contact_id_string = "";
	}
	task_contacts = document.getElementById('task_contacts');
	task_contacts.value = contact_id_string;
	selected_contacts_id = contact_id_string;
}

function submitIt(form){
	if (form.task_name.value.length < 3) {
			alert( task_name_msg );
			form.task_name.focus();
			return false;
	}

	// Check the sub forms
	for (var i = 0; i < subForm.length; i++) {
		if (!subForm[i].check())
			return false;
		// Save the subform, this may involve seeding this form
		// with data
		subForm[i].save();
	}

	form.submit();
}

function addUser(form) {
	var fl = form.resources.length -1;
	var au = form.assigned.length -1;
	//gets value of percentage assignment of selected resource
	var perc = form.percentage_assignment.options[form.percentage_assignment.selectedIndex].value;

	var users = "x";

	//build array of assiged users
	for (au; au > -1; au--) {
		users = users + "," + form.assigned.options[au].value + ","
	}

	//Pull selected resources and add them to list
	for (fl; fl > -1; fl--) {
		if (form.resources.options[fl].selected && users.indexOf( "," + form.resources.options[fl].value + "," ) == -1) {
			t = form.assigned.length
			opt = new Option( form.resources.options[fl].text+" ["+perc+"%]", form.resources.options[fl].value);
			form.hperc_assign.value += form.resources.options[fl].value+"="+perc+";";
			form.assigned.options[t] = opt
		}
	}
}

function removeUser(form) {
	fl = form.assigned.length -1;
	for (fl; fl > -1; fl--) {
		if (form.assigned.options[fl].selected) {
			//remove from hperc_assign
			var selValue = form.assigned.options[fl].value;			
			var re = ".*("+selValue+"=[0-9]*;).*";
			var hiddenValue = form.hperc_assign.value;
			if (hiddenValue) {
				var b = hiddenValue.match(re);
				if (b[1]) {
					hiddenValue = hiddenValue.replace(b[1], '');
				}
				form.hperc_assign.value = hiddenValue;
				form.assigned.options[fl] = null;
			}
//alert(form.hperc_assign.value);
		}
	}
}

//Check to see if None has been selected.
function checkForTaskDependencyNone(obj){
	var td = obj.length -1;
	for (td; td > -1; td--) {
		if(obj.options[td].value==task_id){
			clearExceptFor(obj, task_id);
			break;
		}
	}
}

//If None has been selected, remove the existing entries.
function clearExceptFor(obj, id){
	var td = obj.length -1;
	for (td; td > -1; td--) {
		if(obj.options[td].value != id){
			obj.options[td]=null;
		}
	}
}

function addTaskDependency(form, datesForm) {
	var at = form.all_tasks.length -1;
	var td = form.task_dependencies.length -1;
	var tasks = "x";

	//Check to see if None is currently in the dependencies list, and if so, remove it.

	if(td>=0 && form.task_dependencies.options[0].value==task_id){
		form.task_dependencies.options[0] = null;
		td = form.task_dependencies.length -1;
	}

	//build array of task dependencies
	for (td; td > -1; td--) {
		tasks = tasks + "," + form.task_dependencies.options[td].value + ","
	}

	//Pull selected resources and add them to list
	for (at; at > -1; at--) {
		if (form.all_tasks.options[at].selected && tasks.indexOf( "," + form.all_tasks.options[at].value + "," ) == -1) {
			t = form.task_dependencies.length
			opt = new Option( form.all_tasks.options[at].text, form.all_tasks.options[at].value );
			form.task_dependencies.options[t] = opt
		}
	}
	
	checkForTaskDependencyNone(form.task_dependencies);
	setTasksStartDate(form, datesForm);
}

function removeTaskDependency(form, datesForm) {
	td = form.task_dependencies.length -1;

	for (td; td > -1; td--) {
		if (form.task_dependencies.options[td].selected) {
			form.task_dependencies.options[td] = null;
		}
	}
	
	setTasksStartDate(form, datesForm);
}

function setAMPM( field) {
	ampm_field = document.getElementById(field.name + "_ampm");
	if (ampm_field) {
		if ( field.value > 11 ){
			ampm_field.value = "pm";
		} else {
			ampm_field.value = "am";
		}
	}
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
function calcDuration(f) {

	var int_st_date = new String(f.task_start_date.value + f.start_hour.value + f.start_minute.value);
	var int_en_date = new String(f.task_end_date.value + f.end_hour.value + f.end_minute.value);

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
	
	//could be 1 or 24 (based on TaskDurationType value)
	var durnType = parseFloat(f.task_duration_type.value);	
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
		alert( 'End date is before start date!');
		return false;
	} else {
		f.task_duration.value = Math.round(durn);
		return true;
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
/**
* @modify reason calcFinish does not use time info and working_days array 
*/
function calcFinish(f) {
	//var int_st_date = new String(f.task_start_date.value);
	var int_st_date_time = new String(f.task_start_date.value + f.start_hour.value + f.start_minute.value);	
	var int_st_date = int_st_date_time;
	var e = new Date(int_st_date_time.substring(0,4),(int_st_date_time.substring(4,6)-1),int_st_date_time.substring(6,8), int_st_date_time.substring(8,10), int_st_date_time.substring(10,12));

	// The task duration
	var durn = parseFloat(f.task_duration.value);//hours
	var durnType = parseFloat(f.task_duration_type.value); //1 or 24

	//temporary variables
	var inc = Math.floor(durn);
	var hoursToAddToLastDay = 0;
	var hoursToAddToFirstDay = durn;
	var fullWorkingDays = 0;
	var int_st_hour = e.getHours();

	// calculate the number of non-working days
	var k = 7 - working_days.length;

	var durnMins = (durn - inc) * 60;
	if ((e.getMinutes() + durnMins) >= 60)
		inc++;

	var mins = ( e.getMinutes() + durnMins ) % 60;
	if (mins > 38)
		e.setMinutes( 45 );
	else if (mins > 23)
		e.setMinutes( 30 );
	else if (mins > 8)
		e.setMinutes( 15 );
	else
		e.setMinutes( 0 );
	
	// jump over to the first working day
	for (var i = 0; i < k; i++){
		if ( !isInArray(working_days, e.getDay()) ) {
			e.setDate(e.getDate() + 1);
		}
	}
		
	if ( durnType==24 ) {
		fullWorkingDays = Math.ceil(inc)+1;
		e.setMinutes( 0 );

		// Include start day as a working day (if it is one)
		if ( isInArray(working_days, e.getDay()) ) fullWorkingDays--;

	 	for (var i = 0; i < fullWorkingDays; i++)
		{
			e.setDate(e.getDate() + 1);
			if ( !isInArray(working_days, e.getDay()) ) i--;			
		}
		
		f.end_hour.value = f.start_hour.value;
	} else {
		
		hoursToAddToFirstDay = inc;
		if ( e.getHours() + inc > cal_day_end )
			hoursToAddToFirstDay = cal_day_end - e.getHours();
		if ( hoursToAddToFirstDay > workHours )
			hoursToAddToFirstDay = workHours;
		inc -= hoursToAddToFirstDay;
		hoursToAddToLastDay = inc % workHours;
		fullWorkingDays = Math.floor((inc - hoursToAddToLastDay) / workHours);

		if (hoursToAddToLastDay <= 0)
			e.setHours(e.getHours()+hoursToAddToFirstDay);
		else
		{
			e.setHours(cal_day_start+hoursToAddToLastDay);
			e.setDate(e.getDate() + 1);
		}

			
		if ((e.getHours() == cal_day_end || (e.getHours() - int_st_hour) == workHours) && mins > 0)
		{
			e.setDate(e.getDate() + 1);
			e.setHours(cal_day_start);
		}
			
		f.end_minute.value = (e.getMinutes() < 10 ? "0"+e.getMinutes() : e.getMinutes());
		
		// boolean for setting later if we just found a non-working day
		// and therefore do not have to add a day in the next loop
		// (which would have caused to not respecting multiple non-working days after each other)
		var g = false;
	 	for (var i = 0; i < Math.ceil(fullWorkingDays); i++){
			if (!g) {
				e.setDate(e.getDate() + 1);
			}
			g = false;
			// calculate overriden non-working days
			if ( !isInArray(working_days, e.getDay()) ) {
				e.setDate(e.getDate() + 1);
				i--;
				g = true;
			}
		}
		f.end_hour.value = (e.getHours() < 10 ? "0"+e.getHours() : e.getHours());
	}
	
	var tz1 = "";
	var tz2 = "";

	// if there was no fullworkingday we have to check whether the end day is a working day 
	// and in the negative case postpone the end date by appropriate days
	for (var i = 0; i < 7-working_days.length; i++){
		// override  possible non-working enddays
		if ( !isInArray(working_days, e.getDay()) ) {
			e.setDate(e.getDate() + 1);
		}
	}

	if ( e.getDate() < 10 ) tz1 = "0";
	if ( (e.getMonth()+1) < 10 ) tz2 = "0";

	f.task_end_date.value = e.getUTCFullYear()+tz2+(e.getMonth()+1)+tz1+e.getDate();
	//f.end_date.value = tz2+(e.getMonth()+1)+"/"+tz1+e.getDate()+"/"+e.getUTCFullYear(); // MM/DD/YY
	//f.end_date.value = tz1+e.getDate()+"/"+tz2+(e.getMonth()+1)+"/"+e.getUTCFullYear(); // DD/MM/YY
	var url = 'index.php?m=public&a=date_format&dialog=1&field='+f.name+'.end_date&date=' + f.task_end_date.value;
	thread = window.frames['thread']; //document.getElementById('thread');
	thread.location = url;
	setAMPM(f.end_hour);
}

function changeRecordType(value){
	// if the record type is changed, then hide everything
	hideAllRows();
	// and how only those fields needed for the current type
	eval("show"+task_types[value]+"();");
}

var subForm = new Array();

function FormDefinition(id, form, check, save) {
	this.id = id;
	this.form = form;
	this.checkHandler = check;
	this.saveHandler = save;
	this.check = fd_check;
	this.save = fd_save;
	this.submit = fd_submit;
	this.seed = fd_seed;
}

function fd_check()
{
	if (this.checkHandler) {
		return this.checkHandler(this.form);
	} else {
		return true;
	}
}

function fd_save()
{
	if (this.saveHandler) {
		var copy_list = this.saveHandler(this.form);
		return copyForm(this.form, document.editFrm, copy_list);
	} else {
		return this.form.submit();
	}
}

function fd_submit()
{
	if (this.saveHandler)
		this.saveHandler(this.form);
	return this.form.submit();
}

function fd_seed()
{
	return copyForm(document.editFrm, this.form);
}

// Sub-form specific functions.
function checkDates(form) {
	if (can_edit_time_information && check_task_dates) {
		if (!form.task_start_date.value) {
			alert( task_start_msg );
			form.task_start_date.focus();
			return false;
		}
		if (!form.task_end_date.value) {
			alert( task_end_msg );
			form.task_end_date.focus();
			return false;
		}
		//check if the start date is > then end date
		var int_st_date = new String(form.task_start_date.value + form.start_hour.value + form.start_minute.value);
		var int_en_date = new String(form.task_end_date.value + form.end_hour.value + form.end_minute.value);

		var s = Date.UTC(int_st_date.substring(0,4),(int_st_date.substring(4,6)-1),int_st_date.substring(6,8), int_st_date.substring(8,10), int_st_date.substring(10,12));
		var e = Date.UTC(int_en_date.substring(0,4),(int_en_date.substring(4,6)-1),int_en_date.substring(6,8), int_en_date.substring(8,10), int_en_date.substring(10,12));
		if ( s > e ) {
			alert( 'End date is before start date!');
			return false;
		}
	}
	return true;
}

function copyForm(form, to, extras) {
	// Grab all of the elements in the form, and copy them
	// to the main form.  Do not copy hidden fields.
	var h = new HTMLex;
	for (var i = 0; i < form.elements.length; i++) {
		var elem = form.elements[i];
		if (elem.type == 'hidden') {
			// If we have anything in the extras array we check to see if we
			// need to copy it across
			if (!extras)
				continue;
			var found = false;
			for (var j = 0; j < extras.length; j++) {
				if (extras[j] == elem.name) {
				  found = true;
					break;
				}
			}
			if (! found)
				continue;
		}
		// Determine the node type, and determine the current value
		switch (elem.type) {
			case 'text':
			case 'textarea':
			case 'hidden':
				to.appendChild(h.addHidden(elem.name, elem.value));
				break;
			case 'select-one':
				if (elem.options.length > 0)
					to.appendChild(h.addHidden(elem.name, elem.options[elem.selectedIndex].value));
				break;
			case 'select-multiple':
				var sel = to.appendChild(h.addSelect(elem.name, false, true));
				for (var x = 0; x < elem.options.length; x++) {
					if (elem.options[x].selected) {
						sel.appendChild(h.addOption(elem.options[x].value, '', true));
					}
				}
				break;
			case 'radio':
			case 'checkbox':
				if (elem.checked) {
					to.appendChild(h.addHidden(elem.name, elem.value));
				}
				break;
		}
	}
	return true;
}

function saveDates(form) {
	if (can_edit_time_information) {
		if ( form.task_start_date.value.length > 0 ) {
			form.task_start_date.value += form.start_hour.value + form.start_minute.value;
		}
		if ( form.task_end_date.value.length > 0 ) {
			form.task_end_date.value += form.end_hour.value + form.end_minute.value;
		}
	}
	

	return new Array('task_start_date', 'task_end_date');
}

function saveDepend(form) {
	var dl = form.task_dependencies.length -1;
        hd = form.hdependencies;
	hd.value = "";
	for (dl; dl > -1; dl--){
		hd.value = "," + hd.value +","+ form.task_dependencies.options[dl].value;
	}
        return new Array('hdependencies');;
}

function checkDetail(form) {
	return true;
}

function saveDetail(form) {
	return null;
}

function checkResource(form) {
	return true;
}

function saveResource(form) {
	var fl = form.assigned.length -1;
	ha = form.hassign;
	ha.value = "";
	for (fl; fl > -1; fl--){
		ha.value = "," + ha.value +","+ form.assigned.options[fl].value;
	}
	return new Array('hassign', 'hperc_assign');
}
