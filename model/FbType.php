﻿<?php 
include_once 'model/DbObjects.php'; include_once 'model/Evaluable.php';
/**
     * set id to create new fbType from db <br>
     * -------------------------------<br>
     * load without param to create blank object <br>
     * set properties --> $this->properties['coloumname'] = $param <br>
     * save() to create in db
     * 
     * @param any $id 
     * 
     * @return object
     */    
class FbType extends DbObjects implements Evaluable{
    protected $properties,$coloums,$table;
    
    public function __construct($id = NULL){
        $this->properties = array("id"=>"","name"=>"","description"=>"","pos"=>"","iconPath"=>"","checklistId"=>"","hits"=>"");
        $this->coloums = array("fbtypeId","name","description","pos","iconPath","checklistId","hits");
        $this->table = "fbtype";
        
        if(isset($id)){
            $dbData = $this->load($id)->fetch_object();
                //loaded with id (existing)
            $this->properties['name'] = $dbData->name;
            $this->properties['id'] = $dbData->fbtypeId;
            $this->properties['description'] = $dbData->description;
            $this->properties['pos'] = $dbData->pos;
            $this->properties['iconPath'] = $dbData->iconPath;
            $this->properties['checklistId'] = $dbData->checklistId;
            $this->properties['hits'] = $dbData->hits;  
        }else{
                 //loaded without id (not jet existing)
            $this->createNewFbType();            
        }   
                
    }
    
    /**
     * @param string $for --> "backend" or "frontend"
     * @return type 
     */
    public function getTypesHtml($for) {
        switch($for){
            case 'frontend': 
                return $this->createTypesHtmlFront();
                break;
            case 'backend':
                return '<button class="blueButton">'.$this->properties['name'].'</button>';
                break;
        }        
    }
    /**    
     * @return string controlsHtml 
     */
    public function getControlsHtml(){        
         return '<span class="fbTypesOrderButtons"><span class="fbTypePos">'.$this->properties['pos'].'&nbsp;</span><button class="blueButton fbTypePosHigh">+</button><button class="blueButton fbTypePosLow">-</button><button style="font-size: .7em;" class="blueButton fbTypeDelete">X</button></span>';
            
    }
    
    /**
     * creates new fbType <br>
     * called from contructor 
     */
    private function createNewFbType(){
            //get next free id
        $takenIds = $this->select("SELECT `fbtypeId` FROM `fbtype` ORDER BY `fbtypeId`");            
        $freeTry = 1;
        while($row = $takenIds->fetch_object()){
            if($row->fbtypeId != $freeTry){
                $freeId = $freeTry;
            }else{ $freeTry++; }
        }
        isset($freeId) ? $freeId : $freeId = $freeTry++;
        $this->properties['id'] = $freeId;
        
            //check if new order is needed ||     
        if($_GET['newOrder'] == "true"){
            $this->updatePositions($_GET['pos']);
        }            
    }    
    
            /**
            * sets position+1 for all following elments after a new created element  
            */
            private function updatePositions($pos){
                /*   get elements in db >= pos       */
                $elementsLookupSql = "SELECT `pos`,`fbtypeId` FROM `fbtype` WHERE `pos` >=".$pos;
                     $elementsLookup = $this->select($elementsLookupSql);
                /*   update elements in db to pos +1  */
                while($row = $elementsLookup->fetch_object()){
                    $addFbTypeSql = "UPDATE `fbtype` SET `pos` = ".($row->pos+1)." WHERE `fbtypeId` = ".$row->fbtypeId."; ";
                    $this->select($addFbTypeSql);
                }   
            }
    
    private function createTypesHtmlFront(){
        $html .= '<li>';
        $html .= '<a href="#'.$this->properties['name'].'"><span><img class="typeIcon" src="images/typeIcons/'.$this->properties['iconPath'].'" /></span><span class="typeDesc" style="display:none;">'.$this->properties['description'].'</span>';
        $html .= '<span class="screen-reader-text">'.$this->properties['name'].'</span><span class="idHolder">'.$this->properties['checklistId'].'</span></a><span class="typeButtonText">'.$this->properties['name'].'</span></li>';
        return $html;
    }
    
     /**
      * setter
      * @param type $key
      * @param type $value 
      */
    public function setValue($key, $value){
         $this->properties[$key] = $value;
    }
    
    public function count() {    
        
    }
}

?>
