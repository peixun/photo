<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 *///会员
class BookingAction extends CommonAction{
	public function index()
	{
		//列表过滤器，生成查询Map对象
		$company = M("Company")->findAll();
		$this->assign("company",$company);
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
      //  dump();
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model,$map);
		}

		$this->assign("map",$map);

		$this->assign("city_list",D("GroupCity")->where("status=1")->findAll());
		$this->display ();
		return;
	}



	public function edit() {
		$default_lang_id = D("LangConf")->where("lang_name='".C('DEFAULT_LANG')."'")->field("id")->find();
		$default_lang_id = $default_lang_id['id'];  //默认语言的ID
		$select_dispname = "name_".$default_lang_id;
		$this->assign("select_dispname",$select_dispname);
		$this->assign("group_list",D("UserGroup")->findAll());


		$name=$this->getActionName();
		$model = M ( $name );
		$id = $_REQUEST [$model->getPk ()];
		$vo = $model->getById ( $id );
		$this->assign ( 'vo', $vo );
	    $umap['status']=1;
        $user_list = D("company")-> where($umap)-> findAll();


		$this->assign("user_list",$user_list);

		$this->display ();
	}
	function update() {
		//B('FilterString');
		$new_user_pwd = $_REQUEST['new_user_pwd'];
		$id = intval($_REQUEST['id']);
		$name=$this->getActionName();
		$model = D ( $name );
		$data = $model->create();
		if (false === $data) {
			$this->error ( $model->getError () );
		}

		$o_data = D("User")->getById($id);  //原来的用户数据

		if($new_user_pwd!="")
		{
			$data['user_pwd'] = md5($new_user_pwd);
			$cfg = array('username'=>$o_data['user_name'], 'password'=>$new_user_pwd);
		}
		else
		{
			$data['user_pwd'] = D("User")->where("id=".$id)->getField("user_pwd");
			$cfg = array('username'=>$o_data['user_name']);
		}

		// 更新数据


	    $users  = &init_users();
		$users->need_sync = false;
	    //dump($users);
	    //$cfg = array('username'=>$o_data['user_name'], 'password'=>$new_user_pwd, 'email'=>$data['email']);

	    if (!$users->edit_user($cfg))
	    {
	        if ($users->error == ERR_EMAIL_EXISTS)
	        {
	            $msg =  'eamil已经存在!';// $_LANG['email_exists'];
	        }
	        else
	        {
	            $msg = L('EDIT_FAILED');
	        }
			$this->saveLog(0,0,"修改会员".$o_data['user_name']);
			$this->error ($msg);
	    };


		$list=$model->save ($data);
		if (false !== $list) {

			//开始处理扩展字段的数据
			M("UserExtend")->where("user_id=".$data['id'])->delete();
		    $extend_fields = M("UserField")->where("is_show=1")->findAll();
		    foreach ($extend_fields as $kk=>$vv)
		    {
		    	$ext_data['field_value'] =$_REQUEST[$vv['field_name']];
		    	$ext_data['field_id'] = $vv['id'];
		    	$ext_data['user_id'] = $data['id'];
		    	M("UserExtend")->add($ext_data);
		    }

			//成功提示
			$score = intval($_POST['score']) - intval($o_data['score']);
			$money = floatval($_POST['money']) - floatval($o_data['money']);

			if($money!=0)
			user_money_log($o_data['id'],$o_data['id'],'User',$money,'#ADMIN_EDIT_USER#',true);
			if($score!=0)
			user_score_log($o_data['id'],$o_data['id'],'User',$score,'#ADMIN_EDIT_USER#',true);


			$mail_item = M("MailAddressList")->where("user_id=".$id)->find();
			if($mail_item)
			{
				M("MailAddressList")->where("user_id=".$id)->setField("city_id",$data['city_id']);
			}
			$msg = "修改会员".$o_data['user_name'];
			$this->saveLog(1,$list,$msg);
			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('EDIT_SUCCESS'));
		} else {
			//错误提示
			$msg = "修改会员".$o_data['user_name'];
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
			$users  = &init_users();
			$users->need_sync = false;
			foreach($ids as $idd)
			{
				$names .= M("User")->where("id=".$idd)->getField("user_name").",";
			}
			if($names!='')
			{
				$names = substr($names,0,strlen($names)-1);
			}
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if ((false !== $users->remove_user_by_names($names))&&(false !== $model->where ( $condition )->delete ())) {

					//删除会员将永久删除相关的一切留言，回复，订单，订单商品等数据，请谨慎操作
					//删除相关会员留言
					M("UserExtend")->where(array ('user_id' => array ('in', explode ( ',', $id ) ) ))->delete();
					$msglist = D("Message")->where(array ("user_id" => array ('in', explode ( ',', $id ) ) ))->findAll();
					D("Message")->where(array ("user_id" => array ('in', explode ( ',', $id ) ) ))->delete();
					foreach($msglist as $item)
					{
						D("Message")->where("pid=".$item['id'])->delete();
					}
					//删除订单及商品
					$orderlist = D("Order")->where( array ("user_id" => array ('in', explode ( ',', $id ) ) ))->findAll();
					D("Order")->where( array ("user_id" => array ('in', explode ( ',', $id ) ) ))->delete();
					foreach($orderlist as $orderitem)
					{
						D("OrderGoods")->where("order_id=".$orderitem['id'])->delete();
						M("OrderIncharge")->where("order_id=".$orderitem['id'])->delete();
						M("OrderUncharge")->where("order_id=".$orderitem['id'])->delete();
						M("OrderLog")->where("order_id=".$orderitem['id'])->delete();
						$order_re_consignment = M("OrderReConsignment")->where("order_id=".$orderitem['id'])->findAll();
						M("OrderReConsignment")->where("order_id=".$orderitem['id'])->delete();
						foreach($order_re_consignment as $reconsignment)
						{
							M("OrderReConsignmentGoods")->where("order_re_consignment_id = ".$reconsignment['id'])->delete();
						}
					}

					//删除会员的收货人列表
					D("UserConsignee")->where(array ("user_id" => array ('in', explode ( ',', $id ) ) ))->delete();

					//删除相关会员的资金与积分日志
					M("UserMoneyLog")->where(array ("user_id" => array ('in', explode ( ',', $id ) ) ))->delete();
					M("UserScoreLog")->where(array ("user_id" => array ('in', explode ( ',', $id ) ) ))->delete();

					//删除相关的团购券
					M("GroupBond")->where(array ("user_id" => array ('in', explode ( ',', $id ) ) ))->delete();

					//删除邮件
					M("MailAddressList")->where(array ("user_id" => array ('in', explode ( ',', $id ) ) ))->delete();

					$mail_address_ids = M("MailAddressList")->where(array ("user_id" => array ('in', explode ( ',', $id ) ) ))->findAll();
					foreach($mail_address_ids as $k=>$v)
					{
						M("MailAddressSendList")->where(array ("mail_address_id" => $v['id']))->delete();
					}

					//删除会员的发起团购
					$group_msg_ids = M("GroupMessage")->where(array ("user_id" => array ('in', explode ( ',', $id ) ) ))->findAll();
					foreach($group_msg_ids as $k=>$v)
					{
						M("GroupMessageFollow")->where(array ("message_id" => $v['id']))->delete();
					}
					M("GroupMessage")->where(array ("user_id" => array ('in', explode ( ',', $id ) ) ))->delete();
					M("GroupMessageFollow")->where(array ("user_id" => array ('in', explode ( ',', $id ) ) ))->delete();

					//返利
					M("Referrals")->where(array ("user_id" => array ('in', explode ( ',', $id ) ) ))->delete();

					//提现
					M("UserUncharge")->where(array ("user_id" => array ('in', explode ( ',', $id ) ) ))->delete();
					M("UserIncharge")->where(array ("user_id" => array ('in', explode ( ',', $id ) ) ))->delete();

					//促销卡
					$card_list = M("PromoteUserCard")->where(array ("user_id" => array ('in', explode ( ',', $id ) ) ))->findAll();
					M("PromoteUserCard")->where(array ("user_id" => array ('in', explode ( ',', $id ) ) ))->delete();
					foreach($card_list as $card_item)
					{
						M("PromoteCard")->where("id=".$card_item['card_id'])->delete();
					}

					//重置被推荐的会员
					M("User")->where(array ("parent_id" => array ('in', explode ( ',', $id ) ) ))->setField("parent_id",0);
					$msg = "删除会员:".$names;
					$this->saveLog(1,0,$msg);
					$this->success (L('DEL_SUCCESS'));
				} else {
					$msg = "删除会员:".$names;
					$this->saveLog(0,0,$msg);
					$this->error (L('DEL_FAILED'));
				}
			} else {
				$msg = "删除会员:".$names;
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
		$name = $model->where("id=".$id)->getField("user_name");
		if ($list!==false) {
			$msg = "禁用会员：".$name;
			$this->saveLog(1,$id,$msg);
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( L('FORBID_SUCCESS') );
		} else {
			$msg = "禁用会员：".$name;
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
		$name = $model->where("id=".$id)->getField("user_name");
		if (false !== $model->resume ( $condition )) {
			$msg = "恢复会员：".$name;
			$this->saveLog(1,$id,$msg);
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( L('RESUME_SUCCESS') );
		} else {
			$msg = "恢复会员：".$name;
			$this->saveLog(0,0,$msg);
			$this->error ( L('RESUME_FAILED') );
		}
	}

	/*下载团购券导入文件*/
	public function download()
	{
		Vendor('PHPExcel');
		Vendor('PHPExcel.IOFactory');
		$gbPHPExcel = new PHPExcel();
		$gbPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1','会员帐号')
					->setCellValue('B1','电子邮件')
					->setCellValue('C1','口令')
					->setCellValue('D1','手机');

		$gbPHPExcel->getActiveSheet()->setTitle('会员列表');
		$gbPHPExcel->setActiveSheetIndex(0);

		header('Content-Type:application/vnd.ms-excel;');
		header('Content-Disposition:attachment;filename="'.utf8ToGB('会员导入文件').'.xls"');
		header('Cache-Control: max-age=0');
		$gbWriter = PHPExcel_IOFactory::createWriter($gbPHPExcel, 'Excel5');
		$gbWriter->save('php://output');
	}


	//导出会员列表
	function expbookingcsv(){
		//set_time_limit(0);

        $Booking =D("Booking");
        if(!empty($_REQUEST['type'])){
            $condition['type'] = $_REQUEST['type'];
        }
        if(!empty($_REQUEST['company_id'])){
            $condition['company_id'] = $_REQUEST['company_id'];
        }
        if(!empty($_REQUEST['promote_begin_time'])&&!empty($_REQUEST['promote_end_time'])){
             $startime =localStrToTimeMin($_REQUEST['promote_begin_time']);
             $endtime =localStrToTimeMax($_REQUEST['promote_end_time']);
             $condition['create_time']=array(array('gt',$startime),array('lt',$endtime)) ;
        }elseif(!empty($_REQUEST['promote_begin_time'])){
             $startime =localStrToTimeMin($_REQUEST['promote_begin_time']);
             $condition['create_time']=array('gt',$startime);
        }elseif(!empty($_REQUEST['promote_end_time'])){
            $endtime =localStrToTimeMax($_REQUEST['promote_end_time']);
            $condition['create_time']=array('lt',$endtime) ;
        }else{

        }




		$list =$Booking->where($condition)->order('id desc')->select();



        Vendor('PHPExcel');
		Vendor('PHPExcel.IOFactory');
		$userPHPExcel = new PHPExcel();
		$userPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1','用户名')
					->setCellValue('B1','邮箱')
					->setCellValue('C1','手机')
					->setCellValue('D1','报名类型')
					->setCellValue('E1','报名时间');

		$i = 2;
		foreach($list as $user)
		{
             if($user['type']==1){
                    $type ='免费量房';
                }elseif($user['type']==2){
                     $type = '免费预算';
                }elseif($user['type']==3){
                    $type = '免费设计';
                }elseif($user['type']==4){
                     $type = '免费咨询';
                }elseif($user['type']==5){
                     $type ='活动报名';
                }else{

                }

            $create_time=date('Y-m-d',$user['create_time']);
			$userPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A$i",$user['user_name'])
					->setCellValue("B$i",$user['email'])
					->setCellValue("C$i",$user['mobile'])
					->setCellValue("D$i",$type)
					->setCellValue("E$i",$create_time);
			$i++;
		}

		$userPHPExcel->getActiveSheet()->setTitle('报名列表列表');
		$userPHPExcel->setActiveSheetIndex(0);

		header('Content-Type:application/vnd.ms-excel;');
		header('Content-Disposition:attachment;filename="book_list.xls"');
		header('Cache-Control: max-age=0');
		$userWriter = PHPExcel_IOFactory::createWriter($userPHPExcel, 'Excel5');
		$userWriter->save('php://output');
	}

	//导出会员列表
	function expuserxls(){
		set_time_limit(0);
		ini_set("memory_limit","50M");
		$sql = "select u.user_name, u.email, u.mobile_phone, c.name as city_name, u.address from ".C("DB_PREFIX")."user as u ".
			   "left outer join ".C("DB_PREFIX")."group_city c on c.id = u.city_id";
		$list = D()->query($sql);

		Vendor('PHPExcel');
		Vendor('PHPExcel.IOFactory');
		$userPHPExcel = new PHPExcel();
		$userPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1','用户名')
					->setCellValue('B1','邮箱')
					->setCellValue('C1','手机');

		$i = 2;
		foreach($list as $user)
		{
			$userPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A$i",$user['user_name'])
					->setCellValue("B$i",$user['email'])
					->setCellValue("C$i",$user['mobile_phone']);
			$i++;
		}

		$userPHPExcel->getActiveSheet()->setTitle('会员列表');
		$userPHPExcel->setActiveSheetIndex(0);

		header('Content-Type:application/vnd.ms-excel;');
		header('Content-Disposition:attachment;filename="user_list.xls"');
		header('Cache-Control: max-age=0');
		$userWriter = PHPExcel_IOFactory::createWriter($userPHPExcel, 'Excel5');
		$userWriter->save('php://output');
	}

	function showdetail(){
		$name=$this->getActionName();
		$model = M ( $name );
		$user_id = $_REQUEST ['user_id'];
		$vo = $model->getById ( $user_id );


		$user_group = D("UserGroup")->field("id, name_".DEFAULT_LANG_ID." as name")->where('id='.$vo['group_id'])->find();
		$vo['group_name'] = $user_group['name'];
		$vo['referrals_count'] = M("User")->where("parent_id=".$vo['id']." and status = 1")->count();
		$this->assign ( 'vo', $vo );
		$time = gmtTime();

		//会员订单
		$sql = 'SELECT a.*, og.data_name, og.rec_id, u.user_name,'.
					' (select sum(og1.number) from '.C("DB_PREFIX").'order_goods og1 where og1.order_id = a.id) as goods_num, '.
					' (select gc.name from '.C("DB_PREFIX").'group_city gc where gc.id = g.city_id) as city_name, '.
					' (a.cost_total_price + a.cost_delivery_fee + a.cost_protect_fee + a.cost_payment_fee + a.cost_other_fee) as order_cost'.
					'  FROM '.C("DB_PREFIX").'order a left join '.C("DB_PREFIX").'user u on u.id = a.user_id '.
					'  left join '.C("DB_PREFIX").'order_goods og on og.order_id = a.id '.
					'  left join '.C("DB_PREFIX").'goods g on g.id = og.rec_id where a.user_id ='.$user_id;
		$list = D()->query($sql);
		$this->assign ( 'order_list', $list );


		//收款单
		$sql =  'SELECT a.*,'.
					'       b.sn as order_sn,'.
					'       u.user_name as user_name,'.
					'       b.order_total_price as  final_amount,'.
					'       c.name_'.DEFAULT_LANG_ID.' as PName,'.
					'       d.name_'.DEFAULT_LANG_ID.' AS AName,'.
					'       og.data_name,'.
					'       og.number'.
					'  FROM '.C("DB_PREFIX").'order_incharge a'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'order b ON b.id = a.order_id'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'payment c ON c.id = a.payment_id'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'currency d ON d.id = a.currency_id'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'user u ON u.id = b.user_id'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'order_goods og ON og.order_id = a.order_id'.
					'  where b.id is not null and b.user_id = '.$user_id.
					' ORDER BY a.create_time desc';
		$list = D()->query($sql);
		$this->assign ( 'order_incharge_list', $list );


		//发货单
		$sql =  'SELECT a.*,'.
					'       b.sn as order_sn,'.
					'       b.order_total_price as final_amount,'.
					'       c.name_'.DEFAULT_LANG_ID.' as fname,'.
					'       d.user_name as mname,'.
					'       ocg.number,'.
					'		(select data_name from '.C("DB_PREFIX").'order_goods og where og.id = ocg.order_goods_id) as data_name'.
					'  FROM '.C("DB_PREFIX").'order_consignment a'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'order b ON a.order_id = b.id'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'delivery c ON a.delivery_id = c.id'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'user d ON b.user_id = d.id'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'order_consignment_goods ocg ON ocg.order_consignment_id = a.id'.
					'  where b.id is not null and b.user_id = '.$user_id.
					' ORDER BY b.sn, a.id';
		$list = D()->query($sql);
		$this->assign ( 'order_consignment_list', $list );

		//用户方维券
		$list = D("GroupBond")->where("user_id = ".$user_id)->order('create_time desc')->findAll();
		//dump(D("GroupBond")->getLastSql());
		foreach($list as $k=>$v)
		{
			$list[$k]['create_time_format'] = toDate($v['create_time'],'Y-m-d');
			$list[$k]['buy_time_format'] = toDate($v['buy_time'],'Y-m-d');
			$list[$k]['use_time_format'] = toDate($v['use_time'],'Y-m-d');
			$list[$k]['end_time_format'] = toDate($v['end_time'],'Y-m-d');

			$list[$k]['end_time_format'] = toDate($v['end_time'],'Y-m-d');

			if(($v['end_time'] > $time || $v['end_time'] == 0) && $v['use_time'] == 0)
				$list[$k]['is_edit'] = 1;

			if(eyooC("IS_SMS") == 1)
				$list[$k]['is_sms'] = 1;

			if ($list[$k]['status'] == 1)
				$list[$k]['status_name'] = '未使用';
			else if ($list[$k]['status'] == 2)
				$list[$k]['status_name'] = '已使用';
			else if ($list[$k]['status'] == 3)
				$list[$k]['status_name'] = '已过期';
		}

		//dump($list);
		$this->assign ( 'groupbond_list', $list );

		//资金日志
		$sql = "select *,'money' as log_type from ".C("DB_PREFIX")."user_money_log where user_id= ".$user_id." order by create_time desc ";

		$list = D()->query($sql);
		foreach($list as $k=>$v)
		{
			if($v['log_type'] == 'money')
			{
				$list[$k]['value'] = formatPrice(abs($v['money']));
			}
			if($v['log_type'] == 'score')
			{
				$list[$k]['value'] = formatScore($v['money']);
			}
			if($v['money']>=0)
			{
				$list[$k]['op_type'] = 0;
			}
			else
			{
				$list[$k]['op_type'] = 1;
			}
		}
		$this->assign ( 'log_money_list', $list );


		//积分日志
		$sql = "select *,'score' as log_type from ".C("DB_PREFIX")."user_score_log where user_id = ".$user_id." order by create_time desc ";

		$list = D()->query($sql);
		foreach($list as $k=>$v)
		{
			if($v['log_type'] == 'money')
			{
				$list[$k]['value'] = formatPrice(abs($v['money']));
			}
			if($v['log_type'] == 'score')
			{
				$list[$k]['value'] = formatScore($v['money']);
			}
			if($v['money']>=0)
			{
				$list[$k]['op_type'] = 0;
			}
			else
			{
				$list[$k]['op_type'] = 1;
			}
		}
		$this->assign ( 'log_score_list', $list );

		//代金券信息
		$sql = "select e.id,e.sn,e.password,e.use_date_time,uu.user_name as use_user_name,et.name,et.use_start_date,et.use_end_date,".
			   " g.name_1 as goods_name,et.money,et.status from ".C("DB_PREFIX")."ecv as e left join ".C("DB_PREFIX")."ecv_type  as et ".
				" on et.id = e.ecv_type left join ".C("DB_PREFIX")."user as uu on uu.id = e.use_user_id left join ".C("DB_PREFIX")."goods as g on g.id = e.goods_id ".
				" where e.user_id = ".$user_id." group by e.id order by e.id desc";

		$list = M()->query($sql);

		foreach($list as $k=>$v)
		{

			$list[$k]['money_format'] = '￥'.floatval($v['money']);
			$list[$k]['use_date_time_format'] = toDate($v['use_date_time'],'Y-m-d H:i');
			$list[$k]['use_end_date_format'] = toDate($v['use_end_date'],'Y-m-d H:i');
			$list[$k]['use_start_date_format'] = toDate($v['use_start_date'],'Y-m-d H:i');
		}
		$this->assign ( 'evc_list', $list );


		//会员充值
		$list = M("UserIncharge")->where("user_id = ".$user_id)->order('create_time desc')->findAll();
		$this->assign ( 'incharge_list', $list );

		//会员取款
		$list = M("UserUncharge")->where("user_id = ".$user_id)->order('create_time desc')->findAll();
		$this->assign ( 'uncharge_list', $list );



		$this->display ();
	}

	//增加会员字段的配置
	public function field_list()
	{
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();

		$model = D ("UserField");
		if (! empty ( $model )) {
			$this->_list ( $model,$map);
		}

		$this->assign("map",$map);
		$this->display ();
		return;
	}
	public function addUserField()
	{
		$this->assign("new_sort",M("UserField")->max("sort")+1);
		$this->display();
	}
	public function insertUserField()
	{
		$model = D ("UserField");
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		if(M("UserField")->where("field_name='".$data['field_name']."'")->count()>0)
		{
			$this->error("字段名称已存在");
		}
		//保存当前数据对象
		$list=$model->add($data);
		if ($list!==false) { //保存成功
			$this->success (L('ADD_SUCCESS'));
		} else {
			//失败提示
			$this->error (L('ADD_FAILED'));
		}
	}
	public function editUserField()
	{
		$model = M ( "UserField" );
		$id = $_REQUEST [$model->getPk ()];
		$vo = $model->getById ( $id );
		$this->assign ( 'vo', $vo );
		$this->display ();
	}
	public function updateUserField() {
		$id = intval($_REQUEST['id']);

		$model = D ( "UserField" );
		$data = $model->create();
		if (false === $data) {
			$this->error ( $model->getError () );
		}
		if($data['type'] != 1)
		{
			$data['val_scope'] = '';
		}

		if(M("UserField")->where("field_name='".$data['field_name']."' and id <>".$data['id'])->count()>0)
		{
			$this->error("字段名称已存在");
		}

		$list=$model->save ($data);
		if (false !== $list) {

			$this->success (L('EDIT_SUCCESS'));
		} else {
			$this->error (L('EDIT_FAILED'));
		}
	}

	public function delUserField()
	{
		$id = intval($_REQUEST['id']);
		M("UserField")->where("id=".$id)->delete();
		M("UserExtend")->where("field_id=".$id)->delete();

		$ajax = intval($_REQUEST['ajax']);
		$this->success("删除成功",$ajax);
	}
}
?>