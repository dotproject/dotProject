function submitAllocation(f)
{
	if (f.user_id.value == '') {
    		alert('You must allocate some user');
    		return false;
  	}
  	f.submit();
  	return true;
}