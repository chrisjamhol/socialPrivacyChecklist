<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ImageCrop
 *
 * @author chris
 */
class ImageCrop {
    private $cropedImage,
            $filename = "",
            $ext,
            $path,
            $crop_width,
            $crop_height,
            $image,
            $canvas;
    
    public function __construct($pathInfo,$cropW,$cropH) {
        $this->filename = $pathInfo['filename'];
        $this->ext = $pathInfo['ext'];
        $this->path = $pathInfo['uploadDirectory'];
        $this->crop_height = $cropH;
        $this->crop_width = $cropW;
        $uploadeName = $this->path.$this->filename.'.'.$this->ext;
        switch($this->ext){
            case 'jpeg': 
               $this->image = imagecreatefromjpeg($uploadeName);
                break;
            case 'jpg': 
               $this->image = imagecreatefromjpeg($uploadeName);
                break;
            case 'gif': 
               $this->image = imagecreatefromgif($uploadeName); 
                break;
            case 'png': 
               $this->image = imagecreatefrompng($uploadeName); 
                break;
            default: echo 'please just upload jpeg,gif or png image';
        }
    }
    
    public function cropImage(){       
        list($current_width, $current_height) = getimagesize($this->path.$this->filename.'.'.$this->ext);
                
        if($current_height < $current_width){                   //portaimode -> resize to height
            $ratio = $this->crop_height / $current_height;
            $width = $current_width * $ratio;
            $this->resize($width,$this->crop_height,$current_width,$current_height);
            $this->crop();
        }else{                                                  //landscape or width=height -> resize to width
            $ratio = $this->crop_width / $current_width;
            $height = $current_height * $ratio;
            $this->resize($current_width,$height);
            $this->crop();
        }
    }
    
    private function crop(){        
        $thumbnailPath = $this->path.'/thumbnails/'.$this->filename.'.'.$this->ext;
        $this->canvas = imagecreatetruecolor($this->crop_width, $this->crop_height);
        switch($this->ext){
            case 'jpeg': 
                imagecopyresampled($this->canvas, $this->image, 0, 0, 0, 0, $this->crop_width, $this->crop_height, $this->crop_width, $this->crop_height);
                imagejpeg($this->canvas, $thumbnailPath, 100); 
                break;
            case 'jpg': 
                imagecopyresampled($this->canvas, $this->image, 0, 0, 0, 0, $this->crop_width, $this->crop_height, $this->crop_width, $this->crop_height);
                imagejpeg($this->canvas, $thumbnailPath, 100); 
                break;
            case 'gif': 
                imagecopyresampled($this->canvas, $this->image, 0, 0, 0, 0, $this->crop_width, $this->crop_height, $this->crop_width, $this->crop_height);
                imagegif($this->canvas, $thumbnailPath); 
                break;
            case 'png': 
                imagecopyresampled($this->canvas, $this->image, 0, 0, 0, 0, $this->crop_width, $this->crop_height, $this->crop_width, $this->crop_height);
                imagepng($this->canvas, $thumbnailPath); 
                break;
            default: echo 'please just upload jpeg,gif or png image';
        }
    }
    
    private function resize($width,$height,$current_width,$current_height) {
      $new_image = imagecreatetruecolor($width, $height);
      imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $current_width, $current_height);
      $this->image = $new_image;
   }  
    
}

?>
