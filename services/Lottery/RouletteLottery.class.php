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

// 轮盘抽奖
class RouletteLottery
{
	public function Get($id)
	{
		$lottery = array();
		
		$sql = "SELECT li. *,count(DISTINCT lu.id) as wcount from ".$GLOBALS['db_config']['DB_PREFIX']."lottery_items as li ".
				"LEFT JOIN ".$GLOBALS['db_config']['DB_PREFIX']."lottery_user AS lu ON lu.lottery_id = li.id AND lu.lottery_item_index = li.`index` AND lu.id IS NOT NULL ".
				"WHERE li.id = $id GROUP BY li.`index` ORDER BY li.`index` ASC";
		
		$items = $GLOBALS['db']->getAll($sql);
		
		$now = gmtTime();
		$tBTime = mktime(0, 0, 0, date("m")  , date("d"), date("Y")) - date('Z');
		$total = 10000;
		$winnings = array();
		
		$lotteryKeys = array();
		
		foreach($items as $key => $item)
		{
			if(intval($item['begin_time']) > $now)
				continue;
			
			$total_num = intval($item['total_num']);
			$day_num = intval($item['day_num']);
			
			if($total_num > -1)
			{
				if(intval($item['wcount']) >= $total_num)
					continue;
				else
				{
					if($day_num > 0)
					{
						$sql = "SELECT count(DISTINCT id) from ".$GLOBALS['db_config']['DB_PREFIX']."lottery_user where lottery_id = $id AND lottery_item_index = ".$item['index']." AND id IS NOT NULL AND create_time >= $tBTime";
						$dcount = intval($GLOBALS['db']->getOne($sql));
						
						if($dcount >= $day_num)
							continue;
					}
				}
			}
			
			$i = 0;
			for($i;$i < $item['probability'];$i++)
			{
				$lotteryKeys[] = $key;
			}
			
			if(intval($item['status']) == 1)
				$winnings[] = $item;
		}
		
		$empty = $total - count($lotteryKeys);
		
		$i = 0;
		for($i;$i < $empty;$i++)
		{
			$lotteryKeys[] = -1;
		}
		
		shuffle($lotteryKeys);
		
		$rand = mt_rand(0,$total - 1);

		$winningKey = $lotteryKeys[$rand];
		
		if($winningKey == -1 && count($winnings) > 0)
		{
			if(count($winnings) > 1)
			{
				$winningTotal = 0;
				$lotteryKeys = array();
				
				foreach($winnings as $key => $winning)
				{
					$i = 0;
					for($i;$i < $winning['probability'];$i++)
					{
						$lotteryKeys[] = $key;
					}
				}
				
				shuffle($lotteryKeys);
				
				$rand = mt_rand(0,$winningTotal - 1);
				$winningKey = $lotteryKeys[$rand];
				$winning = $winnings[$winningKey];
			}
			else
			{
				$winning = current($winnings);
			}
			
			$result['index'] = $winning['index'];
			$result['name'] = $winning['name'];
		}
		else
		{
			if($winningKey > -1)
			{
				$winning = $items[$winningKey];
				$result['index'] = $winning['index'];
				$result['name'] = $winning['name'];
			}
			else
			{
				$result['index'] = -1;
				$result['name'] = "还差一点就可以中奖拉！";
			}
		}
		
		return $result;
	}
}
?>