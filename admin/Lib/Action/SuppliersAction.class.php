<?php
// +----------------------------------------------------------------------
// | Fanwe 多语商城建站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: awfigq(67877605@qq.com)
// +----------------------------------------------------------------------

// 文章
class SuppliersAction extends CommonAction{
	public function index()
	{
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		
		$name=$this->getActionName();
		
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->assign("map",$map);
		
		$this->display ();
	}
	//增
	public function add()
	{
		$list = M("SuppliersCate")->findAll();
		$this->assign("cate_list",$list);

		$this->display();
	}
	//改
	public function edit()
	{
		$list = M("SuppliersCate")->findAll();
		$this->assign("cate_list",$list);
				
		parent::edit();
	}
	public function departList()
	{		
		$map = $this->_search ();
		$map['supplier_id'] = intval($_REQUEST['supplier_id']);
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		
		$model = D ("SuppliersDepart");
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->assign("map",$map);
		
		$this->display ();
	}
	public function edit_tmpl()
	{
		$id = intval($_REQUEST['id']);
		$this->assign("html",M("Suppliers")->where("id=".$id)->getField("bond_tmpl"));
		$this->assign("id",$id);
		$this->display();
	}
	public function updateTpl()
	{
		$content = $_REQUEST['content'];
		$id = intval($_REQUEST['id']);
		M("Suppliers")->where("id=".$id)->setField("bond_tmpl",$content);
		$this->success("修改成功");
	}
	public function insert()
	{
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		
		$maps = $_REQUEST['maps'];
		$addresss = $_REQUEST['addresss'];
		
		foreach ($addresss as $k=>$v)
		{
			$data['address_'.($k+1)] = $v;
		}
		foreach ($maps as $k=>$v)
		{
			$data['map_'.($k+1)] = $v;
		}
		
		$file = $this->getRealPath()."/Public/bond_tmpl.html";
		$bond_tmpl = @file_get_contents($file);
		$bond_tmpl = str_replace("{\$SHOP_LOGO}",__ROOT__."/".eyooC("SHOP_LOGO"),$bond_tmpl);
		$data['bond_tmpl'] = $bond_tmpl;   //为预设赋值
		
		
		
		//保存当前数据对象
		$list = $model->add ($data); 
		if ($list!==false) { //保存成功
			
			//处理预览图
			if($_FILES['img']['name']!='')
			{
				$tmp_file = $_FILES['attachment'];
				unset($_FILES['attachment']);
				$uplist = $this->uploadFile();
				if($uplist)
				{
				$img_path = $uplist[0]['recpath'].$uplist[0]['savename'];
				D("Suppliers")->where("id=".$list)->setField("img",$img_path);
				unset($_FILES);
				$_FILES['attachment'] = $tmp_file;
				}
			}
			else 
			{
				unset($_FILES['img']);
			}
			$msg = '添加供应商'.$_REQUEST['name'];
			$this->saveLog(1,$list,$msg);			
			$this->assign ( 'jumpUrl', u('Suppliers/index'));
			$this->success (L('ADD_SUCCESS'));
		} else {
			//失败提示
			$msg = '添加供应商'.$_REQUEST['name'];
			$this->saveLog(0,0,$msg);	
			$this->error (L('ADD_FAILED'));
		}
	}
	
