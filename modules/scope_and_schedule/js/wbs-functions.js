$(document).ready(function (){

     dialog = $( "#dialog_move_wbs" ).dialog({
      autoOpen: false,
      height: 200,
      width: 350,
      modal: true,
      buttons: {
		/*
        Cancel: function() {
          dialog.dialog( "close" );
        }
		*/
      },
      close: function() {
        
      }
    });
 
  });  
  
  function openMoveWBSItem(wbsItemId, wbsItemName , projectId){
	  document.move_wbs_item.id.value=wbsItemId;
	  document.getElementById("move_wbs_item_name").innerHTML = wbsItemName;
	  dialog.dialog( "open" );
  }
  
    function submitMoveItem(){
	  document.move_wbs_item.submit();
  }
	
  function closeMoveWBSItem(){
	  dialog.dialog( "close" );
  }
  
  function ajaxFormSubmit(formId){
	$.post($("#"+formId).attr('action'), $("#"+formId).serialize())
  }
	