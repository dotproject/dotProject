<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}
global $AppUI;
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_report.class.php");
$AppUI->savePlace();
$tabBox = new CTabBox('?m=monitoringandcontrol', DP_BASE_DIR . '/modules/monitoringandcontrol/', $tab);
$company_id = dPgetParam( $_GET, 'company_id', 0 );
$controllerReport = new ControllerReport();  

?>
<script src="./modules/monitoringandcontrol/js/respons.js"> </script>

<!-- ***************** LISTA CADASTRADOS  ****************************-->	
	<br/>
	<table class="std" width="95%" id="tb_resp" align="center" border="0" >	
		
		<tr>
			<th align="center"><?php echo $AppUI->_('LBL_PROJETO');?></th>
			<th align="center"><?php echo $AppUI->_('LBL_TAMANHO');?>(<?php echo $AppUI->_('LBL_HORA');?>)</th>
			<th align="center">%</th>
			<th align="center"><?php echo $AppUI->_('LBL_IDC');?></th>
			<th align="center"><?php echo $AppUI->_('LBL_IDP');?></th>
			<th align="center"><?php echo $AppUI->_('LBL_VP');?></th>
			<th align="center"><?php echo $AppUI->_('LBL_VA');?></th>
			<th align="center"><?php echo $AppUI->_('LBL_CR');?></th>
            <th align="center"><?php echo $AppUI->_('LBL_NUMERO_BASELINE');?></th>
			<th align="center"></th>
         </tr>		
				<?php 
					$cadastrados = $controllerReport -> obterDadosRelatorioGerenciaSenior($company_id);	
					foreach($cadastrados as $cad){
					
					if($row['idc'] < 0.8){$corIdc="#FF9FA5";}elseif ($row['idc'] < 1){$corIdc="#FFFFAE";}elseif ($row['idc'] > 1){$corIdc="#B7FFB7";}
					if($row['idp'] < 0.8){$corIdp="#FF9FA5";}elseif ($row['idp'] < 1){$corIdp="#FFFFAE";}elseif ($row['idp'] > 1){$corIdp="#B7FFB7";} 						
				?>										
					<tr>
						<td align="center"><a href="?m=projects&a=view&project_id=<?php echo $cad[id]; ?>">  <?php echo $cad[projeto]; ?> </a></td>
						<td  align="center"><?php echo $cad[tamanho]; ?> </td>
						<td  align="center"><?php echo number_format($cad[percentual], 2, ',', '.'); ?></td>
						<td bgcolor="<?php echo $corIdc; ?>" align="center" ><?php echo number_format($cad[idc], 2, ',', '.'); ?> </td>
						<td bgcolor="<?php echo $corIdp; ?>" align="center" ><?php echo number_format($cad[idp], 2, ',', '.'); ?> </td>							
						
						<td align="center" ><?php echo number_format($cad[vp], 2, ',', '.'); ?> </td>
						<td align="center" ><?php echo number_format($cad[va], 2, ',', '.'); ?> </td>
						<td align="center" ><?php echo number_format($cad[cr], 2, ',', '.'); ?> </td>
						<td align="center"><?php echo $cad[baseline]; ?> </td>	
						<td align="center"><a href="?m=monitoringandcontrol&a=addedit_ata&project_id=<?php echo $cad[id]; ?>"><?php echo $AppUI->_('LBL_ATA');?></a></td>				
			        </tr>				
				<?php } ?>	
					<tr><td colspan='10'align="center">
						<table class="std" width="40%" >
						<tr><td align="center" width="20" style="border-style:solid;border-width:1px" bgcolor="#FF9FA5"></td>
						<td align="left">&lt; 0,8</td>
						<td width="20" style="border-style:solid;border-width:1px" bgcolor="#FFFFAE">&nbsp; &nbsp;</td>
						<td align="left">&lt; 1</td>
						<td width="20" style="border-style:solid;border-width:1px" bgcolor="#B7FFB7">&nbsp; &nbsp;</td>
						<td align="left">&gt; 1</td>
						</tr></table> </td>   						
					</tr>								

	</table>	


