<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 广告
class AdvAction extends CommonAction{
	public function getAdvInfo()
	{
		$adv_info = D("Adv")->getById(intval($_REQUEST['id']));
		echo json_encode($adv_info);
	}
	
	public function add()
	{
		$positions = D("AdvPosition")-> findAll();
		$this->assign('positions',$positions);
		$this->display();
	}

	public function insert() {
		//B('FilterString');
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		$upload_list = $this->uploadFile(0,"adv");
		if($upload_list)
			$model->code = $upload_list[0]['recpath'].$upload_list[0]['savename'];
		elseif($_REQUEST['code'])
			$model->code = $_REQUEST['code'];
		
		if($_REQUEST['type']==1)
			$model->url = $_REQUEST['url'];
		else 
			$model->url = "";
		$model->type = $_REQUEST['type'];
		
		if($_REQUEST['target_type'] == 0)
			$model->target_id = 0;
		
		//保存当前数据对象
		$list=$model->add ();
		if ($list!==false) { //保存成功
			$this->saveLog(1,$list);
			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('ADD_SUCCESS'));
		} else {
			//失败提示
			$this->saveLog(0,$list);
			$this->error (L('ADD_FAILED'));
		}
	}
	
	public function edit()
	{		
		$name=$this->getActionName();
		$model = M ( $name );
		$id = $_REQUEST [$model->getPk ()];
		$vo = $model->getById ( $id );
		$this->assign ( 'vo', $vo );	

		$positions = D("AdvPosition")-> findAll();
		$this->assign('positions',$positions);
		$this->display();
	}
	
	public function update() {
		//B('FilterString');
		$name=$this->getActionName();
		$model = D ( $name );
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		$upload_list = $this->uploadFile(0,"adv");
		if($upload_list)
		{			
			$map[$model->getPk()] = $_REQUEST[$model->getPk()];
			$item = $model->where($map)->find();
			@unlink($this->getRealPath().$item['code']);
			$model->code = $upload_list[0]['recpath'].$upload_list[0]['savename'];
		}
		elseif($_REQUEST['code'])
		{
			$map[$model->getPk()] = $_REQUEST[$model->getPk()];
			$item = $model->where($map)->find();
			@unlink($this->getRealPath().$item['code']);
			$model->code = $_REQUEST['code'];
		}
		
		if($_REQUEST['type']==1)
		{
			$model->url = $_REQUEST['url'];
		}
		else 
		{
			$model->url = "";
		}
		
		$model->type = $_REQUEST['type'];
		
		if($_REQUEST['target_type'] == 0)
			$model->target_id = 0;
			
		// 更新数据
		$list=$model->save ();
		if (false !== $list) {
			//成功提示
			$this->saveLog(1);
			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
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
				$items = $model->where($condition)->findAll();				
				
				
				if (false !== $model->where ( $condition )->delete ()) {
					foreach($items as $item)
					{
						@unlink($this->getRealPath().$item['code']);
					}
					//echo $model->getlastsql();
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
	
	public function getAdvPositionName($positionID)
	{
		if($positionID > 0)
			return D("AdvPosition")->where("id = $positionID")->getField('name');
	}
}
?>