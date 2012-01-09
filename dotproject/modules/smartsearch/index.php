<?php /* SMARTSEARCH$Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

$files = $AppUI->readFiles((DP_BASE_DIR . '/modules/' . $m . '/searchobjects'), '\.php$');
sort($files);

$keyword1 = dPgetCleanParam($_POST, 'keyword1', '');
$keyword2 = dPgetCleanParam($_POST, 'keyword2', '');
$keyword3 = dPgetCleanParam($_POST, 'keyword3', '');
$keyword4 = dPgetCleanParam($_POST, 'keyword4', '');
$all_words = dPgetCleanParam($_POST, 'allwords', '');
$mod_selection = dPgetCleanParam($_POST, 'modselection', '');
$advanced_search = dPgetCleanParam($_POST, 'advancedsearch', '');

if ($advanced_search == 'on') { 
	$ignore_specchar = dPgetCleanParam($_POST, 'ignorespecchar', '');
	$ignore_case = dPgetCleanParam($_POST, 'ignorecase', '');
	$display_all_flds = dPgetCleanParam($_POST, 'displayallflds', '');
	$show_empty = dPgetCleanParam($_POST, 'showempty', '');
}
else {
	$ignore_case = 'on';
}

?>
<script language="javascript" type="text/javascript">

	function focusOnSearchBox() {
		document.forms.frmSearch.keyword1.focus();
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
foreach ($files as $tmp) {
	$temp = mb_substr($tmp,0,-8);
?>							
		document.frmSearch.mod_<?php echo $temp ?>.checked=true;
<?php
}
?>
	}		

	function deselModAll() {
		<?php
$objarray = Array();
foreach ($files as $tmp) {
	$temp = mb_substr($tmp,0,-8);
?>							
		document.frmSearch.mod_<?php echo $temp ?>.checked=false;
<?php
}
?>
	}
	
	window.onload = focusOnSearchBox;
</script>

<?php
$titleBlock = new CTitleBlock('SmartSearch', 'kfind.png', $m, $m . '.' . $a);
$titleBlock->show();
?>
<form name="frmSearch" action="?m=<?php echo $m ;?>"  method="post">
<table cellspacing="5" cellpadding="0" border="0">
<tr>
	<td align="left">
		<div id="div_advancedsearch1" name="div_advancedsearch1"  style="<?php 
echo ($advanced_search == 'on' ? 'visibility:visible':'visibility:hidden'); ?> ">
		 1.
		</div>
	</td>
	<td align="left">
		<input class="text" size="18" type="text" id="keyword1" name="keyword1" value="<?php 
echo stripslashes($keyword1); ?>" />
	</td>
	<td align="left">
		<input class="button" type="submit" value="<?php echo $AppUI->_('Search'); ?>" />
	</td>
	<td align="left">
		<input name="allwords" id="allwords" type="checkbox" value="on"<?php 
echo (($all_words == 'on') ? ' checked="checked"' : ''); ?> /> 
		<label for="all_words"><?php echo $AppUI->_('All words');?></label>
	</td>
	<td align="left">
		<input name="modselection" id="modselection" type="checkbox" value="on"<?php 
echo (($mod_selection == 'on') ? ' checked="checked"' : ''); ?> onclick="javascript:toggleModules(this)" />
		<label for="modselection"><?php 
echo $AppUI->_('Modules selection');?></label>
	</td>
	<td align="left">
		<input name="advancedsearch" id="advancedsearch" type="checkbox"<?php 
echo (($advanced_search == 'on') ? ' checked="checked"' : ''); ?> onclick="javascript:toggleStatus(this)" />
		<label for="advancedsearch"><?php echo $AppUI->_('Advanced search'); ?></label>
	</td>
</tr>
</table>
<div id="div_advancedsearch" name="div_advancedsearch"  style="<?php 
echo ($advanced_search== 'on' ? 'display:block' : 'display:none'); ?> ">
<table cellspacing="5" cellpadding="0" border="0">
<tr>
	<td align="left">
		<label for="keyword2">2.</label>
		<input class="text" size="18" type="text" id="keyword2" name="keyword2" value="<?php 
echo stripslashes($keyword2); ?>" />
	</td>
	<td align="left">
		<label for="keyword3">3.</label>
		<input class="text" size="18" type="text" id="keyword3" name="keyword3" value="<?php 
echo stripslashes($keyword3); ?>" />
	</td>
	<td align="left">
		<label for="keyword4">4.</label>
		<input class="text" size="18" type="text" id="keyword4" name="keyword4" value="<?php 
echo stripslashes($keyword4); ?>" />
	</td>
	<td align="left">
		<input name="ignorespecchar" id="ignorespecchar" type="checkbox" value="on"<?php 
echo (($ignore_specchar == 'on') ? ' checked="checked"' : ''); ?> />
		<label for="ignorespecchar"><?php echo $AppUI->_('Ignore special chars'); ?></label>
	</td>
	<td align="left">
		<input name="ignorecase" id="ignorecase" type="checkbox" value="on"<?php 
echo (($ignore_case == 'on') ? ' checked="checked"' : ''); ?> />
		<label for="ignorecase"><?php echo $AppUI->_('Ignore case'); ?></label>
	</td>
	<td align="left">
		<input name="displayallflds" id="displayallflds" type="checkbox" value="on"<?php 
echo (($display_all_flds == 'on') ? ' checked="checked"' : ''); ?> />
		<label for="displayallflds"><?php echo $AppUI->_('Display all fields'); ?></label>
	</td>
	<td align="left">
		<input name="showempty" id="showempty" type="checkbox" value="on"<?php 
echo (($show_empty == 'on') ? ' checked="checked"' : ''); ?> />
		<label for="showempty"><?php echo $AppUI->_('Show empty'); ?></label>
	</td>
</tr>
</table>
</div>
<div id="div_selmodules" name="div_selmodules"  style="<?php 
echo ($mod_selection == 'on' ? 'display:block' : 'display:none'); ?> ">
<table cellspacing="0" cellpadding="0" border="0">
<tr>
	<td>
		<a href="#" onclick="javascript:selModAll(this)"><?php echo $AppUI->_('Select all'); ?></a> | 
		<a href="#" onclick="javascript:deselModAll(this)"><?php echo $AppUI->_('Deselect all'); ?></a>
	</td>
</tr>
<?php
$objarray = Array();
foreach ($files as $tmp) {
	$temp = mb_substr($tmp,0,-8);
	require_once('./modules/' . $m . '/searchobjects/' . $tmp);
	
	$class_obj = new $temp();
	$mod_select[$temp] = dPgetCleanParam($_POST, ('mod_' . $temp), '');
	
	if (getPermission($class_obj->table_module, 'access')) {
?>							
<tr>
	<td align="left">
		<input name="mod_<?php echo $temp; ?>" id="mod_<?php echo $temp; ?>" type="checkbox" value="on"<?php 
		echo (($mod_select[$temp] == 'on') ? ' checked="checked"' : ''); ?> />
		<label for="mod_<?php echo $temp; ?>"><?php echo $AppUI->_($class_obj->table_title); ?></label>
	</td>
</tr>
<?php 
	}
}
?>
</table>
</div>
</form>

<?php
if ($keyword1) { 
	$search = new smartsearch();
	$search->keyword = addslashes($keyword1);
	
	$keywords = array();
	for ($x = 1; $x <= 4; $x++) {
		if (isset(${('keyword' . $x)}) && mb_strlen(${('keyword'.$x)}) > 0) {
			$or_keywords = preg_split('/[\s,;]+/', addslashes(${('keyword'.$x)}));
			foreach ($or_keywords as $or_keyword) {
				$keywords[$or_keyword][0] = $or_keyword;
				$keywords[$or_keyword][1] = $x -1;
			}
		} else {
			$or_keywords = preg_split('/[\s,;]+/', addslashes(${('keyword'.$x)}));
			foreach ($or_keywords as $or_keyword) {
				unset($keywords[$or_keyword]);
			}
		}
	}

?>

<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<?php
$reccount = 0;
foreach ($files as $tmp) {
	require_once('./modules/' . $m . '/searchobjects/' . $tmp);
	$temp = mb_substr($tmp, 0, -8);
	
	$search_opts['all_words'] = $all_words;
	
	$search_opts['ignore_specchar'] = $ignore_specchar;
	$search_opts['ignore_case'] = $ignore_case;
	$search_opts['display_all_flds'] = $display_all_flds;
	$search_opts['show_empty'] = $show_empty;
	
	$search_opts['keywords'] = $keywords;
	  
	if ($mod_selection != 'on' || $mod_select[$temp] == 'on') {
		$class_search = new $temp();
		$class_search->setKeyword($search->keyword);
		$class_search->setAdvanced($search_opts);
		$results = $class_search->fetchResults($reccount);
		echo $results;
	}
}
echo ('<tr><td><b>' . $AppUI->_('Total records found') . ': ' . $reccount . '</b></td></tr>');
?>
</table>
<?php 
} 
?>
