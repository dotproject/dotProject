<?php
// chama a classe 'class.ezpdf.php' necess�ria para se gerar o documento
//include "lib/ezpdf/class.ezpdf.php"; 
$font_dir = DP_BASE_DIR.'/lib/ezpdf/fonts';
require($AppUI->getLibraryClass('ezpdf/class.ezpdf'));

$id=intval(dPgetParam($_GET, 'id', 0));

$q = new DBQuery();
$q->addQuery('*');
$q->addTable('initiating_stakeholder');
$q->addWhere('initiating_stakeholder_id = ' . $id);

$obj = new CStakeholder();

// load the record data
$obj = null;
if (!db_loadObject($q->prepare(), $obj) && $id > 0) {
	$AppUI->setMsg('Initiating');
	$AppUI->setMsg("invalidID", UI_MSG_ERROR, true);
	$AppUI->redirect();
}

$q = new DBQuery();
$q->addQuery('*');
$q->addTable('contacts');
$q->addWhere('contact_id = ' . $obj->contact_id);
$contact = $q->loadHash();

$q = new DBQuery();
$q->addQuery('*');
$q->addTable('initiating');
$q->addWhere('initiating_id = ' . $obj->initiating_id);
$initiating = $q->loadHash();

// instancia um novo documento com o nome de pdf
$pdf = new Cezpdf();

// seta a fonte que ser� usada para apresentar os dados
//essas fontes s�o aquelas dentro do diret�rio GeraPDF/fonts
//$pdf->selectFont('lib/ezpdf/Helvetica.afm'); 
$pdf->selectFont("$font_dir/Helvetica.afm");

// chama o m�todo ezText e passa o texto que dever� ser apresentado no documento
//o numero ap�s o texto se refere ao tamanho da fonte
$pdf->ezText("\n");
$pdf->ezText("<b>". utf8_decode($AppUI->_("LBL_STAKEHOLDER_MATRIX",UI_OUTPUT_HTML)) ."</b>",18,array('justification'=>'center')); 
$pdf->ezText('');
$pdf->ezText('');
$pdf->ezText("<b>Stakeholder: </b>" . $contact['contact_first_name'] . " " .  $contact['contact_last_name'],16);
$pdf->ezText('');
$pdf->ezText("<b>". utf8_decode($AppUI->_("Project Title",UI_OUTPUT_HTML)) . ":</b>" . $initiating['initiating_title'],16);
$pdf->ezText('');
$pdf->ezText("<b>".  $AppUI->_("Responsibilities").":</b>" . $obj->stakeholder_responsibility,16);
$pdf->ezText('');
if ($obj->stakeholder_power == 1) {
	$pdf->ezText("<b>".  $AppUI->_("Power").": </b>" . "Alto",16);
} else if ($obj->stakeholder_power == 2) {
	$pdf->ezText("<b>".  $AppUI->_("Power").": </b>" . "Baixo",16);
} else {
	$pdf->ezText("<b>".  $AppUI->_("Power").": </b>",16);
}
$pdf->ezText('');
if ($obj->stakeholder_interest == 1) {
	$pdf->ezText("<b>".  $AppUI->_("Interest").": </b>" . "Alto",16);
} else if ($obj->stakeholder_interest == 2) {
	$pdf->ezText("<b>".  $AppUI->_("Interest").": </b>" . "Baixo",16);
} else {
	$pdf->ezText("<b>".  $AppUI->_("Interest").": </b>",16);
}
$pdf->ezText('');
$strategy=$AppUI->_("Strategy",UI_OUTPUT_HTML);
$pdf->ezText("<b>". utf8_decode($strategy)  .": </b>" . $obj->stakeholder_strategy,16);

// gera o PDF
$pdf->ezStream(); 
?>