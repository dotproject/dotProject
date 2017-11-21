<?php
require_once (DP_BASE_DIR . "/modules/timeplanning/control/quality/controller_quality_planning.class.php");
$controller = new ControllerQualityPlanning();
$object = $controller->getQualityPlanningPerProject($_GET["project_id"]);
$quality_planning_id = $object->getId();
?>
<a name="project_quality_planning"></a>
<style>
    textarea{
        width:80%;
        height: 150px;
        text-align: left;
    }
</style>
<script>
    function newAuditItem() {
        document.quality_form.form_action.value = 1;
        document.quality_form.submit();
    }

    function newRequirement() {
        document.quality_form.form_action.value = 2;
        document.quality_form.submit();
    }

    function newGoal() {
        document.quality_form.form_action.value = 3;
        document.quality_form.submit();
    }

    function newQuestion(goal_id_new_question) {
        document.quality_form.form_action.value = 4;
        document.quality_form.goal_id_new_question.value = goal_id_new_question;
        document.quality_form.submit();
    }

    function newMetric(question_id_new_metric) {
        document.quality_form.form_action.value = 5;
        document.quality_form.question_id_new_metric.value = question_id_new_metric;
        document.quality_form.submit();
    }

    function deleteRecord(action, id) {
        document.quality_form.form_action.value = action;
        document.quality_form.id_for_delete.value = id;
        document.quality_form.submit();
    }

