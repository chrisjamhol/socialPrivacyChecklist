<?php
class Includer{    
    public function includeShit(){
        include_once 'model/Article.php';
        include_once 'model/Checklist.php';
        include_once 'model/DbConnector.php';
        include_once 'model/FbType.php';
        include_once 'model/Screenshot.php';
    }
}
?>
