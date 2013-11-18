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

/**
 +------------------------------------------------------------------------------
 *  系统节点
 +------------------------------------------------------------------------------
 * @Author: Nonant <nonant@163.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class SystemNodeLwModel extends LW_Model
{
	var $table_name = 'system_node';

	//获取游客权限数组
	function getGuestPopedom(){
		$list = ts_cache('guestpopedom');
		if(!$list){
			$list = $this->getNodeList(0);
			ts_cache('guestpopedom',$list);
		}
		return $list;
	}
	
	function getNodeList($pid='0',$type='guest') {
		return $this->_MakeTree($pid,$type);
	}	
	
	function _MakeTree($pid,$type='guest') {
		if($isall!='All'){
			$map['state'] = 1;
		}
		$map['pid'] = $pid;
		$map['type'] = $type;		
		$result = $this->where($map)->order('ordernum ASC')->findall();
		if($result){
			foreach ($result as $key => $value){
				$name = strtoupper($value['name']);
				if($value['containaction']=='All'){
					$list[$name] = 'All';
				}elseif ($value['level']==3){
					$aslist = unserialize($value['containaction']);
					foreach ($aslist as $value){
						$list[strtoupper($value['name'])] = 'TRUE';
					}
				}else{
					$list[$name] = $this->_MakeTree($value['id'],$type);
				}
			}
		}
		return $list;
	}
}