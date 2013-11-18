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

// 团购券
class GroupBondAction extends CommonAction{
	public function __construct()
	{
		$goods_id = intval(Session::get('goods_id'));
		if($goods_id==0||isset($_REQUEST['goods_id']))
		{
			if(!$_SESSION['all_city'])
			$goods_id = D("Goods")->where(array("id"=>intval($_REQUEST['goods_id']),"city_id"=>array("in",$_SESSION['admin_city_ids'])))->getField("id");
			else
			$goods_id = intval($_REQUEST['goods_id']);
		}
		
		Session::set('goods_id',$goods_id);
	
		parent::__construct();
		
		if(!Session::is_set('goods_id')&&$_REQUEST['a']!='printTpl'&&$_REQUEST['a']!='updateTpl')
			$this->redirect('Goods/index');
			
		$this->assign('goods_id',Session::get('goods_id'));
	}
	
	public function index()
	{
		
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$map['goods_id'] = Session::get('goods_id');
		if(($_REQUEST['user_name']))
		$map['user_id'] = intval(M("User")->where("user_name='".trim($_REQUEST['user_name'])."'")->getField("id"));

		if(intval($map['user_id'])==0)
		unset($map['user_id']);
		
		unset($map['password']);
		unset($map['status']);

		$name=$this->getActionName();
		
		$model = D ($name);

		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->assign("map",$map);
		$this->display ();
	}
	
	public function printGroupBond()
	{
		
		$default_lang_id = D("LangConf")->where("lang_name='".C('DEFAULT_LANG')."'")->field("id")->find();
		$default_lang_id = $default_lang_id['id'];  //默认语言的ID
					
		$goods_id = intval($_REQUEST['goods_id']);
		if(!$_SESSION['all_city'])
		$goods_id = D("Goods")->where(array("id"=>intval($_REQUEST['goods_id']),"city_id"=>array("in",$_SESSION['admin_city_ids'])))->getField("id");
		$sql = "select a.*, b.user_name, b.email, b.mobile_phone from ".C("DB_PREFIX")."group_bond a left outer join ".C("DB_PREFIX")."user b on b.id = a.user_id where a.goods_id =".intval($_REQUEST['goods_id']);
		$list = M()->query($sql);
		$this->assign("list",$list);
		//dump($list);
		$goods = D("Goods")->where('id='.intval($_REQUEST['goods_id']))->findAll();//getField("name_".$default_lang_id." as goods_name,suppliers_id");
		//dump(D("Goods")->getLastSql());
		//dump($goods);
		$suppliers = D("Suppliers")->where('id='.intval($goods[0]['suppliers_id']))->findAll();
		$this->assign("suppliers_name",$suppliers[0]["name"]);
		
		$this->assign("goods_name",$goods[0]["name_".$default_lang_id]);			
		$this->assign("goods_id",$goods_id);
		$this->display("print");
	}	
	
	public function add()
	{
		$goods_id = $_REQUEST['goods_id'];
		if(!$_SESSION['all_city'])
		$goods_id = D("Goods")->where(array("id"=>intval($_REQUEST['goods_id']),"city_id"=>array("in",$_SESSION['admin_city_ids'])))->getField("id");
		$this->assign("goods_id",$goods_id);
		$this->display();
	}
	
	public function batchAdd()
	{
		$goods_id = $_REQUEST['goods_id'];
		if(!$_SESSION['all_city'])
		$goods_id = D("Goods")->where(array("id"=>intval($_REQUEST['goods_id']),"city_id"=>array("in",$_SESSION['admin_city_ids'])))->getField("id");
		$this->assign("goods_id",$goods_id);
		$this->display();
	}
	
	public function batchEdit()
	{
		$goods_id = $_REQUEST['goods_id'];
		if(!$_SESSION['all_city'])
		$goods_id = D("Goods")->where(array("id"=>intval($_REQUEST['goods_id']),"city_id"=>array("in",$_SESSION['admin_city_ids'])))->getField("id");
		$this->assign("goods_id",$goods_id);
		$this->display();
	}
	
