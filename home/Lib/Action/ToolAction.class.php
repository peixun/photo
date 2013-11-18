<?php
// 本类由系统自动生成，仅供测试用途
class ToolAction extends PublicAction{
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

    public function get_city_name(){
		$data['id'] = $_POST['id'];

		$city = M('RegionConf')->where($data)->findAll();

		$str = '';
		for($i= 0;$i<count($city);$i++){
			$str .= '<option value="'.$city[$i]['id'].'">'.$city[$i]['name'].'</option>';
		}
		echo  $str;
	}

     public function get_huxing_name(){
		$data['pid'] = $_POST['id'];

		$city = M('Category')->where($data)->findAll();

		$str = '';
		for($i= 0;$i<count($city);$i++){
			$str .= '<option value="'.$city[$i]['id'].'">'.$city[$i]['name_1'].'</option>';
		}
		echo  $str;
	}

 //根据省获车型
	public function get_car(){
		$data['pid'] = $_POST['id'];

		$city = M('CarCate')->where($data)->findAll();

		$str = '';
		for($i= 0;$i<count($city);$i++){
			$str .= '<option value="'.$city[$i]['id'].'">'.$city[$i]['name_1'].'</option>';
		}
		echo  $str;
	}

    //得到用户信息
    public function getUserInfo(){
        $id=$_POST['id'];
        $User =D("User");
        $user =$User->getById($id);
        if($user){
            echo "<p>email:".$user['email']."</p><p>手机号码:".$user['mobile']."</p>";
        }

    }

	public function del_photo(){
		$data['id'] = $_POST['id'];
		$res = M('Photo')->where($data)->delete();
		echo M('Photo')->getlastsql();
	}


	public function checkEmail(){
		$data['email'] =$_POST['email'];
		$result = M('User')->where($data)->find();
		if(empty($result)){
			echo '1';
		}else{
			echo '0';
		}
	}

	public function checkNickname(){
		$data['nickname'] = $_POST['nickname'];
		$result = M('User')->where($data)->find();
		if(empty($result)){
			echo '1';
		}else{
			echo '0';
		}
	}

    /*
	+------------------
	*  上传相册操作
	+------------------
	*/
	function upload_album(){
       $paths ='./Public/upload/logo/thum/';
       $paths1 ='./Public/upload/logo/photo/';
       if (!is_dir($paths)) @mk_dir($paths);
        if (!is_dir($paths1)) @mk_dir($paths1);
       $uploadList = $this->_upload($_FILES);
       // dump($uploadList);
       $logo = $uploadList['0']['savename'];




		imagezoom($paths1.$logo,  $paths.$logo, 988, 98, '#f0f0f0');

		//unlink('./Public/upload/temp/blog/Album/img/'.$logo);


		echo "<script>parent.stopSends('".$logo."')</script>";
	}

    function _upload() {
        import("@.ORG.UploadFile");
		$upload = new UploadFile ();
		//设置上传文件大小
		$upload->maxSize  = '1000201020102012010201' ;
		//设置上传文件类型
		$upload->allowExts = explode ( ',', 'jpg,gif,png,jpeg,rar' );
		//设置附件上传目录
		$upload->thumb = true;
        $upload->thumbPrefix ='m_' ;
        $upload->thumbMaxHeight = '98';
        $upload->thumbMaxWidth =  '988';
		//设置上传文件规则
		$upload->saveRule = 'uniqid';
		//存在同名文件覆盖,可上传相同图片
		$upload->uploadReplace = 'true';
		//删除原图路径
		$upload->thumbRemoveOrigin = false;

		$upload->savePath = './Public/upload/logo/photo/';
		//$upload->saveRule = true;
		if (! $upload->upload ()) {
			$this->error ( $upload->getErrorMsg () );
		} else {
         //   dump($_FILES);
			$uploadList = $upload->getUploadFileInfo ();

			return $uploadList;
		}
	}
    /**
     +----------------------------------------------------------
     * @添加预约工地
     +----------------------------------------------------------
    */

    public function addReservation(){
        if(empty($_SESSION['uid'])){
            exit('nologin');
        }

            $ReservationSite =D("ReservationSite");


            $data['construction_id']=$_REQUEST['id'];
            $data['com_uid']=$_REQUEST['com_id'];
            $data['uid']=$_SESSION['uid'];
            $res1=$ReservationSite->where($data)->find();
            if($res1==false){
                $data['create_time']=time();
                $data['update_time']=time();
                $res =$ReservationSite->add($data);
                if($res!=false){
                    echo 'ok';
                }else{
                    echo '0';
                }
            }else{
                echo 'have';
            }

    }

    /**
     +----------------------------------------------------------
     * @预约设计师
     +----------------------------------------------------------
    */
    public function addDesignBook(){
        if(empty($_SESSION['uid'])){
            exit('nologin');
        }

        $DesignerBook =D("DesignerBook");

            if (!empty($_REQUEST['id']))
            {
              $data['case_id']=$_REQUEST['id'];
            }

               $data['com_uid']=$_REQUEST['com_id'];

            $data['designer_id']=$_REQUEST['designer_id'];
            $data['uid']=$_SESSION['uid'];
            $res1=$DesignerBook->where($data)->find();
            if($res1==false){
                $data['create_time']=time();
                $data['update_time']=time();
                $res =$DesignerBook->add($data);
                if($res!=false){
                    echo 'ok';
                }else{
                    echo '0';
                }
            }else{
                echo 'have';
            }


    }
    /**
     +----------------------------------------------------------
     * @添加收藏
     +----------------------------------------------------------
    */
    public function addAttention(){
        if(empty($_SESSION['uid'])){
            exit('nologin');
        }

        $Watchlist =D("Watchlist");

        $data['fid']=$_REQUEST['id'];
        $data['model']=$_REQUEST['model'];
        $data['uid']=$_SESSION['uid'];
        if(!empty($_REQUEST['com_id'])){

            $data['select_uid']=$_REQUEST['com_id'];
        }
        $res1=$Watchlist->where($data)->find();
        if($res1==false){
            $data['create_time']=time();
            $data['status']=1;

            $res =$Watchlist->add($data);

            if($res!=false){
                echo 'ok';
            }else{
                echo 'failed';
            }
        }else{
            echo 'have';
        }

    }

}
?>