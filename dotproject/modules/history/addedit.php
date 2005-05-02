<?php /* $Id$ */
$history_id = defVal( @$_GET["history_id"], 0);

/*
// check permissions
if (!$canEdit) {
	$AppUI->redirect( "m=public&a=access_denied" );
}
*/
$action = @$_REQUEST["action"];
$q = new DBQuery;
if($action) {
	$history_description = dPgetParam($_POST, 'history_description', '');
	$history_project = dPgetParam($_POST, 'history_project', '');
	$userid = $AppUI->user_id;
	
	if( $action == 'add' ) {
		$q->addTable('history');
		$q->addInsert('history_table', "history");
		$q->addInsert('history_action', "add");
		$q->addInsert( 'history_date', str_replace( "'", '', $db->DBTimeStamp( time() ) ) );
		$q->addInsert('history_description', $history_description);
		$q->addInsert('history_user', $userid);
		$q->addInsert('history_project', $history_project);
		$okMsg = 'History added';
	} else if ( $action == 'update' ) {
		$q->addTable('history');
		$q->addUpdate('history_description', $history_description);
		$q->addUpdate('history_project', $history_project);
		$q->addWhere('history_id ='.$history_id);
		$okMsg = 'History updated';
	} else if ( $action == 'del' ) {
		$q->setDelete('history');
		$q->addWhere('history_id ='.$history_id);
		$okMsg = 'History deleted';				
	}
	if(!$q->exec()) {
		$AppUI->setMsg( db_error() );
	} else {	
		$AppUI->setMsg( $okMsg );
                if ($action == 'add')
			$q->clear();
			$q->addTable('history');
			$q->addUpdate('history_item = history_id');
			$q->addWhere('history_table = \'history\'');
			$okMsg = 'History deleted';
	}
	$q->clear();
	$AppUI->redirect();
}

// pull the history
$q->addTable('history');
$q->addQuery('*');
$q->addWhere('history_id ='.$history_id);
$sql = $q->prepare();
$q->clear();
db_loadHash( $sql, $history );
?>

<form name="AddEdit" method="post">				
<table width="100%" border="0" cellpadding="0" cellspacing="1">
<input name="action" type="hidden" value="<?php echo $history_id ? "update" : "add"  ?>">
<tr>
	<td><img src="./images/icons/tasks.gif" alt="" border="0"></td>
	<td align="left" nowrap="nowrap" width="100%"><h1><?php echo $AppUI->_( $history_id ? 'Edit history' : 'New history' );?></h1></td>
</tr>
</table>

<table border="0" cellpadding="4" cellspacing="0" width="98%">
<tr>
	<td width="50%" align="right">
		<a href="javascript:delIt()"><img align="absmiddle" src="./images/icons/trash.gif" width="16" height="16" alt="" border="0"><?php echo $AppUI->_('delete history');?></a>
	</td>
</tr>
</table>

<table border="1" cellpadding="4" cellspacing="0" width="98%" class="std">
	
<script>
	function delIt() {
		document.AddEdit.action.value = "del";
		document.AddEdit.submit();
	}	
</script>
	
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Project' );?>:</td>
	<td width="60%">
<?php
// pull the projects list
$q->addTable('projects');
$q->addQuery('project_id, project_name');
$q->addOrder('project_name');
$projects = arrayMerge( array( 0 => '('.$AppUI->_('any', UI_OUTPUT_RAW).')' ), $q->loadHashList() );
echo arraySelect( $projects, 'history_project', 'class="text"', $history["history_project"] );
?>
	</td>
</tr>
	
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Description' );?>:</td>
	<td width="60%">
		<textarea name="history_description" class="textarea" cols="60" rows="5" wrap="virtual"><?php echo $history["history_description"];?></textarea>
	</td>
</tr>	
		
<table border="0" cellspacing="0" cellpadding="3" width="98%">
<tr>
	<td height="40" width="30%">&nbsp;</td>
	<td  height="40" width="35%" align="right">
		<table>
		<tr>
			<td>
				<input class="button" type="button" name="cancel" value="<?php echo $AppUI->_('cancel'); ?>" onClick="javascript:if(confirm('<?php echo $AppUI->_('Are you sure you want to cancel?', UI_OUTPUT_JS); ?>')){location.href = '?<?php echo $AppUI->getPlace();?>';}">
			</td>
			<td>
				<input class="button" type="button" name="btnFuseAction" value="<?php echo $AppUI->_('save'); ?>" onClick="submit()">
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>	
	
</table>
</form>		
</body>
</html>
