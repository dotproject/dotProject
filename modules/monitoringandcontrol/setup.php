<?php
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly');
}

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'Monitoring and Controlling';
$config['mod_version'] = '1.0';
$config['mod_directory'] = 'monitoringandcontrol';
$config['mod_setup_class'] = 'CSetup_MonitoringAndControl';
$config['mod_type'] = 'user';
$config['mod_config'] = false;
$config['mod_ui_name'] = 'Monitoring and Controlling';
$config['mod_ui_icon'] = 'graph-up.png';
$config['mod_description'] = "Monitoring and Controlling";

if (@$a == 'setup') {
	echo dPshowModuleConfig( $config );
}

class CSetup_MonitoringAndControl{   

	function install() {
		$this->updateTranslationFiles();
		
		//table monitoring_baseline
		$sql = "(
		  `baseline_id` INTEGER(11) NOT NULL AUTO_INCREMENT,
		  `project_id` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
		  `baseline_name` VARCHAR(255) COLLATE latin1_swedish_ci DEFAULT NULL,
		  `baseline_version` VARCHAR(255) COLLATE latin1_swedish_ci DEFAULT NULL,
		  `baseline_observation` TEXT COLLATE latin1_swedish_ci,
		  `user_id` INTEGER(11) NOT NULL,
		  `baseline_date` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
		  PRIMARY KEY (`baseline_id`)
		)ENGINE=InnoDB";		
		$q = new DBQuery;
		$q->createTable('monitoring_baseline');
		$q->createDefinition($sql);
		$q->exec();
		$q->clear();	
		
