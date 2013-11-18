<?php

class FriendLwModel extends LW_Model {

	public function get($uid=false,$format="php"){

		$map["uid"]		 =  $uid?$uid:TS_D("User")->getLoggedInUser();
		$map["status"]	 =  1;
 
		$fris	=	$this->where($map)->field("fuid")->order('dateline DESC')->findAll();

		foreach($fris as $v){
			$fri_ids[]	=	$v["fuid"];
		}

		//if(!$fri_ids) $fri_ids[] = TS_D("User")->getLoggedInUser(); //测试用

		$result = $fri_ids;

		switch($format) {
			case "json": return json_encode($result);
			default    : return $result;
		}
	}


	//根据gid获取uids
	public function getGroupUids($gid,$format="php") {

		$map_g["uid"]	 =  TS_D("User")->getLoggedInUser();
		if($gid) $map_g["gid"]	 =	$gid;
		
		$fuids = array();
		$fuids = TS_D("Fg")->where($map_g)->field("DISTINCT fuid")->findAll();

		foreach($fuids as $k=>$v){
			$result[]		= $v["fuid"];
		}

		switch($format) {
			case "json": return json_encode($result);
			default    : return $result;
		}
	}

	//默认显示10条
	public function getIdName($uid=false,$gid=false,$pageLimit=10,$format="php") {


		//分页相关1
		$curPage = $_GET["p"]?intval($_GET["p"]):1;
		$firstRow = ($curPage-1)*$pageLimit;
		$limit    =	$firstRow.','.$pageLimit;
		//分页相关1 end


		if($gid){
			$map_g["uid"]	 =  $uid?$uid:TS_D("User")->getLoggedInUser();
			$map_g["gid"]	 =	$gid;

			$fris_num_arr	=	TS_D("Fg")->where($map_g)->field("count(DISTINCT fuid) as count")->find();

			$fris_num['count'] = $fris_num_arr["count"];
			if(!$fris_num) return;
			
			$fuids = TS_D("Fg")->where($map_g)->field("DISTINCT fuid")->limit($limit)->findAll();


			foreach($fuids as $k=>$v){
				$fris[$k]["fuid"]		= $v["fuid"];
				$fris[$k]["fusername"]	= TS_D("User")->getUserName($v["fuid"]);	
			}
			
		}else{
			$map["uid"]		 =  $uid?$uid:TS_D("User")->getLoggedInUser();
			$map["status"]	 =  1;		

			$fris		=	$this->where($map)->field("fuid,fusername")->limit($limit)->findAll();
			//echo $this->getLastSql();
			$fris_num	=	$this->where($map)->field("count(*) as count")->find();	

		}

		//分页相关2
		if($fris){
			$result["total_page"] = ceil($fris_num["count"]/$pageLimit);   //总页数
                        $result["data"] = $fris;
		}
		//分页相关2 end

		switch($format) {
			case "json": return json_encode($result);
			default    : return $result;
		}
	}

	public function getFriNum($uid=false,$gid=false){

		if($gid){
			$map["gid"] = $gid;
			$map["uid"] = $uid;
			
			$r = TS_D("Fg")->where($map)->field("count(*) as num")->find();	

			return $r["num"];
		}else{
			$map["uid"] = $uid;
			$r = $this->where($map)->field("count(*) as num")->find();	

			return $r["num"];
		} 


		
//		$map["uid"]		 =  $uid?$uid:TS_D("User")->getLoggedInUser();
//		$map["status"]	 =  1;
//		if($gid) $map["gid"] = $gid;
//
//		$fris_num	=	$this->where($map)->field("count(*) as count")->find();	
//		return $fris_num["count"];
	}

	public function areFriends($uid1,$uid2){

		$map["uid"]    =  $uid1;
		$map["fuid"]   =  $uid2;
		$map["status"] =  1;

		$r	= $this->where($map)->field("count(*)")->find();
		$result = $r["count(*)"] == "1"?true:false;

		return $result;
		
	}

	public function getAppUsers($format="php") {


		switch($format) {
			case "json": return json_encode($result);
			default    : return $result;
		}
	}


}

?>