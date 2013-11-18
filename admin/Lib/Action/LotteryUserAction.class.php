<?php
// +----------------------------------------------------------------------
// | Fanwe 多语商城建站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: awfigq(awfigq@qq.com)
// +----------------------------------------------------------------------

// 抽奖结果
class LotteryUserAction extends CommonAction
{
	public function __construct()
	{
		$lottery_id = intval(Session::get('lottery_id'));
		if($lottery_id==0||isset($_REQUEST['lottery_id']))
		{
			$lottery_id = intval($_REQUEST['lottery_id']);
		}
		
		Session::set('lottery_id',$lottery_id);
	
		parent::__construct();
		
		if(!Session::is_set('lottery_id'))
			$this->redirect('Lottery/index');
			
		$this->assign('lottery_id',Session::get('lottery_id'));
	}
	
	public function index()
	{
		$lottery_id = $this->get("lottery_id");
		$index = intval($_REQUEST['index']);
		$user_name = trim($_REQUEST['user_name']);
		$status = trim($_REQUEST['status']);
		
		$where.=" and lu.lottery_id = $lottery_id";
		$parameter .= "lottery_id=" . $lottery_id."&";
		
		if($index > 0)
		{
			$this->assign("index",$index);
			$where.=" and lu.lottery_item_index = $index";
			$parameter .= "index=" .$index."&";
		}
		
		if(!empty($user_name))
		{
			$this->assign("user_name",$user_name);
			$where.=" and (u.user_name like '%$user_name%')";
			$parameter .= "user_name=" . urlencode($user_name)."&";
		}
		
		if($status != "" && $status >= 0)
		{
			$where.=" and lu.status = $status";
			$parameter .= "status=" . $status ."&";
			$this->assign("status",$status);
		}
		else
		{
			$this->assign("status",-1);
		}
		
		$sql_str = 'SELECT lu.id,l.name as lottery_name,li.name as item_name,u.user_name,lu.create_time,lu.status,lu.update_time '.
					'FROM '.C("DB_PREFIX").'lottery_user as lu '.
					'left join '.C("DB_PREFIX").'lottery_items as li on li.id = lu.lottery_id and li.`index` = lu.lottery_item_index '.
					"left join ".C("DB_PREFIX")."lottery as l on l.id = lu.lottery_id ".
					"left join ".C("DB_PREFIX")."user as u on u.id = lu.user_id ".
					"where lu.id is not null and lu.lottery_item_index > -1 $where group by lu.id";
		
		$model = M();
        $voList = $this->_Sql_list($model, $sql_str, "&".$parameter, 'id', false);
		
		$lotteryItems = D("LotteryItems")->where("id = $lottery_id")->order("`index` asc")->findAll();
		$this->assign("lotteryItems",$lotteryItems);
		
		$this->display ();
		return;
	}
	
	public function emptydata()
	{
		$lottery_id = $this->get("lottery_id");
		D(MODULE_NAME)->where("lottery_id = $lottery_id")->delete();
		$this->success("成功清空数据");
	}
	
	public function updateLotteryUserStatus()
	{
		$id = intval($_REQUEST['id']);
		$lotteryUser = D(MODULE_NAME)->where('id='.$id)->find();
		$now = gmtTime();
		
		if($lotteryUser['status'] == 0)
			$status = 1;
		else
			$status = 0;
		
		if($this->lotteryHandler($lotteryUser,$status,$now))
		{
			if($status == 0)
			{
				$result['html'] = "设为已发放";
				$result['color'] = "#f00";
				$result['date'] = "";
			}
			else
			{
				$result['html'] = "取消发放";
				$result['color'] = "#333";
				$result['date'] = toDate($now);
			}
			
			echo json_encode($result);
		}
	}
	
	public function send()
	{
		$ids = $_REQUEST["ids"];
		if(!empty($ids))
		{
			$condition['id'] = array('in',explode(',',$ids));
			$condition['status'] = 0;
			$lotteryUsers = D(MODULE_NAME)->where($condition)->findAll();
			{
				$now = gmtTime();
				
				foreach($lotteryUsers as $lotteryUser)
				{
					$this->lotteryHandler($lotteryUser,1,$now);
				}
			}
		}
		$this->saveLog(1);
		$this->success("发放成功");
	}
	
	public function sendAll()
	{
		$condition['lottery_id'] = $this->get("lottery_id");
		$condition['status'] = 0;
		$condition['lottery_item_index'] = array("gt",-1);
		$lotteryUsers = D(MODULE_NAME)->where($condition)->findAll();
		{
			$now = gmtTime();
			
			foreach($lotteryUsers as $lotteryUser)
			{
				$this->lotteryHandler($lotteryUser,1,$now);
			}
		}
		$this->saveLog(1);
		$this->success("发放成功");
	}
	
	public function clear()
	{
		$ids = $_REQUEST["ids"];
		if(!empty($ids))
		{
			$condition['id'] = array('in',explode(',',$ids));
			$condition['status'] = 1;
			$lotteryUsers = D(MODULE_NAME)->where($condition)->findAll();
			{
				$now = gmtTime();
				
				foreach($lotteryUsers as $lotteryUser)
				{
					$this->lotteryHandler($lotteryUser,0,$now);
				}
			}
		}
		$this->saveLog(1);
		$this->success("取消成功");
	}
	