		//table monitoring_baseline_task
		$sql = "(
		  `baseline_task_id` INTEGER(11) NOT NULL AUTO_INCREMENT,
		  `baseline_id` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
		  `task_id` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
		  `task_start_date` DATETIME DEFAULT NULL,
		  `task_duration` FLOAT UNSIGNED DEFAULT '0',
		  `task_duration_type` INTEGER(11) NOT NULL DEFAULT '1',
		  `task_hours_worked` FLOAT UNSIGNED DEFAULT '0',
		  `task_end_date` DATETIME DEFAULT NULL,
		  `task_percent_complete` TINYINT(4) DEFAULT NULL,
		  PRIMARY KEY (`baseline_task_id`)
		)ENGINE=InnoDB";		
		$q = new DBQuery;
		$q->createTable('monitoring_baseline_task');
		$q->createDefinition($sql);
		$q->exec();
		$q->clear();			

		//table monitoring_baseline_task_log
		$sql = "(
		  `baseline_task_id_log` INTEGER(11) NOT NULL AUTO_INCREMENT,
		  `baseline_task_id` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
		  `task_log_id` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
		  `task_log_creator` INTEGER(11) NOT NULL DEFAULT '0',
		  `task_log_hours` FLOAT NOT NULL DEFAULT '0',
		  `task_log_date` DATETIME DEFAULT NULL,
		  PRIMARY KEY (`baseline_task_id_log`)
		)ENGINE=InnoDB";		
		$q = new DBQuery;
		$q->createTable('monitoring_baseline_task_log');
		$q->createDefinition($sql);
		$q->exec();
		$q->clear();	

		//table monitoring_baseline_user_cost
		$sql = "(
		  `baseline_cost_id` INTEGER(11) NOT NULL AUTO_INCREMENT,
		  `baseline_id` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
		  `cost_id` INTEGER(11) UNSIGNED NOT NULL DEFAULT '0',
		  `user_id` INTEGER(11) UNSIGNED NOT NULL,
		  `cost_value` DECIMAL(10,2) DEFAULT '0.00',
		  `cost_per_use` DECIMAL(11,0) DEFAULT NULL,
		  `cost_dt_begin` DATETIME NOT NULL,
		  `cost_dt_end` DATETIME DEFAULT NULL,
		  PRIMARY KEY (`baseline_cost_id`)
		)ENGINE=InnoDB";
		$q = new DBQuery;
		$q->createTable('monitoring_baseline_user_cost');
		$q->createDefinition($sql);
		$q->exec();
		$q->clear();	
  
		//table monitoring_change_request
		$sql = "(
		  `change_id` INTEGER(10) NOT NULL AUTO_INCREMENT,
		  `task_id` INTEGER(10) DEFAULT '0',
		  `change_impact` INTEGER(11) NOT NULL DEFAULT '0',
		  `change_status` INTEGER(11) DEFAULT NULL,
		  `change_description` TEXT COLLATE latin1_swedish_ci NOT NULL,
		  `change_cause` TEXT COLLATE latin1_swedish_ci NOT NULL,
		  `change_request` TEXT COLLATE latin1_swedish_ci NOT NULL,
		  `user_id` INTEGER(10) NOT NULL DEFAULT '0',
		  `change_date_limit` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `meeting_id` INTEGER(10) UNSIGNED DEFAULT NULL,
		  `project_id` INTEGER(10) NOT NULL,
		  PRIMARY KEY (`change_id`)
		)ENGINE=InnoDB";
		$q = new DBQuery;
		$q->createTable('monitoring_change_request');
		$q->createDefinition($sql);
		$q->exec();
		$q->clear();			

		//table monitoring_meeting
		$sql = "(
		  `meeting_id` INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		  `project_id` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
		  `dt_meeting_begin` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `ds_title` TEXT COLLATE latin1_swedish_ci NOT NULL,
		  `ds_subject` TEXT COLLATE latin1_swedish_ci NOT NULL,
		  `dt_meeting_end` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `meeting_type_id` INTEGER(10) NOT NULL,
		  PRIMARY KEY (`meeting_id`)
		)ENGINE=InnoDB";
		$q = new DBQuery;
		$q->createTable('monitoring_meeting');
		$q->createDefinition($sql);
		$q->exec();
		$q->clear();	

		//table monitoring_meeting_item
		$sql = "(
		  meeting_item_id int NOT NULL AUTO_INCREMENT,
		  meeting_item_description TEXT,
                  knownledge_area TEXT,
                  language TEXT,
                  item_order int,
		  PRIMARY KEY (meeting_item_id)
		)";
		$q = new DBQuery;
		$q->createTable("monitoring_meeting_item");
		$q->createDefinition($sql);
		$q->exec();
		$q->clear();	
		// Insert default items to monitoring meeting checklist
                //Check list in english
                $this->addCheckListItem("Is the use and communication of data following the plan?", "comunication", "EN", 1);
                $this->addCheckListItem("Is the schedule being carried out according to plan?", "time", "EN", 1);
                $this->addCheckListItem("Is the stakeholder involvement following the plan?", "stakeholder", "EN", 1);
                $this->addCheckListItem("Were there changes in the risks?", "risk", "EN", 1);
		$this->addCheckListItem("There is new risks?", "risk", "EN", 2);
                $this->addCheckListItem("Some contingency action has been carried out?", "risk", "EN", 3);
                $this->addCheckListItem("The contingency reserves are been used as planned?", "risk", "EN", 4);
                $this->addCheckListItem("The planned risks' responses are effectiveness to solve the risk root cause?", "risk", "EN", 5);
		$this->addCheckListItem("Are the costs being carried out according to plan?", "cost", "EN", 2);
                
		//Checklist in portuguese
                $this->addCheckListItem("Os dados do projeto estão seguindo o plano de comunicação?", "comunication", "pt_br", 1);
                $this->addCheckListItem("O cronograma está sendo executado de acordo com o plano?", "time", "pt_br", 1);
                $this->addCheckListItem("O envolvimento dos stakeholders está seguindo o plano?", "stakeholder", "pt_br", 1);
                $this->addCheckListItem("Houve alguma alteração nos riscos?", "risk", "pt_br", 1);
		$this->addCheckListItem("Foram identificados novos riscos?", "risk", "pt_br", 2);
                $this->addCheckListItem("Alguma ação de contingência foi executada?", "risk", "pt_br", 3);
                $this->addCheckListItem("As reservas de contingência estão sendo aplicadas de acordo com o planejado?", "risk", "pt_br", 4);
		$this->addCheckListItem("As ações de resposta aos riscos planejadas foram eficazes na correção da causa raíz do risco?", "risk", "pt_br", 5);
                $this->addCheckListItem("Os custos do projeto estão ocorrendo de acordo com o plano?", "cost", "pt_br", 2);	
                
		//table monitoring_meeting_item_select
		$sql = "(
		  `meeting_item_select_id` INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		  `meeting_item_id` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
		  `meeting_id` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
		  `status` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
		  PRIMARY KEY (`meeting_item_select_id`)
		)ENGINE=InnoDB";
		$q = new DBQuery;
		$q->createTable('monitoring_meeting_item_select');
		$q->createDefinition($sql);
		$q->exec();
		$q->clear();		
		
		//table monitoring_meeting_item_senior
		$sql = "(
		  `meeting_item_senior_id` INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		  `meeting_percentual` DECIMAL(10,2) DEFAULT '0.00',
		  `meeting_size` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
		  `meeting_idc` DECIMAL(10,2) DEFAULT '0.00',
		  `meeting_idp` DECIMAL(10,2) DEFAULT '0.00',
		  `meeting_vp` DECIMAL(10,2) DEFAULT '0.00',
		  `meeting_va` DECIMAL(10,2) DEFAULT '0.00',
		  `meeting_cr` DECIMAL(10,2) DEFAULT '0.00',
		  `meeting_baseline` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
		  `meeting_id` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
		  PRIMARY KEY (`meeting_item_senior_id`)
		)ENGINE=InnoDB";
		$q = new DBQuery;
		$q->createTable('monitoring_meeting_item_senior');
		$q->createDefinition($sql);
		$q->exec();
		$q->clear();		
				
		//table monitoring_meeting_item_tasks_delivered
		$sql = "(
		  `meeting_item_taks_delivered_id` INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		  `task_id` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
		  `meeting_id` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
		  PRIMARY KEY (`meeting_item_taks_delivered_id`)
		)ENGINE=InnoDB";
		$q = new DBQuery;
		$q->createTable('monitoring_meeting_item_tasks_delivered');
		$q->createDefinition($sql);
		$q->exec();
		$q->clear();						
				
		//table monitoring_meeting_type
		$sql = "(
		  `meeting_type_id` INTEGER(11) NOT NULL AUTO_INCREMENT,
		  `meeting_type_name` VARCHAR(255) COLLATE latin1_swedish_ci DEFAULT NULL,
		  PRIMARY KEY (`meeting_type_id`)
		)ENGINE=InnoDB";
		$q = new DBQuery;
		$q->createTable('monitoring_meeting_type');
		$q->createDefinition($sql);
		$q->exec();
		$q->clear();	
		
		$q = new DBQuery();
		$q-> addTable('monitoring_meeting_type');
		$q->addInsert('meeting_type_id', 1);
		$q->addInsert('meeting_type_name', 'Standard');
		$q->exec();						
				
		$q = new DBQuery();
		$q-> addTable('monitoring_meeting_type');
		$q->addInsert('meeting_type_id', 2);
		$q->addInsert('meeting_type_name', 'Delivery');
		$q->exec();						
		
		$q = new DBQuery();
		$q-> addTable('monitoring_meeting_type');
		$q->addInsert('meeting_type_id', 3);
		$q->addInsert('meeting_type_name', 'Monitoring');
		$q->exec();						
		
		$q = new DBQuery();
		$q-> addTable('monitoring_meeting_type');
		$q->addInsert('meeting_type_id', 4);
		$q->addInsert('meeting_type_name', 'Status Report');
		$q->exec();						
		
		$q = new DBQuery();
		$q-> addTable('monitoring_meeting_type');
		$q->addInsert('meeting_type_id', 5);
		$q->addInsert('meeting_type_name', 'Monitoring / Status Report');
		$q->exec();																		
				
		//table monitoring_meeting_user
		$sql = "(
		  `meeting_user_id` INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		  `user_id` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
		  `meeting_id` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
		  PRIMARY KEY (`meeting_user_id`)
		)ENGINE=InnoDB";
		$q = new DBQuery;
		$q->createTable('monitoring_meeting_user');
		$q->createDefinition($sql);
		$q->exec();
		$q->clear();				
												
		//table monitoring_quality
		$sql = "(
		  `quality_id` INTEGER(11) NOT NULL AUTO_INCREMENT,
		  `quality_type_id` INTEGER(11) NOT NULL,
		  `quality_description` TEXT COLLATE latin1_swedish_ci,
		  `user_id` INTEGER(11) DEFAULT NULL,
		  `quality_status_id` INTEGER(11) NOT NULL,
		  `quality_date_end` DATETIME DEFAULT NULL,
		  `task_id` INTEGER(11) NOT NULL,
		  PRIMARY KEY (`quality_id`)
		)ENGINE=InnoDB";
		$q = new DBQuery;
		$q->createTable('monitoring_quality');
		$q->createDefinition($sql);
		$q->exec();
		$q->clear();
		
		//table monitoring_quality_status
		$sql = "(
		  `quality_status_id` INTEGER(11) NOT NULL AUTO_INCREMENT,
		  `quality_status_name` VARCHAR(255) COLLATE latin1_swedish_ci DEFAULT NULL,
		  PRIMARY KEY (`quality_status_id`)
		)ENGINE=InnoDB";
		$q = new DBQuery;
		$q->createTable('monitoring_quality_status');
		$q->createDefinition($sql);
		$q->exec();
		$q->clear();	
		
		$q = new DBQuery();
		$q-> addTable('monitoring_quality_status');
		$q->addInsert('quality_status_id', 1);
		$q->addInsert('quality_status_name', 'Pending');
		$q->exec();			
		
		$q = new DBQuery();
		$q-> addTable('monitoring_quality_status');
		$q->addInsert('quality_status_id', 2);
		$q->addInsert('quality_status_name', 'Concluded');
		$q->exec();			
		
		$q = new DBQuery();
		$q-> addTable('monitoring_quality_status');
		$q->addInsert('quality_status_id', 3);
		$q->addInsert('quality_status_name', 'Canceled');
		$q->exec();			
						
		//table monitoring_quality_type
		$sql = "(
		  `quality_type_id` INTEGER(11) NOT NULL AUTO_INCREMENT,
		  `quality_type_name` VARCHAR(255) COLLATE latin1_swedish_ci DEFAULT NULL,
		  PRIMARY KEY (`quality_type_id`)
		)ENGINE=InnoDB";
		$q = new DBQuery;
		$q->createTable('monitoring_quality_type');
		$q->createDefinition($sql);
		$q->exec();
		$q->clear();	
		
		$q = new DBQuery();
		$q-> addTable('monitoring_quality_type');
		$q->addInsert('quality_type_id', 1);
		$q->addInsert('quality_type_name', 'Logical Error');
		$q->exec();												
		
		$q = new DBQuery();
		$q-> addTable('monitoring_quality_type');
		$q->addInsert('quality_type_id', 2);
		$q->addInsert('quality_type_name', 'Business Error');
		$q->exec();											
		
		$q = new DBQuery();
		$q-> addTable('monitoring_quality_type');
		$q->addInsert('quality_type_id', 3);
		$q->addInsert('quality_type_name', 'Analysis Error');
		$q->exec();															
		
		//table monitoring_responsibility_matriz
		$sql = "(
		  `responsibility_id` INTEGER(11) NOT NULL AUTO_INCREMENT,
		  `responsibility_description` VARCHAR(255) COLLATE latin1_swedish_ci DEFAULT NULL,
		  `responsibility_user_id_consultation` INTEGER(11) DEFAULT NULL,
		  `responsibility_user_id_execut` INTEGER(11) DEFAULT NULL,
		  `responsibility_user_id_support` INTEGER(11) DEFAULT NULL,
		  `responsibility_user_id_approve` INTEGER(11) DEFAULT NULL,
		  `project_id` INTEGER(11) NOT NULL,
		  PRIMARY KEY (`responsibility_id`)
		)ENGINE=InnoDB";
		$q = new DBQuery;
		$q->createTable('monitoring_responsibility_matriz');
		$q->createDefinition($sql);
		$q->exec();
		$q->clear();					

		//table monitoring_user_cost
		$sql = "(
		  `cost_id` INTEGER(11) NOT NULL AUTO_INCREMENT,
		  `user_id` INTEGER(11) NOT NULL,
		  `cost_value` DECIMAL(10,2) DEFAULT '0.00',
		  `cost_per_use` DECIMAL(11,0) DEFAULT NULL,
		  `cost_dt_begin` DATETIME NOT NULL,
		  `cost_dt_end` DATETIME DEFAULT NULL,
		  PRIMARY KEY (`cost_id`)
		)ENGINE=InnoDB";
		$q = new DBQuery;
		$q->createTable('monitoring_user_cost');
		$q->createDefinition($sql);
		$q->exec();
		$q->clear();

		return true;
	}

        private function addCheckListItem($item,$area,$language, $order){            
            $q = new DBQuery();
            $q-> addTable("monitoring_meeting_item");
            $q->addInsert("knownledge_area",$area);
            $q->addInsert("language", $language);
            $q->addInsert("item_order",$order);
            $q->addInsert("meeting_item_description", $item);
            $q->exec();	
        }
        
	private function updateTranslationFiles(){
            return true;
		//update translations
		//1. Define translations values
		$projectsEn="\n".'"1LBL_BASELINE"=>"Baseline",'."\n".'"2LBLRESPONSABILIDADE"=>"Responsability",'."\n".'"3LBLACAOCORRETIVA"=>"Change Request",'."\n".'"4LBLATA"=>"Meeting",'."\n".'"5LBLCUSTO"=>"Cost",'."\n".'"6LBLCRONOGRAMA"=>"Schedule",'."\n".'"7LBLQUALIDADE"=>"Quality",';
		$projectsBr="\n".'"1LBL_BASELINE"=>"Linha de Base",'."\n".'"2LBLRESPONSABILIDADE"=>"Responsabilidade",'."\n".'"3LBLACAOCORRETIVA"=>"Acao Corretiva",'."\n".'"4LBLATA"=>"Ata",'."\n".'"5LBLCUSTO"=>"Custo",'."\n".'"6LBLCRONOGRAMA"=>"Cronograma",'."\n".'"7LBLQUALIDADE"=>"Qualidade",';
					
		$companiesEn="\n".'"1LBLMONITORAMENTO"=>"Monitoring",';
		$companiesBr="\n".'"1LBLMONITORAMENTO"=>"Monitoramento",';
		
		$projectTasksEn="\n".'"3LBLACAOCORRETIVA"=>"Change Request",'."\n".'"7LBLQUALIDADE"=>"Quality",';
		$projectTasksBr="\n".'"3LBLACAOCORRETIVA"=>"Acao Corretiva",'."\n".'"7LBLQUALIDADE"=>"Qualidade",';
		
		$adminEn="\n".'"5LBLCUSTO"=>"Cost",';
		$adminBr="\n".'"5LBLCUSTO"=>"Custo",';
		
		//2. Define file names
		$fileProjectEn=DP_BASE_DIR.'\\locales\\en\\projects.inc';
		$fileProjectBr=DP_BASE_DIR.'\\locales\\pt_br\\projects.inc';

		$fileCompanyEn=DP_BASE_DIR.'\\locales\\en\\companies.inc';
		$fileCompanyBr=DP_BASE_DIR.'\\locales\\pt_br\\companies.inc';
		
		$fileTaskEn=DP_BASE_DIR.'\locales\en\tasks.inc';
		$fileTaskBr=DP_BASE_DIR.'\locales\pt_br\tasks.inc';
		
		$fileAdminEn=DP_BASE_DIR.'\locales\en\admin.inc';
		$fileAdminBr=DP_BASE_DIR.'\locales\pt_br\admin.inc';		

		//3. Update translations files
		$this->updateFile($fileProjectEn,$projectsEn);
		$this->updateFile($fileProjectBr,$projectsBr);

		$this->updateFile($fileCompanyEn,$companiesEn);
		$this->updateFile($fileCompanyBr,$companiesBr);
		
		$this->updateFile($fileTaskEn,$projectTasksEn);
		$this->updateFile($fileTaskBr,$projectTasksBr);
		
		$this->updateFile($fileAdminEn,$adminEn);
		$this->updateFile($fileAdminBr,$adminBr);		
	}	
	
	private function updateFile($fileName,$content){
		if(!file_exists($fileName)){
			$fileName=str_replace("\\","/",$fileName);
		}
		$fp = fopen($fileName, 'a');
		fwrite($fp, $content);
		fclose($fp);
	}	

	function remove() {
		//table monitoring_baseline
		$q = new DBQuery;
		$q->dropTable('monitoring_baseline');
		$q->exec();
		$q->clear();		
		
		//table monitoring_baseline_task
		$q = new DBQuery;
		$q->dropTable('monitoring_baseline_task');
		$q->exec();
		$q->clear();		
		
		//table monitoring_baseline_task_log
		$q = new DBQuery;
		$q->dropTable('monitoring_baseline_task_log');
		$q->exec();
		$q->clear();		
		
		//table monitoring_baseline_user_cost
		$q = new DBQuery;
		$q->dropTable('monitoring_baseline_user_cost');
		$q->exec();
		$q->clear();		
		
		//table monitoring_change_request
		$q = new DBQuery;
		$q->dropTable('monitoring_change_request');
		$q->exec();
		$q->clear();		
		
		//table monitoring_meeting
		$q = new DBQuery;
		$q->dropTable('monitoring_meeting');
		$q->exec();
		$q->clear();		
		
		//table monitoring_meeting_item
		$q = new DBQuery;
		$q->dropTable('monitoring_meeting_item');
		$q->exec();
		$q->clear();		
		
		//table monitoring_meeting_item_select
		$q = new DBQuery;
		$q->dropTable('monitoring_meeting_item_select');
		$q->exec();
		$q->clear();		
		
		//table monitoring_meeting_item_senior
		$q = new DBQuery;
		$q->dropTable('monitoring_meeting_item_senior');
		$q->exec();
		$q->clear();		
		
		//table monitoring_meeting_item_tasks_delivered
		$q = new DBQuery;
		$q->dropTable('monitoring_meeting_item_tasks_delivered');
		$q->exec();
		$q->clear();		
		
		//table monitoring_meeting_type
		$q = new DBQuery;
		$q->dropTable('monitoring_meeting_type');
		$q->exec();
		$q->clear();		
		
		//table monitoring_meeting_user
		$q = new DBQuery;
		$q->dropTable('monitoring_meeting_user');
		$q->exec();
		$q->clear();		
		
		//table monitoring_quality
		$q = new DBQuery;
		$q->dropTable('monitoring_quality');
		$q->exec();
		$q->clear();		
		
		//table monitoring_quality_status
		$q = new DBQuery;
		$q->dropTable('monitoring_quality_status');
		$q->exec();
		$q->clear();		
		
		//table monitoring_quality_type
		$q = new DBQuery;
		$q->dropTable('monitoring_quality_type');
		$q->exec();
		$q->clear();		
		
		//table monitoring_responsibility_matriz
		$q = new DBQuery;
		$q->dropTable('monitoring_responsibility_matriz');
		$q->exec();
		$q->clear();		
		
		//table monitoring_user_cost
		$q = new DBQuery;
		$q->dropTable('monitoring_user_cost');
		$q->exec();
		$q->clear();		

		return true;
	
	}

	function upgrade($version = 'all') {
		return true;
	}
	
	
	function configure(){
		return true;
	}
	
}


