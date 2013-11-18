<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 在线支付
class PaymentAction extends CommonAction {
	
	/**
	 * 获得所有模块的名称以及链接地址
	 *
	 * @access      public
	 * @param       string      $directory      插件存放的目录
	 * @return      array
	 */
	function read_modules($directory = '.')
	{
	    $dir         = @opendir($directory);
	    $set_modules = true;
	    $modules     = array();
	
	    while (false !== ($file = @readdir($dir)))
	    {
	        if (preg_match("/^.*?\.php$/", $file))
	        {
	            include_once($directory. '/' .$file);
	        }
	    }
	    @closedir($dir);
	    unset($set_modules);
	
	    foreach ($modules AS $key => $value)
	    {
	        ksort($modules[$key]);
	    }
	    ksort($modules);
	
	    return $modules;
	}
		
	public function index(){
		
	    /* 查询数据库中启用的支付方式 */
	    $pay_list = array();
	    $payment_list = D("Payment")->where("status = 1")->field('id,name_'.DEFAULT_LANG_ID.' as name,online_pay,class_name,money')->findAll();
	    foreach($payment_list as $payment){
	    	$pay_list[$payment['class_name']] = $payment;
	    }
    		
		$modules = $this->read_modules(VENDOR_PATH.'payment');
		
		for ($i = 0; $i < count($modules); $i++)
	    {
	    	
	        $code = $modules[$i]['code'];
	        /* 如果数据库中有，取数据库中的名称和描述 */
	        if (isset($pay_list[$code]))
	        {
	        	$modules[$i]['id'] = $pay_list[$code]['id'];;
	            $modules[$i]['name'] = $pay_list[$code]['name'];
	            $modules[$i]['online_pay'] = $pay_list[$code]['online_pay'];
	            $modules[$i]['money'] = $pay_list[$code]['money'];
	            //$modules[$i]['desc'] = $pay_list[$code]['pay_desc'];
	            $modules[$i]['install'] = '1';
	        }
	        else
	        {
	            //$modules[$i]['name'] = $_LANG[$modules[$i]['code']];
	            //$modules[$i]['desc'] = $_LANG[$modules[$i]['desc']];
	            $modules[$i]['install'] = '0';
	        }	

	        $modules[$i]['online_pay_name'] = L('ONLINE_PAY_'.$modules[$i]['online_pay']);
	    }
	    		
		$this->assign('modules', $modules);
		$this->display ();
	}
	
	public function install(){
	    /* 取相应插件信息 */
	    //开始修改列的结构
	    M()->query("ALTER table `".C("DB_PREFIX")."payment` MODIFY config text NOT NULL");
		$model	=	D("Payment");
		
		$set_modules = true;
		    
	    require_once(VENDOR_PATH.'payment/'.$_REQUEST['code'].'Payment.class.php');
		$data = $modules[0];
		    		
		$vo = $model->where("class_name='".$_REQUEST['code']."'")->find();
		//dump($model->getLastSql());
		//dump($vo);
		if ($vo){
			$id = $vo['id'];
			//$vo['status'] = 1; 
			//$model->save($vo);
			M("Payment")->where("id = '$id'")->setField(array('status','online_pay'), array('1',$data['online_pay']));
			//dump(M("Payment")->getLastSql());
		}else{
	        if(false === $vo = $model->create()) {
	        	$this->error($model->getError());
	        }
	
		    $vo['class_name'] = $_REQUEST['code'];
		    $vo['name_'.DEFAULT_LANG_ID] = $data['name'];
		    $vo['online_pay']  = $data['online_pay'];        
			$vo['status']   = 1; 
			$vo['money']   = 0; 
		
	        $id = $model->add($vo);			
		}

        if (intval($id) > 0){
			$this->assign ('jumpUrl', u('Payment/edit', array('id'=>intval($id))));
			$this->success (L('ADD_SUCCESS'));
        }else{
        	$this->error (L('ADD_FAILED'));
        }		        
		
	}
	
/*------------------------------------------------------ */
//-- 卸载支付方式 ?act=uninstall&code={$code}
/*------------------------------------------------------ */
	public function uninstall(){
	    M("Payment")->where("id = ".intval($_REQUEST['id']))->setField('status', 0);
		$this->success (L('EDIT_SUCCESS'));
	}
	
