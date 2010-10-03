// $Id$
// Task view support routines.
function popEmailContacts() {
	updateEmailContacts();
	var email_others = document.getElementById('email_others');
	window.open(
	  '?m=public&'+'a=contact_selector&'+'dialog=1&'+'call_back=setEmailContacts&'
		+ 'selected_contacts_id='+ email_others.value, 
		'contacts','height=600,width=400,resizable,scrollbars=yes');
}

function setEmailContacts(contact_id_string) {
	if (! contact_id_string)
		contact_id_string = "";
	var email_others = document.getElementById('email_others');
	email_others.value = contact_id_string;
}

function updateEmailContacts() {
	var email_others = document.getElementById('email_others');
	var task_emails = document.getElementById('email_task_list');
	var proj_emails = document.getElementById('email_project_list');
	var do_task_emails = document.getElementById('email_task_contacts');
	var do_proj_emails = document.getElementById('email_project_contacts');

	// Build array out of list of contact ids.
	var email_list = email_others.value.split(',');
	if (do_task_emails.checked) {
		var telist = task_emails.value.split(',');
		var full_list = email_list.concat(telist);
		email_list = full_list;
		do_task_emails.checked = false;
	}

	if (do_proj_emails.checked) {
		var prlist = proj_emails.value.split(',');
		var full_proj = email_list.concat(prlist);
		email_list = full_proj;
		do_proj_emails.checked = false;
	}

	// Now do a reduction
	email_list.sort();
	var output_array = new Array();
	var last_elem = -1;
	for (var i = 0; i < email_list.length; i++) {
		if (email_list[i] == last_elem) {
			continue;
		}
		last_elem = email_list[i];
		output_array.push(email_list[i]);
	}
	email_others.value = output_array.join();
}

function emailNumericCompare(a, b) {
	return a - b;
}
