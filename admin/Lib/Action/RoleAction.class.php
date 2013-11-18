<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 管理员角色
class RoleAction extends CommonAction{
	public function edit()
	{
		$role_id = intval($_REQUEST['id']);		
		$node_ids_res = D("RoleAccess")->where("role_id=".$role_id)->field("node_id")->findAll();	
			
		$node_ids = array();
		foreach($node_ids_res as $row)
		{
			array_push($node_ids,$row['node_id']);	
		}
		
		//取出模块授权
		$modules = D("RoleNode")->where("status = 1 and auth_type = 1")->findAll();
		foreach($modules as $k=>$v)
		{
			$modules[$k]['actions'] = D("RoleNode")->where("status=1 and auth_type = 0 and module='".$v['module']."'")->findAll();
		}

		
		foreach($modules as $k=>$module)
		{
			if(in_array($module['id'],$node_ids))
			{
				$modules[$k]['checked'] = true;
			}
			else 
			{
				$modules[$k]['checked'] = false;
			}
			foreach($module['actions'] as $kk=>$action)
			{
					$checkall = true;
					if(in_array($action['id'],$node_ids))
					{
						$modules[$k]['actions'][$kk]['checked'] = true;
					}
					else 
					{
						$checkall = false;
						$modules[$k]['actions'][$kk]['checked'] = false;
					}				
			}
			if($checkall)
			{
				$modules[$k]['checkall'] = true;
			}
			else 
			{
				$modules[$k]['checkall'] = false;
			}

		}
		$this->assign('access_list',$modules);
		parent::edit();
	}
	
	public function update()
	{
		 $role_id = intval($_REQUEST['id']);
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
				D("RoleAccess")->where("role_id=".$role_id)->delete();
				$node_ids = $_REQUEST['access_node'];
				foreach($node_ids as $node_id)
				{
					$data['role_id'] = $role_id;
					$data['node_id'] = $node_id;
					D("RoleAccess")->add($data);
				}
				$this->saveLog(1);
	//			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
				$this->success (L('EDIT_SUCCESS'));
			} else {
				//错误提示
				$this->saveLog(0);
				$this->error (L('EDIT_FAILED'));
			}
	}
	
	public function add()
	{
		//取出模块授权
		$modules = D("RoleNode")->where("status = 1 and auth_type = 1")->findAll();
		foreach($modules as $k=>$v)
		{
			$modules[$k]['actions'] = D("RoleNode")->where("status=1 and auth_type = 0 and module='".$v['module']."'")->findAll();
		}

		$this->assign('access_list',$modules);
		parent::add();
	}
	public function insert()
	{
	//B('FilterString');
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$list=$model->add ();
		if ($list!==false) { //保存成功
				$node_ids = $_REQUEST['access_node'];
				foreach($node_ids as $node_id)
				{
					$data['role_id'] = $list;
					$data['node_id'] = $node_id;
					D("RoleAccess")->add($data);
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
	public function foreverdelete() {
		//删除指定记录
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				if(D("Admin")->where(array ("role_id" => array ('in', explode ( ',', $id ) ) ))->count()>0)
				{
					$this->saveLog(0);
					$this->error (L('ADM_EXIST_IN_ROLE'));
				}
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if (false !== $model->where ( $condition )->delete ()) {
					//echo $model->getlastsql();
					//删除相关角色的权限
					D("RoleAccess")->where(array ("role_id" => array ('in', explode ( ',', $id ) ) ))->delete();
					$this->saveLog(1);
					$this->success (L('DEL_SUCCESS'));
				} else {
					$this->saveLog(0);
					$this->error (L('DEL_FAILED'));
				}
			} else {
				$this->saveLog(0);
				$this->error ( L('INVALID_OP'));
			}
		}
		$this->forward ();
	}
}
?>