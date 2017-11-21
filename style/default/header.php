<?php /* STYLE/DEFAULT $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly');
}
$dialog = (int)dPgetParam($_GET, 'dialog', 0);
if ($dialog)
	$page_title = '';
else
	$page_title = ($dPconfig['page_title'] == 'dotProject') ? $dPconfig['page_title'] . '&nbsp;' . $AppUI->getVersion() : $dPconfig['page_title'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
        <meta name="Description" content="dotProject Default Style" />
        <meta name="Version" content="<?php echo @$AppUI->getVersion(); ?>" />
        <meta http-equiv="Content-Type" content="text/html;charset=<?php echo isset($locale_char_set) ? $locale_char_set : 'UTF-8'; ?>" />
        <title><?php echo @dPgetConfig('page_title'); ?></title>
        <link rel="stylesheet" type="text/css" href="./style/<?php echo $uistyle; ?>/main.css" media="all" />
        <style type="text/css" media="all">@import "./style/<?php echo $uistyle; ?>/main.css";</style>
        <link rel="shortcut icon" href="./style/<?php echo $uistyle; ?>/images/favicon.ico" type="image/ico" />
        <?php @$AppUI->loadJS(); ?>
        <style type="text/css" media="all">@import "./style/<?php echo $uistyle; ?>/dropdown_menu.css";</style>
        <style>
            .label_dpp{
                text-align: left;
                font-weight: bold;
            }
        </style>
    </head>
    <body onload="this.focus();">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td>
                    <table width="100%" cellpadding="3" cellspacing="0" border="0">
                        <tr style="height: 30px">
                            <th style="background: url('style/<?php echo $uistyle; ?>/images/titlegrad.jpg');text-align: center" class="banner">
                                <span style="font-size: 20px;color:darkblue"><?php for($k=0;$k<20;$k++){echo "&nbsp;";} ?>
                                      <img src="style/<?php echo $uistyle; ?>/images/dotproject_plus_logo_header.png" style="width: 150px;height: 40px" />    
                                </span>
                                <span style="float:right;color:#000000" >
                                    <?php //echo dPcontextHelp('Help');?> 

                                    <span id='cssmenu'style="display: inline-block" >
                                        <ul>
                                            <li class='active has-sub'>


                                                <u style="cursor:pointer"><?php echo $AppUI->user_first_name . ' ' . $AppUI->user_last_name; ?>
                                                    <img style="vertical-align: bottom" src="./style/<?php echo $uistyle; ?>/images/arrow_down.png" />

                                                </u>

                                                <ul>
                                                    <li>

                                                        <a href="./index.php?m=admin&amp;a=viewuser&amp;user_id=<?php echo $AppUI->user_id; ?>">
                                                            <img src="./style/<?php echo $uistyle; ?>/images/icon_my_data.png" />&nbsp;
                                                            Meus dados
                                                        </a>
                                                    </li>

                                                    <?php
                                                    if (getPermission('calendar', 'access')) {
                                                        $now = new CDate();
                                                        ?>                              
                                                        <li>                               
                                                            <a href="./index.php?m=calendar&amp;a=day_view&amp;date=<?php echo $now->format(FMT_TIMESTAMP_DATE); ?>">
                                                                <img src="./style/<?php echo $uistyle; ?>/images/icon_schedule.png" />&nbsp;
                                                                Agenda
                                                            </a>
                                                        </li>
                                                    <?php } ?>

                                                    <li class='last'>
                                                        <a href="?logout=-1">
                                                            <img src="./style/<?php echo $uistyle; ?>/images/icon_logout.png" />&nbsp;                                                        
                                                            Sair
                                                        </a>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>

                                    </span>
                                    <a href="http://www.gqs.ufsc.br/evolution-of-dotproject/" target="blank">
                                        <img style="cursor:pointer" src="./style/<?php echo $uistyle; ?>/images/icon_help.png" /> 
                                    </a>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                </span>
                            </th>
                        </tr>
                    </table>
                </td>
            </tr>
            <?php
            if (!$dialog) {
                // top navigation menu
                $nav = $AppUI->getMenuModules();
                ?>
                <tr>
                    <td class="nav" align="left">
                        <table width="100%" cellpadding="3" cellspacing="0">
                            <tr>
                                <td>
                                    <?php
                                    $links = array();
                                    foreach ($nav as $module) {
                                        if (getPermission($module['mod_directory'], 'access')) {
                                            $links[] = '<a href="?m=' . $module['mod_directory'] . '">' . $AppUI->_($module['mod_ui_name']) . '</a>';
                                        }
                                    }
                                    echo implode(' | ', $links);
                                    echo "\n";
                                    ?>
                                </td>
                                <td nowrap="nowrap" align="right">
                                    <form name="frm_new" method="get" action="./index.php">
                                        <table cellpadding="0" cellspacing="0">
                                            <tr><td>

                                                    <?php
                                                    /**
                                                      $newItemPermCheck = array('companies' => 'Company',
                                                      'contacts' => 'Contact',
                                                      'calendar' => 'Event',
                                                      'files' => 'File',
                                                      'projects' => 'Project');

                                                      $newItem = array(0=>'- New Item -');
                                                      foreach ($newItemPermCheck as $mod_check => $mod_check_title) {
                                                      if (getPermission($mod_check, 'add')) {
                                                      $newItem[$mod_check] = $mod_check_title;
                                                      }
                                                      }

                                                      echo arraySelect($newItem, 'm', 'style="font-size:10px" onChange="javascript:f=document.frm_new;mod=f.m.options[f.m.selectedIndex].value;if (mod) f.submit();"', '', true);
                                                     * */
                                                    echo "<input type=\"hidden\" name=\"a\" value=\"addedit\" />\n";

