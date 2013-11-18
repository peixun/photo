<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 商品品牌
class BrandAction extends CommonAction{
	//增
	public function add()
	{
		$new_sort = D(MODULE_NAME)-> max("sort") + 1;
		$this->assign('new_sort',$new_sort);
		$this->display();
	}

	public function insert() {

		//B('FilterString');
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $model->create ()) {
            $this->assign ( 'jumpUrl',U('Brand/add'));
			$this->error ( $model->getError () );
		}
		$upload_list = $this->uploadFile(0,"brand");
		if($upload_list)
		$model->logo = $upload_list[0]['recpath'].$upload_list[0]['savename'];

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


	public function update() {
		//B('FilterString');
		$name=$this->getActionName();
		$model = D ( $name );
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		$upload_list = $this->uploadFile(0,"brand");
		if($upload_list)
		{
			$map[$model->getPk()] = $_REQUEST[$model->getPk()];
			$item = $model->where($map)->find();
			@unlink($this->getRealPath().$item['logo']);
			$model->logo = $upload_list[0]['recpath'].$upload_list[0]['savename'];
		}
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

				$goods_condition = array("brand_id"=>array ('in', explode ( ',', $id ) ));
				if(D("Goods")->where($goods_condition)->count()>0)
				{
					$this->saveLog(0);
					$this->error (L('BRAND_USED'));
				}

				if (false !== $model->where ( $condition )->delete ()) {
					foreach($items as $item)
					{
						@unlink($this->getRealPath().$item['logo']);
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

}
?>