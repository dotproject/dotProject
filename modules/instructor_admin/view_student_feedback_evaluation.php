<?php
require_once DP_BASE_DIR . "/modules/dotproject_plus/feedback/user_feedback_evaluation/feedback_evaluation_statistic.class.php";
$feedbackManager=new InstructionalFeebackManager();//imported automatically in the index.php using dotproject_plus theme
$kaList=$feedbackManager->getKaList();
?>
<style>
    .feedback_report td{
         vertical-align: top;
         border-spacing: 15px;
    }            
</style>

<a href="index.php?m=instructor_admin"><?php echo $AppUI->_("Instructor Admin") ?> </a> 
<p style="text-align: center">
    <?php echo $AppUI->_("LBL_FEEDBACK_EVALUATION_REPORT") ?>
</p>

<?php
foreach ($kaList as $ka) {
    $feedbackListKa = $feedbackManager->getAllFeedbackPerKnoledgeArea($ka);
    ?>

        <br />
        <br />
        <table class="feedback_report tbl" >
        <tr>
            <th colspan="6">
    <?php echo $ka ?>
            </th>
        </tr>
        <tr>
            <th width="5%"><?php echo $AppUI->_("Id"); ?></th>
            <th width="35%"><?php echo $AppUI->_("LBL_FEEDBACK_SHORT_FORMAT"); ?></th>
            <th width="45%"><?php echo $AppUI->_("Feedback"); ?></th>
            <th width="5%"><?php echo $AppUI->_("LBL_FEADBACK_TOTAL_EVALUATIONS"); ?></th>
            <th width="5%"><?php echo $AppUI->_("LBL_FEEDBACK_AVERAGE_EVALUATION"); ?></th>
            <th width="5%"><?php echo $AppUI->_("LBL_FEEDBACK_STDV_EVALUATION"); ?></th>
        </tr>
        <?php
        foreach ($feedbackListKa as $feedback) {
            $id = $feedback->getId();
            $statistics=new FeedbackEvaluationStatistic($id);
            $short = $feedback->getShort();
            $description = $feedback->getDescription();
            $average= number_format((float)$statistics->getAverage(), 2, '.', '');
            $stdv = number_format((float)$statistics->getStdv(), 2, '.', '');;
            $total =$statistics->getTotal();
            ?>
            <tr>
                
                <td><?php echo $id ?></td>
                <td><?php echo $short ?></td>
                <td><?php echo $description ?></td>
                <td style="text-align: center;vertical-align: middle"><?php echo $total ?></td>
                <td style="text-align: center;vertical-align: middle"><?php echo $average ?></td>
                <td style="text-align: center;vertical-align: middle"><?php echo $stdv ?></td>
            </tr>
        <?php
    }
    ?>
    </table>        
    <?php
}
?>
