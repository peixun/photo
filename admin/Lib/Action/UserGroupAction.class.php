<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 *///会员等级分组
class UserGroupAction extends CommonAction{
	
	public function insert() {
		//B('FilterString');
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$list=$model->add ();
		if ($list!==false) { //保存成功
			$msg = "添加会员组".$_REQUEST['name_'.DEFAULT_LANG_ID];
			$this->saveLog(1,$list,$msg);
//			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('ADD_SUCCESS'));
		} else {
			//失败提示
			$msg = "添加会员组".$_REQUEST['name_'.DEFAULT_LANG_ID];
			$this->saveLog(0,0,$msg);
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
		// 更新数据
		$list=$model->save ();
		if (false !== $list) {
			//成功提示
			$msg = "修改会员组".$_REQUEST['name_'.DEFAULT_LANG_ID];
			$this->saveLog(1,$list,$msg);
//			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('EDIT_SUCCESS'));
		} else {
			//错误提示
			$msg = "修改会员组".$_REQUEST['name_'.DEFAULT_LANG_ID];
			$this->saveLog(0,$list,$msg);
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
			$ids = explode ( ',', $id );
			$names = '';
			foreach($ids as $idd)
			{
				$names .= M("UserGroup")->where("id=".$idd)->getField("name_".DEFAULT_LANG_ID).",";
			}
			if($names!='')
			{
				$names = substr($names,0,strlen($names)-1);
			}
			if (isset ( $id )) {
				if(D("User")->where(array ("group_id" => array ('in', explode ( ',', $id ) ) ))->count()>0)
				{
					$msg = "删除会员组失败";
					$this->saveLog(0,0,$msg);
					$this->error ( L('USER_EXIST_IN_GROUP') );
				}
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if (false !== $model->where ( $condition )->delete ()) {
					//echo $model->getlastsql();
					D("UserGroupPrice")->where(array ('user_group_id' => array ('in', explode ( ',', $id ) ) ))->delete();
					$msg = "删除会员组:".$names;
					$this->saveLog(1,0,$msg);
					$this->success (L('DEL_SUCCESS'));
				} else {
					$msg = "删除会员组失败";
					$this->saveLog(0,0,$msg);
					$this->error (L('DEL_FAILED'));
				}
			} else {
				$msg = "删除会员组失败";
				$this->saveLog(0,0,$msg);
				$this->error ( L('INVALID_OP') );
			}
		}
		$this->forward ();
	}
	
	public function forbid() {
		$name=$this->getActionName();
		$model = D ($name);
		$pk = $model->getPk ();
		$id = $_REQUEST [$pk];
		$condition = array ($pk => array ('in', $id ) );
		$list=$model->forbid ( $condition );
		$name = $model->where("id=".$id)->getField("name_".DEFAULT_LANG_ID);
		if ($list!==false) {
			D("User")->forbid(array ("group_id" => array ('in', $id ) ));  //同步禁用分组下的会员
			$msg = "禁用会员组".$name;
			$this->saveLog(1,0,$msg);
				
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( L('FORBID_SUCCESS') );
		} else {
			$msg = "禁用会员组".$name;
			$this->saveLog(0,0,$msg);
			$this->error  (  L('FORBID_FAILED') );
		}
	}
	
	public function resume() {
		//恢复指定记录
		$name=$this->getActionName();
		$model = D ($name);
		$pk = $model->getPk ();
		$id = $_GET [$pk];
		$condition = array ($pk => array ('in', $id ) );
		$name = $model->where("id=".$id)->getField("name_".DEFAULT_LANG_ID);
		if (false !== $model->resume ( $condition )) {
			$msg = "恢复会员组".$name;
			$this->saveLog(1,0,$msg);
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( L('RESUME_SUCCESS') );
		} else {
			$msg = "恢复会员组".$name;
			$this->saveLog(0,0,$msg);
			$this->error ( L('RESUME_FAILED') );
		}
	}
	

}
?>