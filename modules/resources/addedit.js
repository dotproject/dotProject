function submitIt(f)
{
  if (f.resource_name.value.length == 0) {
    alert('You must enter a name for the resource');
    return false;
  }

  f.submit();
  return true;
}