	function getPayment(){
		$id = intval($_REQUEST ['id']);
		$model = D ("Payment");
		$vo = $model->getById ($id);			
		echo json_encode($vo);
	}
		
	function getPaymentList(){
		$currency_id= intval($_REQUEST ['currency_id']);
		$model = D ("Payment");
		if (empty($_REQUEST ['status'])){
			$voList = $model->field('id,name_'.DEFAULT_LANG_ID.' as name')->where("currency=".$currency_id)->findAll();
		}else{
			$status = intval($_REQUEST ['status']);
			$voList = $model->field('id,name_'.DEFAULT_LANG_ID.' as name')->where("currency=".$currency_id." and status =".$status)->findAll();
		}
		
		echo json_encode($voList);
	}	
	
	
	public function edit()
	{
		$model = M ("Payment");
		$id = $_REQUEST ['id'];
		//dump($id);
		
		$vo = $model->getById ( $id );

		$vo['config'] = unserialize($vo['config']);	
	
		$payment_model = $vo['class_name']."Payment";
		require_once(VENDOR_PATH.'payment/'.$vo['class_name'].'Payment.class.php');
		//dump(VENDOR_PATH.'payment/'.$vo['class_name'].'Payment.class.php');
		$payment_model = $vo['class_name']."Payment";
		$current_model = new $payment_model;
		//dump($current_model);
		foreach($vo['config'] as $k=>$item)
		{
			$current_model->config[$k] = $item;
		}
		$vo['config'] = $current_model->config;
		if($vo['fee_type']==0)
		$vo['fee'] = getBaseMoney($vo['fee'],$vo['currency']);
		
		if($vo['cost_fee_type']==0)
		$vo['cost_fee'] = getBaseMoney($vo['cost_fee'],$vo['currency']);
		
		$this->assign ( 'vo', $vo );
		
		$currency_list = D("Currency")->findAll();
		$default_lang_id = D("LangConf")->where("lang_name='".C('DEFAULT_LANG')."'")->getField("id");
		foreach($currency_list as $k=>$v)
		{
			$currency_list[$k]['name'] = $v['name_'.$default_lang_id];
		}
		$this->assign('currency_list',$currency_list);
		
		$this->display ();
	}
	
	public function update()
	{
		$payment_model = D("Payment");
		if (!$payment_model->create()){
			$this->error($payment_model->getError());
		}
		$data = $_POST;
		
		$id = intval($_REQUEST['id']);
		$o_data = D("Payment")->getById($id);  //原来的用户数据
		
		$upload_list = $this->uploadFile(0,"public");
	
		if($upload_list)
		{			
			$map[$payment_model->getPk()] = $_REQUEST[$payment_model->getPk()];
			$item = $payment_model->where($map)->find();
			@unlink($this->getRealPath().$item['logo']);
			$data['logo'] = $upload_list[0]['recpath'].$upload_list[0]['savename'];
		}
		
		$rs = $payment_model->save($data);
		
		//add by chenfq 2010-06-5  记录帐户资金变化明细 begin
		if ($rs){
			$money = floatval($_POST['money']) - floatval($o_data['money']);
			if($money !=0 ){
				payment_money_log($id, 
								  $_SESSION[C('USER_AUTH_KEY')], 
								  $id, 
								  'Payment', 
								  $money, 
								  $_SESSION['adm_name'].'管理员后台“调整”金额：'.$money, 
								  true, 
								  'Admin', 
								  '', 
								  $_SESSION['adm_name']);				
			}
		}
		//add by chenfq 2010-06-5  记录帐户资金变化明细 end
		
		$this->success (L('EDIT_SUCCESS'));		
	}
	public function forbid() {
		$id = $_REQUEST ['id'];	
		$sql_str = "update ".C("DB_PREFIX")."payment set status = 0 where id=".$id ;
		$list = D("Payment")->query($sql_str);
		if ($list!==false) {			
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( L('FORBID_SUCCESS') ,1);
		} else {
			$this->error  (  L('FORBID_FAILED') ,1);
		}
	}
	
