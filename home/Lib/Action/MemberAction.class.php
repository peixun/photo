<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.paizoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2011-2-18
 * @Action  Index Action
 * @copyright Copyright &copy; 2009-2011 eyoo Software LLC
 +----------------------------------------------------------
 */

class MemberAction extends PublicAction{
    /**
     +----------------------------------------------------------
     * 个人用户中心
     +----------------------------------------------------------
    */

    public function index(){
         $this->checkUser();

		$user =M("User")->getById($_SESSION['uid']);
		$this->assign('user',$user);
		$this->assign('sps',$sps);

		$this->display();


    }
    /**
     +----------------------------------------------------------
     * @完善用户信息
     +----------------------------------------------------------
    */
    public function information(){
        $this->checkUser();
        $user =D("User")->getById($_SESSION['uid']);
        $datamap['pid']=321;
        $city = M('RegionConf')->where($datamap)->findAll();
        $this->assign('user',$user);
        $this->assign('city',$city);
        $this->assign('title','完善用户信息');
        $this->display();
    }
    public function get_city(){
		$data['pid'] = $_POST['id'];
		$data['region_level']= 3;
		$city = M('RegionConf')->where($data)->findAll();

		$str = '';
		for($i= 0;$i<count($city);$i++){
			$str .= '<option value="'.$city[$i]['id'].'">'.$city[$i]['name'].'</option>';
		}
		echo  $str;
	}

    public function get_area(){
		$data['pid'] = $_POST['city_id'];
		$data['region_level']= 4;
		$city = M('RegionConf')->where($data)->findAll();

		$str = '';
		for($i= 0;$i<count($city);$i++){
			$str .= '<option value="'.$city[$i]['id'].'">'.$city[$i]['name'].'</option>';
		}
		echo  $str;
	}

    /**
     +----------------------------------------------------------
     * @执行完善用户信息
     +----------------------------------------------------------
    */
    public function doInformation(){
        $this->checkUser();
        $User =D("User");
        $data['real_name']=$_POST['real_name'];
        $data['sex']=$_POST['sex'];
        $data['age']=$_POST['age'];

        $data['province']=25;
        $data['city']=321;
        $data['towns']=$_POST['towns'];

        $data['shequ']=$_POST['shequ'];
        $data['zip']=$_POST['zip'];
        $data['address']=$_POST['address'];
        $data['update_time']=time();
        $data['id']=$_SESSION['uid'];
        $user =$User->save($data);
        if($user){
             $this->assign("jumpUrl","__APP__/Member");
             $this->success ('完善用户信息成功！');
        }else{
             $this->error ('完善用户信息失败！');
        }

    }

    //修改头像
	public function setAvator(){

		$this->display();

	}

    public function uploadavatar(){
        $upload = new UploadFile ();
		//设置上传文件大小
		$upload->maxSize = 3292200;
		//设置上传文件类型
		$upload->allowExts = explode ( ',', 'jpg,gif,png,jpeg' );
		//设置附件上传目录
		$upload->thumb = false;
		$upload->thumbPrefix = 'l_,m_,s_';
		$upload->thumbMaxHeight = '377,162,118';
		$upload->thumbMaxWidth = '760,326,164';
		//设置上传文件规则
		$upload->saveRule = 'uniqid';
		//存在同名文件覆盖,可上传相同图片
		$upload->uploadReplace = 'true';
		//删除原图路径
		$upload->thumbRemoveOrigin = false;

		$upload->savePath = './Public/upload/avatar/';
		//$upload->saveRule = true;
		if (! $upload->upload ()) {
			 echo '上传失败。';
		} else {
			$uploadList = $upload->getUploadFileInfo ();

            $returnImage='./Public/upload/avatar//'.$uploadList[0]['savename'];
            Session::set('avatar',$returnImage);
             echo 'ok';
		}


	}

    public function editAvatar() {
		$filename = Session::get('avatar');
		$this->assign('fileurl',$filename);
		$arr = getimagesize($filename);
		$filename = substr($filename,1);
		$this->assign('filename',$filename);
		$this->assign('width',$arr[0]);
		$this->assign('height',$arr[1]);
		$this->display();
	}

