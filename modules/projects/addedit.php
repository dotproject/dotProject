<?php /* PROJECTS $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}
include_once ($AppUI->getLibraryClass('quilljs/richedit.class'));

$project_id = intval(dPgetParam($_GET, 'project_id', 0));
$company_id = intval(dPgetParam($_GET, 'company_id', 0));
$company_internal_id = intval(dPgetParam($_GET, 'company_internal_id', 0));
$contact_id = intval(dPgetParam($_GET, 'contact_id', 0));

// check permissions for this record
$canEdit = getPermission($m, 'edit', $project_id);
$canAuthor = getPermission($m, 'add', $project_id);
if (!(($canEdit && $project_id) || ($canAuthor && !($project_id)))) {
	$AppUI->redirect('m=public&a=access_denied');
}

// get a list of permitted companies
require_once($AppUI->getModuleClass ('companies'));

$row = new CCompany();
$companies = $row->getAllowedRecords($AppUI->user_id, 'company_id,company_name', 'company_name');
$companies = arrayMerge(array('0'=>''), $companies);

// get internal companies
// 6 is standard value for internal companies
$companies_internal = $row->listCompaniesByType(array('6'));
$companies_internal = arrayMerge(array('0'=>''), $companies_internal);

// pull users
$q = new DBQuery;
$q->addTable('users','u');
$q->addTable('contacts','con');
$q->addQuery('user_id');
$q->addQuery('CONCAT_WS(", ",contact_last_name,contact_first_name)');
$q->addOrder('contact_last_name');
$q->addWhere('u.user_contact = con.contact_id');
$users = $q->loadHashList();

// load the record data
$row = new CProject();

if (!$row->load($project_id, false) && $project_id > 0) {
	$AppUI->setMsg('Project');
	$AppUI->setMsg("invalidID", UI_MSG_ERROR, true);
	$AppUI->redirect();
} else if (count($companies) < 2 && $project_id == 0) {
	$AppUI->setMsg("noCompanies", UI_MSG_ERROR, true);
	$AppUI->redirect();
}

if ($project_id == 0 && $company_id > 0) {
	$row->project_company = $company_id;
}

// add in the existing company if for some reason it is dis-allowed
if ($project_id && !array_key_exists($row->project_company, $companies)) {
	$q  = new DBQuery;
	$q->addTable('companies', 'co');
	$q->addQuery('company_name');
	$q->addWhere('co.company_id = '.$row->project_company);
	$sql = $q->prepare();
	$q->clear();
	$companies[$row->project_company] = db_loadResult($sql);
}

// get critical tasks (criteria: task_end_date)
$criticalTasks = ($project_id > 0) ? $row->getCriticalTasks() : NULL;

// get ProjectPriority from sysvals
$projectPriority = dPgetSysVal('ProjectPriority');

// format dates
$df = $AppUI->getPref('SHDATEFORMAT');

$start_date = (!empty($row) && intval($row->project_start_date)) ? new CDate($row->project_start_date) : null;
$end_date = (!empty($row) && intval($row->project_end_date)) ? new CDate($row->project_end_date) : null;
$actual_end_date = (!empty($criticalTasks) && intval($criticalTasks[0]['task_end_date'])) ? new CDate($criticalTasks[0]['task_end_date']) : null;
$style = (($actual_end_date > $end_date) && !empty($end_date)) ? 'style="color:red; font-weight:bold"' : '';

// setup the title block
$ttl = $project_id > 0 ? "Edit Project" : "New Project";
$titleBlock = new CTitleBlock($ttl, 'applet3-48.png', $m, "$m.$a");
$titleBlock->addCrumb("?m=projects", "projects list");
if ($project_id != 0)
$titleBlock->addCrumb("?m=projects&amp;a=view&amp;project_id=$project_id", "view this project");
$titleBlock->show();

//Build display list for departments
$company_id = $row->project_company;
$selected_departments = array();
if ($project_id) {
	$q = new DBQuery;
	$q->addTable('project_departments');
	$q->addQuery('department_id');
	$q->addWhere('project_id = ' . $project_id);
	$selected_departments = $q->loadColumn();
}
$departments_count = 0;
$department_selection_list = getDepartmentSelectionList($company_id, $selected_departments);
if ((!empty($department_selection_list) && $department_selection_list != '') || $project_id) {
	$department_selection_list = ($AppUI->_('Departments')."<br />\n"
	                              . '<select name="dept_ids[]" class="text">' . "\n"
	                              . '<option value="0"></option>' . "\n"
	                              . $department_selection_list . "\n" .'</select>');
} else {
	$department_selection_list = ('<input type="button" class="button" value="'
	                              . $AppUI->_('Select department...')
	                              . '" onclick="javascript:popDepartment();" />'
	                              . '<input type="hidden" name="project_departments"');
}

// Get contacts list
$selected_contacts = array();
if ($project_id) {
	$q = new DBQuery;
	$q->addTable('project_contacts');
	$q->addQuery('contact_id');
	$q->addWhere('project_id = ' . $project_id);
	$res =& $q->exec();
	for ($res; ! $res->EOF; $res->MoveNext())
		$selected_contacts[] = $res->fields['contact_id'];
	$q->clear();
}
if ($project_id == 0 && $contact_id > 0) {
	$selected_contacts[] = $contact_id;
}
?>
<link rel="stylesheet" media="all" href="<?php echo DP_BASE_URL;?>/lib/calendar/calendar-dp.css" title="blue" />
<!-- import the calendar script -->
<script  src="<?php echo DP_BASE_URL;?>/lib/calendar/calendar.js"></script>
<!-- import the language module -->
<script  src="<?php echo DP_BASE_URL;?>/lib/calendar/lang/calendar-<?php echo $AppUI->user_locale; ?>.js"></script>

<script language="javascript" >

function setShort() {
var f = document.editFrm;
var x = 10;
if (f.project_name.value.length < 11) {
	x = f.project_name.value.length;
}
if (f.project_short_name.value.length == 0) {
	f.project_short_name.value = f.project_name.value.substr(0,x);
}
}

function submitIt() {
	var f = document.editFrm;
	var msg = '';

	/*
	if (f.project_end_date.value > 0 && f.project_end_date.value < f.project_start_date.value) {
		msg += "\n<?php echo $AppUI->_('projectsBadEndDate1');?>";
	}
	if (f.project_actual_end_date.value > 0 && f.project_actual_end_date.value < f.project_start_date.value) {
		msg += "\n<?php echo $AppUI->_('projectsBadEndDate2');?>";
	}
	*/

	<?php
	/*
	** Automatic required fields generated from System Values
	*/
	$requiredFields = dPgetSysVal('ProjectRequiredFields');
	echo dPrequiredFields($requiredFields);
	?>

	if (msg.length < 1) {
		f.submit();
	} else {
		alert(msg);
	}
}

