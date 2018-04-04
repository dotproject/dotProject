<?php
 
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly');
}
require_once (DP_BASE_DIR . '/modules/tasks/tasks.class.php');

class ActivityMDP {

    private $id = NULL;
    private $name = NULL;
    private $dependencies = NULL;

    function ActivityMDP() {
        
    }

    function updateDependencies($taskId, $dependencies) {
        $obj = new CTask();
        $obj->load($taskId);
        $obj->updateDependencies($dependencies);
    }


    function load($id, $name, $dependencies) {
        $this->id = $id;
        $this->name = $name;
        $this->dependencies = $dependencies;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getDependencies() {
        return $this->dependencies;
    }

    public function setDependencies($dependencies) {
        $this->dependencies = $dependencies;
    }

}