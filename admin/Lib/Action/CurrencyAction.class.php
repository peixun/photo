<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 货币配置
class CurrencyAction extends CommonAction{
	
	function getCurrency(){
		$id = intval($_REQUEST ['id']);
		$model = D ("Currency");
		$vo = $model->getById( $id );			
		echo  json_encode($vo);
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
				//验证
				if(D("LangConf")->where(array ('currency' => array ('in', explode ( ',', $id ) ) ))->count()>0)
				{
					$this->error (L('CURRENCY_USED'));
				}
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