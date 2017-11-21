<?php

require_once DP_BASE_DIR . "/modules/initiating/initiating.class.php";
require_once DP_BASE_DIR . "/modules/projects/projects.class.php";
//initialize user feedback messages configuration and history in the current session
//$sid = session_id();

if (isset($_SESSION['AppUI']) && !isset($_SESSION["user_feedback_read"])) {
    //session_start();
    $_SESSION["user_feedback_read"] = array();
    $_SESSION["user_especific_feedback"] = 1;
    $_SESSION["user_generic_feedback"] = 1;
}

$feedback_list = array();
$_SESSION["user_feedback"] = array(); 

class InstructionalFeebackManager {

    private $kaList;

    function __construct() {
        global $AppUI;
        $this->kaList = array(
            $AppUI->_("LBL_FEEDBACK_INTEGRATION"),
            $AppUI->_("LBL_FEEDBACK_SCOPE"),
            $AppUI->_("LBL_FEEDBACK_TIME"),
            $AppUI->_("LBL_FEEDBACK_COST"),
            $AppUI->_("LBL_FEEDBACK_QUALITY"),
            $AppUI->_("LBL_FEEDBACK_HR"),
            $AppUI->_("LBL_FEEDBACK_COMUNICATION"),
            $AppUI->_("LBL_FEEDBACK_RISK"),
            $AppUI->_("LBL_FEEDBACK_ACQUISITIONS"),
            $AppUI->_("Stakeholder")
        );
        $this->initializeFeedbackMessages();
    }

