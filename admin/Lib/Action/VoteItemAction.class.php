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
class VoteItemAction extends CommonAction{
	public function __construct()
	{
		if($_REQUEST['vote_id'])
			Session::set('vote_id',$_REQUEST['vote_id']);
			
		parent::__construct();
		
		if(!Session::is_set('vote_id'))
			$this->redirect('Vote/index');
			
		$this->assign('vote_id',Session::get('vote_id'));
	}
	
	public function add()
	{
		$new_sort = D("VoteItem")->where("vote_id = ".Session::get('vote_id'))-> max("sort") + 1;
		$vote_title = D("Vote")-> where("id = ".Session::get('vote_id'))->getField('title');
		$this->assign("new_sort",$new_sort);
		$this->assign("vote_title",$vote_title);
		$this->display();
	}
	
	public function edit()
	{
		$vote_title = D("Vote")-> where("id = ".Session::get('vote_id'))->getField('title');
		$this->assign("vote_title",$vote_title);
		
		$name=$this->getActionName();
		$model = M ( $name );
		$id = $_REQUEST [$model->getPk ()];
		$vo = $model->getById ( $id );
		$this->assign ( 'vo', $vo );	
		
		$this->display();
	}
	
	public function getVoteTitle($vID)
	{
		return D("Vote")-> where("id = ".$vID)->getField('title');
	}
	
	public function getIsMulti($isMulti)
	{
		if($isMulti == 0)
			return "单选";
		else
			return "多选";
	}
	
	public function getOptionUrl($id)
	{
		return "<a href='".u('VoteOption/index',array('item_id'=>$id))."'>".L("SHOW_VOTE_OPTION")."</a>&nbsp;<a href='".u('VoteOption/add',array('item_id'=>$id))."'>".L("ADD_VOTE_OPTION")."</a>";
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
					$condition = array("item_id"=>array('in',explode(',',$id )));
					D("VoteGroup")->where($condition)->delete();
					$vote_option_list = D("VoteOption")->where($condition)->findAll();
					if(false !== D("VoteOption")->where($condition)->delete())
					{
						foreach($vote_option_list as $vote_option)
						{
							D("VoteInput")->where("option_id =".$vote_option['id'])->delete();
						}
					}
					
					$itemCount =D("VoteItem")->where("vote_id = '".Session::get('vote_id')."'")->sum('vote_count');
					D("Vote")->where("id = '".Session::get('vote_id')."'")->setField("vote_count",$itemCount);
					
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