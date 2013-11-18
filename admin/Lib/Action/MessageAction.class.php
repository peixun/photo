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
class MessageAction extends CommonAction{
	//禁用与恢复操作
	public function forbid() {
		$name=$this->getActionName();
		$model = D ($name);
		$pk = $model->getPk ();
		$id = $_REQUEST [$pk];
		$msgData = M("Message")->getById($id);
		$condition = array ($pk => array ('in', $id ) );
		$list=$model->forbid ( $condition );
		if ($list!==false) {
			$msg = '禁用留言:'.$msgData['title'];
			$this->saveLog(1,$msgData['id'],$msg);
			$this->success ( L('FORBID_SUCCESS') );
		} else {
			$msg = '禁用留言:'.$msgData['title'];
			$this->saveLog(0,$msgData['id'],$msg);
			$this->error  (  L('FORBID_FAILED') );
		}
	}
	function resume() {
		//恢复指定记录
		$name=$this->getActionName();
		$model = D ($name);
		$pk = $model->getPk ();
		$id = $_GET [$pk];
		$msgData = M("Message")->getById($id);
		$condition = array ($pk => array ('in', $id ) );
		if (false !== $model->resume ( $condition )) {
			$msg = '恢复留言:'.$msgData['title'];
			$this->saveLog(1,$msgData['id'],$msg);
			$this->success ( L('RESUME_SUCCESS') );
		} else {
			$msg = '恢复留言:'.$msgData['title'];
			$this->saveLog(0,$msgData['id'],$msg);
			$this->error ( L('RESUME_FAILED') );
		}
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
				$names .= M("Message")->where("id=".$idd)->getField("title").",";
			}
			if($names!='')
			{
				$names = substr($names,0,strlen($names)-1);
			}
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if (false !== $model->where ( $condition )->delete ()) {
					//echo $model->getlastsql();
					$model->where(array ("pid" => array ('in', explode ( ',', $id ) ) ))->delete();  //删除相关的回复数据
					$msg = '删除留言:'.$names;
					$this->saveLog(1,0,$msg);
					$this->success (L('DEL_SUCCESS'));
				} else {
					$msg = '删除留言:'.$names;
					$this->saveLog(0,0,$msg);
					$this->error (L('DEL_FAILED'));
				}
			} else {
				$msg = '删除留言:'.$names;
				$this->saveLog(0,0,$msg);
				$this->error ( L('INVALID_OP') );
			}
		}
		$this->forward ();
	}
	
}
?>