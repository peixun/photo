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
class VoteOptionAction extends CommonAction{
	public function __construct()
	{
		if($_REQUEST['item_id'])
			Session::set('item_id',$_REQUEST['item_id']);
			
		parent::__construct();
		
		if(!Session::is_set('item_id'))
			$this->redirect('Vote/index');
		
		$vote_id = D("VoteItem")-> where("id = ".Session::get('item_id'))->getField('vote_id');
		$this->assign('vote_id',$vote_id);
		$this->assign('item_id',Session::get('item_id'));
	}
	
	public function add()
	{
		$new_sort = D("VoteOption")->where("item_id = ".Session::get('item_id'))-> max("sort") + 1;
		$vote_item = D("VoteItem")-> where("id = ".Session::get('item_id'))->find();
		$vote_title = D("Vote")-> where("id = ".$vote_item['vote_id'])->getField('title');
		$vote_group = D("VoteGroup")-> where("item_id = ".Session::get('item_id'))->order("sort asc")->findAll();
		$vote_group_sort = D("VoteGroup")-> where("item_id = ".Session::get('item_id'))-> max("sort") + 1;
		
		$this->assign("vote_group",$vote_group);
		$this->assign("vote_group_sort",$vote_group_sort);
		$this->assign("new_sort",$new_sort);
		$this->assign("vote_title",$vote_title);
		$this->assign("item_title",$vote_item['title']);
		$this->display();
	}
	
	public function edit()
	{
		$name=$this->getActionName();
		$model = M ( $name );
		$id = $_REQUEST [$model->getPk ()];
		$vo = $model->getById ($id);
		$this->assign ('vo',$vo);	
		
		$vote_item = D("VoteItem")-> where("id = ".$vo['item_id'])->find();
		$vote_title = D("Vote")-> where("id = ".$vote_item['vote_id'])->getField('title');
		$vote_group = D("VoteGroup")-> where("item_id = ".$vo['item_id'])->order("sort asc")->findAll();
		$vote_group_sort = D("VoteGroup")-> where("item_id = ".$vo['item_id'])-> max("sort") + 1;
		
		$this->assign("vote_group",$vote_group);
		$this->assign("vote_group_sort",$vote_group_sort);
		$this->assign("vote_title",$vote_title);
		$this->assign("item_title",$vote_item['title']);
		$this->display();
	}
	
	public function getVoteTitle($id)
	{
		$vote_id = D("VoteItem")-> where("id = ".$id)->getField('vote_id');
		return D("Vote")-> where("id = ".$vote_id)->getField('title');
	}
	
	public function getItemTitle($id)
	{
		return D("VoteItem")-> where("id = ".$id)->getField('title');
	}
	
	public function getGroupTitle($id)
	{
		return D("VoteGroup")-> where("id = ".$id)->getField('title');
	}
	
	public function getOptionUrl($id)
	{
		return "<a href='".u('VoteOption/index',array('item_id'=>$id))."'>".L("SHOW_VOTE_OPTION")."</a>&nbsp;<a href='".u('VoteOption/add',array('item_id'=>$id))."'>".L("ADD_VOTE_OPTION")."</a>";
	}
	
	public function addVoteGroup()
	{
		$result = array("status"=>0);
		$data['title'] = trim($_REQUEST["title"]);
		$data['item_id'] = intval($_REQUEST["item_id"]);
		$data['sort'] = intval($_REQUEST["sort"]);
		
		$vgid = D("VoteGroup")->add($data);
		
		if($vgid)
		{
			$result["status"] = 1;
			$result["groups"] = D("VoteGroup")-> where("item_id = ".$data['item_id'])->order("sort asc")->findAll();
			$result["sort"] = D("VoteGroup")-> where("item_id = ".$data['item_id'])-> max("sort") + 1;
		}
		
		echo json_encode($result);
	}
	
	public function updateVoteGroup()
	{
		$result = array("status"=>0);
		$data['id'] = intval($_REQUEST["id"]);
		$data['title'] = trim($_REQUEST["title"]);
		$data['item_id'] = intval($_REQUEST["item_id"]);
		$data['sort'] = intval($_REQUEST["sort"]);
		
		$vgid = D("VoteGroup")->save($data);
		
		if($vgid)
		{
			$result["status"] = 1;
			$result["groups"] = D("VoteGroup")-> where("item_id = ".$data['item_id'])->order("sort asc")->findAll();
			$result["sort"] = D("VoteGroup")-> where("item_id = ".$data['item_id'])-> max("sort") + 1;
		}
		
		echo json_encode($result);
	}
	
	public function removeVoteGroup()
	{
		$result = array("status"=>0);
		$item_id = intval($_REQUEST["item_id"]);
		$id = intval($_REQUEST["id"]);
		
		if(D("VoteGroup")->where("id = $id")->delete())
		{
			$result["status"] = 1;
			$result["groups"] = D("VoteGroup")-> where("item_id = ".$item_id)->order("sort asc")->findAll();
			$result["sort"] = D("VoteGroup")-> where("item_id = ".$item_id)-> max("sort") + 1;
		}
		
		echo json_encode($result);
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
					$condition = array("option_id"=>array('in',explode(',',$id )));
					D("VoteInput")->where($condition)->delete();
					
					$voteItem = D("VoteItem")->getById(Session::get('item_id'));
					
					$optionCount =D("VoteOption")->where("item_id = '$voteItem[id]'")->sum('vote_count');
					D("VoteItem")->where("id = '$voteItem[id]'")->setField("vote_count",$optionCount);
					
					$itemCount =D("VoteItem")->where("vote_id = '$voteItem[vote_id]'")->sum('vote_count');
					D("Vote")->where("id = '$voteItem[vote_id]'")->setField("vote_count",$itemCount);
					
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
}
?>