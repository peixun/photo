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

// 抽奖
class LotteryModel extends CommonModel {
	protected $_validate = array(
		array('name','require','名称不能为空'), 
	);
	
	protected $_auto = array ( 		
		array('status',1),  // 新增的时候把status字段设置为1
		array('create_time','gmtTime',1,'function'), // 对create_time字段在插入的时候写入当前时间戳	
		array('update_time','gmtTime',3,'function'), 
		array('begin_time','localStrToTimeMin',3,'function'), 	   
		array('end_time','localStrToTimeMax',3,'function'),
	);
	
	public function getLotteryLang()
	{
		$langSet = C('DEFAULT_LANG');	
		$files = scandir(getcwd()."/admin/Lang/".$langSet."/lottery/");
		foreach($files as $file)
		{
			if($file!='.'&&$file!='..')
			{
				L(include LANG_PATH.$langSet.'/lottery/'.$file);
			}
		}
	}
	
	public function getLotteryType()
	{
		$type = array();
		$files = scandir(getcwd()."/admin/Lib/Lottery/");
		foreach($files as $file)
		{
			if($file!='.'&&$file!='..')
			{
				$name = str_replace("Lottery.class.php","",$file);
				if(!empty($name))
				{
					$type[] = array("class"=>$name,"name"=>L(strtoupper($name)."_NAME"));
				}
			}
		}
		
		return $type;
	}
}
?>