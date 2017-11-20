	<td width="50%" valign="top">
		<strong><?php echo $AppUI->_('Details');?></strong>
		<table cellspacing="1" cellpadding="2" border="0" width="100%">
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Company');?>:</td>
			<?php if ($perms->checkModuleItem( 'companies', 'access', $obj->project_company )) {?>
            			<td class="hilite" width="100%"> <?php echo "<a href='?m=companies&a=view&company_id=" . $obj->project_company ."'>" . htmlspecialchars( $obj->company_name, ENT_QUOTES) . '</a>' ;?></td>
			<?php } else {?>
            			<td class="hilite" width="100%"><?php echo htmlspecialchars( $obj->company_name, ENT_QUOTES) ;?></td>
			<?php }?>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Short Name');?>:</td>
			<td class="hilite"><?php echo htmlspecialchars( @$obj->project_short_name, ENT_QUOTES) ;?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Start Date');?>:</td>
			<td class="hilite"><?php echo $start_date ? $start_date->format( $df ) : '-';?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Target End Date');?>:</td>
			<td class="hilite"><?php echo $end_date ? $end_date->format( $df ) : '-';?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Actual End Date');?>:</td>
			<td class="hilite">
                                 <?php if ($project_id > 0) { ?>
                                        <?php echo $actual_end_date ? '<a href="?m=tasks&a=view&task_id='.$criticalTasks[0]['task_id'].'">' : '';?>
                                        <?php echo $actual_end_date ? '<span '. $style.'>'.$actual_end_date->format( $df ).'</span>' : '-';?>
                                        <?php echo $actual_end_date ? '</a>' : '';?>
                                 <?php } else { echo $AppUI->_('Dynamically calculated');} ?>
                        </td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Target Budget');?>:</td>
			<td class="hilite"><?php echo $dPconfig['currency_symbol'] ?><?php echo @$obj->project_target_budget;?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Project Owner');?>:</td>
			<td class="hilite"><?php echo $obj->user_name; ?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('URL');?>:</td>
			<td class="hilite"><a href="<?php echo @$obj->project_url;?>" target="_new"><?php echo @$obj->project_url;?></A></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Staging URL');?>:</td>
			<td class="hilite"><a href="<?php echo @$obj->project_demo_url;?>" target="_new"><?php echo @$obj->project_demo_url;?></a></td>
		</tr>
		<tr>
			<td colspan="2">
			<?php
				require_once("./classes/CustomFields.class.php");
				$custom_fields = New CustomFields( $m, $a, $obj->project_id, "view" );
				$custom_fields->printHTML();
			?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			<strong><?php echo $AppUI->_('Description');?></strong><br />
			<table cellspacing="0" cellpadding="2" border="0" width="100%">
			<tr>
				<td class="hilite">
					<?php echo str_replace( chr(10), "<br>", $obj->project_description) ; ?>&nbsp;
				</td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
	</td>
	<td width="50%" rowspan="9" valign="top">
		<strong><?php echo $AppUI->_('Summary');?></strong><br />
		<table cellspacing="1" cellpadding="2" border="0" width="100%">
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Status');?>:</td>
			<td class="hilite" width="100%"><?php echo $AppUI->_($pstatus[$obj->project_status]);?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Priority');?>:</td>
			<td class="hilite" width="100%" style="background-color:<?php echo $projectPriorityColor[$obj->project_priority]?>"><?php echo $AppUI->_($projectPriority[$obj->project_priority]);?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Type');?>:</td>
			<td class="hilite" width="100%"><?php echo $AppUI->_($ptype[$obj->project_type]);?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Progress');?>:</td>
			<td class="hilite" width="100%"><?php printf( "%.1f%%", $obj->project_percent_complete );?></td>
		</tr>
<!--		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Active');?>:</td>
			<td class="hilite" width="100%"><?php echo $obj->project_status == 7 ? $AppUI->_('Yes') : $AppUI->_('No');?></td>
		</tr>-->
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Worked Hours');?>:</td>
			<td class="hilite" width="100%"><?php echo $worked_hours ?></td>
		</tr>	
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Scheduled Hours');?>:</td>
			<td class="hilite" width="100%"><?php echo $total_hours ?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Project Hours');?>:</td>
			<td class="hilite" width="100%"><?php echo $total_project_hours ?></td>
		</tr>				
		<?php
		$q  = new DBQuery;
		$q->addTable('departments', 'a');
		$q->addTable('project_departments', 'b');
		$q->addQuery('a.dept_id, a.dept_name, a.dept_phone');
		$q->addWhere("a.dept_id = b.department_id and b.project_id = $project_id");
		$depts = $q->loadHashList("dept_id");
		if (count($depts) > 0) {
		?>
		    <tr>
		    	<td><strong><?php echo $AppUI->_("Departments"); ?></strong></td>
		    </tr>
		    <tr>
		    	<td colspan='3' class="hilite">
		    		<?php
		    			foreach($depts as $dept_id => $dept_info){
		    				echo "<div>".$dept_info["dept_name"];
		    				if($dept_info["dept_phone"] != ""){
		    					echo "( ".$dept_info["dept_phone"]." )";
		    				}
		    				echo "</div>";
		    			}
		    		?>
		    	</td>
		    </tr>
	 		<?php
		}
		
			$q  = new DBQuery;
			$q->addTable('contacts', 'a');
			$q->addTable('project_contacts', 'b');
			$q->addJoin('departments', 'c', 'a.contact_department = c.dept_id', 'left outer');			
			$q->addQuery('a.contact_id, a.contact_first_name, a.contact_last_name,
					a.contact_email, a.contact_phone, c.dept_name');
			$q->addWhere("a.contact_id = b.contact_id and b.project_id = $project_id
					and (contact_owner = '$AppUI->user_id' or contact_private='0')");

			$contacts = $q->loadHashList("contact_id");
			if(count($contacts)>0){
				?>
			    <tr>
			    	<td><strong><?php echo $AppUI->_("Contacts"); ?></strong></td>
			    </tr>
			    <tr>
			    	<td colspan='3' class="hilite">
			    		<?php
			    			echo "<table cellspacing='1' cellpadding='2' border='0' width='100%' bgcolor='black'>";
			    			echo "<tr><th>".$AppUI->_("Name")."</th><th>".$AppUI->_("Email")."</th><th>".$AppUI->_("Phone")."</th><th>".$AppUI->_("Department")."</th></tr>";
			    			foreach($contacts as $contact_id => $contact_data){
			    				echo "<tr>";
			    				echo "<td class='hilite'>";
							$canEdit = $perms->checkModuleItem('contacts', 'edit', $contact_id);
							if ($canEdit)
								echo "<a href='index.php?m=contacts&a=view&contact_id=$contact_id'>";
							echo $contact_data["contact_first_name"]." ".$contact_data["contact_last_name"];
							if ($canEdit)
								echo "</a>";
							echo "</td>";
			    				echo "<td class='hilite'><a href='mailto: ".$contact_data["contact_email"]."'>".$contact_data["contact_email"]."</a></td>";
			    				echo "<td class='hilite'>".$contact_data["contact_phone"]."</td>";
			    				echo "<td class='hilite'>".$contact_data["dept_name"]."</td>";
			    				echo "</tr>";
			    			}
			    			echo "</table>";
			    		?>
			    	</td>
			    </tr>
			    <tr>
			    	<td>
		 <?php
		}?>
		</table>
	</td>
