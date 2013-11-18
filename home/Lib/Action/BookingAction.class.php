<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.to-serve.com
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2012-03-29
 * @Action  预约报名
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */
class BookingAction extends  PublicAction{
    function _initialize()  {
        $Booking =D("Booking");
        $book =$Booking->order('id desc')->limit("8")->select();
        $bookcout =$Booking->count('id');
        $this->assign('bookcout',$bookcout);
        $this->assign('book',$book);
         parent::_initialize();
    }
    /**
     +----------------------------------------------------------
     * @在线预约
     +----------------------------------------------------------
    */
    public function index(){
        if(empty($_GET['type'])){
             $type =4;
        }else{
             $type =$_GET['type'];
        }
        $this->assign('type',$type);
        if(empty($_SESSION['uid'])){


            $this->display('regOl');
            exit;
        }
        $User =D("User");

        $user =$User->getById($_SESSION['uid']);
        $this->assign('user',$user);
         $this->assign('title','预约报名');
        $this->display();
    }
    /**
     +----------------------------------------------------------
     * @在线报名
     +----------------------------------------------------------
    */

    public function BookInset(){
        if(empty($_SESSION['uid'])){
            $User =D("User");
            $userData['type']=1;
            $userData['status']=1;
            $userData['active']=1;
            $userData['mobile']=$_POST['mobile'];
            $userData['email']=$_POST['email'];
            $userData['sex']=1;
            $userData['user_pwd']=md5($_POST['user_pwd']);
            $userData['user_name']=$_POST['user_name'];
            $userData['create_time']=time();
            $userData['update_time']=time();
            $user =$User->add($userData);

            if($user){
                $Booking =D("Booking");
                $data['type']=$_POST['type'];
                $data['mobile']=$_POST['mobile'];
                $data['sex']=1;
                $data['user_name']=$_POST['user_name'];
                $data['email']=$_POST['email'];
                $data['create_time']=time();
                if($_POST['type']==5){
                   $data['cate_pid']=$_POST['cate_pid'];
                   $data['cate_id']=$_POST['cate_id'];
                }

                $data['status']=1;
                $data['uid']=$user;
                $booking =$Booking ->add($data);
                ///echo $Booking ->getlastsql();

                if($booking!=false){
                    $_SESSION['uid']=$user;
                    $_SESSION['type']=1;
                    $_SESSION['user_name']=$_POST['user_name'];
                    $this->assign("jumpUrl","__APP__/");
                    $this->success('报名成功!');
                }else{
                    $this->error('报名失败!');
                }
            }else{
                $this->error('报名失败!');
            }
        }else{
                $user =D("User")->getById($_SESSION['uid']);
                $Booking =D("Booking");
                $data['type']=$_POST['type'];
                if(!empty($_POST['mobile'])){
                    $data['mobile']=$_POST['mobile'];
                }else{
                    $data['mobile']=$user['mobile'];
                }
                $data['sex']=$user['sex'];

                if(!empty($_POST['user_name'])){
                    $data['user_name']=$_POST['user_name'];
                }else{
                   $data['user_name']=$user['user_name'];
                }

                $data['email']=$user['email'];
                $data['create_time']=time();
                if($_POST['type']==5){
                   $data['cate_pid']=$_POST['cate_pid'];
                   $data['cate_id']=$_POST['cate_id'];
                }

                $data['status']=1;
                $data['uid']=$_SESSION['uid'];
                $booking =$Booking ->add($data);

                if($booking!=false){
                    $this->assign("jumpUrl","__APP__/");
                    $this->success('报名成功!');
                }else{
                    $this->error('报名失败!');
                }
        }
    }
    /**
     +----------------------------------------------------------
     * @针对公司的预约报名
     +----------------------------------------------------------
    */
    public function company(){
       $id=$_GET['id'];
       if(empty($_GET['type'])){
            $type =4;
        }else{
            $type =$_GET['type'];
        }
       $this->assign('id',$id);
       $this->assign('type',$type);
       if(empty($_SESSION['uid'])){

            $this->display('regCompany');
            exit;
        }


        $User =D("User");
        $user =$User->getById($_SESSION['uid']);
        $this->assign('user',$user);
        $this->display();

    }
    /**
     +----------------------------------------------------------
     * @执行企业添加
     +----------------------------------------------------------
    */
    public function companyBookInset(){
          $cmpmap['uid']=$_POST['select_uid'];
          $company =D("Company")->where($cmpmap)->find();
         if(empty($_SESSION['uid'])){
            $User =D("User");
            $userData['type']=1;
            $userData['status']=1;
            $userData['active']=1;
            $userData['mobile']=$_POST['mobile'];
            $userData['email']=$_POST['email'];
            //$userData['sex']=$_POST['sex'];
            $userData['user_pwd']=md5($_POST['user_pwd']);
            $userData['user_name']=$_POST['user_name'];
            $userData['create_time']=time();
            $userData['update_time']=time();
            $user =$User->add($userData);
            //echo $User->getlastsql();
            if($user){

                $Booking =D("Booking");
                $data['type']=$_POST['type'];
                $data['mobile']=$_POST['mobile'];
                //$data['sex']=$_POST['sex'];
                $data['user_name']=$_POST['user_name'];
                $data['email']=$_POST['email'];
                $data['create_time']=time();
                if($_POST['type']==5){
                   $data['cate_pid']=$_POST['cate_pid'];
                   $data['cate_id']=$_POST['cate_id'];
                }

                $data['status']=1;
                $data['uid']=$user;
                $data['select_uid']=$_POST['select_uid'];
                $data['company_id']=$company['id'];
                $booking =$Booking ->add($data);
                //echo $Booking ->getlastsql();

                if($booking!=false){
                    $_SESSION['uid']=$user;
                    $_SESSION['user_name']=$_POST['user_name'];
                    $_SESSION['type']=1;
                    if ( $_SESSION['refer_urls'] != '' ) {
                        $refer_url	=	$_SESSION['refer_urls'];
                        unset($_SESSION['refer_url']);

                    }else{
                        //$this->assign("jumpUrl","__APP__/");
                         $refer_url = U('/index.php');
                    }
                    $this->assign("jumpUrl",$refer_url);
                    $this->success('报名成功!');
                }else{
                    $this->error('报名失败!');
                }
            }else{
                $this->error('报名失败!');
            }
        }else{
                $user =D("User")->getById($_SESSION['uid']);
                $Booking =D("Booking");
                $data['type']=$_POST['type'];
                $data['mobile']=$user['mobile'];
                //$data['sex']=$user['sex'];
                $data['user_name']=$user['user_name'];
                $data['email']=$user['email'];
                $data['create_time']=time();
                if($_POST['type']==5){
                   $data['cate_pid']=$_POST['cate_pid'];
                   $data['cate_id']=$_POST['cate_id'];
                }

                $data['status']=1;
                $data['uid']=$_SESSION['uid'];
                $data['select_uid']=$_POST['select_uid'];
                $data['company_id']=$company['id'];
                $booking =$Booking ->add($data);

                if($booking!=false){
                     if ( $_SESSION['refer_urls'] != '' ) {
                        $refer_url	=	$_SESSION['refer_urls'];
                        unset($_SESSION['refer_url']);

                    }else{
                        //$this->assign("jumpUrl","__APP__/");
                         $refer_url = U('/');
                    }
                    $this->assign("jumpUrl",$refer_url);
                    $this->success('报名成功!');
                }else{
                    $this->error('报名失败!');
                }
        }


    }


}
?>