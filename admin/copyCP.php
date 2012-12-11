<?php
    include 'model/DbConnector.php';
    $db = DbConnector::getInstance()->connect();
    $highesId = 43;
    $nextId = $highesId+1;
    $origChecklist = 1;
    $copieChecklist = 2;
    
    echo "SELECT `c.checklistPointId`,`c.text`,`c.heading` FROM checklistpoints AS c,checklist_checklistpoints AS cc WHERE cc.checklistPoint = ".$origChecklist;
    $clpIds = $db->query("SELECT `c.checklistPointId`,`c.text`,`c.heading` FROM `checklistpoints` AS c,`checklist_checklistpoints` AS cc WHERE cc.checklistPoint = ".$origChecklist);
    
    while($row = $clpIds->fetch_object()){
        $clp = new checklistPoint($row,$nextId);
        $nextId++;
    }
    
    class checklistPoint{
        public function __construct($row,$id){
            $this->data = $row;
            $this->double($id);
        }
        
        private function double($id){
            echo 'INSERT INTO `checklistpoints`(`checklistPointId`, `text`, `heading`) VALUES ('.$id.',"'.$this->data->text.'","'.$this->data->heading.'")<br />';
            echo '';
            //$db->query('INSERT INTO `checklistpoints`(`checklistPointId`, `text`, `heading`) VALUES ('.$data->checklistPointId.',"'.$data->text.'","'.$data->heading.'")');
            $nextId++;
        }
    }
?>