//build URI string
                                                    if (isset($company_id)) {
                                                        echo '<input type="hidden" name="company_id" value="' . $company_id . '" />';
                                                    }
                                                    if (isset($task_id)) {
                                                        echo '<input type="hidden" name="task_parent" value="' . $task_id . '" />';
                                                    }
                                                    if (isset($file_id)) {
                                                        echo '<input type="hidden" name="file_id" value="' . $file_id . '" />';
                                                    }
                                                    ?>
                                                </td></tr>
                                        </table>
                                    </form>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            <?php } // END showMenu  ?>
        </table>
        <!-- Aplication message panel -->
        <?php
        $app_message = $AppUI->getMsg();
        ?>   
        <table align="center" width="400" id="app_message_panel" style="border:1px solid silver;background-color: #FFFFFF;padding: 5px 5px;margin-top: 5px;display:<?php echo $app_message != "" ? "block" : "none" ?>">
            <tr>
                <td>
                    <span style="text-align: center;" id="app_message_panel_content">
                        <?php echo $app_message; ?>
                    </span>
                </td>
            </tr>
        </table>
        <script>
            //function defined to set messages on the panel. It was setup in this file, to be available to to whole application
            //These threee constants below represents the types of message, and are identical to the image file name.
            var APP_MESSAGE_TYPE_SUCCESS="sucess";
            var APP_MESSAGE_TYPE_WARNING="warning";
            var APP_MESSAGE_TYPE_INFO="info";
            function setAppMessage(message,type){
                var app_message_panel=document.getElementById("app_message_panel");
                var img="<img src='./style/<?php echo $uistyle; ?>/images/icon_"+type+".png' />";
                document.getElementById("app_message_panel_content").innerHTML=img + "&nbsp;&nbsp;" + message;
                app_message_panel.style.display="block";
                app_message_panel.scrollIntoView();
                setTimeout(closeAppMessage, 15000);//close the message panel after x seconds.
            }
            function closeAppMessage(){
                document.getElementById("app_message_panel").style.display="none";
            }
        </script>
        <!-- end message panel -->
        <table width="100%" cellspacing="0" cellpadding="4" border="0">
            <tr>
                <td valign="top" align="left" width="98%">

