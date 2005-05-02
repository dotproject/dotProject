<?php

function highlight($text, $key)
{
	return str_replace($key, '<span style="background: yellow">'.$key.'</span>', $text);
}

$files = $AppUI->readFiles( dPgetConfig( 'root_dir' )."/modules/smartsearch/searchobjects", "\.php$" );

require_once( $AppUI->getModuleClass('smartsearch') );
?>
<table width="100%" border="0" cellpadding="0" cellspacing=1>
	<tr>
		<td nowrap width="100%"><h1><?php echo $AppUI->_('Search')?></h1></td>
	</tr>
</table>
	<form name="frmSearch" action="?m=smartsearch"  method="POST">
			<table cellspacing="10" cellpadding="0" border="0">
				<tr>
					<td align="left"><INPUT class="text" type="text" id="keyword" name="keyword" value="<?php echo @$_POST['keyword']; ?>"></td>
					<td align="left"><input class="button" type="submit" value="<?php echo $AppUI->_('Search')?>"></td>
				</tr>
			</table>
	</form>
<script language="JavaScript">

	function focusOnSearchBox(){
		document.forms.frmSearch.keyword.focus();
	}

	window.onload = focusOnSearchBox;

</script>
<?php
if (isset ($_POST['keyword']))
{ 
	$search = new smartsearch();
	$search->keyword = ($_POST['keyword']);
?>

	<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<?php
    $perms = &$AppUI->acl();
	sort($files);
	foreach ($files as $tmp){
		require_once('./modules/smartsearch/searchobjects/'.$tmp);
		$temp = substr($tmp,0,-8);
		$temp .= '()';	
		eval ("\$class_search = new $temp;");
		$class_search->setKeyword($search->keyword);
		$results = $class_search->fetchResults($perms);
		echo $results;
	}
?>
</table>
<?php 
unset ($_POST['keyword']);
} 
?>
