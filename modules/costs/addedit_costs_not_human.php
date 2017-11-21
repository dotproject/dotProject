<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

require_once DP_BASE_DIR . "/modules/costs/costs_functions.php";

$cost_id = intval(dPgetParam($_GET, 'cost_id', 0));
$project_id = intval(dPgetParam($_GET, 'project_id', 0));

// check if this record has dependancies to prevent deletion
$msg = '';
$obj = new CCosts();
if ($cost_id > 0) {
    // check permissions for this record
    $canEdit = getPermission($m, 'edit', $cost_id);
    if (!(($canEdit && $cost_id) || ($canAuthor && !($cost_id)))) {
        $AppUI->redirect('m=public&a=access_denied');
    }

    $canDelete = $obj->canDelete($msg, $cost_id);
    $obj->load($cost_id);
} else {
    $cost_id = 0;
}

/* transform date to dd/mm/yyyy */
$date_begin = intval($obj->cost_date_begin) ? new CDate($obj->cost_date_begin) : null;
$date_end = intval($obj->cost_date_end) ? new CDate($obj->cost_date_end) : null;
$df = $AppUI->getPref('SHDATEFORMAT');

/* Get end date project */
$q = new DBQuery();
$q->addQuery('project_start_date,project_end_date');
$q->addTable('projects');
$q->addWhere("project_id = '$project_id'");
$datesProject = & $q->exec();
$dateTemp = substr($datesProject->fields['project_end_date'], 0, -9);
$dateEP = (string) $dateTemp;

$projectEndDate=new CDate($datesProject->fields["project_end_date"]);
$projectEndDateUserFormat=$projectEndDate->format($df);


require_once (DP_BASE_DIR . '/modules/timeplanning/view/link_to_project.php');
// setup the title block
$ttl = $cost_id ? "Edit" : "Add";
$titleBlock = new CTitleBlock($ttl, 'costs.png', $m, "$m.$a");
if ($canDelete && $cost_id > 0) {
    $titleBlock->addCrumbDelete('delete non-human resource', $canDelete, $msg);
}
$titleBlock->show();
?>
<!-- import the calendar script -->
<script type="text/javascript" src="<?php echo DP_BASE_URL; ?>/lib/calendar/calendar.js"></script>
<!-- import the language module -->
<script type="text/javascript" src="<?php echo DP_BASE_URL; ?>/lib/calendar/lang/calendar-<?php echo $AppUI->user_locale; ?>.js"></script>