var selected_contacts_id = "<?php echo implode(',', $selected_contacts); ?>";

// See above note re firefox bug and window.open
function popContacts() {
	window.open('?m=public&a=contact_selector&dialog=1&call_back=setContacts&selected_contacts_id='+selected_contacts_id,
	            'contacts','height=600,width=400,resizable,scrollbars=yes');
}

function setContacts(contact_id_string) {
	if (!contact_id_string) {
		contact_id_string = "";
	}
	document.editFrm.project_contacts.value = contact_id_string;
	selected_contacts_id = contact_id_string;
}

var selected_departments_id = "<?php echo implode(',', $selected_departments); ?>";

function popDepartment() {
//due to a bug in Firefox (where window.open, when in a function, does not properly unescape a url)
// we CANNOT do a window open with &amp; separating the parameters
//this bug does not occur if the window open occurs in an onclick event
//this bug does NOT occur in Internet explorer
	var f = document.editFrm;
	var url = '?m=public&a=selector&dialog=1&callback=setDepartment&table=departments&company_id='
            + f.project_company.options[f.project_company.selectedIndex].value
            + '&dept_id='
            + selected_departments_id;
//prompt('',url);
        window.open(url,'dept','left=50,top=50,height=250,width=400,resizable');

//	window.open('?m=public&a=selector&dialog=1&call_back=setDepartment&selected_contacts_id='+selected_contacts_id, 'contacts','height=600,width=400,resizable,scrollbars=yes');
}

function setDepartment(department_id_string) {
	if (!department_id_string) {
		department_id_string = "";
	}
	document.editFrm.project_departments.value = department_id_string;
	selected_departments_id = department_id_string;
}

</script>

