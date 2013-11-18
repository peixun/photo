<?php
/**
 *
 * Web63.com Common class library 1.0
 * @author          Twilight QQ:654706160 <webs63@qq.com>
 * @copyright       Copyright (c) 20010-2012  (http://www.webs63.com)
 */
 class image{
    private $image_source;
    private $image_quality = 96;
    private $image_process;
    private $image_mode;
    private $imgInfo;
    private $image_save='';
    private $image_ext='';
    private $image_position=null;
    private $font_color = '#FFFFFF';
    private $font_family = 'tmp.ttf';
    private $font_Tilted = 0;
    private $font_side = 0;	
    private $font_size = 12;
    private $font_text = 'hello world';
    private $font_location = 'center';
    private $font_bgImage = '';
    private $font_bgside = 0;
    private $thumb_width = 0;
    private $thumb_height = 0;
    private $thumb_mode = 3;
    private $thumb_clear=true;

    public function __construct($imageSource='',$Output=0){
        if($imageSource=='')die('图片源未设置');
        if(!file_exists($imageSource))die('未发现有效图片源');
        if($Output==0)die('参数丢失！');
        $this->image_source = $imageSource;
        $this->image_mode = $Output;
        $this->imgInfo = getimagesize($this->image_source);
        switch($this->imgInfo[2]){
            case 1:
                $this->image_process = imagecreatefromgif($this->image_source);
                break;
            case 2:
                $this->image_process = imagecreatefromjpeg($this->image_source);
                break;
            case 3:
                $this->image_process = imagecreatefrompng($this->image_source);
        }
    }

    public function TextPrint(){
        $this->getImageExt();
        $this->CreateSavePath();
        $imgInfo = $this->imgInfo;
        $FontColor = $this->FetchColor($this->font_color);
        $Size = imagettfbbox($this->font_size,$this->font_Tilted,$this->font_family,$this->font_text);
        $textWidth = $Size[4];
        $textHeight = abs($Size[7]);

        switch($this->font_location){
            case 'top_left':
                $x = 0 + $this->font_side;
                $y = $imgInfo[1]-($imgInfo[1]-$textHeight) + $this->font_side;
            break;
            case 'top_right':
                $x = $imgInfo[0] - $textWidth-$this->font_side;
                $y = $imgInfo[1]-($imgInfo[1]-$textHeight) + $this->font_side;
            break;
            case 'bottom_left':
                $x = 0+$this->font_side;
                $y = $imgInfo[1]-$this->font_side;
            break;
            case 'bottom_right':
                $x = $imgInfo[0]-$textWidth-$this->font_side;
                $y = $imgInfo[1]-$this->font_side;
            break;
            default:
                $x = ($imgInfo[0]-$textWidth)/2;
                $y = $imgInfo[1]/2+5;
        }
        if($this->font_bgImage!=''){
            $bgInfo = getimagesize($this->font_bgImage);
            switch($bgInfo[2]){
                case 1:
                    $bgImage = imagecreatefromgif($this->font_bgImage);
                    break;
                case 2:
                    $bgImage = imagecreatefromjpeg($this->font_bgImage);
                    break;
                case 3:
                    $bgImage = imagecreatefrompng($this->font_bgImage);
                    break;
            }
            switch($this->font_location){
                case 'top_left':
                    $bgx = 0;
                    $bgy = 0 + $this->font_bgside;
                break;
                case 'top_right':
                    $bgx = $imgInfo[0] - $bgInfo[0];
                    $bgy = 0+$this->font_bgside;
                break;
                case 'bottom_left':
                    $bgx = 0;
                    $bgy = $imgInfo[1]-$bgInfo[1]-$this->font_bgside;
                break;
                case 'bottom_right':
                    $bgx = $imgInfo[0] - $bgInfo[0];
                    $bgy = $imgInfo[1]-$bgInfo[1]-$this->font_bgside;
                break;
                default:
                    $bgx = ($imgInfo[0]-$bgInfo[0])/2;
                    $bgy = ($imgInfo[1]-$bgInfo[1])/2;
            }
            imagecopy($this->image_process,$bgImage,$bgx,$bgy,0,0,$bgInfo[0],$bgInfo[1]);
        }
        if($this->image_position!=null){
            $x = $this->image_position[0];
            $y = $this->image_position[1];
        }
        imagettftext($this->image_process,$this->font_size,$this->font_Tilted,$x,$y,$FontColor,$this->font_family,$this->font_text);
        if($this->image_save==''){
            switch($imgInfo[2]){
                case 1:
                    header('Content-type: image/gif');
                    imagegif($this->image_process);
                    break;
                case 2:
                    header('Content-type: image/jpeg');
                    imagejpeg($this->image_process);
                    break;
                case 3:
                    header('Content-type: image/png');
                    imagepng($this->image_process);
            }
        }else{
            if($this->image_ext==''){
                switch($imgInfo[2]){
                    case 1:
                        imagegif($this->image_process,$this->image_save.'.'.$this->image_ext);
                        break;
                    case 2:
                        imagejpeg($this->image_process,$this->image_save.'.'.$this->image_ext,$this->image_quality);
                        break;
                    case 3:
                        imagepng($this->image_process,$this->image_save.'.'.$this->image_ext);
                        break;
                }
            }else{
                switch($this->image_ext){
                    case 'gif':
                        imagegif($this->image_process,$this->image_save.'.'.$this->image_ext);
                        break;
                    case 'jpg':
                        imagejpeg($this->image_process,$this->image_save.'.'.$this->image_ext,$this->image_quality);
                        break;
                    case 'png':
                    case 3:
                        imagepng($this->image_process,$this->image_save.'.'.$this->image_ext);
                        break;
                }
            }
            return true;
        }
    }

    public function ImagePrint(){
        $this->getImageExt();
        $this->CreateSavePath();
        $imgInfo = $this->imgInfo;
        $bgInfo = getimagesize($this->font_bgImage);
        switch($bgInfo[2]){
            case 1:
                $bgImage = imagecreatefromgif($this->font_bgImage);
                break;
            case 2:
                $bgImage = imagecreatefromjpeg($this->font_bgImage);
                break;
            case 3:
                $bgImage = imagecreatefrompng($this->font_bgImage);
                break;
        }
        switch($this->font_location){
            case 'top_left':
                $x = 0 + $this->font_bgside;
                $y = 0 + $this->font_bgside;
                break;
            case 'top_right':
                $x = $imgInfo[0] - $bgInfo[0]-$this->font_bgside;
                $y = 0+$this->font_bgside;
                break;
            case 'bottom_left':
                $x = 0+$this->font_bgside;
                $y = $imgInfo[1]-$bgInfo[1]-$this->font_bgside;
                break;
            case 'bottom_right':
                $x = $imgInfo[0] - $bgInfo[0]-$this->font_bgside;
                $y = $imgInfo[1]-$bgInfo[1]-$this->font_bgside;
                break;
            default:
                $x = ($imgInfo[0]-$bgInfo[0])/2;
                $y = ($imgInfo[1]-$bgInfo[1])/2;
        }
        if($this->image_position!=null){
            $x = $this->image_position[0];
            $y = $this->image_position[1];
        }
        imagecopy($this->image_process,$bgImage,$x,$y,0,0,$bgInfo[0],$bgInfo[1]);
        if($this->image_save==''){
            switch($imgInfo[2]){
                case 1:
                    header('Content-type: image/gif');
                    imagegif($this->image_process);
                    break;
                case 2:
                    header('Content-type: image/jpeg');
                    imagejpeg($this->image_process);
                    break;
                case 3:
                    header('Content-type: image/png');
                    imagepng($this->image_process);
            }
        }else{
            if($this->image_ext==''){
                switch($imgInfo[2]){
                    case 1:
                        imagegif($this->image_process,$this->image_save.'.'.$this->image_ext);
                        break;
                    case 2:
                        imagejpeg($this->image_process,$this->image_save.'.'.$this->image_ext,$this->image_quality);
                        break;
                    case 3:
                        imagepng($this->image_process,$this->image_save.'.'.$this->image_ext);
                        break;
                }
            }else{
                switch($this->image_ext){
                    case 'gif':
                        imagegif($this->image_process,$this->image_save.'.'.$this->image_ext);
                        break;
                    case 'jpg':
                        imagejpeg($this->image_process,$this->image_save.'.'.$this->image_ext,$this->image_quality);
                        break;
                    case 'png':
                    case 3:
                        imagepng($this->image_process,$this->image_save.'.'.$this->image_ext);
                        break;
                }
            }
            return true;
        }
    }

    public function Thumb(){
        $this->getImageExt();
        $this->CreateSavePath();
        $imgInfo = $this->imgInfo;
        $dst_x = 0;
        $dst_y = 0;
        $src_w = $imgInfo[0];
        $src_h = $imgInfo[1];
        $src_x = 0;
        $src_y = 0;

        if($this->thumb_mode==1){
            $dst_w = $this->thumb_width;
            $dst_h = round($src_h/($src_w/$dst_w));
            $board_x = $dst_w;
            $board_y = $dst_h;
        }

        if($this->thumb_mode==2){
            $dst_h = $this->thumb_height;
            $dst_w = round($src_w/($src_h/$dst_h));
            $board_x = $dst_w;
            $board_y = $dst_h;
        }

        if($this->thumb_mode==3){
            $board_x = $this->thumb_width;
            $board_y = $this->thumb_height;
            $dst_w = $this->thumb_width;
            $dst_h = round($src_h/($src_w/$dst_w));
            if($dst_h<$board_y){
                $dst_h = $board_y;
                $dst_w = round($src_w/($src_h/$dst_h));
                $dst_x = -($dst_w-$board_x)/2;
            }elseif($dst_h>$board_y){
                $dst_y = -($dst_h-$board_y)/2;
            }
        }

       if($this->thumb_mode==4){
           $src_x = $this->image_position[0];
           $src_y = $this->image_position[1];
           $src_w = $this->image_position[2];
           $src_h = $this->image_position[3];
           if($this->thumb_width!=0&&$this->thumb_height==0){
               $dst_w = $this->thumb_width;
               $dst_h = round($src_h/($src_w/$dst_w));
               $board_x = $dst_w;
               $board_y = $dst_h;
           }
           if($this->thumb_height!=0&&$this->thumb_width==0){
               $dst_h = $this->thumb_height;
               $dst_w = round($src_w/($src_h/$dst_h));
               $board_x = $dst_w;
               $board_y = $dst_h;
           }
       }
       $new = ImageCreateTrueColor($board_x,$board_y);
       if($this->thumb_clear){
           imagecopyresampled($new,$this->image_process,$dst_x,$dst_y,$src_x,$src_y,$dst_w,$dst_h,$src_w,$src_h);
       }else{
           imagecopyresized($new,$this->image_process,$dst_x,$dst_y,$src_x,$src_y,$dst_w,$dst_h,$src_w,$src_h);
       }

       if($this->image_save==''){
           switch($imgInfo[2]){
               case 1:
                   header('Content-type: image/gif');
                   imagegif($new);
                   break;
               case 2:
                   header('Content-type: image/jpeg');
                   imagejpeg($new);
                   break;
               case 3:
                   header('Content-type: image/png');
                   imagepng($new);
           }
        }else{
            if($this->image_ext==''){
                switch($imgInfo[2]){
                    case 1:
                        imagegif($new,$this->image_save.'.'.$this->image_ext);
                        break;
                    case 2:
                        imagejpeg($new,$this->image_save.'.'.$this->image_ext,$this->image_quality);
                        break;
                    case 3:
                        imagepng($new,$this->image_save.'.'.$this->image_ext);
                }
            }else{
                switch($this->image_ext){
                    case 'gif':
                        imagegif($new,$this->image_save.'.'.$this->image_ext);
                        break;
                    case 'jpg':
                        imagejpeg($new,$this->image_save.'.'.$this->image_ext,$this->image_quality);
                        break;
                    case 'png':
                    case 3:
                        imagepng($new,$this->image_save.'.'.$this->image_ext);
                        break;
                }
            }
            return true;
        }
    }

    private function getImageExt(){
        if($this->image_ext==''){
            $tempArr = explode(".", $this->image_source);
            $fileExt = array_pop($tempArr);
            $fileExt = trim($fileExt);
            $fileExt = strtolower($fileExt);
            $this->image_ext=$fileExt;
        }
    }

    private function FetchColor($Color=''){
        $Color = ereg_replace("^#","",$Color);
        $r = $Color[0].$Color[1];
        $r = hexdec($r);
        $b = $Color[2].$Color[3];
        $b = hexdec($b);
        $g = $Color[4].$Color[5];
        $g = hexdec($g);
        $Color = imagecolorallocate($this->image_process, $r, $b, $g);
        return $Color;
    }

    private function CreateSavePath(){
        if($this->image_save!=''){
            $image_dir = substr($this->image_save,0,strrpos($this->image_save,'/')+1);
            if(!is_dir($image_dir)){
                mkdir($image_dir, 0777);
                chmod($image_dir, 0777);
            }
        }
    }

    public function __set($property,$value){
        switch($this->image_mode){
            case 1:
                if($property=='color'){$this->font_color=$value;}
                if($property=='family'){$this->font_family=$value;}
                if($property=='tilted'){$this->font_Tilted=$value;}
                if($property=='size'){$this->font_size=$value;}
                if($property=='text'){$this->font_text=$value;}
                if($property=='side'){$this->font_side=$value;}
                if($property=='location'){$this->font_location=$value;}
                if($property=='bgimage'){$this->font_bgImage=$value;}
                if($property=='bgside'){$this->font_bgside=$value;}
            break;
            case 2:
                if($property=='bgimage'){$this->font_bgImage=$value;}
                if($property=='location'){$this->font_location=$value;}
                if($property=='side'){$this->font_bgside=$value;}
            break;
            case 3:
                if($property=='mode'){$this->thumb_mode=$value;}
                if($property=='width'){$this->thumb_width=$value;}
                if($property=='height'){$this->thumb_height=$value;}
                if($property=='clear'){$this->thumb_clear=$value;}
            break;
        }
        if($property=='position'){$this->image_position=$value;}
        if($property=='savePath'){$this->image_save=$value;}
        if($property=='ext'){$this->image_ext=$value;}
        if($property=='quality'){$this->image_quality=$value;}
    }

    public function __get($value){
        if($value=='path'&&$this->image_save!=''){return $this->image_save.'.'.$this->image_ext;}
        if($value=='ext'&&$this->image_save!=''){return $this->image_ext;}
    }
 }
?>
