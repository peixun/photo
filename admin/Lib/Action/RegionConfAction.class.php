<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 地区配置
class RegionConfAction extends CommonAction{
	public function index()
	{
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		$map['pid'] = intval($_REQUEST['pid']);
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$this->assign('pid',$map['pid']);  //输出当前列表的父地区ID
		//开始输出当前地区列表的等级
		$parent_region = D("RegionConf")->getById($map['pid']);
		if($parent_region)
		{
			$this->assign("region_level",$parent_region['region_level']+1);
			
		}
		else 
		{
			$this->assign("region_level",1);
		}

		//输出上级地区的列表参数
		$this->assign("back_level_param",array('pid'=>intval($parent_region['pid'])));
			
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
		return;
	}
	
	//增
	public function add() {
		$pid = intval($_REQUEST['pid']);
		$parent_region = D("RegionConf")->getById($pid);
		$this->assign("region_level",intval($parent_region['region_level'])+1);
		$this->assign('pid',$pid);
		$this->assign('back_param',array('pid'=>$pid));
		$this->display ();
	}

	function insert() {
		//B('FilterString');
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$list=$model->add ();
		if ($list!==false) { //保存成功
			$this->updateRegionJS();
			$this->saveLog(1,$list);
			$this->success (L('ADD_SUCCESS'));
		} else {
			//失败提示
			$this->saveLog(0,$list);
			$this->error (L('ADD_FAILED'));
		}
	}
	
	
	function edit() {
		$name=$this->getActionName();
		$model = M ( $name );
		$id = $_REQUEST [$model->getPk ()];
		$vo = $model->getById ( $id );
		$this->assign ( 'vo', $vo );
		$this->assign('back_param',array('pid'=>$vo['pid']));
		$this->display ();
	}

	
	function update() {
		//B('FilterString');
		$name=$this->getActionName();
		$model = D ( $name );
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save ();
		if (false !== $list) {
			//成功提示
			$this->updateRegionJS();
			$this->saveLog(1);
			$this->success (L('EDIT_SUCCESS'));
		} else {
			//错误提示
			$this->saveLog(0);
			$this->error (L('EDIT_FAILED'));
		}
	}
	
	public function foreverdelete() {
		//删除指定记录
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if (false !== $model->where ( $condition )->delete ()) {
					foreach(explode(',',$id) as $idItem)
					{
						$subIds = D("RegionConf")->getChildIds($idItem);
						D("RegionConf")->where(array($pk=>array('in',$subIds)))->delete();
					}
					//echo $model->getlastsql();
					$this->updateRegionJS();
					$this->saveLog(1);
					$this->success (L('DEL_SUCCESS'));
				} else {
					$this->saveLog(0);
					$this->error (L('DEL_FAILED'));
				}
			} else {
				$this->saveLog(0);
				$this->error ( L('INVALID_OP') );
			}
		}
		$this->forward ();
	}
	
	public function updateRegionJS()
	{
		$jsStr = "var regionConf = ".$this->getRegionChildJS();
		$path = $this->getRealPath()."/Public/regionConf.js";
		
		if(file_exists($path))
		{
			@file_put_contents($path,$jsStr);
		}
	}
	
	public function getRegionChildJS($pid = 0)
	{
		$jsStr = "";
		$childRegionList = D("RegionConf")->where("pid=".$pid)->order("id asc")->findAll();
		
		foreach($childRegionList as $childRegion)
		{
			if(empty($jsStr))
				$jsStr .= "{";
			else
				$jsStr .= ",";
				
			$childStr = $this->getRegionChildJS($childRegion['id']);
			$jsStr .= "\"r$childRegion[id]\":{\"i\":$childRegion[id],\"n\":\"$childRegion[name]\",\"c\":$childStr}";
		}
		
		if(!empty($jsStr))
			$jsStr .= "}";
		else
			$jsStr .= "\"\"";
				
		return $jsStr;
	}
	
	//获取子地区列表
	public function getChildRegion()
	{
		$pid = intval($_REQUEST['pid']);
		$childRegionList = D("RegionConf")->where("pid=".$pid)->findAll();
		if($childRegionList)
		echo json_encode($childRegionList);
		else
		echo '';
	}
	
	//列表地区树
	public function listTree()
	{
		$list = D("RegionConf")->where('region_level=1')->order("id asc")->findAll();
		$list = D("RegionConf")->toTree($list);
		$this->assign("region_tree",$list);
		$this->display();
	}
}
?>