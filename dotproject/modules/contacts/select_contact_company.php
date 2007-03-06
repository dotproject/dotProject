<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

	$table_name = dPgetParam($_GET, "table_name", "companies");

	switch($table_name){
		case "companies":
			$id_field          = "company_id";
			$name_field        = "company_name";
			$selection_string  = "Company";
			$filter            = null;
			$additional_get_information = "";
			break;
		case "departments":
			$id_field          = "dept_id";
			$name_field        = "dept_name";
			$selection_string  = "Department";
			$filter            = "dept_company = ".$_GET["company_id"];
			$additional_get_information = "company_id=".$_GET["company_id"];
			break;
	}
	
	$q  = new DBQuery;
	$q->addTable($table_name);
	$q->addQuery("$id_field, $name_field");
	if ($filter != null) { $q->addWhere($filter); }
	$q->addOrder($name_field);
	$company_list = array("0" => "") + $q->loadHashList();

?>

<?php
	if(dPgetParam($_POST, $id_field, 0) != 0){
		$q  = new DBQuery;
		$q->addTable($table_name);
		$q->addQuery('*');
		$q->addWhere("$id_field=".$_POST[$id_field]);
		$sql = $q->prepare();
		$q->clear();
		db_loadHash($sql, $r_data);
		$data_update_script = "";
		$update_address     = isset($_POST["overwrite_address"]);
			
		if($table_name == "companies"){
			$update_fields = array();
			if($update_address){
				$update_fields = array("company_address1" => "contact_address1",
				                       "company_address2" => "contact_address2",
				                       "company_city"     => "contact_city",
				                       "company_state"    => "contact_state",
				                       "company_zip"      => "contact_zip",
				                       "company_phone1"   => "contact_phone",
				                       "company_phone2"   => "contact_phone2",
				                       "company_fax"   => "contact_fax");
			}
			$data_update_script = "opener.setCompany('".$_POST[$id_field]."', '" . db_escape($r_data[$name_field]) . "');\n";
		} else if($table_name == "departments"){
			$update_fields = array("dept_id"     => "contact_department");
			if($update_address){
				$update_fields = array("dept_address1" => "contact_address1",
				                       "dept_address2" => "contact_address2",
				                       "dept_city"     => "contact_city",
				                       "dept_state"    => "contact_state",
				                       "dept_zip"      => "contact_zip",
				                       "dept_phone"   => "contact_phone",
				                       "dept_fax"   => "contact_fax");
			}
			$data_update_script = "opener.setDepartment('" . $_POST[$id_field] . "', '" . db_escape($r_data[$name_field]) . "');\n";
		}
	
		// Let's figure out which fields are going to
		// be updated
		foreach ($update_fields as $record_field => $contact_field){
			$data_update_script .= "opener.document.changecontact.$contact_field.value = '".$r_data[$record_field]."';\n";
		}
		?>
			<script language='javascript'>
				<?php echo $data_update_script; ?>
				self.close();
			</script>
		<?php
	} else {
		?>
		
		<form name="frmSelector" action="./index.php?m=contacts&a=select_contact_company&dialog=1&table_name=<?php echo $table_name."&$additional_get_information"; ?>" method="post">
			<table cellspacing="0" cellpadding="3" border="0">
			<tr>
				<td colspan="2">
			<?php
				echo $AppUI->_( 'Select' ).' '.$AppUI->_( $selection_string ).':<br />';
				echo arraySelect( $company_list, $id_field, ' size="10"', $company_id );
			?>
				</td>
			</tr>
			<tr>
				<td>
					<input type="button" class="button" value="<?php echo $AppUI->_( 'cancel' );?>" onclick="window.close()" />
				</td>
				<td align="right">
					<input type='checkbox' name='overwrite_address' /> <?php echo $AppUI->_("Overwrite contact address information"); ?>
					<input type="submit" class="button" value="<?php echo $AppUI->_( 'Select', UI_CASE_LOWER );?>" />
				</td>
			</tr>
			</table>
		</form>
	<?php
	}
?>
