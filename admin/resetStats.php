<?php
include 'model/DbConnector.php';
$db = DbConnector::getInstance()->connect();

if($_GET['really'] == "yeah"){
    $db->query("UPDATE `stats` SET `all`= 0, `index` = 0");
    $db->query("UPDATE `checklist` SET `hits` = 0 WHERE 1");
    $db->query("UPDATE `checklist_checklistpoints` SET `hits` = 0 WHERE 1");
    $db->query("UPDATE `fbtype` SET `hits` = 0 WHERE 1");
    echo 'reseted stats';
}else{
    echo 'set param "really" to "year" ($_GET["really"] = "yeah")';
}

?>
