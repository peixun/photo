<?php
/**
 * 地区信息
 *
 */
class NetworkLwModel extends LW_Model {
	
	
	public function getList($pid='0') {
		$list = F('network','','',SITE_PATH.'/data/cache/');
		if(!$list){
			$list = $this->_MakeTree($pid);
			F('network',$list,'-1',SITE_PATH.'/data/cache/');
		}
		return $list;
	}	

	protected function _MakeTree($pid,$level='1') {
		$dao = D();
		$result = $dao ->query('SELECT * FROM '.C('DB_PREFIX').'network WHERE pid='.$pid.' AND status=1');
		if($result){
			foreach ($result as $key => $value){
				$id = $value['id'];
				$list[$id]['id']    = $value['id'];
				$list[$id]['pid']    = $value['pid'];
				$list[$id]['title']  = $value['title'];
				$list[$id]['level']  = $level;
				$list[$id]['child'] = $this->_MakeTree($value['id'],$level+1);
			}
		}
		return $list;
	}
}

?>
