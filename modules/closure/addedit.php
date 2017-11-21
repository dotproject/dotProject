<?php
/* PROJECTS $Id: addedit.php 5433 2007-10-17 15:02:46Z gregorerhardt $ */
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly');
}
require_once DP_BASE_DIR . "/modules/projects/projects.class.php";
require_once DP_BASE_DIR . "/modules/closure/closure.class.php";
if (!isset($project_id)) {
    $project_id = $_GET["project_id"];
}
$projectObj = new CProject();
$projectObj->load($project_id);

// load the record data
$row = new CClosure();
if ($pma_id > 0 && !$row->load($pma_id)) {
    $AppUI->setMsg('Post Mortem');
    $AppUI->setMsg('invalidID', UI_MSG_ERROR, true);
//	$AppUI->redirect();
}
$df = $AppUI->getPref('SHDATEFORMAT');
if ($pma_id == 0) {
    $q = new DBQuery();
    $q->addTable('projects');
    $q->addQuery('project_target_budget, project_start_date, project_end_date, project_name');
    $q->addWhere('project_id=' . $project_id);
    $res = & $q->exec();

    $row->planned_budget = $res->fields['project_target_budget'];
    $row->project_planned_start_date = $res->fields['project_start_date'];
    $row->project_planned_end_date = $res->fields['project_end_date'];
    $row->project_name = $res->fields['project_name'];

    //$start_date = new CDate($row->project_planned_start_date);
    $end_date = intval($row->project_planned_end_date) ? new CDate($row->project_planned_end_date) : null;
    $meeting_date = new CDate();
    $start_date = new CDate($projectObj->project_start_date);
    $actual_start_date = null;
    $actual_end_date = null;
} else {
    // format dates  
    $start_date = new CDate($row->project_planned_start_date);
    $end_date = intval($row->project_planned_end_date) ? new CDate($row->project_planned_end_date) : null;
    $meeting_date = intval($row->project_meeting_date) ? new CDate($row->project_meeting_date) : null;
    $actual_start_date = intval($row->project_start_date) ? new CDate($row->project_start_date) : null;
    $actual_end_date = intval($row->project_end_date) ? new CDate($row->project_end_date) : null;
    $participants = $row->participants;
}

