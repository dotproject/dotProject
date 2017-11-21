function submitPolicies(f)
{
	if(f.company_policies_recognition.value == '' &&
		f.company_policies_policy.value == '' &&
		f.company_policies_safety.value == '') {
		alert('You must add policies.');
		return false;
	}
	f.submit();
  	return true;
}