<?php /* $Id$ */
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

$AppUI->savePlace();

if (! $canAccess) {
	$AppUI->redirect('m=public&a=access_denied');
}

$perms =& $AppUI->acl();

// To configure an aditional filter to use in the search string
$additional_filter = "";
// retrieve any state parameters
if (isset( $_GET['where'] )) {
	$AppUI->setState( 'ContIdxWhere', $_GET['where'] );
}
if (isset( $_GET["search_string"] )){
	$AppUI->setState ('ContIdxWhere', "%".$_GET['search_string']);
				// Added the first % in order to find instrings also
	$additional_filter = "OR contact_first_name like '%{$_GET['search_string']}%'
	                      OR contact_last_name  like '%{$_GET['search_string']}%'
						  OR company_name       like '%{$_GET['search_string']}%'
						  OR contact_notes      like '%{$_GET['search_string']}%'
						  OR contact_email      like '%{$_GET['search_string']}%'";
}
$where = $AppUI->getState( 'ContIdxWhere' ) ? $AppUI->getState( 'ContIdxWhere' ) : '%';

$orderby = 'contact_order_by';

// Pull First Letters
$let = ":";
$search_map = array($orderby, 'contact_first_name', 'contact_last_name');
foreach ($search_map as $search_name)
{
	$q  = new DBQuery;
	$q->addTable('contacts');
	$q->addQuery("DISTINCT UPPER(SUBSTRING($search_name,1,1)) as L");
	$q->addWhere("contact_private=0 OR (contact_private=1 AND contact_owner=$AppUI->user_id)
								OR contact_owner IS NULL OR contact_owner = 0");
	$arr = $q->loadList();
	foreach( $arr as $L )
		$let .= $L['L'];
}

// optional fields shown in the list (could be modified to allow breif and verbose, etc)
$showfields = array(
	// "test" => "concat(contact_first_name,' ',contact_last_name) as test",    why do we want the name repeated?
    "contact_company" => "contact_company",
	"company_name" => "company_name",
	"contact_phone" => "contact_phone",
	"contact_email" => "contact_email"
);

require_once $AppUI->getModuleClass('companies');
$company =& new CCompany;
$allowedCompanies = $company->getAllowedSQL($AppUI->user_id);

// assemble the sql statement
$q = new DBQuery;
$q->addQuery('contact_id, contact_order_by');
$q->addQuery($showfields);
$q->addQuery('contact_first_name, contact_last_name, contact_phone');
$q->addTable('contacts', 'a');
$q->leftJoin('companies', 'b', 'a.contact_company = b.company_id');
foreach($search_map as $search_name)
        $where_filter .=" OR $search_name LIKE '$where%'";
$where_filter = substr($where_filter, 4);
$q->addWhere("($where_filter $additional_filter)");
$q->addWhere("
	(contact_private=0
		OR (contact_private=1 AND contact_owner=$AppUI->user_id)
		OR contact_owner IS NULL OR contact_owner = 0
	)");
if (count($allowedCompanies)) {
	$comp_where = implode(' AND ', $allowedCompanies);
	$q->addWhere( '( (' . $comp_where . ') OR contact_company = 0 )' );
}
$q->addOrder('contact_order_by');

$carr[] = array();
$carrWidth = 4;
$carrHeight = 4;

$sql = $q->prepare();
$q->clear();
$res = db_exec( $sql );
if ($res)
	$rn = db_num_rows( $res );
else {
	echo db_error();
	$rn = 0;
}

$t = floor( $rn / $carrWidth );
$r = ($rn % $carrWidth);

if ($rn < ($carrWidth * $carrHeight)) {
	for ($y=0; $y < $carrWidth; $y++) {
		$x = 0;
		//if($y<$r)	$x = -1;
		while (($x<$carrHeight) && ($row = db_fetch_assoc( $res ))){
			$carr[$y][] = $row;
			$x++;
		}
	}
} else {
	for ($y=0; $y < $carrWidth; $y++) {
		$x = 0;
		if($y<$r)	$x = -1;
		while(($x<$t) && ($row = db_fetch_assoc( $res ))){
			$carr[$y][] = $row;
			$x++;
		}
	}
}

$tdw = floor( 100 / $carrWidth );

/**
* Contact search form
*/
 // Let's remove the first '%' that we previously added to ContIdxWhere
$default_search_string = dPformSafe(substr($AppUI->getState( 'ContIdxWhere' ), 1, strlen($AppUI->getState( 'ContIdxWhere' ))), true);

$form = "<form action='./index.php' method='get'>".$AppUI->_('Search for')."
           <input type='text' name='search_string' value='$default_search_string' />
		   <input type='hidden' name='m' value='contacts' />
		   <input type='submit' value='>' />
		   <a href='./index.php?m=contacts&amp;search_string='>".$AppUI->_('Reset search')."</a>
		 </form>";
// En of contact search form

$a2z = "\n<table cellpadding=\"2\" cellspacing=\"1\" border=\"0\">";
$a2z .= "\n<tr>";
$a2z .= "<td width='100%' align='right'>" . $AppUI->_('Show'). ": </td>";
$a2z .= '<td><a href="./index.php?m=contacts&where=0">' . $AppUI->_('All') . '</a></td>';
for ($c=65; $c < 91; $c++) {
	$cu = chr( $c );
	$cell = strpos($let, "$cu") > 0 ?
		"<a href=\"?m=contacts&where=$cu\">$cu</a>" :
		"<font color=\"#999999\">$cu</font>";
	$a2z .= "\n\t<td>$cell</td>";
}
$a2z .= "\n</tr>\n<tr><td colspan='28'>$form</td></tr></table>";


// setup the title block

// what purpose is the next line for? Commented out by gregorerhardt, Bug #892912
// $contact_id = $carr[$z][$x]["contact_id"];

$titleBlock = new CTitleBlock( 'Contacts', 'monkeychat-48.png', $m, "$m.$a" );
$titleBlock->addCell( $a2z );
if ($canEdit) {
	$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('new contact').'">', '',
		'<form action="?m=contacts&a=addedit" method="post">', '</form>'
	);
	$titleBlock->addCrumbRight(
		'<a href="./index.php?m=contacts&a=csvexport&suppressHeaders=true">' . $AppUI->_('CSV Download'). "</a> | " .
		'<a href="./index.php?m=contacts&a=vcardimport&dialog=0">' . $AppUI->_('Import vCard') . '</a>'
	);
}
$titleBlock->show();

// TODO: Check to see that the Edit function is separated.

?>
<script language="javascript">
// Callback function for the generic selector
function goProject( key, val ) {
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
<table width="100%" border="0" cellpadding="1" cellspacing="1" height="400" class="contacts">
<tr>
<?php
	for ($z=0; $z < $carrWidth; $z++) {
?>
	<td valign="top" align="left" bgcolor="#f4efe3" width="<?php echo $tdw;?>%">
	<?php
		for ($x=0; $x < @count($carr[$z]); $x++) {
	?>
		<table width="100%" cellspacing="1" cellpadding="1">
		<tr>
			<td width="100%">
                                <?php $contactid = $carr[$z][$x]['contact_id']; ?>
				<a href="./index.php?m=contacts&a=view&contact_id=<?php echo $contactid; ?>"><strong><?php echo $carr[$z][$x]['contact_first_name'] . ' ' . $carr[$z][$x]['contact_last_name'];?></strong></a>&nbsp;
				&nbsp;<a  title="<?php echo $AppUI->_('Export vCard for').' '.$carr[$z][$x]["contact_first_name"].' '.$carr[$z][$x]["contact_last_name"]; ?>" href="?m=contacts&a=vcardexport&suppressHeaders=true&contact_id=<?php echo $contactid; ?>" >(vCard)</a>
                                &nbsp;<a title="<?php echo $AppUI->_('Edit'); ?>" href="?m=contacts&a=addedit&contact_id=<?php echo $contactid; ?>"><?php echo $AppUI->_('Edit'); ?></a>
<?php
$q  = new DBQuery;
$q->addTable('projects');
$q->addQuery('count(*)');
$q->addWhere("project_contacts like \"" .$carr[$z][$x]["contact_id"]
	.",%\" or project_contacts like \"%," .$carr[$z][$x]["contact_id"] 
	.",%\" or project_contacts like \"%," .$carr[$z][$x]["contact_id"]
	."\" or project_contacts like \"" .$carr[$z][$x]["contact_id"] ."\"");
	
 $res = $q->exec();
 $projects_contact = db_fetch_row($res);
 $q->clear();
 if ($projects_contact[0]>0)
   echo "				&nbsp;<a href=\"\" onClick=\"	window.open('./index.php?m=public&a=selector&dialog=1&callback=goProject&table=projects&user_id=" .$carr[$z][$x]["contact_id"] ."', 'selector', 'left=50,top=50,height=250,width=400,resizable')
;return false;\">".$AppUI->_('Projects')."</a>";
?>
			</td>
		</tr>
		<tr>
			<td class="hilite">
			<?php
				reset( $showfields );
				while (list( $key, $val ) = each( $showfields )) {
					if (strlen( $carr[$z][$x][$key] ) > 0) {
						if($val == "contact_email") {
						  echo "<A HREF='mailto:{$carr[$z][$x][$key]}' class='mailto'>{$carr[$z][$x][$key]}</a>\n";
                        } elseif($val == "contact_company" && is_numeric($carr[$z][$x][$key])) {
						} else {
						  echo  $carr[$z][$x][$key]. "<br />";
						}
					}
				}
			?>
			</td>
		</tr>
		</table>
		<br />&nbsp;<br />
	<?php }?>
	</td>
<?php }?>
</tr>
</table>
