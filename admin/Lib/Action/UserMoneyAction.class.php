<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 *///会员资金
class UserMoneyAction extends CommonAction{
	public function incharge()
	{
    	$user_name = $_REQUEST['user_name'];
    	$user_id = intval(M("User")->where("user_name='".$user_name."'")->getField("id")); 	
    	$payment_list = M("Payment")->where("status=1")->findAll();
    	$this->assign("payment_list",$payment_list);
    	
		//列表过滤器，生成查询Map对象
		$map = $this->_search ("UserIncharge");
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		if($user_name&&$user_name!='')
		{
			$map['user_id'] = $user_id;
		}
		$model = D ("UserIncharge");
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
		return;		
	}
	public function resumeIncharge()
	{
	//恢复指定记录

		$model = D ("UserIncharge");
		$pk = $model->getPk ();
		$id = $_GET [$pk];
		$condition = array ($pk => array ('in', $id ) );
		if (false !== $model->resume ( $condition )) {
			$incharge_info = $model->getById($id);
//			$user_info = D("User")->getById($incharge_info['user_id']);
//			D("User")->where("id=".$incharge_info['user_id'])->setField("money",$user_info['money']+$incharge_info['money']);
			
			user_money_log($incharge_info['user_id'],$id,'UserIncharge',$incharge_info['money'],'#INCHARGE_RESUME_SUCCESS#');
			
			//add by chenfq 2010-06-5  记录帐户资金变化明细 begin
			if ($incharge_info){
				payment_money_log($incharge_info['payment'], 
								  $_SESSION[C('USER_AUTH_KEY')], 
								  $id, 
								  'UserIncharge', 
								  $incharge_info['money'], 
								  $_SESSION['adm_name'].'管理员后台“确认”会员冲值金额：'.$incharge_info['money'], 
								  false, 
								  'Admin', 
								  '', 
								  $_SESSION['adm_name']);	
			}				  
			//add by chenfq 2010-06-5  记录帐户资金变化明细 end
							  
			//send_orderpaid_sms($id);
			send_userincharge_sms($id);
			$model -> where("id=".$id)->setField('update_time',gmtTime());
			$sn = $model->where("id=".$id)->getField('sn');
			$msg = "充值单：".$sn." 确定成功";
			$this->saveLog(1,$id,$msg);
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			
			$this->success ( L('CONFIRM_SUCCESS') );
		} else {
			$sn = $model->where("id=".$id)->getField('sn');
			$msg = "充值单：".$sn." 确定失败";
			$this->saveLog(0,0,$msg);
			$this->error ( L('CONFIRM_FAILED') );
		}
	}
	public function forbidIncharge() {
		$model = D ("UserIncharge");
		$pk = $model->getPk ();
		$id = $_REQUEST [$pk];
		$condition = array ($pk => array ('in', $id ) );
		$list=$model->forbid ( $condition );
		if ($list!==false) {	
			$incharge_info = $model->getById($id);
			user_money_log($incharge_info['user_id'],$id,'UserIncharge',"-".$incharge_info['money'],'#INCHARGE_FORBID_SUCCESS#');
			//add by chenfq 2010-06-5  记录帐户资金变化明细 begin
			if ($incharge_info){
				payment_money_log($incharge_info['payment'], 
								  $_SESSION[C('USER_AUTH_KEY')], 
								  $id,
								  'UserIncharge', 
								  $incharge_info['money'] * -1, 
								  $_SESSION['adm_name'].'管理员后台“取消”会员冲值金额：'.$incharge_info['money'], 
								  false, 
								  'Admin', 
								  '', 
								  $_SESSION['adm_name']);	
			}				  
			//add by chenfq 2010-06-5  记录帐户资金变化明细 end					
			
			$sn = $model->where("id=".$id)->getField('sn');
			$msg = "充值单：".$sn." 取消成功";
			$this->saveLog(1,$id,$msg);
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( L('CANCEL_SUCCESS') );
		} else {
			$sn = $model->where("id=".$id)->getField('sn');
			$msg = "充值单：".$sn." 取消失败";
			$this->saveLog(0,0,$msg);
			$this->error  (  L('CANCEL_FAILED') );
		}
	}
	public function  foreverdeleteIncharge()
	{
		$name="UserIncharge";
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			$sn = $model->where("id=".$id)->getField('sn');
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if (false !== $model->where ( $condition )->delete ()) {
					//echo $model->getlastsql();
					
					$msg = "充值单：".$sn." 删除成功";
					$this->saveLog(1,$id,$msg);
					$this->success (L('DEL_SUCCESS'));
				} else {
					
					$msg = "充值单：".$sn." 删除失败";
					$this->saveLog(0,0,$msg);
					$this->error (L('DEL_FAILED'));
				}
			} else {				
				$msg = "充值单删除失败";
				$this->saveLog(0,0,$msg);
				$this->error ( L('INVALID_OP') );
			}
		}
		$this->forward ();
	}
	
	//提现
	public function uncharge()
	{
		$user_name = $_REQUEST['user_name'];
    	$user_id = intval(M("User")->where("user_name='".$user_name."'")->getField("id")); 	
		//列表过滤器，生成查询Map对象
		$map = $this->_search ("UserUncharge");
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		if($user_name&&$user_name!='')
		{
			$map['user_id'] = $user_id;
		}
		$model = D ("UserUncharge");
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
		return;		
	}
	public function resumeUncharge()
	{
	//恢复指定记录

		$model = D ("UserUncharge");
		$pk = $model->getPk ();
		$id = $_GET [$pk];
		$condition = array ($pk => array ('in', $id ) );
		
		$uncharge_info = $model->getById($id);
		$user_info = D("User")->getById($uncharge_info['user_id']);
		if($uncharge_info['money'] > $user_info['money'])
		{
			$sn = $model->where("id=".$id)->getField('sn');
			$msg = "提现单：".$sn." 确定失败";
			$this->saveLog(0,$id,$msg);
			$this->error("用户余额不足");
		}
			
		if (false !== $model->resume ( $condition )) {
			
			user_money_log($uncharge_info['user_id'],$id,'UserIncharge','-'.$uncharge_info['money'],'#UNCHARGE_RESUME_SUCCESS#');
			
			
			//$money = $user_info['money']- $uncharge_info['money']>0?$user_info['money']- $uncharge_info['money']:0;
			//D("User")->where("id=".$uncharge_info['user_id'])->setField("money",$money);
			
			$model -> where("id=".$id)->setField('update_time',gmtTime());
			$sn = $model->where("id=".$id)->getField('sn');
			$msg = "提现单：".$sn." 确定成功";
			$this->saveLog(1,$id,$msg);
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			
			$this->success ( L('CONFIRM_SUCCESS') );
		} else {
			$sn = $model->where("id=".$id)->getField('sn');
			$msg = "提现单：".$sn." 确定失败";
			$this->saveLog(0,$id,$msg);
			$this->error ( L('CONFIRM_FAILED') );
		}
	}
	public function forbidUncharge() {
		$model = D ("UserUncharge");
		$pk = $model->getPk ();
		$id = $_REQUEST [$pk];
		$condition = array ($pk => array ('in', $id ) );
		$list=$model->forbid ( $condition );
		if ($list!==false) {

			$uncharge_info = $model->getById($id);
			
			user_money_log($uncharge_info['user_id'],$id,'UserIncharge',$uncharge_info['money'],'#UNCHARGE_FORBID_SUCCESS#');
			
			//$user_info = D("User")->getById($uncharge_info['user_id']);
			//D("User")->where("id=".$uncharge_info['user_id'])->setField("money",$user_info['money']+$uncharge_info['money']);
			
					
			$sn = $model->where("id=".$id)->getField('sn');
			$msg = "提现单：".$sn." 取消成功";
			$this->saveLog(1,$id,$msg);
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( L('CANCEL_SUCCESS') );
		} else {
			$sn = $model->where("id=".$id)->getField('sn');
			$msg = "提现单：".$sn." 取消失败";
			$this->saveLog(0,$id,$msg);
			$this->error  (  L('CANCEL_FAILED') );
		}
	}
	public function  foreverdeleteUncharge()
	{
		$name="UserUncharge";
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			$sn = $model->where("id=".$id)->getField('sn');
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if (false !== $model->where ( $condition )->delete ()) {
					//echo $model->getlastsql();
					
					$msg = "提现单：".$sn." 删除成功";
					$this->saveLog(1,$id,$msg);
					$this->success (L('DEL_SUCCESS'));
				} else {
					
					$msg = "提现单：".$sn." 删除失败";
					$this->saveLog(0,$id,$msg);
					$this->error (L('DEL_FAILED'));
				}
			} else {
				$msg = "提现单删除失败";
				$this->saveLog(0,0,$msg);
				$this->error ( L('INVALID_OP') );
			}
		}
		$this->forward ();
	}
	
	public function user_money_log()
	{
		//列表过滤器，生成查询Map对象
		$map = array ();
		$map['user_name'] = $_REQUEST['user_name'];
		$map['begin_create_time'] = $_REQUEST['begin_create_time'];
		$map['end_create_time'] = $_REQUEST['end_create_time'];
		
		$this->assign ( 'user_name', $map['user_name'] );
		$this->assign ( 'begin_create_time', $map['begin_create_time'] );
		$this->assign ( 'end_create_time', $map['end_create_time'] );
				
		$begin_create_time = ! empty ($map['begin_create_time']) ? localStrToTime($map['begin_create_time']) : 0;
		$end_create_time = ! empty ($map['end_create_time']) ? localStrToTime($map['end_create_time']) : 0;
		
		
		$sum_money = 0;
		
		//资金管理
		$sql = "select a.*, memo_".DEFAULT_LANG_ID." as memo, b.user_name from ".C("DB_PREFIX")."user_money_log a".
			   " LEFT OUTER JOIN ".C("DB_PREFIX")."user b ON b.id = a.user_id".	
			   " where a.money <> 0 and b.id is not null ";
		
		$sql_str = "select sum(a.money) as money from ".C("DB_PREFIX")."user_money_log a".
			   " LEFT OUTER JOIN ".C("DB_PREFIX")."user b ON b.id = a.user_id".	
			   " where a.money <> 0 and b.id is not null ";
					
		
		if ((!empty($map['user_name'])) && ($map['user_name'] <>'')){
			$sql .= " and b.user_name like '%".$map['user_name']."%'";
			
			$sql_str .= " and b.user_name like '%".$map['user_name']."%'";
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
		
		
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key=" . urlencode ( $val ) . "&";
			}
		}		
		
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
	
	public function user_score_log()
	{
		//列表过滤器，生成查询Map对象
		$map = array ();
		$map['user_name'] = $_REQUEST['user_name'];
		$map['begin_create_time'] = $_REQUEST['begin_create_time'];
		$map['end_create_time'] = $_REQUEST['end_create_time'];
		
		$this->assign ( 'user_name', $map['user_name'] );
		$this->assign ( 'begin_create_time', $map['begin_create_time'] );
		$this->assign ( 'end_create_time', $map['end_create_time'] );
				
		$begin_create_time = ! empty ($map['begin_create_time']) ? localStrToTime($map['begin_create_time']) : 0;
		$end_create_time = ! empty ($map['end_create_time']) ? localStrToTime($map['end_create_time']) : 0;
		
		
		$sum_score = 0;
		
		//积分管理
		$sql = "select a.*, memo_".DEFAULT_LANG_ID." as memo, b.user_name from ".C("DB_PREFIX")."user_score_log a".
			   " LEFT OUTER JOIN ".C("DB_PREFIX")."user b ON b.id = a.user_id".	
			   " where a.score <> 0 and b.id is not null ";
		
		$sql_str = "select sum(a.score) as score from ".C("DB_PREFIX")."user_score_log a".
			   " LEFT OUTER JOIN ".C("DB_PREFIX")."user b ON b.id = a.user_id".	
			   " where a.score <> 0 and b.id is not null ";
					
		
		if ((!empty($map['user_name'])) && ($map['user_name'] <>'')){
			$sql .= " and b.user_name like '%".$map['user_name']."%'";
			
			$sql_str .= " and b.user_name like '%".$map['user_name']."%'";
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
			$sum_score = $tmp[0]['score'];
		}
		
		$this->assign ( 'sum_score', $sum_score);
		
		
		foreach ( $map as $key => $val ) {
			//dump($key);
			if ((!is_array($val)) && ($val <> '')){
				$parameter .= "$key='" . urlencode ( $val ) . "'&";
			}
		}		
		
		//dump($sql);
        $model = D($this->name);		
        $voList = $this->_Sql_list($model, $sql, $parameter, 'create_time', true);
        
		foreach($voList as $k=>$v)
		{
			$sum_score = $sum_score + $v['score'];
			if($v['score']>=0)
			{
				//$list[$k]['dec_score'] = '';
				$voList[$k]['inc_score'] = abs($v['score']);
			}
			else
			{
				//$list[$k]['inc_score'] = '';
				$voList[$k]['dec_score'] = abs($v['score']);
			}
			
			$voList[$k]['sum_score'] = $sum_score;
		}		
		$this->assign ( 'score_log_list', $voList);
				
		$this->display ();
		return;		
	}		
}
?>