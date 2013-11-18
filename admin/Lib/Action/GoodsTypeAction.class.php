<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 商品类型
class GoodsTypeAction extends CommonAction{
	public function foreverdelete()
	{
		//删除指定记录
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				//验证类型下是否有属性
				$attr_condition = array ("type_id" => array ('in', explode ( ',', $id ) ) );
				if(D("GoodsTypeAttr")->where($attr_condition)->count()>0)
				{
					$this->saveLog(0);
					$this->error (L('ATTR_EXIST'));
				}
				
				//验证类型下是否有商品
				$goods_condition = array ("type_id" => array ('in', explode ( ',', $id ) ) );
				if(D("Goods")->where($goods_condition)->count()>0)
				{
					$this->saveLog(0);
					$this->error (L('TYPE_USED'));
				}
				
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