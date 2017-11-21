<?php
if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}

require_once DP_BASE_DIR . "/modules/costs/costs_functions.php";

$budget_reserve_id = intval(dPgetParam($_GET, "budget_reserve_id", 0));
$projectSelected = intval(dPgetParam($_GET, "project_id"));

$q = new DBQuery();
$q->addQuery("*");
$q->addTable("budget_reserve");
$q->addWhere("budget_reserve_id = " . $budget_reserve_id);
//$project_id = $q->loadList();
// check if this record has dependancies to prevent deletion
$msg = "";
$obj = new CBudgetReserve();
$canDelete = $obj->canDelete($msg, $budget_reserve_id);

// load the record data
$obj = null;
if (!db_loadObject($q->prepare(), $obj) && ($budget_reserve_id > 0)) {
    $AppUI->setMsg("Budget");
    $AppUI->setMsg("invalidID", UI_MSG_ERROR, true);
    $AppUI->redirect();
}

$q->clear();
$q->addQuery("project_start_date,project_end_date");
$q->addTable("projects");
$q->addWhere("project_id = \"$projectSelected\"");
$datesProject = & $q->exec();
$dateSP = substr($datesProject->fields["project_start_date"], 0, -9);
$dateTemp = substr($datesProject->fields["project_end_date"], 0, -9);
$dateEP = (string) $dateTemp;
// format dates
$date_begin = intval($obj->budget_reserve_inicial_month) ? new CDate($obj->budget_reserve_inicial_month) : null;
$date_end = intval($obj->budget_reserve_final_month) ? new CDate($obj->budget_reserve_final_month) : null;
$df = $AppUI->getPref("SHDATEFORMAT");
$projectEndDate=new CDate($datesProject->fields["project_end_date"]);
$projectEndDateUserFormat=$projectEndDate->format($df);


require_once (DP_BASE_DIR . '/modules/timeplanning/view/link_to_project.php');
// setup the title block
$ttl = $budget_reserve_id ? "Edit" : "Add";
$titleBlock = new CTitleBlock($ttl, "costs.png", $m, "$m.$a");

/*
$canDelete = getPermission($m, "delete", $budget_reserve_id);
if ($canDelete && $budget_reserve_id > 0) {
    $titleBlock->addCrumbDelete("Deletar", $canDelete, $msg);
}
*/
$titleBlock->show();
?>

