<?php
require_once ("../../../../base.php");
require_once DP_BASE_DIR . ("/includes/config.php");
require_once (DP_BASE_DIR . "/classes/csscolor.class.php"); // Required before main_functions
require_once (DP_BASE_DIR . "/includes/main_functions.php");
require_once (DP_BASE_DIR . "/includes/db_adodb.php");
require_once (DP_BASE_DIR . "/includes/db_connect.php");
require_once (DP_BASE_DIR . "/classes/ui.class.php");
require_once (DP_BASE_DIR . "/classes/permissions.class.php");
require_once (DP_BASE_DIR . "/includes/session.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/dompdf-master-v2/dompdf_config.inc.php");
 
set_time_limit (300);
$htmlCode = file_get_contents($baseUrl . "/export.php?project_id=".$_GET["project_id"]."&print=1");
//$htmlCode = file_get_contents($baseUrl . "/teste.php");
$dompdf = new DOMPDF();
//fix specific characters that aren't threated by html_entity_decode
$htmlCode=str_ireplace("&nbsp;", " ", $htmlCode);
$htmlCode=str_ireplace("&aacute;", "á", $htmlCode);
$htmlCode=str_ireplace("&agrave;", "à", $htmlCode);
$htmlCode=html_entity_decode($htmlCode,0);//convert HTML chars (e.g. &nbsp;) to the real characters
$htmlCode=str_ireplace("&Atilde;&copy;", "é", $htmlCode);
$htmlCode=utf8_decode($htmlCode);
$dompdf->load_html($htmlCode);
$dompdf->render();
$dompdf->stream("project_plan.pdf");
?>