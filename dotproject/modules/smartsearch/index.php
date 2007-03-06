<?php /* SMARTSEARCH$Id$ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

//--MSy--
$files = $AppUI->readFiles( DP_BASE_DIR."/modules/$m/searchobjects", "\.php$" );
$ssearch=ARRAY();
$ssearch['keywords']=ARRAY();

$ssearch['advanced_search']=dPgetParam($_POST, 'advancedsearch', "");

$ssearch['mod_selection']=dPgetParam($_POST, 'modselection', "");
sort($files);
foreach ($files as $tmp){
	$temp = substr($tmp,0,-8);
	$ssearch["mod_$temp"]=dPgetParam($_POST, "mod_$temp", "");
}
	

$ssearch['all_words']=dPgetParam($_POST, 'allwords', "");

if($ssearch['advanced_search']=="on") { 
	$ssearch['ignore_case']=dPgetParam($_POST, 'ignorecase', "");
	$ssearch['ignore_specchar']=dPgetParam($_POST, 'ignorespecchar', "");
	$ssearch['display_all_flds']=dPgetParam($_POST, 'displayallflds', "");
	$ssearch['show_empty']=dPgetParam($_POST, 'showempty', "");
}
else {
	$ssearch['ignore_case']="on";
	$ssearch['ignore_specchar']="";
	$ssearch['display_all_flds']="";
	$ssearch['show_empty']="";
}

?>
<script language="JavaScript">

	function focusOnSearchBox() {
		document.forms.frmSearch.keyword.focus();
	}
	function toggleStatus(obj) {
		if (obj.checked) {
				var block=document.getElementById("div_advancedsearch");
				block.style.display="block";
				var block1=document.getElementById("div_advancedsearch1");
				block1.style.visibility="visible";
			}
		else {
				var block=document.getElementById("div_advancedsearch");
				block.style.display="none";
				var block1=document.getElementById("div_advancedsearch1");
				block1.style.visibility="hidden";
				var key2=document.getElementById("keyword2");
				key2.value="";
				var key3=document.getElementById("keyword3");
				key3.value="";
				var key4=document.getElementById("keyword4");
				key4.value="";
			}
	}

	function toggleModules(obj) {
		var block=document.getElementById("div_selmodules");
		
		if (obj.checked) {
				block.style.display="block";
			}
		else {
				block.style.display="none";
			}
	}
	
	function selModAll() {
		<?php
		$objarray = Array();
		foreach ($files as $tmp){
			$temp = substr($tmp,0,-8);
			?>							
		document.frmSearch.mod_<?php echo $temp ?>.checked=true;
			<?php
		}
		?>
	}		

	function deselModAll() {
		<?php
		$objarray = Array();
		foreach ($files as $tmp){
			$temp = substr($tmp,0,-8);
			?>							
		document.frmSearch.mod_<?php echo $temp ?>.checked=false;
			<?php
		}
		?>
	}		

	
	window.onload = focusOnSearchBox;

</script>


<?php
     $titleBlock = new CTitleBlock( 'SmartSearch', 'kfind.png', $m, "$m.$a" );
     $titleBlock->show();
?>
	<form name="frmSearch" action="?m=<?php echo $m ;?>"  method="POST">
			<table cellspacing="5" cellpadding="0" border="0">
				<tr>
					<td align="left">
					<div id="div_advancedsearch1" name="div_advancedsearch1"  style="<?php  echo ($ssearch['advanced_search']=="on" ? 'visibility:visible':'visibility:hidden'); ?> "> 1. </div></td>
					<td align="left"><INPUT class="text" size="18" type="text" id="keyword" name="keyword" value="<?php echo @stripslashes($_POST['keyword']); ?>"></td>
					<td align="left"><input class="button" type="submit" value="<?php echo $AppUI->_('Search');?>"></td>
					<td align="left"><input  name="allwords" id="allwords" type="checkbox"  <?php  echo ($ssearch['all_words']=="on" ? 'checked="checked"':""); ?>  > <?php echo $AppUI->_('All words');?></td>
					<td align="left"><input  name="modselection" id="modselection" type="checkbox"  <?php  echo ($ssearch['mod_selection']=="on" ? 'checked="checked"':""); ?> onclick="toggleModules(this)" > <?php echo $AppUI->_('Modules selection');?></td>
					<td align="left"><input name="advancedsearch" id="advancedsearch" type="checkbox" <?php  echo ($ssearch['advanced_search']=="on" ? 'checked="checked"':""); ?> onclick="toggleStatus(this)" > <?php echo $AppUI->_('Advanced search');?></td>
				</tr>
			</table>
			<div id="div_advancedsearch" name="div_advancedsearch"  style="<?php  echo ($ssearch['advanced_search']=="on" ? 'display:block':'display:none'); ?> ">
				<table cellspacing="5" cellpadding="0" border="0">
					<tr>
						<td align="left"> 2. </td>
						<td align="left"><INPUT class="text" size="18" type="text" id="keyword2" name="keyword2" value="<?php echo @stripslashes($_POST['keyword2']); ?>"></td>
						<td align="left"> 3. <INPUT class="text" size="18" type="text" id="keyword3" name="keyword3" value="<?php echo @stripslashes($_POST['keyword3']); ?>"></td>
						<td align="left"> 4. <INPUT class="text" size="18" type="text" id="keyword4" name="keyword4" value="<?php echo @stripslashes($_POST['keyword4']); ?>"></td>
						<td align="left"><input  name="ignorespecchar" id="ignorespecchar" type="checkbox"  <?php  echo ($ssearch['ignore_specchar']=="on" ? 'checked="checked"':""); ?>  > <?php echo $AppUI->_('Ignore special chars');?></td>
						<td align="left"><input  name="ignorecase" id="ignorecase" type="checkbox"  <?php  echo ($ssearch['ignore_case']=="on" ? 'checked="checked"':""); ?>  > <?php echo $AppUI->_('Ignore case');?></td>
						<td align="left"><input  name="displayallflds" id="displayallflds" type="checkbox"  <?php  echo ($ssearch['display_all_flds']=="on" ? 'checked="checked"':""); ?>  > <?php echo $AppUI->_('Display all fields');?></td>
						<td align="left"><input  name="showempty" id="showempty" type="checkbox"  <?php  echo ($ssearch['show_empty']=="on" ? 'checked="checked"':""); ?>  > <?php echo $AppUI->_('Show empty');?></td>
					</tr>
				</table>
			</div>
			<div id="div_selmodules" name="div_selmodules"  style="<?php  echo ($ssearch['mod_selection']=="on" ? 'display:block':'display:none'); ?> ">
				<table cellspacing="0" cellpadding="0" border="0">
				<tr><td><a href="#" onclick="selModAll(this)"><?php echo $AppUI->_('Select all'); ?></a> | <a href="#" onclick="deselModAll(this)"><?php echo $AppUI->_('Deselect all'); ?></a></td></tr>
						<?php
						$objarray = Array();
						foreach ($files as $tmp){
							require_once("./modules/$m/searchobjects/".$tmp);
							$temp = substr($tmp,0,-8);
							$tempf=$temp.'()';
							eval ("\$class_obj = new $tempf;");
							$temp_title = $class_obj->table_title;
							$objarray[$temp]=$temp_title;
						?>							
				<tr><td align="left"><input name="mod_<?php echo $temp; ?>" id="mod_<?php echo $temp; ?>" type="checkbox" 
						<?php 
							echo ($ssearch["mod_$temp"]=='on') ? "checked='checked'" :'';
							echo '>'.$AppUI->_($objarray[$temp]);
						?> 
				</td></tr>
				<?php 	}?>
				</table>
			</div>
	</form>
<?php
if (isset ($_POST['keyword']))
{ 
	$search = new smartsearch();
	$search->keyword = addslashes($_POST['keyword']);

	if (isset ($_POST['keyword']) && strlen($_POST['keyword'])>0) {
		$or_keywords = preg_split('/[\s,;]+/', addslashes($_POST['keyword']));
		foreach ($or_keywords as $or_keyword) {
			$ssearch['keywords'][$or_keyword]=Array($or_keyword);
			$ssearch['keywords'][$or_keyword][1]=0;
		}
	} else {
		$or_keywords = preg_split('/[\s,;]+/', addslashes($_POST['keyword']));
		foreach ($or_keywords as $or_keyword) {
			unset($ssearch['keywords'][$or_keyword]);
		}
	}
	
	if (isset ($_POST['keyword2']) && strlen($_POST['keyword2'])>0) {
		$or_keywords = preg_split('/[\s,;]+/', addslashes($_POST['keyword2']));
		foreach ($or_keywords as $or_keyword) {
			$ssearch['keywords'][$or_keyword]=Array($or_keyword);
			$ssearch['keywords'][$or_keyword][1]=1;
		}
	} else {
		$or_keywords = preg_split('/[\s,;]+/', addslashes($_POST['keyword2']));
		foreach ($or_keywords as $or_keyword) {
			unset($ssearch['keywords'][$or_keyword]);
		}
	}
	
	if (isset ($_POST['keyword3']) && strlen($_POST['keyword3'])>0) {
		$or_keywords = preg_split('/[\s,;]+/', addslashes($_POST['keyword3']));
		foreach ($or_keywords as $or_keyword) {
			$ssearch['keywords'][$or_keyword]=Array($or_keyword);
			$ssearch['keywords'][$or_keyword][1]=2;
		}
	} else {
		$or_keywords = preg_split('/[\s,;]+/', addslashes($_POST['keyword3']));
		foreach ($or_keywords as $or_keyword) {
			unset($ssearch['keywords'][$or_keyword]);
		}
	}

	
	if (isset ($_POST['keyword4']) && strlen($_POST['keyword4'])>0) {
		$or_keywords = preg_split('/[\s,;]+/', addslashes($_POST['keyword4']));
		foreach ($or_keywords as $or_keyword) {
			$ssearch['keywords'][$or_keyword]=Array($or_keyword);
			$ssearch['keywords'][$or_keyword][1]=3;
		}
	} else {
		$or_keywords = preg_split('/[\s,;]+/', addslashes($_POST['keyword4']));
		foreach ($or_keywords as $or_keyword) {
			unset($ssearch['keywords'][$or_keyword]);
		}
	}

?>

	<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
	<?php
    $perms = &$AppUI->acl();
	sort($files);
	$reccount = 0;
	foreach ($files as $tmp){
		require_once("./modules/$m/searchobjects/".$tmp);
		$temp = substr($tmp,0,-8);
		if ($ssearch["mod_selection"]=='' || $ssearch["mod_$temp"]=='on') {
			$temp .= '()';	
			eval ("\$class_search = new $temp;");
			$class_search->setKeyword($search->keyword);
			if (method_exists($class_search,'setAdvanced'))
				$class_search->setAdvanced($ssearch);
			$results = $class_search->fetchResults($perms, $reccount);
			echo $results;
		}
	}
	echo "<tr><td><b>".$AppUI->_('Total records found').': '.$reccount."</b></td></tr>";
?>
</table>
<?php 
} 
?>
