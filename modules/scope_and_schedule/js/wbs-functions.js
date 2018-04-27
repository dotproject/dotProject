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
	
	 dialogDictionary = $( "#dialog_wbs_dictionary" ).dialog({
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
	
	
	dialogMoveActivityOrder = $( "#dialog_move_activity" ).dialog({
      autoOpen: false,
      height: 240,
      width: 450,
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
	
	
	 dialogMoveActivity = $( "#dialog_move_project_activity" ).dialog({
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
  
  
  function openDialogMoveActivity(activityId, activityDescription){
	  document.move_project_activity.task_id.value=activityId;
	  document.getElementById("move_activity_description").innerHTML = activityDescription;
	  dialogMoveActivity.dialog("open");
  }
  
 
  
  function closeMoveActivity(){
	  dialogMoveActivity.dialog( "close" );
  }
  
  function openDialogWBSDictionary(wbsItemId, wbsItemName, wbsDictionaryDescription){
	  document.wbs_dictionary.id.value=wbsItemId;  
	  document.wbs_dictionary.dictionary.value=wbsDictionaryDescription;
	  document.getElementById("dictionary_wbs_item_name").innerHTML = wbsItemName;
	  dialogDictionary.dialog("open");
  }
  
    function closeDialogWBSDictionary(){
	  dialogDictionary.dialog( "close" );
  }
  
  function openMoveWBSItem(wbsItemId, wbsItemName , projectId){
	  document.move_wbs_item.id.value=wbsItemId;
	  document.getElementById("move_wbs_item_name").innerHTML = wbsItemName;
	  dialog.dialog( "open" );
  }
  
  
  function openMoveActivity(wbsItemId, activityId, activityName){
	  document.move_activity.task_id.value=activityId;
	  $("#move_activity_name").html(activityName);
	  $("#move_activity_wbs_item_id").val(wbsItemId);
	  dialogMoveActivityOrder.dialog( "open" );
  }
  
  function closeMoveActivityOrder(){
	  dialogMoveActivityOrder.dialog( "close" );
  }
  
  
    function submitMoveItem(){
	  document.move_wbs_item.submit();
  }
	
  function closeMoveWBSItem(){
	  dialog.dialog( "close" );
  }
  
  function ajaxFormSubmit(formId){
	
	$.post($("#"+formId).attr('action'), $("#"+formId).serialize(), function( data ) {
		alertify.success("Data successfully saved.");
		console.log(data);
	});
  }
  
  function verifyResourceSelection(field){
	  var result=true;
	  if(field.options[field.selectedIndex].value==-1){
		alertify.error("Please select a resource to add."); 
		result=false;
	  }
	  return result;
  }
	