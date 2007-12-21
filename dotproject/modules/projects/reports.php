<?php /* PROJECTS $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

$project_id = intval( dPgetParam( $_REQUEST, "project_id", 0 ) );
$report_type = dPgetParam( $_REQUEST, "report_type", '' );

// check permissions for this record
$perms =& $AppUI->acl();

$canRead = $perms->checkModuleItem( $m, 'view', $project_id );
if (!$canRead) {
	$AppUI->redirect( "m=public&a=access_denied" );
}

$project_list=array("0"=> $AppUI->_("All", UI_OUTPUT_RAW) );

$obj = new CProject();
$ptrc = $obj->getAllowedProjectsInRows($AppUI->user_id);

$nums=db_num_rows($ptrc);

echo db_error();
for ($x=0; $x < $nums; $x++) {
	$row = db_fetch_assoc( $ptrc );
	if ($row["project_id"] == $project_id) $display_project_name='('.$row["project_short_name"].') '.$row["project_name"];
	$project_list[$row["project_id"]] = '('.$row["project_short_name"].') '.$row["project_name"];
}

if (! $suppressHeaders) {
?>
<script language="javascript">
                                                                                
function changeIt() {
        var f=document.changeMe;
        f.submit();
}
</script>

<?php
}
// get the prefered date format
$df = $AppUI->getPref('SHDATEFORMAT');

$reports = $AppUI->readFiles( DP_BASE_DIR.'/modules/projects/reports', "\.php$" );

// setup the title block
if (! $suppressHeaders) {
	$titleBlock = new CTitleBlock( 'Project Reports', 'applet3-48.png', $m, "$m.$a" );
	$titleBlock->addCrumb( "?m=projects", "projects list" );
	$titleBlock->addCrumb( "?m=projects&a=view&project_id=$project_id", "view this project" );
	if ($report_type) {
		$titleBlock->addCrumb( "?m=projects&a=reports&project_id=$project_id", "reports index" );
	}
	$titleBlock->show();
}

$report_type_var = dPgetParam($_GET, 'report_type', '');
if (!empty($report_type_var))
	$report_type_var = '&report_type=' . $report_type;

if (! $suppressHeaders) {
if (!isset($display_project_name)) 
	$display_project_name = $AppUI->_('All');
echo $AppUI->_('Selected Project') . ": <b>".$display_project_name."</b>"; 
?>
<form name="changeMe" action="./index.php?m=projects&a=reports<?php echo $report_type_var; ?>" method="post">
<?php echo $AppUI->_('Projects') . ':';?>
<?php echo arraySelect( $project_list, 'project_id', 'size="1" class="text" onchange="changeIt();"', $project_id, false );?>
</form>

<?php
}
if ($report_type) {
	$report_type = $AppUI->checkFileName( $report_type );
	$report_type = str_replace( ' ', '_', $report_type );
	require DP_BASE_DIR.'/modules/projects/reports/'.$report_type.'.php';
} else {
	echo "<table>";
	echo "<tr><td><h2>" . $AppUI->_( 'Reports Available' ) . "</h2></td></tr>";
	foreach ($reports as $v) {
		$type = str_replace( ".php", "", $v );
		$desc_file = str_replace( ".php", '.'.$AppUI->user_locale.'.txt', $v );

		// Load the description file for the user locale, default to 'en'
		if( file_exists( $desc_file ) ) {
			$desc = file( DP_BASE_DIR.'/modules/projects/reports/'.$desc_file);
		} else {
			$desc_file_en = str_replace( ".php", '.en.txt', $v );
			//FIXME : need to handle description file non existence
			$desc = @file( DP_BASE_DIR.'/modules/projects/reports/'.$desc_file_en);
		}

		echo "\n<tr>";
		echo "\n	<td><a href=\"index.php?m=projects&a=reports&project_id=$project_id&report_type=$type";
		if (isset($desc[2]))
			echo "&" . $desc[2];
		echo "\">";
		echo @$desc[0] ? $desc[0] : $v;
		echo "</a>";
		echo "\n</td>";
		echo "\n<td>" . (@$desc[1] ? "- $desc[1]" : '') . "</td>";
		echo "\n</tr>";
	}
	echo "</table>";
}
?>