	public function batchInsert()
	{
		$name=$this->getActionName();
		$model = D ($name);
		$goodsID = intval($_REQUEST["goods_id"]);
		if(!$_SESSION['all_city'])
		$goodsID = D("Goods")->where(array("id"=>intval($_REQUEST['goods_id']),"city_id"=>array("in",$_SESSION['admin_city_ids'])))->getField("id");
		$number = intval($_REQUEST["number"]);
		$prefix = trim($_REQUEST["prefix"]);
		$is_password = intval($_REQUEST["is_password"]);
		$end_time = localStrToTimeMax($_REQUEST["end_time"]);
		
		$data['goods_id'] = $goodsID;
		$data['end_time'] = $end_time;
		$data['status'] = 0;
		if($number > 0 && $goodsID > 0 && $end_time > 0)
		{
			$name=$this->getActionName();
			$model = D ($name);
		
			$default_lang_id = D("LangConf")->where("lang_name='".C('DEFAULT_LANG')."'")->field("id")->find();
			$default_lang_id = $default_lang_id['id'];  //默认语言的ID
			$select_dispname = "name_".$default_lang_id;

			$goods_name = D("Goods")->where('id='.$_REQUEST['goods_id'])->getField("name_".$default_lang_id);
			$data['goods_name'] = $goods_name;
			
			for ($i = 0; $i < $number; $i++)
			{
				$tempsn = gen_groupbond_sn($goodsID);
				$data['sn'] = $prefix.$tempsn;
				
				if($is_password == 1)
				{
					$password = unpack('H8',str_shuffle(md5(uniqid())));
					$data['password'] = $password[1];
				}
					
				while(!$model->add($data))
				{
					$tempsn = gen_groupbond_sn($goodsID);
					$data['sn'] = $prefix.$tempsn;
				}
				
			}
			
			$this->saveLog(1);
			$this->success (L('ADD_SUCCESS'));
		}
		else
		{
			$this->saveLog(0);
			$this->error (L('ADD_FAILED'));
		}
	}
	
	public function batchUpdate()
	{
		$goodsID = intval($_REQUEST["goods_id"]);
		if(!$_SESSION['all_city'])
		$goodsID = D("Goods")->where(array("id"=>intval($_REQUEST['goods_id']),"city_id"=>array("in",$_SESSION['admin_city_ids'])))->getField("id");
		$end_time = localStrToTimeMax($_REQUEST["end_time"]);
		if($end_time > 0)
		{
		  M("GroupBond")->where("goods_id = '$goodsID'")->setField("end_time",$end_time);
		  $this->success (L('EDIT_SUCCESS'));
		}
		else
		{
			$this->error(L('EDIT_FAILED'));
		}
	}
	
	function insert() {
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		
		if($model->where("sn = '".$_REQUEST['sn']."'")->count() > 0)
		{
			$this->error ('此序列号已经被使用');
		}
		
		$list=$model->add ();
		
		if ($list!==false) { //保存成功
			
			$default_lang_id = D("LangConf")->where("lang_name='".C('DEFAULT_LANG')."'")->field("id")->find();
			$default_lang_id = $default_lang_id['id'];  //默认语言的ID
			$select_dispname = "name_".$default_lang_id;

			$goods_name = D("Goods")->where('id='.$_REQUEST['goods_id'])->getField("name_".$default_lang_id);
			$model-> where('id = '.$list)->setField('goods_name',$goods_name);
			
			$this->saveLog(1,$list);
			$this->success (L('ADD_SUCCESS'));
		} else {
			//失败提示
			$this->saveLog(0,$list);
			$this->error (L('ADD_FAILED'));
		}
	}
	
	public function edit() {
		$name=$this->getActionName();
		$model = M ( $name );
		$id = $_REQUEST [$model->getPk ()];
		$vo = $model->getById ( $id );
		$this->assign ( 'vo', $vo );
		$this->display();
	}
	
	function update() {
		//B('FilterString');
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		
		// 更新数据
		$list=$model->save ();
		
		if (false !== $list) {
			$default_lang_id = D("LangConf")->where("lang_name='".C('DEFAULT_LANG')."'")->field("id")->find();
			$default_lang_id = $default_lang_id['id'];  //默认语言的ID
			$select_dispname = "name_".$default_lang_id;

			$goods_name = D("Goods")->where('id='.$_REQUEST['goods_id'])->getField("name_".$default_lang_id);
			$model-> where('id = '.$list)->setField('goods_name',$goods_name);
			
			$this->saveLog(1);
			$this->success (L('EDIT_SUCCESS'));
		} else {
			//错误提示
			$this->saveLog(0);
			$this->error (L('EDIT_FAILED'));
		}
	}
	
	public function getGroupBondStatus($status)
	{
		if($status == 0)
			return "未激活";
		elseif($status == 1)
			return "已激活";
	}
	
	public function getUsername($user_id)
	{
		return D("User")->where("id = $user_id")->getField("user_name");
	}
	
	public function printTpl()
	{
		$html = eyooC("PRINT_TPL");
		$this->assign('html',$html);
		$this->display();
	}
	
