<?php
/**
 * 系统权限配置
 *
 */
class SyPopedomLwModel extends LW_Model {
	var $table_name = "system_popedom";
	
	//根据组获取用户权限
	function getGroupPopedom($groupid){
		if($groupid){
				$arrPopedomList = $this->where('groupid='.$groupid)->field('menuid,modelid,arraction,type')->findAll();
		
				foreach ($arrPopedomList as $key=>$val){
					
					$modelname = TS_D('SyNode')->where('id='.$val['modelid'])->field('name')->find();
					$arraction = unserialize($val['arraction']);
						$map['id'] = array('in',$arraction);
						$actionlist = TS_D('SyNode')->where($map)->field('title,name,containaction')->findall();
						foreach ($actionlist as $k=>$v){
							foreach (unserialize($v['containaction']) as $kk => $vv){
								$arr[strtoupper($vv['name'])] = true;
							}
							
						}
						
						
						if($val['type']=='admin'){
							$result['ADMIN'][strtoupper($modelname['name'])] = $arr;
						}else{
							
							$result[strtoupper($modelname['name'])]['ADMIN']= $arr;
						}						
						unset($arr);
						unset($map);
				}
				

			}
	
			$r= array_change_key_case($result,CASE_UPPER);
			return $result ;

	}	
}

?>