<?php
include_once "model/DbObjects.php";
include_once "model/Evaluable.php";
class Article extends DbObjects implements Evaluable{
    private $screenshots = array();
    private $heading = "";
    private $text = "";
        
    
    public function __construct(){
        $saveValues = array("eins","zwei", "drei");
        $this->table = "testTable";
        $this->coloums = array("articleId","name","description");
        $this->save($saveValues);
    }   
    
    function count(){
        
    }
}

?>
