<?php
/**
  * @Link    http://www.TOP-SERVE.cn
  * @author  eric <yangxiao242@gmail.com>
  * @time    2012-03-20
  * @Action  Public Action
  * @copyright Copyright &copy; 2009-2012 higame Software LLC
  */
class PublicAction extends BaseAction {


    /**
    * reg
    */
    public function reg(){
        $fid=$_GET['fid'];
        $this->assign('title','用户注册');
        $this->assign('fid',$fid);
        $this->display();
    }

    //验证验证码

    function checkVerify()
    {
    	if($_SESSION['verify'] !=md5($_POST['verify']))
        {
          echo "0";
		 }else
		 {
		 	echo "1";
		 }
    }
	/**
     +----------------------------------------------------------
     * 用户注册
     +----------------------------------------------------------
    */

	public function insertUser(){


		 $_POST['status'] = 1;
		 $User = D("User");
		 if(false ===  $User->create() )
		 {
		 	$this->error($User->getError());
  		 }
         
        $User->active=1;
        

         dump($_POST);
          $User->type=$_POST['type'];
		  $result = $User->add();
          echo $User->getlastsql();
          //exit;
		  if($result !== null){
           

            $refer_url = U('Public/login');
		  	$this->assign("jumpUrl",$refer_url);
			$this->success('注册成功！');
		  }else{
			$this->error('注册失败！');
		  }
	}
    /**
    * 注册协议
    */
    public function Agreement(){
        $this->assign('title','注册协议');
        $this->display();
    }
    /**
     +----------------------------------------------------------
     * 用户登录
     +----------------------------------------------------------
    */

	public function login(){
		$this->display();
	}
    /**
     +----------------------------------------------------------
     * 验证用户是否登录
     +----------------------------------------------------------
    */

   protected function checkUser() {
		if(!isset($_SESSION['uid'])) {
			$this->assign('waitSecond','3');
			$this->assign('jumpUrl',__APP__.'/Public/login');
			$this->error('请先登录！');
		}
    }
    /**
     +----------------------------------------------------------
     * 用户登录操作
     +----------------------------------------------------------
    */

    public function checkLogins() {
    /*判断验证码*/
       if($_POST['indexlogin']=='1'){
          if($_SESSION['verify'] !=md5($_POST['verify']))
            {
              $this->assign('jumpUrl',__APP__.'/Public/login');
               $this->error('验证码错误！');
           }
        }
            //判断登录错误次数
            $session_id=$_COOKIE['PHPSESSID'];
            $times =localStrToTimeMin('today');
            $loginmap['create_time']=array('gt',$times);
            $loginRec=M('LoginRecord')->where($loginmap)->count('id');
            if($loginRec>4){
                $this->assign("jumpUrl","__APP__/");
                $this->error('你登录的错误超过5次，明天再来吧！');
            }

            if(empty($_POST['user_name'])){
                    $this->error('用户名不能为空！');
            }
            $Member=M("User");
            //复合查询邮箱，昵称手机号登陆
            $where['email']  = $_POST['user_name'];
            $where['mobile'] = $_POST['user_name'];
            //$where['active'] = 1;
            $where['_logic'] = 'or';
            $map['_complex'] = $where;

            $vo=$Member->where($map)->find();


            if($vo!=false){
                if($vo['status']==0){
                     $this->error('企业用户审核中,请联系得利网客服！');
                }
                if($vo['user_pwd']==md5($_POST['user_pwd'])){


                          $_SESSION['uid']=$vo['id'];
                          $_SESSION['email']=$vo['email'];
                          $_SESSION['user_name']=$vo['user_name'];
                          $_SESSION['type']=$vo['type'];
                         //$expire =3600*24*7;
			            //setcookie('user_name',$_POST['user_name'],time()+$expire);
			            //setcookie('password', $_POST['user_pwd'],time()+$expire);
                        if($_POST['remarkpass']==1){

                            setcookie('remarkpass',1,time()+3600*24*7,'/');

                            setcookie('user_name',$_POST['user_name'],time()+3600*24*7,'/');
			                setcookie('password',$_POST['user_pwd'],time()+3600*24*7,'/');

                        }else{

                            setcookie('remarkpass',1,time()-3600*24*7,'/');

                            setcookie('user_name',$_POST['user_name'],time()-3600*24*7,'/');
			                setcookie('password',$_POST['user_pwd'],time()-3600*24*7,'/');

                        }

						//跳转至登录前输入的url
						if ( $_SESSION['refer_url'] != '' ) {
							$refer_url	=	$_SESSION['refer_url'];
							unset($_SESSION['refer_url']);
						}else {
	                        if($vo['type']=='1'){
								    $refer_url = U('Member/index');
                                }else{
                                    $refer_url = U('Company/management');
                                }
						}
							$logindate['last_login_time']=time();
							$logindate['last_ip']=get_client_ip();
							$logindate['id']=$vo['id'];
							$Member->save($logindate);

						  $this->assign("jumpUrl",$refer_url);
                          $this->success('登录成功');

//                      }else{
//                         $_SESSION['email']=$_POST['email'];
//                         $this->assign("jumpUrl","__URL__/sendmail");
//                         $this->error('该用户请邮件激活！');
//                      }
                }else{


                    $logindate['session_id']=$session_id;
                    $logindate['ip']=get_client_ip();
                    $logindate['create_time']=time();
                    M('LoginRecord')->add($logindate);
                    $this->assign("jumpUrl","__APP__/");
                     $this->error('密码不正确！');
                }
            }else{

                $logindate['session_id']=$session_id;
                $logindate['ip']=get_client_ip();
                $logindate['create_time']=time();
                M('LoginRecord')->add($logindate);
                $this->assign("jumpUrl","__APP__/");
                $this->error('用户名不存在！');
            }
    }

