<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 订单
class OrderAction extends CommonAction{
	public function index() {
		/*$goods_id = empty($_REQUEST['goods_id']) ?  0 : intval($_REQUEST['goods_id']);
		$money_status  = $_REQUEST['money_status'];
		$goods_status  = $_REQUEST['goods_status'];
		$status  = $_REQUEST['status'];
		$sn  = $_REQUEST['sn'];
		
		$where = " o.id is not null";
		if($money_status != -1)
			$where .= " and o.money_status = '$money_status'";
		
		if($goods_status != -1)
			$where .= " and o.goods_status = '$goods_status'";
			
		if($status != -1)
			$where .= " and o.status = '$status'";
			
		if($goods_id > 0)
		{
			$sql = "select o.*,og.rec_id from ".C("DB_PREFIX")."order as o left join ".C("DB_PREFIX")."order_goods  as og on og.order_id = o.id where og.rec_id = '$goods_id'  ";
		}
		else
		{
			
		}*/
		
		$map = array ();
		$map['user_name'] = $_REQUEST['user_name'];
		$map['goods_id'] = $_REQUEST['goods_id'];
		$map['city_id'] = $_REQUEST['city_id'];
		$map['sn'] = $_REQUEST['sn'];
		if(!isset($_REQUEST['repay_status']))
		{
			$map['repay_status'] = -1;
		}
		else
		{
			$map['repay_status']  = intval($_REQUEST['repay_status']);
		}
		
		if (!isset($_REQUEST['money_status']))
			$map['money_status'] = -1;
		else	
			$map['money_status']  = $_REQUEST['money_status'];
		

		if (!isset($_REQUEST['goods_status']))
			$map['goods_status'] = -1;
		else	
			$map['goods_status']  = $_REQUEST['goods_status'];			
		
		$user_name  = $_REQUEST['user_name'];
		$goods_id = D("Goods")->where(array("id"=>intval($_REQUEST['goods_id']),"city_id"=>array("in",$_SESSION['admin_city_ids'])))->getField("id");
		$city_id  = $_REQUEST['city_id'];
		if (intval($city_id) > 0)
		{
			if(!in_array($city_id,$_SESSION['admin_city_ids']))
			{
				$this->error("不能搜索其他地区的团购订单");
			}
		}
		//$city_id =  D("GroupCity")->where(array("id"=>intval($_REQUEST['city_id']),"id"=>array("in",$_SESSION['admin_city_ids'])))->getField("id");
		$order_sn = $_REQUEST['sn'];
		$money_status = $map['money_status'];
		$goods_status = $map['goods_status'];
		$repay_status = $map['repay_status'];
		
		$sql_str = 'SELECT a.id,a.sn,a.create_time,a.order_total_price,a.order_incharge,a.money_status,a.goods_status,a.repay_status, og.data_name, u.user_name,'.
					' (select sum(og1.number) from '.C("DB_PREFIX").'order_goods og1 where og1.order_id = a.id) as goods_num, '.
					' (select gc.name from '.C("DB_PREFIX").'group_city gc where gc.id = g.city_id) as city_name, '.
					' (a.cost_total_price + a.cost_delivery_fee + a.cost_protect_fee + a.cost_payment_fee + a.cost_other_fee) as order_cost'.
					'  FROM '.C("DB_PREFIX").'order a left join '.C("DB_PREFIX").'user u on u.id = a.user_id '.
					'  left join '.C("DB_PREFIX").'order_goods og on og.order_id = a.id '.
					'  left join '.C("DB_PREFIX").'goods g on g.id = og.rec_id where 1 = 1';
		
		if ((!empty($user_name)) && ($user_name <>'')){
			$sql_str .= " and u.user_name like '%".$user_name."%'";
		}
		
		if (intval($repay_status) > 0){
			$sql_str .= " and a.repay_status = '".intval($repay_status)."'";
		}
		
		if (intval($goods_id) > 0){
			$sql_str .= " and og.rec_id = '".intval($goods_id)."'";
		}
		
		if (intval($city_id) > 0){
			$sql_str .= " and g.city_id = '".intval($city_id)."'";
		}
		else 
		{
			if(!$_SESSION['all_city'])
			$sql_str .= " and g.city_id in (".implode(",",$_SESSION['admin_city_ids']).")";
		}
				
		if ((!empty($order_sn)) && ($order_sn <>'')){
			$sql_str.= " and a.sn='".$order_sn."'";
		}
		
		
		if ((intval($money_status) >= 0)){
			$sql_str .= " and a.money_status = '".intval($money_status)."'";
		
		}		
		
		if ((intval($goods_status) >= 0)){
			$sql_str .= " and a.goods_status = '".intval($goods_status)."'";
		}	

		
		//城市	
		
		$city_list = D("GroupCity")->where(array("status"=>1,"id"=>array("in",$_SESSION['admin_city_ids'])))->order("is_defalut desc,id asc")->findAll();
		$this->assign("city_list",$city_list);		
		
		//团购商品列表
		
		$goods_list = D("Goods")->where(array("city_id"=>array("in",$_SESSION['admin_city_ids'])))->field("id, name_".DEFAULT_LANG_ID." as name")->order("sort asc")->findAll();
		$this->assign("goods_list",$goods_list);		
		
		$this->assign ( 'user_name', $user_name );
		$this->assign ( 'goods_id', $goods_id );
		$this->assign ( 'city_id', $city_id );
		$this->assign(	'order_sn',$order_sn);
		$this->assign(	'money_status',$money_status);
		$this->assign(	'goods_status',$goods_status);
		$this->assign('repay_status',$repay_status);
		
		//dump($sql_str);
         //创建数据对象
         
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}		
		
		//dump($parameter);
        $model = D($this->name);		
        $voList = $this->_Sql_list($model, $sql_str, "&".$parameter, 'create_time', false);

        //总利润，订单数，
        $total_count = 0;
        $total_profit = 0.0;
        $total_cost = 0.0;
        foreach ($voList as $key=>$vo){
        	$total_count += 1;
        	$total_cost += $voList[$key]['order_cost'];
        	$total_profit += $voList[$key]['order_profit'];
        }
	
        $this->assign ( 'total_count', $total_count );
        $this->assign ( 'total_cost', $total_cost );
        $this->assign ( 'total_profit', $total_profit );
        
