<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>dotProject+</title>
        <meta http-equiv="Content-Type" content="text/html;charset=<?php echo isset($locale_char_set) ? $locale_char_set : 'UTF-8'; ?>" />
        <meta http-equiv="Pragma" content="no-cache" />
        <meta name="Version" content="<?php echo @$AppUI->getVersion(); ?>" />
        <link rel="stylesheet" type="text/css" href="./style/<?php echo $uistyle; ?>/main.css" media="all" />
        <style type="text/css" media="all">
            @import "./style/<?php echo $uistyle; ?>/main.css";
        </style>
        <link rel="shortcut icon" href="./style/<?php echo $uistyle; ?>/img/favicon.ico" type="image/ico" />
    </head>
    <body bgcolor="#f0f0f0" onload="document.loginform.username.focus();" style="text-align: center">

        <br />
        
        <br />
        <br />
        <br />
        <div class="company_name" style="padding:0px;"><a href="<?php echo $dPconfig['base_url'] ?>"><?php echo $dPconfig['company_name'] ?></a></div>
        <br />
        <form method="post" action="<?php echo $loginFromPage; ?>" name="loginform">
            <table cellpadding="0" cellspacing="0" border="0" width="300px" align="center">
                <tr>
                    <td><img src="style/<?php echo $uistyle; ?>/img/tl.png" /></td>
                    <td bgcolor="#ffffff" height="8px"></td>
                    <td><img src="style/<?php echo $uistyle; ?>/img/tr.png" /></td>
                </tr>
                <tr>
                    <td bgcolor="#ffffff" width="8px"></td>
                    <td bgcolor="#ffffff">
                        <img src="style/<?php echo $uistyle; ?>/img/dotproject_plus_logo.jpg" style="width: 170px;height: 50px" />
                        <table align="center" border="0" width="250" cellpadding="6" cellspacing="0" class="std">
                            <input type="hidden" name="login" value="<?php echo time(); ?>" />
                            <input type="hidden" name="lostpass" value="0" />
                            <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
                            <tr>
                                <td align="right" nowrap><?php echo $AppUI->_('Username'); ?>:</td>
                                <td align="left" nowrap><input type="text" size="25" maxlength="20" name="username" class="text" /></td>
                            </tr>
                            <tr>
                                <td align="right" nowrap><?php echo $AppUI->_('Password'); ?>:</td>
                                <td align="left" nowrap><input type="password" size="25" maxlength="32" name="password" class="text" /></td>
                            </tr>
                            <tr>
                                <td align="left" nowrap></td>
                                <td align="right" valign="bottom" nowrap><input type="submit" name="login" value="<?php echo $AppUI->_('login'); ?>" class="button" /></td>
                            </tr>
                            <tr>
                                <td colspan="2" align="center"><a href="#" onclick="f = document.loginform;
                                        f.lostpass.value = 1;
                                        f.submit();"><?php echo $AppUI->_('forgotPassword'); ?></a></td>
                            </tr>
                        </table>
                        <?php if (@$AppUI->getVersion()) { ?>
                            <div align="center"><span style="font-size:7pt">Version <?php echo @$AppUI->getVersion(); ?></span></div>
                        <?php } ?>


                        <div align="center" style="font-size:9px;"><?php
                            echo '<span class="error">' . $AppUI->getMsg() . '</span>';
                            $msg = '';
                            $msg .= phpversion() < '4.1' ? '<br /><span class="warning">WARNING: dotproject is NOT SUPPORT for this PHP Version (' . phpversion() . ')</span>' : '';
                            $msg .= function_exists('mysql_pconnect') ? '' : '<br /><span class="warning">WARNING: PHP may not be compiled with MySQL support.  This will prevent proper operation of dotProject.  Please check you system setup.</span>';
                            echo $msg;
                            ?></div>

                        <div style="font-size:10px;color:#999999; width:250px;text-align: center">
                            <?php echo "* " . $AppUI->_("You must have cookies enabled in your browser"); ?>
                        </div>
                    </td>
                    <td bgcolor="#FFFFFF" width="8px"></td>
                </tr>
                <tr>
                    <td><img src="style/<?php echo $uistyle; ?>/img/bl.png" /></td>
                    <td bgcolor="#FFFFFF"></td>
                    <td><img src="style/<?php echo $uistyle; ?>/img/br.png" /></td>

                </tr>
            </table>
    </body>
</html>