 // 重发激活邮件
    public function sendActive() {

        $mmap['email'] =$_POST['email'];
        $member   =  M('User')->where($mmap)->field('id,nickname,email')->find();

        if($member){
        if ($_POST['Active']==1)
        {
            $sendmail =$_POST['email'];
        }else{
            $sendmail=$member['email'];
        }
        // 发送邮件激活
        import('@.ORG.Base64');
        $key =  base64_encode(md5($member['id']).Base64::encrypt($member['id'],'think').md5($member['email']));
        $result   =  sendMail($sendmail,'重发激活邮件',"感谢您注册拍物园会员{$member['nickname']}，您需要完成邮箱激活后才能进行进行竞拍。[ <a href='http://{$_SERVER['HTTP_HOST']}/Public/active/$key'>点击激活</a> ]<p>本邮件为系统自动发送，请不要直接回复！");
        if($result) {
            $this->success('重新发送激活邮件成功！');
        }else{
            $this->error('重新发送激活邮件失败！');
        }
        }else{
             $this->error('输入的注册时的email不对！');
        }
    }

    // 重发激活邮件
    public function sendActives() {

        $mmap['email'] =$_POST['email'];
        $member   =  M('User')->where($mmap)->field('id,nickname,email')->find();

        // 发送邮件激活
        import('@.ORG.Base64');
        $key =  base64_encode(md5($member['id']).Base64::encrypt($member['id'],'think').md5($member['email']));
        $result   =  sendMail($member['email'],'重发激活邮件',"感谢您注册拍物园会员{$member['nickname']}，您需要完成邮箱激活后才能进行进行竞拍。[ <a href='http://{$_SERVER['HTTP_HOST']}/Public/active/$key'>点击激活</a> ]<p>本邮件为系统自动发送，请不要直接回复！");
        if($result) {
            $this->success('重新发送激活邮件成功！');
        }else{
            $this->error('重新发送激活邮件失败！');
        }
    }

    // 会员激活
    public function active() {
        import('@.ORG.Base64');
        $auth =  base64_decode($_GET['active']);
        $hashId = substr($auth,0,32);
        $hashEmail   =  substr($auth,-32);
        $id = Base64::decrypt(substr(substr($auth,32),0,-32),'think');
        if(md5($id)==$hashId) {
            $Member   =  M('User');
            $member   =  $Member->field('id,nickname,email,active,parent_id')->where('id='.(int)$id)->find();
            //dump($member);
            //exit;
            if($member && $member['active']=='0' && md5($member['email'])==$hashEmail) {
                $result   =  $Member->where('id='.(int)$id)->setField('active',1);
                if($result) {
                    $_SESSION['active']  =  1;


                    $friedmap['id']=$member['parent_id'];
                    $friedmap['balance']=array('exp','balance+50');
                    $friedmap['score']=array('exp','score+50');
                    $Member ->save($friedmap);
                    //$this->assign('jumpUrl','__APP__/');
                    $this->assign("jumpUrl","__APP__/");
                    $this->success('拍物园账号'.$member['nickname'].'对应的邮箱'.$member['email'].'激活完成！');
                }
            }
            $this->_404('错误操作或者该邮箱已经激活！');
        }else{
            $this->_404('非法操作！');
        }
    }
    //忘记密码
    public function forgetpw(){
        //dump($_SERVER);

        $this->display();
    }

