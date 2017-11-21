<?php
require_once (DP_BASE_DIR . "/modules/projects/projects.class.php");
require_once (DP_BASE_DIR . "/base.php");
require_once DP_BASE_DIR . ("/includes/config.php");
require_once (DP_BASE_DIR . "/classes/csscolor.class.php"); // Required before main_functions
require_once (DP_BASE_DIR . "/includes/main_functions.php");
require_once (DP_BASE_DIR . "/includes/db_adodb.php");
require_once (DP_BASE_DIR . "/includes/db_connect.php");
require_once (DP_BASE_DIR . "/classes/ui.class.php");
require_once (DP_BASE_DIR . "/classes/permissions.class.php");
require_once (DP_BASE_DIR . "/includes/session.php");
require_once (DP_BASE_DIR . "/modules/projects/projects.class.php");
require_once (DP_BASE_DIR . "/modules/companies/companies.class.php");
require_once (DP_BASE_DIR . "/modules/contacts/contacts.class.php");
require_once (DP_BASE_DIR . "/modules/admin/admin.class.php");
include_once (DP_BASE_DIR . "/locales/core.php");
include ($AppUI->getLibraryClass("jpgraph/src/jpgraph"));
include ($AppUI->getLibraryClass("jpgraph/src/jpgraph_gantt"));

$user_id = 1;
$showWork = 1;
$flags = "";
$locale_char_set = "utf-8";
$projectId = $_GET["project_id"];
$projectObj = new CProject();
$projectObj->load($projectId);

$companyId = $projectObj->project_company;
$companyObj = new CCompany();
$companyObj->load($companyId);

$userObj = new CUser();
$userObj->load($projectObj->project_owner);

$display_option = "all";
$showAllGantt = 1;
$showLabels = 0;
$showInactive = 0;
$sortTasksByName = 0;
$addPwOiD = 0;
$proFilter = -1;
$startDate = strtotime($projectObj->project_start_date);

if(isset($_GET["f_date_begin"])){
    $startDate = strtotime($_GET["f_date_begin"]);
}

$startDateText = date("Y-m-d", $startDate);
$endDateText = null;
if(isset($_GET["f_end_begin"])){
     $endDate  = strtotime($_GET["f_end_begin"]);
     $endDateText = date("Y-m-d", $endDate);
}else if (isset($projectObj->project_end_date)) {
    $endDate = strtotime($projectObj->project_end_date);
    $endDateText = date("Y-m-d", $endDate);
} else {
    $endDateText = date("Y-m-d");
}

$ganttWidth = 1200;
$dir="modules/timeplanning/view/export_project_plan/temp/";
$imageName = $dir."gantt_" . $projectId . ".png";

if (file_exists($imageName)) {
    unlink($imageName);
}

require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/gantt_chart_generator.php");
if (file_exists($imageName)) {
$source = imagecreatefrompng($imageName);

?>
<img src="<?php echo $imageName; ?>" align="center" />
<?php
}
?>