/* * ************** Display ******************** */
$q = new DBQuery;
$q->addTable('projects');
$q->addQuery('project_name');
$p = $q->loadList();
$projects = array();
$i = 0;
foreach ($p as $project) {
    $projects[$project['project_name']] = $project['project_name'];
}
$q = new DBQuery;
$q->addTable('contacts');
$q->addQuery('contact_first_name, contact_last_name');
$res = & $q->exec();
$pieces = explode(", ", $participants);
?>
<link href="modules/timeplanning/css/table_form.css" type="text/css" rel="stylesheet" />
<form name="editFrm" action="?m=closure" method="post">
    <input type="hidden" name="dosql" value="do_closure_aed" />
    <input type="hidden" name="pma_id" value="<?php echo dPformSafe($pma_id); ?>" />
    <input type="hidden" name='participants' id='participants' value="<?php echo dPformSafe($row->participants); ?>" /> 
    <input type="hidden" name="project_name" value="<?php echo $projectObj->project_name; ?>" />

    <table id="tb1" width='100%' border='0' cellpadding='2' cellspacing='1' class='std'  name="table_form">
        <tr>
            <th colspan="2" align="center">
                <?php echo $AppUI->_("LBL_CLOSURE_POST_MORTEM_TITLE"); ?>
            </th>
        </tr>
        <tr>
            <td class="td_section" colspan="2">
                <?php echo $AppUI->_("LBL_CLOSURE_MEETING_SETTINGS") ?>

            </td>
        </tr>

        <tr > 
            <td class="td_label"><?php echo $AppUI->_("LBL_CLOSURE_PARTICIPANTS") ?>:</td>

            <td valign="top">
                <select multiple size="3" name="list1" style="width: 30%;">

                    <?php
                    $i = 0;
                    for ($res; !$res->EOF; $res->MoveNext()) {
                        $contact_first_name = $res->fields['contact_first_name'];
                        if (array_search($contact_first_name, $pieces) === false) {
                            ?>
                            <option value = <?php echo $i; ?> >
                                <?php echo $contact_first_name; ?>
                            </option>
                            <?php
                            $i++;
                        }
                    }
                    ?>
                </select>

                <input type="button" onClick="move(this.form.list2, this.form.list1)" value="<<" />
                <input type="button" onClick="move(this.form.list1, this.form.list2)" value=">>" />

                <select multiple size="3" valign="left" name="list2" style="width: 30%;">
                    <?php
                    $p2 = "";
                    $limit = count($pieces);

                    $i = 0;
                    if ($limit > 1) {
                        for ($i = 0; $i < $limit; $i++) {
                            $p2 = $p2 . $pieces[$i];
                            ?>
                            <option value = <?php echo $i; ?> >
                                <?php echo $pieces[$i]; ?>
                            </option>
                            <?php
                        }
                    } else {
                        if ($participants) {
                            ?>
                            <option value = <?php echo $i; ?> >
                                <?php echo $participants; ?>
                            </option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>

        <tr>
            <td class="td_label"><?php echo $AppUI->_("LBL_CLOSURE_MEETING_DATE") ?>:</td>
            <td >
                <input type="hidden" name="project_meeting_date" id="project_meeting_date" value="<?php echo (($meeting_date) ? $meeting_date->format(FMT_TIMESTAMP_DATE) : ''); ?>"/>
                <!-- format(FMT_TIMESTAMP_DATE) -->
                <input type="text" class="text" name="meeting_date" id="date0" value="<?php echo (($meeting_date) ? $meeting_date->format($df) : ''); ?>" disabled="disabled"  style="width:80px" />

                <a href="#" onclick="popCalendar('meeting_date', 'meeting_date');">
                    <img src="./images/calendar.gif" width="24" height="12" alt="{dPtranslate word='Calendar'}" border="0" />
                </a>
            </td>
        </tr>

        <tr>
            <td class="td_section" colspan="2">
                <?php echo $AppUI->_("LBL_CLOSURE_PROJECT_SUMMARY") ?>
            </td>
        </tr>
        <!--
        <tr>
            <td class="td_label"><?php echo $AppUI->_("LBL_CLOSURE_PROJECT_NAME") ?>:</td>
            
            <td nowrap="nowrap">
        <?php echo $projectObj->project_name; ?>
            </td>
            
        </tr>
        -->
        <tr>
            <td colspan="2">
                <table>
                    <tr>
                        <td>
                            <table>
                                <tr>

                                    <td class="td_label" nowrap="nowrap"><?php echo $AppUI->_("LBL_CLOSURE_PLANNED_START_DATE") ?>:</td>
                                    <td nowrap="nowrap">
                                        <input type="hidden" name="project_planned_start_date" id="project_planned_start_date" value="<?php echo (($start_date) ? $start_date->format(FMT_TIMESTAMP_DATE) : ''); ?>"/>
                                        <!-- format(FMT_TIMESTAMP_DATE) -->
                                        <input type="text" class="text" name="planned_start_date" id="date1" value="<?php echo (($start_date) ? $start_date->format($df) : ''); ?>" disabled="disabled"  style="width:80px" />

                                        <a href="#" onclick="popCalendar('planned_start_date', 'planned_start_date');">
                                            <img src="./images/calendar.gif" width="24" height="12" alt="{dPtranslate word='Calendar'}" border="0" />
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="td_label" nowrap="nowrap"><?php echo $AppUI->_("LBL_CLOSURE_ACTUAL_START_DATE") ?>:</td>
                                    <td nowrap="nowrap">
                                        <input type="hidden" name="project_start_date" id="project_start_date" value="<?php echo (($actual_start_date) ? $actual_start_date->format(FMT_TIMESTAMP_DATE) : ''); ?>"/>
                                        <!-- format(FMT_TIMESTAMP_DATE) -->
                                        <input type="text" class="text" name="start_date" id="date2" value="<?php echo (($actual_start_date) ? $actual_start_date->format($df) : ''); ?>" disabled="disabled"  style="width:80px" />
                                        <a href="#" onclick="popCalendar('start_date', 'start_date');">
                                            <img src="./images/calendar.gif" width="24" height="12" alt="{dPtranslate word='Calendar'}" border="0" />
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="td_label" nowrap="nowrap"><?php echo $AppUI->_("LBL_CLOSURE_PLANNED_END_DATE") ?>:</td>
                                    <td nowrap="nowrap">
                                        <input type="hidden" name="project_planned_end_date" id="project_planned_end_date" value="<?php echo (($end_date) ? $end_date->format(FMT_TIMESTAMP_DATE) : ''); ?>"/>
                                        <!-- format(FMT_TIMESTAMP_DATE) -->
                                        <input type="text" class="text" name="planned_end_date" id="date3" value="<?php echo (($end_date) ? $end_date->format($df) : ''); ?>" disabled="disabled"  style="width:80px" />

                                        <a href="#" onclick="popCalendar('planned_end_date', 'planned_end_date');">
                                            <img src="./images/calendar.gif" width="24" height="12" alt="{dPtranslate word='Calendar'}" border="0" />
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="td_label" nowrap="nowrap"><?php echo $AppUI->_("LBL_CLOSURE_ACTUAL_END_DATE"); ?>:</td>
                                    <td nowrap="nowrap">
                                        <input type="hidden" name="project_end_date" id="project_end_date" value="<?php echo (($actual_end_date) ? $actual_end_date->format(FMT_TIMESTAMP_DATE) : ''); ?>" />
                                        <!-- format(FMT_TIMESTAMP_DATE) -->
                                        <input type="text" class="text" name="end_date" id="date4" value="<?php echo (($actual_end_date) ? $actual_end_date->format($df) : ''); ?>" disabled="disabled"  style="width:80px" />

                                        <a href="#" onclick="popCalendar('end_date', 'end_date');">
                                            <img src="./images/calendar.gif" width="24" height="12" alt="{dPtranslate word='Calendar'}" border="0" />
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td class="td_label" nowrap="nowrap"s><?php echo $AppUI->_("LBL_CLOSURE_PLANNED_BUDGET"); ?>  &nbsp;(<?php echo dPgetConfig("currency_symbol") ?>):</td>
                                    <td><input class="text" name='planned_budget' value="<?php echo dPformSafe($row->planned_budget); ?>"/></td>
                                </tr>

                                <tr>
                                    <td class="td_label" nowrap="nowrap"><?php echo $AppUI->_("LBL_CLOSURE_ACTUAL_BUDGET"); ?>  &nbsp;(<?php echo dPgetConfig("currency_symbol") ?>):</td>
                                    <td><input class="text" name='budget' value="<?php echo dPformSafe($row->budget); ?>"/></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>        
            </td>
        </tr>
        <tr>
            <td class="td_section" colspan="2">
                <?php echo $AppUI->_("LBL_CLOSURE_LEASSONS_LEARNED"); ?>
            </td>
        </tr>
        <tr>
            <td class="td_label"><?php echo $AppUI->_("LBL_CLOSURE_PROJECT_STRENGHTS") ?>:</td>
            <td>
                <textarea name='project_strength' cols=50 rows=3 ><?php echo dPformSafe($row->project_strength); ?></textarea>
                <br />
                <?php echo $AppUI->_("LBL_CLOSURE_STRENGHTS_HINTS") ?>
            </td>
        </tr>
        <tr>
            <td class="td_label"><?php echo $AppUI->_("LBL_CLOSURE_PROJECT_WEAKNESSES"); ?>:</td>
            <td><textarea name='project_weaknesses' cols=50 rows=3 ><?php echo dPformSafe($row->project_weaknesses); ?></textarea>
                <br />
                <?php echo $AppUI->_("LBL_CLOSURE_PROJECT_WEAKNESSES_HINTS") ?>
            </td>
        </tr>
        <tr>
            <td class="td_label"><?php echo $AppUI->_("LBL_CLOSURE_IMPROMENT_SUGGESTIONS"); ?>:</td>
            <td><textarea name='improvement_suggestions' cols=50 rows=3 ><?php echo dPformSafe($row->improvement_suggestions); ?></textarea>
                <br />
                <?php echo $AppUI->_("LBL_CLOSURE_IMPROMENT_SUGGESTIONS_HINTS"); ?>                    
            </td>
        </tr>
        <tr>
            <td class="td_label"><?php echo $AppUI->_("LBL_CLOSURE_CONCLUSIONS"); ?>:</td>
            <td>
                <textarea name='conclusions' cols=50 rows=3 ><?php echo dPformSafe($row->conclusions); ?></textarea>
                <br />
                <?php echo $AppUI->_("LBL_CLOSURE_CONCLUSIONS_HINTS"); ?>
            </td>
        </tr>
    </table>

    <div>
        <table cellspacing="1" cellpadding="1" border="0" width="100%">
            <tr>
                <td>
                   <!-- <input type="button" value="<?php echo $AppUI->_('back'); ?>" class="button" onclick="javascript:history.back(-1);" /> -->
                </td>
                <td align="right">
                    <input type="button" value="<?php echo $AppUI->_('LBL_SUBMIT'); ?>"
                           class="button" onclick="move(editFrm.list1, editFrm.list2);
                        submitItHere();" />
                </td>

            </tr>
        </table>
    </div>
</form>

<!-- import the calendar script -->
<script type="text/javascript" src="<?php echo DP_BASE_URL; ?>/lib/calendar/calendar.js"></script>
<!-- import the language module -->
<script type="text/javascript" src="<?php echo DP_BASE_URL; ?>/lib/calendar/lang/calendar-<?php echo $AppUI->user_locale; ?>.js"></script>

<script type="text/javascript" language="javascript">

                    function setShort() {
                        var f = document.editFrm;
                        var x = 10;
                        if (f.project_name.value.length < 11) {
                            x = f.project_name.value.length;
                        }
                        if (f.project_short_name.value.length == 0) {
                            f.project_short_name.value = f.project_name.value.substr(0, x);
                        }
                    }

                    var calendarField = '';
                    var calWin = null;

                    function popCalendar(field) {
                        calendarField = field;
                        idate = eval('document.editFrm.project_' + field + '.value');
                        window.open('index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'width=280, height=250, scrollbars=no');
                    }

                    /**
                     *	@param string Input date in the format YYYYMMDD
                     *	@param string Formatted date
                     */
                    function setCalendar(idate, fdate) {
                        fld_date = eval('document.editFrm.project_' + calendarField);
                        fld_fdate = eval('document.editFrm.' + calendarField);
                        fld_date.value = idate;
                        fld_fdate.value = fdate;

                        // set end date automatically with start date if start date is after end date
                        if (calendarField == 'start_date') {
                            if (document.editFrm.end_date.value < idate) {
                                document.editFrm.project_end_date.value = idate;
                                document.editFrm.end_date.value = fdate;
                            }
                        }
                    }

                    function submitItHere() {
                        var f = document.editFrm;
                        f.submit();
                    }


                    function move(MenuOrigem, MenuDestino) {

                        var arrMenuOrigem = new Array();

                        var arrMenuDestino = new Array();

                        var arrLookup = new Array();

                        var i;

                        var stringToPersist = "";

                        for (i = 0; i < MenuDestino.options.length; i++) {

                            arrLookup[MenuDestino.options[i].text] = MenuDestino.options[i].value;

                            arrMenuDestino[i] = MenuDestino.options[i].text;
                        }

                        var fLength = 0;

                        var tLength = arrMenuDestino.length;

                        for (i = 0; i < MenuOrigem.options.length; i++) {

                            arrLookup[MenuOrigem.options[i].text] = MenuOrigem.options[i].value;

                            if (MenuOrigem.options[i].selected && MenuOrigem.options[i].value != "") {

                                arrMenuDestino[tLength] = MenuOrigem.options[i].text;
                                tLength++;

                            }

                            else {

                                arrMenuOrigem[fLength] = MenuOrigem.options[i].text;

                                fLength++;

                            }

                        }

                        arrMenuOrigem.sort();

                        arrMenuDestino.sort();

                        MenuOrigem.length = 0;

                        MenuDestino.length = 0;

                        var c;

                        for (c = 0; c < arrMenuOrigem.length; c++) {

                            var no = new Option();

                            no.value = arrLookup[arrMenuOrigem[c]];

                            no.text = arrMenuOrigem[c];

                            MenuOrigem[c] = no;

                        }

                        for (c = 0; c < arrMenuDestino.length; c++) {

                            var no = new Option();

                            no.value = arrLookup[arrMenuDestino[c]];

                            no.text = arrMenuDestino[c];

                            stringToPersist += arrMenuDestino[c];

                            if (c != arrMenuDestino.length - 1)
                                stringToPersist += ", ";

                            MenuDestino[c] = no;

                        }

                        document.getElementById('participants').value = stringToPersist;
                    }

</script>