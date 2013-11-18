<?php 
// 退款单
class OrderUnchargeAction extends CommonAction{

	public function index()
    {
    	$order_sn = $_REQUEST['order_sn'];
    	$group_id = $_REQUEST['group_id'];
    	$user_name = $_REQUEST['user_name'];
    	$user_id = intval(M("User")->where("user_name='".$user_name."'")->getField("id"));
    	$payment_id = intval($_REQUEST['payment_id']);
    	
    	$payment_list = M("Payment")->where("status=1")->findAll();
    	$this->assign("payment_list",$payment_list);
    	
		import('ORG.Util.HashMap');
		
		$parameter = null;
		$sql_str =  'SELECT a.*,'.
					'       b.sn as ORDER_SN,'.
					'       u.user_name as user_name,'.
					'       b.order_total_price as  final_amount,'.
						'       CASE'.
						'          WHEN c.class_name = \'Accountpay\' THEN'.
						'            \'退到用户帐户(余额支付)\''.					
						'          ELSE'.
						'       	c.name_'.DEFAULT_LANG_ID.''.
						//'            \'不退到用户帐户\''.
						'       END as PName,'.			
					//'       c.name_'.DEFAULT_LANG_ID.' as PName,'.
					'       d.name_'.DEFAULT_LANG_ID.' AS AName,'.
					'       og.data_name,'.
					'       og.number'.		
					'  FROM '.C("DB_PREFIX").'order_uncharge a'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'order b ON b.id = a.order_id'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'payment c ON c.id = a.payment_id'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'currency d ON d.id = a.currency_id'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'user u ON u.id = b.user_id'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'order_goods og ON og.order_id = a.order_id'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'goods as g on g.id = og.rec_id';
		if(!$_SESSION['all_city'])		
		$sql_str .= '  where g.city_id in ('.implode(',',$_SESSION['admin_city_ids']).')';	
		else
		$sql_str .= ' where 1 = 1 ';
		
    	if($order_sn&&$order_sn!='')
		{
			$sql_str.=" and b.sn ='".$order_sn."' ";
		}
    	if($group_id&&$group_id!='')
		{
			$sql_str.=" and g.id =".intval($group_id);
		}
    	if($user_name&&$user_name!='')
		{
			$sql_str.=" and b.user_id =".$user_id;
		}
		if($payment_id!=0)
		{
			$sql_str.=" and a.payment_id =".$payment_id;
		}
		
		$sql_str .= ' ORDER BY a.create_time desc';

		
		//dump($sql_str);
         //创建数据对象
        $model = D($this->name);		
        $this->_Sql_list($model, $sql_str, $parameter);
        
        $this->display();
    }	
	
	function commAssign($model, $order_id, $consignment_id){
		
		//地区：国家
		$RegionConf = D ( "RegionConf" );
		$region_lv1List = $RegionConf->where("pid = 0")->field("id,name")->findAll();
		//dump($region_lv1List);
		$this->assign ( "region_lv1List", $region_lv1List);
		//dump($countryList);
		$this->assign ( "countryList", $region_lv1List);
		
		//配送方式
		$FreightMode = D ( "Delivery" );
		$FreightModeList = $FreightMode->field('id,name_'.DEFAULT_LANG_ID.' as name')->findAll();
		$this->assign ( "freightModeList", $FreightModeList );
				
		$sql_str = 'SELECT a.rec_module,'.
					'       a.id,'.
					'       a.data_sn,'.
					'       a.data_name,'.
					'       ifnull(a.data_price, 0) as data_price,'.
					'       ifnull(a.data_weight, 0) as data_weight,'.
					'       ifnull(a.number, 0) as goods_num,'.
					'       ifnull(a.send_number, 0) as send_num,'.
					'       ifnull(b.stock, 0) as goods_number,'.
					'       ifnull(c.number, 0) as cd_num'.
					'  FROM '.C("DB_PREFIX").'order_goods a'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'goods_spec_item b ON b.id = a.rec_id'.
					'  LEFT OUTER JOIN '.C("DB_PREFIX").'order_consignment_goods c ON c.order_consignment_id = '.intval($consignment_id).
					'                                                 AND c.order_goods_id = a.id'.
					' WHERE (a.rec_module = \'GoodsSpecItem\' or a.rec_module = \'ScoreGoods\' or a.rec_module = \'PromoteGoods\')'.
					'   AND a.order_id = '.$order_id.
					' order by a.rec_module';
		
		//dump($sql_str);
		$OrderDetailList = $model->query($sql_str, false);
		$this->assign ( "orderGoodsList", json_encode($OrderDetailList) );			
		//dump($OrderDetailList);
	}
	
