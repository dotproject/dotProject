<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

global $AppUI, $dPconfig, $locale_char_se;

$human_resource_id = intval(dPgetParam($_GET, 'human_resource_id', 0));

$hr = new CHumanResource();
if ($human_resource_id && !$hr->load($human_resource_id)) {
    $AppUI->setMsg('Human Resources');
    $AppUI->setMsg('invalidID', UI_MSG_ERROR, true);
    $AppUI->redirect();
}

$user_id = intval(dPgetParam($_GET, 'user_id', 0));
if (!$human_resource_id)
    $hr->human_resource_user_id = $user_id;

$titleBlock = new CTitleBlock((($human_resource_id) ? 'Edit Human Resource' : 'Configuração de RH'),'applet3-48.png', $m, "$m.$a");

$company_id = intval(dPgetParam($_GET, 'company_id', 0));
$query = new DBQuery();
$query->addTable('companies', 'c');
$query->addQuery('company_name');
$query->addWhere('c.company_id = ' . $company_id);
$res = & $query->exec();
//$titleBlock->addCrumb(('?m=companies&amp;a=view&amp;company_id=' . $company_id),$res->fields['company_name']);
$query->clear();

$contact_id = intval(dPgetParam($_GET, 'contact_id', 0));
$query = new DBQuery;
$query->addTable('contacts', 'c');
$query->addQuery('contact_last_name, contact_first_name');
$query->addWhere('c.contact_id = ' . $contact_id);
$res = & $query->exec();
$contact_name = $res->fields['contact_first_name'] ." ". $res->fields['contact_last_name'];
//$titleBlock->addCrumb("?m=human_resources&a=view_hr&user_id=".$user_id."&contact_id=".$contact_id."&company_id=".$company_id,  $contact_name);
$query->clear();
//$titleBlock->addCrumb('?m=human_resources&amp;a=view_company_users&amp;company_id=' . $company_id, 'users list');
$titleBlock->show();

$cwd = array();
$cwd[0] = '0';
$cwd[1] = '1';
$cwd[2] = '2';
$cwd[3] = '3';
$cwd[4] = '4';
$cwd[5] = '5';
$cwd[6] = '6';
$cwd_conv = array_map('cal_work_day_conv', $cwd);
 
//translation to portuguese
$cwd_conv[0]="Domingo";
$cwd_conv[1]="Segunda-feira";
$cwd_conv[2]="Terça-feira";
$cwd_conv[3]="Quarta-feira";
$cwd_conv[4]="Quinta-feira";
$cwd_conv[5]="Sexta-feira";
$cwd_conv[6]="Sábado";

function cal_work_day_conv($val) {
    global $locale_char_set;
    setlocale(LC_ALL, 'en_AU' . (($locale_char_set) ? ('.' . $locale_char_set) : '.utf8'));
    $wk = Date_Calc::getCalendarWeek(null, null, null, "%a", LOCALE_FIRST_DAY);
    setlocale(LC_ALL, $AppUI->user_lang);

    $day_name = $wk[($val - LOCALE_FIRST_DAY) % 7];
    if ($locale_char_set == "utf-8" && function_exists("utf8_encode")) {
        $day_name = utf8_encode($day_name);
    }
    return htmlentities($day_name, ENT_COMPAT, $locale_char_set);
}
?>
<script>
function displayWorkingDays(){
    var value=$('input[name=eventual]:checked').val();
    if(value=="1"){
        $("#working_days").slideUp();
    }else{
        $("#working_days").slideDown();
    }
}
//initiate the display state
$( document ).ready(function() {
    displayWorkingDays();
});
</script>

<script src="./modules/human_resources/addedit_hr.js"></script>