    public function doforgetpw(){


		//if ( !$this->isValidEmail($_POST['email']) )
		//	$this->error(L('MAIL_FORMAT_ERROR'));

        $where['email']  = $_POST['email'];
        $where['mobile'] = $_POST['email'];
        //$where['active'] = 1;
        $where['_logic'] = 'or';
        $map['_complex'] = $where;

		$user =	M("user")->where($map)->find();

        if(!$user) {
        	$this->error(L("EMAIL_NOT_REG"));
        }else {
            $code = base64_encode( $user["id"] . "." . md5($user["id"] . '+' . $user["password"]) );
            $url  = U('Public/resetPassword', array('code'=>$code));
            $body = <<<EOD
<strong>{$user["uname"]}，你好: </strong><br/>

您只需通过点击下面的链接重置您的密码: <br/>

<a href="http://{$_SERVER['SERVER_NAME']}/$url">"http://{$_SERVER['HTTP_HOST']}/$url"</a><br/>

如果通过点击以上链接无法访问，请将该网址复制并粘贴至新的浏览器窗口中。<br/>

如果你错误地收到了此电子邮件，你无需执行任何操作来取消帐号！此帐号将不会启动。
EOD;

			//global $ts;
			//$email_sent = service('Mail')->send_email($user['email'], L('reset')."{$ts['site']['site_name']}".L('password'), $body);

             $result   =  sendMail($user['email'],'重置用户密码',$body);
            if ($result) {
	            $this->assign('jumpUrl',"__APP__/Public/loginmail/".$user['email']);
	            $this->success(L('send_you_mailbox').$user['email'] .L('notice_accept'));
            }else {
            	$this->error('找回密码邮件发送失败！');
            }
		}
    }

    public function resetPassword() {
		$code = explode('.', base64_decode($_GET['code']));
        $user = M('user')->where('`id`=' . $code[0])->find();

        if ( $code[1] == md5($code[0].'+'.$user["password"]) ) {
	        $this->assign('email',$user["email"]);
	        $this->assign('code', $_GET['code']);
	        $this->display();
        }else {
        	$this->error(L("link_error"));
        }
	}

	public function doResetPassword() {
		if($_POST["password"] != $_POST["repassword"]) {
        	$this->error(L("password_same_rule"));
        }

		$code = explode('.', base64_decode($_POST['code']));
        $user = M('user')->where('`id`=' . $code[0])->find();
        ///echo M('user')->getlastsql();
        if ( $code[1] == md5($code[0] . '+' . $user["password"]) ) {

	        $data['user_pwd'] = md5($_POST['password']);
	        $data['id'] = $code[0];
	        $data['last_login_time'] = time();
	        $res = D('user')->save($data);

	        if ($res) {
	        	$this->assign('jumpUrl', U('Public/login'));
	        	$this->success('重置用户名密码成功！');
	        }else {
	        	$this->error(L('save_error_retry'));
	        }
        }else {
        	$this->error(L("safety_code_error"));
        }
	}
    //检查Email地址是否合法
	public function isValidEmail($email) {

			return preg_match("/[_a-zA-Z\d\-\.]+@[_a-zA-Z\d\-]+(\.[_a-zA-Z\d\-]+)+$/i", $email) !== 0;

	}