	function add() {
		
		$this->assign ( 'isNew', json_encode(true) );
		
		$model = D ("Order");
		$order_id = $_REQUEST ['order_id'];
		$order_vo = $model->getById ( $order_id );
		//下单时间
		$order_vo['create_time'] = timeToLocalStr($order_vo ['create_time'], 'Y-m-d h:i:s');

		//收款状态：0:未发货; 1:部分发货; 2:全部发货; 3:部分退货; 4:全部退货
		$order_vo['goods_status'] = L("ORDER_GOODS_STATUS_".$order_vo['goods_status']);
		
		$this->assign ( 'order_vo', $order_vo );


				
		
		$this->commAssign($model, $order_id, -1);
		
		//dump($Account->getLastSql());
		//dump($AccountList);
		//acttime
		$Consignment = D ( $this->name );
		$vo = $Consignment->create();
		$vo['order_id'] = $order_id;
		
		$vo['delivery_id'] = $order_vo['delivery_id'];
		$vo['delivery_fee'] = $order_vo['delivery_fee'];
		$vo['protect'] = $order_vo['protect'];
		$vo['protect_fee'] = $order_vo['protect_fee'];
		
		$vo['region_lv1'] = $order_vo['region_lv1'];
		$vo['region_lv2'] = $order_vo['region_lv2'];
		$vo['region_lv3'] = $order_vo['region_lv3'];
		$vo['region_lv4'] = $order_vo['region_lv4'];
		
		
		$vo['consignee'] = $order_vo['consignee'];
		$vo['mobile_phone'] = $order_vo['mobile_phone'];
		$vo['fix_phone'] = $order_vo['fix_phone'];
		$vo['zip'] = $order_vo['zip'];
		$vo['address'] = $order_vo['address'];
		$vo['email'] = $order_vo['email'];
		$vo['memo'] = $order_vo['memo'];

		
		$this->assign ( 'vo', $vo );
		
		$this->display ( 'edit' );
	}
	
   function save()
    {
		$model	=	D('OrderReConsignment');
        if(false === $vo = $model->create()) {
        	$this->error($model->getError());
        }
        
        $vo['create_time'] = gmtTime();
        //保存当前数据对象
        $id = $model->add($vo);
        
        if($id) { //保存成功
            //成功提示
        	$order_goods_id_list = $_REQUEST['order_goods_id_list'];
        	$cd_num_list = $_REQUEST['cd_num_list'];
        	
        	$this->saveConsignmentGoods($vo['order_id'], $id, $order_goods_id_list, $cd_num_list);
                    
			$this->assign ('jumpUrl', u('Order/show', array('id'=>intval($vo['order_id']))));
            $this->success(L('ADD_SUCCESS'));
        }else {
            //失败提示
            $this->error(L('ADD_FAIL'));
        }
    }	
	
	function saveConsignmentGoods($order_id,
							 $order_re_consignment_id, 
							 $order_goods_id_list, 
							 $num_list) {

		$model = D("Order");							 	
		$sql_str = 'delete from '.C("DB_PREFIX").'order_re_consignment_goods where order_re_consignment_id = '.$order_re_consignment_id;
		$model->execute($sql_str);							 	
	
		$total = count($order_goods_id_list);
		for($i=0;$i<$total;$i++){
			if ($num_list[$i] > 0){
				$sql_str = 'insert into '.C("DB_PREFIX").'order_re_consignment_goods(order_re_consignment_id,order_goods_id, number) values('.
					$order_re_consignment_id.','.
					'\''.$order_goods_id_list[$i].'\','.
					'\''.$num_list[$i].'\''.
					')';
				//dump($sql_str);
				$model->execute($sql_str);				
			}
		}			
		
		//已发货 统计
		order_send_num($order_id);
		
		//增加库存
		order_inc_stock($order_re_consignment_id);
		
		$order_vo = $model->getById ( $order_id );
		//未发货数量和
		$sql_str = 'select sum(number - send_number) as num  from '.C("DB_PREFIX").'order_goods where order_id = '.$order_id;
		$num = $model->query($sql_str, false);
		//收款状态：0:未发货; 1:部分发货; 2:全部发货; 3:部分退货; 4:全部退货
		if (intval($num[0]['num']) == 0){
			$order_vo["goods_status"] = 2;
		}else{
			$order_vo["goods_status"] = 1;
		}
		
		$model->save($order_vo);
		
		//
		
	}    
} 
?>