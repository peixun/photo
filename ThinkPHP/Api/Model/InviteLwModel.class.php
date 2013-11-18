<?php
// +----------------------------------------------------------------------
// | ThinkSnS
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://www.thinksns.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Nonant <nonant@163.com>
// +----------------------------------------------------------------------
// $Id$


//邀请操作
class InviteLwModel extends LW_Model {

	/**
	 * 获取邀请链接的验证码
	 *
	 * @param string $type
	 * @param intId $typeId
	 * @param array $data
	 * @return string
	 */
	function getInviteLink($type,$typeId,$data=''){
		$map['type'] = $type;
		$map['typeid'] = $typeId;
		$info = $this->where($map)->find();
		if(!$info){
			$map['data'] = serialize($data);
			$id = $this->add($map);
			$code['code'] = md5(time()+$id);
			$this->where('id='.$id)->save($code);
			return $this->getInviteLink($type,$typeId);
		}else{
			return $info['code'];
		}
	}
	
	/**
	 * 验证验证码是否正确
	 *
	 * @param string $type
	 * @param int $typeId
	 * @param string $code
	 * @return boolen
	 */
	function checkCode($type,$typeId,$code){
		$map['code'] = $code;
		$map['type'] = $type;
		$map['typeid'] = $typeId;
		$info = $this->where($map)->find();
		if($info){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 返回验证相关数据
	 *
	 * @param int $uid
	 * @param string $code
	 * @return array
	 */
	function getInfo($uid,$code){
		$map['code'] = $code;
		$info = $this->where($map)->find();
		$info['data'] = unserialize($info['data']);
		return $info;
	}
}
?>