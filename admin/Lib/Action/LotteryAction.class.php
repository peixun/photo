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

// 抽奖
class LotteryAction extends CommonAction
{
	public function index()
	{
		D(MODULE_NAME)->getLotteryLang();
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		$model = D(MODULE_NAME);
		$this->_list($model,$map);
		$list = $this->get("list");
		foreach($list as $key => $item)
		{
			$list[$key]['type_name'] = L(strtoupper($item['lottery_type'])."_NAME");
		}
		$this->assign("list",$list);
		$this->assign("map",$map);
		$this->assign("name",$_REQUEST['name']);
		$this->display ();
	}
	
	public function add()
	{
		D(MODULE_NAME)->getLotteryLang();
		$types = D(MODULE_NAME)->getLotteryType();
		$this->assign ('types', $types);
		
		$user_group = D("UserGroup")-> where("status=1")-> findAll();
		$this->assign("user_group",$user_group);
		
		$this->display ();
	}
	
	public function insert()
	{
		$type = $_POST["lottery_type"];
		$lotteryName = $type."Lottery";
		if(empty($type) || !class_exists($lotteryName))
			$this->error("请选择正确的抽奖类型");
		
		$lotteryClass = new $lotteryName();
		
		$data['name'] = trim($_POST['name']);
		$data['desc'] = trim($_POST['desc']);
		
		$uplist = $this->uploadFile(0,'lottery');
		if($uplist && $uplist[0]['key'] == 'img_file')
		{
			$data['img'] = $uplist[0]['recpath'] . $uplist[0]['savename'];
			unset($uplist[0]);
		}
		
		$data['lottery_type'] = $type;
		$data['integral_min'] = intval($_POST['integral_min']);
		$data['integral_sub'] = intval($_POST['integral_sub']);
		$data['user_group'] = trim($_POST['user_group']);
		$data['goods_ids'] = trim($_POST['goods_ids']);
		$data['frequency_type'] = intval($_POST['frequency_type']);
		$data['frequency_unit'] = intval($_POST['frequency_unit']);
		$data['frequency'] = intval($_POST['frequency']);
		$data['begin_time'] =  trim($_POST['begin_time']);
		$data['end_time'] = trim($_POST['end_time']);
		$lottery = D(MODULE_NAME);
		
		if($lottery->create($data))
		{
			$lid = $lottery->add();
			if($lid > 0)
			{
				if(empty($_POST['goods_ids']))
				{
					$lg['lottery_id'] = $lid;
					$lg['goods_id'] = 0;
					D("LotteryGoods")->add($lg);
				}
				else
				{
					$goods_ids = explode(",",trim($_POST['goods_ids']));
					foreach($goods_ids as $gid)
					{
						$lg['lottery_id'] = $lid;
						$lg['goods_id'] = $gid;
						D("LotteryGoods")->add($lg);
					}
				}
				
				if(empty($_POST['user_group']))
				{
					$lug['lottery_id'] = $lid;
					$lug['user_group_id'] = 0;
					D("LotteryUserGroup")->add($lug);
				}
				else
				{
					$ug_ids = explode(",",trim($_POST['user_group']));
					foreach($ug_ids as $ugid)
					{
						$lug['lottery_id'] = $lid;
						$lug['user_group_id'] = $ugid;
						D("LotteryUserGroup")->add($lug);
					}
				}
				
				$langSet = eyooC('DEFAULT_LANG');
				$langItem = D("LangConf")->where("lang_name='$langSet'")->find();
		
				$tpl = trim($_POST['lottery_tpl']);
				
				$dir = $this->getRealPath()."/home/tpl/".$langItem['tmpl']."/Inc/lottery/";
				$tpl = $dir.strtolower($type)."_$id.tpl";
				
				if(!file_exists($dir))
				{
					if (@mkdir(rtrim($dir, '/'), 0777))
					{
						@chmod($dir, 0777);
					}
				}
				
				if(!file_put_contents($tpl,$tpl))
				{
					D(MODULE_NAME)->where("id = $lid")->delete();
					$this->error("写入模板文件失败，请检查home/tpl/".$langItem['tmpl']."/Inc/lottery目录权限");
				}
				
				$lotteryClass->Insert($lid,$this,$uplist);

				if($lotteryClass->result['type'] == "error")
					$this->error($lotteryClass->result['message']);
				else
					$this->success("成功创建抽奖活动");
			}
			else
			{
				$this->error("创建抽奖活动失败");
			}
		}
		else
		{
			$this->error($lottery->getError());
		}
	}
	
