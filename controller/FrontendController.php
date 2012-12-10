<?php 
include_once 'model/Includer.php';
$includer = new Includer();
$includer->includeShit();
    $db = DbConnector::getInstance()->connect();    
    
    function createChecklistView($db){
        $id = $_GET['id'];
        if($id != ""){
            $checklist = new Checklist($id);
            echo ltrim($checklist->returnAsHtml());
        }else{
            echo '<p>Leider keine Checkliste für diesen Typ gefunden</p>';
        }        
    }
    
    function getFbTypes($db){
         $getFbTypesSql = "SELECT `fbtypeId` FROM `fbtype` ORDER BY `pos`";
         $result = $db->query($getFbTypesSql);	   
         /*$typesHtml = "";*/
         while ($row = $result->fetch_object()){
             $type = new FbType($row->fbtypeId);
             $typesHtml .= $type->getTypesHtml("frontend");             
         }
         echo ltrim($typesHtml);       
    }
    
    function getChecklistIdByName($db){
        $data = $db->query('SELECT `checklistId`,`iconPath`,`description` FROM `fbtype` WHERE `name`="'.$_GET['name'].'"')->fetch_object();
        
        if($_GET['from'] == "front" || $_POST['from'] == "front"){
            if($_GET['op'] == "getChecklistIdByName" || $_POST['op'] == "getChecklistIdByName"){
                if(isset($_SESSION['typeView'])){
                    if(strpos($_SESSION['typeView'],$_GET['name']) === false){
                        $_SESSION['typeView'] = $_SESSION['typeView']."|".$_GET['name'];            
                        $db->query('UPDATE `fbtype` SET `hits` = (`hits`+1) WHERE `name`="'.$_GET['name'].'"');
                    }                   
                }else{
                    $db->query('UPDATE `fbtype` SET `hits` = (`hits`+1) WHERE `name`="'.$_GET['name'].'"');
                    $_SESSION['typeView'] = $_GET['name'];
                }           
            }
        }
        
        echo json_encode(array("id"=>$data->checklistId, "iconPath"=>$data->iconPath, "description"=>$data->description));
    }
     
    function getScreenshotData($db){
        echo json_encode(getimagesize($_GET['source']));
    }
    
    function sendListAsMail(){        
        include_once 'model/ListAsMail.php';
        $mail = new ListAsMail();
        echo $mail->create($_GET['checklistId'],$_GET['to']);
        
    }
    
    function increasePageView($db){
        if(!isset($_SESSION['viewAll'])){
            $_SESSION['viewAll'] = true;
            $db->query("UPDATE `stats` SET `all` = `all`+1");
        }      
    }
    
    function increaseIndexStats($db){
        if(!isset($_SESSION['viewIndex'])){
            $_SESSION['viewIndex'] = true;
            $db->query("UPDATE `stats` SET `index` = `index`+1");
        }
    }
    
function saveFeedback($db){
    $db->query("INSERT INTO `feedback` (`name`, `mail`, `message`) VALUES ('".$_GET['data']['name']."','".$_GET['data']['mail']."','".$_GET['data']['message']."')");
    mail('spc@itsbetter.de', 'Social Privacy Checklist Freedback', "Name: ".$_GET['data']['name']."\r\n"."E-Mail: ".$_GET['data']['mail']."\r\n".$_GET['data']['message']);    
}
?>
