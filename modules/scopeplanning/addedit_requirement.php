<?php
/**
 * Scope Planning Module
 * @author Danilo Felicio Jr danilofjr@hotmail.com
 * May/2013
 */
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

$req_id = intval(dPgetParam($_GET, 'reqid', 0));
$project_id = intval(dPgetParam($_GET, 'projid', 0));

$titleBlock = new CTitleBlock((($req_id) ? 'LBL_SP_EDIT_REQ' : 'LBL_SP_CREATE_REQ'),
                'scope.png', $m, "$m.$a");
$titleBlock->addCrumb("?m=$m", "LBL_SP_SCOPEPLANNING");
$href = "?m=$m";

$canDelete = getPermission($m, 'delete', $req_id);
if ($canDelete && $req_id > 0) {
    $titleBlock->addCrumbDelete('LBL_SP_DELETE', $canDelete, $msg);
}
$titleBlock->show();

$q = new DBQuery();
$q->addQuery('*');
$q->addTable('scope_requirements', 's');
$q->addWhere('req_id =' . $req_id);
$q->addJoin('projects', 'p', 'p.project_id = s.project_id');
$q->addJoin('scope_requirement_categories', 'c', 's.req_categ_prefix_id = c.req_categ_prefix_id');
$q->addOrder('p.project_id');

// check if this record has dependancies to prevent deletion
$msg = '';
$obj = new CScopeRequirements();
$canDelete = $obj->canDelete($msg, $req_id);

// load the record data
$obj = null;
if (!db_loadObject($q->prepare(), $obj) && $req_id > 0) {
    $AppUI->setMsg('LBL_SP_REQ');
    $AppUI->setMsg("invalidID", UI_MSG_ERROR, true);
    $AppUI->redirect();
}

$projects = new DBQuery();
if ($project_id == null) {
    $projects->addQuery('project_id, project_name');
    $projects->addTable('projects');
    $projects = $projects->loadHashList();
} else {
    $projects->addQuery('project_id, project_name');
    $projects->addTable('projects', 'p');
    $projects->addWhere('project_id = ' . $project_id);
    $projects->addOrder('project_id');
    $projects = $projects->loadHashList();
}

/* transform date to dd/mm/yyyy */
$start_date = intval($obj->req_inclusiondate) ? new CDate($obj->req_inclusiondate) : null;
$end_date = intval($obj->req_conclusiondate) ? new CDate($obj->req_conclusiondate) : null;
$df = $AppUI->getPref('SHDATEFORMAT');

//load the category's kind list
$categoriesList = new DBQuery();
$categoriesList->addQuery('r.req_categ_prefix_id, r.req_categ_name');
$categoriesList->addTable('scope_requirement_categories', 'r');
$categoriesList = $categoriesList->loadList();

//load the priority's kind list
$prioritiesList = new DBQuery();
$prioritiesList->addQuery('p.req_priority_id');
$prioritiesList->addTable('scope_requirement_priorities', 'p');
$prioritiesList = $prioritiesList->loadList();

//load the status's kind list
$statusList = new DBQuery();
$statusList->addQuery('s.req_status_id');
$statusList->addTable('scope_requirement_status', 's');
$statusList = $statusList->loadList();
?>

<!-- import the calendar script -->
<script type="text/javascript" src="<?php echo DP_BASE_URL; ?>/lib/calendar/calendar.js"></script>
<!-- import the language module -->
<script type="text/javascript" src="<?php echo DP_BASE_URL; ?>/lib/calendar/lang/calendar-<?php echo $AppUI->user_locale; ?>.js"></script>

