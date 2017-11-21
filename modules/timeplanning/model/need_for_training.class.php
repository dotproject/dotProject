<?php
class NeedForTraining   {
    private $id=null;
    private $description=null;
    
    public function getId(){
        return $this->id;
    }
    public function getDescription(){
        return $this->description;
    }
    
    public function setId($id){
        $this->id=$id;
    }
    
    public function setDescription($description){
        $this->description=$description;
    }
    
    public function store(){
        $q = new DBQuery();
        $q->addQuery("t.project_id");
        $q->addTable("need_for_training", "t");
        $q->addWhere("t.project_id= " . $this->id);
        $sql = $q->prepare();
        $records = db_loadList($sql);
        $q = new DBQuery();
        $q->addTable("need_for_training");
        if (sizeof($records) > 0) {
            $q->addUpdate("description", $this->description);
            $q->addWhere("project_id =" . $this->id);
        } else {
            $q->addInsert("description", $this->description);
            $q->addInsert("project_id", $this->id);
        }
        $q->exec();
    }
    
    public function load($id){
        $q = new DBQuery();
        $q->addQuery("t.project_id,t.description");
        $q->addTable("need_for_training", "t");
        $q->addWhere("t.project_id= " . $id);
        $sql = $q->prepare();
        $records = db_loadList($sql);
        foreach ($records as $record) {
            $this->id = $record["project_id"];
            $this->description = $record["description"];
        }
    }
}
?>
