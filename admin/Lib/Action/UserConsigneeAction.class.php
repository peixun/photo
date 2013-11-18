<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */class UserConsigneeAction extends CommonAction{
	public function index()
	{
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		$map['user_id'] = intval($_REQUEST['user_id']);
		$this->assign("user_param",array('id'=>$map['user_id']));
		$this->assign("user_id",$map['user_id']);
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}

		$list = $this->get("list");
		foreach($list as $k=>$v)
		{
			$list[$k]['region_lv1'] = D("RegionConf")->getById($v['region_lv1']);
			$list[$k]['region_lv2'] = D("RegionConf")->getById($v['region_lv2']);
			$list[$k]['region_lv3'] = D("RegionConf")->getById($v['region_lv3']);
			$list[$k]['region_lv4'] = D("RegionConf")->getById($v['region_lv4']);
		}
		$this->assign("list",$list);
		$this->display ();
		return;
	}
	
	public function add()
	{
		$user_id = intval($_REQUEST['user_id']);
		$this->assign("user_param",array('user_id'=>$user_id));
		$this->assign("user_id",$user_id);
		//输出一级地区
		$region_lv1_list = D("RegionConf")->where("pid=0")->findAll();
		$this->assign("region_lv1_list",$region_lv1_list);
		$this->display();
	}
	
	function edit() {
		$name=$this->getActionName();
		$model = M ( $name );
		$id = $_REQUEST [$model->getPk ()];
		$vo = $model->getById ( $id );
		$this->assign ( 'vo', $vo );
		
		$user_id = $vo['user_id'];
		$this->assign("user_param",array('user_id'=>$user_id));
		$this->assign("user_id",$user_id);
		//输出一级地区
		$region_lv1_list = D("RegionConf")->where("pid=0")->findAll();
		$this->assign("region_lv1_list",$region_lv1_list);
		
		//输出二级地区
		$region_lv2_list = D("RegionConf")->where("pid=".D("RegionConf")->where("id=".$vo['region_lv2'])->getField("pid"))->findAll();
		$this->assign("region_lv2_list",$region_lv2_list);
		
		//输出三级地区
		$region_lv3_list = D("RegionConf")->where("pid=".D("RegionConf")->where("id=".$vo['region_lv3'])->getField("pid"))->findAll();
		$this->assign("region_lv3_list",$region_lv3_list);
		
		//输出四级地区
		$region_lv4_list = D("RegionConf")->where("pid=".D("RegionConf")->where("id=".$vo['region_lv4'])->getField("pid"))->findAll();
		$this->assign("region_lv4_list",$region_lv4_list);
		$this->display ();
	}
}
?>