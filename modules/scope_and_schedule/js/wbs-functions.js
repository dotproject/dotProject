$(document).ready(function (){

	$( ".draggable_wbs" ).draggable();

	$( ".draggable_wbs" ).draggable({
		
		drag: function( event, ui ) {
			//alert("testing drag");
		}
		
		});  

		$( ".draggable_wbs" ).draggable({
			stop: function( event, ui ) {  
				//if position is not on a droppable element, reset
				//alert(this.OriginalX + " | "+ this.OriginalX );
				$(this).css("top",this.OriginalY);
				$(this).css("left",this.OriginalX);

			}
		
		});
		
		$( ".draggable_wbs" ).draggable({
		
		
		start: function( event, ui) {
			 this.OriginalX= ui.position.left;
			 this.OriginalY= ui.position.top;	
		  }
		
		});
	


	
	 
		 
/*		 
	$( ".draggable" ).droppable({
		 
    });
	*/
	
  });  
	