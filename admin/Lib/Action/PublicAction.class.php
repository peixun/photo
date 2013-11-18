<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-3-18
 * @Action  Index Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */
class PublicAction extends CommonAction {
	// 检查用户是否登录

	protected function checkUser() {
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			$this->assign('jumpUrl','Public/login');
			$this->error(L('NOT_LOGIN'));
		}
	}



	// 用户登录页面
	public function login() {
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			$this->display();
		}else{
			$this->redirect('Index/index');
		}
	}

	public function index()
	{

		//如果通过认证跳转到首页
		redirect(__APP__);
	}

	// 用户登出
    public function logout()
    {
        if(isset($_SESSION[C('USER_AUTH_KEY')])) {
        	//登出时删除相关未用到的商品图片
			$list = D("GoodsGallery")->where("session_id='".$_SESSION['verify']."' and goods_id = 0")->findAll();
			foreach($list as $item)
			{
				@unlink($this->getRealPath().$item['small_img']);
				@unlink($this->getRealPath().$item['big_img']);
				@unlink($this->getRealPath().$item['origin_img']);
			}
			D("GoodsGallery")->where("session_id='".$_SESSION['verify']."' and goods_id = 0")->delete();

			$spec_list = D("GoodsSpec")->where("session_id='".Session::id()."'")->findAll();
			foreach($spec_list as $spec_item)
			{
				if(D("Spec")->where("img='".$spec_item['img']."'")->count()==0)
				{
					@unlink($this->getRealPath().$spec_item['img']);
				}
			}
			D("GoodsSpec")->where("session_id='".Session::id()."'")->delete();

			//del by chenfq 2010-06-08 begin
			$current_lang = $_SESSION['fanwe_lang'];  //先保存语言环境，保证登出操作不丢失语言环境
			$_SESSION[C('USER_AUTH_KEY')] = NULL;
			unset($_SESSION[C('USER_AUTH_KEY')]);
			unset($_SESSION);
			session_destroy();
			//del by chenfq 2010-06-08 end

			//add by chenfq 2010-06-08 无法退出问题
			//$_SESSION[C('USER_AUTH_KEY')] = NULL;


			$this->assign("jumpUrl",U("Public/login"));
            $this->success(L('LOGOUT_SUCCESS'));
        }else {
            $this->error(L('LOGOUT_ALREADY'));
        }
    }

	// 登录检测
	public function checkLogin() {

		if(empty($_POST['adm_name'])) {
			$this->error(L('ADM_NAME_ERROR'));
		}elseif (empty($_POST['adm_pwd'])){
			$this->error(L('ADM_PWD_REQUIRE'));
		}elseif (empty($_POST['verify'])){
			$this->error(L('VERIFY_REQUIRE'));
		}
        //生成认证条件
        $map            =   array();
		// 支持使用绑定帐号登录
		$map['adm_name']	= $_POST['adm_name'];
        $map["status"]	=	array('gt',0);
		if($_SESSION['verify'] != md5($_POST['verify'])) {
			$this->error(L('VERIFY_ERROR'));
		}
		import ( '@.ORG.RBAC' );
        $authInfo = RBAC::authenticate($map);
        //使用用户名、密码和状态的方式进行认证
        if(false === $authInfo) {
        	$this->saveLog(0,0);
            $this->error(L('ADM_NAME_NOT_EXIST'));
        }else {
            if($authInfo['adm_pwd'] != md5($_POST['adm_pwd'])) {
            	$this->saveLog(0,0);
            	$this->error(L('ADM_PWD_ERROR'));
            }
            $EXPIRED_TIME = intval(eyooC("EXPIRED_TIME"));
            if ($EXPIRED_TIME >0)//add by chenfq 2020-05-25 防止$EXPIRED_TIME值为空时，无法登陆系统
            	Session::setExpire(time()+eyooC("EXPIRED_TIME")*60);
            $_SESSION[C('USER_AUTH_KEY')]	=	$authInfo['id'];
            $_SESSION['adm_name']		=	$authInfo['adm_name'];
            $_SESSION['last_time']		=	$authInfo['last_time'];
			$_SESSION['login_count']	=	$authInfo['login_count'];
			$city_ids = array();
            if($authInfo['adm_name']==eyooC('SYS_ADMIN')) {
            	$_SESSION[C('ADMIN_AUTH_KEY')]		=	true;
           		//查询所有的城市ID

            	$city_ids_f = M("GroupCity")->findAll();
            	foreach ($city_ids_f as $v)
            	{
            		array_push($city_ids,$v['id']);
            	}
            	array_push($city_ids,0);
            	$_SESSION['all_city'] = true;
            }
            else
            {
            	//查询所有的城市ID

            	//$city_ids_f = M("AdminCity")->field("city_id as id")->where("admin_id=".$authInfo['id'])->findAll();//用于开启分城市管理
            	$city_ids_f = M("GroupCity")->findAll();
            	foreach ($city_ids_f as $v)
            	{
            		array_push($city_ids,$v['id']);
            	}
            	array_push($city_ids,0);
            	$_SESSION['all_city'] = true;
            }
            $_SESSION['admin_city_ids'] = $city_ids;

            //保存登录信息
			$User	=	M(C('USER_AUTH_MODEL'));
			$ip		=	get_client_ip();
			$time	=	gmtTime();
            $data = array();
			$data['id']	=	$authInfo['id'];
			$data['last_time']	=	$time;
			$data['login_count']	=	array('exp','login_count+1');
			$data['last_ip']	=	$ip;
			$User->save($data);

			// 缓存访问权限
            RBAC::saveAccessList();
           // $this->saveLog(1,0);
			$this->success(L('LOGIN_SUCCESS'));

		}
	}


	 public function verify()
    {
		$type	 =	 isset($_GET['type'])?$_GET['type']:'png';
		//dump($type);
		$type = 'png';
        import("@.ORG.Image");
        Image::buildImageVerify(4,1,$type);
    }

    public function uploadBatch()
    {
    	$gallery_data['goods_id'] = $_POST['goods_id'];
    	$gallery_data['session_id'] = $_POST['session_id'];
    	if(eyooC("WATER_MARK")){
          //  echo 'ok';
    		$result = $this->uploadFile(1,'goods',1,true);  //上传商品图片
    	}else{

    		$result = $this->uploadFile(0,'goods',1,true);  //上传商品图片
        }
    	$res = $result['uploadList'];
        //echo $gallery_data['goods_id'];
        //echo $gallery_data['session_id'];
        //dump($res);
    	$gallery_data['origin_img'] = $res[0]['recpath'].$res[0]['savename'];
    	$gallery_data['big_img'] = $res[0]['bigrecpath'].$res[0]['savename'];
    	$gallery_data['small_img'] = $res[0]['smallrecpath'].$res[0]['savename'];
    	if($result['status'])
    	{
			$id = D("GoodsGallery")->add($gallery_data);
			$gallery_data['id'] = $id;
			$data['msg'] = '';
			$data['data'] = $gallery_data;
			echo json_encode($data);
    	}
    	else
    	{
    		$data['msg'] = $result['msg'];
			$data['data'] = '';
			echo json_encode($data);
    	}
    }

