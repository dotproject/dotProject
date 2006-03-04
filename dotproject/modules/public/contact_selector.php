<?php
	$company_id           = dPgetParam($_REQUEST, "company_id", 0);
	$contact_id           = dPgetParam($_POST, "contact_id", 0);
	$call_back            = dPgetParam($_GET, "call_back", null);
	$contacts_submited    = dPgetParam($_POST, "contacts_submited", 0);
	$selected_contacts_id = dPgetParam($_GET, "selected_contacts_id", "");

	if($contacts_submited == 1){
		$contacts_id = "";
		if(is_array($contact_id)){
			$contacts_id = implode(",", $contact_id);
		}
		$call_back_string = !is_null($call_back) ? "window.opener.$call_back('$contacts_id');" : "";
		?>
			<script language="javascript">
				<?php echo $call_back_string ?>
				self.close();
			</script>
		<?php
	}
	
	$contacts_id = explode(",", $selected_contacts_id);

	if ( ! $company_id ) {
		//  Contacts from all allowed companies
		require_once( $AppUI->getModuleClass( 'companies' ) );
		$oCpy = new CCompany ();
                $aCpies = $oCpy->getAllowedRecords ($AppUI->user_id, "company_id, company_name", 'company_name');
		$aCpies_esc = array();
		foreach ($aCpies as $key => $company)
		{
			$aCpies_esc[$key] = db_escape($company);
		}
                $where = "contact_company = '' OR (contact_company IN ('" .
                                implode('\',\'' , array_values($aCpies_esc)) .
                                "')) OR ( contact_company IN ('" .
				implode(",", array_keys($aCpies_esc)) .
		"'))" ;
		$company_name = $AppUI->_('Allowed Companies');
	} else {
		// Contacts for this company only
		$q =& new DBQuery;
		$q->addTable('companies', 'c');
		$q->addQuery('c.company_name');
		$q->addWhere('company_id = '.$company_id);
		$company_name = $q->loadResult();
		/*
		$sql = "select c.company_name
	        	from companies as c
	        	where company_id = $company_id";
		$company_name = db_loadResult($sql);
		*/
		$company_name_sql = db_escape($company_name);
		$where = " ( contact_company = '$company_name_sql' or contact_company = '$company_id' )";
	}
	
	// This should now work on company ID, but we need to be able to handle both
	$q =& new DBQuery;
	$q->addTable('contacts', 'a');
	$q->leftJoin('companies', 'b', 'company_id = contact_company');
	$q->leftJoin('departments', 'c', 'dept_id = contact_department');
	$q->addQuery('contact_id, contact_first_name, contact_last_name, contact_company, contact_department, contact_last_name');
	$q->addQuery('company_name');
	$q->addQuery('dept_name');
	$q->addWhere($where);
	$q->addWhere("(contact_owner = '$AppUI->user_id' or contact_private = '0')");
	$q->addOrder("company_name, contact_company, dept_name, contact_department"); // May need to review this.

	$contacts = $q->loadHashList("contact_id");
?>

<h2><?php echo $AppUI->_('Contacts for'); ?> <?php echo $company_name ?></h2>

<form action='index.php?m=public&a=contact_selector&dialog=1&<?php if(!is_null($call_back)) echo "call_back=$call_back&"; ?>company_id=<?php echo $company_id ?>' method='post' name='frmContactSelect'>
<?php
	$actual_department = "";
	$actual_company    = "";
	
	if(!$company_id){
		
		$companies_names = array(0 => $AppUI->_("Select a company")) + $aCpies;
		echo arraySelect($companies_names, "company_id", "onchange=\"document.frmContactSelect.contacts_submited.value=0; document.frmContactSelect.submit();\"", 0)."<hr />";
	} else {
		?>
			<a href='index.php?m=public&a=contact_selector&dialog=1&<?php if(!is_null($call_back)) echo "call_back=$call_back&"; ?>'><?php echo $AppUI->_("View all allowed companies"); ?></a>
		<?php
	}
	
	foreach($contacts as $contact_id => $contact_data){
		if (! $contact_data["company_name"])
			$contact_company = $contact_data['contact_company'];
		else
			$contact_company = $contact_data['company_name'];
		if($contact_company  && $contact_company != $actual_company){
			echo "<h4>$contact_company</h4>";
			$actual_company = $contact_company;
		}
		$contact_department = $contact_data["dept_name"] ? $contact_data["dept_name"] : $contact_data["contact_department"];
		if($contact_department && $contact_department != $actual_department){
			echo "<h5>$contact_department</h5>";
			$actual_department = $contact_department;
		}
		$checked = in_array($contact_id, $contacts_id) ? "checked" : "";
		echo "<input type='checkbox' name='contact_id[]' value='$contact_id' $checked />";
		echo $contact_data["contact_first_name"]." ".$contact_data["contact_last_name"];
		echo "<br />";
	}
?>
<hr />
<input name='contacts_submited' type='hidden' value='1' />
<input type='submit' value='<?php echo $AppUI->_("Continue"); ?>' class='button' />
</form>
