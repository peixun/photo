<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 规格类型
class SpecTypeAction extends CommonAction{
	public function insert()
	{

		$name=$this->getActionName();
		$model = D ($name);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$list=$model->add ();
		if ($list!==false) { //保存成功
			$lang_envs = D("LangConf")->findAll();
			$img_upload = $this->uploadFile(0,'spec');  //上传成功的图片集
			$img_list = $_FILES['spec_img'];  //上传的图片集
			$img_row = 0;
			foreach($img_list['name'] as $k=>$v)
			{
				$spec_item = array();
				$spec_item['spec_type_id'] = $list;
				foreach ($lang_envs as $lang_item)
				{
					$spec_item['spec_name_'.$lang_item['id']] = $_POST['spec_name_'.$lang_item['id']][$k];
				}
				if($v!='')
				{

					$spec_item['img'] = $img_upload[$img_row]['recpath'].$img_upload[$img_row]['savename'];
					$img_row++;
				}
		
				D("Spec")->add($spec_item);
			}

			
			$this->saveLog(1,$list);
//			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
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
		//输出列表
		$spec_list = D("Spec")->where("spec_type_id=".$vo['id'])->findAll();
		$this->assign("spec_list",$spec_list);
		$this->display ();
	}
	
	public function delSpecItem()
	{
		$spec_item_id = intval($_REQUEST['id']);
		$spec_item = D("Spec")->getById($spec_item_id);
		

		if(D("GoodsSpec")->where("spec_id=".$spec_item_id." and session_id = ''")->count()>0)
		{
			echo 0;
			exit;
		}

		D("GoodsSpec")->where("spec_id=".$spec_item_id)->delete();		
		$rs = D("Spec")->where("id=".$spec_item_id)->delete();		
		@unlink($this->getRealPath().$spec_item['img']);
		echo 1;
	}
	
	public function update()
	{
			//B('FilterString');
		$name=$this->getActionName();
		$model = D ( $name );
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save ();
		if (false !== $list) {
			$lang_envs = D("LangConf")->findAll();
			$img_upload = $this->uploadFile(0,'spec');  //上传成功的图片集
			$img_list = $_FILES['spec_img'];  //上传的图片集
			$img_row = 0;
			foreach($img_list['name'] as $k=>$v)
			{
				$spec_item = array();
				$spec_item['spec_type_id'] = intval($_REQUEST['id']);
				$spec_item['id'] = $_POST['spec_item_id'][$k];
				foreach ($lang_envs as $lang_item)
				{
					$spec_item['spec_name_'.$lang_item['id']] = $_POST['spec_name_'.$lang_item['id']][$k];
				}
				if($v!='')
				{
					if($spec_item['id']!=0)
					{
						$origin_spec_item = D("Spec")->getById($spec_item['id']);
						@unlink($this->getRealPath().$origin_spec_item['img']);
					}
					$spec_item['img'] = $img_upload[$img_row]['recpath'].$img_upload[$img_row]['savename'];
					//同步更新所有关联的商品默认规格图
					D("GoodsSpec")->where("spec_id=".$spec_item['id']." and define_img=0")->setField("img",$spec_item['img']);
					$img_row++;
				}
				else 
				{
					if($spec_item['id']!=0)
					{
						$origin_spec_item = D("Spec")->getById($spec_item['id']);
						$spec_item['img'] = $origin_spec_item['img'];
					}
				}
				if($spec_item['id']!=0)
					{
						D("Spec")->save($spec_item);
					}
					else 
					{
						D("Spec")->add($spec_item);
					}
			}
			
			
			
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
	
	public function foreverdelete()
	{
		//删除指定记录
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if(D("GoodsSpec")->where(array ('spec_type_id' => array ('in', explode ( ',', $id ) ),'session_id'=>array('eq','') ))->count()>0)
				{
					$this->error (L('GOODS_SPEC_USED'));
				}
				if (false !== $model->where ( $condition )->delete ()) {
					//echo $model->getlastsql();
					
					$spec_items = D("Spec")->where(array ('spec_type_id' => array ('in', explode ( ',', $id ) ) ))->findAll();
					foreach($spec_items as $k=>$v)
					{
						@unlink($this->getRealPath().$v['img']);
					}
					D("Spec")->where(array ('spec_type_id' => array ('in', explode ( ',', $id ) ) ))->delete();
					D("GoodsSpec")->where(array ('spec_type_id' => array ('in', explode ( ',', $id ) )))->delete();
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
	
	public function addRow()
	{
		$this->display("SpecType:specItem");
	}
}
?>