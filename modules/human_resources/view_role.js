function submitRole(f){
	if(f.human_resources_role_name.value.length == 0) {
		alert('You must enter a role name.');
    		return false;
	}
  	f.submit();
  	return true;
}

 function delIt() {
        if (window.confirm(document.editfrm.del_msg.value)) {
            var f = document.editfrm;
            f.del.value = "1";
            f.submit();
        }
    }