<?php

if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly');
}
require_once (DP_BASE_DIR . '/modules/tasks/tasks.class.php');

class ActivityMDP {

    private $id = NULL;
    private $x = NULL;
    private $y = NULL;
    private $name = NULL;
    private $dependencies = NULL;

    function ActivityMDP() {
        
    }

    function updateDependencies($taskId, $dependencies) {
        $obj = new CTask();
        $obj->load($taskId);
        $obj->updateDependencies($dependencies);
    }

    function updatePosition($task_id, $x, $y) {
        $q = new DBQuery();
        $q->addQuery('id');
        $q->addTable('tasks_mdp');
        $q->addWhere('task_id =' . $task_id);
        $record = $q->loadResult();
        $q->clear();
        $q->addTable('tasks_mdp');
        if (empty($record)) {
            $q->addInsert('pos_x', $x);
            $q->addInsert('pos_y', $y);
            $q->addInsert('task_id', $task_id);
        } else {
            $q->addUpdate('pos_x', $x);
            $q->addUpdate('pos_y', $y);
            $q->addWhere('task_id =' . $task_id);
        }
        $q->exec();
    }

    function load($id, $name, $x, $y, $dependencies) {
        $this->id = $id;
        $this->name = $name;
        $this->x = $x;
        $this->y = $y;
        $this->dependencies = $dependencies;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getX() {
        return $this->x;
    }

    public function setX($x) {
        $this->x = $x;
    }

    public function getY() {
        return $this->y;
    }

    public function setY($y) {
        $this->y = $y;
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
