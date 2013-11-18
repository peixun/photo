<?php

class UserLwModel extends LW_Model {


/**
 * getInfo
 * 获取用户信息
 * SamPeng 重构
 * @param <type> $uids
 * @param <type> $fields
 * @param <type> $format
 * @return <type>
 */
    public function getInfo($uids,$fields=null,$format="php") {
        $map = $this->__getInfoId($uids);
        if((!strpos($uids,","))&&!is_array($uids)) {
            $result = $this->where($map)->field($fields)->find();
            //echo $this->getlastsql();
        }else {
            $result = $this->where($map)->field($fields)->findAll();

        }

        switch($format) {
            case "json": return json_encode($result);
            default    : return $result;
        }
    }

    private function __getInfoId($uids) {
        if((!strpos($uids,","))&&!is_array($uids)) {
            $map["id"] = $uids;
        }else {
            if(is_array($uids)) {
                $map["id"] = array("IN",$uids);
            }else {
                $map = "id IN ($uids)" ;
            }
        }
        return $map;
    }

	public function getLoggedInUser(){
		$userInfo = unserialize($_SESSION["userInfo"]);
		return $userInfo["id"]?intval($userInfo["id"]):0;
	}

	public function getLoggedInName(){
		$result = unserialize($_SESSION["userInfo"]);
		return $result["name"];
	}

    public function getUserReg($uid){
    	$regTime = $this->where('id='.$uid)->field('cTime')->find();
        //echo  $this->getlastsql();
    	return $regTime['cTime'];
    }


	public function getLoggedInUserLevel() {
		$r =  $this->where('id='.$this->getLoggedInUser())->field("admin_level")->find();
		$result = $r["admin_level"];
		return $result;
	}



	public function getLinkName($uid) {
		$result = "<a href='space.php?$uid'>".$this->UserName($uid).'</a>';
		return $result;
	}

	public function getUserName($uid) {
		$info = $this->find($uid);
		return $info["name"];
	}

    /*
     * 检测是否记住登陆了
     *
     */
    public function isRemembor(){
         $remembor = $_COOKIE["remembor"];
         if($remembor && !$_SESSION["userInfo"]){
            $user_r  = unserialize(stripcslashes($remembor));

            //安全检测
            if($user_r["agent"] == $_SERVER["HTTP_USER_AGENT"]){
	            $map["email"]  = $user_r["email"];
	            $map["passwd"] = $user_r["passwd"];
	            $user = $this->where($map)->field("id,name")->find();
	            if($user){
				    //IP访问控制
					$site_opts = EY_D("Option")->get();
					ip_banned($site_opts["deny_ips"],$site_opts["allow_ips"]);

				     //修改最后一次登录IP
					 EY_D("LoginRecord")->record($user["id"]);

	                 unset($user["active"]);
	                 $_SESSION["userInfo"] = serialize($user);
	            }
            }
        }
    }

    //退出登陆
    public function userLogout(){

    }

    //推荐用户列表
    public function getCommendUser($limit){
        $opts = EY_D( 'Option' )->get();
        $map['commend'] = 1;
         $map['active']  = 1;
        //查找所有被推荐的用户id
        $uid_list = array();
        $command_user = $this->where( $map )->field( 'id' )->limit( '0,'.$limit )->order( 'rand()' )->findAll();
        foreach (  $command_user as $value ){
            $result[] = $value['id'];
        }
        return $result;
    }
}

?>
