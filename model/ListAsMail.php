<?php

class ListAsMail extends DbObjects implements Evaluable{
    private $status = false;

    public function create($checklistId,$to){
        $subject = "Deine Social Privacy Checklist";
        $contentHtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        $contentHtml .= '<html>';
        $contentHtml .= '<head>';
        $contentHtml .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
        $contentHtml .= '<style>';
        $contentHtml .= '.checklistPoint{margin-top: 10px; margin-bottom: 10px; border:1px solid black;} ';
        $contentHtml .= '.heading{background-color: lightgray; border-bottom: 1px solid black;margin: 0px; padding: 6px;} ';
        $contentHtml .= '.content{padding: 6px;}';
        $contentHtml .= '</style>';
        $contentHtml .= '</head>';
        $contentHtml .= '<body>';
        $contentPlain = "";

        $checklistPointsSql = "SELECT `checklistPointId` AS `id` FROM `checklist_checklistpoints` WHERE `checklistId` = ".$checklistId." ORDER BY `position`";
        $checklistPoints = $this->select($checklistPointsSql);
        $i = 1;
        while($checklistPoint = $checklistPoints->fetch_object()){
            $content = $this->getChecklistPointContent($checklistPoint->id);
            /*preg_replace("/<img[^>]+\>/i", "(image) ", $content); */
            $contentHtml .= '<div class="checklistPoint">';
                $contentHtml .= '<div class="heading">'.$content->heading.'</div>';
                $contentHtml .= '<div class="content">'.preg_replace("|images/screenshots/|","http://de.itsbetter.com/socialprivacychecklist/images/screenshots/",$content->text).'</div>';
            $contentHtml .= '</div>';

            $contentPlain .= $i.". ".strip_tags($content->heading,'<a>')."\n\r\n\r";
            $contentPlain .= strip_tags(str_replace("&nbsp;"," ",str_replace("</p>","\n\r",str_replace("<br />","\n\r",$content->text))));
            $contentPlain .= "\n\r\n\r";
            $i++;
        }

        $contentHtml .= '</body></html>';

        //echo $contentHtml;
            //start building mail
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "From: spc@itsbetter.de \r\n";
        $headers .= "To: ".$to."\r\n";
        $headers .= "Content-Type: text/html; charset=utf-8";

        $message = $contentHtml;

        $this->status = mail($to,$subject,$message,$headers);

        return $this->status ? "send" : "failed";
    }

    private function getChecklistPointContent($id){
        $dataObject = new DataObject();
        $dbData = $this->select("SELECT `text`,`heading` FROM `checklistpoints` WHERE `checklistPointId` = ".$id)->fetch_object();
        $dataObject->produce($dbData->text,$dbData->heading);
        return $dataObject;
    }

    public function count() {

    }
}

class DataObject{
    public function produce($content,$heading){
        $this->text = $content;
        $this->heading = $heading;
    }
}

?>