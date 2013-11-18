<?php
class AppLwModel extends LW_Model
{
	function getLeftNav($format="php") {
		$map["status"] = 0;
		$left_nav = $this->where($map)->findAll();		

		switch($format) {
			case "json": return json_encode($left_nav);
			default    : return $left_nav;
		}
	}


	//获取全站可用的APP信息
      function getChoiceList(){
		$arrApps = ts_cache('applist');
		if($arrApps){
			return $arrApps;
		}else{
			$map["status"] = array('in',array(0,1));
			$list = $this->where($map)->order('order2 asc')->findAll();
			foreach ($list as $key=>$val){
				$APP_URL = str_replace('http://{APPS_URL}',SITE_URL.'/apps',$val['url']);
				$appid                            = $val['id'];
				$applist[$appid]['url']           = $APP_URL.'/'.$val['url_exp'];
				$applist[$appid]['uid_url']       = str_replace('http://{APP_URL}',$APP_URL,$val['uid_url']);
				$applist[$appid]['add_url']       = str_replace('http://{APP_URL}',$APP_URL,$val['add_url']);
				$applist[$appid]['icon']          = str_replace('http://{APP_URL}',$APP_URL,$val['icon']);
				$applist[$appid]['id']            = $val['id'];
				$applist[$appid]['name']          = $val['name'];
				$applist[$appid]['enname']        = $val['enname'];
				$applist[$appid]['place']         = $val['place'];
				$applist[$appid]['canvas_url']    = $val['type'];
				$applist[$appid]['add_name']      = $val['add_name'];
				$applist[$appid]['status']        = $val['status'];
			}
			
			ts_cache('applist',$applist);
			return $applist;
		}
	}
	
	//获以当前应用的ID
	function getChoiceId($name){
		$map["enname"]   = $name;
//		$map["status"] = array('in',array(0,1));
//		$info = $this->where($map)->field('id')->find();
//		return $info['id'];
		return $this->array_multi_search($name,$this->getChoiceList());
	}
	
	//获取可选应用的信息
	function getChoice(){
		$list = $this->getChoiceList();
		foreach ($list  as $key => $v){
			if($v['status']=='1'){
				$result['optional'][] = $v['id'];
			}else{
				$result['default'][] = $v['id'];
			}
		}
		return $result;		
	}
	
	//获取单个APP应用的信息
	function getappinfo($appId,$field=''){
		$getInfo = $this->getChoiceList();
		$info = $getInfo[$appId];
		if(!$info){
			$result = array(
				'APP_NAME'     => 'thinksns',
				'APP_CNNAME'     => '核心应用',
				'APP_ICON'     => SITE_URL.'/public/images/system.gif',
				'APP_URL'      => SITE_URL.'/index.php?s=',
				'APP_ID'       => '0',
			);
		}else{
			$result['APP_URL'] = $info['url'];
			$result['APP_ICON'] = $info['icon'];
			$result['APP_CNNAME'] = $info['name'];
			$result['APP_ENNAME'] =   $info['enname'];
			$result['APP_ID']   =   $info['id'];
		}
		if($field){
			return $result[$field];
		}else{
			return $result;
		}
	}
	/**
	 * 获取用户的APP应用列表
	 *
	 * @param string place空间 
	 * @param Array $userapps
	 * @return Array
	 */
	function getUserAppList($type='',$userapps=''){
		$list = $this->getChoiceList();
		if($type=='place'){
			foreach ($list as $key => $v){
				if(in_array($v['id'],$userapps) || $v['status']!='1'){
					if($v['place']=='2'){
						$result['APPMENUS_LEFT'][] = $v; 
						$result['APPMENUS_TOP'][]  = $v; 
					}elseif ($v['place']=='1'){
						$result['APPMENUS_TOP'][] = $v; 
					}else{
						$result['APPMENUS_LEFT'][] = $v; 
					}
				}
			}
			return $result;	
		}else{
			return $list;
		}
	}
	
	//查找二维数组中值对应用的键值
	protected function array_multi_search( $needle, $haystack )
	{
    	foreach ($haystack as $key=>$val) {
    		if(array_search($needle,$val)){
    			return $key;
    		}
    	}
	}
}
?>