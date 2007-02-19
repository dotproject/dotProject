<?php /* ADMIN  $Id$ */ 
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly');
}

?>
<table cellpadding="2" cellspacing="1" border="0" width="100%" class="tbl">
<tr>
	<td width="60" align="right">
		&nbsp; <?php echo $AppUI->_('sort by');?>:&nbsp;
	</td>
	<?php if (dPgetParam($_REQUEST, "tab", 0) == 0){ ?>
	<th width="125">
	           <?php echo $AppUI->_('Login History');?>
	</th>
	<?php } ?>
	<th width="150">
		<a href="?m=admin&a=index&orderby=user_username" class="hdr"><?php echo $AppUI->_('Login Name');?></a>
	</th>
	<th>
		<a href="?m=admin&a=index&orderby=contact_last_name" class="hdr"><?php echo $AppUI->_('Real Name');?></a>
	</th>
	<th>
		<a href="?m=admin&a=index&orderby=contact_company" class="hdr"><?php echo $AppUI->_('Company');?></a>
	</th>
</tr>
<?php 

$perms =& $AppUI->acl();
foreach ($users as $row) {
	if ($perms->isUserPermitted($row['user_id']) != $canLogin)
		continue;
?>
<tr>
	<td align="right" nowrap="nowrap">
<?php if ($canEdit) { ?>
		<table cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td>
				<a href="./index.php?m=admin&a=addedituser&user_id=<?php echo $row["user_id"];?>" title="<?php echo $AppUI->_('edit');?>">
					<?php echo dPshowImage( './images/icons/stock_edit-16.png', 16, 16, '' ); ?>
				</a>
			</td>
			<td>
				<a href="?m=admin&a=viewuser&user_id=<?php echo $row["user_id"];?>&tab=3" title="">
					<img src="images/obj/lock.gif" width="16" height="16" border="0" alt="<?php echo $AppUI->_('edit permissions');?>">
				</a>
			</td>
			<td>
<?php 
$user_display = addslashes($row["contact_first_name"] . " " . $row["contact_last_name"]);

$user_display = trim($user_display);
if (empty($user_display))
        $user_display = $row['user_username'];
?>
				<a href="javascript:delMe(<?php echo $row["user_id"];?>, '<?php echo $user_display;?>')" title="<?php echo $AppUI->_('delete');?>">
					<?php echo dPshowImage( './images/icons/stock_delete-16.png', 16, 16, '' ); ?>
				</a>
			</td>
		</tr>
		</table>
<?php } ?>
	</td>
	<?php if (dPgetParam($_REQUEST, "tab", 0) == 0){ ?>
	<td>
	       <?php 
	          	$q  = new DBQuery;
			$q->addTable('user_access_log', 'ual');
			$q->addQuery("user_access_log_id, ( unix_timestamp( now( ) ) - unix_timestamp( date_time_in ) ) / 3600 as 		hours, ( unix_timestamp( now( ) ) - unix_timestamp( date_time_last_action ) ) / 3600 as 		idle, if(isnull(date_time_out) or date_time_out ='0000-00-00 00:00:00','1','0') as online");
			$q->addWhere("user_id ='". $row["user_id"]."'");
			$q->addOrder('user_access_log_id DESC');
			$q->setLimit(1);
			$user_logs = $q->loadList();
	           
                if ($user_logs)
	           foreach ($user_logs as $row_log) {
	               if ($row_log["online"] == '1'){
	                   echo '<span style="color: green">'.$row_log["hours"]." ".$AppUI->_('hrs.'). "( ".$row_log["idle"]." ". $AppUI->_('hrs.')." ".$AppUI->_('idle'). ") - " . $AppUI->_('Online');  
	               } else {
	                   echo '<span style="color: red">'.$AppUI->_('Offline');
	               }
	} 
                else
                        echo '<span style="color: grey">'.$AppUI->_('Never Visited');
        echo '</span>';
	}?>
	</td>
	<td>
		<a href="./index.php?m=admin&a=viewuser&user_id=<?php echo $row["user_id"];?>"><?php echo $row["user_username"];?></a>
	</td>
	<td>
		<a href="mailto:<?php echo $row["contact_email"];?>"><img src="images/obj/email.gif" width="16" height="16" border="0" alt="email"></a>
<?php
if ($row['contact_last_name'] && $row['contact_first_name'])
        echo $row["contact_last_name"].', '.$row["contact_first_name"];
else
        echo '<span style="font-style: italic">unknown</span>';
?>
	</td>
	<td>
		<a href="./index.php?m=companies&a=view&company_id=<?php echo $row["contact_company"];?>"><?php echo $row["company_name"];?></a>
	</td>
</tr>
<?php }?>

</table>
