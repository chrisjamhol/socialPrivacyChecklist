<?php
include_once 'model/DbObjects.php'; include_once 'model/Evaluable.php';

class ChecklistPoint extends DbObjects implements Evaluable{
    protected $checklistId;
    protected $htmlChecklistPoint;    
    protected $coloums,$table,$properties;
    protected $screenshots = array();
    
    public function __construct($id = false) {
        $this->checklistId = $id;
        $this->properties = array("id"=>"", "text"=>"", "heading"=>"");
        $this->coloums = array("checklistPointId","text","heading");
        $this->table = "checklistpoints";
        if($this->checklistId != false){
            $dbData = $this->load($id)->fetch_object();
                $this->properties['id'] = $dbData->checklistPointId;
                $this->properties['text'] = $dbData->text;
                $this->properties['heading'] = $dbData->heading; 
        }else{
            $this->createNewChecklistPoint();
        }
    }
    
    public function returnAsHtmlFront(){
        $this->createHtmlFront();
        return $this->htmlChecklistPoint;
    }
    
    public function returnAsHtmlBack(){
         $this->createHtmlBack();
        return $this->htmlChecklistPoint; 
    }
    
    private function createHtmlFront(){
        //strlen($this->properties['text']) > 1600 ? $followPoints = "..." : $followPoints = "";
        //$screenshots = $this->getScreenshots();
        $this->htmlChecklistPoint .= '<div class="checklistPoint">'.
                                        '<div class="heading"><input type="checkbox" class="checkBox" />'.$this->properties['heading'].'</div>'.
                                        '<div class="checklistPointPreview top">'.                                            
                                            '<div class="textContainer">'.$this->properties['text'].'</div>'.   
                                        '</div>'.
                                    '</div> ';
        $this->count();
    }
    
    private function createHtmlBack(){
         $wrapperStart = '<div>';  
         $wrapperEnd = '</div>';
         $orderButtons = '<div class="checklistPointOderContainer"><button class="blueButton checklistPointOrderButtonUp">hoch</button><button class="blueButton checklistPointOrderButtonDown">runter</button></div>';
         $checklistPoint = '<div class="checklistPointEdit" id="checklistPoint'.$this->properties['id'].'">'.
                                        '<div class="heading">'.$this->properties['heading'].'</div>'.
                                        '<div class="checklistPointPreview">'.                                            
                                            '<div class="textContainer">'.$this->properties['text'].$followPoints.'</div>'.   
                                        '</div>'.          
                                      '</div>';
         
         $this->htmlChecklistPoint .= $wrapperStart.$checklistPoint.$orderButtons.$wrapperEnd;
    }
    
    private function getScreenshots(){
        $screenshotsSql = "SELECT `name`,`description` FROM `screenshots` Left JOIN `checklistpoint_screenshot` USING(`screenshotId`)".
                          " LEFT JOIN `checklistpoints` USING(`checklistPointId`) WHERE `checklistPointId` = ".$this->properties['id']." ORDER BY `position`";
        $screenshots = $this->select($screenshotsSql);
        $screenshotsHtml = '';
        while($row = $screenshots->fetch_object()){
             //$screenshotsHtml[$i] .= '<div class="screenshotsGeneral screenshot'.$i.'"><a href="images/screenshots/'.$row->name.'"><img src="images/screenshots/'.$row->name.'" alt="'.$row->description.'" title="'.$row->description.'" /></a></div>';
             $screenshotsHtml .= '<a href="images/screenshots/'.$row->name.'" class="screenshotThumbnail"><img src="images/screenshots/thumbnails/'.$row->name.'" alt="'.$row->description.'" title="'.$row->description.'" /></a>';
        }        
        return $screenshotsHtml;
    } 
    
    private function createNewChecklistPoint(){
             //get next free id
        $takenIds = $this->select("SELECT `checklistPointId` FROM `checklistpoints` ORDER BY `checklistPointId`");            
        $freeTry = 1;
        while($row = $takenIds->fetch_object()){
            if($row->checklistPointId != $freeTry){
                $freeId = $freeTry;
            }else{ $freeTry++; }
        }
        isset($freeId) ? $freeId : $freeId = $freeTry++;
        $this->properties['id'] = $freeId;
    }   
    
    public function setValue($key, $value){
         $this->properties[$key] = $value;
    }
    
    public function getValue($key){
        return $this->properties[$key];
    }
    
    public function count() {
        if($_GET['from'] == "front" || $_POST['from']){
            $this->select("UPDATE `checklist_checklistpoints` SET `hits` = (`hits`+1) WHERE `checklistPointId` = ".$this->properties['id']);
        }
    }
}

?>
