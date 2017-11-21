<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}
require_once DP_BASE_DIR . "/modules/costs/costs_functions.php";

$cost_id = intval(dPgetParam($_GET, 'cost_id', 0));
$project_id = intval(dPgetParam($_GET, 'project_id', 0));

// check permissions for this record
$canEdit = getPermission($m, 'edit', $cost_id);
if (!(($canEdit && $cost_id) || ($canAuthor && !($cost_id)))) {
    $AppUI->redirect('m=public&a=access_denied');
}

$q = new DBQuery();
$q->addQuery('*');
$q->addTable('costs');
$q->addWhere('cost_id = ' . $cost_id);

// check if this record has dependancies to prevent deletion
$msg = '';
$obj = new CCosts();
$canDelete = $obj->canDelete($msg, $cost_id);

// load the record data
$obj = null;
if (!db_loadObject($q->prepare(), $obj) && ($cost_id > 0)) {
    $AppUI->setMsg('Estimative Costs');
    $AppUI->setMsg("invalidID", UI_MSG_ERROR, true);
    $AppUI->redirect();
}

/* transform date to dd/mm/yyyy */
$date_begin = intval($obj->cost_date_begin) ? new CDate($obj->cost_date_begin) : null;
$date_end = intval($obj->cost_date_end) ? new CDate($obj->cost_date_end) : null;
$df = $AppUI->getPref('SHDATEFORMAT');

/* Get date end project */
$q->clear();
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

/* There is no sense to delete cost with human resource, because it is automatially included/exclude by the system when the cost baseline page is loaded.
if ($canDelete && $cost_id > 0) {
    $titleBlock->addCrumbDelete('delete human resource', $canDelete, $msg);
}
*/

$titleBlock->show();
?>
<link href="modules/timeplanning/css/table_form.css" type="text/css" rel="stylesheet" />
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
        if(date2 > date1)
        {
            msg = "\n<?php echo $AppUI->_("LBL_VALIDATION_DATE_CONTINGENCY_PROJECT", UI_OUTPUT_JS); ?>";
            alert(msg);
            return false;
        }
        
        var msg = '';        
        var foc=false;
        if (f.cost_quantity.value == 0 || f.cost_quantity.value < 0) {
            msg += "\n<?php echo $AppUI->_("LBL_VALIDATION_HOURS_PER_MONTH", UI_OUTPUT_JS); ?>";
            if ((foc==false) && (navigator.userAgent.indexOf('MSIE')== -1)) {
                f.cost_quantity.focus();
                foc=true;
            }
        }
        if (msg.length < 1) {
            sumTotalValue();
            f.submit();
        } else {
            alert(msg);
        }
        
    }
    
    function delIt() {
        if (confirm("<?php echo $AppUI->_("LBL_DELETE_HUMAN_RESOURCE_COST_ESTIMATIVE", UI_OUTPUT_JS); ?>")) {
            var f = document.uploadFrm;
            f.del.value='1';
            f.submit();
        }
    }
    
    function monthDiff(d1, d2) {
        var months;
        months = (d2.getFullYear() - d1.getFullYear()) * 12;
        months -= d1.getMonth() + 1;
        months += d2.getMonth();
        return months;
    }
    
    function sumTotalValue(){ 
        var VU = document.getElementById('cost_value_unitary').value; 
        var HM = document.getElementById('cost_quantity').value;
        var date1 = document.getElementById('cost_date_begin').value;
        var date2 = document.getElementById('cost_date_end').value;
        var total = 0;
        
        
        var year1 =  date1.substring(0,4);
        var month1 =  date1.substring(4,6);
        var day1 = date1.substring(6);
        
        var year2 =  date2.substring(0,4);
        var month2 =  date2.substring(4,6);
        var day2 = date2.substring(6);
        
        var diffMonths = monthDiff(new Date(year1,month1,day1),new Date(year2,month2,day2)); 
        
        var diff_date = new Date(year2,month2,day2) - new Date(year1,month1,day1) ;
        var num_months = (diff_date % 31536000000)/2628000000;   
        
        if(diffMonths < 0)
            total = VU * HM;
        else
            total = (VU * HM) * (Math.floor(num_months)+1);
            
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

<form name="uploadFrm" action="?m=costs" method="post">
    <input type="hidden" name="dosql" value="do_costs_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="cost_id" value="<?php echo $cost_id; ?>" />
    <input type="hidden" name="cost_project_id" value="<?php echo $project_id ?>" />
    <input type="hidden" name="cost_type_id" value="0" />
    <table width="100%" border="0" cellpadding="3" cellspacing="3" class="std" name="table_form">
        <tr>
            <th colspan="2"><?php echo $AppUI->_("LBL_COST_HUMAN_RESOURCE_REGISTER"); ?></th>
        </tr>
        <tr>
            <td class="td_label"><?php echo $AppUI->_('Name'); ?>:</td>
            <td>
                <?php
                echo dPformSafe($obj->cost_description);
                ?>
            </td>
        </tr>
        <tr>
            <td class="td_label"><?php echo $AppUI->_('Date Begin'); ?><span class="span_mandatory">*</span>:</td>
            <td>
                <input type="hidden" name="cost_date_begin" id="cost_date_begin"  value="<?php echo (($date_begin) ? $date_begin->format(FMT_TIMESTAMP_DATE) : ''); ?>"/>
                <!-- format(FMT_TIMESTAMP_DATE) -->
                <input type="text" style="width:85px" class="text" name="date_begin" id="date0" value="<?php echo (($date_begin) ? $date_begin->format($df) : ''); ?>" disabled="disabled"  />

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
            <td class="td_label"><?php echo $AppUI->_('Hours per Month'); ?>*:</td>
            <td>
                <input name="cost_quantity"  id="cost_quantity" value="<?php echo dPformSafe($obj->cost_quantity); ?>" />
            </td>
        </tr>
        <tr>
            <td class="td_label"><?php echo $AppUI->_('Unitary Value'); ?> &nbsp;(<?php echo dPgetConfig("currency_symbol") ?>):</td>
            <td>
                <span id="text_unit_value"><?php echo dPformSafe($obj->cost_value_unitary); ?></span>
                <input type="hidden" name="cost_value_unitary"  id="cost_value_unitary" value="<?php echo dPformSafe($obj->cost_value_unitary); ?>" />
            </td>
        </tr>
        <tr>
            <td class="td_label"><?php echo $AppUI->_('Total Value'); ?> &nbsp;(<?php echo dPgetConfig("currency_symbol") ?>):</td>
            <td>
                <span id="text_total" > <?php echo dPformSafe($obj->cost_value_total); ?> </span>
                <input type="hidden" name="cost_value_total"  id="cost_value_total" value="<?php echo dPformSafe($obj->cost_value_total); ?>" />
                <span style="color: #6E6E6E">(<?php echo $AppUI->_("LBL_COST_HR_RULE_OF_CALCULUS"); ?>)</span>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="right">
                <input type="button" class="button" value="<?php echo ucfirst($AppUI->_("LBL_SUBMIT")); ?>" onclick="sumTotalValue();submitIt()" />        
               <script> var targetScreenOnProject="/modules/costs/view_costs.php";</script>
               <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/subform_back_button_project.php"); ?>
            </td>
        </tr>
    </table>
    <br />
    <span class="span_mandatory">*</span> <?php echo $AppUI->_('Required Fields'); ?>
    <br />
    <span style='color:red'>*</span> <?php echo $AppUI->_("LBL_RH_AUTOMATICALLY_ADDED_COST_BASELINE") ?>
</form>