<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 前台菜单列表
class NavAction extends CommonAction{
	//定义站内的操作结构（可用于菜单配置）
	private $nav_conf = array(
		'Article'=>array(
			'show',		//展示
		),
		'User'=>array(
			'register', //注册
			'login',    //登录
		),
		'Message'	=>array(
			'index',
			'comment', //讨论
		),
		'BelowLine' => array(
			'index',  //地面团购
		),
		'Advance' => array(
			'index',  //团购预告
		),
		'Goods' => array(
			'other',  //今日其他团购
			'index',  //往期团购
			'show',	  //团购详情
			'showcate', //团购分类详情
		),
		'Supplier' => array(
			'index',  //商家分类
			'show',		//内容
		),
		'Index' => array(
			'index',  //首页
		),
	);
	//增
	public function add()
	{
		$this->assign("module_list",$this->nav_conf);  //输出模块列表
		$new_sort = D(MODULE_NAME)-> max("sort") + 1;
		$this->assign('new_sort',$new_sort);
		$this->display();
	}

	
	//改
	public function edit()
	{
		$this->assign("module_list",$this->nav_conf);  //输出模块列表
		
		$name=$this->getActionName();
		$model = M ( $name );
		$id = $_REQUEST [$model->getPk ()];
		$vo = $model->getById ( $id );
		
		$action_list = $this->nav_conf;
		$action_list = $action_list[$vo['rec_module']];
		$res = array();
		foreach($action_list as $k=>$v)
		{
			$res[$k] = array('value'=>$v,'name'=>L("LANG_SHOW_MODULE_".$vo['rec_module']."_ACTION_".$v));
		}
		$this->assign("action_list",$res);
		
		$this->assign ( 'vo', $vo );
		$this->display ();
	}
	
	public function getActionList()
	{
		$rec_module = $_REQUEST['rec_module'];
		$action_list = $this->nav_conf;
		$action_list = $action_list[$rec_module];
		$res = array();
		foreach($action_list as $k=>$v)
		{
			$res[$k] = array('value'=>$v,'name'=>L("LANG_SHOW_MODULE_".$rec_module."_ACTION_".$v));
		}
		echo json_encode($res);
	}
	public function update()
	{
			//B('FilterString');
		$name=$this->getActionName();
		$model = D ( $name );
		if (false ===$data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$data['show_cate']  = intval($_REQUEST['show_cate']);
		$list=$model->save ($data);
		if (false !== $list) {
			//成功提示
			$this->saveLog(1);
//			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('EDIT_SUCCESS'));
		} else {
			//错误提示
			$this->saveLog(0);
			$this->error (L('EDIT_FAILED'));
		}		
	}
}
?>