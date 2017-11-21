<?php
session_start();
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}
 global $AppUI;	
$AppUI->savePlace();
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_acao_corretiva.class.php");
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_ata.class.php");
require_once (DP_BASE_DIR . "/modules/monitoringandcontrol/control/controller_util.class.php");
header("Content-Type:text/html; charset=iso-8859-1",true);
 
$project_id = dPgetParam( $_GET, 'project_id', 0 );
$titleBlock = new CTitleBlock('Ação Corretiva', 'graph-up.png', $m, $m . '.' . $a);
$titleBlock->show();

$controllerAta = new ControllerAta();
$controllerUtil = new ControllerUtil(); 
$controllerAcaoCorretiva = new ControllerAcaoCorretiva();

?>
<?php
	  echo "<script>
			   globalUserNameList= new Array();
			   globalUserIdList= new Array();
		      </script>";		
					
		       $list = array();	
			   $list = $controllerUtil -> getUsers();
			   $i=0;
			   foreach($list as $row){	
				  echo "<script>	
							 globalUserNameList[$i] = '$row[1]';
					   		 globalUserIdList[$i] = '$row[0]';
							</script>";
			       			 $i++;								
			   }
?>
<script>	
// hidden fields: change_impact,  change_description, change_cause,

var nextId = 1000;
var opt = ["Selecione...", "Aberto","Fechado","Em desenvolvimento","Cancelado"];
function retorna(id){	
	nextId++;
	if(id==''){
		id=nextId;
	}	
var acao_corretiva = document.formPendencia.acao_corretiva.value;
var user = document.formPendencia.user.value;
var date_limit = document.formPendencia.date_limit.value;
var status = document.formPendencia.status.value;
var impacto = document.formPendencia.impact.value;
var causa = document.formPendencia.cause.value;
var desvio = document.formPendencia.description.value;

	var table = window.opener.document.getElementById("tbl_pendencias");
	var lastRow= table.rows.length;
	var row = table.insertRow(lastRow);	
	row.id=id;
	
	var td = row.insertCell(0);
	td.noWrap=true;		
	var field=document.createElement("input");
	field.type="hidden";
	field.name="index[]";
	field.value="index"+(parseInt(id)-1000);
	td.appendChild(field);	
	
	var field=document.createElement("input");
	field.type="hidden";
	field.name="impact[]";
	field.value=impacto;
	td.appendChild(field);	
	
	var field=document.createElement("input");
	field.type="hidden";
	field.name="description[]";
	field.value= desvio;
	td.appendChild(field);
	
	var field=document.createElement("input");
	field.type="hidden";
	field.name="cause[]";
	field.value= causa;
	td.appendChild(field);
	
	var field=document.createElement("input");
	field.type="hidden";
	field.name="status[]";
	field.value= status;
	td.appendChild(field);	
	
	var field=document.createElement("input");
	field.type="text";
	field.id="acao_corretiva"+id;
	field.name="acao_corretiva[]";
	field.style.width="98%";
	field.readOnly=true;
	field.value= acao_corretiva ;
	td.appendChild(field);
	
	var td = row.insertCell(1);
	td.noWrap=true;	
	var field=document.createElement("input");
	field.type="hidden";
	field.name="user[]";
	field.value= user ;
	td.appendChild(field);	
	
	var field=document.createElement("input");
	field.type="text";
	field.id="username"+id;
	field.name="username[]";	
	field.style.width="98%";
	field.readOnly=true;
	    for(i=0; i<globalUserIdList.length; i++){
                        if(user == globalUserIdList[i] ){	
                        field.value = globalUserNameList[i];
                        }
		}	
	td.appendChild(field);	
	
	var td = row.insertCell(2);
	td.noWrap=true;	
	var field=document.createElement("input");
	field.type="text";
	field.id="date_limit"+id;
	field.name="date_limit[]";
	field.style.width="98%";
	field.readOnly=true;
	field.value= date_limit ;
	td.appendChild(field);
	
	var td = row.insertCell(3);
	td.noWrap=true;
	var field=document.createElement("input");
	field.type="text";
	field.id="stts"+id;
	field.name="stts[]";
	field.style.width="98%";
	field.readOnly=true;
			for(i=0; i < opt.length; i++){
				 if(status ==  i){								 
						field.value = opt[i];
				 }
			}	
	td.appendChild(field);	
	
	//add a exclude button
	td = row.insertCell(4);
	td.noWrap=true;
	div=field=document.createElement("div");
	div.style.textAlign ="center";
	div.innerHTML="<input type='image' alt='./images/icons/stock_delete-16.png'  src='./images/icons/stock_delete-16.png' title='Deletar' name='deletar' value='deletar' onclick=deleteRole('"+row.id+"')>";
  	td.appendChild(div);	
	
	window.opener.document.tbl_pendencias;
	window.close();
}