    public function friends(){
         $this->checkUser();
         $friedurl="http://{$_SERVER['HTTP_HOST']}/Public/reg/fid/".$_SESSION['id'];

          $this->assign('friedurl',$friedurl);
         $this->display();
    }
    public function friendList(){
        $this->checkUser();

        $UserFriend =D("UserFriendView");
        $where['UserFriend.user_id']=$_SESSION['id'];
        $where['UserFriend.status']=1;
        $flist =$UserFriend->where($where)->order('dateline desc')->select();
        //echo $UserFriend->getlastsql();
        //dump($flist);
         if($flist!=false){
            $this->assign('flist',$flist);
        }
           //读取公告信息
        $article =D("Article")->where('cate_id=22')->limit('10')->order('id desc')->select();

        $this->assign('user',$user);
         //dump($article);
        if($article!=false){
            $this->assign('article',$article);
        }
          $this->display();
    }

    //修改密码
    public function password(){
     $this->checkUser();
     //读取公告信息
        $article =D("Article")->where('cate_id=22')->limit('10')->order('id desc')->select();

        $this->assign('user',$user);
         //dump($article);
        if($article!=false){
            $this->assign('article',$article);
        }
        $this->display();
    }
    /**
     +----------------------------------------------------------
     * @后台推荐的促销活动
     +----------------------------------------------------------
    */
    public function myAnnounce(){
        $this->checkUser();

        $Announcement=D("Announcement");
        import("@.ORG.Pages");
        //$map['ReservationSite.status']=1;


        $count = $Announcement->count('id');
        $listRows = empty ( $_REQUEST ["listRows"] ) ? 10 : $_REQUEST ["listRows"];
        $p = new Page ( $count, $listRows );
        $list = $Announcement->limit($p->firstRow . ',' . $p->listRows)->order('id desc')->select();
        //echo $Article->getlastsql();
        $this->assign('list',$list);
        $page = $p->show ();

        $this->assign('page',$page);
        $this->display();

    }
    /**
     +----------------------------------------------------------
     * @查看
     +----------------------------------------------------------
    */
    public function showAnnounce(){
        $this->checkUser();

        $Announcement=D("Announcement");
        $vo =$Announcement->getById($_GET['id']);
        if($vo){
            $newmap['click_count']= array('exp','click_count+1');
            $newmap['id']= $_GET['id'];
            $Announcement ->save($newmap);
            $ReadLog =D("ReadLog");
            $rmap['announcement_id']=$_GET['id'];
            $rmap['uid']=$_SESSION['uid'];
            $res =$ReadLog->where($rmap)->find();
            if($res==false){
                $rmap['create_time']=time();
                $ReadLog->add($rmap);
            }
            $this->assign('vo',$vo);
            $this->display();
        }else{
             $this->assign("jumpUrl","__APP__/Member");
            $this->error("查看的促销公告不存在！");
        }

    }
    /**
     +----------------------------------------------------------
     * @预约报名
     +----------------------------------------------------------
    */
    public function news(){
         $this->checkUser();
        $Article=D("Booking");
        import("@.ORG.Pages");
        //$map['ReservationSite.status']=1;
        $map['uid']=$_SESSION['uid'];

        $count = $Article->where($map)->count('id');
        $listRows = empty ( $_REQUEST ["listRows"] ) ? 10 : $_REQUEST ["listRows"];
        $p = new Page ( $count, $listRows );
        $list = $Article->where($map)->limit($p->firstRow . ',' . $p->listRows)->order('id desc')->select();
        //echo $Article->getlastsql();
        $this->assign('list',$list);
        $page = $p->show ();

        $this->assign('page',$page);
        $this->display();
    }
    /**
    +----------------------------------------------------------
    * @删除预约报名
    +----------------------------------------------------------
   */
   public function delBook(){
        $this->checkUser();
        $Watchlist=D("Booking");
        $id=$_POST['id'];
        $amap['id']=$id;
        $vo=$Watchlist->where($amap)->delete();
        if($vo){
            echo '1';
        }else{
            echo '0';
        }


   }
    /**
     +----------------------------------------------------------
     * @预约设计师
     +----------------------------------------------------------
    */
    public function bookDesigner(){
         $this->checkUser();
        $Article=D("DesignerBook");
        import("@.ORG.Pages");
        //$map['ReservationSite.status']=1;
        $map['uid']=$_SESSION['uid'];

        $count = $Article->where($map)->count('id');
        $listRows = empty ( $_REQUEST ["listRows"] ) ? 10 : $_REQUEST ["listRows"];
        $p = new Page ( $count, $listRows );
        $list = $Article->where($map)->limit($p->firstRow . ',' . $p->listRows)->order('id desc')->select();
        //echo $Article->getlastsql();
        $this->assign('list',$list);
        $page = $p->show ();

        $this->assign('page',$page);
         $this->display();
    }
    /**
    +----------------------------------------------------------
    * @删除预约设计师
    +----------------------------------------------------------
   */
   public function delbookDesigner(){
        $this->checkUser();
        $Watchlist=D("DesignerBook");
        $id=$_POST['id'];
        $amap['id']=$id;
        $vo=$Watchlist->where($amap)->delete();
        if($vo){
            echo '1';
        }else{
            echo '0';
        }


   }
	 //执行修改密码
    public function doPasswd(){
        $this->checkUser();
        if(empty($_POST['newPassword'])){
            $this->error ('密码不能为空');
        }

        if($_POST['newPassword']!=$_POST['repass']){
            $this->error ('两次输入是密码不一致');
        }
        $password=trim($_POST['oldpws']);
        $id=$_SESSION['uid'];
        $user =D('User')->getById($id);
        if($user['user_pwd']==md5($password)){
            $data['id']=$id;
            $data['user_pwd']=md5($_POST['newPassword']);
            $data['update_time']=time();
            $users =D('User')->save($data);
            //echo D('User')->getlastsql();
            if($users){
                if($_POST['type']==2){
                    $this->assign("jumpUrl","__APP__/Company/management");
                }else{
                    $this->assign("jumpUrl","__APP__/Member");
                }
                $this->success ('修改用户密码成功！');
            }else{
                 $this->error ('修改用户密码失败！');
            }
        }else{
            $this->error ('输入的旧密码不正确');
        }

    }


