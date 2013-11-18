<?php
class ReportLwModel extends LW_Model
{
	
	//检测某用户是否已举报了此评论
	function check($appid='0',$type='',$recordId='0',$uid='0'){
		$map['uid']       = $uid;
		$map['appid']     = $appid;
		$map['type']      = $type;
		$map['recordId']  = $recordId;
		$info = $this->where($map)->find();
		if($info['id']){
			return true;
		}else{
			return false;
		}
	}
	
	//获取举报的内容
	function get($type='',$appid='null',$result='recordId'){
		if($type && $appid){
			$map['type'] = $type;
			$map['appid'] = $appid;
			$list = $this->where($map)->findall();
			if($result=='id'){
				foreach ($list as $key =>$val){
					$return[] = $val['recordId'];
				}
			}else{
				$return = $list;
			}
			return $return; 
		}
	}
}
?>