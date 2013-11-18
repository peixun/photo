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

// 投票配置
class VoteAction extends CommonAction{
	public function getItemUrl($id)
	{
		return "<a href='".u('Vote/result',array('id'=>$id))."' target='_blank'>".L("VOTE_RESULT")."</a>&nbsp;<a href='".u('VoteItem/index',array('vote_id'=>$id))."'>".L("SHOW_VOTE_ITEM")."</a>&nbsp;<a href='".u('VoteItem/add',array('vote_id'=>$id))."'>".L("ADD_VOTE_ITEM")."</a>";
	}
	
	public function getCityName($id)
	{
		return D("GroupCity")-> where("id = ".$id)->getField('name');
	}
	
	public function add()
	{
		$new_sort = D("Vote")-> max("sort") + 1;
		$city_list = D("GroupCity")->where("status=1")->order("is_defalut desc,id asc")->findAll();
		$this->assign("city_list",$city_list);
		$this->assign("new_sort",$new_sort);
		$this->display();
	}
	
	public function edit()
	{
		$city_list = D("GroupCity")->where("status=1")->order("is_defalut desc,id asc")->findAll();
		$this->assign("city_list",$city_list);
		
		$name=$this->getActionName();
		$model = M ( $name );
		$id = $_REQUEST [$model->getPk ()];
		$vo = $model->getById ( $id );
		$this->assign ( 'vo', $vo );	
		
		$this->display();
	}
	
	public function result()
	{
		$id = intval($_REQUEST["id"]);
		$this->assign("vote",D("Vote")->getVote($id));
		$this->display();
	}
	
	
	public function foreverdelete() {
		//删除指定记录
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if (false !== $model->where ( $condition )->delete ())
				{
					$condition = array("vote_id"=>array('in',explode(',',$id )));
					$vote_item_list = D("VoteItem")->where($condition)->findAll();
					if(false !== D("VoteItem")->where($condition)->delete())
					{
						foreach($vote_item_list as $vote_item)
						{
							D("VoteGroup")->where("item_id =".$vote_item['id'])->delete();
							$vote_option_list = D("VoteOption")->where("item_id =".$vote_item['id'])->findAll();
							if(false !== D("VoteOption")->where("item_id =".$vote_item['id'])->delete())
							{
								foreach($vote_option_list as $vote_option)
								{
									D("VoteInput")->where("option_id =".$vote_option['id'])->delete();
								}
							}
						}
					}
					
					$this->saveLog(1);
					$this->success (L('DEL_SUCCESS'));
				}
				else 
				{
					$this->saveLog(0);
					$this->error (L('DEL_FAILED'));
				}
			} else {
				$this->saveLog(0);
				$this->error ( L('INVALID_OP'));
			}
		}
		$this->forward ();
	}
}
?>