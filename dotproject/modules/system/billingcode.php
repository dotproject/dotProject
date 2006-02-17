<?php /* SYSTEM $Id$ */
##
## add or edit a user preferences
##
$company_id=0;
$company_id = isset($_REQUEST['company_id']) ? $_REQUEST['company_id'] : 0;
// Check permissions
if (!$canEdit && $transmit_user_id != $AppUI->user_id) {
  $AppUI->redirect("m=public&a=access_denied" );
}

$sql = "
SELECT
        billingcode_id,
        billingcode_name,
        billingcode_value,
        billingcode_desc
FROM billingcode
WHERE billingcode_status = 0
AND company_id = '$company_id'
ORDER BY billingcode_name ASC
";

$billingcodes = NULL;
$ptrc=db_exec($sql);
$nums=db_num_rows($ptrc);
echo db_error();
for ($x=0; $x < $nums; $x++) {
        $row = db_fetch_assoc ( $ptrc ) ;
        $billingcodes[] = $row;
}

$sql="
SELECT
        company_id,
        company_name
FROM companies 
ORDER BY company_name ASC
";
$company_name="";
$company_list=array("0"=> $AppUI->_("Select Company") );
$ptrc = db_exec($sql);
$nums=db_num_rows($ptrc);
echo db_error();
for ($x=0; $x < $nums; $x++) {
        $row = db_fetch_assoc( $ptrc );
        $company_list[$row["company_id"]] = $row["company_name"];
        if ($company_id == $row["company_id"]) $company_name=$row["company_name"];
}

function showcodes(&$a) {
        global $AppUI;

        $s = "\n<tr height=20>";
        $s .= "<td width=40><a href=\"javascript:delIt2({$a['billingcode_id']});\" title=\"".$AppUI->_('delete')."\"><img src=\"./images/icons/stock_delete-16.png\" border=\"0\" alt=\"Delete\"></a></td>";
        $alt = htmlspecialchars( $a["billingcode_desc"] );
        $s .= '<td align=left>&nbsp;' . $a["billingcode_name"] . '</td>';
        $s .= '<td nowrap="nowrap" align=center>'.$a["billingcode_value"].'</td>';
        $s .= '<td nowrap="nowrap">'.$a["billingcode_desc"].'</td>';
        $s .= "</tr>\n";
        echo $s;
}

$titleBlock = new CTitleBlock( 'Edit Billing Codes', 'myevo-weather.png', $m, "$m.$a" );
$titleBlock->addCrumb( "?m=system", "system admin" );
$titleBlock->show();




?>
<script language="javascript">
function submitIt(){
        var form = document.changeuser;
        form.submit();
}

function changeIt() {
        var f=document.changeMe;
        var msg = '';
        f.submit();
}


function delIt2(id) {
        document.frmDel.billingcode_id.value = id;
        document.frmDel.submit();
}
</script>

<form name="changeMe" action="./index.php?m=system&a=billingcode" method="post">
        <?php echo arraySelect( $company_list, 'company_id', 'size="1" class="text" onchange="changeIt();"', $obj->task_status, false );?>
</form>

<?php echo "<b>$company_name</b>"; ?>

<table width="100%" border="0" cellpadding="1" cellspacing="1" class="std">
<form name="frmDel" action="./index.php?m=system" method="post">
        <input type="hidden" name="dosql" value="do_billingcode_aed" />
        <input type="hidden" name="del" value="1" />
        <input type="hidden" name="billingcode_id" value="" />
</form>

<form name="changeuser" action="./index.php?m=system" method="post">
        <input type="hidden" name="dosql" value="do_billingcode_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="company_id" value="<?php echo $company_id; ?>" />
        <input type="hidden" name="billingcode_status" value="0" />

<tr height="20">
        <th width="40">&nbsp;</th>
        <th><?php echo $AppUI->_('Billing Code');?></th>
        <th><?php echo $AppUI->_('Value');?></th>
        <th><?php echo $AppUI->_('Description');?></th>
</tr>

<?php
        for ($s=0; $s < count($billingcodes); $s++) {
                showcodes( $billingcodes[$s],1);
        }
?>

<tr>
        <td>&nbsp;</td>
        <td><input type="text" name="billingcode_name" value=""></td>
        <td><input type="text" name="billingcode_value" value=""></td>
        <td><input type="text" name="billingcode_desc" value=""</td>
</tr>

<tr>
        <td align="left"><input class="button"  type="button" value="<?php echo $AppUI->_('back');?>" onClick="javascript:history.back(-1);" /></td>
        <td align="right"><input class="button" type="button" value="<?php echo $AppUI->_('submit');?>" onClick="submitIt()" /></td>
</tr>
</table>