	public function updateTpl()
	{
		$content = $_REQUEST['content'];
		$content = stripslashes($content);
		$content = html_entity_decode($content);
		$content = urldecode($content);	
		M("SysConf")->where("name='PRINT_TPL'")->setField("val",$content);
		$this->success("更新成功");
	}
	
	/*下载团购券导入文件*/
	public function download()
	{
		$content = utf8ToGB("序列号,密码,过期时间" . "\n");
		$timestr = toDate(gmtTime(),"Y-m-d H:i:s");
		$content .= utf8ToGB("FW123456,FWPWD123456,".$timestr . "\n");	
	    header("Content-Disposition: attachment; filename=sample.csv");
	    echo $content; 
	}
	
	//导出会员列表
	function exportcsv($page = 1,$condition=''){
		set_time_limit(0);
		$limit = (($page - 1)*500).",".(500);
		if($condition=='')
		{
			if(!$_SESSION['all_city'])
			$goods_id = D("Goods")->where(array("id"=>intval($_REQUEST['goods_id']),"city_id"=>array("in",$_SESSION['admin_city_ids'])))->getField("id");
			else
			$goods_id = intval($_REQUEST['goods_id']);
			$condition['goods_id'] = $goods_id;
			$condition['sn'] = $_REQUEST['sn'];
			$condition['order_sn'] = $_REQUEST['order_sn'];
			$condition['user_name'] = $_REQUEST['user_name'];
		}	
		$sql = "select gb.*,u.user_name,u.mobile_phone from ".C("DB_PREFIX")."group_bond as gb left join ".C("DB_PREFIX")."user  as u on u.id = gb.user_id where 1=1 ";
		
		if($condition['goods_id']>0)
		$sql .= " and gb.goods_id = ".$condition['goods_id'] ;
		
		if($condition['sn']&&$condition['sn']!='')
		$sql .= " and gb.sn = '".$condition['sn']."'" ;
		
		if($condition['user_name']&&$condition['user_name']!='')
		$sql .= " and u.user_name = '".$condition['user_name']."'" ;
		
		if($condition['order_sn']&&$condition['order_sn']!='')
		$sql .= " and gb.order_id = '".$condition['order_sn']."'" ;
		
		$sql.=" group by gb.id limit ".$limit;
		
		$list = D()->query($sql);
		if($list)
		{
			register_shutdown_function(array(&$this, 'exportcsv'), $page+1,$condition);
			//dump($sql);
	    	/* csv文件数组 */
	    	$groupBond_value = array('id'=>'""', 'sn'=>'""', 'password'=>'""', 'goods_id'=>'""', 'goods_name'=>'""', 'order_id'=>'""', 'user_name'=>'""','mobile_phone'=>'""', 'end_time'=>'""', 'use_time'=>'""');
			if($page == 1)
	    	$content = utf8ToGB("编号,序列号,密码,团购编号,团购名称,订单号,会员名称,会员手机号,过期时间,使用时间" . "\n");
	    	 
			foreach($list as $k=>$v)
			{
				$groupBond_value['id'] = utf8ToGB('"' . $v['id'] . '"');
				$groupBond_value['sn'] = utf8ToGB('"' . $v['sn'] . '"');
				$groupBond_value['password'] = utf8ToGB('"' . $v['password'] . '"');
				$groupBond_value['goods_id'] = utf8ToGB('"' . $v['goods_id'] . '"');
				$groupBond_value['goods_name'] = utf8ToGB('"' . $v['goods_name'] . '"');
				$groupBond_value['order_id'] = utf8ToGB('"' . $v['order_id'] . '"');
				$groupBond_value['user_name'] = utf8ToGB('"' . $v['user_name'] . '"');
				$groupBond_value['mobile_phone'] = utf8ToGB('"' . $v['mobile_phone'] . '"');
				$groupBond_value['end_time'] = utf8ToGB('"' . toDate($v['end_time']) . '"');
				$groupBond_value['use_time'] = utf8ToGB('"' . toDate($v['use_time']) . '"');				
				$content .= implode(",", $groupBond_value) . "\n";
			}	
			
	    	header("Content-Disposition: attachment; filename=group_bond_list.csv");
	    	//header("Content-Type: application/octet-stream");
	    	//die();
	    	echo $content;   
		}
	}	
		
