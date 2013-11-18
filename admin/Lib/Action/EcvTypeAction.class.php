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

// 代金券
class EcvTypeAction extends CommonAction{
	
	public function insert() {
		//B('FilterString');
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$list=$model->add ();
		$dj_name = $_REQUEST['name'];
		
		if ($list!==false) { //保存成功
			$msg = "代金券".$dj_name."添加成功";
			$this->saveLog(1,$list,$msg);
//			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('ADD_SUCCESS'));
		} else {
			//失败提示
			$msg = "代金券".$dj_name."添加失败";
			$this->saveLog(0,$list,$msg);
			$this->error (L('ADD_FAILED'));
		}
	}
	
	public function update() {
		//B('FilterString');
		$name=$this->getActionName();
		$model = D ( $name );
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save ();
		$dj_name = $_REQUEST['name'];
		if (false !== $list) {
			//成功提示
			$msg = "代金券".$dj_name."修改成功";
			$this->saveLog(1,$list,$msg);
//			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('EDIT_SUCCESS'));
		} else {
			//错误提示
			$msg = "代金券".$dj_name."修改失败";
			$this->saveLog(0,0,$msg);
			$this->error (L('EDIT_FAILED'));
		}
	}
	
	public function foreverdelete()
	{
		//删除指定记录
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model ))
		{
			$pk = $model->getPk ();
			$ids = $_REQUEST [$pk];
			$ids = explode ( ',', $ids );  //edit by hc 2010-6-3 
			$names = '';
			foreach($ids as $idd)
			{
				$names .= M("EcvType")->where("id=".$idd)->getField("name").",";
			}
			if($names!='')
			{
				$names = substr($names,0,strlen($names)-1);
			}
			
			$condition = array ($pk => array ('in', $ids));
			
			if (false !== $model->where ($condition)->delete())
			{
				$condition = array ("ecv_type" => array ('in', $ids));
				D("Ecv")->where($condition)->delete();
				
				$msg = "代金券:".$names."删除成功";
				$this->saveLog(1,0,$msg);
			
				$this->success (L('DEL_SUCCESS'));
			}
			else
			{
				$msg = "代金券:".$names."删除失败";
				$this->saveLog(0,0,$msg);
				$this->error (L('DEL_FAILED'));
			}
		}
		else
		{
			$msg = "代金券删除失败";
			$this->saveLog(0,0,$msg);
			$this->error ( L('INVALID_OP') );
		}
		$this->forward();
	}
	
	public function getCreateNumber($id)
	{
		return D("Ecv")->where("ecv_type = $id")->count();
	}
	
	public function getUseNumber($id)
	{
		return D("Ecv")->where("ecv_type = $id and use_date_time > 0")->count();
	}
	
	public function emptyAll()
	{
		$id = intval($_REQUEST['id']);
		if(D("Ecv")->where("ecv_type = $id")->delete())
		{
			M("EcvType")->where("id=".$id)->setField("gen_count",0);
			$msg = "代金券".M("EcvType")->where("id=".$id)->getField("name")."清空";
			$this->saveLog(1,$id,$msg);
			echo 1;
		}			
		else
		{
			$msg = "代金券".M("EcvType")->where("id=".$id)->getField("name")."清空失败";
			$this->saveLog(0,$id,$msg);
			echo 0;
		}
			
	}

	public function export($page=1)
	{
		set_time_limit(0); 
		$id = intval($_REQUEST['id']);
		$limit = (($page - 1)*500).",".(500);
		
		$name = M("EcvType")->where("id=".$id)->getField("name");
		$sql = "select e.id,e.sn,e.order_sn,e.password,e.status,e.use_date_time,u.user_name,uu.user_name as use_user_name,et.name from ".C("DB_PREFIX")."ecv as e left join ".C("DB_PREFIX")."ecv_type  as et on et.id = e.ecv_type left join ".C("DB_PREFIX")."user as u on u.id = e.user_id left join ".C("DB_PREFIX")."user as uu on uu.id = e.use_user_id where e.ecv_type = $id group by e.id limit ".$limit;
		$list = M()->query($sql);
		if($list)
		{
			register_shutdown_function(array(&$this, 'export'), $page+1);
			//dump($sql);
	    	/* csv文件数组 */
	    	$ecv_value = array('id'=>'""', 'sn'=>'""', 'password'=>'""', 'user_name'=>'""', 'use_user_name'=>'""', 'order_sn'=>'""', 'use_date_time'=>'""');
			if($page == 1)
	    	$content = utf8ToGB("编号,序列号,密码,发放会员,使用会员,使用订单号,使用时间" . "\n");
	    	 
			foreach($list as $k=>$v)
			{
				$ecv_value['id'] = utf8ToGB('"' . $v['id'] . '"');
				$ecv_value['sn'] = utf8ToGB('"' . $v['sn'] . '"');
				$ecv_value['password'] = utf8ToGB('"' . $v['password'] . '"');
				$ecv_value['user_name'] = utf8ToGB('"' . $v['user_name'] . '"');
				$ecv_value['use_user_name'] = utf8ToGB('"' . $v['use_user_name'] . '"');
				$ecv_value['order_sn'] = utf8ToGB('"' . $v['order_sn'] . '"');
				$ecv_value['use_date_time'] = utf8ToGB('"' . toDate($v['user_name']) . '"');
//				$ecv_value['mobile_phone'] = utf8ToGB('"' . $v['mobile_phone'] . '"');
//				$ecv_value['end_time'] = utf8ToGB('"' . toDate($v['end_time']) . '"');
//				$ecv_value['use_time'] = utf8ToGB('"' . toDate($v['use_time']) . '"');				
				$content .= implode(",", $ecv_value) . "\n";
			}	
			
	    	header("Content-Disposition: attachment; filename=ecv_list.csv");
	    	//header("Content-Type: application/octet-stream");
	    	//die();
	    	echo $content;  
		} 
	}
	
	public function getEditLink($id,$status)
	{
		$ecv = M("EcvType")->where("id=".$id)->find();
		$exchange=$ecv['exchange'];
		$exchange_score = $ecv['exchange_score'];
		$str = "";
		if($exchange==0)
		{
			if($status)
				$str = "<a href='".u("Ecv/add",array("ecv_type"=>$id))."'>发放<a>&nbsp;&nbsp;<a href='".u("Ecv/index",array("ecv_type"=>$id))."'>查看<a>&nbsp;&nbsp;<a href='javascript:;' onclick='ecvTypeEmapty($id,this);'>清空</a>";
			else
				$str = "<a href='".u("Ecv/index",array("ecv_type"=>$id))."'>查看<a>&nbsp;&nbsp;<a href='javascript:;' onclick='ecvTypeEmapty($id,this);'>清空</a>";
			
			$str .="&nbsp;&nbsp;<a href='".u("Ecv/import",array("ecv_type"=>$id))."'>导入</a>&nbsp;&nbsp;<a href='".u("EcvType/export",array("id"=>$id))."'>导出</a>"; 
		}
		else
		{
			$str = $exchange_score."积分兑换&nbsp;&nbsp;<a href='".u("Ecv/index",array("ecv_type"=>$id))."'>查看<a>&nbsp;&nbsp;<a href='javascript:;' onclick='ecvTypeEmapty($id,this);'>清空</a>";
			$str .="&nbsp;&nbsp;<a href='".u("EcvType/export",array("id"=>$id))."'>导出</a>"; 
		}
		return $str;
	}
}
?>