     public function loginmail(){

           $email=$_REQUEST['loginmail'];
           if(strpos($email,"@163.com")!==false){
             $url="mail.163.com";
           }elseif(strpos($email,"@gmail.com")!==false){
                $url="www.gmail.com";
           }elseif(strpos($email,"@hotmail.com")!==false){
                 $url="www.hotmail.com";
           }elseif(strpos($email,"@qq.com")!==false){
                 $url="mail.qq.com";
           }elseif(strpos($email,"@sina")!==false){
                 $url="mail.sina.com.cn";
           }elseif(strpos($email,"@sohu.com")!==false){
                 $url="mail.sohu.com";
           }elseif(strpos($email,"@126.com")!==false){
                 $url="www.126.com";
           }elseif(strpos($email,"@yahoo")!==false){
                 $url="mail.cn.yahoo.com";
           }elseif(strpos($email,"@tom.com")!==false){
                 $url="mail.tom.com";
           }elseif(strpos($email,"@139.com")!==false){
                 $url="mail.139.com";
           }


           $this->assign("email",$email);
           $this->assign("url",$url);
           $this->display('mail');
    }
    /*----------------------------------------------
    * logout
    ----------------------------------------------*/
    public function logout(){
        if(isset($_SESSION['uid'])) {
            unset($_SESSION['loginId']);
            unset($_SESSION[C('USER_AUTH_KEY')]);
            unset($_SESSION);

            session_destroy();
            $this->assign("jumpUrl","__APP__/");
            $this->success('登出成功！');
        }else {
            $this->assign("jumpUrl","__APP__/");
            $this->success( '已经登出！');
        }
    }


    /****
    * email验证
    */
    public function checkEmail(){
        $email=trim($_POST['email']);

        $Member=D("User");
        $where['email']=$email;
        $list=$Member->where($where)->find();

        if(empty($list)){
            echo '0';
        }else{
            echo '1';
        }
    }


    /****
    * 昵称验证
    */
    public function checkNickname(){
        $email=trim($_POST['nickname']);

        $Member=D("User");
        $where['nickname']=$email;
        $list=$Member->where($where)->find();

        if(empty($list)){
            echo '0';
        }else{
            echo '1';
        }
    }



    /****
    * email验证
    */
    public function checkPhone(){
        $mobiles=trim($_POST['mobiles']);
        $Member=D("User");
        $where['mobile']=$mobiles;
        $list=$Member->where($where)->find();

        if($list){
            echo '0';
        }else{
            echo true;
        }
    }
    /****
    * 通行证验证
    */
     public function checkAccount(){
        $username=trim($_POST['user_name']);
        $Member=D("User");
        $where['user_name']=$username;
        $list=$Member->where($where)->find();
        if($list){
            echo '0';
        }else{
            echo '1';
        }
    }
    /****
    * 通行证验证
    */
     public function checkCompany(){
        $username=trim($_POST['company_name']);
        $Member=D("Company");
        $where['company_name']=$username;
        $list=$Member->where($where)->find();
        if($list){
            echo '0';
        }else{
            echo '1';
        }
    }


    /****
    *检查就密码
    */
    public function checkOldpw(){
        $password=trim($_POST['oldpws']);
        $Member=D("User");
        $id=$_SESSION['id'];
        //$where['user_pwd']=MD5($password);
        $list=$Member->getById($id);
       // echo $Member->getlastsql();
        if($list){
            if($list['user_pwd']==md5($password)){
                echo '1';
            }else{
                echo '0';
            }
        }else{
            echo '0';
        }
    }
    /****
    *验证码
    */