	public function edit()
	{
		$model = D(MODULE_NAME);
		$model->getLotteryLang();
		$id = $_REQUEST [$model->getPk ()];
		$vo = $model->getById($id);
		$vo['type_name'] = L(strtoupper($vo['lottery_type'])."_NAME");
		
		$user_group = D("UserGroup")-> where("status=1")-> findAll();
		
		if(!empty($vo['user_group']))
		{
			foreach($user_group as $key => $user)
			{
				if(strpos(",$vo[user_group],",",$user[id],") !== false)
					$user_group[$key]['selected'] = 1;
			}
		}
		$this->assign("user_group",$user_group);
		
		$name = 'name_'.DEFAULT_LANG_ID;
		$where['id']  = array('in',$vo['goods_ids']);
		$goodsList = D("Goods")->where($where)->field("id,$name as name")->findAll();
		$this->assign ('goodsList',$goodsList);
		
		$this->assign ( 'vo', $vo );
		$this->display ();
	}
	
	public function update()
	{
		$lottery = D(MODULE_NAME);
		$id = $_REQUEST [$lottery->getPk ()];
		$data = $lottery->getById($id);
		$type = $data['lottery_type'];
		$lotteryName = $type."Lottery";
		
		if(empty($type) || !class_exists($lotteryName))
			$this->error("请选择正确的抽奖类型");
		
		$lotteryClass = new $lotteryName();
		
		$data['name'] = trim($_POST['name']);
		$data['desc'] = trim($_POST['desc']);
		
		$uplist = $this->uploadFile(0,'lottery');

		if($uplist && $uplist[0]['key'] == 'img_file')
		{
			unlink($this->getRealPath().$data['img']);
			$data['img'] = $uplist[0]['recpath'] . $uplist[0]['savename'];
			unset($uplist[0]);
		}

		$data['integral_min'] = intval($_POST['integral_min']);
		$data['integral_sub'] = intval($_POST['integral_sub']);
		$data['user_group'] = trim($_POST['user_group']);
		$data['goods_ids'] = trim($_POST['goods_ids']);
		$data['frequency_type'] = intval($_POST['frequency_type']);
		$data['frequency_unit'] = intval($_POST['frequency_unit']);
		$data['frequency'] = intval($_POST['frequency']);
		$data['begin_time'] =  trim($_POST['begin_time']);
		$data['end_time'] = trim($_POST['end_time']);

		if($lottery->create($data))
		{
			if($lottery->save())
			{
				D("LotteryGoods")->where("lottery_id = $id")->delete();
				if(empty($_POST['goods_ids']))
				{
					$lg['lottery_id'] = $id;
					$lg['goods_id'] = 0;
					D("LotteryGoods")->add($lg);
				}
				else
				{
					$goods_ids = explode(",",trim($_POST['goods_ids']));
					foreach($goods_ids as $gid)
					{
						$lg['lottery_id'] = $id;
						$lg['goods_id'] = $gid;
						D("LotteryGoods")->add($lg);
					}
				}
				
				D("LotteryUserGroup")->where("lottery_id = $id")->delete();
				if(empty($_POST['user_group']))
				{
					$lug['lottery_id'] = $id;
					$lug['user_group_id'] = 0;
					D("LotteryUserGroup")->add($lug);
				}
				else
				{
					$ug_ids = explode(",",trim($_POST['user_group']));
					foreach($ug_ids as $ugid)
					{
						$lug['lottery_id'] = $id;
						$lug['user_group_id'] = $ugid;
						D("LotteryUserGroup")->add($lug);
					}
				}
				
				$langSet = eyooC('DEFAULT_LANG');
				$langItem = D("LangConf")->where("lang_name='$langSet'")->find();
		
				$tpl = trim($_POST['lottery_tpl']);
				
				$dir = $this->getRealPath()."/home/tpl/".$langItem['tmpl']."/Inc/lottery/";
				$tpl = $dir.strtolower($type)."_$id.tpl";
				
				if(!file_exists($dir))
				{
					if (@mkdir(rtrim($dir, '/'), 0777))
					{
						@chmod($dir, 0777);
					}
				}
				
				if(!file_put_contents($tpl,$tpl))
				{
					$this->error("写入模板文件失败，请检查home/tpl/".$langItem['tmpl']."/Inc/lottery目录权限");
				}
				
				$lotteryClass->Update($id,$this,$uplist);

				if($lotteryClass->result['type'] == "error")
					$this->error($lotteryClass->result['message']);
				else
				{
					$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
					$this->success("成功编辑抽奖活动：".$data['name']);
				}
				
			}
			else
			{
				$this->error("编辑抽奖活动失败");
			}
		}
		else
		{
			$this->error($lottery->getError());
		}
	}
	