<!-- import the calendar script -->
<script type="text/javascript" src="<?php echo DP_BASE_URL; ?>/lib/calendar/calendar.js"></script>
<!-- import the language module -->
<script type="text/javascript" src="<?php echo DP_BASE_URL; ?>/lib/calendar/lang/calendar-<?php echo $AppUI->user_locale; ?>.js"></script>
<script language="javascript">
    function submitIt() {      
        var f = document.uploadFrm;
        var trans = "<?php echo $dateEP; ?>";
        var str1 = String(trans); //project end date
        var str2 = document.getElementById("budget_reserve_final_month").value; //risk end date
        var str3 = document.getElementById("budget_reserve_inicial_month").value; //risk start date
        var financialImpact = f.budget_reserve_financial_impact.value;
        if(str1 != "" && str2 != "" && str3 != "" && financialImpact!="" ){
            var yr1  = parseInt(str1.substring(0,4),10);
            var mon1 = parseInt(str1.substring(5,7),10);
            var dt1  = parseInt(str1.substring(8,10),10);

            var yr2  = parseInt(str2.substring(0,4),10);
            var mon2 = parseInt(str2.substring(4,6),10);
            var dt2  = parseInt(str2.substring(6,8),10);

            var yr3  = parseInt(str3.substring(0,4),10);
            var mon3 = parseInt(str3.substring(4,6),10);
            var dt3  = parseInt(str3.substring(6,8),10);

            var date1 = new Date(yr1, mon1, dt1); // project end date
            var date2 = new Date(yr2, mon2, dt2); // risk end date
            var date3 = new Date(yr3, mon3, dt3); // risk start date


            if(date2 > date1){ //risk end date has to be smaller than project end date
                msg = "\n<?php echo $AppUI->_("LBL_VALIDATION_DATE_CONTINGENCY_PROJECT", UI_OUTPUT_JS) . "  ($projectEndDateUserFormat)" ?>";
                alert(msg);
                return false;
            }

            if(date3 > date2){ //risk start date has to be smaller than risk end date
                msg = "\n<?php echo $AppUI->_("LBL_VALIDATION_DATE_CONTINGENCY_RISK", UI_OUTPUT_JS) ?>";
                alert(msg);
                return false;
            }
        }else{
            msg = "\n<?php echo $AppUI->_("LBL_MANDARORY_FIELDS", UI_OUTPUT_JS) ?>";
            alert(msg);
            return false;
        }
        var msg = "";        
        var foc=false;
        financialImpact =parseFloat(financialImpact);
        if (isNaN(financialImpact) || financialImpact  == 0 || financialImpact  < 0) {
            msg += "\n<?php echo $AppUI->_("LBL_VALIDATION_FINANCIAL_IMPACT", UI_OUTPUT_JS); ?>";
            if ((foc==false) && (navigator.userAgent.indexOf("MSIE")== -1)) {
                f.budget_reserve_financial_impact.focus();
                foc=true;
            }
        }
        
        if (msg.length < 1) {
            f.submit();
        } else {
            alert(msg);
        }
        
    }
    
    function delIt() {
        if (confirm("<?php echo $AppUI->_("Delete this Contingency cost?", UI_OUTPUT_JS); ?>")) {
            var f = document.uploadFrm;
            f.del.value="1";
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
        var FI = document.getElementById("budget_reserve_financial_impact").value; 
        document.getElementById("budget_reserve_value_total").value = FI; 
        /*
        var date1 = document.getElementById("budget_reserve_inicial_month").value;
        var date2 = document.getElementById("budget_reserve_final_month").value;
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
            total = FI;
        else
            total = FI * (Math.floor(num_months)+1);
            
        document.getElementById("budget_reserve_value_total").value = total; 
        document.getElementById("text_total").innerHTML=total;
        */
    }
    
    function popCalendar( field ){
        calendarField = field;
        idate = eval( "document.uploadFrm.budget_" + field + ".value" );
        window.open( "index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=" + idate, "calwin", "width=280, height=250, scrollbars=no" );
    }

    /**
     *	@param string Input date in the format YYYYMMDD
     *	@param string Formatted date
     */
    function setCalendar( idate, fdate ) {
        fld_date = eval( "document.uploadFrm.budget_" + calendarField );
        fld_fdate = eval( "document.uploadFrm." + calendarField );
        fld_date.value = idate;
        fld_fdate.value = fdate;

        // set end date automatically with start date if start date is after end date
        if (calendarField == "reserve_inicial_month") {
            if( document.uploadFrm.reserve_final_month.value < idate) {
                document.uploadFrm.budget_reserve_final_month.value = idate;
                document.uploadFrm.reserve_final_month.value = fdate;
            }
        }
    }
</script>

<link href="modules/timeplanning/css/table_form.css" type="text/css" rel="stylesheet" />
<form name="uploadFrm" action="?m=costs" method="post">
    <input type="hidden" name="dosql" value="do_budget_reserve_aed" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="project_id" value="<?php echo $_GET["project_id"]; ?>" />
    <input type="hidden" name="budget_reserve_id" value="<?php echo $budget_reserve_id; ?>" />
    <input type="hidden" name="budget_reserve_value_total"  id="budget_reserve_value_total" value="<?php echo dPformSafe($obj->budget_reserve_value_total); ?>" />
    
    <table width="100%" border="0" cellpadding="3" cellspacing="3" class="std" name="table_form">
        <tr>
            <th colspan="2">
                <?php echo $AppUI->_("LBL_COST_CONTINGENCY_RESERVE_REGISTER"); ?>
            </th>
        </tr>
        <tr>
            <td class="td_label"><?php echo $AppUI->_("Name"); ?>:</td>
            <td>
                <?php
                echo dPformSafe($obj->budget_reserve_description);
                ?>
            </td>
        </tr>

        <tr>
            <td class="td_label"><?php echo $AppUI->_("Financial Impact"); ?>&nbsp;(<?php echo dPgetConfig("currency_symbol") ?>)<span class="span_mandatory">*</span>:</td>
            <td>
                <input name="budget_reserve_financial_impact" id="budget_reserve_financial_impact" value="<?php echo dPformSafe($obj->budget_reserve_financial_impact); ?>" />
            </td>
        </tr>

        <tr>
            <td class="td_label"><?php echo $AppUI->_("Date Begin"); ?><span class="span_mandatory">*</span>:</td>
            <td>
                <input type="hidden" name="budget_reserve_inicial_month" id="budget_reserve_inicial_month"  value="<?php echo (($date_begin) ? $date_begin->format(FMT_TIMESTAMP_DATE) : ""); ?>"/>
                <!-- format(FMT_TIMESTAMP_DATE) -->
                <input type="text" class="text" style="width:85px" name="reserve_inicial_month" id="date0" value="<?php echo (($date_begin) ? $date_begin->format($df) : ""); ?>" disabled="disabled" />

                <a href="#" onclick="popCalendar( 'reserve_inicial_month', 'reserve_inicial_month');">
                    <img src="./images/calendar.gif" width="24" height="12" alt="{dPtranslate word='Calendar'}" border="0" />
                </a>
            </td>
        </tr>

        <tr>
            <td class="td_label"><?php echo $AppUI->_("Date End"); ?><span class="span_mandatory">*</span>:</td>
            <td>
                <input type="hidden" name="budget_reserve_final_month" id="budget_reserve_final_month" value="<?php echo (($date_end) ? $date_end->format(FMT_TIMESTAMP_DATE) : ""); ?>"/>
                <!-- format(FMT_TIMESTAMP_DATE) -->
                <input type="text" class="text" style="width:85px"  name="reserve_final_month" id="date1" value="<?php echo (($date_end) ? $date_end->format($df) : ""); ?>" disabled="disabled" />

                <a href="#" onclick="popCalendar('reserve_final_month', 'reserve_final_month');">
                    <img src="./images/calendar.gif" width="24" height="12" alt="{dPtranslate word='Calendar'}" border="0" />
                </a>
                &nbsp;<?php echo $AppUI->_("LBL_VALIDATION_DATE_CONTINGENCY_PROJECT")?>&nbsp; (<?php echo $projectEndDateUserFormat ?>)
            </td>
        </tr>
        <tr>   
            <td align="right" colspan="2">
                <input type="button" class="button" value="<?php echo ucfirst($AppUI->_("LBL_SUBMIT")); ?>" onclick="sumTotalValue();submitIt();" />
                
                 <script> var targetScreenOnProject="/modules/costs/view_budget.php";</script>
                <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/subform_back_button_project.php"); ?>
            </td>
        </tr>
    </table>
    <span class="span_mandatory">*</span> <?php echo $AppUI->_("Required Fields"); ?>
</form>