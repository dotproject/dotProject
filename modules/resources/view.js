var delete_msg = '';
var can_delete = false;

function delIt() {
  if (can_delete) {
    if (confirm(delete_msg)) {
      document.frmDelete.submit();
    }
  } else {
    alert('Function not permitted');
  }
}