	public function verify()
    {
		$type	 =	 isset($_GET['type'])?$_GET['type']:'gif';
        import("@.ORG.Image");
        Image::buildImageVerify(4,1,$type);
    }
    //返回当前目录
	public function getRealPath() {
		return getcwd ();
	}
    protected function uploadFile($water = 0, $dir = "attachment", $uploadType = 0, $big_width, $big_height, $small_width, $small_height, $showstatus = FALSE) {
		$water_mark = $this->getRealPath () . eyooC ( "WATER_IMAGE" );
		//dump( eyooC);
		$alpha = eyooC ( "WATER_ALPHA" );
		$place = eyooC ( "WATER_POSITION" );
		import ( "@.ORG.UploadFile" );
		$upload = new UploadFile ();
		$upload->maxSize = eyooC ( "MAX_UPLOAD" );
		$upload->allowExts = explode ( ",", eyooC ( "ALLOW_UPLOAD_EXTS" ) );
		if ($uploadType) {
			$save_rec_Path = "/Public/upload/" . $dir . "/origin/";
		} else {
			$save_rec_Path = "/Public/upload/" . $dir . "/";
		}
		$savePath = $this->getRealPath () . $save_rec_Path;
		if (! is_dir ( $savePath )) {
			mk_dir ( $savePath );
		}
		$upload->saveRule = "uniqid";
		$upload->savePath = $savePath;
		if ($upload->upload ()) {
			$uploadList = $upload->getUploadFileInfo ();
			foreach ( $uploadList as $k => $fileItem ) {
				if ($uploadType) {
					$big_width = $big_width;
					$big_height = $big_height;
					$small_width = $small_width;
					$small_height = $small_height ;
					//echo 	$big_width;
					//echo 	$big_height;
					//echo 	$small_width;
					//echo 	$small_height;
					$file_name = $fileItem ['savepath'] . $fileItem ['savename'];
					$big_save_path = str_replace ( "origin", "big", $savePath );
					if (! is_dir ( $big_save_path )) {
						mk_dir ( $big_save_path );
					}
					$big_file_name = str_replace ( "origin", "big", $file_name );
					if (eyooC ( "AUTO_GEN_IMAGE" ) == 1) {
						Image::thumb ( $file_name, $big_file_name, "", $big_width, $big_height );
					} else {
						@copy ( $file_name, $big_file_name );
					}
					if ($water && file_exists ( $water_mark ) && eyooC ( "AUTO_GEN_IMAGE" ) == 1) {
						Image::water ( $big_file_name, $water_mark, $big_file_name, $alpha, $place );
					}
					$small_save_path = str_replace ( "origin", "small", $savePath );
					if (! is_dir ( $small_save_path )) {
						mk_dir ( $small_save_path );
					}
					$small_file_name = str_replace ( "origin", "small", $file_name );
					Image::thumb ( $file_name, $small_file_name, "", $small_width, $small_height );
					$big_save_rec_Path = str_replace ( "origin", "big", $save_rec_Path );
					$small_save_rec_Path = str_replace ( "origin", "small", $save_rec_Path );
					$uploadList [$k] ['recpath'] = $save_rec_Path;
					$uploadList [$k] ['bigrecpath'] = $big_save_rec_Path;
					$uploadList [$k] ['smallrecpath'] = $small_save_rec_Path;
				} else {
					$uploadList [$k] ['recpath'] = $save_rec_Path;
					$file_name = $fileItem ['savepath'] . $fileItem ['savename'];
					if (! $water && ! file_exists ( $water_mark )) {
						Image::water ( $file_name, $water_mark, $file_name, $alpha, $place );
					}
				}
			}
			if ($showstatus) {
				$result ['status'] = TRUE;
				$result ['uploadList'] = $uploadList;
				$result ['msg'] = "";
				return $result;
			}
			return $uploadList;
		}
		if ($showstatus) {
			$result ['status'] = FALSE;
			$result ['uploadList'] = FALSE;
			$result ['msg'] = $upload->getErrorMsg ();
			return $result;
		}
		return $uploadList;
	}

    public function saveTag($tags, $id, $module = MODULE_NAME) {

		if (! empty ( $tags ) && ! empty ( $id )) {

			$Tag = D ( "Tag" );
			$Tagged = D ( "Tagged" );
			// 记录已经存在的标签
			$exists_tags = $Tagged->where ( "module='{$module}' and record_id={$id}" )->getField ( "id,tag_id" );
			$Tagged->where ( "module='{$module}' and record_id={$id}" )->delete ();
			$tags =  str_replace ( '，',',', $tags );
            $tags2 = explode ( ',', $tags );

			foreach ( $tags2 as $key => $val ) {
				$val = trim ( $val );
				if (! empty ( $val )) {
					$tag = $Tag->where ( "module='{$module}' and name='{$val}'" )->find ();
					if ($tag) {
						// 标签已经存在
						if (! in_array ( $tag ['id'], $exists_tags )) {
							$Tag->where ( 'id=' . $tag ['id'] )->setInc ( 'count' );
						}
					} else {
						// 不存在则添加
						$tag = array ();
						$tag ['name'] = $val;
						$tag ['count'] = 1;
						$tag ['module'] = $module;
						$result = $Tag->add ( $tag );
						$tag ['id'] = $result;
					}
					// 记录tag关联信息
					$t = array ();
					$t ['uid'] = $_SESSION['uid'];
					$t ['module'] = $module;
					$t ['record_id'] = $id;
					$t ['create_time'] = time ();
					$t ['tag_id'] = $tag ['id'];
					$Tagged->add ( $t );
				}
			}
		}
	}

    //第三方登录页面显示
	function tryOtherLogin(){
		if ( !in_array($_GET['type'], array('sina', 'douban', 'qq')) ) {
			$this->error(L('parameter_error'));
		}
		include(VENDOR_PATH ."login/{$_GET['type']}.class.php");
        $platform = new $_GET['type']();
        redirect($platform->getUrl());
	}