	public function update()
	{
		$name=$this->getActionName();
		$model = D ( $name );
		if (false ===$data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		$data['address_1'] = '';
		$data['address_2'] = '';
		$data['address_3'] = '';
		$data['address_4'] = '';
		$data['address_5'] = '';
		
		$data['map_1'] = '';
		$data['map_2'] = '';
		$data['map_3'] = '';
		$data['map_4'] = '';
		$data['map_5'] = '';
		
		$maps = $_REQUEST['maps'];
		$addresss = $_REQUEST['addresss'];
		
		foreach ($addresss as $k=>$v)
		{
			$data['address_'.($k+1)] = $v;
		}
		foreach ($maps as $k=>$v)
		{
			$data['map_'.($k+1)] = $v;
		}
		
		// 更新数据
		$list=$model->save ($data);
		if (false !== $list) {
			//成功提示
			//处理预览图
			
			if($_FILES['img']['name']!='')
			{
				$s_info = D("Suppliers")->getById(intval($_REQUEST['id']));
				$tmp_file = $_FILES['attachment'];
				unset($_FILES['attachment']);
				$uplist = $this->uploadFile();
				if($uplist)
				{
					@unlink($this->getRealPath().$s_info['img']);
					$img_path = $uplist[0]['recpath'].$uplist[0]['savename'];
					D("Suppliers")->where("id=".intval($_REQUEST['id']))->setField("img",$img_path);
					unset($_FILES);
					$_FILES['attachment'] = $tmp_file;
				}
			}
			else 
			{
				unset($_FILES['img']);
			}
			
			$msg = '修改供应商'.$_REQUEST['name'];
			$this->saveLog(1,$list,$msg);	
			$this->assign ( 'jumpUrl', u('Suppliers/index'));
			$this->success (L('EDIT_SUCCESS'));
		} else {
			//错误提示
			$msg = '修改供应商'.$_REQUEST['name'];
			$this->saveLog(0,0,$msg);
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
				$names .= M("Suppliers")->where("id=".$idd)->getField("name").",";
			}
			if($names!='')
			{
				$names = substr($names,0,strlen($names)-1);
			}
			if (isset ( $id )) {
				if(M("Goods")->where(array ('suppliers_id' => array ('in', explode ( ',', $id ) ) ))->findAll())
				{
					$this->error("该商家还存在未删除的团购活动");
				}
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				$s_list = $model->where ( $condition )->findAll();
				if (false !== $model->where ( $condition )->delete ())
				{
					foreach($s_list as $s_item)
					{
						@unlink($this->getRealPath().$s_item['img']);
					}
					$msg = '删除供应商:'.$names;
					$this->saveLog(1,0,$msg);
					
					$this->success (L('DEL_SUCCESS'));
				} else {
					$msg = '删除供应商:'.$names;
					$this->saveLog(0,0,$msg);
					$this->error (L('DEL_FAILED'));
				}
			} else {
				$msg = '删除供应商:'.$names;
				$this->saveLog(0,0,$msg);
				$this->error ( L('INVALID_OP') );
			}
		}
	}
	
	public function deleteImg()
	{
		$s_id = intval($_REQUEST['id']);
		$s_info = D("Suppliers")->getById($s_id);
		if($s_info['img']!="")
		{
			@unlink($this->getRealPath().$s_info['img']);
			D("Suppliers")->where("id=".$s_id)->setField("img","");
		}
		
		$this->success (L('DEL_SUCCESS'));
	}
	
    public function reset()
    {
		    	
		$name=$this->getActionName();
		$model = M ( $name );
		$id = $_REQUEST [$model->getPk ()];
		$vo = $model->getById ( $id );
		$this->assign ( 'vo', $vo );
		$this->display ("reset_password");
    }
    
    public function doReset()
    {
    	$cfm_password = trim($_POST['user_pwd_confirm']);
    	$user_pwd = trim($_POST['user_pwd']);

		
		$err = "";
		
		if(strlen($user_pwd) < 4)
		{
			$err = "密码太短，至少4个字符";
		}
		elseif($user_pwd !=$cfm_password)
		{
			$err = "密码跟确认密码不一致";
		}
		
		if($err != '')
		{
			$this->assign("error",$err);
			$this->reset();
			exit;
		}
		
		
		$suppliersinfo = D("Suppliers")->where("id = ".intval($_POST['id']))->find();
		if($suppliersinfo)
		{
			$suppliersinfo['pwd'] = md5($user_pwd);
			D("Suppliers")->save($suppliersinfo);
		}

		$msg = '重设供应商['.$suppliersinfo['name'].']密码';
		$this->saveLog(1,$suppliersinfo['id'],$msg);
		$this->assign("jumpUrl",U("Suppliers/index"));
		$this->success('您的密码已重设成功');		
    }

	public function getSuppliersName($cID)
	{
		return D("Suppliers")->where("id=$cID")->getField('name');
	}    
	