<form name="editFrm" action="?m=projects" enctype="multipart/form-data" method="post">
	<input type="hidden" name="dosql" value="do_project_aed" />
	<input type="hidden" name="project_id" value="<?php echo $project_id;?>" />
	<input type="hidden" name="project_creator" value="<?php echo $AppUI->user_id;?>" />
	<input name='project_contacts' type='hidden' value="<?php echo implode(',', $selected_contacts); ?>" />
<table cellspacing="0" cellpadding="4" border="0" width="100%" class="std">
<tr>
	<td>
		<input class="button" type="button" name="cancel2" value="<?php echo $AppUI->_('cancel');?>" onclick="javascript:if (confirm('Are you sure you want to cancel.')) {location.href = '?m=projects';}" />
	</td>
	<td align="right">
		<input class="button" type="button" name="btnFuseAction2" value="<?php echo $AppUI->_('submit');?>" onclick="javascript:submitIt();" />
	</td>
</tr>
<tr>
	<td width="50%" valign="top">
		<table cellspacing="0" cellpadding="2" border="0">
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Project Name');?></td>
			<td width="100%" colspan="2">
				<input autofocus type="text" name="project_name" value="<?php echo dPformSafe($row->project_name);?>" size="25" maxlength="50" onblur="javascript:setShort();" class="text" /> *
			</td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Project Owner');?></td>
			<td colspan="2">
<?php echo arraySelect($users, 'project_owner', 'size="1" style="width:200px;" class="text"', $row->project_owner? $row->project_owner : $AppUI->user_id) ?>
			</td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Company');?></td>
			<td width="100%" nowrap="nowrap" colspan="2">
<?php
		echo arraySelect($companies, 'project_company', 'class="text" size="1"', $row->project_company);
?> *</td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Internal Division');?></td>
			<td width="100%" nowrap="nowrap" colspan="2">
<?php
		echo arraySelect($companies_internal, 'project_company_internal', 'class="text" size="1"', $row->project_company_internal);
?></td>
 		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Start Date');?></td>
			<td nowrap="nowrap">
				<input type="date" class="date text" name="project_start_date" id="date1" value="<?php echo $start_date ? $start_date->format(FMT_DATE_HTML5) : '';?>" class="text"/>
			</td>
			<td rowspan="6" valign="top">
					<?php
						if ($AppUI->isActiveModule('contacts') && getPermission('contacts', 'view')) {
							echo '<input type="button" class="button" value="'.$AppUI->_("Select contacts...").'" onclick="javascript:popContacts();" />';
						}
						// Let's check if the actual company has departments registered
						if ($department_selection_list != "") {
							?>
								<br />
								<?php echo $department_selection_list; ?>
							<?php
						}
					?>
			</td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Target Finish Date');?></td>
			<td nowrap="nowrap">
				<input type="date" class="date text" name="project_end_date" id="date2" value="<?php echo $end_date ? $end_date->format(FMT_DATE_HTML5) : '';?>" class="text"/>
			</td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Target Budget');?> <?php echo $dPconfig['currency_symbol'] ?></td>
			<td>
				<input type="text" name="project_target_budget" value="<?php echo @$row->project_target_budget;?>" maxlength="10" class="text" />
			</td>
		</tr>
		<tr>
			<td colspan="3"><hr noshade="noshade" size="1" /></td>
		</tr>
