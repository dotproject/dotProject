<?php  // $Id$
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly');
}

$cn = $_REQUEST['cn'];
?>
<table cellspacing="0" cellpadding="3" border="0" class="std" width="100%" align="center">
	<tr>
 		<td align="left" colspan="2"><?php echo $AppUI->_($cn.'_tooltip'); ?></td>
	</tr>
</table>
