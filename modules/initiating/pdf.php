<?php
require_once (DP_BASE_DIR ."/base.php");
require_once DP_BASE_DIR . ("/includes/config.php");
require_once (DP_BASE_DIR . "/classes/csscolor.class.php"); // Required before main_functions
require_once (DP_BASE_DIR . "/includes/main_functions.php");
require_once (DP_BASE_DIR . "/includes/db_adodb.php");
require_once (DP_BASE_DIR . "/includes/db_connect.php");
require_once (DP_BASE_DIR . "/classes/ui.class.php");
require_once (DP_BASE_DIR . "/modules/projects/projects.class.php");
require_once (DP_BASE_DIR . "/classes/permissions.class.php");
require_once (DP_BASE_DIR . "/includes/session.php");
require_once (DP_BASE_DIR . "/modules/timeplanning/view/export_project_plan/dompdf-master-v2/dompdf_config.inc.php");

function formatListField($text){
    $text=str_ireplace("\n", "<br />", $text);
    return $text;
}

set_time_limit (300);
$htmlCode = "";
//$htmlCode = file_get_contents($baseUrl . "/teste.php");
$dompdf = new DOMPDF();
//fix specific characters that aren't threated by html_entity_decode
?>

<?php
// chama a classe 'class.ezpdf.php' necess�ria para se gerar o documento
$id=intval(dPgetParam($_GET, 'id', 0));
$q = new DBQuery();
$q->addQuery('*');
$q->addTable('initiating');
$q->addWhere('initiating_id = ' . $id);
$obj = new CInitiating(); 
// load the record data
$obj = null;
if (!db_loadObject($q->prepare(), $obj) && $id > 0) {
	$AppUI->setMsg('Initiating');
	$AppUI->setMsg("invalidID", UI_MSG_ERROR, true);
	$AppUI->redirect();
}
$q = new DBQuery();
$q->addQuery("*");
$q->addTable("contacts","con");
$q->addJoin("users", "u", "u.user_contact=con.contact_id");
$q->addWhere("user_id = " . $obj->initiating_manager);
$contact = $q->loadHash();

//get company info
$projectId = $obj->project_id;
$projectObj = new CProject();
$projectObj->load($projectId);
$companyId = $projectObj->project_company;
$companyObj = new CCompany();
$companyObj->load($companyId);
$companyName = $companyObj->company_name;

$htmlCode.="
    <html>
        <head>
            <meta charset='UTF-8' content='text/html' http-equiv='Content-Type' />
            <style>;
            
                @page {
                    size: A4;
                    margin: 2cm;
                }
                body{
                    margin-top: 1.0cm;
                    margin-left:75px;
                    margin-right:75px;
                }
                
                body, div{
                    font-size:11px;
                    color:#000000;
                    font-family:  arial, Helvetica;
                    line-height: 140%;
                }
            </style>
        </head>
        <body>";
