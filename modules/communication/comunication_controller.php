<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ComunicationController
 *
 * @author rafael
 */
class ComunicationController {
    
    public function channelAlreadyExists($channel){
        $sql="SELECT  FROM dotp_ where ucase(communication_channel) = ucase(".  ($channel). ")";
        $q = new DBQuery();
        $q->addQuery("communication_channel_id");
        $q->addTable("communication_channel");
        $q->addWhere("ucase(communication_channel) = ucase('" . ($channel) . "')");
        $sql = $q->prepare();

        $records= db_loadList($sql);
        return sizeof($records) >0?true:false;
    }
    
    public function frequencyAlreadyExists($frequency){
        $q = new DBQuery();
        $q->addQuery("communication_frequency_id");
        $q->addTable("communication_frequency");
        $q->addWhere("ucase(communication_frequency) = ucase('" . ($frequency) . "')");
        $sql = $q->prepare();
       
        $records= db_loadList($sql);
        return sizeof($records) >0?true:false;
    }
    
    public function channelIsBeenUtilized($channelId){
        $count=0;
        $q = new DBQuery();
        $q->addQuery("count(communication_channel_id)");
        $q->addTable("communication");
        $q->addWhere("communication_channel_id=" . ($channelId));
        $sql = $q->prepare();
        $records= db_loadList($sql);
        foreach($records as $record){
           $count=$record[0];
        }
        return $count;
    }
    
    public function frequencyIsBeenUtilized($frequencyId){
        $count=0;
        $q = new DBQuery();
        $q->addQuery("count(communication_frequency_id)");
        $q->addTable("communication");
        $q->addWhere("communication_frequency_id=" . ($frequencyId));
        $sql = $q->prepare();
        $records= db_loadList($sql);
        foreach($records as $record){
           $count=$record[0];
        }
        return $count;
    }
    
}
