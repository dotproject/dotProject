<?php
class WBSDictionaryEntry   {
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
        $q->addQuery("dic.wbs_item_id");
        $q->addTable("wbs_dictionary", "dic");
        $q->addWhere("dic.wbs_item_id= " . $this->id);
        $sql = $q->prepare();
        $tasksEstimations = db_loadList($sql);
        $q = new DBQuery();
        $q->addTable("wbs_dictionary");
        if (sizeof($tasksEstimations) > 0) {
            $q->addUpdate("description", $this->description);
            $q->addWhere("wbs_item_id =" . $this->id);
        } else {
            $q->addInsert("description", $this->description);
            $q->addInsert("wbs_item_id", $this->id);
        }
        $q->exec();
    }
    
    public function load($id){
        $q = new DBQuery();
        $q->addQuery("dic.wbs_item_id,dic.description");
        $q->addTable("wbs_dictionary", "dic");
        $q->addWhere("dic.wbs_item_id= " . $id);
        $sql = $q->prepare();
        $records = db_loadList($sql);
        foreach ($records as $record) {
            $this->id = $record["wbs_item_id"];
            $this->description = $record["description"];
        }
    }
}
?>