</script>
<script type="text/javascript" src="./modules/monitoringandcontrol/js/ata.js"   ></script>
<!--  calendar  -->
	<link type="text/css" rel="stylesheet" href="./modules/monitoringandcontrol/js/jsLibraries/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css" media="screen" />
	<script type="text/javascript" src="./modules/monitoringandcontrol/js/jsLibraries/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js"   ></script>
<!-- end calendar  -->  
<?php		
			
?>

		  <form action="?m=monitoringandcontrol&a=addedit_ata&project_id=<?php echo $project_id;?>"  method="get" name="formPendencia" id="formPendencia" enctype="multipart/form-data">		    
	<input name="dosql" type="hidden" value="addedit_ata" />
    
    		<br/>
<table class="std" width="100%" cellspacing="0" cellpadding="4" border="0">
      	  <tr>
      		<td colspan="4">&nbsp;</td>
        </tr>
        <tr>
      		<td   style="width:35%; padding-left:30px">Projeto: 
             		<?php 						
							$project_name = $controllerUtil->getProjectName($project_id) ; 						
					 ?>
                 <input type="text" name="projeto" size="20" id="projeto" readonly="readonly" value="<?php echo  $project_name[0][0] ; ?> " />			
            <td colspan="2" style="width:45%">&nbsp;</td>
        </tr>
      	  <tr>
      		<td colspan="4">&nbsp;</td>
        </tr>
        <tr>
      		<td  style="width:20%; padding-left:30px">Impacto (horas):
            <input name="impact" type="text" id="impact" size="3" maxlength="3"  />					
            </td>
            <td align="right">Status:</td>            
	        <td >            
                <select name="status" size="1"  id="status" >
   					<option value="0">Selecione...</option>  
					<option value="1">Aberto</option>  
					<option value="2">Fechado</option>  
					<option value="3">Em desenvolvimento</option>  
					<option value="4">Cancelado</option>  
                </select>
         </td>     
 	   </tr> 
      <tr>
            <td style="width:20%; padding-left:30px">Respons&aacute;vel:    
                <select name="user" size="1"  id="user" >
   					<option value"0">Selecione...</option>  
                  		 <?php						
									$list = array();	
									$list = $controllerUtil -> getUsers();
									$i=0;
									foreach($list as $row){	
						  			echo "<option value='$row[0]' >$row[1]</option>";						
									}
						 ?>     
                </select>
                </td>
             <td align="right" >Prazo:</td>
			 <td nowrap="nowrap" >                 	  
				   <input type="text" class="text"  name="date_limit"  id="date_edit" />
				   <img src="./modules/monitoringandcontrol/images/img.gif" id="calendar_trigger" style="cursor:pointer" onclick="displayCalendar(document.getElementById('date_edit'),'dd/mm/yyyy',this)" />     
                </td>  
	  <tr>
      		<td colspan="4">&nbsp;</td>
        </tr>     		
                
           </tr>                     
     		 <tr>
                 <td  style="padding-left:30px">Descri&ccedil;&atilde;o do desvio:</td><td  colspan="6">&nbsp;</td>
           </tr>
           <tr>

           		<td  style="padding-left:30px" colspan="6"><textarea  name="description" cols="60" rows="4" ></textarea></td>
           </tr>
           
          <tr>
           		<td  style="padding-left:30px">Causa:</td><td  colspan="7">&nbsp;</td>
           </tr>
           <tr>
				 
           		<td style="padding-left:30px" colspan="6"><textarea  name="cause" cols="60" rows="4" ></textarea></td>
           </tr>
           
          <tr>
           		<td style="padding-left:30px">A&ccedil;&atilde;o Corretiva:</td><td  colspan="7">&nbsp;</td>
           </tr>
           <tr>
				
           		<td style="padding-left:30px" colspan="6"><textarea  name="acao_corretiva" cols="60" rows="4" ></textarea></td>
           </tr>   
	  <tr>
      		<td colspan="4">&nbsp;</td>

		<tr>
			<td colspan="4">
				<input type="button" value="<?php echo $AppUI->_('Fechar');?>" class="button" onClick="javascript:top.close();" /></td>
			<td align="right"><input type="button" value="<?php echo $AppUI->_('Salvar');?>" class="button"  onclick="retorna('');" /></td>
		</tr>
</table>		
</form>				