	public function export()
	{
		ini_set("memory_limit","150M"); 
		set_time_limit(0);
		$goods_id = intval($_REQUEST['goods_id']);
		
		Vendor('PHPExcel');
		Vendor('PHPExcel.IOFactory');
		$groupBondPHPExcel = new PHPExcel();
		$groupBondPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1','ID号')
					->setCellValue('B1','序列号')
					->setCellValue('C1','密码')
					->setCellValue('D1','团购编号')
					->setCellValue('E1','团购名称')
					->setCellValue('F1','订单号')
					->setCellValue('G1','会员名称')
					->setCellValue('H1','过期时间')
					->setCellValue('I1','使用时间');
					
		$name = M("Goods")->where("id=".$goods_id)->getField("name_1");
		
		$sql = "select gb.*,u.user_name from ".C("DB_PREFIX")."group_bond as gb left join ".C("DB_PREFIX")."user  as u on u.id = gb.user_id where gb.goods_id = $goods_id group by gb.id";

		$groupBondList = M()->query($sql);
		
		$i = 2;
		foreach($groupBondList as $groupBond)
		{
			$groupBondPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A$i",$groupBond['id'])
					->setCellValue("B$i",$groupBond['sn'])
					->setCellValue("C$i",$groupBond['password'])
					->setCellValue("D$i",$groupBond['goods_id'])
					->setCellValue("E$i",$groupBond['goods_name'])
					->setCellValue("F$i",$groupBond['order_id'])
					->setCellValue("G$i",$groupBond['user_name'])
					->setCellValue("H$i",toDate($groupBond['end_time']))
					->setCellValue("I$i",toDate($groupBond['use_time']));
			$i++;
		}
		
		$groupBondPHPExcel->getActiveSheet()->setTitle('团购券');
		$groupBondPHPExcel->setActiveSheetIndex(0);
		
		header('Content-Type:application/vnd.ms-excel;');
		header('Content-Disposition:attachment;filename="'.utf8ToGB($name).'.xls"');
		header('Cache-Control: max-age=0');
		$groupBondWriter = PHPExcel_IOFactory::createWriter($groupBondPHPExcel, 'Excel5');
		$groupBondWriter->save('php://output'); 
	}
	
	/*导入*/	
	public function importInsert()
	{
		$gbExcel = $_FILES['excel'];		
		$csvPath = $this->getRealPath()."/admin/Runtime/Temp/".uniqid().".csv";
		$goodsID = intval($_REQUEST["goods_id"]);
		if(!$_SESSION['all_city'])
		$goodsID = D("Goods")->where(array("id"=>intval($_REQUEST['goods_id']),"city_id"=>array("in",$_SESSION['admin_city_ids'])))->getField("id");
		if(intval($goodsID)==0)
		{
			$this->error("非法操作");
		}
		
		$goods_info = D("Goods")->where('id='.$goodsID)->find();
		$goods_name = $goods_info['goods_short_name']==''?$goods_info['name_1']:$goods_info['goods_short_name'];		
		$data['goods_id'] = $goodsID;
		$data['goods_name'] = $goods_name;
		$fail = 0;
		if(move_uploaded_file($gbExcel['tmp_name'],$csvPath))
		{
			$content = @file_get_contents($csvPath);
			$content = explode("\n",$content);
			unset($content[0]);
			foreach($content as $k=>$v)
			{
				if($v!='')
				{
					$imp_row = explode(",",$v);
					//开始检测 $v[0]  是否存在
					if(M("GroupBond")->where("goods_id=".$goodsID." and sn='".trim($imp_row[0])."'")->count()==0)
					{
						$data['sn'] = trim($imp_row[0]);
						if(trim($imp_row[1])!='')
						{
							$data['password'] = trim($imp_row[1]);
						}
						else
						{
							$data['password'] = "";
						}
						$data['end_time'] = localStrToTimeMax($imp_row[2]);
						$res = M("GroupBond")->add($data);
						if(intval($res)==0)
						{
							$fail = 1;
						}
					}				
				}
			}
		}
		@unlink($csvPath);
		if($fail==0)
		$this->success("导入成功");
		else
		$this->error("导入失败");
	}
	
	public function send_sms()
	{
		$gb_id = intval($_REQUEST['id']);
		$gb = M("GroupBond")->getById($gb_id);
		$status = send_sms($gb['user_id'], $gb_id, true);
		if($status)
		$this->success("发送成功");
		else
		$this->success("发送失败");
	}
	public function send_mail()
	{
		$gb_id = intval($_REQUEST['id']);
		$gb = M("GroupBond")->getById($gb_id);
		if(eyooC("MAIL_ON")==0)
			$this->error("邮件服务未开启");
		else {
			send_grounp_bond_mail($gb['user_id'], $gb_id,true);
			$this->success("发送成功");
		}

	}
}
?>