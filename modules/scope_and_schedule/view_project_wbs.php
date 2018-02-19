<?php
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}
require_once DP_BASE_DIR . "/modules/scope_and_schedule/wbs_item.class.php";
$project_id = dPgetParam($_GET, "project_id", 0);
$_SESSION["wbsItemsArray"]= array();
function printWBSItem($wbsItem){
		global $project_id;
		//get children
		$listChildren=$wbsItem->loadWBSItems($project_id, $wbsItem->id);
		//update is leaf/work package attribute
		$wbsItem->is_leaf=sizeof($listChildren)==0?1:0;
		$wbsItem->store();
		//update global list: for dropdown and other on screen functions
		array_push($_SESSION["wbsItemsArray"], $wbsItem);
		
		?>
		<br />
			<li>  &nbsp;			
			<form action="?m=scope_and_schedule" name="wbs_add_child_<?php echo $wbsItem->id ?>" method="post" style="display:inline">
				<input type="hidden" name="dosql" value="do_wbs_item_aed">
				<input type="hidden" name="project_id" value="<?php echo $project_id ?>" /> 
				<input type="hidden" name="sort_order" value="1" />
				<input type="hidden" name="number" value="1" />
				<input type="hidden" name="is_leaf" value="0" />  	
				<input type="hidden" name="id_wbs_item_parent" value="<?php echo $wbsItem->id ?>" />
				<input type="hidden" name="item_name" placeholder="Input item description..." /> 
				<img src="modules/scope_and_schedule/images/add_button_icon.png" style="cursor:pointer;height:18px;width:18px" onclick="saveScrollPosition();document.wbs_add_child_<?php echo $wbsItem->id ?>.submit();" />
			</form>
			<?php if ($wbsItem->id_wbs_item_parent!=0){?> 
				<img src="modules/scope_and_schedule/images/reorder_icon.png" style="cursor:pointer;height:18px;width:18px"  id="wbs_move_<?php echo $wbsItem->id ?>"   onclick="saveScrollPosition();openMoveWBSItem(<?php echo $wbsItem->id ?>, <?php echo $project_id ?>);" />
			<?php  } ?> 
			<?php 
			if ($wbsItem->is_leaf==1){
			?>
				&nbsp;&nbsp; <img src="modules/scope_and_schedule/images/work_package_icon.png" style="cursor:pointer;height:15px;width:15px" onclick="saveScrollPosition();" />
			<?php } ?>
			<br />
			<form action="?m=scope_and_schedule" name="wbs_delete_<?php echo $wbsItem->id ?>" method="post" style="display:inline">
				<input type="hidden" name="dosql" value="do_wbs_item_deletion">
				<input type="hidden" name="project_id" value="<?php echo $project_id ?>" /> 
				<input type="hidden" name="id" value="<?php echo $wbsItem->id ?>" /> 
				<img src="modules/scope_and_schedule/images/trash-icon.png" style="cursor:pointer;height:15px;width:12px" onclick="saveScrollPosition();document.wbs_delete_<?php echo $wbsItem->id ?>.submit();" />
			</form>
			
			
			
				<form action="?m=scope_and_schedule" name="wbs_update_<?php echo $wbsItem->id ?>" id="wbs_update_<?php echo $wbsItem->id ?>" method="post" style="display:inline">
					<input type="hidden" name="dosql" value="do_wbs_item_aed">
					<input type="hidden" name="project_id" value="<?php echo $project_id ?>" /> 
					<input type="hidden" name="id" value="<?php echo $wbsItem->id ?>" /> 
					<input type="hidden" name="sort_order" value="1" />
					<input type="hidden" name="number" value="1" />
					<input type="hidden" name="is_leaf" value="0" />  	
					<input type="hidden" name="id_wbs_item_parent" value="<?php echo $wbsItem->id_wbs_item_parent ?>" />
					<input type="text" name="item_name" placeholder="Input item description..." value="<?php echo $wbsItem->item_name; ?>" onblur="saveScrollPosition();ajaxFormSubmit('wbs_update_<?php echo $wbsItem->id ?>');" style="width:40%" maxlength="100" /> 			
				</form>
				
				<ol>
					<?php
						$order=1;
						foreach ($listChildren as $child){
							$child->sort_order=$order;
							$child->number=$wbsItem->number.".".$order;
							$child->store();
							printWBSItem($child);
							$order++;
						}
					?>
				</ol>
				
			</li>
			
<?php
}
?>
<script src="modules/scope_and_schedule/js/jquery-3.2.1.min.js"></script>
<script src="modules/scope_and_schedule/js/jquery-ui.js"></script>
<script src="modules/scope_and_schedule/js/wbs-functions.js"></script>
 <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script>
//function should be called after any submit in wbs page
function saveScrollPosition(){
		var y= window.scrollY;
	    window.sessionStorage.setItem('wbsScrollY',y);
}
</script>
<style>
.wbs {}
.wbs OL { counter-reset: item }
.wbs LI { display: inline;}
.wbs LI:before { content: counters(item, ".") " "; counter-increment: item }
</style>


<span class="wbs">
	<ol>
	<?php
	$wbsItem= new WBSItem ();
	$list=$wbsItem->loadWBSItems($project_id, 0);//seed of recursive procedure, the root WBS Item

	if(sizeof($list)==0){
		// there is no WBS, start creating the root one - automatically utilizing the project name
		$wbsItem->id_wbs_item_parent=0;
		$wbsItem->project_id = $project_id;
		$wbsItem->sort_order = 0;
		$wbsItem->item_name  = "";
		$wbsItem->number  = 1;
		$wbsItem->is_leaf = 1;
		$wbsItem->store();
		$list[0]=$wbsItem;
	}
	printWBSItem($list[0]);
	
	?>
	</ol>
</span>

<div id="dialog_move_wbs" title="Move WBS item" style="background-color:FFF">
<form name="move_wbs_item" action="?m=scope_and_schedule" method="post">
<input type="hidden" name="dosql" value="do_wbs_item_move" />
<input type="hidden" name="project_id" value="<?php echo $project_id ?>" />
<input type="hidden" name="id" value="" /> 
  Move to position:<br />
  <select name="wbs_id_position"> 
  <?php
  foreach ($_SESSION["wbsItemsArray"] as $wbsItem){
	  if($wbsItem->number != 1){
	  ?>
	  <option value="<?php echo $wbsItem->id  ?>"><?php echo $wbsItem->number  ?></option>
  <?php
	  }
  }
  ?>
  </select>
  <br /><br />
  Order: <br />
  <select name="order">
	<option value="-0.1">Before</option> 
	<option value="0.1">After </option>
	</select> 
	<br /><br />
     <input type="button" onclick="submitMoveItem()" value="Confirm" />
    <input type="button" value="Cancel" onclick="closeMoveWBSItem()" />
	</form>
</div>


<script>
	//keep the scroll position in the same position after user perform submit action in wbs
	var y=window.sessionStorage.getItem('wbsScrollY');
	if(y!=null && y !=""){
		window.scrollTo(0, y)
	}
</script>