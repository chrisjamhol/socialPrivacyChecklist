<?php
include_once 'model/ChecklistPoint.php'; include_once 'model/DbObjects.php'; include_once 'model/Evaluable.php';

class Checklist extends DbObjects implements Evaluable{
    private $checklistPoints;

    public function __construct($id = false) {
        $this->properties = array("id"=>"","name"=>"");
        $this->coloums = array("checklistId","name");
        $this->table = "checklist";
        if($id != false){
            $dbData = $this->load($id)->fetch_object();
                $this->properties['name'] = $dbData->name;
                $this->properties['id'] = $dbData->checklistId;   
                $this->getChecklistPoints($id);                
        }else{
            $this->createNewChecklist();
        }
        $this->count();
    }   
    
    /**
     * creates ChecklistPoints
     * @param type $id 
     */
    private function getChecklistPoints($id){
        $countPointsSql = "SELECT COUNT(`checklistPointId`) FROM `checklist_checklistpoints` WHERE `checklistId` = ".$id;
        $pointsCount = $this->select($countPointsSql)->fetch_object();
        
        $getPointsSql = "SELECT `checklistPointId` FROM `checklist_checklistpoints` WHERE `checklistId` = ".$id." ORDER BY `position`";
        $points = $this->select($getPointsSql);
        while($row = $points->fetch_object()){
            $point = new ChecklistPoint($row->checklistPointId);
            $this->checklistPoints .= $point->returnAsHtmlFront();
        }        
    }
    
    private function createNewChecklist(){
            //get next free id
        $takenIds = $this->select("SELECT `checklistId` FROM `checklist` ORDER BY `checklistId`");            
        $freeTry = 1;
        while($row = $takenIds->fetch_object()){
            if($row->checklistId != $freeTry){
                $freeId = $freeTry;
            }else{ $freeTry++; }
        }
        isset($freeId) ? $freeId : $freeId = $freeTry++;
        $this->properties['id'] = $freeId;
    }

    /**
     * returns the checklist with checklistPoints 
     * 
     * @return string checklistPoints 
     */
    public function returnAsHtml(){
        return $this->checklistPoints;
    }    
    
    /**
     * setter
     * @param type $key
     * @param type $value 
     */
    public function setValue($key, $value){
         $this->properties[$key] = $value;
    }
    
    public function count(){
        if($_GET['from'] == "front" || $_POST['from'] == "front"){
            if($_GET['op'] == "createChecklistView" || $_POST['op'] == "createChecklistView"){
                if(isset($_SESSION['checklistView'])){    
                    if(strpos($_SESSION['checklistView'],$_GET['id']) === false){
                        $_SESSION['checklistView'] = $_SESSION['checklistView']."|".$_GET['id'];            
                        $this->select("UPDATE `checklist` SET `hits` = (`hits`+1) WHERE `checklistId` = ".$_GET['id']);
                    }else{

                    }
                }else{
                    $_SESSION['checklistView'] = $_GET['id'];
                    $this->select("UPDATE `checklist` SET `hits` = (`hits`+1) WHERE `checklistId` = ".$_GET['id']);
                }        
            }
        }      
    }
}

?>