public function uploadBatchNews()
    {
    	$gallery_data['news_id'] = $_POST['news_id'];
    	$gallery_data['session_id'] = $_POST['session_id'];
    	if(eyooC("WATER_MARK")){
          
    		$result = $this->uploadFile(1,'news',1,true);  //上传商品图片
    	}else{

    		$result = $this->uploadFile(0,'news',1,true);  //上传商品图片
        }
    	$res = $result['uploadList'];
        //echo $gallery_data['goods_id'];
        //echo $gallery_data['session_id'];
        //dump($result);
    	$gallery_data['origin_img'] = $res[0]['recpath'].$res[0]['savename'];
    	$gallery_data['big_img'] = $res[0]['bigrecpath'].$res[0]['savename'];
    	$gallery_data['small_img'] = $res[0]['smallrecpath'].$res[0]['savename'];
		//echo json_encode($result);
		//exit;
    	if($result['status'])
    	{
			//echo $gallery_data['origin_img'];
			//echo $gallery_data['big_img'];
			//echo $gallery_data['small_img'];
			$id = D("NewsGallery")->add($gallery_data);
			//echo D("NewsGallery")->getlastsql();
			
			$gallery_data['id'] = $id;
			$data['msg'] = '';
			$data['data'] = $gallery_data;
			echo json_encode($data);
    	}
    	else
    	{
    		$data['msg'] = $result['msg'];
			$data['data'] = '';
			echo json_encode($data);
    	}
    }

 	public function uploadSpecIcon()
    {
    	$spec_id = intval($_REQUEST['spec_id']);
    	$idx = intval($_REQUEST['idx']);
    	//开始检测该规格是否能重定义图片
//    	// 存在该规格商品的引用不能重定义
//    	if(D("GoodsSpecItem")->where("spec1_id=".$spec_id." or spec2_id=".$spec_id)->count()>0)
//    	{
//    		$res['status'] = 0;
//    		$res['info'] = L("EXIST_SPEC_ITEM");
//    		echo json_encode($res);
//    		exit;
//    	}
    	$file = $this->uploadFile(0,'spec');  //上传商品图片
    	$spec_item = D("GoodsSpec")->getById($spec_id);

    	//开始检测源图能否删除，如为系统预设规格图则无法删除
    	if(D("Spec")->where("img='".$spec_item['img']."'")->count()==0)
    	{
    		@unlink($this->getRealPath().$spec_item['img']);
    	}
    	D("GoodsSpec")->where("id=".$spec_id)->setField("define_img",1);
    	D("GoodsSpec")->where("id=".$spec_id)->setField("img",$file[0]['recpath'].$file[0]['savename']);
    	$res['status'] = 1;
    	$res['info'] = $file[0]['recpath'].$file[0]['savename'];
    	$res['id'] = "spec_img_".$idx."_".$spec_id;

    	echo json_encode($res);
    	exit;
    }

    public function clearCache()
	{
		clear_cache();
       	$this->success(L('CLEAR_SUCCESS'),1);
	}
}
?>