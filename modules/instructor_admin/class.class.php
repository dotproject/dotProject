<?php
class CClass extends CDpObject {
  var $class_id = null;
  var $educational_institution = null;
  var $course = null;
  var $disciplin = null;
  var $instructor=null;
  var $semester=null;
  var $year=null;
  var $number_of_students = 0;
  
  function CClass() {
        parent::CDpObject('dpp_classes', 'class_id');
    }
  
    public function toString(){
        return $this->educational_institution ."/". $this->course ."/".$this->disciplin .$this->semester ."/". $this->year;
    }
    
  public static function getAllClasses() {
        $classes=array();
        $query = new DBQuery;
        $query->addTable('dpp_classes');
        $query->addQuery('class_id');
         $query->addOrder('year desc, semester desc');
        $sql = $query->prepare();
        
        $resultset = db_loadList($sql);
        foreach ($resultset as $record) {
            $obj=new CClass();
            $obj->load($record['class_id']);
            $classes[$record['class_id']]=$obj;
        }
        return $classes;
    }
    
    public function delete(){
        $return=0;
        if($this->class_id>0){
            $query = new DBQuery;
            $query->addTable('dpp_classes');
            $query->addWhere('class_id='.$this->class_id);
            $sql = $query->prepareDelete();
            $return=db_exec($sql);
     
        }
        return $return;
    }
}  
?>