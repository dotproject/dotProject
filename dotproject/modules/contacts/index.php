<?php /* $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

$AppUI->savePlace();

if (!($canAccess)) {
	$AppUI->redirect('m=public&a=access_denied');
}

$perms =& $AppUI->acl();
$q = new DBQuery;
$search_string = dPgetParam($_GET, 'search_string', null);

// To configure an aditional filter to use in the search string
$additional_filter = '';
// retrieve any state parameters
if ($search_string) {
	$AppUI->setState('ContIdxWhere', $search_string);
	$get_search = addslashes(stripslashes($search_string));
	$additional_filter = ("contact_first_name LIKE '%" . $get_search . "%'" 
	                      . " OR contact_last_name LIKE '%" . $get_search . "%'" 
	                      . " OR company_name LIKE '%" . $get_search . "%'" 
	                      . " OR contact_notes LIKE '%" . $get_search . "%'" 
	                      . " OR contact_email LIKE '%" . $get_search . "%'");
} else if (isset($_GET['where'])) {
	$AppUI->setState('ContIdxWhere', $_GET['where']);
}

$where = ($AppUI->getState('ContIdxWhere') ? $AppUI->getState('ContIdxWhere') : '%');

// Pull First Letters
$let = ":";
$search_map = array('contact_order_by', 'contact_first_name', 'contact_last_name');
foreach ($search_map as $search_name) {
	$q->addTable('contacts', 'c');
	$q->leftJoin('users', 'u', 'u.user_contact=c.contact_id');
	$q->addQuery('DISTINCT UPPER(SUBSTRING(' . $search_name . ',1,1)) as L, user_id');
	$q->addWhere('contact_private = 0 OR (contact_private = 1 AND contact_owner = ' 
	             . $AppUI->user_id . ') OR contact_owner IS NULL OR contact_owner = 0');
	$arr = $q->loadList();
	foreach ($arr as $L) {
		if (!($L['user_id']) || $perms->checkLogin($L['user_id'])) {
			$let .= $L['L'];
		}
	}
}
$q->clear();

// optional fields shown in the list (could be modified to allow breif and verbose, etc)
$showfields = array('contact_company' => 'contact_company', 'company_name' => 'company_name',
                    'contact_phone' => 'contact_phone','contact_email' => 'contact_email');

require_once $AppUI->getModuleClass('companies');
$company = new CCompany;
$allowedCompanies = $company->getAllowedSQL($AppUI->user_id);

// assemble the sql statement
$q->addTable('contacts', 'a');
$q->leftJoin('companies', 'b', 'a.contact_company = b.company_id');
$q->leftJoin('users', 'u', 'u.user_contact=a.contact_id');
$q->addQuery('contact_id, contact_order_by');
$q->addQuery('contact_first_name, contact_last_name, contact_phone, contact_owner');
$q->addQuery($showfields);
$q->addQuery('user_id');
foreach ($search_map as $search_name) {
	$where_filter .= (' OR ' . $search_name . " LIKE '$where%'");
}
$where_filter = mb_substr($where_filter, 4);
$where_filter .= (($additional_filter) ? (' OR ' . $additional_filter) : '');
$q->addWhere('(' . $where_filter . ')');
$q->addWhere('(contact_private = 0 OR (contact_private = 1 AND contact_owner = ' . $AppUI->user_id 
             . ') OR contact_owner IS NULL OR contact_owner = 0)');
if (count($allowedCompanies)) {
	$comp_where = implode(' AND ', $allowedCompanies);
	$q->addWhere('((' . $comp_where . ') OR contact_company = 0)');
}
$q->addOrder('contact_order_by');

$sql = $q->prepare();
$q->clear();

$rn = 0;
$disp_arr = array();
if (!($res = db_exec($sql))) {
	echo db_error();
} else {
	while ($row = db_fetch_assoc($res)) {
		if (!($row['user_id']) || $perms->checkLogin($row['user_id'])) {
			$disp_arr[] = $row;
			$rn++;
		}
	}
}

$carrWidth = 4;
$t = floor($rn / $carrWidth); //total "height"
$r = ($rn % $carrWidth); // remainder column height
$t += (($r == 0 && $t > 0) ? 0 : 1);

$carr[] = array();
$x = 0;
foreach ($disp_arr as $row) {
	$y = floor($x / $t);
	$carr[$y][] = $row;
	$x++;	
}

$tdw = floor(100 / $carrWidth);

/**
* Contact search form
*/
 // Let's remove the first '%' that we previously added to ContIdxWhere
$default_search_string = dPformSafe(mb_substr($AppUI->getState('ContIdxWhere'), 1, 
                                              mb_strlen($AppUI->getState('ContIdxWhere'))), true);

$a2z = "\n" . '<table cellpadding="2" cellspacing="1" border="0">';
$a2z .= "\n<tr>";
$a2z .= '<td width="100%" align="right">' . $AppUI->_('Show'). ': </td>';
$a2z .= '<td><a href="?m=contacts&amp;where=0">' . $AppUI->_('All') . '</a></td>';
for ($c=65; $c < 91; $c++) {
	$cu = chr($c);
	$cell = ((mb_strpos($let, "$cu") > 0) 
			 ? ('<a href="?m=contacts&amp;where=' . $cu . '">' . $cu . '</a>') 
			 : ('<font color="#999999">' . $cu . '</font>'));
	$a2z .= "\n\t<td>$cell</td>";
}
$a2z .= ("\n</tr>\n<tr>" . '<td colspan="28">' 
         . '<form action="./index.php" method="get">' . $AppUI->_('Search for') 
         . '<input type="text" name="search_string" value="' . $default_search_string . '" />' 
         . '<input type="hidden" name="m" value="contacts" /><input type="submit" value=">" />'
         . '<a href="./index.php?m=contacts&amp;search_string=">' . $AppUI->_('Reset search') 
		 . '</a></form></td></tr>' 
		 . "\n</table>\n");

