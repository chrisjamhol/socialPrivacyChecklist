<?php 
echo getcwd();
#include_once '../model/DbObjects.php'; include_once '../model/DbConnector.php';
/*class Fixer extends DbObjects{
	
	public function showChecklistPosition(){
		$ids = $this->select("SELECT `checklistId`,`checklistPointId`,`position` FROM `checklist_checklistpoints` ORDER BY `checklistId`"); 
		$table = "<table>";
		while($row = $ids->fetch_object()){            
            	$table .= "<tr><td>".$row->checklistId."</td><td>".$row->checklistPointId."</td><td>".$row->position."</td></tr>";            
        }
        $table .= "</table>";
        echo $table;
        var_dump($table);
		
	}

	public function fixChecklistPositions(){
		$checklist = $this->select("SELECT DISTINCT `checklistId` FROM `checklist_checklistpoints`");

		while($row = $checklist->fetch_object()){
			echo "checklist ".$row->checklistId."<br />";
						
			$length = $this->select("SELECT COUNT(`checklistPointId`) AS anzahl FROM `checklist_checklistpoints` WHERE `checklistId` = ".$row->checklistId)->fetch_object()->anzahl;
			$checklistPointIds = $this->select("SELECT `checklistPointId` FROM `checklist_checklistpoints` WHERE `checklistId` = ".$row->checklistId);
			$this->select("DELETE FROM `checklist_checklistpoints` WHERE `checklistId` = ".$row->checklistId);
			$checklistPointId = array();
			while($cp = $checklistPointIds->fetch_object()){
				array_push($checklistPointId,$cp->checklistPointId);
			}
			for($i = 0; $i < $length; $i++){
				echo "INSERT INTO `checklist_checklistpoints` (`checklistId`,`checklistPointId`,`position`) VALUES (".$row->checklistId.",".$checklistPointId[$i].",".$i.");<br />";
				$this->select("INSERT INTO `checklist_checklistpoints` (`checklistId`,`checklistPointId`,`position`) VALUES (".$row->checklistId.",".$checklistPointId[$i].",".$i.");");
			}
			echo "<br /> <br />";
		}
	}

}*/

#$fixer = new Fixer();
#$fixer->fixChecklistPositions();
#$fixer->showChecklistPosition();

?>