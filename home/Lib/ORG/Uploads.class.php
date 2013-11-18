<?php
/**
 *
 * Web63.com Common class library 1.0
 * @author          Twilight QQ:654706160 <webs63@qq.com>
 * @copyright       Copyright (c) 20010-2012  (http://www.webs63.com)
 */
class upload {
    private $file_name = 'null';
    private $file_size = 0;
    private $file_tmpName = '';
    private $upload_type = array('jpg','jpeg','gif','png','bmp');
    private $upload_Ext = 'null';
    private $upload_dir = 'null';
    private $upload_name = 'null';
    private $upload_width = 0;
    private $upload_height = 0;
    private $upload_msg = '';
    private $upload_mode;

    public function __construct($control='',$filePath='',$fileName='',$fileType='',$size=0) {
        $this->upload_mode = true;

        if($control=='')
            $this->error('请正确指定上传控件。');
        else{
            $this->file_name=$_FILES[$control]['name'];
            $this->file_size=$_FILES[$control]['size'];
            $this->file_tmpName=$_FILES[$control]['tmp_name'];

            if($fileType!='')
                $this->upload_type = $fileType;

            if($filePath=='')
                $this->upload_dir='./';
            else
                $this->upload_dir=$filePath;

            if(!is_uploaded_file($this->file_tmpName))
                $this->error('没有文件被上传');
            else{
                if($size!=0&&$this->file_size>$size)
                    $this->error('上传文件不允许超过'.$this->getSize($size));

                if(!$this->typeDetect($this->file_name))
                    $this->error('上传文件扩展名是不允许的扩展名。');
                else{
                    if(!$this->fileDetect($this->file_tmpName))
                        $this->error('无效的图片文件或文件已损坏。');
                }

                if($fileName=='')
                    $this->upload_name=$this->file_name;
                else
                    $this->upload_name=$fileName.'.'.$this->upload_Ext;
            }
        }
        if($this->mode)
            $this->saveFile();
    }

    public function __get($name){
        switch($name){
            case 'path':
                return $this->upload_dir;
                break;
            case 'name':
                return $this->upload_name;
                break;
            case 'ext':
                return $this->upload_Ext;
                break;
            case 'size':
                return $this->getSize($this->file_size);
            case 'width':
                return $this->upload_width;
                break;
            case 'height':
                return $this->upload_height;
                break;
            case 'mode':
                return $this->upload_mode;
                break;
            case 'msg':
                return $this->upload_msg;
                break;
        }
    }

    private function typeDetect($OldFile){
        $tempArr = explode(".", $OldFile);
        $fileExt = array_pop($tempArr);
        $fileExt = trim($fileExt);
        $fileExt = strtolower($fileExt);
        if(in_array($fileExt,$this->upload_type)){
            $this->upload_Ext=$fileExt;
            return true;
        }
        else
            return false;
    }

    private function fileDetect($tmpName){
        $arr = getimagesize($tmpName);
        if(!$arr){
            return false;
        }else{
            $this->upload_width = $arr[0];
            $this->upload_height = $arr[1];
            return true;
        }
    }

    private function saveFile(){
        if(!is_dir($this->upload_dir)){
            mkdir($this->upload_dir, 0777);
            chmod($this->upload_dir, 0777);
        }
        $save_file = move_uploaded_file($this->file_tmpName,$this->upload_dir.$this->upload_name);
        if(!$save_file)
            $this->error($this->err5);
        else
            $this->upload_msg=$this->success;
    }

    private function getSize($tmpSize){
        $value = 'B';
        if($tmpSize>1024){
            $tmpSize = floor($tmpSize/1024);
            $value = 'KB';
        }
        if($tmpSize>1024){
            $tmpSize = round($tmpSize/1024,2);
            $value = 'MB';
        }
        return $tmpSize.$value;
    }

    private function error($msg=''){
        $this->upload_msg .= $msg;
        $this->upload_mode = false;
    }
}

?>