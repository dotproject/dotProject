<?php $actionEstimation = $_GET['action_estimation']; ?>
<a name="minute_estimation_form"></a>
<div id="ata_div" width="100%" style="display:<?php echo $actionEstimation == "read" ? 'block' : 'none'; ?>">
    <!-- TinyMCE -->
    <script type="text/javascript" src="./modules/timeplanning/js/jsLibraries/tiny_mce/tiny_mce.js"></script>
    <script type="text/javascript">
        tinyMCE.init({
            mode: "textareas",
            theme: "advanced",
            width: "90%",
            height: "240",
            theme_advanced_buttons1: "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
            theme_advanced_buttons2: "search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
            theme_advanced_buttons3: "hr,removeformat,sub,sup,charmap"
        });
    </script>
    <!-- /TinyMCE -->
    <!-- calendar goodies -->
    <link type="text/css" rel="stylesheet" href="./modules/timeplanning/js/jsLibraries/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen"></LINK>
    <SCRIPT type="text/javascript" src="./modules/timeplanning/js/jsLibraries/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>

    <!-- ajax caller -->
    <script type="text/javascript" src="./modules/timeplanning/js/serviceActivator.js"></script>

    <table class="std" align="center" width="95%" >			
        <tr>
            <td nowrap><?php echo $AppUI->_('LBL_TYPE'); ?>:</td>
            <td nowrap>
                <input type="checkbox" name="isEffort" value="1" <?php echo $isEffort == "" ? "" : "checked"; ?>><?php echo $AppUI->_('LBL_EFFORT'); ?>  &nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox" name="isDuration" value="1" <?php echo $isDuration == "" ? "" : "checked"; ?>><?php echo $AppUI->_('LBL_DURATION'); ?>   &nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox" name="isResource" value="1" <?php echo $isResource == "" ? "" : "checked"; ?>><?php echo $AppUI->_('LBL_RESOURCES'); ?>
                <input type="checkbox" name="isSize" value="1" <?php echo $isSize == "" ? "" : "checked"; ?>><?php echo $AppUI->_('LBL_SIZE'); ?>
            </td>	
        </tr>
        <tr>
            <td nowrap><?php echo $AppUI->_('LBL_DATE'); ?>:</td>
            <td nowrap >
                <input type="hidden" name="date" id="date">
                <input type="text" class="text" disabled name="date_edit" value="<?php echo $date; ?>" id="date_edit">
                <img src="./modules/timeplanning/images/img.gif" id="calendar_trigger" style="cursor:pointer" onclick="displayCalendar(document.getElementById('date_edit'), 'dd/mm/yyyy', this)" />
            </td>
        </tr>
        <tr>
            <td nowrap valign="top" colspan="2">
                <?php
                $project_id = dPgetParam($_GET, 'project_id', 0);
                $all_users = $controllerProjectMinute->getAllProjectStakeholders($project_id);
                //update allocated users list with his names
                if (!is_array($members)) {
                    $members = array();
                }
                foreach ($members as $member) {
                    $key = $member . ""; //make key as string
                    $members[$key] = $all_users[$key];
                }
                //clear any empty option
                for ($i = 0; $i < sizeof($members); $i++) {
                    if ($members[$i] == "") {
                        unset($members[$i]);
                    }
                }
                //remove from all users array the users already allocated
                $all_users = array_diff($all_users, $members);
                ?>
                         
                <table width="100%"  cellpadding="4" cellspacing="0" >
                    <tr>
                        <td valign="top" align="center">
                            <table cellspacing="0" cellpadding="2" border="0">
                                <tr>
                                    
                                    <td valign="top">
                                        <fieldset style="border: 0px solid black;">
                                            <legend><?php echo $AppUI->_('LBL_ALL_MEMBERS'); ?></legend>
                                            <?php echo arraySelect($all_users, 'all_members', 'style="width:290px;" size="10" class="text"  ', null); ?>
                                        </fieldset>
                                        <br/>
                                    </td>
                                    <td valign="top"> 
                                        <fieldset style="border: 0px solid black;">
                                            <legend><?php echo $AppUI->_('LBL_PARTICIPANTS'); ?></legend>
                                            <?php echo arraySelect($members, 'selected_members', 'style="width:290px" size="10" class="text" id="selected_members" ', null); ?>			
                                        </fieldset>
                                    </td>
                                    <td valign="top" width="80" style="font-size: 10px;color:black;">
                                        <br /><br />
                                        <span style="color:red">*</span> 
                                        <?php echo $AppUI->_("LBL_ESTIMATION_MINUTE_MISSING_MEMBERS",UI_OUTPUT_HTML); ?>
                                    </td>
                                    
                                </tr>
                                
                                <tr>
                                    <td colspan="2" align="center">
                                        <table>
                                            <tr>
                                                <td align="right"><input type="button" class="button" value="&gt;" onclick="addMember('minute_form', 'all_members', 'selected_members')" /></td>	
                                                <td align="left"><input type="button" class="button" value="&lt;" onclick="removeMember('minute_form', 'all_members', 'selected_members')" /></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>

        <tr>
            <td valign="top" nowrap colspan="2" align="center">
                <?php echo $AppUI->_('LBL_REPORT'); ?><br>
                <input id="description" name="description" type="text" style="display:none" >
                <textarea id="description_edit" name="description_edit" rows="25" cols="80" ><?php echo $description; ?></textarea>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="right">
                <input type="button" name="Salvar" value="<?php echo $AppUI->_('LBL_SAVE'); ?>" onclick=saveReport() class="button">
                <input type="button" name="Fechar" value="<?php echo ucfirst($AppUI->_('LBL_CANCEL')); ?>" onclick=closeReport() class="button">
            </td>
        </tr>
    </table>
</div>