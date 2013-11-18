<?php
class AttachLwModel extends LW_Model {
	public function getInfo($map) {
		return $this->order('id DESC')->find($map);
	}
	//上传附件
	public function upload($attach_type='attach',$input_options=array()){
		//网站定义的主上传目录
		$upload_path	=	C('UPLOAD_PATH');

		$default_options	=	array();
		$default_options['custom_path']	=	date('Ymd/H/');			//应用定义的上传目录规则
		$default_options['max_size']	=	'2048000';				//默认是2兆
		$default_options['allow_exts']	=	'jpg,gif,png,jpeg,bmp';
		$default_options['allow_types']	=	'';
		$default_options['save_path']	=	$upload_path.$default_options['custom_path'];
		$default_options['save_name']	=	'';
		$default_options['save_rule']	=	'uniqid';

		//覆盖默认设置
		$options	=	array_merge($default_options,$input_options);

		//载入上传类
		import("ORG.Net.UploadFile");

		//初始化上传参数
        $upload = new UploadFile($options['max_size'],$options['allow_exts'],$options['allow_types']);
		//设置上传路径
		$upload->savePath		=	$options['save_path'];
        //启用子目录
		$upload->autoSub		=	false;
		//保存的名字
        $upload->saveName		=   $options['save_name'];
		//默认文件名规则
		$upload->saveRule		=	$options['save_rule'];
        //是否缩略图
        $upload->thumb          =   false;

		//创建目录
		mkdir($upload->savePath,0777,true);
		//执行上传操作
        if(!$upload->upload()) {

			//上传失败，返回错误
			$return['status']	=	false;
			$return['info']		=	$upload->getErrorMsg();
			return	$return;

		}else{

			$upload_info		=	$upload->getUploadFileInfo();

			//如果同步保存照片
			$uid	=	TS_D("User")->getLoggedInUser();

			//保存信息到附件表
			foreach($upload_info as $u){
				unset($map);
				$map['attach_type']	=	$attach_type;
				$map['userId']		=	$uid;
				$map['uploadTime']	=	time();
				$map['name']		=	$u['name'];
				$map['type']		=	$u['type'];
				$map['size']		=	$u['size'];
				$map['extension']	=	$u['extension'];
				$map['hash']		=	$u['hash'];
				$map['savepath']	=	$options['custom_path'];
				$map['savename']	=	$u['savename'];
				//$map['savedomain']=	C('ATTACH_SAVE_DOMAIN');
				$result	=	$this->add($map);
				$map['id']	=	$result;
				$infos[]	=	$map;
			}
			if(isset($options['save_photo'])){
				$infos	=	$this->save_photo($options['save_photo'],$infos);
			}
			//输出信息
			$return['status']	=	true;
			$return['info']		=	$infos;
			//上传成功，返回信息
			return	$return;
    	}
	}

	//保存照片信息
	public function save_photo($album,$attachInfos) {

		//数据库表前缀
		$pre	=	C('DB_PREFIX');

		//创建新相册
		if( $album['new_album']===true && trim($album['album_name'])!='' ){
			$albumInfo	=	$album;
			$albumId	=	$this->table("{$pre}photo_album")->add($album);

		//使用旧相册
		}elseif( intval($album['albumId'])>0 ){
			$albumId	=	intval($album['albumId']);
			$albumInfo	=	$this->table("{$pre}photo_album")->find($albumId);

		//退出
		}else{
			return $attachInfos;
		}

		//保存图片附件进入相册
		foreach($attachInfos as $k=>$v){
			$photo['attachId']	=	$v['id'];
			$photo['albumId']	=	$albumId;
			$photo['userId']	=	$v['userId'];
			$photo['cTime']		=	time();
			$photo['mTime']		=	time();
			$photo['name']		=	substr($v['name'],'0',strpos($v['name'],'.'));	//去掉后缀名
			$photo['size']		=	$v['size'];
			$photo['savepath']	=	$v['savepath'].$v['savename'];
			$photo['privacy']	=	$albumInfo['privacy'];
			$photo['order']		=	0;
			$photoid			=	$this->table("{$pre}photo")->add($photo);
			$attachInfos[$k]['photoId']		=	$photoid;
			$attachInfos[$k]['albumId']		=	$albumId;
		}

		//更新相册照片数
		$photoCount			=	$this->table("{$pre}photo")->where("albumId='$albumId'")->field('count(1) as photocount')->find();
		$map['photoCount']	=	$photoCount['photocount'];
		$this->table("{$pre}photo_album")->where("id='$albumId'")->save($map);

		return $attachInfos;
	}
}
?>