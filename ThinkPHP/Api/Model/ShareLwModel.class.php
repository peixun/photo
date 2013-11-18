<?php

class ShareLwModel extends LW_Model {
	/**
    * doaddShare
    * 站内增加分享API
    *
    * @param $type,$aimId,$toUid,$info,$purview,$data
    * @access public
    * @return  0 失败 1 成功 -1 已经分享 -2 分类出错 -3 内容为空  -4 不能分享自己的东西 -10 描述超过100字
    */
	function addShare($type,$aimId,$data,$info,$purview=0,$fri_ids=null,$url=null) {		
		$info = h($info);
		$check = $this->_check($type,$info,$data);
        if($check!=1){
        	return $check;
        }        
		if(empty($info)){
			$info = '&nbsp;';
		}
				
		$userLwDao	 =	TS_D("User");
		$mid		 =	$userLwDao->getLoggedInUser();
		$username	 =	$userLwDao->getLoggedInName();	
        
		$typeId = $type['typeId'];
		if(!empty($aimId)){
			$map['aimId'] = $aimId;
			$test = $this->isForbid($mid,$typeId,$aimId);
			if($test!=1)  return $test;
		}elseif(isset($data['url'])){
			$data['url'] = h($data['url']);
			$map['url'] = $data['url'];
		}
		
        $data = $this->_dealData($data);
        		
		$map['title'] = $data['title'];		
		$map['typeId'] = $typeId;
		$map['toUid'] = $mid;
		$map['toUserName'] = $username;
		//fromUid 方便以后扩充而设置
		$map['fromUid'] = $mid;
		$map['fromUserName'] = $username;
		
		$map['info'] = $info;		
		$map['cTime'] = time();
		$map['purview'] = $purview;
		$map['data'] = serialize($data);
	
        $result = $this->add($map);
		if($result){
			//发送通知和动态信息
             $this->_sendMessage($type,$aimId,$mid,$username,$data,$info,$fri_ids,$url,$result);
			//更新分享统计
			$this->_updateCount($mid);
			//增加积分
			$this->_addScoure($mid,$data,$type);
			return $result;
		}else{
			return 0;
		}	
		
	}
	
	function getShareNum($uid=null){
		$where = 'isDel = 0';
		if(empty($uid)){
			$list = $this->where($where)->field('count(*) as num')->find();
		}elseif (is_array($uid)){
			$where .= " AND toUid IN (" . join(",", $uid) . ")";
			$list = $this->where($where)->field('count(*) as num')->find();
		}else{
			$where .= " AND toUid = '$uid'";
			$list = $this->where($where)->field('count(*) as num')->find();
		}

		return $list["num"];
	}
	
	function isForbid($mid,$typeId,$aimId){
		$where = "toUid = '$mid' AND typeId = '$typeId' AND aimId = '$aimId' AND isDel=0";
		$test = $this->where($where)->field('id')->find();
		if($test){
			return -1;
		}else {
			return 1;
		}
	}
	
	function _updateCount($mid){
		//更新分享统计
		$spaceDao	 =	TS_D("Space");
		$spaceDao->changeCount( 'share',$this->getShareNum($mid) );
	}
	
	function _sendMessage($type,$aimId,$mid,$username,$data,$info,$fri_ids,$url,$result){
		if(isset($data['content']))  unset($data['content']);
		$body_data = $data;
		if(!empty($aimId))  $body_data['aimId'] = $aimId;
		$body_data['typeId'] = $type['typeId'];
		$body_data['toUid'] = $mid;
		$body_data['toUserName'] = $username;
		$body_data['info'] = $info;

		$body_data['id'] = $result;
		$title_data['id'] =  $result;
		if($type['typeId']==10){
			$body_data['username'] = getUserName($data['uid']);
			$userface = getUserFace($data['uid']);
			$body_data['userface'] = str_replace(SITE_URL,'{WR}',$userface);
		}
		
		$appid = TS_D('App')->getChoiceId('share');
		TS_D('Feed')->publish("share_".$type['alias'],$title_data,$body_data,$appid);

		$notifyDao = TS_D('Notify');
		$notifyDao->setAppId($appid);
		
		$title_data['type'] = $type['typeName'];
		
		if(empty($url)){
			$url = $this->_getURL($type['typeId'],$aimId,$data);
		}
		if(empty($url)){
			$url = SITE_URL.'/apps/share/index.php?s=/Index/content/id/'.$result;
		}
		if(!empty($fri_ids)){			
			$notifyDao->send($fri_ids,"share_notice",$title_data,$body_data,$url);
		}
		
		$uid = $this->_getUid($data,$type);
		if(!empty($uid)&&$uid!=$mid){
			$notifyDao->send($uid,"share_notice2",$title_data,$body_data,$url);
		}
		if ($type['typeId']==10){
			$uid = $aimId;
			$notifyDao->send($uid,"share_notice3",$title_data,$body_data,$url);			
		}
	}
	
	function _check($type,$info,$data){
        if(empty($data)){        	
        	return -3;
        }
        if(!empty($data['url'])){
        	$url = h($data['url']);
        	if(empty($url)||$url=='http://'){
        		return 0;
        	}
        }
        if(StrLenW($info)>100){
        	return -10;
        }		
		if(empty($type['typeId'])){
			return -2;
		}
		return 1;	
	}
	
	function _dealData($data){
		foreach ($data as $k=>$v){
			$data[$k] = stripcslashes($v);
			$data[$k] = str_replace(SITE_URL,'{WR}',$v);
		}
		return $data;
	}
	
	function _getURL($typeId,$aimId,$data){
		switch ($typeId){
			case 5:
				$url = '{WR}/apps/blog/index.php?s=/Index/show/id/'.$aimId.'/mid/'.$data['uid'];
				break;
			case 6:
				$url = '{WR}/apps/photo/index.php?s=/Index/album/id/'.$aimId.'/uid/'.$data['userId'];
				break;
			case 7:
				$url = '{WR}/apps/photo/index.php?s=/Index/photo/id/'.$aimId.'/aid/'.$data['albumId'].'/uid/'.$data['userId'].'/type/mAll';
				break;
			case 8:
				$url = '{WR}/apps/group/index.php?s=/Group/index/gid/'.$aimId;
				break;
			case 9:
				$url = '{WR}/apps/group/index.php?s=/Topic/topic/gid/'.$data['gid'].'/tid/'.$data['id'];
				break;
			case 10:
				$url = '{WR}/apps/share/index.php?s=/Index/content/id/'.$aimId;
				break;
			case 12:
				$url = $data['url'];
				break;
			case 13:
				$url = '{WR}/apps/vote/index.php?s=/Index/pollDetail/id/'.$aimId;
				break;
			default: 
			    $url='';
			    break; 				
		}
		
		return $url;		
	}
	
	function _addScoure($mid,$data,$type){
		setScore($mid,'add_share');//发起分享
		
        $uid = $this->_getUid($data,$type);
		if(!empty($uid)){
			setScore($uid,'shared');//被分享
		}		
	}
	
	function _getUid($data,$type){
		if(!empty($data['uid'])&&$type['typeId']!=10){
			$uid = $data['uid'];
		}elseif (!empty($data['userId'])){
			$uid = $data['userId'];
		}
		return $uid;		
	}
	
}
?>