		$this->display ();
		return;
	}

	//获得会员地址列表
	function getUserConsignee(){
		$UserConsignee_id = $_REQUEST ['UserConsignee_id'];//$_REQUEST ['memberAddress_id'];
		if (empty($UserConsignee_id)){
			$user_name = $_REQUEST ['user_name'];
			$user_name = json_decode($user_name);
			$User = D ( "User" );
			$sql_str = "select id from ".C("DB_PREFIX")."user a where user_name = '".$user_name."' limit 1";
	        $rs = $User->query($sql_str, false);
	        $user_id = $rs[0]['id'];
		        //dump($id);
			if (!empty($user_id)){
	        	$UserConsigneeList = $User->getUserConsignee($user_id);
				//dump($MemberAddressList);
				if (empty($UserConsigneeList)){
					echo json_encode($user_id);
				}else{
					echo json_encode($UserConsigneeList);
				}
			}else{
				echo json_encode("-1");
			}	        
		}else{
			$model = D ( "UserConsignee" );
			$vo = $model->getById ( $UserConsignee_id );			
			echo json_encode($vo);
		}
	}	
	
	//获取地区列表; 国家，省，地区/市，县/区
	public function getRegion3(){
		$pid = intval($_REQUEST ['pid']);
		$RegionConf = D ( "RegionConf" );
		$RegionList = $RegionConf->where("pid = ".$pid)->field("id,name")->findAll();
		//echo $RegionConf->getLastSql(); exit;
		echo json_encode($RegionList); 
	}	
	
	public function getChildData(){
		if($_REQUEST['ids']!='')
		{
			$ids = json_decode($_REQUEST['ids']);
			
			$LANG_ID = intval($_REQUEST['lang_conf_id']);
			if ($LANG_ID == 0){
				$LANG_ID = DEFAULT_LANG_ID;
			}
			
			$ids = implode(",",$ids);
			if (empty($ids)){
				$ids = "-1";
			}
			
			$sql_str = 'SELECT GoodsSpec.id AS id,'.
						'       GoodsSpec.sn AS goods_sn,'.
						'       GoodsSpec.goods_id AS goods_id,'.
						'       GoodsSpec.spec1_type_id AS spec1_type_id,'.
						'       GoodsSpec.spec1_id AS spec1_id,'.
						'       GoodsSpec.spec2_type_id AS spec2_type_id,'.
						'       GoodsSpec.spec2_id AS spec2_id,'.
						'       GoodsSpec.shop_price AS shop_price,'.
						'       GoodsSpec.cost_price AS cost_price,'.
						'       GoodsSpec.stock AS goods_stock,'.
						'       GoodsSpec.weight AS goods_weight,'.
						'       Goods.name_'.$LANG_ID.' AS goods_name,'.
						'       Goods.brand_id AS brand_id,'.
						'       Goods.cate_id AS cate_id,'.
						'       Goods.status AS status,'.
						'       Goods.score AS score,'.
						'       GoodsSpecDetail_A.spec_name_'.$LANG_ID.' AS spec_name1,'.
						'       GoodsSpecDetail_B.spec_name_'.$LANG_ID.' AS spec_name2,'.
						'       concat(CASE'.
						'                WHEN GoodsSpecDetail_A.spec_name_'.$LANG_ID.' IS NULL THEN'.
						'                 \'\''.
						'                ELSE'.
						'                 GoodsSpecDetail_A.spec_name_'.$LANG_ID.''.
						'              END,'.
						'              CASE'.
						'                WHEN GoodsSpecDetail_b.spec_name_'.$LANG_ID.' IS NULL THEN'.
						'                 \'\''.
						'                ELSE'.
						'                 concat(\'、\', GoodsSpecDetail_b.spec_name_'.$LANG_ID.')'.
						'              END) as specname'.
						'  FROM '.C("DB_PREFIX").'goods_spec_item GoodsSpec'.
						'  LEFT OUTER JOIN '.C("DB_PREFIX").'goods Goods ON Goods.id = GoodsSpec.goods_id'.
						'  LEFT OUTER JOIN '.C("DB_PREFIX").'goods_spec GoodsSpecDetail_A ON GoodsSpecDetail_A.id ='.
						'                                                        GoodsSpec.spec1_id'.
						'  LEFT OUTER JOIN '.C("DB_PREFIX").'goods_spec GoodsSpecDetail_B ON GoodsSpecDetail_B.id ='.
						'                                                        GoodsSpec.spec2_id'.
						' WHERE GoodsSpec.id in ('.$ids.')';
			
			
	         //创建数据对象
	        $model = D("Order");		
	        $voList = $model->query($sql_str);
	        foreach($voList as $k=>$vo){
	        	if (empty($vo['specname']))
	        	{
	        		$voList[$k]['specname'] = $vo['goods_name'];
	        	}else{
	        		$voList[$k]['specname'] = $vo['goods_name'].'('.$vo['specname'].')';
	        	}	
	        }
			echo json_encode($voList);
		}		
	}
		
	function commAssign(&$order, $calc_radio){
		$id = intval($order['id']);
		
		//货币
		$Currency = D ( "Currency" );
		$CurrencyList = $Currency->field('id,name_'.DEFAULT_LANG_ID.' as name')->findAll();
		$this->assign ( "currencyList", $CurrencyList );
		
		//支付方式
		$PaymentMode = D ( "Payment" );
		$PaymentModeList = $PaymentMode->field('id,name_'.DEFAULT_LANG_ID.' as name')->findAll();
		$this->assign ( "paymentModeList", $PaymentModeList );		
	
		//配送方式
		$FreightMode = D ( "Delivery" );
		$FreightModeList = $FreightMode->field('id,name_'.DEFAULT_LANG_ID.' as name')->findAll();
		$this->assign ( "freightModeList", $FreightModeList );

		//地区：国家
		$RegionConf = D ( "RegionConf" );
		$region_lv1List = $RegionConf->where("pid = 0")->field("id,name")->findAll();
		//dump($region_lv1List);
		$this->assign ( "region_lv1List", $region_lv1List);
				
		//语言列表
		$lang_envs = D("LangConf")->field("id, show_name as name")->findAll();
		$this->assign ( "langList", $lang_envs);
		
		//会员地址列表
		$User = D ( "User" );
		$UserConsigneeList = $User->getUserConsignee(intval($order['user_id']));
		$this->assign ( "userConsigneeList", json_encode($UserConsigneeList));		
		
		$this->assign ( 'user', $User->getById(intval($order['user_id'])));
				
		
		if ($id != 0){
			$OrderGoods = D ( "OrderGoods" );
			$OrderGoodsList = $OrderGoods->where('order_id='.intval($order['id']))->findAll();
			$this->assign ( "orderGoodsList", $OrderGoodsList);	
			//dump($OrderGoodsList);
			
			$lc_vo = D("LangConf")->field("id, show_name as name")->getById(intval($order['lang_conf_id']));
			$order['lang_name'] = $lc_vo['name'];
						
			$fm_vo = $FreightMode->field('id,name_'.DEFAULT_LANG_ID.' as name')->getById(intval($order['delivery']));
			$order['delivery_name'] = $fm_vo['name'];
			
			$cu_vo = $Currency->field('id,name_'.DEFAULT_LANG_ID.' as name')->getById(intval($order['currency_id']));
			$order['currency_name'] = $cu_vo['name'];			
			
			
			$pm_vo = $PaymentMode->field('id,name_'.DEFAULT_LANG_ID.' as name')->getById(intval($order['payment']));
			$order['payment_name'] = $pm_vo['name'];
			//$order['payment_fee'] = $pm_vo['fee'];
			
			if ($calc_radio == 1){
		         //商品总金额
		         $order['total_price'] = round($order['total_price'] * $order['currency_radio'], 3);
		         //配送费用
		         $order['delivery_fee'] = round($order['delivery_fee'] * $order['currency_radio'], 3);
		         //保价
		         $order['protect_fee'] = round($order['protect_fee'] * $order['currency_radio'], 3);
		         //促销优惠金额
		         $order['promote_money'] = round($order['promote_money'] * $order['currency_radio'], 3);
		         //税金
		         $order['tax_money'] = round($order['tax_money'] * $order['currency_radio'], 3); 
		         //支付手续费
		         $order['payment_fee'] = round($order['payment_fee'] * $order['currency_radio'], 3);         
		         //订单总金额
		         $order['order_total_price'] = round($order['order_total_price'] * $order['currency_radio'], 3); 					
			}
		}

		$order['money_status_name'] = L("ORDER_MONEY_STATUS_".intval($order['money_status']));
		$order['goods_status_name'] = L("ORDER_GOODS_STATUS_".intval($order['goods_status']));
		$order['status_name'] = L("ORDER_STATUS_".intval($order['status']));
		
	}
		
	function add() {
		$this->edit();
	}
		
    	
	public function edit() {
		$id = intval($_REQUEST['id']);
		
		$model = D("Order");
		if ($id == 0){//新增
			$this->assign ( 'isNew', json_encode(true) );
			$vo = $model->create();
			$vo['id'] = 0;		
			//下单时间
			$vo ['create_time'] = toDate(gmtTime());		
			//订单号
			$vo ['sn'] = toDate(gmtTime(), 'Ymdhis');
			//送货日期 任意日期
			$vo ['money_status'] = 0;
			$vo ['goods_status'] = 0;
			$vo ['region_lv1'] = -1;
			$vo ['region_lv2'] = -1;
			$vo ['region_lv3'] = -1;
			$vo ['region_lv4'] = -1;
			$vo ['currency_radio'] = 1;		
		}else{//编辑
			$this->assign ( 'isNew', json_encode(false) );
			$vo = $model->getById ( $id );
			$vo ['create_time'] = toDate($vo ['create_time']);
		}
		
		$this->commAssign($vo, 1);
		
		$this->assign ( 'vo', $vo );
		//dump($vo);
		$this->display ( 'edit' );		
	}

	public function show(){
		
		$id = intval($_REQUEST['id']);
		
		$model = D("Order");
				
		$vo = $model->getById ( $id );
		if(!$_SESSION['all_city'])
		{
			$goods_id = M("OrderGoods")->where("order_id=".$id)->getField("rec_id");
			$c_id = M("Goods")->where("id=".$goods_id)->getField("city_id");
			if(!in_array($c_id,$_SESSION['admin_city_ids']))
			$this->error("不能查看其他地区的订单");
		}
		$vo ['create_time'] = toDate($vo ['create_time']);
		
		$this->commAssign($vo, 0);
		
		$RegionConf = D ( "RegionConf" );
		$region_lv = $RegionConf->getById($vo['region_lv2']);
		$address = "";
		if ($region_lv){
			$address = $region_lv['name'];
		}		
		
		$region_lv = $RegionConf->getById($vo['region_lv3']);
		if ($region_lv){
			$address = $address.$region_lv['name'];
		}		
		
		$region_lv = $RegionConf->getById($vo['region_lv4']);
		if ($region_lv){
			$address = $address.$region_lv['name'];
		}		
		
		if (!empty($address)){
			$vo['address'] = $address.$vo['address'];
		}
		
		$this->assign ( 'vo', $vo );
		
		//收款单
		$sql_str =  'SELECT a.*,'.
					'       b.sn as ORDER_SN,'.
					'       b.order_total_price as  final_amount,'.
					'       c.name_'.DEFAULT_LANG_ID.' as PName,'.
					'       d.name_'.DEFAULT_LANG_ID.' AS AName'.
					'  FROM '.C("DB_PREFIX").'order_incharge a'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'order b ON b.id = a.order_id'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'payment c ON c.id = a.payment_id'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'currency d ON d.id = a.currency_id'.
					'  where b.id ='.$id.
					' ORDER BY a.create_time desc';
		
		$order_incharge_list = $model->query($sql_str);
		$this->assign ( 'order_incharge_list', $order_incharge_list );
		
		//退款单
		$sql_str =  'SELECT a.*,'.
					'       b.sn as ORDER_SN,'.
					'       b.order_total_price as  final_amount,'.
					//'       c.name_'.DEFAULT_LANG_ID.' as PName, c.class_name,'.
						'       CASE'.
						'          WHEN c.class_name = \'Accountpay\' THEN'.
						'            \'退到用户帐户(余额支付)\''.					
						'          ELSE'.
						'       	c.name_'.DEFAULT_LANG_ID.''.
						//'            \'不退到用户帐户\''.
						'       END as PName,'.		
					'       d.name_'.DEFAULT_LANG_ID.' AS AName'.
					'  FROM '.C("DB_PREFIX").'order_uncharge a'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'order b ON b.id = A.order_id'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'payment c ON c.id = a.payment_id'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'currency d ON d.id = a.currency_id'.
					'  where b.id ='.$id.
					' ORDER BY a.create_time desc';		
		$order_uncharge_list = $model->query($sql_str);
		$this->assign ( 'order_uncharge_list', $order_uncharge_list );

		//发货单
		$sql_str =  'SELECT a.*,'.
					'       b.sn as order_sn,'.
					'       b.order_total_price as final_amount,'.
					'       c.name_'.DEFAULT_LANG_ID.' as fname,'.
					'       d.user_name as mname'.
					'  FROM '.C("DB_PREFIX").'order_consignment a'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'order b ON a.order_id = b.id'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'delivery c ON a.delivery_id = c.id'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'user d ON b.user_id = d.id'.
					'  where b.id ='.$id.
					' ORDER BY a.create_time desc';
		$order_consignment_list = $model->query($sql_str);
		$this->assign ( 'order_consignment_list', $order_consignment_list );
				
		//退货单
		$sql_str =  'SELECT A.*,'.
					'       b.sn as order_sn,'.
					'       b.order_total_price as final_amount,'.
					'       c.name_'.DEFAULT_LANG_ID.' as fname,'.
					'       d.user_name as mname'.
					'  FROM '.C("DB_PREFIX").'order_re_consignment a'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'order b ON a.order_id = b.id'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'delivery c ON a.delivery_id = c.id'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'user d ON b.user_id = d.id'.
					'  where b.id ='.$id.
					' ORDER BY b.sn, a.id';
		$order_re_consignment_list = $model->query($sql_str);
		$this->assign ( 'order_re_consignment_list', $order_re_consignment_list );	

		//优惠方式
		$sql_str =  'SELECT a.*'.
					'  FROM '.C("DB_PREFIX").'order_promote A'.
					'  where a.order_id ='.$id.
					' ORDER BY a.priority desc';
		$order_promote_list = $model->query($sql_str);
		$this->assign ( 'order_promote_list', $order_promote_list );
				
		//begin add by chenfq 2010-04-07 线下订单完成操作
		$offlinecomplete = 0;
		
		if ($vo['offline'] == 1 && $vo['money_status'] == 2){ //全额支付
			//团购已经完成 is_group_fail: 0; 1: 失败；2：成功
			$sql_str =  'SELECT count(*) as num'.
						'  FROM '.C("DB_PREFIX").'goods a '.
						'where a.is_group_fail <> 2 and a.id in (select rec_id from '.C("DB_PREFIX").'order_goods where order_id = '.$id.')';
			$num = $model->query($sql_str);
			$num = intval($num[0]['num']);
			
			if ($num == 0){
				$offlinecomplete = 1;
			}
		}
		
		$this->assign ( 'offlinecomplete', $offlinecomplete );
		
		$goods_type_sql = "select g.type_id from ".C("DB_PREFIX")."goods as g left join ".
						  C("DB_PREFIX")."order_goods as og on g.id=og.rec_id left join ".
						  C("DB_PREFIX")."order as o on o.id = og.order_id where o.id = ".$id;
		$res = M()->query($goods_type_sql);		
		$type_id = $res[0]['type_id'];
		
		$this->assign('type_id',$type_id);
		//end add by chenfq 2010-04-07 线下订单完成操作
		
		
		//订单留言列表
		$sql_str =  'SELECT A.*, '.
						'       CASE'.
						'          WHEN a.rec_module = \'Order\' THEN'.
						'            \'订单留言\''.
						'          WHEN a.rec_module = \'Payment\' THEN'.
						'            \'我已付款\''.
						'          WHEN a.rec_module = \'OrderReConsignment\' THEN'.
						'            \'退款申请\''.
						'          WHEN a.rec_module = \'OrderUncharge\' THEN'.
						'            \'退款申请\''.						
						'          ELSE'.
						'            \'未知\''.
						'       END as type_name'.		
					'  FROM '.C("DB_PREFIX").'message a'.
					'  where a.rec_module in (\'Order\',\'Payment\',\'OrderReConsignment\',\'OrderUncharge\') and a.user_id = '. intval($vo['user_id']).' and a.rec_id ='.$id.
					' ORDER BY a.rec_module, a.id';
		$order_message_list = $model->query($sql_str);

		$this->assign ( 'order_message_list', $order_message_list );
		
		//dump($sql_str);
		//dump($order_message_list);
		
		
		//dump($vo);
		$this->display ( 'show' );			
	}
	
	//添加，修改保存时公共调用的函数
	function commSave($model,&$vo, $currency_radio){
		
		$saveAddress = $_REQUEST['saveAddress'];
		import('ORG.Util.HashMap');
        $rec_module_list = array();
        $goods_id_list = array();
        $goods_sn_list = array();
        $goods_name_list = array();
        $goods_price_list = array();
        $goods_num_list = array();
        $price_sum_list = array();
        $goods_weight_list = array();
        $goods_score_list = array();
        
        $rec_module_list = $_REQUEST['rec_module_list'];
        $goods_id_list = $_REQUEST['goods_id_list'];
        $goods_sn_list = $_REQUEST['goods_sn_list'];
        $goods_name_list = $_REQUEST['goods_name_list'];
        $cost_price_list = $_REQUEST['cost_price_list'];
        $goods_price_list = $_REQUEST['goods_price_list'];
        $goods_num_list = $_REQUEST['goods_num_list'];
        $price_sum_list = $_REQUEST['price_sum_list'];
        $goods_weight_list = $_REQUEST['goods_weight_list'];
        $goods_score_list = $_REQUEST['goods_score_list'];
        //dump($calc_score);	

		//商品明细
		$order_id = intval($vo['id']);
        
		$sql_str = 'delete from '.C("DB_PREFIX").'order_goods where order_id = '.$order_id;
		
		$model->execute($sql_str);							 	
	
		$total = count($goods_id_list);
		for($i=0;$i<$total;$i++){
			$sql_str = 'insert into '.C("DB_PREFIX").'order_goods(order_id,rec_module, rec_id, data_sn,data_name,data_cost_price, data_price,number,data_total_price,data_weight,data_score,send_number) values('.
				$order_id.','.
				'\''.$rec_module_list[$i].'\','.
				'\''.$goods_id_list[$i].'\','.
				'\''.$goods_sn_list[$i].'\','.
				'\''.$goods_name_list[$i].'\','.
				'\''.$cost_price_list[$i]*$currency_radio.'\','.
				'\''.$goods_price_list[$i]*$currency_radio.'\','.
				'\''.$goods_num_list[$i].'\','.
				'\''.$price_sum_list[$i]*$currency_radio.'\','.
				'\''.$goods_weight_list[$i].'\','.
				'\''.$goods_score_list[$i].'\','.
				'0)';
			//dump($sql_str);
			$model->execute($sql_str);			
		}			        		

			        		
		//保存本次收货地址到会员地址列表
		if (($saveAddress == 1 || $saveAddress == true) && (!empty($vo['user_id']) && $vo['user_id'] > 0)){
			//MemberAddress
			$condition = array();
			
			$condition['user_id'] = $vo['user_id'];
			$condition['consignee'] = $vo['consignee'];
			$condition['region_lv1'] = $vo['region_lv1'];
			$condition['region_lv2'] = $vo['region_lv2'];
			$condition['region_lv3'] = $vo['region_lv3'];
			$condition['region_lv4'] = $vo['region_lv4'];
			$condition['address'] = $vo['address'];
			$condition['zip'] = $vo['zip'];
			$condition['mobile_phone'] = $vo['mobile_phone'];
			$condition['fix_phone'] = $vo['fix_phone'];
			
			$UserConsignee = D( "UserConsignee" );
			if ($UserConsignee->where($condition)->count() == 0){
				$ma_vo = $UserConsignee->create();
				$ma_vo['user_id'] = $vo['user_id'];
				$ma_vo['consignee'] = $vo['consignee'];
				$ma_vo['region_lv1'] = $vo['region_lv1'];
				$ma_vo['region_lv2'] = $vo['region_lv2'];
				$ma_vo['region_lv3'] = $vo['region_lv3'];
				$ma_vo['region_lv4'] = $vo['region_lv4'];
				$ma_vo['address'] = $vo['address'];
				$ma_vo['zip'] = $vo['zip'];
				$ma_vo['mobile_phone'] = $vo['mobile_phone'];
				$ma_vo['fix_phone'] = $vo['fix_phone'];
				$ma_vo['id'] = null;
				$UserConsignee->add($ma_vo);
			}				
		}
	}
		
   function save()
    {
		$model	=	D("Order");
        if(false === $vo = $model->create()) {
        	$this->error($model->getError());
        }
         //是否要保价
         $vo ['protect'] = ! empty($_POST ['protect']) ? 1:0;
         //是否开发票
         $vo ['tax'] = ! empty($_POST ['tax']) ? 1:0;

         $vo['create_time'] = ! empty ( $_POST ['create_time'] ) ? localStrToTime($_POST ['create_time'] ) : 0;
         
         $vo['update_time'] = gmtTime();
          
         if (empty($vo['currency_radio']) || $vo['currency_radio'] == 0){
         	$vo['currency_radio'] = 1;
         }
         $currency_radio = 1 / $vo['currency_radio'];
         
         
         //商品总金额
         $vo['total_price'] = $vo['total_price'] * $currency_radio;
         //配送费用
         $vo['delivery_fee'] = $vo['delivery_fee'] * $currency_radio;
         //保价
         $vo['protect_fee'] = $vo['protect_fee'] * $currency_radio;
         //促销优惠金额
         $vo['promote_money'] = $vo['promote_money'] * $currency_radio;
         //税金
         $vo['tax_money'] = $vo['tax_money'] * $currency_radio; 
         //支付手续费
         $vo['payment_fee'] = $vo['payment_fee'] * $currency_radio;         
         //订单总金额
         $vo['order_total_price'] = $vo['order_total_price'] * $currency_radio;          
         
         //商品成本  
	     $cost_price_list = $_REQUEST['cost_price_list'];
	     $goods_num_list = $_REQUEST['goods_num_list'];         
	     $cost_total_price = 0;
    	  for($i=0;$i<count($cost_price_list);$i++){
		  	$cost_total_price = $cost_total_price + floatval($cost_price_list[$i]) * floatval($goods_num_list[$i]) * $currency_radio;		
		  }
		 $vo['cost_total_price'] = $cost_total_price;      
         
        //保存当前数据对象
    	if (intval($_REQUEST['id']) == 0){
			$vo['id'] = $model->add($vo);
			
			//dump($model->getLastSql());
			if ($vo['id']!==false) { //保存成功
				$this->commSave($model, $vo, $currency_radio);
				$this->saveLog(1,$vo['id']);
				$this->assign ('jumpUrl', u('Order/show', array('id'=>intval($vo['id']))));
				$this->success (L('ADD_SUCCESS'));
			} else {
				//失败提示
				$this->saveLog(0,$vo['id']);
				$this->error (L('ADD_FAILED'));
			}			
		}else{
			// 更新数据
			$list = $model->save($vo);
			//dump($model->getLastSql());
			if (false !== $list) {
				//dump('aa');
				$this->commSave($model, $vo, $currency_radio);
				
				//成功提示
				$this->saveLog(1);
				$this->assign ('jumpUrl', u('Order/show', array('id'=>intval($vo['id']))));
				$this->success (L('EDIT_SUCCESS'));
			} else {
				//错误提示
				$this->saveLog(0);
				$this->error (L('EDIT_FAILED'));
			}			
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
				$names .= M("Order")->where("id=".$idd)->getField("sn").",";
			}
			if($names!='')
			{
				$names = substr($names,0,strlen($names)-1);
			}
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if(!$_SESSION['all_city'])
				{
				$lists = $model->where($condition)->findAll();
				foreach ($lists as $v)
				{
					$goods_id = M("OrderGoods")->where("order_id=".$v['id'])->getField("rec_id");
					$city_id = M("Goods")->where("id=".$goods_id)->getField("city_id");
					if(!in_array($city_id,$_SESSION['admin_city_ids']))
					{
						$this->error("不能删除其他城市的订单");
					}
				}
				}
				if (false !== $model->where ( $condition )->delete ()) {
					//echo $model->getlastsql();
					D("OrderGoods")->where(array ("order_id" => array ('in', explode ( ',', $id ) ) ))->delete();  //同步删除礼包
					$msgList = D("Message")->where(array ("rec_module"=>'Order',"rec_id" => array ('in', explode ( ',', $id ) ) ))->findAll();  //同步删除礼包
					D("Message")->where(array ("rec_module"=>'Order',"rec_id" => array ('in', explode ( ',', $id ) ) ))->delete();  //删除订单留言
					M("GroupBond")->where("order_id='".M("Order")->where($condition)->getField("sn")."'")->delete();
					M("PaymentLog")->where("rec_id=".$id." and rec_module='Order'")->delete();
					foreach($msgList as $msg)
					{
						D("Message")->where("pid=".$msg['id'])->delete();  //删除订单留言的相关回复
					}
					
					$msg = '删除定单:'.$names."ID:".$id;
					$this->saveLog(1,0,$msg);
					$this->success (L('DEL_SUCCESS'));
				} else {
					
					$msg = '删除定单:'.$names."ID:".$id;
					$this->saveLog(0,0,$msg);
					$this->error (L('DEL_FAILED'));
				}
			} else {				
				$msg = '删除定单:'.$names;
				$this->saveLog(0,0,$msg);
				$this->error ( L('INVALID_OP') );
			}
		}
		$this->forward ();
	}
	
	
	function add_incharge() {
		
		$this->assign ( 'isNew', true );
		$id = intval($_REQUEST['id']);
			
		
		$OrderIncharge = D ("OrderIncharge");
		if ($id == 0){
			$vo = $OrderIncharge->create();
		}else{
			$vo = $OrderIncharge->getById ( $id );
		}
		
		$model = D ("Order");
		$order_id = $_REQUEST ['order_id'];
		if(!$_SESSION['all_city'])
		{
		$goods_id = M("OrderGoods")->where("order_id=".$order_id)->getField("rec_id");
					$city_id = M("Goods")->where("id=".$goods_id)->getField("city_id");
					if(!in_array($city_id,$_SESSION['admin_city_ids']))
					{
						$this->error("不能操作其他城市的订单");
					}
		}
		$order_vo = $model->getById ( $order_id );
		//下单时间
		$order_vo['create_time'] = timeToLocalStr($order_vo ['create_time'], 'Y-m-d h:i:s');
		//收款状态：0:未收款; 1:部分收款; 2:全部收款; 3:部分退款; 4:全部退款
		$order_vo['money_status_name'] = L("ORDER_MONEY_STATUS_".intval($order_vo['money_status']));
         
	    //订单总金额
	    $order_vo['order_total_price'] = round($order_vo['order_total_price'] * $order_vo['currency_radio'], 3);
		//已收金额	
	    $order_vo['order_incharge'] = round($order_vo['order_incharge'] * $order_vo['currency_radio'], 3);
	         
		$this->assign ( 'order_vo', $order_vo );

		//支付方式
		$user_money = 0.0;//会员帐户余额
		$PaymentMode = D ( "Payment" );
		if ($order_vo['user_id'] > 0) //如果不是会员，则无法使用：余额支付
		{
		  $PaymentList = $PaymentMode->where('status = 1')->field('id,name_'.DEFAULT_LANG_ID.' as name')->findAll();
		  //$sql_str = 'select money from '.C("DB_PREFIX").'user where id = '.$order_vo['user_id'];
		  //$money = $model->getSqlValue($sql_str, 'money');
		  
		  $user =  D ('User')->getById ($order_vo['user_id']);
		  $user['money'] = round($user['money'] * $order_vo['currency_radio'], 3);
		  $user_money = $user['money']; 
		  $this->assign ( 'user', $user);
		}else{ 
		  $PaymentList = $PaymentMode->where('class_name <> \'Accountpay\' and status = 1')->field('id,name_'.DEFAULT_LANG_ID.' as name')->findAll ();
		}	
		  
		//dump($PaymentList);
		//支付方式
		$this->assign ( "user_money", $user_money);  
		$this->assign ( "paymentList", $PaymentList );

		//dump($PaymentList);
		
		//货币
		$Currency = D ( "Currency" );
		$CurrencyList = $Currency->field('id,name_'.DEFAULT_LANG_ID.' as name')->findAll();
		$this->assign ( "currencyList", $CurrencyList );
		
		//dump($CurrencyList);
		
		
		$vo['order_id'] = $order_id;
		$vo['currency_id'] = $order_vo['currency_id'];
		$vo['currency_radio'] = $order_vo['currency_radio'];
		$vo['money'] = $order_vo['order_total_price'] - $order_vo['order_incharge'];
		
		
		$this->assign ( 'vo', $vo );
		
		$this->display ( 'incharge' );
	}
	
   function save_incharge()
    {
		$model	=	D("OrderIncharge");
        if(false === $vo = $model->create()) {
        	$this->error($model->getError());
        }
        if(!$_SESSION['all_city'])
        {
    	$goods_id = M("OrderGoods")->where("order_id=".$vo['order_id'])->getField("rec_id");
					$city_id = M("Goods")->where("id=".$goods_id)->getField("city_id");
					if(!in_array($city_id,$_SESSION['admin_city_ids']))
					{
						$this->error("不能操作其他城市的订单");
					}
   		 }
         if (empty($vo['currency_radio']) || $vo['currency_radio'] == 0){
         	$vo['currency_radio'] = 1;
         }
         $currency_radio = 1 / $vo['currency_radio'];
                 
         //收款金额
        $vo['money'] = $_REQUEST['money'] * $currency_radio;
        $vo['cost_payment_fee'] = $_REQUEST['cost_payment_fee'] * $currency_radio;
                
		$Payment = D ( "Payment" );
		$payment = $Payment->getById(intval($_REQUEST ['payment_id']));
		        
		$user_id = intval($_REQUEST ['user_id']);
    	if ($payment['class_name'] == 'Accountpay' && $user_id > 0){//会员使用预存款支付
		  $user = D ('User')->getById ($user_id);
		  if (($user['money'] < 0) || ($vo['money'] - $user['money'] > 0.01 )){
            //失败提示
            $this->error('会员帐户余额不足，请选择其它支付方式。');
            exit;	  	
		  } 
		}        
        
        //保存当前数据对象
        $vo['create_time'] = gmtTime();
		
        $id = $model->add($vo);
        
        if($id) { //保存成功
            //增加已收金额
            //dump('ccc');
			inc_order_incharge($id);
			//dump('ffff');
			//$this->
			$order_sn = M("Order")->where("id=".$vo['order_id'])->getField("sn");
			$msg = '添加定单'.$order_sn."的收款单,ID:".$id;
			$this->saveLog(1,$id,$msg);
			$this->assign ('jumpUrl', u('Order/show', array('id'=>intval($vo['order_id']))));
            $this->success(L('ADD_SUCCESS'));
        }else {
            //失败提示
            $order_sn = M("Order")->where("id=".$vo['order_id'])->getField("sn");
			$msg = '添加定单'.$order_sn."的收款单,ID:".$id;
			$this->saveLog(0,$id,$msg);
            $this->error(L('ADD_FAIL'));
        }
    }

	
	function add_uncharge() {
		
		$this->assign ( 'isNew', true );
		$id = intval($_REQUEST['id']);
		$OrderIncharge = D ("OrderIncharge");
		if ($id == 0){
			$vo = $OrderIncharge->create();
		}else{
			$vo = $OrderIncharge->getById ( $id );
		}
		
		$model = D ("Order");
		$order_id = $_REQUEST ['order_id'];
		if(!$_SESSION['all_city'])
		{
	$goods_id = M("OrderGoods")->where("order_id=".$order_id)->getField("rec_id");
					$city_id = M("Goods")->where("id=".$goods_id)->getField("city_id");
					if(!in_array($city_id,$_SESSION['admin_city_ids']))
					{
						$this->error("不能操作其他城市的订单");
					}
		}
		$order_vo = $model->getById ( $order_id );
		
		if ($order_vo['currency_radio'] == 0){
			$order_vo['currency_radio'] = 1;
		}
		
		//下单时间
		$order_vo['create_time'] = timeToLocalStr($order_vo ['create_time'], 'Y-m-d h:i:s');
		//收款状态：0:未收款; 1:部分收款; 2:全部收款; 3:部分退款; 4:全部退款
		$order_vo['money_status_name'] = L("ORDER_MONEY_STATUS_".intval($order_vo['money_status']));
         
	    //订单总金额
	    $order_vo['order_total_price'] = round($order_vo['order_total_price'] * $order_vo['currency_radio'], 3);
		//已收金额	
	    $order_vo['order_incharge'] = round($order_vo['order_incharge'] * $order_vo['currency_radio'], 3);

	    //dump($order_vo);
		$this->assign ( 'order_vo', $order_vo );

		//支付方式
		$user_money = 0.0;//会员帐户余额
		$PaymentMode = D ( "Payment" );
		
		//$PaymentList = array();
		//$PaymentList[] =  array("id"=>0, "name"=>"不退到用户帐户");
		if ($order_vo['user_id'] > 0) //如果不是会员，则无法使用：余额支付
		{
		  $PaymentList = $PaymentMode->field('id,class_name,name_'.DEFAULT_LANG_ID.' as name')->where('status=1')->findAll();
		  
		  foreach($PaymentList as $k=>$v){
			  if ($v['class_name'] == 'Accountpay'){
			  	$PaymentList[$k]['name'] = "退到用户帐户(余额支付)";
			  }
		  }
		  
		  //dump($PaymentList);
		  //$Payment = $PaymentMode->field('id,name_'.DEFAULT_LANG_ID.' as name')->where('class_name =\'Accountpay\'')->find();
		  //dump($Payment);
		  //$PaymentList[] =  array("id"=>$Payment['id'], "name"=>"退到用户帐户");
		  //$sql_str = 'select money from '.C("DB_PREFIX").'user where id = '.$order_vo['user_id'];
		  //$money = $model->getSqlValue($sql_str, 'money');
		  
		  $user =  D ('User')->getById ($order_vo['user_id']);
		  $user['money'] = round($user['money'] * $order_vo['currency_radio'], 3);
		  $user_money = $user['money']; 
		  $this->assign ( 'user', $user);
		}else 
		{
			$PaymentList = $PaymentMode->where('class_name <> \'Accountpay\' and status=1')->field('id,name_'.DEFAULT_LANG_ID.' as name')->findAll ();
		}	
		  //dump($PaymentList);
		  
		//支付方式
		$this->assign ( "user_money", $user_money);  
		$this->assign ( "paymentList", $PaymentList );

		//dump($PaymentList);
		
		//货币
		$Currency = D ( "Currency" );
		$CurrencyList = $Currency->field('id,name_'.DEFAULT_LANG_ID.' as name')->findAll();
		$this->assign ( "currencyList", $CurrencyList );
		
		//dump($CurrencyList);
		
		
		$vo['order_id'] = $order_id;
		$vo['currency_id'] = $order_vo['currency_id'];
		$vo['currency_radio'] = $order_vo['currency_radio'];
		$vo['money'] = $order_vo['order_incharge'];
		$vo['dec_score'] = $order_vo['order_score'];
		
		$this->assign ( 'vo', $vo );
		
		$this->display ( 'uncharge' );
	}
	
   function save_uncharge()
    {
		$model	=	D("OrderUncharge");
        if(false === $vo = $model->create()) {
        	$this->error($model->getError());
        }
        if(!$_SESSION['all_city'])
        {
    	$goods_id = M("OrderGoods")->where("order_id=".$vo['order_id'])->getField("rec_id");
					$city_id = M("Goods")->where("id=".$goods_id)->getField("city_id");
					if(!in_array($city_id,$_SESSION['admin_city_ids']))
					{
						$this->error("不能操作其他城市的订单");
					}
        }
         if (empty($vo['currency_radio']) || $vo['currency_radio'] == 0){
         	$vo['currency_radio'] = 1;
         }
         $currency_radio = 1 / $vo['currency_radio'];
                 
         //收款金额
        $vo['money'] = $_REQUEST['money'] * $currency_radio;
        $vo['cost_payment_fee'] = $_REQUEST['cost_payment_fee'] * $currency_radio;                
        
        //保存当前数据对象
        $vo['create_time'] = gmtTime();
		
        $id = $model->add($vo);
        
        if($id) { //保存成功
            //减少已收金额
			$this->inc_order_uncharge($id);
			
			$order_sn = M("Order")->where("id=".$vo['order_id'])->getField("sn");
			$msg = '添加定单'.$order_sn."的退款单,ID:".$id;
			$this->saveLog(1,$id,$msg);
			
			$this->assign ('jumpUrl', u('Order/show', array('id'=>intval($vo['order_id']))));
            $this->success(L('ADD_SUCCESS'));
        }else {
            //失败提示
            $order_sn = M("Order")->where("id=".$vo['order_id'])->getField("sn");
			$msg = '添加定单'.$order_sn."的退款单,ID:".$id;
			$this->saveLog(1,$id,$msg);
            $this->error(L('ADD_FAIL'));
        }
    }
    	
    //批量退款
    function batch_uncharge()
    {
    	$goods_id = intval($_REQUEST['goods_id']);
    	if(!$_SESSION['all_city'])
    	$goods_id = D("Goods")->where(array("id"=>intval($_REQUEST['goods_id']),"city_id"=>array("in",$_SESSION['admin_city_ids'])))->getField("id");
    	$orderCountArr = M()->query("select distinct og.order_id from ".M("OrderGoods")->getTableName()." as og left join ".M("Order")->getTableName()." as o on o.id = og.order_id AND o.money_status in(1,2,3) where og.rec_id = '$goods_id' and og.id is not null and o.id is not null and o.user_id >0");
    	
    	$orderCount = count($orderCountArr);
    	$icount = 0;
    	if ($orderCount > 0){
	    	$model_o = D("Order");
	    	
	    	$payment = D("Payment")->where("class_name='Accountpay'")->find();
	    	foreach($orderCountArr as $k=>$v)
			{
				$order_id = intval($v['order_id']);
				$order_vo = $model_o->getById($order_id);
			
				$model = D("OrderUncharge");
			    if(false === $vo = $model->create()) {
	        		$this->error($model->getError());
			    }
		         //收款金额
		        $vo['order_id'] = $order_id;  
		        $vo['cost_payment_fee'] = 0;
		        $vo['currency_id'] = $order_vo['currency_id']; 
		        $vo['currency_radio'] = $order_vo['currency_radio'];              
		        $vo['money'] = $order_vo['order_incharge'];
		        $vo['memo'] = '批量订单退款';		    
				$vo['payment_id'] = $payment['id'];	
				$vo['create_time'] = gmtTime();
				
				$id = $model->add($vo);
				if($id) { //保存成功
					$this->inc_order_uncharge($id);	//减少已收金额
					$icount = $icount + 1;
				}			
			}
			    	
			$this->assign ('jumpUrl', u('Goods/index'));
	        $this->success('批量退款成功！共【'.$orderCount.'】张单据，成功退款【'.$icount.'】张');     		
    	}else{
			$this->assign ('jumpUrl', u('Goods/index'));
	        $this->success('未发现，退款单据！');     		
    	}
    }
    
	//减少已收金额
	function inc_order_uncharge($order_uncharge_id){
		
		$uncharge_vo = D("OrderUncharge")->getById ( $order_uncharge_id );
		
		$model = D("Order");
		$order_vo = $model->getById ( $uncharge_vo['order_id'] );
		$order_vo['order_incharge'] = $order_vo['order_incharge'] - $uncharge_vo['money'];

		
		$payment = D ( "Payment" )->getById($uncharge_vo['payment_id']);
		if ($payment['class_name'] == 'Accountpay' && $order_vo['user_id'] > 0){//会员使用预存款支付，减少预存款
			//记录会员预存款变化明细
			$sql_str = 'insert into '.C("DB_PREFIX").'user_money_log(user_id, rec_id, money,create_time,rec_module,memo_'.DEFAULT_LANG_ID.') values('.
				$order_vo['user_id'].','.
				$order_uncharge_id.','.
				$uncharge_vo['money'].','.
				gmtTime().','.
				'\'OrderUncharge\','.
				'\'订单退款\''.  //预存款退款
				')';
			//dump($sql_str);
			$model->execute($sql_str);	
			//减去会员的预存款金额
			$sql_str = 'update '.C("DB_PREFIX").'user set money = money + '.$uncharge_vo['money'].' where id = '.$order_vo['user_id'];
			$model->execute($sql_str);
		} 
		
		//add by chenfq 2010-06-5  记录帐户资金变化明细 begin
		if ($payment){
			payment_money_log($payment['id'], 
								  $_SESSION[C('USER_AUTH_KEY')], 
								  $order_uncharge_id, 
								  'OrderUncharge', 
								  $uncharge_vo['money'] * -1, 
								  '管理员后台退订单金额：'.$uncharge_vo['money'], 
								  false, 
								  'Admin', 
								  $payment['name_1'], 
								  $_SESSION['adm_name']);
		}								  		
		//add by chenfq 2010-06-5  记录帐户资金变化明细 end
		
		//会员积明细,扣掉订单所得积分	
		if ($order_vo['user_id'] > 0 && $uncharge_vo['uncharge_score'] > 0){
				$sql_str = 'insert into '.C("DB_PREFIX").'user_score_log(user_id, create_time, score, memo_'.DEFAULT_LANG_ID.') values('.
					$order_vo['user_id'].','.
					gmtTime().','.
					-1 * $uncharge_vo['uncharge_score'].','.
					'\''.L("ORDER_SCORE_MEMO_3").'('.$order_vo['sn'].')\''.  //扣掉订单所得积分
					')';
					
				//dump($sql_str);	
				$model->execute($sql_str);	

			//增加会员积分
			$sql_str = 'update '.C("DB_PREFIX").'user set score = score - '.$uncharge_vo['uncharge_score'].' where id = '.$order_vo['user_id'];
			$model->execute($sql_str);				
		} 		
		
		//已收金额 > 订单总金额
		//收款状态：0:未收款; 1:部分收款; 2:全部收款; 3:部分退款; 4:全部退款		
		if ($order_vo["order_incharge"] <= 0.01){
			$order_vo["money_status"] = 4;
		}else if($order_vo["order_incharge"] < $order_vo['order_total_price']){
			$order_vo["money_status"] = 3;
		}		
		
		//退款手续费
		$order_vo["cost_payment_fee"] = floatval($order_vo["cost_payment_fee"]) + $uncharge_vo['cost_payment_fee'];	

		$model->save($order_vo);
		
		//退款时，删除方维卷 add by chenfq 2010-04-22
		D("GroupBond")->where("order_id = '".$order_vo['sn']."'")->delete(); //没有分配的方维卷
		//4:全部退款 重新计算购买人数
		if ($order_vo["money_status"] == 4){
			$orderGoods = D("OrderGoods")->where('order_id = '.$order_vo['id'])->find();
			$goods = D("Goods")->where('id = '.$orderGoods['rec_id'])->find();
			if (count($goods) > 0){
				//计算已经购买了几个商品
				$sql = "select sum(og.number) as number from ".C("DB_PREFIX")."order as o left join ".C("DB_PREFIX")."order_goods  as og on og.order_id = o.id where og.rec_id = $goods[id] and o.money_status = 2";
				$number = M()->query($sql);
					
				$buy_count = intval($number[0]['number']);
				M("Goods")->where('id = '.$goods['id'])->setField('buy_count',$buy_count);
				//dump(M("Goods")->getLastSql());
			}			
		}
		
		//退还返利
		$referral_list = M("Referrals")->where("order_id=".$order_vo['id']." and is_pay = 1")->findAll();
		foreach($referral_list as $k=>$v)
		{
			unPayReferrals($v['id']);
		}
		
		
	}

	
	public function profit() {
		$id = intval($_REQUEST['order_id']);
		if(!$_SESSION['all_city'])
		{
		$goods_id = M("OrderGoods")->where("order_id=".$id)->getField("rec_id");
					$city_id = M("Goods")->where("id=".$goods_id)->getField("city_id");
					if(!in_array($city_id,$_SESSION['admin_city_ids']))
					{
						$this->error("不能操作其他城市的订单");
					}
		}
		$model = D("Order");
		if ($id == 0){//新增
			$this->error('无效订单ID');	
		}else{//编辑
			$vo = $model->getById ( $id );
			$vo ['create_time'] = timeToLocalStr($vo ['create_time'], 'Y-m-d h:i:s');
			$vo['money_status_name'] = L("ORDER_MONEY_STATUS_".intval($vo['money_status']));
			$vo['goods_status_name'] = L("ORDER_GOODS_STATUS_".intval($vo['goods_status']));
					
			$this->assign ( 'vo', $vo );
			//dump($vo);
			$this->display ( 'profit' );			
		}
	}	
	
	//订单完成
   function save_profit()
    {
		$model	=	D("Order");
        if(false === $vo = $model->create()) {
        	$this->error($model->getError());
        }
        if(!$_SESSION['all_city'])
        {
   		 $goods_id = M("OrderGoods")->where("order_id=".$vo['id'])->getField("rec_id");
					$city_id = M("Goods")->where("id=".$goods_id)->getField("city_id");
					if(!in_array($city_id,$_SESSION['admin_city_ids']))
					{
						$this->error("不能操作其他城市的订单");
					}
        }
        //保存当前数据对象
        $vo['update_time'] = gmtTime();
        if ($vo['status'] != 1){//订单状态 0: 处理中1: 完成2: 作废
        	$vo['status'] = 1;
        	//发放优惠卷
        	if ($vo['user_id'] > 0){
        		$sql_str = 'select a.* from '.C("DB_PREFIX").'order_promote a left outer join '.C("DB_PREFIX").'promote b where b.promote_type_id = 5 and order_id = '.intval($vo['id']);
        		$voList = $model->query($sql_str);
        		if ($voList != false){
	        		$puc_model = D ('PromoteUserCard');
			        foreach($voList as $k=>$cart_vo){
			        	$card_id = intval($cart_vo['card_id']);
			        	$num = intval($cart_vo['promote_data_number']);
			        	if ($card_id > 0 && $num > 0)
			        	{
			        		$p_vo = D ('Promote')->getById ($card_id);
			        		$pc_model = D ('PromoteCard');
							for($i=0;$i<$num;$i++){
								//生成新的优惠卡
								$pc_vo = $pc_model->create();
								$pc_vo['id'] = null;
								$pc_vo['promote_id'] = $card_id;
								$pc_vo['card_limit'] = $p_vo['card_limit'];
								$pc_vo['card_used'] = 0;
								$pc_id = $pc_model->add($pc_vo);
								
								$pc_vo['id'] = $pc_id;
								$pc_vo['card_code'] = buildCard($pc_id);
								$pc_model->save($pc_vo);
								
								//将优惠卡赋值给会员
								$puc_vo = $puc_model->create();
								$puc_vo['id'] = null;
								$puc_vo['user_id'] = $vo['user_id'];
								$puc_vo['card_id'] = $pc_id;
								$puc_model->save($puc_vo);
						 }
			        }//if ($card_id > 0 && $num > 0)
			        //统计发放总量	
					$sql_str = 'update '.C("DB_PREFIX").'promote set card_total = (select count(*) from '.C("DB_PREFIX").'promote_card where promote_id = '.$card_id.') where id ='.$card_id;
					$model->execute($sql_str);			    
			   }//foreach($voList as $k=>$cart_vo)     
        	}//if ($voList != false)
        }//if ($vo['user_id'] > 0)
    }//if ($vo['status'] != 1)
				
    $id = $model->save($vo);
    if($id) { //保存成功		
		$this->assign ('jumpUrl', u('Order/show', array('id'=>intval($vo['id']))));
        $this->success(L('EDIT_SUCCESS'));
    }else {
       //失败提示
       $this->error(L('EDIT_FAIL'));
    }
  }	
  
  
	public function offlinecomplete() {
		$id = intval($_REQUEST['order_id']);
		if(!$_SESSION['all_city'])
		{
		$goods_id = M("OrderGoods")->where("order_id=".$id)->getField("rec_id");
					$city_id = M("Goods")->where("id=".$goods_id)->getField("city_id");
					if(!in_array($city_id,$_SESSION['admin_city_ids']))
					{
						$this->error("不能操作其他城市的订单");
					}
		}
		$model = D("Order");
		if ($id == 0){//新增
			$this->error('无效订单ID');	
		}else{//编辑
			$order_vo = $model->getById ( $id );
			
			if ($order_vo['status'] == 0 && $order_vo["offline"] == 1){// add by chenfq 2010-04-07 offline 0：非线下订单；1：线下订单
				$userid = intval($order_vo['user_id']);
				$orderGoods = D("OrderGoods")->where('order_id = '.$order_vo['id'])->find();
				
				$parentID = intval($order_vo['parent_id']);
					
				if($parentID > 0 && $parentID != $userid)
				{
					$referrals['user_id'] = $userid;
					$referrals['parent_id'] = $parentID;
					$referrals['order_id'] = $order_vo['id'];
					$referrals['goods_id'] = intval($orderGoods['rec_id']);
					$referrals['money'] = intval(eyooC("REFERRALS_MONEY"));
					$referrals['is_pay'] = 0;
					$referrals['create_time'] = gmtTime();
					$referrals['city_id'] = intval(M("Goods")->where("id=".$orderGoods['rec_id'])->getField("city_id"));
					$re_id = D("Referrals")->add($referrals);

				}
				
				//add by chenfq 2010-04-07  会员积明细, 全额支付时，计算积分 注：收款以后 $order_vo["money_status"] 的值都会大小0
				if ($order_vo['user_id'] > 0 && $order_vo['order_score'] > 0){
					if ($order_vo['order_score'] > 0){
						$Remark = L("ORDER_SCORE_MEMO_1").'('.$order_vo['sn'].')';//订单获得积分
					}else if($order_vo['order_score'] < 0){
						$Remark = L("ORDER_SCORE_MEMO_2").'('.$order_vo['sn'].')';//订单消费积分
					}		
			
					$sql_str = 'insert into '.C("DB_PREFIX").'user_score_log(user_id, create_time, score, memo_1) values('.$order_vo['user_id'].','.gmtTime().','.$order_vo['order_score'].','.'\''.$Remark.'\')';
					$model->execute($sql_str);
							
					//增加会员积分
					$sql_str = 'update '.C("DB_PREFIX").'user set score = score + '.$order_vo['order_score'].' where id = '.$order_vo['user_id'];
					$model->execute($sql_str);				
				}				
				
			}			
			
			$order_vo['status'] = 1;//订单状态 0: 未确认; 1: 完成; 2: 作废
			$id = $model->save($order_vo);
		    if($id) { //保存成功		
				$this->assign ('jumpUrl', u('Order/show', array('id'=>intval($order_vo['id']))));
		        $this->success(L('EDIT_SUCCESS'));
		    }else {
		       //失败提示
		       $this->error(L('EDIT_FAIL'));
		    }			
		}
	}	  
	//导出订单列表
	function exporder($page = 1,$condition=''){
		set_time_limit(0);
		$limit = (($page - 1)*500).",".(500);
		if($condition=='')
		{
			if(!$_SESSION['all_city'])
			{
			$condition['goods_id'] = D("Goods")->where(array("id"=>intval($_REQUEST['goods_id']),"city_id"=>array("in",$_SESSION['admin_city_ids'])))->getField("id");	
		    if (intval($_REQUEST['city_id']) > 0)
		    {
		
				if(!in_array(intval($_REQUEST['city_id']),$_SESSION['admin_city_ids']))
				{
					$this->error("不能搜索其他地区的团购订单");
				}
		
		    }
			}
			else
			{
				$condition['goods_id'] = intval($_REQUEST['goods_id']);
			}
			$condition['city_id'] =  intval($_REQUEST['city_id']);
			$condition['sn'] = $_REQUEST['sn'];
			$condition['user_name'] = $_REQUEST['user_name'];
			$condition['money_status'] = $_REQUEST['money_status'];
			$condition['goods_status'] = $_REQUEST['goods_status'];
			$condition['repay_status'] = $_REQUEST['repay_status'];
		}
		//条件
		$user_name  = $condition['user_name'];
		$goods_id  = $condition['goods_id'];
		$city_id  = $condition['city_id'];
		$order_sn = $condition['sn'];
		$money_status = $condition['money_status'];
		$goods_status = $condition['goods_status'];
		$repay_status = $condition['repay_status'];
		
		$sql_str = 'SELECT a.*,g.type_id,og.number '.
					'  FROM '.C("DB_PREFIX").'order a left join '.C("DB_PREFIX").'user u on u.id = a.user_id '.
					'  left join '.C("DB_PREFIX").'order_goods og on og.order_id = a.id '.
					'  left join '.C("DB_PREFIX").'goods g on g.id = og.rec_id where 1 = 1';
		
		if ((!empty($user_name)) && ($user_name <>'')){
			$sql_str .= " and u.user_name like '%".$user_name."%'";
		}
		
		if (intval($goods_id) > 0){
			$sql_str .= " and og.rec_id = '".intval($goods_id)."'";
		}
		
		if (intval($city_id) > 0){
			$sql_str .= " and g.city_id = '".intval($city_id)."'";
		}
		else
		{
			if(!$_SESSION['all_city'])
			$sql_str .= " and g.city_id in (".implode(",",$_SESSION['admin_city_ids']).")";
		}
				
		if ((!empty($order_sn)) && ($order_sn <>'')){
			$sql_str.= " and a.sn='".$order_sn."'";
		}
		
		if ((intval($repay_status) >= 0)){
			$sql_str .= " and a.repay_status = '".intval($repay_status)."'";
		
		}
		if ((intval($money_status) >= 0)){
			$sql_str .= " and a.money_status = '".intval($money_status)."'";
		
		}		
		
		if ((intval($goods_status) >= 0)){
			$sql_str .= " and a.goods_status = '".intval($goods_status)."'";
		}	
		
		$sql_str .=" order by id asc limit ".$limit;
		//条件
		$list = M()->query($sql_str);
		if($list)
		{
			register_shutdown_function(array(&$this, 'exporder'), $page+1,$condition);
			foreach($list as $k=>$v)
			{
				$order_goods_data = M("OrderGoods")->where("order_id=".$v['id'])->find();
				if($order_goods_data['attr']!='')
				$list[$k]['goods_name'] = $order_goods_data['data_name']."(".str_replace("\n",",",$order_goods_data['attr']).")";
				else
				$list[$k]['goods_name'] = $order_goods_data['data_name'];
				$list[$k]['user_name'] = M("User")->where("id=".$v['user_id'])->getField("user_name");
				$list[$k]['money_status'] = l("ORDER_MONEY_STATUS_".$v['money_status']);
				$list[$k]['goods_status'] = l("ORDER_GOODS_STATUS_".$v['goods_status']);
				$list[$k]['create_time'] = toDate($v['create_time']);
				$list[$k]['update_time'] = toDate($v['update_time']);
	//			$list[$k]['order_total_price'] = priceFormat($v['order_total_price']);
	//			$list[$k]['order_incharge'] = priceFormat($v['order_incharge']);
				$list[$k]['region_lv1'] = M("RegionConf")->where("id=".$v['region_lv1'])->getField("name");
				$list[$k]['region_lv2'] = M("RegionConf")->where("id=".$v['region_lv2'])->getField("name");
				$list[$k]['region_lv3'] = M("RegionConf")->where("id=".$v['region_lv3'])->getField("name");
				$list[$k]['region_lv4'] = M("RegionConf")->where("id=".$v['region_lv4'])->getField("name");
				$list[$k]['address'] = $list[$k]['region_lv1'].$list[$k]['region_lv2'].$list[$k]['region_lv3'].$list[$k]['region_lv4'].$v['address'];
				if($v['type_id']==1)
				$list[$k]['mobile_phone'] = $v['mobile_phone'];
				else
				$list[$k]['mobile_phone'] = $v['mobile_phone_sms']==''?M("User")->where("id=".$v['user_id'])->getField("mobile_phone"):$v['mobile_phone_sms'];
			}
			//dump($list);exit;
			//dump($sql);
	    	/* csv文件数组 */
			
	    	$order_value = array('sn'=>'""', 'user_name'=>'""', 'goods_name'=>'""','number'=>'""', 'money_status'=>'""', 'goods_status'=>'""', 'create_time'=>'""', 'order_total_price'=>'""', 'order_incharge'=>'""', 'consignee'=>'""', 'address'=>'""','zip'=>'""','email'=>'""', 'mobile_phone'=>'""', 'memo'=>'""');
	    	if($page == 1)
	    	{
	    	$content = utf8ToGB("订单编号,用户名,团购名称,订购数量,收款状态,发货状态,下单时间,订单总额,已收金额,收货人,发货地址,邮编,用户邮件,手机号码,订单留言");
	    	
	    	
	    	$content = $content . "\n";
	    	}
	    	
			foreach($list as $k=>$v)
			{
				
				$order_value['sn'] = '"' . $v['sn'] . '"';
				$order_value['user_name'] = '"' . utf8ToGB($v['user_name']) . '"';
				$order_value['goods_name'] = '"' . utf8ToGB($v['goods_name']) . '"';
				$order_value['number'] = '"' . utf8ToGB($v['number']) . '"';
				$order_value['money_status'] = '"' . utf8ToGB($v['money_status']) . '"';
				$order_value['goods_status'] = '"' . utf8ToGB($v['goods_status']) . '"';
				$order_value['create_time'] = '"' . utf8ToGB($v['create_time']) . '"';
				$order_value['order_total_price'] = '"' . utf8ToGB($v['order_total_price']) . '"';
				$order_value['order_incharge'] = '"' . utf8ToGB($v['order_incharge']) . '"';
				$order_value['consignee'] = '"' . utf8ToGB($v['consignee']) . '"';
				$order_value['address'] = '"' . utf8ToGB($v['address']) . '"';
				$order_value['zip'] = '"' . utf8ToGB($v['zip']) . '"';
				$order_value['email'] = '"' . utf8ToGB($v['email']) . '"';
				$order_value['mobile_phone'] = '"' . utf8ToGB($v['mobile_phone']) . '"';
				$order_value['memo'] = '"' . utf8ToGB($v['memo']) . '"';
	
				
				
				$content .= implode(",", $order_value) . "\n";
			}	
			
			//dump($content);exit;
	    	header("Content-Disposition: attachment; filename=order_list.csv");
	    	//header("Content-Type: application/octet-stream");
	    	echo ($content);   
		}
	}
  
}

?>