<form name="editfrm" action="?m=human_resources" method="post">
    <input type="hidden" name="dosql" value="do_hr_aed" />
    <input type="hidden" name="company_id" value="<?php echo $company_id; ?>" />
    <input name="roles_ids" id="roles_ids" type="hidden" value="" />
    <input type="hidden" name="human_resource_id" value="<?php echo dPformSafe($human_resource_id); ?>" />
    <input type="hidden" name="human_resource_user_id" value="<?php echo dPformSafe($hr->human_resource_user_id); ?>" />
    <input type="hidden" name="daily_working_hours" value="<?php echo dPformSafe($dPconfig['daily_working_hours']); ?>" />
    <table border="0" class="std" width="100%" align="center">
        <tr>
            <td align="right" >Nome:</td>
            <td align="left" style="width:80%">
                <?php echo $contact_name ?>
                (<a href="index.php?m=contacts&a=addedit&contact_id=<?php echo $contact_id ?>" target="_blank">Acessar contato</a>)
            </td>
        </tr>
            <tr>
                <td align='right'><?php echo $AppUI->_('Lattes URL'); ?>:</td>
                <td align='left'><input type='text' size="100" maxlength="500" name="human_resource_lattes_url" value="<?php echo dPformSafe($hr->human_resource_lattes_url); ?>" /></td>
            </tr>

             <tr>
                <td align="right" style="vertical-align: top"><?php echo $AppUI->_("LBL_EVENTUAL_INVOLVIMENT"); ?>:</td>
                <td width="2%" align="left" >
                    <input type="radio" name="eventual" value="0" <?php echo $hr->eventual != "1" ? "checked=\"checked\"":""; ?> onchange="displayWorkingDays()" /> <?php echo $AppUI->_("LBL_NO") ?>
                    <input type="radio" name="eventual" value="1" <?php echo $hr->eventual == "1" ? "checked=\"checked\"":""; ?> onchange="displayWorkingDays()" /> <?php echo $AppUI->_("LBL_YES") ?>
                    <br />
                    <span style="color:red">*</span>&nbsp;<span style="color:#1C1C1C;font-size: 0.7em;"><?php echo $AppUI->_("LBL_EVENTUAL_INVOLVIMENT_HINT"); ?></span>
                </td>
            </tr>
            </table>
    <div id="working_days">
        <table border="0" class="std" width="100%" align="center">
                <tr id="working_days_title">
                    <td colspan="2"><strong><?php echo $AppUI->_('Weekday working hours'); ?></strong></td>
                </tr>
                <tr id="working_days_monday">
                    <td align='right'><?php echo $cwd_conv[0]; ?>:</td>
                    <td align='left' style="width:80%"><input type='text' size="2" maxlength="5" name="human_resource_mon" value="<?php echo dPformSafe($hr->human_resource_mon); ?>" />
                    </td>
                </tr>
                <tr id="working_days_tuesday">
                    <td align='right'><?php echo $cwd_conv[1]; ?>:</td>
                    <td align='left'><input type='text' size="2" maxlength="5" name="human_resource_tue"
                                            value="<?php echo dPformSafe($hr->human_resource_tue); ?>" />
                    </td>
                </tr>
                <tr id="working_days_wednesday">
                    <td align='right'><?php echo $cwd_conv[2]; ?>:</td>
                    <td align='left'><input type='text' size="2" maxlength="5" name="human_resource_wed"
                                            value="<?php echo dPformSafe($hr->human_resource_wed); ?>" />
                    </td>
                </tr>
                <tr id="working_days_thuesday">
                    <td align='right'><?php echo $cwd_conv[3]; ?>:</td>
                    <td align='left'><input type='text' size="2" maxlength="5" name="human_resource_thu"
                                            value="<?php echo dPformSafe($hr->human_resource_thu); ?>" />
                    </td>
                </tr>
                <tr id="working_days_friday">
                    <td align='right'><?php echo $cwd_conv[4]; ?>:</td>
                    <td align='left'><input type='text' size="2" maxlength="5" name="human_resource_fri"
                                            value="<?php echo dPformSafe($hr->human_resource_fri); ?>" />
                    </td>
                </tr>
                <tr id="working_days_saturday">
                    <td align='right'><?php echo $cwd_conv[5]; ?>:</td>
                    <td align='left'>
                        <input type='text' size="2" maxlength="5" name="human_resource_sat" value="<?php echo dPformSafe($hr->human_resource_sat); ?>" />
                    </td>
                </tr>
                <tr id="working_days_sunday">
                    <td align='right'><?php echo $cwd_conv[6]; ?>:</td>
                    <td align='left'><input type='text' size="2" maxlength="5" name="human_resource_sun" value="<?php echo dPformSafe($hr->human_resource_sun); ?>" /></td>
                </tr>	
                <tr id="working_days_daily_working_hours">
                    <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Daily working hours'); ?>:</td>
                    <td align="left"><?php echo $dPconfig['daily_working_hours']; ?></td>
                </tr>
        </table>
        </div>
</form>
<br />
<br />
<?php
require_once DP_BASE_DIR . "/modules/human_resources/view_hr_roles.php";
?>
<table width="100%">
    <tr>
        <td colspan="2" align="right">
            <input type="button" value="<?php echo ucfirst($AppUI->_('submit')); ?>" class="button" onclick="submitHumanResource(document.editfrm);" />
            <script>goCompanyHome=true;</script>
            <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/subform_back_button_company.php"); ?>     
        </td>
    </tr>
</table>

