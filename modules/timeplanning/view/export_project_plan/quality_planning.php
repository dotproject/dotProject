<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/control/quality/controller_quality_planning.class.php");
$controllerQ = new ControllerQualityPlanning();
$objectQ = $controllerQ->getQualityPlanningPerProject($projectId);
$quality_planning_id = $objectQ->getId();
?>
<!--
<table class="printTable" >  
    <tr>
        <th style="font-weight: bold"><?php echo $AppUI->_("LBL_QUALITY_POLICIES", UI_OUTPUT_HTML); ?>:</th>
    </tr>
    <tr>
        <td>
-->
<h3>4.1 <?php echo $AppUI->_("LBL_QUALITY_POLICIES", UI_OUTPUT_HTML); ?> </h3>
    <p class="print_p">
        <?php 
           $qualityPolicies=$objectQ->getQualityPolicies();
           $qualityPolicies =str_replace( "\n", "<br />",$qualityPolicies  );
           //$qualityPolicies =preg_replace( "/\r|\n/", "<br />",$qualityPolicies  );
           //caracteres não interpretados pelo pdf generator
           $qualityPolicies =str_replace( "–", "-", $qualityPolicies );
           echo $qualityPolicies;
        ?>
    </p>
<!--
        </td>
    </tr>
</table>
-->
<h3> 4.2 <?php echo $AppUI->_("LBL_QUALITY_ASSURANCE", UI_OUTPUT_HTML); ?> </h3>
<table class="printTable" > 
    
    <tr>
        
        <th><?php echo $AppUI->_("LBL_WHAT_AUDIT", UI_OUTPUT_HTML); ?></th>
        <th><?php echo $AppUI->_("LBL_WHO_AUDIT", UI_OUTPUT_HTML); ?></th> 
        <th><?php echo $AppUI->_("LBL_WHEN_AUDIT", UI_OUTPUT_HTML); ?></th> 
        <th><?php echo $AppUI->_("LBL_HOW_AUDIT", UI_OUTPUT_HTML); ?></th> 
        
    </tr>
    <?php
    $assuranceItems = $controllerQ->loadAssuranceItems($quality_planning_id);
    $i = 0;
    foreach ($assuranceItems as $id => $data) {
        $ai_id = $data[0];
        $what = $data[1];
        $who = $data[2];
        $when = $data[3];
        $how = $data[4];
        ?>
        <tr>
            <td valign="top">
                <?php echo $what; ?>
            </td>
            <td valign="top">
                <?php echo $who; ?>
            </td>
            <td valign="top">
                <?php echo $when; ?>
            </td>
            <td valign="top">
                <?php echo $how; ?>
            </td>
        </tr>  
        <?php
        $i++;
    }
    ?>
</table>
<h3>4.3 <?php echo $AppUI->_("LBL_QUALITY_CONTROLLING", UI_OUTPUT_HTML); ?> </h3>
<table class="printTable">
    <tr>
        <th><?php echo $AppUI->_("LBL_QUALITY_REQUIREMENTS", UI_OUTPUT_HTML); ?></th>
    </tr>
    <?php
    $requirements = $controllerQ->loadControlRequirements($quality_planning_id);

    $r = 0;

    foreach ($requirements as $id => $data) {
        $req_id = $data[0];
        $description = $data[1];
        ?>
        <tr>
            <td>
                <?php echo ($r + 1) ?>. &nbsp;<?php echo $AppUI->_($description, UI_OUTPUT_HTML); ?>
            </td>
        </tr>
        <?php
        $r++;
    }
    ?>
</table>