	function resume() {
		$id = $_REQUEST ['id'];
		$sql_str = "update ".C("DB_PREFIX")."payment set status = 1 where id=".$id ;
		$list = D("Payment")->query($sql_str);
		if ($list!==false) {
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( L('RESUME_SUCCESS') ,1);
			
		} else {
			$this->error ( L('RESUME_FAILED') ,1);
		}
	}
	
	public function deleteLogo()
	{
		$id = $_REQUEST ['id'];
		$item = D("Payment")->getById($id);
		$item['logo'] = "";
		
		if(D("Payment")->save($item))
		{
			@unlink($this->getRealPath().$item['logo']);
			$this->success (L('EDIT_SUCCESS'));	
		}
		else
			$this->error(L('EDIT_FAILED'));
	}
	
	public function money_log()
	{
		//列表过滤器，生成查询Map对象
		$map = array ();
		$map['payment_id'] = intval($_REQUEST['payment_id']);
		$map['operator_name'] = $_REQUEST['operator_name'];
		$map['begin_create_time'] = $_REQUEST['begin_create_time'];
		$map['end_create_time'] = $_REQUEST['end_create_time'];
		
		$this->assign ( 'payment_id', $map['payment_id'] );
		$this->assign ( 'operator_name', $map['operator_name'] );
		$this->assign ( 'begin_create_time', $map['begin_create_time'] );
		$this->assign ( 'end_create_time', $map['end_create_time'] );
				
		$payment_list = D("Payment")->findAll();
		$this->assign ( 'payment_list', $payment_list);
		
		
		$begin_create_time = ! empty ($map['begin_create_time']) ? localStrToTime($map['begin_create_time']) : 0;
		$end_create_time = ! empty ($map['end_create_time']) ? localStrToTime($map['end_create_time']) : 0;
		
		
		
		
		
		$sum_money = 0;
		
		//资金管理
		$sql = "select a.* from ".C("DB_PREFIX")."payment_money_log a".
			   " where a.money <> 0  ";
		
		$sql_str = "select sum(a.money) as money from ".C("DB_PREFIX")."payment_money_log a".
			   " where a.money <> 0 ";
					
		
		if ((!empty($map['operator_name'])) && ($map['operator_name'] <>'')){
			$sql .= " and a.operator_name like '%".$map['operator_name']."%'";
			
			$sql_str .= " and a.operator_name like '%".$map['operator_name']."%'";
		}
				
		if ((!empty($map['payment_id'])) && ($map['payment_id'] >0)){
			$sql .= " and a.payment_id = '".$map['payment_id']."'";
			
			$sql_str .= " and a.payment_id = '".$map['payment_id']."'";
		}

		if ($begin_create_time > 0){
			$sql .= " and a.create_time >= '".$begin_create_time."'";
			
			$sql_str .= " and a.create_time < '".$begin_create_time."'";
		}
		
		if ($end_create_time > 0){
			$sql .= " and a.create_time <= '".$end_create_time."'";
		}		
		
		if ($begin_create_time > 0){
			//dump($sql_str);
			$tmp = M()->query($sql_str);
			$sum_money = $tmp[0]['money'];
		}
		
		$this->assign ( 'sum_money', formatPrice($sum_money));
			
		
		//dump($sql_str);
        $model = D($this->name);		
        $voList = $this->_Sql_list($model, $sql, $parameter, 'create_time', true);
        
		foreach($voList as $k=>$v)
		{
			$sum_money = $sum_money + $v['money'];
			if($v['money']>=0)
			{
				//$list[$k]['dec_money'] = '';
				$voList[$k]['inc_money'] = formatPrice(abs($v['money']));
			}
			else
			{
				//$list[$k]['inc_money'] = '';
				$voList[$k]['dec_money'] = formatPrice(abs($v['money']));
			}
			
			$voList[$k]['sum_money'] = formatPrice($sum_money);
		}		
		$this->assign ( 'money_log_list', $voList);
				
		
		$this->display ();
		return;		
	}	

