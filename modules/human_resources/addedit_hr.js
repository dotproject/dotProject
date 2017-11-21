 function submitHumanResource(f)
{
	var rolesIds = document.getElementById("roles_ids");
	var rolesTable = document.getElementById("roles_table");
	var lastRow = rolesTable.rows.length;
	rolesIds.value = "";
	if(rolesTable.rows[1] != null) {
		rolesIds.value = rolesTable.rows[1].children[0].children[0].getAttribute('id')
		for(i = 2; i < lastRow; i++) {
			rolesIds.value += "," + rolesTable.rows[i].children[0].children[0].getAttribute('id');
		}

	}
	
	if(f.human_resource_mon.value > f.daily_working_hours.value
|| f.human_resource_tue.value > f.daily_working_hours.value
|| f.human_resource_wed.value > f.daily_working_hours.value
|| f.human_resource_thu.value > f.daily_working_hours.value
|| f.human_resource_fri.value > f.daily_working_hours.value
|| f.human_resource_sat.value > f.daily_working_hours.value
|| f.human_resource_sun.value > f.daily_working_hours.value) {
		alert('Number greater than daily working hours');
    		return false;
	}
  	f.submit();
  	return true;
}