<?php
$goals = $controllerQ->loadControlGoals($quality_planning_id);
$i = 0;
foreach ($goals as $oid => $data) {
    $goal_id = $data[0];
    $gqm_goal_propose = $data[1];
    $gqm_goal_object = $data[2];
    $gqm_goal_respect_to = $data[3];
    $gqm_goal_point_of_view = $data[4];
    $gqm_goal_context = $data[5];
    ?>
    <table class="printTable">
        <tr>
            <th colspan="2"> <?php echo ($i+1) ?>. <?php echo $AppUI->_("LBL_GOAL_OF_CONTROL", UI_OUTPUT_HTML); ?></th>
        </tr>
        <tr>
            <td style="font-weight: bold;width:50%">
                <label for="gqm_goal_object">
                   <?php echo $AppUI->_("LBL_GQM_GOAL_OBJECT", UI_OUTPUT_HTML); ?>
                </label>
            </td>
            <td style="width:50%">
                <?php echo $gqm_goal_object; ?>
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold">
                <label for="gqm_goal_propose">
                    <?php echo $AppUI->_("LBL_GQM_GOAL_PURPOSE", UI_OUTPUT_HTML); ?>
                </label>
            </td>

            <td style="">
                <?php echo $gqm_goal_propose; ?>
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold">
                <label for="gqm_goal_respect_to">
                   <?php echo $AppUI->_("LBL_GQM_GOAL_RESPECT_TO", UI_OUTPUT_HTML); ?>
                </label>
            </td>

            <td>
                <?php echo $gqm_goal_respect_to; ?>
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold">
                <label for="gqm_goal_point_of_view">
                     <?php echo $AppUI->_("LBL_GQM_GOAL_POINT_OF_VIEW", UI_OUTPUT_HTML) ?>
                </label>
            </td>
 
            <td>
                <?php echo $gqm_goal_point_of_view; ?>
            </td>
        </tr>

        <tr>
            <td style="font-weight: bold">
                <label for="gqm_goal_context">
                    <?php echo $AppUI->_("LBL_GQM_GOAL_CONTEXT", UI_OUTPUT_HTML); ?>
                </label>
            </td>
 
            <td >
                <?php echo $gqm_goal_context; ?>
            </td>
        </tr>
    </table>

    <?php
    $questions = $controllerQ->loadQuestions($goal_id);
    $j = 0;
    foreach ($questions as $qid => $qdata) {
        $question_id = $qdata[0];
        $question = $qdata[1];
        $target = $qdata[2];
        ?>

        <table class="printTable" >
            <tr>
                <th style="font-weight: bold;vertical-align: top"><?php echo ($i+1).".".($j+1)  ?>. <?php echo $AppUI->_("LBL_QUESTION_OF_ANALYSIS", UI_OUTPUT_HTML); ?></th>
                <th style="font-weight: bold;vertical-align: top"><?php echo $AppUI->_("LBL_GQM_TARGET", UI_OUTPUT_HTML); ?></th>
            </tr>

            <tr>
                <td style="vertical-align: top">
                    <?php echo $question ?>
                </td>
                <td style="vertical-align: top">
                    <?php echo $target ?>
                </td>
            </tr>
        </table>

        <table class="printTable" >
             <tr>
                <th colspan="2" style="text-align: center"><?php echo $AppUI->_("LBL_GQM_METRIC", UI_OUTPUT_HTML); ?>s</th>
            </tr>
            <tr>
                <td style="font-weight: bold;vertical-align: top" width="50%">
                    <?php echo $AppUI->_("LBL_GQM_METRIC", UI_OUTPUT_HTML); ?>
                </td>
                <td style="font-weight: bold;vertical-align: top" width="50%">
                    <?php echo $AppUI->_("LBL_GQM_HOW_DATA_IS_COLLECTED", UI_OUTPUT_HTML); ?>
                </td>
            </tr>

            <?php
            $metrics = $controllerQ->loadMetrics($question_id);
            $k = 0;
            foreach ($metrics as $mid => $mdata) {
                $metric_id = $mdata[0];
                $metric = $mdata[1];
                $how_to_collect = $mdata[2];
                ?>           
                <tr>
                    <td style="vertical-align: top;">
                        <?php echo ($i+1).".".($j+1).".". ($k+1)  ?>.<?php echo $metric; ?>
                    </td>
                    <td style="vertical-align: top; ">
                        <?php echo $how_to_collect; ?>
                    </td>
                </tr>
                <?php
                $k++;
            }
            ?>
        </table> 
        <?php
        $j++;
    }
    ?>
    <?php
    $i++;
}
?>
