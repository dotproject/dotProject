<?php
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

$show_all = (int)dPgetParam($_REQUEST, 'show_all', 0);
$company_id = (int)dPgetParam($_REQUEST, 'company_id', 0);
$contact_id = (int)dPgetParam($_POST, 'contact_id', 0);
$call_back = dPgetCleanParam($_GET, 'call_back', null);
$contacts_submited = (int)dPgetParam($_POST, 'contacts_submited', 0);
$selected_contacts_id = dPgetCleanParam($_GET, 'selected_contacts_id', '');
if (dPgetParam($_POST, 'selected_contacts_id'))	{
	$selected_contacts_id = dPgetCleanParam($_POST, 'selected_contacts_id');
}
?>
<script language="javascript">
function setContactIDs (method,querystring)
{
	var URL = 'index.php?m=public&a=contact_selector';
    
	var field = document.getElementsByName('contact_id[]');
	var selected_contacts_id = document.frmContactSelect.selected_contacts_id;
	var currentIDstring = selected_contacts_id.value.toString();
	var currentIDs = currentIDstring.split(',');
	var addkeepIDs = new Array();
	var dropIDs = new Array();
	var resultIDs = new Array();
	var i = 0;
	var j = 0;
	var flag = 0;
	
	if (method == 'GET' && querystring) {
		URL += '&' + querystring;
	}
	
	var countkeep = 0;
	var countdrop = 0;
	
	for (i = 0; i < field.length; i++) {
		if (field[i].checked) {
			addkeepIDs[countkeep++] = field[i].value;
		} else {
			dropIDs[countdrop++] = field[i].value;
		}
	}
	
	var countfinal = 0;
	
	for (i = 0; i < addkeepIDs.length; i++) {
		resultIDs[countfinal++] = addkeepIDs[i];
	}
	
	for (i = 0; i < currentIDs.length; i++) {
		flag = 1;
		for (j = 0; j < addkeepIDs.length; j++) {
			if (currentIDs[i] == addkeepIDs[j]) {
				flag = 0;
			}
		}
		for (j = 0; j < dropIDs.length; j++) {
			if (currentIDs[i] == dropIDs[j]) {
				flag = 0;
			}
		}
		if (flag > 0) {
			resultIDs[countfinal++] = currentIDs[i];
		}
	}
	
	
	selected_contacts_id.value = resultIDs.join(',');
    
	if (method == 'GET') {
		URL +=  '&selected_contacts_id=' + selected_contacts_id.value;
		return URL;
	} else {
		return selected_contacts_id;
	}
}

<?php

if ($contacts_submited == 1) {
	$call_back_string = ((!(is_null($call_back))) 
	                     ? "window.opener.$call_back('$selected_contacts_id');" : '');
echo $call_back_string 
?>
self.close();
<?php
}
?>

</script>
<?php
function remove_invalid($arr) {
	$result = array();
	foreach ($arr as $val) {
		if (!(empty($val)) && trim($val) !== '' && is_numeric($val)) {
			$result[] = $val;
		}
	}	
	return $result;
}

// Remove any empty elements
$contacts_id = remove_invalid(explode(',', $selected_contacts_id));
$selected_contacts_id = implode(',', $contacts_id);

require_once($AppUI->getModuleClass('companies'));
$oCpy = new CCompany ();
$aCpies = $oCpy->getAllowedRecords ($AppUI->user_id, 'company_id, company_name', 'company_name');
$aCpies_esc = array();
foreach ($aCpies as $key => $company) {
	$aCpies_esc[$key] = db_escape($company);
}

$q = new DBQuery;

if (mb_strlen($selected_contacts_id) > 0 && ! $show_all && ! $company_id) {
	$q->addTable('contacts');
	$q->addQuery('DISTINCT contact_company');
	$q->addWhere('contact_id IN (' . $selected_contacts_id . ')');
	$where = implode(',', $q->loadColumn());
	$q->clear();
	if (mb_substr($where, 0, 1) == ',' && $where != ',') { 
		$where = '0'.$where; 
	} else if ($where == ',') {
		$where = '0';
	}
	$where = (($where)?('contact_company IN('.$where.')'):'');
} else if (! $company_id) {
	//  Contacts from all allowed companies
	$where = ("contact_company = ''"
	          ." OR (contact_company IN ('".implode('\',\'' , array_values($aCpies_esc)) ."'))"
	          ." OR (contact_company IN ('".implode('\',\'', array_keys($aCpies_esc)) ."'))") ;
	$company_name = $AppUI->_('Allowed Companies');
} else {
	// Contacts for this company only
	$q->addTable('companies', 'c');
	$q->addQuery('c.company_name');
	$q->addWhere('company_id = '.$company_id);
	$company_name = $q->loadResult();
	$q->clear();
	/*
		$sql = "select c.company_name from companies as c where company_id = $company_id";
		$company_name = db_loadResult($sql);
	*/
	$company_name_sql = db_escape($company_name);
	$where = " (contact_company = '$company_name_sql' or contact_company = '$company_id')";
}