	public function editTpl()
	{
		$file = $this->getRealPath()."/Public/bond_tmpl.html";
		$bond_tmpl = @file_get_contents($file);
		$this->assign("bond_tmpl",$bond_tmpl);
		$this->display();
	}
	public function updateTmpl()
	{
		$content = $_REQUEST['content'];
		$file = $this->getRealPath()."/Public/bond_tmpl.html";
		$bond_tmpl = @file_put_contents($file,$content);
		$this->success("设置成功");
	}

	
	public function delDepart() {
		//删除指定记录
		$model = D ("SuppliersDepart");
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			$ids = explode ( ',', $id );
			$names = '';
			foreach($ids as $idd)
			{
				$names .= M("SuppliersDepart")->where("id=".$idd)->getField("depart_name").",";
			}
			if($names!='')
			{
				$names = substr($names,0,strlen($names)-1);
			}

				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
			
				if (false !== $model->where ( $condition )->delete ())
				{
					
					$msg = '删除供应商分店:'.$names;
					$this->saveLog(1,0,$msg);					
					$this->success (L('DEL_SUCCESS'));
				} 
				else 
				{
					$msg = '删除供应商分店:'.$names;
					$this->saveLog(0,0,$msg);
					$this->error (L('DEL_FAILED'));
				}

		}
	}
	
	public function addDepart()
	{
		$supplier_id = intval($_REQUEST['supplier_id']);
		$this->assign("supplier_id",$supplier_id);
		$this->display();
	}
	
	public function insertSupplierDepart()
	{		
		$model = D ("SuppliersDepart");
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		$data['pwd'] = md5($data['pwd']);
		if(M("SuppliersDepart")->where("supplier_id=".$data['supplier_id'])->count()==0)
		{
			$data['is_main'] = 1;
			M("Suppliers")->where("id=".$data['supplier_id'])->setField(array("api_address","xpoint","ypoint"),array($data['api_address'],$data['xpoint'],$data['ypoint']));
		}
		//保存当前数据对象
		$list = $model->add ($data); 
		if ($list!==false) { //保存成功
			
			$msg = '添加供应商管理员'.$_REQUEST['depart_name'];
			$this->saveLog(1,$list,$msg);			
			$this->success (L('ADD_SUCCESS'));
		} else {
			//失败提示
			$msg = '添加供应商管理员'.$_REQUEST['depart_name'];
			$this->saveLog(0,0,$msg);	
			$this->error (L('ADD_FAILED'));
		}		
	}
	
	public function editDepart()
	{
		$model =D ( "SuppliersDepart" );
		$id = $_REQUEST [$model->getPk ()];
		$vo = $model->getById ( $id );

		$this->assign ( 'vo', $vo );
		$this->display ();
	}
	
	public function updateSupplierDepart()
	{		
		$model = D ("SuppliersDepart");
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		if($data['pwd']!='')
		$data['pwd'] = md5($data['pwd']);
		else
		unset($data['pwd']);

		//保存当前数据对象
		$list = $model->save ($data); 
		if ($list!==false) { //保存成功
			$data = M("SuppliersDepart")->getById($data['id']);
			if($data['is_main']==1)
			{
				M("Suppliers")->where("id=".$data['supplier_id'])->setField(array("api_address","xpoint","ypoint"),array($data['api_address'],$data['xpoint'],$data['ypoint']));
			}
			
			$msg = '修改供应商管理员'.$_REQUEST['depart_name'];
			$this->saveLog(1,$list,$msg);			
			$this->success (L('EDIT_SUCCESS'));
		} else {
			//失败提示
			$msg = '修改供应商管理员'.$_REQUEST['depart_name'];
			$this->saveLog(0,0,$msg);	
			$this->error (L('EDIT_FAILED'));
		}		
	}
	public function setMain()
	{
		$id = intval($_REQUEST['id']);
		$data = M("SuppliersDepart")->getById($id);
		M("SuppliersDepart")->where("supplier_id=".$data['supplier_id'])->setField("is_main",0);
		$data['is_main'] = 1;
		M("SuppliersDepart")->save($data);
		if($data['is_main']==1)
			{
				M("Suppliers")->where("id=".$data['supplier_id'])->setField(array("api_address","xpoint","ypoint"),array($data['api_address'],$data['xpoint'],$data['ypoint']));
			}
		$this->success("设置成功");
	}
}
?>