<script language="javascript">
    function submitIt() {
        
        var f = document.uploadFrm;
        //f.submit();
        
        var trans = "<?php echo $dateEP; ?>";
        var str1 = String(trans);
        var str2 = document.getElementById("cost_date_end").value;
        
        
        var yr1  = parseInt(str1.substring(0,4),10);
        var mon1 = parseInt(str1.substring(5,7),10);
        var dt1  = parseInt(str1.substring(8,10),10);
       
        var yr2  = parseInt(str2.substring(0,4),10);
        var mon2 = parseInt(str2.substring(4,6),10);
        var dt2  = parseInt(str2.substring(6,8),10);
        
        
        var date1 = new Date(yr1, mon1, dt1);
        var date2 = new Date(yr2, mon2, dt2);
        if(date2 > date1){
            msg = "\n<?php echo $AppUI->_("LBL_VALIDATION_DATE_CONTINGENCY_PROJECT", UI_OUTPUT_JS); ?>";
            alert(msg);
            return false;
        }
        
        var quant=parseInt(f.cost_quantity.value);
        if(isNaN(quant)){
            msg = "\n<?php echo $AppUI->_("LBL_VALIDATION_QUANTITY", UI_OUTPUT_JS); ?>";
            alert(msg);
            return false;
        }
        
        var name=f.cost_description.value;
        if(name==""){
            msg = "\n<?php echo $AppUI->_("LBL_RESOURCE_VALIDATION_DESCRIPTION", UI_OUTPUT_JS); ?>";
            alert(msg);
            return false;
        }
        
        var msg = '';        
        var foc=false;
        if (f.cost_value_unitary.value == 0 || f.cost_value_unitary.value < 0) {
            msg += "\n<?php echo $AppUI->_("LBL_VALIDATION_UNITARY_VALUE", UI_OUTPUT_JS); ?>";
            
            if ((foc==false) && (navigator.userAgent.indexOf('MSIE')== -1)) {
                f.cost_value_unitary.focus();
                foc=true;
            }
        }else{
            sumTotalValueNH();
        }
        if (msg.length < 1) {
            f.submit();
        } else {
            alert(msg);
        }
    }
    
    function delIt() {
        if (confirm("<?php echo $AppUI->_("LBL_DELETE_NON_HUMAN_RESOURCE_COST_ESTIMATIVE", UI_OUTPUT_JS); ?>")) {
            var f = document.uploadFrm;
            f.del.value='1';
            f.submit();
        }
    }
    
    function sumTotalValueNH(){ 
        var f = document.uploadFrm;
        var qtd= parseInt(f.cost_quantity.value);
        var uniValue = document.getElementById('cost_value_unitary').value;    
        var total = qtd * uniValue;
                           
        document.getElementById("cost_value_total").value = total;
        document.getElementById("text_total").innerHTML= total;
    }
    
    function popCalendar( field ){
        calendarField = field;
        idate = eval( 'document.uploadFrm.cost_' + field + '.value' );
        window.open( 'index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'width=280, height=250, scrollbars=no' );
    }

    /**
     *	@param string Input date in the format YYYYMMDD
     *	@param string Formatted date
     */
    function setCalendar( idate, fdate ) {
        fld_date = eval( 'document.uploadFrm.cost_' + calendarField );
        fld_fdate = eval( 'document.uploadFrm.' + calendarField );
        fld_date.value = idate;
        fld_fdate.value = fdate;

        // set end date automatically with start date if start date is after end date
        if (calendarField == 'cost_date_end') {
            if( document.uploadFrm.date_end.value < idate) {
                document.uploadFrm.cost_date_end.value = idate;
                document.uploadFrm.date_end.value = fdate;
            }
        }
    }
</script>
<link href="modules/timeplanning/css/table_form.css" type="text/css" rel="stylesheet" />
<form name="uploadFrm" action="?m=costs" method="post">
    <input type="hidden" name="dosql" value="do_costs_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="cost_id" value="<?php echo $cost_id; ?>" />
    <input type="hidden" name="cost_project_id" value="<?php echo $project_id ?>" />
    <input type="hidden" name="cost_type_id" value="1" />
    <table width="100%" border="0" cellpadding="3" cellspacing="3" class="std" name="table_form">
        <tr>
            <th colspan="2" style="padding: 3px">
                <?php echo $AppUI->_("LBL_COST_NON_HUMAN_RESOURCE_REGISTER"); ?>
            </th>
        </tr>
        <tr>
            <td class="td_label"><?php echo $AppUI->_('Name'); ?><span class="span_mandatory">*</span>:</td>
            <td>
                <input type="text" name="cost_description" value="<?php echo $obj->cost_description ?>" />
            </td>
        </tr>

        <tr>
            <td class="td_label"><?php echo $AppUI->_('Quantity'); ?><span class="span_mandatory">*</span>:</td>
            <td id="cost_quantity">
                <input type="text" name="cost_quantity" value="<?php echo $obj->cost_quantity ?>" />
            </td>
        </tr>

        <tr>
            <td class="td_label"><?php echo $AppUI->_('Date Begin'); ?><span class="span_mandatory">*</span>:</td>
            <td>
                <input type="hidden" name="cost_date_begin" id="cost_date_begin"  value="<?php echo (($date_begin) ? $date_begin->format(FMT_TIMESTAMP_DATE) : ''); ?>"/>
                <!-- format(FMT_TIMESTAMP_DATE) -->
                <input type="text" style="width:85px" class="text" name="date_begin" id="date0" value="<?php echo (($date_begin) ? $date_begin->format($df) : ''); ?>" disabled="disabled" />

                <a href="#" onclick="popCalendar( 'date_begin', 'date_begin');">
                    <img src="./images/calendar.gif" width="24" height="12" alt="{dPtranslate word='Calendar'}" border="0" />
                </a>
            </td>
        </tr>

        <tr>
            <td class="td_label"><?php echo $AppUI->_('Date End'); ?><span class="span_mandatory">*</span>:</td>
            <td>
                <input type="hidden" name="cost_date_end" id="cost_date_end"  value="<?php echo (($date_end) ? $date_end->format(FMT_TIMESTAMP_DATE) : ''); ?>"/>
                <!-- format(FMT_TIMESTAMP_DATE) -->
                <input type="text" style="width:85px" class="text" name="date_end" id="date1" value="<?php echo (($date_end) ? $date_end->format($df) : ''); ?>" disabled="disabled" />

                <a href="#" onclick="popCalendar( 'date_end', 'date_end');">
                    <img src="./images/calendar.gif" width="24" height="12" alt="{dPtranslate word='Calendar'}" border="0" />
                </a>
                &nbsp;<?php echo $AppUI->_("LBL_VALIDATION_DATE_CONTINGENCY_PROJECT")?>&nbsp; (<?php echo $projectEndDateUserFormat ?>)
            </td>
        </tr>

        <tr>
            <td class="td_label"><?php echo $AppUI->_('Unitary Value'); ?> &nbsp;(<?php echo dPgetConfig("currency_symbol") ?>)<span class="span_mandatory">*</span>:</td>
            <td>
                <input name="cost_value_unitary" id="cost_value_unitary"  onchange="sumTotalValueNH()" value="<?php echo dPformSafe($obj->cost_value_unitary); ?>" /> 
            </td>
        </tr>

        <tr>
            <td class="td_label"><?php echo $AppUI->_('Total Value'); ?> &nbsp;(<?php echo dPgetConfig("currency_symbol") ?>):</td>
            <td>
                <span id="text_total"><?php echo number_format($obj->cost_value_total, 2, ',', '.'); ?></span>
                <input type="hidden" name="cost_value_total"  id="cost_value_total" value="<?php echo dPformSafe($obj->cost_value_total); ?>"  />
                <span style="color: #6E6E6E">(<?php echo $AppUI->_("LBL_COST_NHR_RULE_OF_CALCULUS", UI_OUTPUT_HTML ); ?>)</span>
            </td>
        </tr>

         <tr>
            <td align="right" colspan="2">
                <input type="button" class="button" value="<?php echo ucfirst($AppUI->_("LBL_SUBMIT")); ?>" onclick="submitIt()" />
               <script> var targetScreenOnProject="/modules/costs/view_costs.php";</script>
               <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/subform_back_button_project.php"); ?>
          
            </td>
        </tr>
    </table>
    <br />
    <span class="span_mandatory">*</span> <?php echo $AppUI->_('Required Fields'); ?>
</form>