</script>
<link href="modules/timeplanning/css/table_form.css" type="text/css" rel="stylesheet" />
<form name="quality_form" method="post" action="?m=timeplanning">
    <input name="dosql" type="hidden" value="do_project_quality_planning" />
    <input name="project_id" type="hidden" value="<?php echo $_GET["project_id"]; ?>" />
    <input name="form_action" type="hidden" value="" />
    <input name="goal_id_new_question" type="hidden" value="" />
    <input name="id_for_delete" type="hidden" value="" />
    <input name="question_id_new_metric" type="hidden" value="" /> 
    <input name="quality_planning_id" type="hidden" value="<?php echo $object->getId() ?>" />
    <br/>
    <table class="tbl" align="center" cellpadding="10" width="95%" style="border: 0px solid black">
        <!--
        <tr>
            <th  align="center" colspan="2"><?php echo $AppUI->_("LBL_QUALITY_PLANNING"); ?></th>
        </tr>

        <tr>
            <td class="td_label"><label for="quality_norms">Normas:</label></td>
            <td>
        
                <input type="text" name="quality_norms" style="width:97%;" />
            </td>
        </tr>

        <tr>
            <td class="td_label"><label for="quality_policies">Políticas:</label></td>
            <td>
                <input type="text" name="quality_policies" style="width:97%;" />
            </td>
        </tr>

        <tr>
            <td class="td_label"><label for="quality_guidelines">Diretrizes:</label></td>
            <td>
                <input type="text" name="quality_guidelines" style="width:97%;" />
            </td>
        </tr>
        -->

        <tr>
            <th colspan="2" style="font-weight: bold"><?php echo $AppUI->_("LBL_QUALITY_POLICIES"); ?>:</th>
        </tr>
        <tr>
            <td colpsan="2">
                <input type="hidden" name="quality_norms" value="" />
                <input type="hidden" name="quality_policies"  value="" />
                <textarea name="quality_policies" style="width:99%;height: 100px; resize: none;" ><?php echo $object->getQualityPolicies() ?></textarea>
            </td>

        </tr>


        <!-- Quality assurance -->
        <tr>
            <th colspan="2" style="font-weight: bold">
                <?php echo $AppUI->_("LBL_QUALITY_ASSURANCE"); ?>
        <div style="font-weight: normal;font-size: 9px">
            <span style="color:red">*</span><?php echo $AppUI->_("LBL_QUALITY_ASSURANCE_DESCRIPTION"); ?>
        </div>            
        </th>
        <!--
        <td nowrap>
            <textarea name="quality_assurance"><?php echo $object->getQualityAssurance() ?></textarea>
        </td>
        -->
        </tr>
        <tr>
            <td colspan="2">
                <br />
                <table width="100%" align="center">
                    <tr>
                        <td>
                            &nbsp;
                         <input type="button" value="<?php echo $AppUI->_("LBL_QUALITY_ADD_ITEM_TO_AUDIT"); ?>" onclick="newAuditItem()" />
                        </td>
                    </tr>
                </table>
                <br />
                <input name="number_audit_items" type="hidden" value="3"  /><!-- computed on page load - the count of entries on db when click to new: submission --> 
                <table class="tbl" style="border: 0px solid black;" width="100%" align="center" cellpadding="10">
                    <tr>
                        <th><?php echo $AppUI->_("LBL_WHAT_AUDIT"); ?></th>
                        <th><?php echo $AppUI->_("LBL_WHO_AUDIT"); ?></th> 
                        <th><?php echo $AppUI->_("LBL_WHEN_AUDIT"); ?></th> 
                        <th><?php echo $AppUI->_("LBL_HOW_AUDIT"); ?></th> 
                        <th style="width:4%">&nbsp;</th>
                    </tr>
                    <?php
                    $assuranceItems = $controller->loadAssuranceItems($quality_planning_id);
                    $i = 0;
                    foreach ($assuranceItems as $id => $data) {
                        $ai_id = $data[0];
                        $what = $data[1];
                        $who = $data[2];
                        $when = $data[3];
                        $how = $data[4];
                        ?>
                        <tr >
                            <td valign="top" >
                                <input name="audit_item_id_<?php echo $i ?>" value="<?php echo $ai_id; ?>" type="hidden" />
                                <textarea  type="text" name="what_audit_<?php echo $i ?>" style="width:97%;height: 38px;resize: none"><?php echo $what; ?></textarea>
                            </td>
                            <td valign="top" >
                                <textarea  type="text" name="who_audit_<?php echo $i ?>" style="width:97%;height: 38px;resize: none"><?php echo $who; ?></textarea>
                            </td>

                            <td valign="top" >
                                <textarea  type="text" name="when_audit_<?php echo $i ?>" style="width:97%;height: 38px;resize: none"><?php echo $when; ?></textarea>
                            </td>

                            <td valign="top">
                                <textarea name="how_audit_<?php echo $i ?>" style="width:97%;height: 38px;resize: none"><?php echo $how; ?></textarea>
                            </td>
                            <td style="vertical-align:top">
                                <img style="cursor:pointer" src="./modules/dotproject_plus/images/trash_small.gif" onclick="deleteRecord(10,<?php echo $ai_id ?>)" />
                            </td>
                        </tr>  
                        <?php
                        $i++;
                    }
                    ?>
                </table>
                <input type="hidden" name="number_audit_items" value="<?php echo $i ?>">
                <br /><br />
            </td>  
        </tr>

        <tr>
            <th colspan="2" style="font-weight: bold;">
                <?php echo $AppUI->_("LBL_QUALITY_CONTROLLING"); ?>     
        <div style="font-weight: normal;font-size: 9px">
            <span style="color:red">*</span>
            <?php echo $AppUI->_("LBL_QUALITY_CONTROL_DESCRIPTION")?>
        </div>
        </th>
        <!--
        <td nowrap>
            <textarea name="quality_controlling"><?php echo $object->getQualityControlling() ?></textarea>
        </td>	
        -->
        </tr>
        <tr>
            <td colspan="2">
                <br />
                &nbsp;
                <input type="button" value="<?php echo $AppUI->_("LBL_QUALITY_ADD_REQUIREMENT") ?>" onclick="newRequirement();" />
                <br /><br />
                <table width="100%" class="tbl" style="border: 0px solid black" cellpadding="10">
                    <th><?php echo $AppUI->_("LBL_QUALITY_REQUIREMENTS"); ?></th>
                    <th style="width:4%;vertical-align:top">&nbsp;</th>
                    <?php
                    $requirements = $controller->loadControlRequirements($quality_planning_id);
                    $i = 0;
                    foreach ($requirements as $id => $data) {
                        $req_id = $data[0];
                        $description = $data[1];
                        ?>
                        <tr>
                            <td>
                                <input name="requirement_id_<?php echo $i ?>" value="<?php echo $req_id; ?>" type="hidden" />
                                <?php echo $i + 1 ?>. <input name="requirement_<?php echo $i ?>" type="text" style="width: 95%" value="<?php echo $description; ?>" />
                            </td>
                            <td style="vertical-align:top">
                                <img style="cursor:pointer" src="./modules/dotproject_plus/images/trash_small.gif" onclick="deleteRecord(9,<?php echo $req_id ?>)" />
                            </td>
                        </tr>
                        <?php
                        $i++;
                    }
                    ?>

                </table>
                <input type="hidden" name="number_requirements" value="<?php echo $i ?>">
            </td>

        </tr>

        <tr>
            <td colspan="2">
                <br />
                <input name="number_goals" type="hidden" value="2"  />
                &nbsp;
                <input type="button" value="<?php echo $AppUI->_("LBL_QUALITY_ADD_GOAL"); ?>" onclick="newGoal();" />
                <br /><br />
                <!-- Primeiro objetivo -->
                <?php
                $goals = $controller->loadControlGoals($quality_planning_id);
                $i = 0;
                foreach ($goals as $oid => $data) {
                    $goal_id = $data[0];
                    $gqm_goal_propose = $data[1];
                    $gqm_goal_object = $data[2];
                    $gqm_goal_respect_to = $data[3];
                    $gqm_goal_point_of_view = $data[4];
                    $gqm_goal_context = $data[5];
                    ?>

                    <input type="hidden" name="goal_id_<?php echo $i; ?>" value="<?php echo $goal_id; ?>">
                    <table width="100%" class="tbl" style="border: 0px solid black" cellpadding="10">
                        <tr>
                            <th style="width:300px"><?php echo $AppUI->_("LBL_GOAL_OF_CONTROL"); ?></th>
                            <th><?php echo $AppUI->_("LBL_QUESTIONS_OF_ANALYSIS"); ?></th>
                            <th style="width:4%;">&nbsp;</th>
                        </tr>
                        <tr>
                            <td style="vertical-align: top">
                                <table>
                                    <tr>
                                        <td>
                                            <label for="gqm_goal_object">
                                                <?php echo $AppUI->_("LBL_GQM_GOAL_OBJECT"); ?>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>

                                        <td style="vertical-align: top">
                                            <input type="text" name="gqm_goal_object_<?php echo $i ?>" style="width:97%;" value="<?php echo $gqm_goal_object; ?>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="gqm_goal_propose">
                                                <?php echo $AppUI->_("LBL_GQM_GOAL_PURPOSE"); ?>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top">
                                            <input type="text" name="gqm_goal_propose_<?php echo $i ?>" style="width:97%;" value="<?php echo $gqm_goal_propose; ?>" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td >
                                            <label for="gqm_goal_respect_to">
                                                <?php echo $AppUI->_("LBL_GQM_GOAL_RESPECT_TO"); ?>
                                            </label>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="text" name="gqm_goal_respect_to_<?php echo $i ?>" style="width:97%;" value ="<?php echo $gqm_goal_respect_to; ?>" /> 
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <label for="gqm_goal_point_of_view">
                                               <?php echo $AppUI->_("LBL_GQM_GOAL_POINT_OF_VIEW") ?>
                                            </label>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="text" name="gqm_goal_point_of_view_<?php echo $i ?>" style="width:97%;" value="<?php echo $gqm_goal_point_of_view; ?>"  /> 
                                        </td>
                                    </tr>

                                    <tr>
                                        <td >
                                            <label for="gqm_goal_context">
                                                <?php echo $AppUI->_("LBL_GQM_GOAL_CONTEXT"); ?>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>

                                        <td style="vertical-align: top">
                                            <input type="text" name="gqm_goal_context_<?php echo $i ?>" style="width:97%;" value="<?php echo $gqm_goal_context; ?>" /> 
                                        </td>
                                    </tr>

                                </table>
                            </td>

                            <!--  question of analysis -->
                            <td style="vertical-align: top">
                                <table width="95%" align="center" cellpadding="10">
                                    <tr>
                                        <td>
                                            <input type="button" value="<?php echo $AppUI->_("LBL_ADD_QUESTION_OF_ANALYSIS") ?>" onclick="newQuestion(<?php echo $goal_id; ?>)" />
                                        </td>
                                    </tr>
                                </table>



                                <table width="95%" align="center" class="tbl" cellpadding="10" style="border: 0px solid white">
                                    <tr>
                                        <th><?php echo $AppUI->_("LBL_QUESTION_OF_ANALYSIS"); ?></th>
                                        <th><?php echo $AppUI->_("LBL_GQM_TARGET"); ?></th>
                                        <th><?php echo $AppUI->_("LBL_GQM_METRIC"); ?></th>
                                        <th>&nbsp;</th>
                                    </tr>
                                    <?php
                                    $questions = $controller->loadQuestions($goal_id);
                                    $j = 0;
                                    foreach ($questions as $qid => $qdata) {
                                        $question_id = $qdata[0];
                                        $question = $qdata[1];
                                        $target = $qdata[2];
                                        ?>
                                        <tr>
                                            <td style="vertical-align: top">
                                                <input type="hidden" name="question_<?php echo $i ?>_id_<?php echo $j ?>" value="<?php echo $question_id; ?>" />
                                                <textarea name="analysis_question_<?php echo $i ?>_<?php echo $j ?>" style="width:97%;resize: none;" ><?php echo $question ?></textarea>
                                            </td>
                                            <td style="vertical-align: top">
                                                <textarea name="analysis_question_<?php echo $i ?>_benchmark_<?php echo $j ?>" style="width:97%;resize: none;"  ><?php echo $target ?></textarea>
                                            </td>
                                            <td valign="top">
                                                <table width="95%" align="center">
                                                    <tr>
                                                        <td>
                                                            <input type="button" value="<?php echo $AppUI->_("LBL_ADD_METRIC"); ?>" onclick="newMetric(<?php echo $question_id ?>)" />
                                                        </td>
                                                    </tr>
                                                </table>

                                                <table width="95%" align="center" cellpadding="10" class="tbl" style="border: 0px solid black">
                                                    <tr>
                                                        <th width="50%">
                                                            <?php echo $AppUI->_("LBL_GQM_METRIC"); ?>
                                                        </th>
                                                        <th width="50%">
                                                            <?php echo $AppUI->_("LBL_GQM_HOW_DATA_IS_COLLECTED"); ?>
                                                        </th>
                                                        <th style="width:4%">
                                                            &nbsp;
                                                        </th>
                                                    </tr>

                                                    <?php
                                                    $metrics = $controller->loadMetrics($question_id);
                                                    $k = 0;
                                                    foreach ($metrics as $mid => $mdata) {
                                                        $metric_id = $mdata[0];
                                                        $metric = $mdata[1];
                                                        $how_to_collect = $mdata[2];
                                                        ?>           
                                                        <tr>
                                                            <td style="vertical-align: top">
                                                                <input type="hidden" name="metric_<?php echo $k ?>_qoa_<?php echo $question_id ?>_id" value="<?php echo $metric_id; ?>" />
                                                                <textarea  name="metric_<?php echo $k; ?>_qoa_<?php echo $question_id; ?>" style="width:97%;height:60px;resize: none;" ><?php echo $metric; ?></textarea>
                                                            </td>
                                                            <td style="vertical-align: top">
                                                                <textarea  name="metric_<?php echo $k; ?>_qoa_<?php echo $question_id; ?>_data_collection" style="width:97%;height:60px;resize: none;" ><?php echo $how_to_collect; ?></textarea>
                                                            </td>
                                                            <td style="vertical-align:top">
                                                                <img style="cursor:pointer" src="./modules/dotproject_plus/images/trash_small.gif" onclick="deleteRecord(6,<?php echo $metric_id; ?>)" />
                                                            </td>
                                                        </tr>
                                                        <?php
                                                        $k++;
                                                    }
                                                    ?>
                                                    <input name="number_metrics_<?php echo $question_id; ?>" type="hidden" value="<?php echo $k; ?>"  />

                                                </table> 
                                            </td>
                                            <td style="vertical-align:top">
                                                <img style="cursor:pointer" src="./modules/dotproject_plus/images/trash_small.gif" onclick="deleteRecord(7,<?php echo $question_id ?>)" />
                                            </td>
                                        </tr>
                                        <?php
                                        $j++;
                                    }
                                    ?>
                                    <input name="number_questions_<?php echo $goal_id; ?>" type="hidden" value="<?php echo $j ?>"  />
                                </table>
                            </td>
                            <td style="vertical-align:top">
                                <img style="cursor:pointer" src="./modules/dotproject_plus/images/trash_small.gif" onclick="deleteRecord(8,<?php echo $goal_id ?>)" />
                            </td>
                        </tr>
                    </table>
                    <br />
                    <?php
                    $i++;
                }
                ?>
                <input type="hidden" name="number_goals" value="<?php echo $i ?>">
            </td>
        </tr>
    </table>

    <table width="95%" align="center">
        <tr>
            <td colspan="2" align="right">
                <input type="submit" name="Salvar" value="<?php echo $AppUI->_("LBL_SAVE"); ?>" class="button" />
                <?php require_once (DP_BASE_DIR . "/modules/timeplanning/view/subform_back_button_project.php"); ?>
            </td>
        </tr>
    </table>
</form>
