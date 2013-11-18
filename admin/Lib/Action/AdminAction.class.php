<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 管理员
class AdminAction extends CommonAction{
	public function add()
	{
		$role_list = D("Role")->where("status=1")->findAll();
		$this->assign("role_list",$role_list);
		$city_list = M("GroupCity")->findAll();
		$this->assign("city_list",$city_list);
		$this->display();
	}
	
	function insert() {
		//B('FilterString');
		$adm_pwd = $_REQUEST['adm_pwd'];
		$name=$this->getActionName();
		$model = D ($name);
		if (false ===$data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		$model->adm_pwd = md5($adm_pwd);
		//保存当前数据对象
		$list=$model->add ($data);
		if ($list!==false) { //保存成功
			$city_ids = $_REQUEST['city_ids'];
			foreach ($city_ids as $city_id)
			{
				$admin_city['admin_id'] = $list;
				$admin_city['city_id'] = $city_id;
				M("AdminCity")->add($admin_city);
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
	
	function edit() {
		$role_list = D("Role")->where("status=1")->findAll();
		$this->assign("role_list",$role_list);
		$name=$this->getActionName();
		$model = M ( $name );
		$id = $_REQUEST [$model->getPk ()];
		$vo = $model->getById ( $id );

		$this->assign ( 'vo', $vo );
		$city_list = M("GroupCity")->findAll();
		
		$admin_citys = M("AdminCity")->where("admin_id=".$vo['id'])->findAll();
		foreach($city_list as $k=>$v)
		{
			foreach ($admin_citys as $kk=>$vv)
			{
				if($vv['city_id']==$v['id'])
				{
					$city_list[$k]['checked']=1;
					break;
				}
			}
		}
		$this->assign("city_list",$city_list);
		$this->display ();
	}
	
	function update() {
		//B('FilterString');
		$new_adm_pwd = $_REQUEST['adm_pwd_new'];
		
		$id = intval($_REQUEST['id']);
		
		$name=$this->getActionName();
		$model = D ( $name );
		if (false ===$data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		if($new_adm_pwd!="")
			{
				$data['adm_pwd'] = md5($new_adm_pwd);
			}
			else 
			{
				$data['adm_pwd'] = D("Admin")->where("id=".$id)->getField("adm_pwd");
			}
		// 更新数据
		$list=$model->save ($data);
		if (false !== $list) {
			//成功提示
			
			M("AdminCity")->where("admin_id=".$data['id'])->delete();
			$city_ids = $_REQUEST['city_ids'];
			foreach ($city_ids as $city_id)
			{
				$admin_city['admin_id'] = $data['id'];
				$admin_city['city_id'] = $city_id;
				M("AdminCity")->add($admin_city);
			}
			
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
				$adm_list =D($name)->where($condition)->findAll();
				foreach($adm_list as $adm_item)
				{
					if($adm_item['adm_name']==eyooC("SYS_ADMIN"))
					{
						$this->saveLog(0);
						$this->error ( L('CANNT_DELETE_DEFAULT_ADM') );
					}
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
	
	public function forbid() {
		$name=$this->getActionName();
		$model = D ($name);
		$pk = $model->getPk ();
		$id = $_REQUEST [$pk];
		$condition = array ($pk => array ('in', $id ) );
				$adm_list =D($name)->where($condition)->findAll();
				foreach($adm_list as $adm_item)
				{
					if($adm_item['adm_name']==eyooC("SYS_ADMIN"))
					{
						$this->saveLog(0);
						$this->error ( L('CANNT_FORBID_DEFAULT_ADM') );
					}
				}
		$list=$model->forbid ( $condition );
		if ($list!==false) {
			$this->saveLog(1);
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( L('FORBID_SUCCESS') );
		} else {
			$this->saveLog(0);
			$this->error  (  L('FORBID_FAILED') );
		}
	}
	
	public function checkAdm()
	{
		$adm_name = $_REQUEST['adm_name'];
		$rs = M("Admin")->where("adm_name='".$adm_name."'")->find();
		if($rs)
		{
			$res['status'] = true;
			$res['origin'] = M("SysConf")->where("name='SYS_ADMIN'")->getField("val");
			echo json_encode($res);
		}
		else
		{
			$res['status'] = false;
			$res['origin'] = M("SysConf")->where("name='SYS_ADMIN'")->getField("val");
			echo json_encode($res);
		}
	}
}
?>