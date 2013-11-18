<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 留言评论
class GroupMessageAction extends CommonAction{
	public function index()
	{
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		$map['pid'] = intval($_REQUEST['pid']);
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display();
		return;
	}
	
	public function edit()
	{
		$name=$this->getActionName();
		$model = M ( $name );
		$id = $_REQUEST [$model->getPk ()];
		$vo = $model->getById ( $id );
		$this->assign ( 'vo', $vo );
		
		if($vo['pid']!=0)
		{
			$this->assign("param_back",array("pid"=>$vo['pid']));
		}
		$city_list = M("GroupCity")->findAll();
		$this->assign("city_list",$city_list);
		$this->display ();		
	}
	
	public function foreverdelete() {
		//删除指定记录
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			$ids = explode ( ',', $id );
			$names = '';
			foreach($ids as $idd)
			{
				$names .= M("GroupMessage")->where("id=".$idd)->getField("tg_title").",";
			}
			if($names!='')
			{
				$names = substr($names,0,strlen($names)-1);
			}
			
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				$p_condition = array ("pid" => array ('in', explode ( ',', $id ) ) );
				if (false !== $model->where ( $condition )->delete ()) {
					//echo $model->getlastsql();
					$model->where ( $p_condition )->delete ();
					$msg = '彻底删除发起团购主题:'.$names;						
					$this->saveLog(1,0,$msg);
					$this->success (L('DEL_SUCCESS'));
				} else {
					$msg = '彻底删除发起团购主题:'.$names;						
					$this->saveLog(0,0,$msg);
					$this->error (L('DEL_FAILED'));
				}
			} else {
				$this->saveLog(0);
				$this->error ( L('INVALID_OP') );
			}
		}
		$this->forward ();
	}
}
?>