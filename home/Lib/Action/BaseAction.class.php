<?php
/*---------------------------------------------------------
|+   Url       : www.thinkphp.cn
|+  系统项目组可以共用的基类库，继承则可，自动加载
-----------------------------------------------------------*/
class BaseAction extends Action {

	/* 控制器初始化处理 可以让所有项目组共同使用*/
	function _initialize()  {
		header("Content-Type:text/html; charset=utf-8");
			/*读取系统配置参数*/
			if (! file_exists ( DATA_PATH . '~config.php' )) {
			$config = D ("SysConf" );
			$list = $config->getField ('name,val' );
			//echo $config->getlastsql();
			$savefile = DATA_PATH . '~config.php';

			/*所有配置参数统一为大写*/
			$content = "<?php\nreturn " . var_export ( array_change_key_case ( $list, CASE_UPPER ), true ) . ";\n?>";
			if (! file_put_contents ( $savefile, $content )) {
				$this->error ( '配置缓存失败！' );
			}
		}
		$config = include_once DATA_PATH . '~config.php';
		C ( $config );

        if($config['SHOP_CLOSED']==1){
            $this->assign('title','网站关闭！');
            $this->display('close');
            exit;
        }
        $RegionConf = D ( "RegionConf" );
		$regWhere ["pid"] = 321;
		$regionConf = $RegionConf->where ( $regWhere )->select ();

        for($i=0;$i<count($regionConf);$i++){
			$info['area_id'] = $regionConf[$i]['id'];
			$info['pid'] = 0;
			$lists = M('Category')->where($info)->order('is_top desc,click_count desc')->findAll();

			if(!empty($lists)){
				$regionConf[$i]['shequ'] = $lists;
			}
		}

       // echo $RegionConf->getlastsql();
        //dump($regionConf);
		$this->assign ( "xiaoqu", $regionConf );

        $Page =D("Page");
        $vo_page =$Page->where('status=1')->select();

        $this->assign ( "vo_page", $vo_page );

	}

    /* 404错误控制器*/
	protected function _404($message='',$jumpUrl='/',$waitSecond=3) {
		$this->assign('msg',$message);
		if(!empty($jumpUrl)) {
			$this->assign('jumpUrl',$jumpUrl);
			$this->assign('waitSecond',$waitSecond);
		}
		$this->display(C('ACTION_404_TMPL'));
		exit;
	}

        //执行单图上传操作
	   protected function _upload($path,$save_name,$is_replace,$is_thumb,$thumb_name,$thumb_max_width) {

		if(!checkDir($path)){
			return '目录创建失败: '.$path;
		}

		import("@.ORG.UploadFile");
        $upload = new UploadFile();
        //设置上传文件大小
        $upload->maxSize	=	'2000000' ;
        //设置上传文件类型
        $upload->allowExts	=	explode(',',strtolower('jpg,gif,png,jpeg,bmp'));
		//存储规则
		$upload->saveRule	=	'uniqid';
		//设置上传路径
		$upload->savePath	=	$path;
        //保存的名字
        $upload->saveName   =   $save_name;
        //是否缩略图
        $upload->thumb          =   $is_thumb;
        $upload->thumbMaxWidth  =   $thumb_max_width;
        $upload->thumbFile      =   $thumb_name;

        //存在是否覆盖
        $upload->uploadReplace  =   $is_replace;
        //执行上传操作
        if(!$upload->upload()) {
            //捕获上传异常
            return $upload->getErrorMsg();
        }else{
			//上传成功
			return $upload->getUploadFileInfo();
    	}
    }
}
?>