$htmlCode.=("<div style='font-size:18px;text-align:center; font-weight: bold'>".$AppUI->_("LBL_PROJECT_CHARTER")."</div>"); 
$htmlCode.="<br /> <br />";
$htmlCode.="<div style='text-align:right;line-height:115%; color:silver;font-size:11px'>";
//this line below is useless, but necessary to the next line be displayed (bugfix)
$htmlCode.="<span color:white;font-size:1px>". $AppUI->_("LBL_PROJECT_COMPANY",UI_OUTPUT_HTML )."</span>";
$htmlCode.="<span>". $AppUI->_("LBL_PROJECT_COMPANY",UI_OUTPUT_HTML ). "</span>: ". $companyName ."<br />";
$htmlCode.="<span>". $AppUI->_("LBL_DATE",UI_OUTPUT_HTML). "</span>: ". date('d/m/Y', time())."<br />";
$htmlCode.="</div>";
$htmlCode.=("<br /><br /> <div style='text-align: justify;'>");
$htmlCode.=(("<br /><b>".($AppUI->_("Project Title",UI_OUTPUT_HTML)).": </b><br />" . $obj->initiating_title));
$htmlCode.=('<br />');
$htmlCode.=(("<br /><b>".$AppUI->_("LBL_PROJECT_PROJECT_MANAGER").": </b><br />" . $contact['contact_first_name'] . " " .  $contact['contact_last_name']));
$htmlCode.=('<br />');
$htmlCode.=(("<br /><b>".$AppUI->_("Justification").": </b><br />" . formatListField($obj->initiating_justification)));
$htmlCode.=('<br />');
$htmlCode.=(("<br /><b>".$AppUI->_("Objectives").":</b><br />" .  formatListField($obj->initiating_objective)));
$htmlCode.=('<br />');
$htmlCode.=(("<br /><b>".$AppUI->_("Expected Results").": </b><br />" .  formatListField($obj->initiating_expected_result)));
$htmlCode.=('<br />');
$htmlCode.=(("<br /><b>".$AppUI->_("Premises").": </b><br />" .  formatListField($obj->initiating_premise)));
$htmlCode.=('<br />');
$htmlCode.=(("<br /><b>".($AppUI->_("Restrictions",UI_OUTPUT_HTML)). ":</b><br />" .  formatListField($obj->initiating_restrictions)));
$htmlCode.=('<br />');
$htmlCode.=(("<br /><b>".($AppUI->_("Budget",UI_OUTPUT_HTML)). " (R$): </b><br />" .  number_format($obj->initiating_budget, 2, ',', '.')));
$htmlCode.=('<br />');

$dateStart=date("d/m/Y", strtotime($obj->initiating_start_date));
$dateEnd=date("d/m/Y", strtotime($obj->initiating_end_date));

$htmlCode.=(("<br /><b>".$AppUI->_("Start Date",UI_OUTPUT_HTML).": </b><br />" .  $dateStart));
$htmlCode.=('<br />');
$htmlCode.=(("<br /><b>".$AppUI->_("End Date",UI_OUTPUT_HTML).": </b><br />" . $dateEnd ));
$htmlCode.=('<br />');
$htmlCode.=(("<br /><b>".$AppUI->_("Milestones",UI_OUTPUT_HTML).": </b><br />" .  formatListField($obj->initiating_milestone)));
$htmlCode.=('<br />');
$htmlCode.=(("<br /><b>".$AppUI->_("Criteria for success",UI_OUTPUT_HTML).": </b><br />" .  formatListField($obj->initiating_success)));
$htmlCode.=('</div>');
$htmlCode.=('<br />');
$htmlCode.=('<br />');
$htmlCode.=("<div style='text-align:center'><b>".$AppUI->_("LBL_SIGNATURE",UI_OUTPUT_HTML)."</b></div>"); 
$htmlCode.= "<br /><br /><br /><br />";
$htmlCode.= "<div style='text-align:center'>";
$htmlCode.= "<span style='border-top: 1px solid #000000'>Patrocinador do projeto</span>";
$htmlCode.= "<br /><br /><br /><br />";
$htmlCode.= "<span style='border-top: 1px solid #000000'>Gerente do projeto</span>";
$htmlCode.= "</div>";
$htmlCode.= "</body></html>";


$htmlCode=utf8_decode($htmlCode); //keep this line, make the special chars on labels keeps well formated
//$htmlCode=str_ireplace("\n", "<br />", $htmlCode);
$htmlCode=str_ireplace("&nbsp;", " ", $htmlCode);
$htmlCode=str_ireplace("&aacute;", "á", $htmlCode);
//tmlCode=str_ireplace("&agrave;", "à", $htmlCode);
//$htmlCode=html_entity_decode($htmlCode,0);//convert HTML chars (e.g. &nbsp;) to the real characters
//$htmlCode=str_ireplace("&Atilde;&copy;", "é", $htmlCode);
//echo $htmlCode;
$dompdf->load_html($htmlCode);
$dompdf->render();
$dompdf->stream("project_charter_". $id.".pdf");
?>