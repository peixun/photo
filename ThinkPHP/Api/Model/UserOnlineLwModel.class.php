<?php

class UserOnlineLwModel extends LW_Model {
	var $table_name = "user_online";
	
	
	function isOnline($uid){
		$info = $this->where('uid='.$uid.' AND activeTime > '.(time()-15*60))->find();
		if($info['uid']){
			return true;
		}else{
			return false;
		}
	}
	
	function recordOnline($uid,$name){
		$delMap['activeTime'] = array('lt',time()-600);
		$this->where($delMap)->delete();
		if($uid){
			$map["uid"] = $uid;
			$num = $this->where($map)->find();
			if($num['uid']){
				$data["activeTime"] = time();
				$this->where($map)->save($data);
			}else{
				$data["uid"] = $uid;
				$data["uname"] = $name;
				$data["activeTime"] = time();
				$this->add($data);
			}
		}
	}
}

?>