<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Actual Finish Date');?></td>
			<td nowrap="nowrap">
      <?php if ($project_id > 0) { ?>
        <?php echo $actual_end_date ? '<a href="?m=tasks&amp;a=view&amp;task_id='.$criticalTasks[0]['task_id'].'">' : '';?>
        <?php echo $actual_end_date ? '<span '. $style.'>'.$actual_end_date->format($df).'</span>' : '-';?>
        <?php echo $actual_end_date ? '</a>' : '';?>
      <?php } else { echo $AppUI->_('Dynamically calculated');} ?>
			</td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Actual Budget');?> <?php echo $dPconfig['currency_symbol'] ?></td>
			<td>
				<input type="text" name="project_actual_budget" value="<?php echo @$row->project_actual_budget;?>" size="10" maxlength="10" class="text" />
			</td>
		</tr>
		<tr>
			<td colspan="3"><hr noshade="noshade" size="1" /></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('URL');?></td>
			<td colspan="2">
				<input type="text" name="project_url" value='<?php echo @$row->project_url;?>' size="40" maxlength="255" class="text" />
			</td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Staging URL');?></td>
			<td colspan="2">
				<input type="text" name="project_demo_url" value='<?php echo @$row->project_demo_url;?>' size="40" maxlength="255" class="text" />
			</td>
		</tr>
		<tr>
			<td align="right" colspan="3">
			<?php
				require_once($AppUI->getSystemClass('CustomFields'));
				$custom_fields = New CustomFields($m, $a, $row->project_id, "edit");
				$custom_fields->printHTML();
			?>
			</td>
		</tr>
		</table>
	</td>
	<td width="50%" valign="top">
		<table cellspacing="0" cellpadding="2" border="0" width="100%">
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Priority');?></td>
			<td nowrap="nowrap">
				<?php echo arraySelect($projectPriority, 'project_priority', 'size="1" class="text"', $row->project_priority, true);?> *
			</td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Short Name');?></td>
			<td colspan="3">
				<input type="text" name="project_short_name" value="<?php echo dPformSafe(@$row->project_short_name) ;?>" size="10" maxlength="10" class="text" /> *
			</td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Color Identifier');?></td>
			<td nowrap="nowrap">
				<input type="color" name="project_color_identifier" value="<?php echo (@$row->project_color_identifier) ? @$row->project_color_identifier : '#FFFFFF';?>" size="10" maxlength="7"/> *
			</td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Project Type');?></td>
			<td colspan="3">
				<?php echo arraySelect($ptype, 'project_type', 'size="1" class="text"', $row->project_type, true);?> *
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<table width="100%" bgcolor="#cccccc">
				<tr>
					<td><?php echo $AppUI->_('Status');?> *</td>
					<td nowrap="nowrap"><?php echo $AppUI->_('Progress');?></td>
				</tr>
				<tr>
					<td>
						<?php echo arraySelect($pstatus, 'project_status', 'size="1" class="text"', $row->project_status, true); ?>
					</td>
					<td>
						<strong><?php echo sprintf("%.1f%%", @$row->project_percent_complete);?></strong>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td align="left" nowrap="nowrap">
				<?php echo $AppUI->_('Import tasks from');?>:<br/>
			</td>
			<td colspan="3">
<?php echo projectSelectWithOptGroup($AppUI->user_id, 'import_tasks_from', 'size="1" class="text"', false, $project_id); ?>
			</td>
		</tr>
		<tr>
			<td align="left" nowrap="nowrap">
				<?php echo $AppUI->_('Scale Imported Tasks');?>:<br/>
			</td>
			<td colspan="3">
				<input type="checkbox" name="scale_project" id="scale_project" value="1" />
			</td>
		</tr>
		<tr>
			<td colspan="4">
			<?php
//        $richedit = new DpRichEdit("project_description", dPformSafe(@$row->project_description));
        $richedit = new DpRichEdit("project_description", dPsanitiseHTML(@$row->project_description));
        $richedit->render();
			?>
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td>
		<input class="button" type="button" name="cancel" value="<?php echo $AppUI->_('cancel');?>" onclick="javascript:if (confirm('Are you sure you want to cancel.')) {location.href = '?m=projects';}" />
	</td>
	<td align="right">
		<input class="button" type="button" name="btnFuseAction" value="<?php echo $AppUI->_('submit');?>" onclick="javascript:submitIt();" />
	</td>
</tr>
</table>
</form>
* <?php echo $AppUI->_('requiredField');?>

<?php
function getDepartmentSelectionList($company_id, $checked_array = array(), $dept_parent=0, $spaces = 0) {
	global $departments_count;
	$parsed = '';

	if ($departments_count < 6) $departments_count++;

	$q  = new DBQuery;
	$q->addTable('departments');
	$q->addQuery('dept_id, dept_name');
	$q->addWhere("dept_parent = '$dept_parent' and dept_company = '$company_id'");
	$q->addOrder('dept_name');

	$depts_list = $q->loadHashList("dept_id");

	foreach ($depts_list as $dept_id => $dept_info) {
		$selected = in_array($dept_id, $checked_array) ? ' selected="selected"' : '';

		$parsed .= ('<option value="' . $dept_id . '"' . $selected . '>'
		            . str_repeat('&nbsp;', $spaces) . $dept_info['dept_name'] . '</option>');
		$parsed .= getDepartmentSelectionList($company_id, $checked_array, $dept_id, $spaces+5);
	}

	return $parsed;
}
?>