	// 腾讯回调地址
	public function qqcallback() {
		include(VENDOR_PATH ."login/qq.class.php");
        $qq = new qq();
        $qq->checkUser();
        redirect(U('/Public/otherlogin'));
	}
    //新浪回调地址
    public function callback(){
		include_once(VENDOR_PATH ."login/sina.class.php");
		$sina = new sina();
		$sina->checkUser();
		redirect(U('/Public/otherlogin'));
	}

	//外站帐号登陆
	public function otherlogin(){
		if ( !in_array($_SESSION['open_platform_type'], array('sina', 'douban', 'qq')) ) {
			$this->error(L('not_authorised'));
		}

		$type = $_SESSION['open_platform_type'];
		include_once( VENDOR_PATH."login/{$type}.class.php" );
		$platform = new $type();
		$userinfo = $platform->userInfo();
		// 检查是否成功获取用户信息
		if ( empty($userinfo['id']) || empty($userinfo['uname']) ) {
			$this->assign('jumpUrl', SITE_URL);
			$this->error(L('user_information_filed'));
		}

        $info = M('login')->where("`type_uid`='".$userinfo['id']."' AND type='{$type}'")->find();

		if ( $info != false) {
			$user = M('User')->where("id=".$info['uid'])->find();
			if (empty($user)) {
				// 未在本站找到用户信息, 删除用户站外信息,让用户重新登陆
				M('login')->where("type_uid=".$userinfo['id']." AND type='{$type}'")->delete();
			}else {
				if ( $info['oauth_token'] == '' ) {
					$syncdata['login_id']        	= $info['login_id'];
					$syncdata['oauth_token']        = $_SESSION[$type]['access_token']['oauth_token'];
					$syncdata['oauth_token_secret'] = $_SESSION[$type]['access_token']['oauth_token_secret'];
					M('login')->save($syncdata);
				}

                    $vo =M("User")->getById($info['uid']);
                    $_SESSION['uid']=$vo['id'];
                    $_SESSION['email']=$vo['email'];
                    $_SESSION['user_name']=$vo['user_name'];
                    $_SESSION['type']=$vo['type'];



                    //跳转至登录前输入的url
                    if ( $_SESSION['refer_url'] != '' ) {
                    $refer_url	=	$_SESSION['refer_url'];
                    unset($_SESSION['refer_url']);
                    }else {
                    if($vo['type']=='1'){
                            $refer_url = U('Member/index');
                        }else{
                            $refer_url = U('Company/management');
                        }
                    }
			    redirect($refer_url );
			}
		}
		$this->assign('user',$userinfo);
		$this->assign('type',$type);
		//$this->setTitle(L('third_party_account_login'));
		$this->display();
	}

    public function bindaccount() {
		if ( ! in_array($_POST['type'], array('douban','sina','qq')) ) {
			$this->error(L('parameter_error'));
		}

		$psd  = ($_POST['passwd']) ? $_POST['passwd'] : true;
		$type = $_POST['type'];

        if(M('User')->where('email='.$_POST['email'])->find()){
            $this->assign('jumpUrl', '__APP__/Public/login');
            $this->error('该email的帐号已经存在，无需外站登录！');
        }
        if(M('User')->where('mobile='.$_POST['mobile'])->find()){
            $this->assign('jumpUrl', '__APP__/Public/login');
            $this->error('该手机号码的帐号已经存在，无需外站登录！');
        }
         $User = D("User");
         $data['user_name']=trim($_POST['user_name']);
         $data['email']=trim($_POST['email']);
         $data['mobile']=trim($_POST['mobile']);
         $data['create_time']=time();
         $data['update_time']=time();
         $data['status']=1;
         $data['type']=1;
         $data['user_pwd']=md5($psd);

         $user =$User->add($data);


		if ( $user) {
			include_once(VENDOR_PATH."login/{$type}.class.php" );
			$platform = new $type();
			$userinfo = $platform->userInfo();

			// 检查是否成功获取用户信息
			if ( empty($userinfo['id']) || empty($userinfo['uname']) ) {
				$this->assign('jumpUrl', SITE_URL);
				$this->error(L('user_information_filed'));
			}

			// 检查是否已加入本站
			$map['type_uid'] = $userinfo['id'];
			$map['type']     = $type;
			if ( ($local_uid = M('login')->where($map)->getField('uid')) && (M('user')->where('uid='.$local_uid)->find()) ) {
				$this->assign('jumpUrl', '__APP__/');
				$this->success(L('you_joined'));
			}

			$syncdata['uid']                = $user;
			$syncdata['type_uid']           = $userinfo['id'];
			$syncdata['type']               = $type;
			$syncdata['oauth_token']        = $_SESSION[$type]['access_token']['oauth_token'];
			$syncdata['oauth_token_secret'] = $_SESSION[$type]['access_token']['oauth_token_secret'];
			M('login')->add($syncdata);

			if ( M('login')->add($syncdata) ) {
                $_SESSION['uid']=$user;
                $_SESSION['email']=trim($_POST['email']);
                $_SESSION['user_name']=trim($_POST['user_name']);
                //$_SESSION['type']=($types == '1') ? '1' : '2';
                $_SESSION['type']= 1;

                // import("@.ORG.PassportService");
                // $PassportService = new PassportService();
                // $PassportService->registerLogin($user);

				$this->assign('jumpUrl', U('Member/index'));
				$this->success('帐号激活成功！');

			}else {
				$this->assign('jumpUrl', '__APP__/');
				$this->error('帐号激活失败');
			}
		}else {
			$this->error('帐号激活失败');
		}
	}

