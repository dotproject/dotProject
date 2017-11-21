<?php
require_once (DP_BASE_DIR . "/modules/dotproject_plus/feedback/feedback.php");
require_once (DP_BASE_DIR . "/modules/dotproject_plus/feedback/feedback_controller.php");

    ?>
<!-- CSS for switch button -->
<link rel="stylesheet" type="text/css" href="./modules/dotproject_plus/feedback/switchbutton.css" /> 
    <span id='cssmenu'style="display: inline-block; margin-right: 13px;" >
        <ul>
            <li class='active has-sub'>
                <u style="text-decoration: none">
                    <img style="cursor:pointer;position: relative;height: 22px;top: 3px" src="./style/dotproject_plus/img/bell_icon.png" /> 
                    <div style="display:inline-block;position: relative;left:-12px;top: 6px; background-color: red;color:#FFFFFF; border-radius: 25px;" id="feedback_count"></div>
                </u>
                <ul style="border:1px solid silver; right:10px;margin-top: -1px;">
                    <br />
                    <b>::<?php echo $AppUI->_("LBL_FEEDBACK_INSTRUCTIONAL_FEEDBACK"); ?>::</b>
                    <br /><br />
                    <form method="post" action="?m=dotproject_plus" name="feedback_preferences">
                        <input name="dosql" type="hidden" value="do_save_feedback_preferences" />
                        <input type="hidden" name="url" value="<?php echo substr($_SERVER["REQUEST_URI"], strrpos($_SERVER["REQUEST_URI"], "?") + 1, strlen($_SERVER["REQUEST_URI"])); ?>" />
                        
                            <?php echo $AppUI->_("LBL_SEE_GENERIC_FEEDBACK"); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br />
                         

                            <div class="onoffswitch" >
                                 <input align="center" value="1" type="checkbox" name="generic_feedback" onclick="document.feedback_preferences.submit()" class="onoffswitch-checkbox" id="onoffswitch_generic" <?php echo $_SESSION["user_generic_feedback"] == 1 ? "checked" : "" ?>  />
                                 <label align="center" class="onoffswitch-label" for="onoffswitch_generic">
                                     <span class="onoffswitch-inner"></span>
                                     <span class="onoffswitch-switch"></span>
                                 </label>
                             </div>

                            <br/>


                            <?php echo $AppUI->_("LBL_SEE_SPECIFIC_FEEDBACK"); ?><br />
                         
                            <div class="onoffswitch" >
                                 <input align="center" value="1"  type="checkbox" name="especific_feedback" onclick="document.feedback_preferences.submit()" class="onoffswitch-checkbox" id="onoffswitch_especific" <?php echo $_SESSION["user_especific_feedback"] == 1 ? "checked" : "" ?>  />
                                 <label align="center" class="onoffswitch-label" for="onoffswitch_especific">
                                     <span class="onoffswitch-inner"></span>
                                     <span class="onoffswitch-switch"></span>
                                 </label>
                                </div>
                            <br />
                        
                    </form>
                    <hr />
                    <?php
                        $feedback_count = 0;
                        foreach ($_SESSION["user_feedback"] as $feedback_id) {
                            $feedback = $feedback_list[$feedback_id];


                            if (($feedback->getGeneric() && $_SESSION["user_generic_feedback"] == 1) || (!$feedback->getGeneric() && $_SESSION["user_especific_feedback"] == 1) ) {
                                $feedback_count++;
                                if ($feedback_count <= 5) {
                                    ?>     

                                    <li>
                                        <a  style="line-height: 120%" href="#" onclick="document.getElementById('show_feedback_<?php echo $feedback->getId() ?>').submit()">
                                            <form name="show_feedback_<?php echo $feedback->getId() ?>" id="show_feedback_<?php echo $feedback->getId() ?>" method="post" action="?m=dotproject_plus">
                                                <img src="./style/dotproject_plus/img/feedback/<?php echo InstructionalFeebackManager::getIconByKnowledgeArea($feedback->getKnowledgeArea()) ?>.png" style="width:20px; height: 20px" />
                                                <b><?php echo $feedback->getKnowledgeArea(); ?></b>
                                                <?php
                                                if (!$feedback->getGeneric()) {
                                                    ?>
                                                    &nbsp;&nbsp;&nbsp;<img src="./style/dotproject_plus/img/feedback/TCC_icon.png" style="width:20px; height: 20px" />

                                                    <?php
                                                }
                                                ?>
                                                <br />
                                                <?php echo $feedback->getShort(); ?>
                                                <input name="dosql" type="hidden" value="do_show_feedback" />

                                                <input type="hidden" name="feedback_id" value="<?php echo $feedback->getId() ?>" />
                                                <input type="hidden" name="url" value="<?php echo substr($_SERVER["REQUEST_URI"], strrpos($_SERVER["REQUEST_URI"], "?") + 1, strlen($_SERVER["REQUEST_URI"])); ?>" />
                                            </form>
                                        </a>
                                        <br />
                                    </li>
                                    <?php
                                }
                            }
                        }
                        if ($feedback_count>0){
                    ?>
                                <script>
                                    $("#feedback_count").html("&nbsp;<?php echo $feedback_count ?>&nbsp;");
                                </script>
                    <?php
                    }
                    ?>
                </ul>
            </li>
        </ul>
    </span>