<script language="javascript">    
    var calendarField = '';
    var calWin = null;

    function popCalendar( field ){
        calendarField = field;
        idate = eval( 'document.uploadFrm.req_' + field + '.value' );
        window.open( 'index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'width=280, height=250, scrollbars=no' );
    }
    
    /**
     *	@param string Input date in the format YYYYMMDD
     *	@param string Formatted date
     */
    function setCalendar( idate, fdate ) {
        fld_date = eval( 'document.uploadFrm.req_' + calendarField );
        fld_fdate = eval( 'document.uploadFrm.' + calendarField );
        fld_date.value = idate;
        fld_fdate.value = fdate;
        
        // set end date automatically with start date if start date is after end date
        if (calendarField == 'req_conclusiondate') {
            if( document.uploadFrm.end_date.value < idate) {
                document.uploadFrm.req_conclusiondate.value = idate;
                document.uploadFrm.end_date.value = fdate;
            }
        }
    }
    
    function submitIt() {
        var f = document.uploadFrm;
        var msg = '';
        if(f.req_idname.value.length<1){
            msg = "<?php echo $AppUI->_('LBL_SP_CONFIRMMESSAGE_REQUIREDFIELDS'); ?>";
        }
        if(f.req_description.value.length<1){
            msg = "<?php echo $AppUI->_('LBL_SP_CONFIRMMESSAGE_REQUIREDFIELDS'); ?>";
        }
        if(f.req_source.value.length<1){
            msg = "<?php echo $AppUI->_('LBL_SP_CONFIRMMESSAGE_REQUIREDFIELDS'); ?>";
        }
        if(f.req_owner.value.length<1){
            msg = "<?php echo $AppUI->_('LBL_SP_CONFIRMMESSAGE_REQUIREDFIELDS'); ?>";
        }
        if(msg.length<1){
            f.submit();
        } else {
            alert(msg);
        }
    }    
    
    function delIt() {
        if (confirm("<?php echo $AppUI->_('LBL_SP_CONFIRMMESSAGE_DELETION', UI_OUTPUT_JS); ?>")) {
            var f = document.uploadFrm;
            f.del.value='1';
            f.submit();
        }
    }      
</script>

<table align="left" border="0" cellpadding="10" cellspacing="5" style="width: 100%" class="std" name="threads" charset=UTF-8>
    <form name="uploadFrm" action="?m=scopeplanning" method="post">
        <input type="hidden" name="dosql" value="do_scopeplanning_requirement_aed" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="req_id" value="<?php echo $req_id; ?>"/>
                               
        <tr><td align="left" nowrap="nowrap">*<?php echo $AppUI->_("LBL_SP_PROJECT"); ?>:</td>
            <td>
                <?php
                echo arraySelect($projects, 'project_id', 'size="1" class="text"', (@$obj->project_id ? dPformSafe(@$obj->project_id) : $project_id));
                ?>
            </td>
        </tr>              
        <tr>
            <td valign="top">*<?php echo $AppUI->_('LBL_SP_REQ'); ?>:</td>
            <td><input maxlength="6" name="req_idname" size="8" type="text" value="<?php echo dPformSafe(@$obj->req_idname); ?>"/>
            </td>
        </tr>
        <tr>
            <td valign="top">*<?php echo $AppUI->_('LBL_SP_DESCRIPTION'); ?>:</td>
            <td><textarea cols="100" name="req_description" class="std" rows="10"><?php echo dPformSafe(@$obj->req_description); ?></textarea></td>
        </tr>
        <tr>
            <td valign="top">*<?php echo $AppUI->_('LBL_SP_SOURCE'); ?>:</td>
            <td><input maxlength="60" name="req_source" size="65" type="text" value="<?php echo dPformSafe(@$obj->req_source); ?>"/>
            </td>
        </tr>
        <tr>
            <td valign="top">*<?php echo $AppUI->_('LBL_SP_OWNER'); ?>:</td>
            <td><input maxlength="60" name="req_owner" size="65" type="text" value="<?php echo dPformSafe(@$obj->req_owner); ?>"/>
            </td>
        </tr>
        <tr>
            <td valign="top">*<?php echo $AppUI->_('LBL_SP_CATEGORY'); ?>:</td>
            <td><select name="req_categ_prefix_id" class="combo" style="width: 230px">
                    <?php
                    foreach ($categoriesList as $reg) {
                        $value = @$obj->req_categ_name;
                        echo '<option value="' . $reg['req_categ_prefix_id'] . '" ' . ($reg['req_categ_name'] == $value ? 'selected="selected"' : '') . '>' . $reg['req_categ_name'] . '</option>';
                    }
                    ?>                                           
                </select>                                                             
            </td>
        </tr>
        <tr>
            <td valign="top">*<?php echo $AppUI->_('LBL_SP_PRIORITY'); ?>:</td>
            <td><select name="req_priority_id" class="combo" style="width: 230px">
                    <?php
                    foreach ($prioritiesList as $reg) {
                        $value = @$obj->req_priority_id;
                        echo '<option value="' . $reg['req_priority_id'] . '" ' . ($reg['req_priority_id'] == $value ? 'selected="selected"' : '') . '>' . $reg['req_priority_id'] . '</option>';
                    }
                    ?>                                        
                </select>
            </td>
        </tr>
        <tr>
            <td valign="top">*<?php echo $AppUI->_('LBL_SP_STATUS'); ?>:</td>
            <td><select name="req_status_id" class="combo" style="width: 230px">
                    <?php
                    foreach ($statusList as $reg) {
                        $value = @$obj->req_status_id;
                        echo '<option value="' . $reg['req_status_id'] . '" ' . ($reg['req_status_id'] == $value ? 'selected="selected"' : '') . '>' . $reg['req_status_id'] . '</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td valign="top"><?php echo $AppUI->_('LBL_SP_VERSION'); ?>:</td>
            <td><input maxlength="20" name="req_version" class="std" size="25" type="text" value="<?php echo dPformSafe(@$obj->req_version); ?>"/>
            </td>
        </tr>
        <tr>
            <td align="left" nowrap="nowrap">*<?php echo $AppUI->_('LBL_SP_INCLUSIONDATE'); ?>:</td>
            <td>
                <input type="hidden" name="req_inclusiondate" id="req_inclusiondate"  value="<?php echo (($start_date) ? $start_date->format(FMT_TIMESTAMP_DATE) : ''); ?>"/>
                <!-- format(FMT_TIMESTAMP_DATE) -->
                <input type="text" class="std" name="inclusiondate" id="inclusiondate" value="<?php echo (($start_date) ? $start_date->format($df) : ''); ?>" disabled="disabled"/>
                                
                <a href="#" onclick="popCalendar( 'inclusiondate', 'inclusiondate');">
                    <img src="./images/calendar.gif" width="24" height="12" alt="{dPtranslate word='Calendar'}" border="0" />
                </a>
            </td>
        </tr>        
        <tr>
            <td align="left" nowrap="nowrap"><?php echo $AppUI->_('LBL_SP_CONCLUSIONDATE'); ?>:</td>
            <td>
                <input type="hidden" name="req_conclusiondate" id="req_conclusiondate"  value="<?php echo (($end_date) ? $end_date->format(FMT_TIMESTAMP_DATE) : ''); ?>"/>
                <!-- format(FMT_TIMESTAMP_DATE) -->
                <input type="text" class="std" name="conclusiondate" id="conclusiondate" value="<?php echo (($end_date) ? $end_date->format($df) : ''); ?>" disabled="disabled"/>
                                
                <a href="#" onclick="popCalendar( 'conclusiondate', 'conclusiondate');">
                    <img src="./images/calendar.gif" width="24" height="12" alt="{dPtranslate word='Calendar'}" border="0" />
                </a>
            </td>
        </tr>
        <tr>
            <td><input name="btn_cancel" class="button" type="button" value="<?php echo $AppUI->_('LBL_SP_CANCEL'); ?>"
                       onclick="{location.href = './index.php?m=scopeplanning';}"/></td>
            <td style="text-align: right"><input name="btn_submit" class="button" type="button" value="<?php echo $AppUI->_('LBL_SP_SAVE'); ?>" onclick="submitIt()"/></td>
        </tr>
    </form>
</table>