   //我的收藏
   public function myAttention(){
        $this->checkUser();
        $Article=D("Watchlist");
        import("@.ORG.Pages");
        //$map['ReservationSite.status']=1;
        $map['uid']=$_SESSION['uid'];

        $count = $Article->where($map)->count('id');
        $listRows = empty ( $_REQUEST ["listRows"] ) ? 10 : $_REQUEST ["listRows"];
        $p = new Page ( $count, $listRows );
        $list = $Article->where($map)->limit($p->firstRow . ',' . $p->listRows)->order('id desc')->select();
        //echo $Article->getlastsql();
        $this->assign('list',$list);
        $page = $p->show ();

        $this->assign('page',$page);
        $this->display();


   }
   /**
    +----------------------------------------------------------
    * @删除收藏
    +----------------------------------------------------------
   */
   public function delAttention(){
        $this->checkUser();
        $Watchlist=D("Watchlist");
        $id=$_POST['id'];
        $amap['id']=$id;
        $vo=$Watchlist->where($amap)->delete();
        if($vo){
            echo '1';
        }else{
            echo '0';
        }


   }


/**
 +----------------------------------------------------------
 *预约工地
 +----------------------------------------------------------
*/
 public function myConstruction(){
      $this->checkUser();

    $Article=D("ReservationSiteView");
    import("@.ORG.Pages");
    //$map['ReservationSite.status']=1;
    $map['ReservationSite.uid']=$_SESSION['uid'];

    $count = $Article->where($map)->count('ReservationSite.id');
    $listRows = empty ( $_REQUEST ["listRows"] ) ? 10 : $_REQUEST ["listRows"];
    $p = new Page ( $count, $listRows );
    $list = $Article->where($map)->limit($p->firstRow . ',' . $p->listRows)->order('id desc')->select();
   // echo $Article->getlastsql();
   // dump($list);
    $this->assign('list',$list);
    $page = $p->show ();

    $this->assign('page',$page);
    $this->display();

 }

    /**
    +----------------------------------------------------------
    * @删除收藏
    +----------------------------------------------------------
   */
   public function delConstruction(){
        $this->checkUser();
        $Watchlist=D("ReservationSite");
        $id=$_POST['id'];
        $amap['id']=$id;
        $vo=$Watchlist->where($amap)->delete();
        if($vo){
            echo '1';
        }else{
            echo '0';
        }


   }
}
?>