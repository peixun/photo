<?php
// +----------------------------------------------------------------------
// | Fanwe 多语商城建站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | AdvPosition: awfigq(67877605@qq.com)
// +----------------------------------------------------------------------

// 广告位
class AdvPositionAction extends CommonAction{
	public function add()
	{
		$adflashdir = $this->getRealPath()."/Public/adflash/";
		
		$adflashlist = new Dir($adflashdir);
		$adflashs = array();
		foreach($adflashlist as $adflash)
		{
			if($adflash['ext'] == "swf")
				$adflashs[] = str_replace(".swf", "", $adflash['filename']);
		}
		$this->assign('adflashs',$adflashs);
		$this->display();
	}

	public function insert() {
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		
		if($_REQUEST['is_flash'])
		{
			$model->is_flash = 1;
			$model->flash_style = $_REQUEST['flash_style'];
		}
		else
		{
			$model->is_flash = 0;
			$model->flash_style = "";
		}
		
		//保存当前数据对象
		$ap=$model->add ();
		if ($ap!==false) { //保存成功
			$this->saveLog(1,$ap);
			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('ADD_SUCCESS'));
		} else {
			//失败提示
			$this->saveLog(0,$ap);
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

		$adflashdir = $this->getRealPath()."/Public/adflash/";
		
		$adflashlist = new Dir($adflashdir);
		$adflashs = array();
		foreach($adflashlist as $adflash)
		{
			if($adflash['ext'] == "swf")
				$adflashs[] = str_replace(".swf", "", $adflash['filename']);
		}
		$this->assign('adflashs',$adflashs);
		$this->display();
	}
	
	
	public function update() {
		//B('FilterString');
		$name=$this->getActionName();
		$model = D ( $name );
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		if($_REQUEST['is_flash'])
		{
			$model->is_flash = 1;
			$model->flash_style = $_REQUEST['flash_style'];
		}
		else
		{
			$model->is_flash = 0;
			$model->flash_style = "";
		}
		// 更新数据
		$ap=$model->save ();
		if (false !== $ap) {
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
				if (false !== $model->where ( $condition )->delete ()) {
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
}
?>