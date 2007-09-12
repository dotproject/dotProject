<?php /* DEPARTMENTS $Id$ */
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}

// load the companies class
require_once( $AppUI->getModuleClass( 'companies' ) );

if (isset( $_POST['company_id'] ))
	$AppUI->setState( 'DeptIdxCompany', intval( $_POST['company_id'] ) );

$company = $company_id = $AppUI->getState( 'DeptIdxCompany' ) !== NULL ? $AppUI->getState( 'DeptIdxCompany' ) : $AppUI->user_company;

$company_prefix = 'company_';

if (isset( $_POST['department'] )) {
	$AppUI->setState( 'DeptIdxDepartment', $_POST['department'] );
	
	//if department is set, ignore the company_id field
	unset($company_id);
}
$department = $AppUI->getState( 'DeptIdxDepartment' ) !== NULL ? $AppUI->getState( 'DeptIdxDepartment' ) : ($AppUI->user_department > 0 ? $AppUI->user_department : $company_prefix.$AppUI->user_company);

$canRead = !getDenyRead( $m, $department);
if (!$canRead) {
	$AppUI->redirect( 'm=public&a=access_denied' );
}
$AppUI->savePlace();

//if $department contains the $company_prefix string that it's requesting a company and not a department.  So, clear the 
// $department variable, and populate the $company_id variable.
if(!(strpos($department, $company_prefix)===false)){
	$company_id = substr($department,strlen($company_prefix));
	$AppUI->setState( 'DeptIdxCompany', $company_id );
	unset($department);
}

$obj = new CCompany();
$q = new DBQuery;
$q->addTable('companies');
$q->addQuery('company_id, company_name');
$q->addOrder('company_name');	
$obj->setAllowedSQL($AppUI->user_id, $q);
$companies = $q->loadList();
$q->clear();

//get list of all departments, filtered by the list of permitted companies.
$q->clear();
$q->addTable('companies');
$q->addQuery('company_id, company_name, dep.*');
$q->addJoin('departments', 'dep', 'companies.company_id = dep.dept_company');
$q->addOrder('company_name,dept_parent,dept_name');
$obj->setAllowedSQL($AppUI->user_id, $q);
$rows = $q->loadList();
$q->clear();

//display the select list
$buffer = '<select name="department" onChange="document.pickCompany.submit()" class="text">';
$buffer .= '<option value="company_0" style="font-weight:bold;">'.$AppUI->_('All').'</option>'."\n";
$company = '';
foreach ($rows as $row) {
	if ($row['dept_parent'] == 0) {
		if($company!=$row['company_id']){
			$buffer .= '<option disabled="disabled" value="'.$company_prefix.$row['company_id'].'" style="font-weight:bold;">'.$row['company_name'].'</option>'."\n";
			$company=$row['company_id'];
		}
		if($row['dept_parent']!=null){
			showchilddept( $row );
			findchilddept( $rows, $row['dept_id'] );
		}
	}
	if($row['dept_id'] == $department) {
		$company_id=$row['dept_company'];
	}
}
$buffer .= '</select>';

// setup the title block
$titleBlock = new CTitleBlock( 'Departments', 'users.gif', $m, $m.$a );
$titleBlock->addCrumb( '?m=companies', 'companies list' );
$titleBlock->addCell( $AppUI->_('Department') . ':');
$titleBlock->addCell( $buffer, '', '<form action="?m=departments" method="post" name="pickCompany">', '</form>');
$titleBlock->addCell();
if ($canEdit) {
	$titleBlock->addCell();
	$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('new department').'">', '',
		'<form action="?m=departments&a=addedit&company_id='.$company_id.'&dept_parent='.$department.'" method="post">', '</form>'
	);
}
$titleBlock->addCrumb( '?m=companies', 'company list' );

if ($company_id) {
	$titleBlock->addCrumb( '?m=companies&a=view&company_id='.$company_id, 'view this company' );
}
if ($canEdit && $department > 0) {
	$titleBlock->addCrumb( '?m=departments&a=addedit&dept_id='.$department, 'edit this department' );

	if ($canDelete) {
		$titleBlock->addCrumbDelete( 'delete department', $canDelete, $msg );
	}
}
$titleBlock->show();

$min_view = true;
include DP_BASE_DIR . '/modules/departments/view.php';
?>