	public function payment_log()
	{
		$page = intval($_REQUEST['p'])==0?1:intval($_REQUEST['p']);
		$limit = ($page-1)*C("PAGE_LISTROWS").",".C("PAGE_LISTROWS");
		$payment_id = intval($_REQUEST['payment_id']);
		$this->assign('payment_id',$payment_id);
		$payment_log_sn = $_REQUEST['payment_log_sn'];
		$order_sn = $_REQUEST['order_sn'];
		$user_name = isset($_REQUEST['user_name'])?$_REQUEST['user_name']:'';
		$uid = intval(M("User")->where("user_name='$user_name'")->getField("id"));
		$group_id = intval($_REQUEST['group_id']);
		if(!isset($_REQUEST['is_paid']))
		$is_paid = -1;
		else
		$is_paid = intval($_REQUEST['is_paid']);
		$this->assign('is_paid',$is_paid);
		$payment_vo = M("Payment")->getById($payment_id);
		$payment_vo['config'] = unserialize($payment_vo['config']);
		if($payment_vo['class_name']=='Alipay')
			$idstr = "concat('fw123456', a.id)";
		else
			$idstr = "(a.id)";
			
		if($payment_vo['class_name']=='TenpayBank'||$payment_vo['class_name']=='Tencentpay')
		{
			if($payment_log_sn&&$payment_log_sn!='')
			{
				$prefix = $payment_vo['config']['tencentpay_id']."00000000";
				$payment_log_sn = intval(substr($payment_log_sn,strlen($prefix)));
			}
		}

			
		$begin_create_time = ! empty ($_REQUEST['begin_create_time']) ? localStrToTime($map['begin_create_time']) : 0;
		$end_create_time = ! empty ($_REQUEST['end_create_time']) ? localStrToTime($map['end_create_time']) : 0;

		$create_time_where  = "";
		if($begin_create_time>0&&$end_create_time==0)
		{
			$create_time_where  = "and o.create_time >= ".$begin_create_time.", ";
		}
		if($begin_create_time==0&&$end_create_time>0)
		{
			$create_time_where  = "and o.create_time <= ".$end_create_time.", ";
		}
		if($begin_create_time>0&&$end_create_time>0)
		{
			$create_time_where  = "and o.create_time between($begin_create_time,$end_create_time), ";
		}
		
		$sql = "select ".$idstr." as 'payment_log_sn',".
			   "o.create_time as 'payment_order_time',".
		       "o.sn as 'payment_order_sn',".
		       "g.data_name as 'payment_goods_name',".
		       "o.order_total_price as 'payment_order_price',".
		       "o.order_incharge as 'payment_incharge_money',".
		       "o.money_status as 'payment_money_status', ".
		       "(select u.user_name from ".C("DB_PREFIX")."user as u where u.id = o.user_id) as 'payment_user_name',".
		       "a.is_paid as 'payment_is_paid', ".
			   "a.create_time, ".
		       "a.money, ".
			   "a.id ".
		   	   "from ".C("DB_PREFIX")."payment_log as a ".
		   	   "left outer join ".C("DB_PREFIX")."order o on o.id = a.rec_id ".
		   	   "left outer join ".C("DB_PREFIX")."order_goods g on g.order_id = a.rec_id ".
		       "where a.payment_id = $payment_id ".
		       "and a.rec_module = 'Order' ".
		       "and o.id is not null ";
		if($is_paid!=-1)
		       $sql.=" and a.is_paid = $is_paid ";
		if($order_sn&&$order_sn!='')
				$sql.=" and o.sn = '$order_sn' ";
		if($uid!=0)
				$sql.=" and o.user_id = $uid ";
		if($group_id!=0)
				$sql.=" and g.rec_id = $group_id ";
			   $sql.=$create_time_where.
			   "and $idstr like '%".$payment_log_sn."%' limit ".$limit;
		
		$list = M()->query($sql);
		
		foreach($list as $k=>$v)
		{
			if($payment_vo['class_name']=='TenpayBank'||$payment_vo['class_name']=='Tencentpay')
			{				 
				 $today = toDate($v['create_time'],'Ymd');
		         /* 将商户号+年月日+流水号 */
		         $bill_no = str_pad($v['id'], 10, 0, STR_PAD_LEFT);
		         $list[$k]['payment_log_sn'] = $payment_vo['config']['tencentpay_id'].$today.$bill_no;			
			}
		}
		$this->assign("list",$list);
		
		$sql_total = "select count(*) as total_count from ".C("DB_PREFIX")."payment_log as a ".
		   	   "left outer join ".C("DB_PREFIX")."order o on o.id = a.rec_id ".
		   	   "left outer join ".C("DB_PREFIX")."order_goods g on g.order_id = a.rec_id ".
		       "where a.payment_id = $payment_id ".
		       "and a.rec_module = 'Order' ".
		       "and o.id is not null ";
		 if($is_paid!=-1)
		       $sql_total.=" and a.is_paid = $is_paid ";
		 if($order_sn&&$order_sn!='')
				$sql_total.=" and o.sn = '$order_sn' ";
		 if($uid!=0)
				$sql_total.=" and o.user_id = $uid ";
		if($group_id!=0)
				$sql_total.=" and g.rec_id = $group_id ";
			   $sql_total.=$create_time_where.
			   "and $idstr like '%".$payment_log_sn."%'";
		$total = M()->query($sql_total);
		
		$total = $total[0]['total_count'];
		
		//分页
		$page = new Page($total,C("PAGE_LISTROWS"));   //初始化分页对象 		
		$p  =  $page->show();
		
        $this->assign('pages',$p);
        //end 分页  

        $this->display();
	}
	
