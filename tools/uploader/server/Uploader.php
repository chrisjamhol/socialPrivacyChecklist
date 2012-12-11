<?php
   
/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr {
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {    
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);
        //echo $path;
        
        if ($realSize != $this->getSize()){            
            return false;
        }
        
        $target = fopen($path, "w");        
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);
        
        return true;
    }
    function getName() {
        return $_GET['qqfile'];
    }
    function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];            
        } else {
            throw new Exception('Getting content length is not supported.');
        }      
    }   
}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm {  
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){
            return false;
        }
        return true;
    }
    function getName() {
        return $_FILES['qqfile']['name'];
    }
    function getSize() {
        return $_FILES['qqfile']['size'];
    }
}

include_once '../../../model/DbObjects.php'; include_once '../../../model/DbConnector.php';
class qqFileUploader extends DbObjects{
    private $allowedExtensions = array();
    private $sizeLimit = 200000;
    private $file;

    function __construct(array $allowedExtensions = array(), $sizeLimit = 200000){        
        $allowedExtensions = array_map("strtolower", $allowedExtensions);
            
        $this->allowedExtensions = $allowedExtensions;        
        $this->sizeLimit = $sizeLimit;
        
        //$this->checkServerSettings();       

        if (isset($_GET['qqfile'])) {
            $this->file = new qqUploadedFileXhr();
        } elseif (isset($_FILES['qqfile'])) {
            $this->file = new qqUploadedFileForm();
        } else {
            $this->file = false; 
        }
    }
    
    private function checkServerSettings(){ 
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));        
        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';             
            die("{'error':'increase post_max_size and upload_max_filesize to $size'}");    
        }        
    }
    
    private function toBytes($str){
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;        
        }
        return $val;
    } 
    
    
    private function getNextFilenameIcon(){      
        $filenames = array();
        $getFilenameIcon = "SELECT `iconPath` FROM `fbtype` WHERE 1";
        $result = $this->select($getFilenameIcon);
        while($row = $result->fetch_object()){
            array_push($filenames, str_replace("icon","",$row->iconPath));
        }
        sort($filenames);
        $x = 1;
        while($x == $filenames[$x-1]){
            $x++;
        }
        return "icon".$x;
    }
    /**
     * crops and saves imagethumbnail in images/screenshots/thumbnails
     */
    private function cropImage($uploadDirectory,$filename,$ext){
        include 'ImageCrop.php';
            $conf = array('uploadDirectory' => $uploadDirectory, 'filename' => $filename, 'ext' => $ext);                
            $cropWidth = 150;
            $cropHeight = 150;            
        $cropImage = new ImageCrop($conf,$cropWidth,$cropHeight);
        $cropImage->cropImage();
        $this->select('INSERT INTO `screenshots`(`name`, `description`) VALUES ("'.$filename.'.'.$ext.'","'.$_GET['description'].'")');        
    }
    
    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload($uploadDirectory, $replaceOldFile = TRUE){
        if (!is_writable($uploadDirectory)){
            return array('error' => "Server error. Upload directory isn't writable.");
        }
        
        if (!$this->file){
            return array('error' => 'No files were uploaded.');
        }
        
        $size = $this->file->getSize();
        
        if ($size == 0) {
            return array('error' => 'File is empty');
        }
        
        if ($size > $this->sizeLimit) {
            return array('error' => 'File is too large');
        }
        
        $pathinfo = pathinfo($this->file->getName());
        $ext = $pathinfo['extension'];
        
        if($_GET['directory'] == "../../../images/typeIcons/"){                                       //icons
            $crop = false;
            if($_GET['icon'] == ""){
               $filename = str_replace(".".$ext,"",$_GET['qqfile']);
           }else{
               $filename = $_GET['icon'];
           } 
        }else if($_GET['directory'] == "../../../images/screenshots/"){                           //screenshots
           $crop = true;
                $search = array("ä","ö","ü","Ä","Ö","Ü","ß"," ");
                $replace = array("ae","oe","ue","Ae","Oe","Ue","ss","");
           $filename = str_ireplace($search,$replace,$pathinfo['filename']);
        }else{
            return array('error' => 'no correct directory');
        }
        
        if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
        }
        
        if(!$replaceOldFile){
            /// don't overwrite previous files that were uploaded
            while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
                $filename .= rand(10, 99);
            }
        }
        
        if ($this->file->save($uploadDirectory . $filename . '.' . $ext)){
            if($crop){
                $this->cropImage($uploadDirectory, $filename, $ext);                
            }
            
            return array('success'=>true,'filename'=>$filename.'.'.$ext); 
        } else {
            return array('error'=> 'Could not save uploaded file.' .
                'The upload was cancelled, or server error encountered');
        }
        
    }   
}

// list of valid extensions, ex. array("jpeg", "xml", "bmp")
$allowedExtensions = array();
// max file size in bytes
$sizeLimit = 10 * 1024 * 1024;

$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);

$result = $uploader->handleUpload($_GET['directory']);
// to pass data through iframe you will need to encode all html tags
echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);