    private function initializeFeedbackMessages() {
        global $feedback_list;
        global $AppUI;
        $feedback_list[0] = new Feedback(0, "", "", true, ""); //dummy feedback, just to fill index 0
        //The last feedback index is: 46 - update this comment accordingly new feedback messages are added.

        /* Integração */
        $feedback_list[1] = new Feedback(1, $AppUI->_("LBL_FEEDBACK_SHORT_1"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_1", UI_OUTPUT_HTML), true, $AppUI->_("LBL_FEEDBACK_INTEGRATION"));
        $feedback_list[1]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_1"));
        $feedback_list[2] = new Feedback(2, $AppUI->_("LBL_FEEDBACK_SHORT_2"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_2", UI_OUTPUT_HTML), false, $AppUI->_("LBL_FEEDBACK_INTEGRATION"));
        $feedback_list[2]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_2"));
        $feedback_list[3] = new Feedback(3, $AppUI->_("LBL_FEEDBACK_SHORT_3"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_3", UI_OUTPUT_HTML), true, $AppUI->_("LBL_FEEDBACK_INTEGRATION"));
        $feedback_list[3]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_3"));  
        /* Escopo */
        $feedback_list[4] = new Feedback(4, $AppUI->_("LBL_FEEDBACK_SHORT_4"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_4", UI_OUTPUT_HTML), true, $AppUI->_("LBL_FEEDBACK_SCOPE"));
        $feedback_list[4]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_4"));
        $feedback_list[5] = new Feedback(5, $AppUI->_("LBL_FEEDBACK_SHORT_5"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_5", UI_OUTPUT_HTML), true, $AppUI->_("LBL_FEEDBACK_SCOPE"));
        $feedback_list[5]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_5"));   
        $feedback_list[6] = new Feedback(6, $AppUI->_("LBL_FEEDBACK_SHORT_6"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_6", UI_OUTPUT_HTML), true, $AppUI->_("LBL_FEEDBACK_SCOPE"));
        $feedback_list[6]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_6"));
        $feedback_list[35] = new Feedback(35, $AppUI->_("LBL_FEEDBACK_SHORT_35"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_35", UI_OUTPUT_HTML), true, $AppUI->_("LBL_FEEDBACK_SCOPE"));
        $feedback_list[35]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_35"));
        $feedback_list[37] = new Feedback(37, $AppUI->_("LBL_FEEDBACK_SHORT_37"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_37", UI_OUTPUT_HTML), false, $AppUI->_("LBL_FEEDBACK_SCOPE"));
        $feedback_list[37]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_37"));
       
//$feedback_list[7]= new Feedback(7, "Pacotes de trabalho não podem ser decompostos em novos items da EAP.","Você já está derivando as atividades do projeto dos pacotes de trabalho. Cuidado para não criar subitens da EAP para pacotes de trabalho que já tiveram atividades derivadas.",true,"Escopo");//será bloqueado a criação de pacotes de trabalho para items da EAP que já tem atividades //\modules\dotproject_plus\projects_tab.planning_and_monitoring.php
        /* Tempo */
        $feedback_list[8] = new Feedback(8, $AppUI->_("LBL_FEEDBACK_SHORT_8"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_8", UI_OUTPUT_HTML), true, $AppUI->_("LBL_FEEDBACK_TIME"));
        $feedback_list[8]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_8"));
        $feedback_list[9] = new Feedback(9, $AppUI->_("LBL_FEEDBACK_SHORT_9"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_9", UI_OUTPUT_HTML), true, $AppUI->_("LBL_FEEDBACK_TIME"));
        $feedback_list[9]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_9"));
        $feedback_list[10] = new Feedback(10, $AppUI->_("LBL_FEEDBACK_SHORT_10"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_10", UI_OUTPUT_HTML), false, $AppUI->_("LBL_FEEDBACK_TIME"));
        $feedback_list[10]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_10"));
        $feedback_list[11] = new Feedback(11, $AppUI->_("LBL_FEEDBACK_SHORT_11"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_11", UI_OUTPUT_HTML), false, $AppUI->_("LBL_FEEDBACK_TIME"));
        $feedback_list[11]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_11"));
        $feedback_list[12] = new Feedback(12, $AppUI->_("LBL_FEEDBACK_SHORT_12"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_12", UI_OUTPUT_HTML), false, $AppUI->_("LBL_FEEDBACK_TIME"));
        $feedback_list[12]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_12"));
        $feedback_list[13] = new Feedback(13, $AppUI->_("LBL_FEEDBACK_SHORT_13"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_13", UI_OUTPUT_HTML), false, $AppUI->_("LBL_FEEDBACK_TIME"));
        $feedback_list[13]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_13"));
        $feedback_list[38] = new Feedback(38, $AppUI->_("LBL_FEEDBACK_SHORT_38"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_38", UI_OUTPUT_HTML), true, $AppUI->_("LBL_FEEDBACK_TIME"));
        $feedback_list[38]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_38"));
        $feedback_list[39] = new Feedback(39, $AppUI->_("LBL_FEEDBACK_SHORT_39"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_39", UI_OUTPUT_HTML), true, $AppUI->_("LBL_FEEDBACK_TIME"));
        $feedback_list[39]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_39"));
        /* Custo */
        $feedback_list[14] = new Feedback(14, $AppUI->_("LBL_FEEDBACK_SHORT_14"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_14", UI_OUTPUT_HTML), true, $AppUI->_("LBL_FEEDBACK_COST"));
        $feedback_list[14]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_14"));
        $feedback_list[15] = new Feedback(15, $AppUI->_("LBL_FEEDBACK_SHORT_15"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_15", UI_OUTPUT_HTML), true, $AppUI->_("LBL_FEEDBACK_COST"));
        $feedback_list[15]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_15"));
        $feedback_list[16] = new Feedback(16, $AppUI->_("LBL_FEEDBACK_SHORT_16"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_16", UI_OUTPUT_HTML), true, $AppUI->_("LBL_FEEDBACK_COST"));
        $feedback_list[16]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_16"));
        $feedback_list[17] = new Feedback(17, $AppUI->_("LBL_FEEDBACK_SHORT_17"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_17", UI_OUTPUT_HTML), true, $AppUI->_("LBL_FEEDBACK_COST"));
        $feedback_list[17]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_17"));
        $feedback_list[18] = new Feedback(18, $AppUI->_("LBL_FEEDBACK_SHORT_18"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_18", UI_OUTPUT_HTML), true, $AppUI->_("LBL_FEEDBACK_COST"));
        $feedback_list[18]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_18")); 
        /* Qualidade */
        $feedback_list[19] = new Feedback(19, $AppUI->_("LBL_FEEDBACK_SHORT_19"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_19", UI_OUTPUT_HTML), false, $AppUI->_("LBL_FEEDBACK_QUALITY"));
        $feedback_list[19]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_19"));
        $feedback_list[36] = new Feedback(36, $AppUI->_("LBL_FEEDBACK_SHORT_36"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_36", UI_OUTPUT_HTML), true, $AppUI->_("LBL_FEEDBACK_QUALITY"));
        $feedback_list[36]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_36"));
        $feedback_list[40] = new Feedback(40, $AppUI->_("LBL_FEEDBACK_SHORT_40"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_40", UI_OUTPUT_HTML), true, $AppUI->_("LBL_FEEDBACK_QUALITY"));
        $feedback_list[40]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_40"));
        /* Recursos Humanos */
        $feedback_list[20] = new Feedback(20, $AppUI->_("LBL_FEEDBACK_SHORT_20"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_20", UI_OUTPUT_HTML), true, $AppUI->_("LBL_FEEDBACK_HR"));
        $feedback_list[20]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_20"));
        $feedback_list[21] = new Feedback(21, $AppUI->_("LBL_FEEDBACK_SHORT_21"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_21", UI_OUTPUT_HTML), true, $AppUI->_("LBL_FEEDBACK_HR"));
        $feedback_list[21]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_21"));
        $feedback_list[22] = new Feedback(22, $AppUI->_("LBL_FEEDBACK_SHORT_22"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_22", UI_OUTPUT_HTML), false, $AppUI->_("LBL_FEEDBACK_HR"));
        $feedback_list[22]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_22"));
        $feedback_list[41] = new Feedback(41, $AppUI->_("LBL_FEEDBACK_SHORT_41"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_41", UI_OUTPUT_HTML), true, $AppUI->_("LBL_FEEDBACK_HR"));
        $feedback_list[41]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_41"));
        /* Comunicação */
        $feedback_list[23] = new Feedback(23, $AppUI->_("LBL_FEEDBACK_SHORT_23"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_23", UI_OUTPUT_HTML), true, $AppUI->_("LBL_FEEDBACK_COMUNICATION"));
        $feedback_list[23]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_23"));
        $feedback_list[24] = new Feedback(24, $AppUI->_("LBL_FEEDBACK_SHORT_24"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_24", UI_OUTPUT_HTML), false, $AppUI->_("LBL_FEEDBACK_COMUNICATION"));
        $feedback_list[24]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_24"));
        $feedback_list[25] = new Feedback(25, $AppUI->_("LBL_FEEDBACK_SHORT_25"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_25", UI_OUTPUT_HTML), false, $AppUI->_("LBL_FEEDBACK_COMUNICATION"));
        $feedback_list[25]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_25"));
        $feedback_list[42] = new Feedback(42, $AppUI->_("LBL_FEEDBACK_SHORT_42"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_42", UI_OUTPUT_HTML), false, $AppUI->_("LBL_FEEDBACK_COMUNICATION"));
        $feedback_list[42]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_42"));
        /* Riscos */
        $feedback_list[26] = new Feedback(26, $AppUI->_("LBL_FEEDBACK_SHORT_26"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_26", UI_OUTPUT_HTML), true, $AppUI->_("LBL_FEEDBACK_RISK"));
        $feedback_list[26]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_26"));
        $feedback_list[27] = new Feedback(27, $AppUI->_("LBL_FEEDBACK_SHORT_27"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_27", UI_OUTPUT_HTML), true, $AppUI->_("LBL_FEEDBACK_RISK"));
        $feedback_list[27]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_27"));
        $feedback_list[28] = new Feedback(28, $AppUI->_("LBL_FEEDBACK_SHORT_28"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_28", UI_OUTPUT_HTML), false, $AppUI->_("LBL_FEEDBACK_RISK"));
        $feedback_list[28]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_28"));
        //$feedback_list[34]= new Feedback(34, $AppUI->_("LBL_FEEDBACK_SHORT_34") ,$AppUI->_("LBL_FEEDBACK_DESCRIPTION_34",UI_OUTPUT_HTML),true,$AppUI->_("LBL_FEEDBACK_RISK"));
        $feedback_list[43] = new Feedback(43, $AppUI->_("LBL_FEEDBACK_SHORT_43"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_43", UI_OUTPUT_HTML), true, $AppUI->_("LBL_FEEDBACK_RISK"));
        $feedback_list[43]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_43"));
        /* Aquisição */
        $feedback_list[29] = new Feedback(29, $AppUI->_("LBL_FEEDBACK_SHORT_29"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_29", UI_OUTPUT_HTML), true, $AppUI->_("LBL_FEEDBACK_ACQUISITIONS"));
        $feedback_list[29]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_29"));
        $feedback_list[30] = new Feedback(30, $AppUI->_("LBL_FEEDBACK_SHORT_30"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_30", UI_OUTPUT_HTML), false, $AppUI->_("LBL_FEEDBACK_ACQUISITIONS"));
        $feedback_list[30]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_30"));
        $feedback_list[44] = new Feedback(44, $AppUI->_("LBL_FEEDBACK_SHORT_44"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_44", UI_OUTPUT_HTML), true, $AppUI->_("LBL_FEEDBACK_ACQUISITIONS"));
        $feedback_list[44]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_44"));
        $feedback_list[45] = new Feedback(45, $AppUI->_("LBL_FEEDBACK_SHORT_45"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_45", UI_OUTPUT_HTML), true, $AppUI->_("LBL_FEEDBACK_ACQUISITIONS"));
        $feedback_list[45]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_45"));
        $feedback_list[46] = new Feedback(46, $AppUI->_("LBL_FEEDBACK_SHORT_46"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_46", UI_OUTPUT_HTML), true, $AppUI->_("LBL_FEEDBACK_ACQUISITIONS"));
        $feedback_list[46]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_46"));
        /* Stakeholder */
        $feedback_list[31] = new Feedback(31, $AppUI->_("LBL_FEEDBACK_SHORT_31"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_31", UI_OUTPUT_HTML), true, "Stakeholder");
        $feedback_list[31]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_31"));
        $feedback_list[32] = new Feedback(32, $AppUI->_("LBL_FEEDBACK_SHORT_32"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_32", UI_OUTPUT_HTML), true, "Stakeholder");
        $feedback_list[32]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_32"));
        $feedback_list[33] = new Feedback(33, $AppUI->_("LBL_FEEDBACK_SHORT_33"), $AppUI->_("LBL_FEEDBACK_DESCRIPTION_33", UI_OUTPUT_HTML), false, "Stakeholder");
        $feedback_list[33]->setDescriptionForAssignment($AppUI->_("LBL_FEEDBACK_EVALUATION_33"));
    }
    

    public function formatMessagesForWord($message){
        return  htmlspecialchars($message,ENT_QUOTES);
    }

    public function runFeedbackTriggersEvaluation() {
        global $feedback_list;
//Start triggers evaluation
        $projectId = $_GET["project_id"];
        if ($projectId != "") {
            $project = new CProject();
            $project->load($projectId);
            $company_id = $project->project_company;
            $initiating_obj = CInitiating::findByProjectId($projectId);
            //variables utilizaed by trigers evaluation
            $wbsItemsCount = wbsItemCount($projectId);
            $risksCount = getRisksCount($projectId);
            $stakeholderCount = getStakeholdersCount($projectId);
            $procurementsCount = getProjectProcurementsCount($projectId);
            $thereIsAllocationToProject = thereIsAllocationToTheProject($projectId);
            $countAllocaredHR = countProjectHRsAllocations($projectId);
            $activitiesCount = getActivitiesCount($projectId);
            //1, 2, 3 -initiating
            if (projectCharterIsCompleteAndNotAuhorized($projectId)) {
                $this->addFeedbackToUser($feedback_list[1]);
            }
            if (projectCharterIsNotComplete($projectId) && (initiating_obj != null && stripos($initiating_obj->initiating_expected_result, "TCC") === false)) {
                $this->addFeedbackToUser($feedback_list[2]);
            }

            if (!thereIsAuthorizedProjectCharter($projectId) && getActivitiesCount($projectId) >= 1) {
                $this->addFeedbackToUser($feedback_list[3]);
            }

            if ($wbsItemsCount <= 3) {
                $this->addFeedbackToUser($feedback_list[4]);
            }
            if ($wbsItemsCount > 10) {
                $this->addFeedbackToUser($feedback_list[5]);
            }
            if (wbsItemWithNoEstimativesCount($projectId) > 0 && $activitiesCount > 0) {
                $this->addFeedbackToUser($feedback_list[6]);
            }
            if (wbsItemWithWrongSizeEstimatives($projectId) > 0) {
                $this->addFeedbackToUser($feedback_list[35]);
            }
            if (thereIsWbsItemForPropostaTCC($projectId) > 0) {
                $this->addFeedbackToUser($feedback_list[37]);
            }

            

            if ($activitiesCount > 0) {

                if (thereIsWBSItemWithSingleActivity($projectId) && existSequencing($projectId)) {
                    $this->addFeedbackToUser($feedback_list[9]);
                }

                if ($activitiesCount > 5 && !thereIsAllocatedRolesForCapstoneProject($projectId)) {
                    $this->addFeedbackToUser($feedback_list[10]);
                }

                if (thereIsActivityLongerThanTwoWeeksDuration($projectId)) {
                    $this->addFeedbackToUser($feedback_list[11]);
                }

                if (thereIsMilestoneForCapstoneProject($projectId)) {
                    $this->addFeedbackToUser($feedback_list[12]);
                }

                if (isProjectDurationLongerThanAYear($projectId)) {
                    $this->addFeedbackToUser($feedback_list[13]);
                }

                if (existsActivitiesWithLessthanEightHours($projectId)) {
                    $this->addFeedbackToUser($feedback_list[38]);
                }

                if (existsActivitiesWithMorethanEightyHours($projectId)) {
                    $this->addFeedbackToUser($feedback_list[39]);
                }
            }

            if ($activitiesCount >= 5) {
//20, 21, 41 -hr
                if (thereIsOverAllocatedResources($projectId)) {
                    $this->addFeedbackToUser($feedback_list[20]);
                }

                
             
                if (thereIsNonAllocatedHRs($projectId) && $countAllocaredHR >= 5) {
                    $this->addFeedbackToUser($feedback_list[21]);
                }

                if (thereIsNonAllocatedRoles($projectId) && $countAllocaredHR >= 3) {
                    $this->addFeedbackToUser($feedback_list[41]);
                }
            }

//13- time
            if (isProjectDurationLongerThanAYear($projectId)) {
                $this->addFeedbackToUser($feedback_list[13]);
            }

//31,32,33 - stakeholder
            if ($stakeholderCount >= 3 && thereIsStakeholdersWithoutPowerInterestAnalysis($projectId)) {
                $this->addFeedbackToUser($feedback_list[31]);
            }
            if ($stakeholderCount < 3 && $procurementsCount > 1) {
                $this->addFeedbackToUser($feedback_list[32]);
                $this->addFeedbackToUser($feedback_list[33]);
            }

            //14,15, 16, 17, 18 - Cost
            if (thereIsNonConfiguredHR($projectId) && $thereIsAllocationToProject) {
                $this->addFeedbackToUser($feedback_list[15]);
            }

            if (thereIsHourRateCostTooLow($projectId)) {
                $this->addFeedbackToUser($feedback_list[17]);
            }

            if (!$thereIsAllocationToProject) {
                $this->addFeedbackToUser($feedback_list[18]);
            }
//risk
            if ($risksCount > 0 && !thereIsRiskToBeContingencied($projectId)) {
                $this->addFeedbackToUser($feedback_list[14]);
            }

            if (thereIsRiskToBeContingencied($projectId) && thereIsManagementReserveNonInitialized($projectId)) {
                $this->addFeedbackToUser($feedback_list[16]);
            }
//quality
            if (isQualityPoliciesTooShort($projectId)) {
                $this->addFeedbackToUser($feedback_list[19]);
            }

            if (thereIsAssuranceItemsMethodAsTest($projectId)) {
                $this->addFeedbackToUser($feedback_list[36]);
            }

            if (thereIsMissingMetricsInQualityPlan($projectId)) {
                $this->addFeedbackToUser($feedback_list[40]);
            }

//23, 24, 25 - comunication
            if (getCommunicationCount($projectId) > 3 && !thereIsComunicationForProjectPlan($projectId)) {
                $this->addFeedbackToUser($feedback_list[23]);
            }
            if (getComunicationChannelCount($projectId) > 2 && !thereIsCommunicationChannelForCapstoneProjectSystem($projectId)) {
                $this->addFeedbackToUser($feedback_list[24]);
            }

            if (getCommunicationCount($projectId) > 3 && !thereIsCommunicationChannelForCapstoneProjectSystem($projectId)) {
                $this->addFeedbackToUser($feedback_list[25]);
            }

            if (getComunicationFrequencyCount($projectId) > 2 && !thereIsComunicationFrequencyAdvisorMeeting($projectId)) {
                $this->addFeedbackToUser($feedback_list[42]);
            }
//26, 27, 28, 43 - risks
            if ($procurementsCount >= 2 && $risksCount < 3) {
                $this->addFeedbackToUser($feedback_list[28]);
            }

            if (thereIsRiskResponse($projectId, 1)) {
                $this->addFeedbackToUser($feedback_list[26]);
            }
            if (thereIsRiskResponse($projectId, 0)) {
                $this->addFeedbackToUser($feedback_list[27]);
            }
            if (thereIsRiskResponsePlanForLowExpositionFactor($projectId) && $risksCount > 0) {
                $this->addFeedbackToUser($feedback_list[43]);
            }

//29 e 30
            if (getNonHRCostCount($projectId) > 1 && $procurementsCount > 1) {
                $this->addFeedbackToUser($feedback_list[29]);
            }
            if ($procurementsCount > 2) {
                $this->addFeedbackToUser($feedback_list[30]);
            }
            if (thereIsAcquisitionWithNoSelectionCriteria($projectId)) {
                $this->addFeedbackToUser($feedback_list[44]);
            }

            if (thereIsAcquisitionDocumentAsDT($projectId)) {
                $this->addFeedbackToUser($feedback_list[45]);
            }

            if (thereIsAcquisitionDocumentAsTechnicalEspecification($projectId)) {
                $this->addFeedbackToUser($feedback_list[46]);
            }


//31,32,33 - stakeholder
            if (getStakeholdersCount($projectId) >= 3 && thereIsStakeholdersWithoutPowerInterestAnalysis($projectId)) {
                $this->addFeedbackToUser($feedback_list[31]);
            }
            if (getStakeholdersCount($projectId) < 3 && $procurementsCount > 1) {
                $this->addFeedbackToUser($feedback_list[32]);
                $this->addFeedbackToUser($feedback_list[33]);
            }

            if (existCircularDependencies($projectId)) {
                $this->addFeedbackToUser($feedback_list[8]);
            }

// 22- rh
            if (getRolesCount($company_id) > 3 and ! thereIsRolesForCapstoneProject($company_id)) {
                $this->addFeedbackToUser($feedback_list[22]);
            }
        }
    }

    function getKaList() {
        return $this->kaList;
    }

    function logFeedbackRead($userId, $feedbackId) {
        $q = new DBQuery();
        $q->addInsert("feedback_id", $feedbackId);
        $q->addInsert("user_id", $userId);
        $q->addInsert("read_on", date('Y-m-d H:i:s'));
        $q->addTable('feedback_message_read_log');
        $q->exec();
    }

    function getUserReadFeedback($userId) {
        $readFeedback = array();
        $q = new DBQuery();
        $q->addQuery("feedback_id,read_on");
        $q->addTable('feedback_message_read_log');
        $q->addWhere("user_id=$userId");
        $sql = $q->prepare();
//echo $sql;
        $records = db_loadList($sql);
        foreach ($records as $record) {
            $readFeedback[$record[0]] = $record[1];
        }
        return $readFeedback;
    }

    function getAllFeedbackPerKnoledgeArea($ka) {
        $feedbackPerKnowledgeArea = array();
        global $feedback_list;
        $i = 0;
        foreach ($feedback_list as $feedbackItem) {
            if ($ka == $feedbackItem->getKnowledgeArea()) {
                $feedbackPerKnowledgeArea[$i] = $feedbackItem;
                $i++;
            }
        }
        return $feedbackPerKnowledgeArea;
    }

    function addFeedbackToUser($feedback) {

        $feedback->setIsTriggerFiredForCurrentUser(true);
        if (!isset($_SESSION["user_feedback_read"][$feedback->getId()])) {//adiciona o feedback apenas se o mesmo ainda não foi lido
            $_SESSION["user_feedback"][$feedback->getId()] = $feedback->getId();
        }
    }

    public function getEvaluationMessagesPerKA($KAKey) {
        global $AppUI;
        $output = "";
        $feedbacksPerKA = $this->getAllFeedbackPerKnoledgeArea($KAKey);
        foreach ($feedbacksPerKA as $feedbackKA) {
            if ($feedbackKA->getIsForAssignment() && $feedbackKA->getIsTriggerFiredForCurrentUser()) {
                $output.=($this->formatMessagesForWord($feedbackKA->getDescriptionForAssignment())). " ; ";//; ;utf8_encode(utf8_decode.          
            }
        }
        $outputLength=strlen($output);
        if ($outputLength>0){
            $output=substr($output, 0 , $outputLength-3);
        }
        return $output;
    }

    public static function getIconByKnowledgeArea($knowledgeArea) {
        global $AppUI;
        switch ($knowledgeArea) {
            case $AppUI->_("LBL_FEEDBACK_INTEGRATION"):
                return "integration";
                break;
            case $AppUI->_("LBL_FEEDBACK_SCOPE"):
                return "scope";
                break;
            case $AppUI->_("LBL_FEEDBACK_TIME"):
                return "time";
                break;
            case $AppUI->_("LBL_FEEDBACK_COST"):
                return "cost";
                break;
            case $AppUI->_("LBL_FEEDBACK_QUALITY"):
                return "quality";
                break;
            case $AppUI->_("LBL_FEEDBACK_HR"):
                return "hr";
                break;
            case $AppUI->_("LBL_FEEDBACK_COMUNICATION"):
                return "comunication";
                break;
            case $AppUI->_("LBL_FEEDBACK_RISK"):
                return "risks";
                break;
            case $AppUI->_("LBL_FEEDBACK_ACQUISITIONS"):
                return "aquisition";
                break;
            case "Stakeholder":
                return "integration";
                break;
        }
    }

}

//initiate an object an run the triggers
$feedbackManager = new InstructionalFeebackManager();
$feedbackManager->runFeedbackTriggersEvaluation();

/**
 * Below are functions to assist in feedback triggers evaluation
 */
//functions to verify feedback trigger
function wbsItemWithSingleWorkPackage($projectId) {
    $list = array();
    $q = new DBQuery();
    $q->addQuery('number, count(*)');
    $q->addTable('project_eap_items', 't');
    $q->addWhere("length(number)>1 and project_id=$projectId group by left(number,length(number)-1) having count(*)=1");
    $sql = $q->prepare();
    $items = db_loadList($sql);
    $result = false;
    if (sizeof($items) > 0) {
        $result = true;
    }
    return $result;
}

function getNonHRCostCount($projectId) {
//SELECT cost_id FROM dotproject_plus.dotp_costs where cost_type_id=1 and cost_project_id=11;
    $q = new DBQuery();
    $q->addQuery('cost_id');
    $q->addTable('costs');
    $q->addWhere("cost_type_id=1 and cost_project_id=$projectId");
    $sql = $q->prepare();
    $items = db_loadList($sql);
    return count($items);
}

function getProjectProcurementsCount($projectId) {
    $q = new DBQuery(); //SELECT id, project_id FROM dotproject_plus.dotp_acquisition_planning;
    $q->addQuery('id, project_id');
    $q->addTable('acquisition_planning');
    $q->addWhere("project_id=$projectId");
    $sql = $q->prepare();
    $items = db_loadList($sql);
    return count($items);
}

function thereIsAcquisitionDocumentAsTechnicalEspecification($projectId) {
    $q = new DBQuery(); //SELECT id, project_id FROM dotproject_plus.dotp_acquisition_planning;
    $q->addQuery('id, project_id');
    $q->addTable('acquisition_planning');
    $q->addWhere("project_id=$projectId and ucase(documents_to_acquisition) like '%ESPECIFICAÇÃO TÉCNICA%'");
    $sql = $q->prepare();
    $items = db_loadList($sql);
    return count($items);
}

function thereIsAcquisitionDocumentAsDT($projectId) {
    $q = new DBQuery(); //SELECT id, project_id FROM dotproject_plus.dotp_acquisition_planning;
    $q->addQuery('id, project_id');
    $q->addTable('acquisition_planning');
    $q->addWhere("project_id=$projectId and (ucase(documents_to_acquisition) like '%DT%' or ucase(documents_to_acquisition) like '%Declaração de Trabalho%')");
    $sql = $q->prepare();
    $items = db_loadList($sql);
    return count($items);
}

function wbsItemCount($projectId) {
    $q = new DBQuery();
    $q->addQuery('number, count(*)');
    $q->addTable('project_eap_items', 't');
    $q->addWhere("project_id=$projectId");
    $sql = $q->prepare();
    $items = db_loadList($sql);
    $result = 0;
    if (sizeof($items) > 0) {
        $result = $items[0][1]; //get count(*)
    }
    return $result;
}

//SELECT te.task_id, te.effort, te.effort_unit FROM dotp_project_tasks_estimations te
//inner join dotp_tasks t on t.task_id =te.task_id and t.task_project=11
//where ((te.effort<=8 and te.effort_unit=0) or (te.effort_unit=1 and te.effort<=480)) and te.effort<>0
function existsActivitiesWithLessthanEightHours($projectId) {
    $q = new DBQuery();
    $q->addQuery('te.task_id, te.effort, te.effort_unit');
    $q->addTable('project_tasks_estimations', 'te');
    $q->addJoin('tasks', 't', "t.task_id =te.task_id and t.task_project=" . $projectId, "inner");
    $q->addWhere("((te.effort<=8 and te.effort_unit=0) or (te.effort_unit=1 and te.effort<=480)) and te.effort<>0");
    $sql = $q->prepare();
//echo $sql;
    $items = db_loadList($sql);
    $result = false;
    if (sizeof($items) > 0) {
        $result = true; //get count(*)
    }
    return $result;
}

function existsActivitiesWithMorethanEightyHours($projectId) {
    $q = new DBQuery();
    $q->addQuery('te.task_id, te.effort, te.effort_unit');
    $q->addTable('project_tasks_estimations', 'te');
    $q->addJoin('tasks', 't', "t.task_id =te.task_id and t.task_project=" . $projectId, "inner");
    $q->addWhere("((te.effort>=80 and te.effort_unit=0) or (te.effort_unit=1 and te.effort>=4800)) and te.effort<>0");
    $sql = $q->prepare();
//echo $sql;
    $items = db_loadList($sql);
    $result = false;
    if (sizeof($items) > 0) {
        $result = true; //get count(*)
    }
    return $result;
}

function thereIsWbsItemForPropostaTCC($projectId) {
    $q = new DBQuery();
    $q->addQuery('number, count(*)');
    $q->addTable('project_eap_items', 't');
    $q->addWhere("project_id=$projectId and (ucase(item_name) like '%PROPOSTA%' and ucase(item_name) like '%TCC%')");
    $sql = $q->prepare();
//echo $sql;
    $items = db_loadList($sql);
    $result = 0;
    if (sizeof($items) > 0) {
        $result = $items[0][1]; //get count(*)
    }
    return $result;
}

function thereIsAcquisitionWithNoSelectionCriteria($projectId) {
    /* SELECT ap.id FROM dotproject_plus.dotp_acquisition_planning ap
      left join dotp_acquisition_planning_criteria ac on ap.id =ac.acquisition_id
      where ap.project_id=11 and ac.id is NULL
     */
    $q = new DBQuery();
    $q->addQuery('ap.id');
    $q->addTable('acquisition_planning', 'ap');
    $q->addJoin("acquisition_planning_criteria", "ac", "ap.id =ac.acquisition_id", "left");
    $q->addWhere("ap.project_id=$projectId and ac.id is NULL");
    $sql = $q->prepare();
//echo $sql;
    $items = db_loadList($sql);
    $result = false;
    if (sizeof($items) > 0) {
        $result = true;
    }
    return $result;
}

function thereIsRolesForCapstoneProject($company_id) {
    /* SELECT r.id, r.role_name FROM dotp_company_role r
      WHERE r.company_id=29 and (ucase(role_name) like '%ALUN%' or ucase(role_name) like '%ORIENTADOR%' or ucase(role_name) like '%BANCA%' or ucase(role_name) like '%ORIENTAND%')
     */
    $q = new DBQuery();
    $q->addQuery('r.id, r.role_name');
    $q->addTable('company_role', 'r');
    $q->addWhere("r.company_id=" . $company_id . " and (ucase(role_name) like '%ALUN%' or ucase(role_name) like '%ORIENTADOR%' or ucase(role_name) like '%BANCA%' or ucase(role_name) like '%ORIENTAND%')");
    $sql = $q->prepare();
//echo $sql;
    $items = db_loadList($sql);
    $result = 0;
    if (sizeof($items) > 0) {
        $result = $items[0][1]; //get count(*)
    }
    return $result;
}

function getRolesCount($company_id) {
    $q = new DBQuery();
    $q->addQuery('r.id, r.role_name');
    $q->addTable('company_role', 'r');
    $q->addWhere("r.company_id=" . $company_id);
    $sql = $q->prepare();
//echo $sql;
    $items = db_loadList($sql);
    return count($items);
}

function getRisksCount($projectId) {
//SELECT count(risk_id) FROM dotproject_plus.dotp_risks where risk_project=11 and risk_active=0 ;
    $q = new DBQuery();
    $q->addQuery('count(risk_id)');
    $q->addTable('risks');
    $q->addWhere("risk_project=$projectId and risk_active=0");
    $sql = $q->prepare();
//echo $sql;
    $items = db_loadList($sql);
    $result = 0;
    if (sizeof($items) > 0) {
        $result = $items[0][0]; //get count(*)
    }
    return $result;
}

/*
 * SELECT t.task_id, ar.rolM dotp_project_tasks_estimated_roles are_id, r.role_name FROM dotp_project_tasks_estimated_roles ar
  inner join dotp_tasks t on t.task_id=ar.task_id and t.task_project=11
  inner join dotp_company_role r on r.id=ar.role_id
  where ucase(r.role_name) like '%ALUN%' or  ucase(r.role_name) like '%ORIENTADOR%'  or ucase(r.role_name) like '%PROFESSOR%'  or ucase(r.role_name) like '%COORDENADOR%' ;
 */

function thereIsAllocatedRolesForCapstoneProject($projectId) {
    $q = new DBQuery();
    $q->addQuery(' t.task_id, ar.role_id, r.role_name');
    $q->addTable('project_tasks_estimated_roles', 'ar');
    $q->addJoin("tasks", "t", "t.task_id=ar.task_id and t.task_project=$projectId", "inner");
    $q->addJoin("company_role", "r", "r.id=ar.role_id", "inner");
    $q->addWhere("ucase(r.role_name) like '%ALUN%' or  ucase(r.role_name) like '%ORIENTADOR%'  or ucase(r.role_name) like '%PROFESSOR%'  or ucase(r.role_name) like '%COORDENADOR%'");
    $sql = $q->prepare();
//echo $sql;
    $items = db_loadList($sql);
    $result = false;
    if (sizeof($items) > 0) {
        $result = true;
    }
    return $result;
}

function wbsItemWithNoEstimativesCount($projectId) {
    $q = new DBQuery();
    $q->addQuery('number, count(*)');
    $q->addTable('project_eap_items', 't');
    $q->addJoin("eap_item_estimations", "e", "t.id=e.eap_item_id");
    $q->addWhere("t.project_id=$projectId and e.size=0 and t.is_leaf=1");
    $sql = $q->prepare();
    $items = db_loadList($sql);
    $result = 0;
    if (sizeof($items) > 0) {
        $result = $items[0][1]; //get count(*)
    }
    return $result;
}

/* SELECT t1.dependencies_task_id, t2.dependencies_task_id FROM dotp_task_dependencies t1 inner join dotp_task_dependencies t2
  on t1.dependencies_task_id= t2.dependencies_req_task_id and t2.dependencies_task_id= t1.dependencies_req_task_id
  inner join dotp_tasks t on t1.dependencies_task_id = t.task_id and t.task_project=11;
 */

function existCircularDependencies($projectId) {
    $q = new DBQuery();
    $q->addQuery('t1.dependencies_task_id, t2.dependencies_task_id');
    $q->addTable('task_dependencies', 't1');
    $q->addJoin("task_dependencies", "t2", "t1.dependencies_task_id= t2.dependencies_req_task_id and t2.dependencies_task_id= t1.dependencies_req_task_id", "inner");
    $q->addJoin("tasks", "t", "t1.dependencies_task_id = t.task_id and t.task_project=" . $projectId, "inner");
    $sql = $q->prepare();
//echo $sql;
    $items = db_loadList($sql);
    $result = false;
    if (sizeof($items) > 0) {
        $result = true;
    }
    return $result;
}

function existSequencing($projectId) {
    $q = new DBQuery();
    $q->addQuery('t1.dependencies_task_id');
    $q->addTable('task_dependencies', 't1');
    $q->addJoin("tasks", "t", "t1.dependencies_task_id = t.task_id and t.task_project=" . $projectId, "inner");
    $sql = $q->prepare();
//echo $sql;
    $items = db_loadList($sql);
    $result = false;
    if (sizeof($items) > 0) {
        $result = true;
    }
    return $result;
}

function wbsItemWithWrongSizeEstimatives($projectId) {
    $q = new DBQuery();
    $q->addQuery('number, count(*)');
    $q->addTable('project_eap_items', 't');
    $q->addJoin("eap_item_estimations", "e", "t.id=e.eap_item_id");
    $q->addWhere("t.project_id=$projectId and (e.size_unit=ucase('DAYS') or e.size_unit=ucase('HOURS') or e.size_unit=ucase('DIAS') or e.size_unit=ucase('HORAS') )");
    $sql = $q->prepare();
//echo $sql;
    $items = db_loadList($sql);
    $result = 0;
    if (sizeof($items) > 0) {
        $result = $items[0][1]; //get count(*)
    }
    return $result;
}

//SELECT number,count(*) FROM dotproject_usability.dotp_project_eap_items where length(number)>1 and project_id=2 group by left(number,length(number)-1) having count(*)=1

function wbsWorkPackageTotalEffort($projectId) {
    $q = new DBQuery();
    $q->addQuery('effort'); //SELECT * FROM dotproject_usability.dotp_project_tasks_estimations te inner join dotproject_usability.dotp_tasks t on t.task_id=te.task_id where te.effort>0 and t.task_project=2;
    $q->addTable('project_tasks_estimations', 'te');
    $q->addJoin("tasks", "t", "t.task_id=te.task_id", "inner");
    $q->addWhere("te.effort>0 and t.task_project=$projectId");
    $sql = $q->prepare();
    $items = db_loadList($sql);
    $result = false;
    if (sizeof($items) > 0) {
        $result = true;
    }
    return $result;
}

/* SELECT hra.human_resource_id, er.task_id FROM dotproject_plus.dotp_human_resource_allocation hra
  inner join dotp_project_tasks_estimated_roles er on hra.project_tasks_estimated_roles_id=er.id
  inner join dotp_tasks t on er.task_id=t.task_id
  where t.task_project=11
 */

function thereIsAllocationToTheProject($projectId) {
    $q = new DBQuery();
    $q->addQuery('hra.human_resource_id, er.task_id'); //SELECT * FROM dotproject_usability.dotp_project_tasks_estimations te inner join dotproject_usability.dotp_tasks t on t.task_id=te.task_id where te.effort>0 and t.task_project=2;
    $q->addTable('human_resource_allocation', 'hra');
    $q->addJoin("project_tasks_estimated_roles", "er", "hra.project_tasks_estimated_roles_id=er.id", "inner");
    $q->addJoin("tasks", "t", "er.task_id=t.task_id", "inner");
    $q->addWhere("t.task_project=$projectId");
    $sql = $q->prepare();
    $items = db_loadList($sql);
    $result = false;
    if (count($items) > 0) {
        $result = true;
    }
    return $result;
}

function thereIsCommunicationChannelForCapstoneProjectSystem($projectId) {
    /* SELECT c.communication_id FROM dotp_communication c
      inner join dotp_communication_channel  cc on c.communication_channel_id=cc.communication_channel_id
      where communication_project_id=11 and (ucase(cc.communication_channel) like '%SISTEMA%' or ucase(cc.communication_channel) like '%SITE%' or ucase(cc.communication_channel) like '%SOFTWARE%' )
     */
    $q = new DBQuery();
    $q->addQuery('c.communication_id');
    $q->addTable('communication', 'c');
    $q->addJoin("communication_channel", "cc", "c.communication_channel_id=cc.communication_channel_id", "inner");
    $q->addWhere("communication_project_id=$projectId and (ucase(cc.communication_channel) like '%SISTEMA%' or ucase(cc.communication_channel) like '%SITE%' or ucase(cc.communication_channel) like '%SOFTWARE%' )");
    $sql = $q->prepare();
    $items = db_loadList($sql);
    $result = false;
    if (count($items) > 0) {
        $result = true;
    }
    return $result;
}

function getComunicationChannelCount($projectId) {
    $q = new DBQuery();
    $q->addQuery('c.communication_id');
    $q->addTable('communication', 'c');
    $q->addJoin("communication_channel", "cc", "c.communication_channel_id=cc.communication_channel_id", "inner");
    $q->addWhere("communication_project_id=$projectId");
    $sql = $q->prepare();
    $items = db_loadList($sql);
    return count($items);
}

function getComunicationFrequencyCount($projectId) {
    $q = new DBQuery();
    $q->addQuery('c.communication_id');
    $q->addTable('communication', 'c');
    $q->addJoin("communication_frequency", "cf", "c.communication_frequency_id=cf.communication_frequency_id", "inner");
    $q->addWhere("communication_project_id=$projectId");
    $sql = $q->prepare();
    $items = db_loadList($sql);
    return count($items);
}

function thereIsComunicationFrequencyAdvisorMeeting($projectId) {
    $q = new DBQuery();
    $q->addQuery('c.communication_id');
    $q->addTable('communication', 'c');
    $q->addJoin("communication_frequency", "cf", "c.communication_frequency_id=cf.communication_frequency_id", "inner");
    $q->addWhere("communication_project_id=$projectId and (ucase(cf.communication_frequency) like '%SEMANAL%' or ucase(cf.communication_frequency) like '%QUINZENAL%' or ucase(cf.communication_frequency) like '%MENSAL%' ) and ( ucase(c.communication_title) like '%REUNIÃO%' or ucase(c.communication_title) like '%ENCONTRO%' )");
    $sql = $q->prepare();
//echo $sql;
    $items = db_loadList($sql);
    return count($items);
}

function countProjectHRsAllocations($projectId) {
    /*
     * SELECT hra.human_resource_allocation_id 
      from  dotp_human_resource_allocation hra
      inner join dotp_project_tasks_estimated_roles er on  hra.project_tasks_estimated_roles_id=er.id
      inner join dotp_tasks t on t.task_id=er.task_id
      where t.task_project=11
     */

    $q = new DBQuery();
    $q->addQuery('hra.human_resource_allocation_id');
    $q->addTable('human_resource_allocation', 'hra');
    $q->addJoin("project_tasks_estimated_roles", "er", "hra.project_tasks_estimated_roles_id=er.id", "inner");
    $q->addJoin("tasks", "t", "er.task_id=t.task_id", "inner");
    $q->addWhere("t.task_project=$projectId");
    $sql = $q->prepare();
    $items = db_loadList($sql);
    return count($items);
}

function thereIsNonAllocatedRoles($projectId) {
    /*
      SELECT *
      FROM dotproject_plus.dotp_company_role r
      left join dotp_project_tasks_estimated_roles er on er.role_id =r.id
      where company_id=29;
     */
    $project = new CProject();
    $project->load($projectId);
    $company_id = $project->project_company;

    $q = new DBQuery();
    $q->addQuery('r.id');
    $q->addTable('company_role', 'r');
    $q->addJoin("project_tasks_estimated_roles", "er", "er.role_id =r.id", "left");
    $q->addWhere("r.company_id=" . $company_id . " and er.id is NULL");
    $sql = $q->prepare();
//echo $sql;
    $items = db_loadList($sql);
    $result = false;
    if (count($items) > 0) {
        $result = true;
    }
    return $result;
}

function thereIsNonAllocatedHRs($projectId) {
    /*
      SELECT *
      FROM dotp_users u
      left join dotp_human_resource hr on u.user_id=hr.human_resource_user_id
      left join dotp_human_resource_allocation hra on hr.human_resource_id= hra.human_resource_id
      left  join dotp_project_tasks_estimated_roles er on  hra.project_tasks_estimated_roles_id=er.id
      where u.user_company=29 and er.task_id is NULL
     */

    $project = new CProject();
    $project->load($projectId);
    $company_id = $project->project_company;

    $q = new DBQuery();
    $q->addQuery('*');
    $q->addTable('users', 'u');
    $q->addJoin("human_resource", "hr", "u.user_id=hr.human_resource_user_id", "left");
    $q->addJoin("human_resource_allocation", "hra", "hr.human_resource_id= hra.human_resource_id", "left");
    $q->addJoin("project_tasks_estimated_roles", "er", "hra.project_tasks_estimated_roles_id=er.id", "left");
    $q->addWhere("u.user_company=" . $company_id . " and er.task_id is NULL");
    $q->addWhere("u.user_username not like 'Grupo%'");
   
    $sql = $q->prepare();
//echo $sql;
    $items = db_loadList($sql);
    $result = false;
    if (count($items) > 0) {
        $result = true;
    }
    return $result;
}

function projectCharterIsCompleteAndNotAuhorized($projectId) {
    require_once DP_BASE_DIR . "/modules/initiating/initiating.class.php";
    $obj = CInitiating::findByProjectId($projectId);
    $initiating_id = $obj->initiating_id;
    $initiating_completed = 0;
// se for update verifica se ja esta concluido o preenchimento do termo de abertura do projeto
    if ($initiating_id) {
        $initiating_completed = $obj->initiating_completed;
    }

// se o termo de abertura estiver concluido verifica se est� aprovado
    $initiating_approved = 0;
    if ($initiating_completed) {
        $initiating_approved = $obj->initiating_approved;
    }

// se o termo de abertura estiver aprovado verifica se est� autorizado
    $initiating_authorized = 0;
    if ($initiating_approved) {
        $initiating_authorized = $obj->initiating_authorized;
    }


    if ($initiating_completed == 1 && $initiating_authorized == 0) {
        return true;
    }
    return false;
}

function projectCharterIsNotComplete($projectId) {
    require_once DP_BASE_DIR . "/modules/initiating/initiating.class.php";
    $obj = CInitiating::findByProjectId($projectId);
    $initiating_id = $obj->initiating_id;
    $initiating_completed = 0;
// se for update verifica se ja esta concluido o preenchimento do termo de abertura do projeto
    if ($initiating_id) {
        $initiating_completed = $obj->initiating_completed;
    }

// se o termo de abertura estiver concluido verifica se est� aprovado
    $initiating_approved = 0;
    if ($initiating_completed) {
        $initiating_approved = $obj->initiating_approved;
    }

// se o termo de abertura estiver aprovado verifica se est� autorizado
    $initiating_authorized = 0;
    if ($initiating_approved) {
        $initiating_authorized = $obj->initiating_authorized;
    }

    if ($initiating_id != "" && $initiating_completed == 0 && $initiating_authorized == 0 && $initiating_approved == 0) {
        return true;
    }
    return false;
}

//get date diference
function monthsBetween($startDate, $endDate) {
    $timeStart = strtotime($startDate);
    $timeEnd = strtotime($endDate);
// Adding current month + all months in each passed year
    $numMonths = 1 + (date("Y", $timeEnd) - date("Y", $timeStart)) * 12;
// Add/subtract month difference
    $numMonths += date("m", $timeEnd) - date("m", $timeStart);
    return $numMonths;
}

function isProjectDurationLongerThanAYear($projectId) {
    require_once DP_BASE_DIR . "/modules/initiating/initiating.class.php";
    $obj = CInitiating::findByProjectId($projectId);
    if ($obj->initiating_start_date != "" && $obj->initiating_end_date != "") {
        $monthsDiff = monthsBetween($obj->initiating_start_date, $obj->initiating_end_date);
        if ($monthsDiff > 16) {
            return true;
        }
    }
    return false;
}

function getActivitiesCount($projectId) {
    $q = new DBQuery();
    $q->addQuery("t.task_id");
    $q->addTable("tasks", "t");
    $q->addWhere("t.task_project=" . $projectId);
    $sql = $q->prepare();
    $records = db_loadList($sql);
    return count($records);
}

function getActivitiesWithEstimativesCount($projectId) {
    $q = new DBQuery();
    $q->addQuery("t.task_id");
    $q->addTable("tasks", "t");
    $q->addWhere("t.task_project=" . $projectId . " and t.task_duration>0 and t.task_start_date is not NULL");
    $sql = $q->prepare();
    $records = db_loadList($sql);
    return count($records);
}

function thereIsWBSItemWithSingleActivity($projectId) {
    $q = new DBQuery();
    $q->addQuery("count(wbs_task.task_id)");
    $q->addTable("project_eap_items", "wbs");
    $q->addJoin("tasks_workpackages", "wbs_task", "wbs.id=wbs_task.eap_item_id", "inner");
    $q->addWhere("wbs.project_id=$projectId group by wbs.id having count(wbs_task.task_id)=1");
    $sql = $q->prepare();
    $records = db_loadList($sql);
    if (count($records) > 0) {
        return true;
    }
    return false;
}

function thereIsMilestoneForCapstoneProject($projectId) {
//SELECT  FROM dotproject_usability. t inner join dotp_project_tasks_estimations te on te.task_id=t.task_id where task_project=4 and  duration>14;
    $q = new DBQuery();
    $q->addQuery("t.task_id, t.task_name");
    $q->addTable("tasks", "t");
    $q->addWhere("task_project=$projectId and  ( ( (ucase(task_name) like ucase('%relatório do TCC%') or ucase(task_name) like ucase('%relatório de TCC%')) and month(task_start_date) <> 7 and month(task_start_date) <> 12) or (ucase(task_name) like ucase('%relatório rascunho%') and month(task_start_date) <>5 and month(task_start_date) <> 10))");
    $sql = $q->prepare();
//echo $sql;
    $records = db_loadList($sql);
    if (count($records) > 0) {
        return true;
    }
    return false;
}

function thereIsManagementReserveNonInitialized($projectId) {
    $q = new DBQuery();
    $q->addQuery("b.budget_reserve_management");
    $q->addTable("budget", "b");
    $q->addWhere("b.budget_project_id=$projectId and b.budget_reserve_management<=0");
    $sql = $q->prepare();
//echo $sql;
    $records = db_loadList($sql);
    if (count($records) > 0) {
        return true;
    }
    return false;
}

function thereIsActivityLongerThanTwoWeeksDuration($projectId) {
//SELECT  FROM dotproject_usability. t inner join dotp_project_tasks_estimations te on te.task_id=t.task_id where task_project=4 and  duration>14;
    $q = new DBQuery();
    $q->addQuery("t.task_id, te.duration");
    $q->addTable("tasks", "t");
    $q->addJoin("project_tasks_estimations", "te", "te.task_id=t.task_id", "inner");
    $q->addWhere("task_project=$projectId and  duration>14");
    $sql = $q->prepare();
    $records = db_loadList($sql);
    if (count($records) > 0) {
        return true;
    }
    return false;
}

function isQualityPoliciesTooShort($projectId) {
//SELECT * FROM dotproject_plus.dotp_quality_planning where CHAR_LENGTH(quality_policies)<=150 and project_id=11;
    $q = new DBQuery();
    $q->addQuery("qp.id");
    $q->addTable("quality_planning", "qp");
    $q->addWhere("CHAR_LENGTH(quality_policies)<=150 and project_id=" . $projectId);
    $sql = $q->prepare();
    $records = db_loadList($sql);
    if (count($records) > 0) {
        return true;
    }
    return false;
}

function thereIsNonConfiguredHR($projectId) {
    $project = new CProject();
    $project->load($projectId);
    $company_id = $project->project_company;
    $query = new DBQuery();
    $query->addTable("users", "u");
    $query->addQuery("u.user_id, u.user_username, contact_last_name, contact_first_name, contact_id");
    $query->addJoin("contacts", "c", "u.user_contact = c.contact_id", "inner");
    $query->addJoin("monitoring_user_cost", "hrc", "hrc.user_id=u.user_id", "left");
    $query->addWhere("c.contact_company = " . $company_id . "  and hrc.cost_value is NULL");
    $sql = $query->prepare();
    $records = db_loadList($sql);
    if (count($records) > 0) {
        return true;
    }
    return false;
}

function thereIsHourRateCostTooLow($projectId) {
    $project = new CProject();
    $project->load($projectId);
    $company_id = $project->project_company;
    
    $query = new DBQuery();
    $query->addTable("users", "u");
    $query->addQuery("u.user_id, u.user_username, contact_last_name, contact_first_name, contact_id");
    $query->addJoin("contacts", "c", "u.user_contact = c.contact_id", "inner");
    $query->addJoin("monitoring_user_cost", "hrc", "hrc.user_id=u.user_id", "left");
    $query->addWhere("c.contact_company = " . $company_id . "  and hrc.cost_value <5");
    $sql = $query->prepare();
    $records = db_loadList($sql);
    if (count($records) > 0) {
        return true;
    }
    
    return false;
}

function thereIsNonHumanResources($projectId) {
    require_once DP_BASE_DIR . "/modules/costs/costs_functions.php";
    $whereProject = ' and cost_project_id=' . $projectId;
    $notHumanCost = getResources("Non-Human", $whereProject);
    if (sizeof($notHumanCost) > 0) {
        return true;
    }
    return false;
}

function thereIsAssuranceItemsMethodAsTest($projectId) {
    /*
      SELECT * FROM dotproject_plus.dotp_quality_assurance_item qi
      inner join dotp_quality_planning qp on qp.id=qi.quality_planning_id
      where  ucase(qi.how) like '%TESTE%' and qp.project_id=11
     */
    $query = new DBQuery();
    $query->addQuery("qp.id");
    $query->addTable("quality_assurance_item", "qi");
    $query->addJoin("quality_planning", "qp", "qp.id=qi.quality_planning_id", "inner");
    $query->addWhere("ucase(qi.how) like '%TEST%' and qp.project_id=" . $projectId);
    $sql = $query->prepare();
    $records = db_loadList($sql);
    if (count($records) > 0) {
        return true;
    }
    return false;
}

function thereIsContingenceReserve($projectId) {
    $q = new DBQuery();
    $q->addQuery('*');
    $q->addTable('budget_reserve', 'b');
    $q->addWhere("budget_reserve_project_id = " . $projectId);
    $q->addOrder('budget_reserve_risk_id');
    $sql = $q->prepare();
    $records = db_loadList($sql);
    if (count($records) > 0) {
        return true;
    }
    return false;
}

function getCommunicationCount($projectId) {
    $q = new DBQuery();
    $q->addQuery('communication_id');
    $q->addTable('communication');
    $q->addWhere("communication_project_id= " . $projectId);
    $sql = $q->prepare();
    $records = db_loadList($sql);
    return count($records);
}

function thereIsComunicationForProjectPlan($projectId) {
    $q = new DBQuery();
    $q->addQuery('communication_id');
    $q->addTable('communication');
    $q->addWhere("communication_project_id= " . $projectId . " and (ucase(communication_title) like '%PLANO%' and ucase(communication_title) like '%PROJETO%')");
    $sql = $q->prepare();
    $records = db_loadList($sql);
    return count($records);
}

function thereIsRiskResponse($projectId, $responseType) {
    $q = new DBQuery();
    $q->addQuery('risk_id');
    $q->addTable('risks', 'r');
    $q->addWhere("risk_project = " . $projectId . " and risk_strategy=$responseType");
    $sql = $q->prepare();
    $records = db_loadList($sql);
    if (count($records) > 0) {
        return true;
    }
    return false;
}

function thereIsRiskResponsePlanForLowExpositionFactor($projectId) {
    $q = new DBQuery();
    $q->addQuery('risk_id');
    $q->addTable('risks', 'r');
    $q->addWhere("risk_project = " . $projectId . " and risk_priority=0 and ((risk_contingency_plan <> '' ) or (risk_prevention_actions <> ''))");
    $sql = $q->prepare();
    //echo $sql;
    $records = db_loadList($sql);
    if (count($records) > 0) {
        return true;
    }
    return false;
}

function thereIsRiskToBeContingencied($projectId) {
// SELECT risk_id FROM dotproject_plus.dotp_risks where risk_project=11 and risk_is_contingency=1;
    $q = new DBQuery();
    $q->addQuery('risk_id');
    $q->addTable('risks', 'r');
    $q->addWhere("risk_project = " . $projectId . " and risk_is_contingency=1");
    $sql = $q->prepare();
//echo $sql;
    $records = db_loadList($sql);
    if (count($records) > 0) {
        return true;
    }
    return false;
}

function thereIsMissingMetricsInQualityPlan($projectId) {
    /*
      SELECT qp.id, qr.id, qg.id FROM dotproject_plus.dotp_quality_control_requirement qr
      inner join dotp_quality_planning qp on qp.id=qr.quality_planning_id
      left join dotp_quality_control_goal qg on qg.quality_planning_id=qr.quality_planning_id
      where qp.project_id=11 and qg.id is NULL
     */
    $q = new DBQuery();
    $q->addQuery('qp.id, qr.id, qg.id');
    $q->addTable('quality_control_requirement', 'qr');
    $q->addJoin("quality_planning", "qp", "qp.id=qr.quality_planning_id", "inner");
    $q->addJoin("quality_control_goal", "qg", "qg.quality_planning_id=qr.quality_planning_id", "left");
    $q->addWhere("qp.project_id=" . $projectId . " and qg.id is NULL");
    $sql = $q->prepare();
//echo $sql;
    $records = db_loadList($sql);
    if (count($records) > 0) {
        return true;
    }
    return false;
}

function getStakeholdersCount($projectId) {
    require_once DP_BASE_DIR . "/modules/initiating/initiating.class.php";
    $initiating = CInitiating::findByProjectId($projectId);
    if (!is_null($initiating)) {
        $initiating_id = $initiating->initiating_id;
        $q = new DBQuery();
        $q->addQuery("*");
        $q->addTable("initiating_stakeholder", "stk");
        $q->addJoin("initiating", "i", "i.initiating_id = stk.initiating_id");
        $q->addJoin("contacts", "c", "c.contact_id = stk.contact_id");
        $q->addWhere("i.initiating_id=" . $initiating_id);
        $q->addOrder("stk.initiating_id");
        $q->addOrder("stk.contact_id");
        $list = $q->loadList();
        return count($list);
    } else {
        return 0;
    }
}

function thereIsStakeholdersWithoutPowerInterestAnalysis($projectId) {
    require_once DP_BASE_DIR . "/modules/initiating/initiating.class.php";
    $initiating = CInitiating::findByProjectId($projectId);
    if (!is_null($initiating)) {
        $initiating_id = $initiating->initiating_id;
        $q = new DBQuery();
        $q->addQuery("*");
        $q->addTable("initiating_stakeholder", "stk");
        $q->addJoin("initiating", "i", "i.initiating_id = stk.initiating_id");
        $q->addJoin("contacts", "c", "c.contact_id = stk.contact_id");
        $q->addWhere("i.initiating_id=" . $initiating_id . " and (stk.stakeholder_strategy IS NULL or stk.stakeholder_strategy='')");
        $list = $q->loadList();
        if (count($list) > 0) {
            return true;
        } else {
            return false;
        }
    }
    return false;
}

function thereIsStakeholders($projectId) {
    require_once DP_BASE_DIR . "/modules/initiating/initiating.class.php";
    $initiating = CInitiating::findByProjectId($projectId);
    if (!is_null($initiating)) {
        $initiating_id = $initiating->initiating_id;
        $q = new DBQuery();
        $q->addQuery("*");
        $q->addTable("initiating_stakeholder", "stk");
        $q->addJoin("initiating", "i", "i.initiating_id = stk.initiating_id");
        $q->addJoin("contacts", "c", "c.contact_id = stk.contact_id");
        $q->addWhere("i.initiating_id=" . $initiating_id);
        $q->addOrder("stk.initiating_id");
        $q->addOrder("stk.contact_id");
        $list = $q->loadList();
        if (sizeof($list) > 0) {
            return true;
        }
    }
    return false;
}

function thereIsAuthorizedProjectCharter($projectId) {
    require_once DP_BASE_DIR . "/modules/initiating/initiating.class.php";
    $result = false;
    $initiating = CInitiating::findByProjectId($projectId);
    if (is_null($initiating)) {
        $result = false;
    } else if ($initiating->initiating_authorized == 1) {
        $result = true;
    }
    return $result;
}

function thereIsOverAllocatedResources($projectId) {
    /*
      SELECT t.task_id, t.task_start_date, t.task_end_date, er.role_id, count(hra.human_resource_id)
      FROM dotp_tasks t
      inner join dotp_project_tasks_estimated_roles er on er.task_id=t.task_id
      inner join dotp_human_resource_allocation hra on hra.project_tasks_estimated_roles_id=er.id
      where t.task_project=2
      group by hra.human_resource_id
      having count(hra.human_resource_id)>1
     */
    $q = new DBQuery();
    $q->addQuery("t.task_id, t.task_start_date, t.task_end_date, er.role_id, count(hra.human_resource_id)");
    $q->addTable("tasks", "t");
    $q->addJoin("project_tasks_estimated_roles", "er", "er.task_id=t.task_id", "inner");
    $q->addJoin("human_resource_allocation", "hra", "hra.project_tasks_estimated_roles_id=er.id", "inner");
    $q->addWhere("t.task_project=$projectId group by hra.human_resource_id having count(hra.human_resource_id)>1");
    $sql = $q->prepare();
//echo $sql;
    $records = db_loadList($sql);
    if (count($records) > 0) {
        return true;
    }
    return false;
}

?>