// This should now work on company ID, but we need to be able to handle both
$q->addTable('contacts', 'a');
$q->leftJoin('companies', 'b', 'b.company_id = a.contact_company');
$q->leftJoin('departments', 'c', 'c.dept_id = a.contact_department');
$q->leftJoin('users', 'u', 'u.user_contact=a.contact_id');
$q->addQuery('a.contact_id, a.contact_first_name, a.contact_last_name,' 
             . ' a.contact_company, a.contact_department');
$q->addQuery('b.company_name');
$q->addQuery('c.dept_name');
$q->addQuery('u.user_id');
if ($where) { // Don't assume where is set. Change needed to fix Mantis Bug 0002056
	$q->addWhere($where);
}
$q->addWhere('(contact_owner = ' . $AppUI->user_id . ' OR contact_private = 0)');
//May need to review this order.
$q->addOrder('company_name, contact_company, dept_name, contact_department' 
             . ', contact_last_name');

$contacts = $q->loadHashList('contact_id');

global $task_id, $project_id;
$perms =& $AppUI->acl();
foreach ($contacts as $key => $row) {
	if ($row['user_id'] && !($perms->checkLogin($row['user_id']))) {
		$contacts[$key]['contact_extra'] .=  ' (' . $AppUI->_('Inactive') . ')';
	}
}

?>

<form action="index.php?m=public&a=contact_selector&dialog=1<?php 
echo ((!is_null($call_back)) ? '&call_back='.$call_back : ''); 
?>&company_id=<?php echo $company_id ?>" method='post' name='frmContactSelect'>

<?php
$pointer_department = '';
$pointer_company    = '';
$companies_names = array(0 => $AppUI->_('Select a company')) + $aCpies;
echo arraySelect($companies_names, 'company_id', 
                 ('onchange="document.frmContactSelect.contacts_submited.value=0; ' 
                  . 'setContactIDs(); document.frmContactSelect.submit();"'), 0);
?>

<br />
<h4><a href="#" onClick="window.location.href=setContactIDs('GET','dialog=1<?php 
echo ((!is_null($call_back)) ? ('&call_back=' . $call_back) : ''); ?>&show_all=1');">
<?php echo $AppUI->_('View all allowed companies'); ?>
</a></h4>
<hr />
<h2><?php echo $AppUI->_('Contacts for'); ?> <?php echo $company_name ?></h2>
<?php	
foreach ($contacts as $contact_id => $contact_data) {
	$contact_company = (($contact_data['company_name']) 
	                    ? $contact_data['company_name'] : $contact_data['contact_company']);
	if ($contact_company  && $contact_company != $pointer_company) {
		echo '<h4>'.$contact_company.'</h4>';
		$pointer_company = $contact_company;
	}
	
	$contact_department = (($contact_data['dept_name']) 
	                       ? $contact_data['dept_name'] : $contact_data['contact_department']);
	if ($contact_department && $contact_department != $pointer_department) {
		echo '<h5>'.$contact_department.'</h5>';
		$pointer_department = $contact_department;
	}

	$checked = in_array($contact_id, $contacts_id) ? 'checked="checked"' : '';
	
	echo ('<input type="checkbox" name="contact_id[]" id="contact_' . $contact_id . '" value="' 
	      . $contact_id . '" ' . $checked . ' />');
	echo ('<label for="contact_' . $contact_id . '">' . $contact_data['contact_first_name'] . ' ' 
	      . $contact_data['contact_last_name'] 
	      . (($contact_data['contact_extra']) ? ($contact_data['contact_extra']) : '') 
	      . '</label>');
	echo ('<br />');
	}
?>
<hr />
<input name="contacts_submited" type="hidden" value="1" />
<input name="selected_contacts_id" type="hidden" value="<?php echo $selected_contacts_id; ?>">
<input type="submit" value="<?php 
echo $AppUI->_('Continue'); ?>" onClick="setContactIDs()" class="button" />
</form>
