<?php

class LoginRecordLwModel extends LW_Model
{
	var $table_name = "login_record";
	
	function record($mid) {

		$ip = get_ip();

		$num_r = $this->field("count(*) as count")->where("uid=$mid")->find();
		$num = intval($num_r["count"]);		

		if($num<2){
			$data["uid"] = $mid;
			$data["login_ip"] = $ip;
			$data["login_time"] = time();
			$this->add($data);
		}else{
			$r = $this->where("uid=$mid")->field("id")->order("login_time asc")->find();
			$map["id"]          = $r["id"];
			$data["login_ip"]   = $ip;
			$data["login_time"] = time();
			$this->where($map)->save($data);
		}

	}

    /**
     * getUser 
     * 获取指定的用户列表
     * @param mixed $uid 
     * @access public
     * @return void
     */
    public function getUser( $uid ,$filed="uid"){
        $map['uid'] = array( 'in',$uid );
        $result = $this->where( $map )->order( 'login_time DESC' )->field( "distinct(".$filed.")" )->findAll();
        return $result;
    }

       /**
     * getUser
     * 获取指定的用户列表
     * @param mixed $uid
     * @access public
     * @return void
     */
    public function getUserLogin( $uid ){
        $map['uid'] = $uid;
        $result = $this->where( $map )->field('login_time')->order('login_time desc')->find();
        return $result;
    }

}
?>
