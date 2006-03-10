<?php
	$show_all             = dPgetParam($_REQUEST, "show_all", 0);
	$company_id           = dPgetParam($_REQUEST, "company_id", 0);
	$contact_id           = dPgetParam($_POST, "contact_id", 0);
	$call_back            = dPgetParam($_GET, "call_back", null);
	$contacts_submited    = dPgetParam($_POST, "contacts_submited", 0);
	$selected_contacts_id = dPgetParam($_GET, "selected_contacts_id", "");
        if (dPgetParam($_POST, "selected_contacts_id"))
           {
           $selected_contacts_id = dPgetParam($_POST, "selected_contacts_id");
           }
?>
<script language="javascript">
// ECMA Script section Carsten Menke <menke@smp-synergie.de>
function setContactIDs (method,querystring)
          {
          var URL = 'index.php?m=public&a=contact_selector';
          
          // !! DO NOT USE !!
          //
          //    document.frmContactSelect.elements['contact_id[]');
          //
          // as the length of the array is undefined if there is just 1 contact Field present
          
          var field = document.getElementsByName('contact_id[]');
          var selected_contacts_id = document.frmContactSelect.selected_contacts_id;
          var tmp = new Array();
          var tmp2 = new Array();
          tmp = selected_contacts_id.value.split(',');
          
          if (method == 'GET')
             {
             if (querystring)
                {
                 URL += '&' + querystring;
	        }
             }
 
          // We copy the values of tmp to tmp2, using
          // the value of tmp as an indice for tmp2, therefore
          // we can later on easily check if a checked field exists
          // we do not use the associative Array hack here, because
          // then methods like tmp2.length would not work.
                                                            
          for (i = 0; i < tmp.length; i++)
              {
              tmp2[tmp[i]] = tmp[i];
              }
          for (i = 0; i < field.length; i++)
              {
              if (field[i].checked == true)
                 {
                  if (!tmp2[field[i].value])
                     {
                     tmp2[field[i].value] = field[i].value;
                     }
                 }
             else
                 {
                  if (tmp2[field[i].value])
                     {
                     delete tmp2[field[i].value];
                     }
                 } 
              }
              tmp = new Array();
              var count = 0;
              for (i = 0; i < tmp2.length; i++)
                 {
                  if (tmp2[i])
                     {
                     tmp[count] = tmp2[i];
                     count++;
                     }
                  }
           selected_contacts_id.value = tmp.join(',');
           
           if (method == 'GET')
              {
              URL +=  '&selected_contacts_id=' + selected_contacts_id.value;
              return URL;
              }
             else {
                  return selected_contacts_id;
             }
          }
</script>
<?php
	if($contacts_submited == 1){
		$call_back_string = !is_null($call_back) ? "window.opener.$call_back('$selected_contacts_id');" : "";
		?>
			<script language="javascript">
				<?php echo $call_back_string ?>
				self.close();
			</script>
		<?php
	}
	
	$contacts_id = explode(",", $selected_contacts_id);

	require_once( $AppUI->getModuleClass( 'companies' ) );
	$oCpy = new CCompany ();
        $aCpies = $oCpy->getAllowedRecords ($AppUI->user_id, "company_id, company_name", 'company_name');
        $aCpies_esc = array();
	foreach ($aCpies as $key => $company)
		{
		$aCpies_esc[$key] = db_escape($company);
		}
       if ($selected_contacts_id && ! $show_all && ! $company_id)
               {
               $q =& new DBQuery;
               $q->addTable('contacts');
               $q->addQuery('DISTINCT contact_company');
               $q->addWhere('contact_id IN (' . $selected_contacts_id . ')');
               $where = implode(',', $q->loadColumn());
               $where = "contact_company IN($where)";
        }

 	else if ( ! $company_id ) {
	        //  Contacts from all allowed companies
                $where = "contact_company = '' OR (contact_company IN ('" .
                                implode('\',\'' , array_values($aCpies_esc)) .
                                "')) OR ( contact_company IN ('" .
				implode('\',\'', array_keys($aCpies_esc)) .
		"'))" ;
		$company_name = $AppUI->_('Allowed Companies');
	}
	else {
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
	$q->addQuery('contact_id, contact_first_name, contact_last_name, contact_company, contact_department');
	$q->addQuery('company_name');
	$q->addQuery('dept_name');
	$q->addWhere($where);
	$q->addWhere("(contact_owner = '$AppUI->user_id' or contact_private = '0')");
	$q->addOrder("company_name, contact_company, dept_name, contact_department, contact_last_name"); // May need to review this.

	$contacts = $q->loadHashList("contact_id");
?>

<form action='index.php?m=public&a=contact_selector&dialog=1&<?php if(!is_null($call_back)) echo "call_back=$call_back&"; ?>company_id=<?php echo $company_id ?>' method='post' name='frmContactSelect'>
<?php
	$actual_department = "";
	$actual_company    = "";
	$companies_names = array(0 => $AppUI->_("Select a company")) + $aCpies;
	echo arraySelect($companies_names, "company_id", "onchange=\"document.frmContactSelect.contacts_submited.value=0; setContactIDs(); document.frmContactSelect.submit();\"", 0);
?>
<br>
 <h4><a href="#" onClick="window.location.href=setContactIDs('GET','dialog=1&<?php if(!is_null($call_back)) echo "call_back=$call_back&"; ?>show_all=1');"><?php echo $AppUI->_("View all allowed companies"); ?></a></h4>
<hr />
<h2><?php echo $AppUI->_('Contacts for'); ?> <?php echo $company_name ?></h2>
<?php	
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
<input name='selected_contacts_id' type='hidden' value='<?php echo $selected_contacts_id; ?>'>
<input type='submit' value='<?php echo $AppUI->_("Continue"); ?>' onClick="setContactIDs()" class='button' />
</form>