	public function clearlog()
	{
		$payment_id = intval($_REQUEST['payment_id']);
		$sql = "select a.id as payment_log_id from ".C("DB_PREFIX")."payment_log as a ".
		   	   "left outer join ".C("DB_PREFIX")."order as o on o.id = a.rec_id ".
		   	   "left outer join ".C("DB_PREFIX")."order_goods as og on og.order_id = a.rec_id ".
			   "left outer join ".C("DB_PREFIX")."goods as g on og.rec_id = g.id ".
		       "where a.payment_id = $payment_id ".
		       "and a.rec_module = 'Order' ".
		       "and o.id is not null ".
		       "and a.is_paid = 0 and (g.id is null or g.promote_end_time<".gmtTime()." )";

		$payment_id_list = M()->query($sql);
		$ids_array = array();
		foreach($payment_id_list as $row)
		{
			array_push($ids_array,$row['payment_log_id']);
		}
		if(count($ids_array)>0)
		$ids = implode(",",$ids_array);
		else
		$ids = 0;
		M()->execute("delete from ".C("DB_PREFIX")."payment_log where id in($ids)");
		
		$this->success("清空成功");
	}
	public function deleteLog()
	{
		$id = intval($_REQUEST['id']);
		$rs = M("PaymentLog")->where("id=".$id)->delete();
		if($rs)
		$this->success("删除成功");
		else
		$this->error("删除失败");
		
	}
}
?>