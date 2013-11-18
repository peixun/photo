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

// 代金券
class EcvAction extends CommonAction{
	public function __construct()
	{
		if($_REQUEST['ecv_type'])
			Session::set('ecv_type',$_REQUEST['ecv_type']);
			
		parent::__construct();
		if(!Session::is_set('ecv_type'))
			$this->redirect('EcvType/index');
		$this->assign('ecv_type',Session::get('ecv_type'));
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
				$names .= M("Ecv")->where("id=".$idd)->getField("sn").",";
			}
			if($names!='')
			{
				$names = substr($names,0,strlen($names)-1);
			}
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if (false !== $model->where ( $condition )->delete ()) {
					//echo $model->getlastsql();
					$msg = '代金券:'.$names.'删除成功ID:'.$id;
					$this->saveLog(1,0,$msg);
					$ecv_types = M("EcvType")->findAll();
					foreach($ecv_types as $k=>$v)
					{
						M("EcvType")->where("id=".$v['id'])->setField("gen_count",M("Ecv")->where("ecv_type=".$v['id'])->count());						
					}
					$this->success (L('DEL_SUCCESS'));
				} else {
					$msg = '代金券:'.$names.'删除失败ID:'.$id;;
					$this->saveLog(0,0,$msg);
					$this->error (L('DEL_FAILED'));
				}
			} else {
				$msg = '代金券:'.$names.'删除失败';
				$this->saveLog(0,0,$msg);
				$this->error ( L('INVALID_OP') );
			}
		}
		$this->forward ();
	}
	
	public function index()
	{
		$map = $this->_search ();
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$map['ecv_type'] = Session::get('ecv_type');
		
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		
		$page = intval($_REQUEST[C("VAR_PAGE")]);
		
	    if($page==0)
	    	$page = 1;
			
		$listrow = 20;
		
		$sn  = trim($_REQUEST['sn']);
		$user_name  = trim($_REQUEST['user_name']);
		$use_user_name  = trim($_REQUEST['use_user_name']);
		$is_use  = $_REQUEST['is_use'];
		$order_sn  = $_REQUEST['order_sn'];
		
		$where = " e.ecv_type = ".Session::get('ecv_type')." and e.id is not null";
		if($is_use == "")
			$is_use = -1;
			
		if($is_use == 0)
			$where .= " and e.use_date_time = 0";
		elseif($is_use == 1)
			$where .= " and e.use_date_time > 0";
		
		if(!empty($order_sn))
			$where .= " and e.order_sn like '%$order_sn%'";
			
		if(!empty($sn))
			$where .= " and e.sn like '%$sn%'";
		
		if(!empty($user_name))
			$where .= " and u.user_name like '%$user_name%'";
			
		if(!empty($parent_name))
			$where .= " and uu.user_name like '%$use_user_name%'";
			
		$order = "e.id";
		$sort = "desc";
		
		if(!empty($_REQUEST["_order"]))
			$order = $_REQUEST["_order"];
			
		if($_REQUEST["_sort"] == 1)
			$sort = "asc";
			
		$sql = "select count(*) as c from ".C("DB_PREFIX")."ecv as e left join ".C("DB_PREFIX")."ecv_type  as et on et.id = e.ecv_type left join ".C("DB_PREFIX")."user as u on u.id = e.user_id left join ".C("DB_PREFIX")."user as uu on uu.id = e.use_user_id where".$where;
		
		$count = M()->query($sql);
		
		$count = intval($count[0]['c']);
		
		if(ceil($count / $listrow ) < intval($page))
			$page = ceil($count / $listrow );
		
		$limit = ($page-1)* $listrow .", $listrow";
		
		$sql = "select e.use_count,e.id,e.sn,e.order_sn,e.password,e.status,e.use_date_time,u.user_name,uu.user_name as use_user_name,et.name from ".C("DB_PREFIX")."ecv as e left join ".C("DB_PREFIX")."ecv_type  as et on et.id = e.ecv_type left join ".C("DB_PREFIX")."user as u on u.id = e.user_id left join ".C("DB_PREFIX")."user as uu on uu.id = e.use_user_id where".$where." group by e.id order by $order $sort LIMIT $limit";

		$list = M()->query($sql);
		
		
		$page = new Page($count,$listrow);   //初始化分页对象 		
		$p =  $page->show();
	    $this->assign('pages',$p);
		
		$this->assign("list",$list);
		$this->assign("map",$map);
		$this->display ();
	}
	
	public function add()
	{
		$user_group = D("UserGroup")-> where("status=1")-> findAll();
		$this->assign("user_group",$user_group);
		$this->display();
	}
	
	public function import()
	{
		$user_group = D("UserGroup")-> where("status=1")-> findAll();
		$this->assign("user_group",$user_group);
		$this->display();
	}
	
	public function insert()
	{
		$name=$this->getActionName();
		$model = D ($name);
		$ecv_type = intval($_REQUEST["ecv_type"]);
		$send_type = intval($_REQUEST["send_type"]);
		$is_password = intval($_REQUEST["is_password"]);
		$user_group = intval($_REQUEST["user_group"]);
		$user_ids = $_REQUEST["user_ids"];
		$number = intval($_REQUEST["number"]);
		$prefix = trim($_REQUEST["prefix"]);
		
		$where = " status = 1";
			
		if($send_type == 1)
		{
			if($user_group > 0)
				$where .= " and group_id = $user_group";
				
			$userList = D("User")->where($where)->field("id")->findAll();
			
			foreach($userList as $user)
			{
				$userIDList[] = $user['id'];
			}
			
		}
		elseif($send_type == 2)
		{
			$userIDList = explode(",", $user_ids);
		}
		
		if($send_type < 3)
			$number = count($userIDList);
		
		if($number >0)
		{
			$data['ecv_type'] = $ecv_type;
			$data['status'] = 0;
			
			for ($i = 0; $i < $number; $i++)
			{
				$tempsn = unpack('H8',str_shuffle(sha1(uniqid())));
				$data['sn'] = $prefix.$tempsn[1];
				
				if($send_type < 3)
					$data['user_id'] = intval($userIDList[$i]);
				
				if($is_password == 1)
				{
					$password = unpack('H8',str_shuffle(md5(uniqid())));
					$data['password'] = $password[1];
				}
									
				$data['type'] = M("EcvType")->where("id=".$data['ecv_type'])->getField("type");
				$data['use_count'] = M("EcvType")->where("id=".$data['ecv_type'])->getField("use_count");
				while(!$model->add($data))
				{
					$tempsn = unpack('H8',str_shuffle(sha1(uniqid())));
					$data['sn'] = $prefix.$tempsn[1];
				}
			}
			
			$msg = "代金券".M("EcvType")->where("id=".$data['ecv_type'])->getField("name")."发放成功";
			$this->saveLog(1,0,$msg);
			$ecv_types = M("EcvType")->findAll();
			foreach($ecv_types as $k=>$v)
					{
						M("EcvType")->where("id=".$v['id'])->setField("gen_count",M("Ecv")->where("ecv_type=".$v['id'])->count());						
					}
			$this->success (L('ADD_SUCCESS'));
		}
		else
		{
			
			if($send_type < 3)
				$this->error (L('ADD_FAILED')."，此会员组下没有会员");
			else
				$this->error (L('ADD_FAILED')."发布的数量为0");
			
			$msg = "代金券".M("EcvType")->where("id=".$data['ecv_type'])->getField("name")."发放失败";
			$this->saveLog(0,0,$msg);
		}	
	}
	
	public function getUserList()
	{
		$usergroup = intval($_REQUEST["usergroup"]);
		$username = trim($_REQUEST["username"]);
		
		$where = " status = 1";
		
		if($usergroup > 0)
			$where .= " and group_id = $usergroup";
			
		if(!empty($username))
			$where .= " and user_name like '%$username%'";
			
		$userlist = D("User")->where($where)->field("id,user_name")->findAll();
		
		echo json_encode($userlist);
	}
	
	/*下载代金券导入文件*/
	public function download()
	{
		$content = utf8ToGB("序列号,密码 \n");
		$content .= utf8ToGB("FW123456,FWPWD123456 \n");	
	    header("Content-Disposition: attachment; filename=sample.csv");
	    echo $content; 
	}
	
	
	public function importInsert()
	{
		$ecvExcel = $_FILES['ecvexcel'];
		$csvPath = $this->getRealPath()."/admin/Runtime/Temp/".uniqid().".csv";
		
		$name=$this->getActionName();
		$model = D ($name);
		$ecv_type = intval($_REQUEST["ecv_type"]);
		$send_type = intval($_REQUEST["send_type"]);
		$user_group = intval($_REQUEST["user_group"]);
		$user_ids = $_REQUEST["user_ids"];
		$use_count = M("EcvType")->where("id=".$ecv_type)->getField("use_count");
		$where = " status = 1";
			
		if($send_type == 1)
		{
			if($user_group > 0)
				$where .= " and group_id = $user_group";
				
			$userList = D("User")->where($where)->field("id")->findAll();
			
			foreach($userList as $user)
			{
				$userIDList[] = $user['id'];
			}
			
		}
		elseif($send_type == 2)
		{
			$userIDList = explode(",", $user_ids);
		}
		
		$errorEcv = array();
		
		$i = 0;
		if(move_uploaded_file($ecvExcel['tmp_name'],$csvPath))
		{
			$evcData['ecv_type'] = $ecv_type;
			$evcData['status'] = 0;
			
			$content = @file_get_contents($csvPath);
			$content = explode("\n",$content);
			unset($content[0]);
			foreach($content as $k=>$v)
			{
				if($v!='')
				{
					$imp_row = explode(",",$v);
					$evcData['sn'] = $imp_row[0];
				
					$password = $imp_row[1];
					
					if(!empty($password))
						$evcData['password'] = $password;
					else
						$evcData['password'] = "";
						
					if($send_type < 3)
						$evcData['user_id'] = intval($userIDList[$i]);
						
					$evcData['use_count'] = $use_count;
					if($model->add($evcData))
						$i++;
					else
						$errorEcv[] = $evcData['sn'];				
				}
			}
		}
		@unlink($csvPath);
		if(count($errorEcv) > 0)
		{
			$this->assign("sns",$errorEcv);
			$this->display("Ecv:error");
		}
		else
		{
			$msg = "代金券".M("EcvType")->where("id=".$data['ecv_type'])->getField("name")."导入成功";
			$this->saveLog(1,0,$msg);
			$ecv_types = M("EcvType")->findAll();
					foreach($ecv_types as $k=>$v)
					{
						M("EcvType")->where("id=".$v['id'])->setField("gen_count",M("Ecv")->where("ecv_type=".$v['id'])->count());						
					}
			$this->success (L('ADD_SUCCESS'));
		}
		
	}
}
?>