	public function clearAll()
	{
		$condition['lottery_id'] = $this->get("lottery_id");
		$condition['status'] = 1;
		$condition['lottery_item_index'] = array("gt",-1);
		$lotteryUsers = D(MODULE_NAME)->where($condition)->findAll();
		{
			$now = gmtTime();
			
			foreach($lotteryUsers as $lotteryUser)
			{
				$this->lotteryHandler($lotteryUser,0,$now);
			}
		}
		$this->saveLog(1);
		$this->success("取消成功");
	}
	
	private function lotteryHandler($lotteryUser,$status,$now)
	{
		if($status == 0)
			$now1= 0;
		else
			$now1= $now;
			
		if(D(MODULE_NAME)->where('id='.$lotteryUser['id'])->setField(array("status","update_time"),array($status,$now1)))
		{
			$lotteryItem = D("LotteryItems")->where("id=$lotteryUser[lottery_id] and `index` = $lotteryUser[lottery_item_index]")->find();
			switch($lotteryItem['type'])
			{
				case 1:
				{
					return $this->lotteryEcvHandler($lotteryItem,$lotteryUser,$status,$now);
				}
				break;
				
				case 2:
				{
					return $this->lotteryScoreHandler($lotteryItem,$lotteryUser,$status,$now);
				}
				break;
				
				case 3:
				{
					return $this->lotteryMoneyHandler($lotteryItem,$lotteryUser,$status,$now);
				}
				break;
			}
			return true;
		}
		else
			return false;
	}
	
	//代金券
	private function lotteryEcvHandler($lotteryItem,$lotteryUser,$status,$now)
	{
		if($status == 1)
		{
			$ecvType = M("EcvType")->where("id=".$lotteryItem['val'])->find();
			
			if($ecvType)
			{
				$data['ecv_type'] = $lotteryItem['val'];
				$data['status'] = 0;
				
				$tempsn = unpack('H8',str_shuffle(sha1(uniqid())));
				$data['sn'] = $tempsn[1];
				
				$data['user_id'] = intval($lotteryUser['user_id']);
				
				$password = unpack('H8',str_shuffle(md5(uniqid())));
				$data['password'] = $password[1];
									
				$data['type'] = $ecvType['type'];
				$data['use_count'] = $ecvType['use_count'];
				$ecv_id = D("Ecv")->add($data);
				
				D(MODULE_NAME)->where('id='.$lotteryUser['id'])->setField("rec_id",$ecv_id);
				
				return true;
			}
			else
				return false;
		}
		else
		{
			D("Ecv")->where("id = ".$lotteryUser['rec_id'])->delete();
			return true;
		}
	}
	
	//积分
	private function lotteryScoreHandler($lotteryItem,$lotteryUser,$status,$now)
	{
		$lotteryName = D("Lottery")->where("id = ".$lotteryItem['id'])->getField("name");
		
		$log['user_id'] = $lotteryUser['user_id'];
		
		$log['create_time'] = $now;
		$log['rec_id'] = $lotteryUser['id'];
		$log['rec_module'] = "LotteryUser";
		
		if($status == 1)
		{
			D("User")->where("id = ".$lotteryUser['user_id'])->setInc("score",intval($lotteryItem['val']));
			$log['score'] = intval($lotteryItem['val']);
			$log['memo_'.DEFAULT_LANG_ID] = "获取 抽奖活动【".$lotteryName."】【".$lotteryItem['name']."】";
			D("UserScoreLog")->add($log); 
		}
		else
		{
			D("User")->where("id = ".$lotteryUser['user_id'])->setDec("score",intval($lotteryItem['val']));
			$log['score'] = intval("-".$lotteryItem['val']);
			$log['memo_'.DEFAULT_LANG_ID] = "取消 抽奖活动【".$lotteryName."】【".$lotteryItem['name']."】";
			D("UserScoreLog")->add($log);
		}
		
		return true;
	}
	
	//余额
	private function lotteryMoneyHandler($lotteryItem,$lotteryUser,$status,$now)
	{
		$lotteryName = D("Lottery")->where("id = ".$lotteryItem['id'])->getField("name");
		$log['user_id'] = $lotteryUser['user_id'];
		
		$log['create_time'] = $now;
		$log['rec_id'] = $lotteryUser['id'];
		$log['rec_module'] = "LotteryUser";
		
		if($status == 1)
		{
			D("User")->where("id = ".$lotteryUser['user_id'])->setInc("money",intval($lotteryItem['val']));
			$log['money'] = intval($lotteryItem['val']);
			$log['memo_'.DEFAULT_LANG_ID] = "获取 抽奖活动【".$lotteryName."】【".$lotteryItem['name']."】";
			D("UserMoneyLog")->add($log); 
		}
		else
		{
			D("User")->where("id = ".$lotteryUser['user_id'])->setDec("money",intval($lotteryItem['val']));
			$log['money'] = intval("-".$lotteryItem['val']);
			$log['memo_'.DEFAULT_LANG_ID] = "取消 抽奖活动【".$lotteryName."】【".$lotteryItem['name']."】";
			D("UserMoneyLog")->add($log);
		}
		
		return true;
	}
}

function getLotteryUserStatus($status,$id)
{
	if($status == 0)
		return "<a href='javascript:;' onclick='updateLotteryUserStatus($id,this)' style='color:#f00;'>设为已发放</a>";
	else
		return "<a href='javascript:;' onclick='updateLotteryUserStatus($id,this)' style='color:#333;'>取消发放</a>";
}
?>