// setup the title block

// what purpose is the next line for? Commented out by gregorerhardt, Bug #892912
// $contact_id = $carr[$z][$x]["contact_id"];

$titleBlock = new CTitleBlock('Contacts', 'monkeychat-48.png', $m, "$m.$a");
$titleBlock->addCell($a2z);
if ($canAuthor) {
	$titleBlock->addCell(('<input type="submit" class="button" value="' . $AppUI->_('new contact') 
	                      . '">'), '', '<form action="?m=contacts&amp;a=addedit" method="post">', 
	                     '</form>');
	$titleBlock->addCrumbRight('<a href="?m=contacts&amp;a=csvexport&amp;suppressHeaders=true">' 
	                           . $AppUI->_('CSV Download'). '</a> | ' 
	                           . '<a href="?m=contacts&amp;a=vcardimport&amp;dialog=0">' 
	                           . $AppUI->_('Import vCard') . '</a>');
}
$titleBlock->show();
// TODO: Check to see that the Edit function is separated.

?>
<script language="javascript" type="text/javascript">
// Callback function for the generic selector
function goProject(key, val) {
	var f = document.modProjects;
	if (val != '') {
		f.project_id.value = key;
		f.submit();
        }
}
</script>
<form action="./index.php" method='get' name="modProjects">
  <input type='hidden' name='m' value='projects' />
  <input type='hidden' name='a' value='view' />
  <input type='hidden' name='project_id' />
</form>
<table width="100%" border="0" cellpadding="1" cellspacing="1" style="height:400px;" class="contacts" summary="Contacts">
<tr>
<?php
for ($z = 0; $z < $carrWidth; $z++) {
?>
	<td valign="top" align="left" bgcolor="#f4efe3" width="<?php echo $tdw;?>%">
	<?php
	for ($x = 0; $x < @count($carr[$z]); $x++) {
	?>
		<table width="100%" cellspacing="1" cellpadding="1" summary="contact info">
		<tr>
			<td width="100%">
				<?php $contactid = $carr[$z][$x]['contact_id']; ?>
				<a href="?m=contacts&amp;a=view&amp;contact_id=<?php 
		echo $contactid; 
?>"><strong><?php 
		echo (($carr[$z][$x]['contact_order_by']) 
		      ? $carr[$z][$x]['contact_order_by'] 
		      : ($carr[$z][$x]['contact_first_name'] . ' ' . $carr[$z][$x]['contact_last_name'])); 
?></strong></a>&nbsp;
				&nbsp;<a title="<?php 
		echo ($AppUI->_('Export vCard for') . ' ' . $carr[$z][$x]['contact_first_name'] . ' ' 
		      . $carr[$z][$x]['contact_last_name']); 
?>" href="?m=contacts&amp;a=vcardexport&amp;suppressHeaders=true&amp;contact_id=<?php 
		echo $contactid; ?>" >(vCard)</a>
				&nbsp;<a title="<?php 
		echo $AppUI->_('Edit'); ?>" href="?m=contacts&amp;a=addedit&amp;contact_id=<?php 
		echo $contactid; ?>"><?php echo $AppUI->_('Edit'); ?></a>
<?php
		$q = new DBQuery;
		$q->addTable('projects');
		$q->addQuery('count(*)');
		$q->addWhere('project_contacts LIKE "' . $carr[$z][$x]['contact_id'] 
		             .',%" OR project_contacts LIKE "%,' . $carr[$z][$x]['contact_id'] 
		             .',%" OR project_contacts LIKE "%,' . $carr[$z][$x]['contact_id'] 
		             .'" OR project_contacts LIKE "' . $carr[$z][$x]['contact_id'] .'"');
		
		$res = $q->exec();
		$projects_contact = db_fetch_row($res);
		$q->clear();
		if ($projects_contact[0] > 0) {
			echo ('&nbsp;<a href="" onclick="javascript:window.open(' 
			      . "'?m=public&amp;a=selector&amp;dialog=1&amp;callback=goProject&amp;table=projects" 
			      . '&user_id=' . $carr[$z][$x]['contact_id'] 
			      . "', 'selector', 'left=50,top=50,height=250,width=400,resizable');" 
			      . 'return false;">' . $AppUI->_('Projects') . '</a>');
		}
?>
			</td>
		</tr>
		<tr>
			<td class="hilite">
			<?php
		reset($showfields);
		while (list($key, $val) = each($showfields)) {
			if (mb_strlen($carr[$z][$x][$key]) > 0) {
				if ($val == "contact_email") {
					echo ('<a href="mailto:' . $carr[$z][$x][$key] . '" class="mailto">' 
					      . $carr[$z][$x][$key] . "</a>\n");
				} else if (!($val == "contact_company" && is_numeric($carr[$z][$x][$key]))) {
					echo  $carr[$z][$x][$key]. "<br />";
				}
			}
		} ?>
			</td>
		</tr>
		</table>
		<br />&nbsp;<br />
	<?php 
	} ?>
	</td>
<?php 
} ?>
</tr>
</table>
