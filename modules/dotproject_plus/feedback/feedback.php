<?php

class Feedback {
    private $id = -1;
    private $short = "";
    private $description = "";
    private $generic=false;
    private $knowledgeArea="";
    private $isTriggerFiredForCurrentUser=false; //this attribute is initialized after each page refresh/load in the /modules/dotproject_plus/feedback_controller.php
    private $descriptionForAssignment="";
    private $isForAssignment=false;//this attribute makes faster to evaluate if it is to be evaluated or not

    
    function Feedback($id, $short,$description, $generic, $knowledgeArea){
        $this->id=$id;
        $this->short=$short;
        $this->description=$description;
        $this->generic=$generic;
        $this->knowledgeArea=$knowledgeArea;
    }
    
    function getKnowledgeArea() {
        return $this->knowledgeArea;
    }

    function setKnowledgeArea($knowledgeArea) {
        $this->knowledgeArea = $knowledgeArea;
    }

        
    function getGeneric() {
        return $this->generic;
    }

    function setGeneric($generic) {
        $this->generic = $generic;
    }
    
    function getId() {
        return $this->id;
    }

    function getShort() {
        return $this->short;
    }

    function getDescription() {
        return $this->description;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setShort($short) {
        $this->short = $short;
    }

    function setDescription($description) {
        $this->description = $description;
    }
    
    function getIsTriggerFiredForCurrentUser() {
        return $this->isTriggerFiredForCurrentUser;
    }

    function setIsTriggerFiredForCurrentUser($isTriggerFiredForCurrentUser) {
        $this->isTriggerFiredForCurrentUser = $isTriggerFiredForCurrentUser;
    }

    function getDescriptionForAssignment() {
        return $this->descriptionForAssignment;
    }
    function setDescriptionForAssignment($descriptionForAssignment) {
        $this->descriptionForAssignment = $descriptionForAssignment;
        $this->isForAssignment=true;
    }

    function getIsForAssignment() {
        return $this->isForAssignment;
    }
}
?>