	public function foreverdelete()
	{
		$langSet = eyooC('DEFAULT_LANG');
		$langItem = D("LangConf")->where("lang_name='$langSet'")->find();
				
		//删除指定记录
		$model = D(MODULE_NAME);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				$items = $model->where($condition)->findAll();
				if (false !== $model->where ( $condition )->delete ())
				{
					foreach($items as $item)
					{
						$type = $item['lottery_type'];
						$lid = $item['id'];
						$tpl = $this->getRealPath()."/home/tpl/".$langItem['tmpl']."/Inc/lottery/".strtolower($type)."_$lid.tpl";
						@unlink($tpl);
						
						if(!empty($item['img']))
							@unlink($this->getRealPath().$item['img']);
						
						$lotteryName = $type."Lottery";
						if(class_exists($lotteryName))
						{
							$lotteryClass = new $lotteryName();
							$lottery = $lotteryClass->Delete($lid,$this);
						}
					}
					
					$this->saveLog(1);
					$this->success (L('DEL_SUCCESS'));
				} else {
					$this->saveLog(0);
					$this->error (L('DEL_FAILED'));
				}
			} else {
				$this->saveLog(0);
				$this->error ( L('INVALID_OP') );
			}
		}
		$this->forward ();
	}
	
	public function getLotterySettings()
	{
		$id = intval($_REQUEST['id']);
		$type = $_REQUEST["type"];
		$lotteryName = $type."Lottery";
		
		if(empty($type) || !class_exists($lotteryName))
			echo "";
		
		$lotteryClass = new $lotteryName();
		$lottery = $lotteryClass->Get($id);
		
		$this->assign("lottery",$lottery);
		$type = strtolower($type);
		ob_start();
		ob_implicit_flush(0);
		$this->display("Lottery:$type");
		$result['html'] = ob_get_clean();
		
		if($id > 0)
		{
			$langSet = eyooC('DEFAULT_LANG');
			$langItem = D("LangConf")->where("lang_name='$langSet'")->find();
			$tpl = $this->getRealPath()."/home/tpl/".$langItem['tmpl']."/Inc/lottery/".$type."_".$id.".tpl";
			
			$tpl = file_get_contents($tpl);
			if($tpl === false)
				$result['tpl'] = file_get_contents($this->getRealPath()."/Public/lottery/$type/tpl.tpl");
			else
				$result['tpl'] = $tpl;
		}
		else
			$result['tpl'] = file_get_contents($this->getRealPath()."/Public/lottery/$type/tpl.tpl");
		
		//echo $result['html'];
		echo json_encode($result);
	}
	
	//搜索团购
	public function getGoods()
	{
		$goodsList = array();
		$key = trim($_REQUEST['key']);
		$name = 'name_'.DEFAULT_LANG_ID;
		$condition["status"] = 1;
		
		if(!empty($key))
		{
			$condition[$name] = array("like","%$key%");
			$goodsList = D("Goods")->where($condition)->field("id,$name as name")->order("sort desc,id desc")->limit(30)->select();
		}
		else
		{
			$goodsList = D("Goods")->where($condition)->field("id,$name as name")->order("sort desc,id desc")->limit(30)->select();
		}
		
		echo json_encode($goodsList);
	}
	
	public function getUploadFile()
	{
		$uplist = $this->uploadFile(0,'lottery');
		return $uplist;
	}
	
	public function getRoot()
	{
		return $this->getRealPath();	
	}
}
?>