	// 激活外站登陆
	public function initotherlogin(){
		if ( ! in_array($_POST['type'], array('douban','sina', 'qq')) ) {
			$this->error(L('parameter_error'));
		}


		if( !isLegalUsername( t($_POST['uname']) ) ){
			$this->error(L('nickname_format_error'));
		}

		$haveName = M('User')->where( "`uname`='".t($_POST['uname'])."'")->find();
		if( is_array( $haveName ) && sizeof($haveName)>0 ){
			$this->error(L('nickname_used'));
		}

		$type = $_POST['type'];
		include_once(VENDOR_PATH."login/{$type}.class.php" );
		$platform = new $type();
		$userinfo = $platform->userInfo();

		// 检查是否成功获取用户信息
		if ( empty($userinfo['id']) || empty($userinfo['uname']) ) {
			$this->assign('jumpUrl', SITE_URL);
			$this->error(L('create_user_information_failed'));
		}

		// 检查是否已加入本站
		$map['type_uid'] = $userinfo['id'];
		$map['type']     = $type;
		if ( ($local_uid = M('login')->where($map)->getField('uid')) && (M('user')->where('uid='.$local_uid)->find()) ) {
			$this->assign('jumpUrl', SITE_URL);
			$this->success(L('you_joined'));
		}
		// 初使化用户信息, 激活帐号
		$data['uname']        = t($_POST['uname'])?t($_POST['uname']):$userinfo['uname'];
		$data['province']     = intval($userinfo['province']);
		$data['city']         = intval($userinfo['city']);
		$data['location']     = $userinfo['location'];
		$data['sex']          = intval($userinfo['sex']);
		$data['is_active']    = 1;
		$data['is_init']      = 1;
		$data['ctime']      = time();
		$data['is_synchronizing']  = ($type == 'sina') ? '1' : '0'; // 是否同步新浪微博. 目前仅能同步新浪微博

		if ( $id = M('user')->add($data) ) {
			// 记录至同步登陆表
			$syncdata['uid']                = $id;
			$syncdata['type_uid']           = $userinfo['id'];
			$syncdata['type']               = $type;
			$syncdata['oauth_token']        = $_SESSION[$type]['access_token']['oauth_token'];
			$syncdata['oauth_token_secret'] = $_SESSION[$type]['access_token']['oauth_token_secret'];
			M('login')->add($syncdata);

			// 转换头像
			if ($_POST['type'] != 'qq') { // 暂且不转换QQ头像: QQ头像的转换很慢, 且会拖慢apache
				D('Avatar')->saveAvatar($id,$userinfo['userface']);
			}

			// 将用户添加到myop_userlog，以使漫游应用能获取到用户信息
			$userlog = array(
				'uid'		=> $id,
				'action'	=> 'add',
				'type'		=> '0',
				'dateline'	=> time(),
			);
			M('myop_userlog')->add($userlog);

			service('Passport')->loginLocal($id);

			$this->registerRelation($id);

			redirect( U('home/Public/followuser') );
		}else{
			$this->error('account_sync_error');
		}
	}

}
?>