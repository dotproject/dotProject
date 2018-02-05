<?php
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

?>
<?php $project_id = dPgetParam($_GET, "project_id", 0); ?>

<style>
.wbs {}
.wbs OL { counter-reset: item }
.wbs LI { display: inline;}
.wbs LI:before { content: counters(item, ".") " "; counter-increment: item }
</style>

<span class="wbs">
<ol>
<br />
<li>&nbsp;<img src="modules/scope_and_schedule/images/add_green.png" style="cursor:pointer;height:15px;width:15px" />
	<form action="?m=scope_and_schedule" method="post">
		<input type="hidden" name="dosql" value="do_wbs_item_aed">
		<input type="hidden" name="project_id" value="<?php echo $project_id ?>" /> 
		<input type="hidden" name="sort_order" value="1" />
		<input type="hidden" name="number" value="1" />
		<input type="hidden" name="is_leaf" value="0" />  	
		<input type="hidden" name="id_wbs_item_parent" value="" />
		<input type="text" name="item_name" placeholder="Scope and Schedule module" style="width:40%" maxlength="100" /> 
		
		<img src="modules/scope_and_schedule/images/trash_small.gif" style="cursor:pointer" />
	</form>
	<ol>
		<!-- Repetitive - make programatically -->
		<br />
		<li>&nbsp;<img src="modules/scope_and_schedule/images/add_green.png" style="cursor:pointer;height:15px;width:15px" />
			<form action="?m=scope_and_schedule" method="post">
				<input type="hidden" name="dosql" value="do_wbs_item_aed">
				<input type="hidden" name="project_id" value="<?php echo $project_id ?>" /> 
				<input type="hidden" name="sort_order" value="1" />
				<input type="hidden" name="number" value="1" />
				<input type="hidden" name="is_leaf" value="0" />  	
				<input type="hidden" name="id_wbs_item_parent" value="" />
				<input type="text" name="item_name" placeholder="WBS definition"  style="width:40%" maxlength="100" /> 
				<img src="modules/scope_and_schedule/images/trash_small.gif" style="cursor:pointer" />
			</form>
			<ol>
			
			</ol>
		</li>
		<br />
		<li>&nbsp;<img src="modules/scope_and_schedule/images/add_green.png" style="cursor:pointer;height:15px;width:15px" />
			<form action="?m=scope_and_schedule" method="post">
				<input type="hidden" name="dosql" value="do_wbs_item_aed">
				<input type="hidden" name="project_id" value="<?php echo $project_id ?>" /> 
				<input type="hidden" name="sort_order" value="1" />
				<input type="hidden" name="number" value="1" />
				<input type="hidden" name="is_leaf" value="0" />  	
				<input type="hidden" name="id_wbs_item_parent" value="" />
				<input type="text" name="item_name" placeholder="Project activities derivation"  style="width:40%" maxlength="100" /> 
				<img src="modules/scope_and_schedule/images/trash_small.gif" style="cursor:pointer" />
			</form>
			<ol>
			
			</ol>
		</li>
		<!-- Repetitive - make programatically -->
	</ol>
</li>
</ol>
</span>