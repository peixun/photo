<?php
//FanWe服务
require_once('init.php');

class FanweServices
{
	function __construct()
	{
		
	}
	
	function GetLotteryByID($id,$user_id)
	{
		$now = gmtTime();
		
		$result = array("type"=>"error","message"=>"","value"=>"","frequency"=>true);
		
		$lottery = $GLOBALS['db']->getRow("SELECT * FROM ".$GLOBALS['db_config']['DB_PREFIX']."lottery WHERE id='$id' and status = 1");
		
		if($lottery)
		{
			if($lottery['begin_time'] > 0 && $lottery['begin_time'] > $now)
			{
				$result['message'] = "抽奖活动还未开始，活动开始时间：".toDate($lottery['begin_time']);
				return $result;
			}
				
			if($lottery['end_time'] > 0 && $lottery['end_time'] < $now)
			{
				$result['message'] = "抽奖活动已经结束，活动结束时间：".toDate($lottery['end_time']);
				return $result;
			}
			
			$user = $GLOBALS['db']->getRow("SELECT * FROM ".$GLOBALS['db_config']['DB_PREFIX']."user WHERE id='$user_id' and status = 1");
			
			if($user)
			{
				if($lottery['integral_min'] > 0)
				{
					if($lottery['integral_min'] > $user['score'])
					{
						$result['message'] = "参加此次抽奖需要 ".$lottery['integral_min']." 以上的积分!";
						return $result;
					}
				}
				
				if(intval($lottery['integral_sub']) > 0)
				{
					$sub = intval($lottery['integral_sub']);
					$log = "参加 抽奖活动【".$lottery['name']."】";
					
					$sql = "UPDATE ".$GLOBALS['db_config']['DB_PREFIX']."user SET score = score - $sub WHERE id='$user_id'";
					$GLOBALS['db']->query($sql);
					
					$sql = "INSERT INTO ".$GLOBALS['db_config']['DB_PREFIX']."user_score_log(user_id,create_time,rec_id,rec_module,score,memo_".$GLOBALS['langItem']['id'].") VALUES($user_id,$now,$id,'Lottery','-$sub','$log')";
					$GLOBALS['db']->query($sql);
				}
				
				if(!empty($lottery['goods_ids']))
				{
					$sql = "select og.rec_id from ".$GLOBALS['db_config']['DB_PREFIX']."order_goods as og ".
							"left join ".$GLOBALS['db_config']['DB_PREFIX']."order as o on o.id = og.order_id ".
							"where o.money_status = 2 and o.user_id = $user_id and og.rec_id in (".$lottery['goods_ids'].") and og.id is not null group by og.rec_id";
					
					$user_goods = $GLOBALS['db']->getAll($sql);
					
					if(count($user_goods) == 0)
					{
						$result['message'] = "你还没有购买此次抽奖指定的团购商品!";
						return $result;
					}
				}
				
				if(!empty($lottery['user_group']))
				{
					if(strpos(",".$lottery['user_group'].",",",".$user['group_id'].",") === false)
					{
						$result['message'] = "你所在的会员组，不能参加此次抽奖!";
						return $result;
					}
				}
				
				//已经抽奖次数
				$lotteryFrequency = intval($GLOBALS['db']->getOne("SELECT count(*) FROM ".$GLOBALS['db_config']['DB_PREFIX']."lottery_user WHERE lottery_id='$id' and user_id = '$user_id'"));
				
				switch($lottery['frequency_type'])
				{
					case 0:
					{
						$totalFrequency = $lottery['frequency'];
						
						if($lotteryFrequency >= $totalFrequency)
						{
							$result['message'] = "对不起最多只能进行 ".$totalFrequency." 次抽奖!";
							
							$result['frequency'] = false;
							return $result;
						}
					}
					break;
					
					case 1:
					{
						if(empty($lottery['goods_ids']))
						{
							$sql = "select og.rec_id from ".$GLOBALS['db_config']['DB_PREFIX']."order_goods as og ".
									"left join ".$GLOBALS['db_config']['DB_PREFIX']."order as o on o.id = og.order_id ".
									"where o.money_status = 2 and o.user_id = $user_id and og.id is not null group by og.rec_id";
									
							$user_goods = $GLOBALS['db']->getAll($sql);
						}
						
						$buyCount = count($user_goods);
						$totalFrequency = floor($buyCount / $lottery['frequency_unit']) * $lottery['frequency'];
						
						if($lotteryFrequency >= $totalFrequency)
						{
							if($totalFrequency > 0)
								$result['message'] = "对不起你参加团购的次数最多只能进行 ".$totalFrequency." 次抽奖!(此次抽奖活动中，多次购买同一团购，只算作一次)";
							else
								$result['message'] = "对不起你参加团购的次数未能达到要求，不能参加此次抽奖!(此次抽奖活动中，多次购买同一团购，只算作一次)";
							
							$result['frequency'] = false;
							return $result;
						}
					}
					break;
					
					case 2:
					{
						if(!empty($lottery['goods_ids']))
							$where = "and og.rec_id in (".$lottery['goods_ids'].")";
							
						$sql = "select sum(og.number) from ".$GLOBALS['db_config']['DB_PREFIX']."order_goods as og ".
								"left join ".$GLOBALS['db_config']['DB_PREFIX']."order as o on o.id = og.order_id ".
								"where o.money_status = 2 and o.user_id = $user_id $where and og.id is not null and og.number is not null";
						
						$goodsCount = $GLOBALS['db']->getOne($sql);
						$totalFrequency = floor($goodsCount / $lottery['frequency_unit']) * $lottery['frequency'];
							
						if($lotteryFrequency >= $totalFrequency)
						{
							if($totalFrequency > 0)
								$result['message'] = "对不起你购买指定团购商品的总数量最多只能进行 ".$totalFrequency." 次抽奖!";
							else
								$result['message'] = "对不起你购买指定团购商品的总数量未能达到要求，不能参加此次抽奖!";
							
							$result['frequency'] = false;
							return $result;
						}
					}
					break;
				}
				
				if(file_exists(ROOT_PATH."services/Lottery/".$lottery['lottery_type']."Lottery.class.php"))
				{
					require_once("Lottery/".$lottery['lottery_type']."Lottery.class.php");
					$lotteryName = $lottery['lottery_type']."Lottery";
					$lotteryClass = new $lotteryName();
					$lotteryItem = $lotteryClass->Get($id);
					
					if($GLOBALS['db']->query("INSERT INTO ".$GLOBALS['db_config']['DB_PREFIX']."lottery_user(lottery_id,lottery_item_index,user_id,ip,create_time) VALUES($id,".$lotteryItem['index'].",$user_id,'".$this->GetIP()."',".gmtTime().")"))
					{
						$result['value'] = $lotteryItem;
						$result['type'] = "success";
					}
					else
					{
						$result['message'] = "抽奖发生错误，请联系管理员！";
						return $result;
					}
				}
			}
			else
			{
				$result['message'] = "请先登陆后，再进行抽奖!";
				return $result;	
			}
		}
		else
		{
			$result['message'] = "没有找到该抽奖活动！";
			return $result;
		}
		
		return $result;
	}

	function GetIP()
	{
        if (getenv('HTTP_CLIENT_IP'))
		{
			$ip = getenv('HTTP_CLIENT_IP'); 
		}
		elseif (getenv('HTTP_X_FORWARDED_FOR'))
		{ 
			//获取客户端用代理服务器访问时的真实ip 地址
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		}
		elseif (getenv('HTTP_X_FORWARDED'))
		{ 
			$ip = getenv('HTTP_X_FORWARDED');
		}
		elseif (getenv('HTTP_FORWARDED_FOR'))
		{
			$ip = getenv('HTTP_FORWARDED_FOR'); 
		}
		elseif (getenv('HTTP_FORWARDED'))
		{
			$ip = getenv('HTTP_FORWARDED');
		}
		else
		{ 
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		
		return addslashes($ip);
	}
}
?>