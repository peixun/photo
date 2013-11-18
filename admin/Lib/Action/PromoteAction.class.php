<?php
// +----------------------------------------------------------------------
// | Fanwe 多语商城建站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 
// +----------------------------------------------------------------------

// 促销列表
class PromoteAction extends CommonAction{
	
	public function promoteModule() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		$map ['is_card'] = 0;//array("eq","0");
		$map ['promote_type_id'] = array("neq","8");
		$model = D ("Promote");
		$this->_list ( $model, $map );
		$this->display("Promote:index");
		return;
	}
		
	public function promoteCard() {
		//列表过滤器，生成查询Map对象
		$map = array();
		$map ['is_card'] = 1;
		$map ['promote_type_id'] = array("neq","8");
		$model = D ("Promote");
		$this->_list ( $model, $map );
		$this->display("Promote:indexCard");
		return;
	}	
	
	public function search() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$map ['is_card'] = 1;
		$map ['promote_type_id'] = array("neq","8");
		$model = D ("Promote");
		$this->_list ( $model, $map );
		$this->display("Promote:search");
		return;
	}
		
	public function add(){
		$this->edit();
	}
	
	//编辑
	public function edit()
	{
		$id = intval($_REQUEST['id']);
		$model = D("Promote");
		if ($id == 0){//新增
			$is_card = intval($_REQUEST['is_card']);
			$vo = $model->create();
			$vo['is_card'] = $is_card;
			if ($is_card == 1){
				$vo['promote_type_id'] = 6;
			}else{
				$vo['promote_type_id'] = 1;
			}
			$vo['id'] = 0;
			$this->assign ( "vo", $vo );
			$this->display("Promote:add");			
		}else{//编辑
			$vo = $model->getById ( $id );
			$this->editPromote($vo['id'], $vo['promote_type_id']);
		}
	}	
	
	public function promoteScore(){
		$model = D("Promote");
		$vo = $model->field('id')->where('promote_type_id = 8')->findAll();
		$id = intval($vo[0]['id']); 
		$this->editPromote($id, 8);
	}
	
	public function editPromote($id, $promote_type_id)
	{
		//$this->assign('type','card');
		if (!empty($_REQUEST['id'])){
			$id = intval($_REQUEST['id']);
		}
		if (!empty($_REQUEST['promote_type_id'])){
			$promote_type_id = intval($_REQUEST['promote_type_id']);
		}	
		//dump($promote_type_id);
		$model = D("Promote");
		if ($id == 0){
			$vo = $model->create();
			$vo['is_card'] = intval($_REQUEST['is_card']);
			$vo['card_limit'] = 1;
			
			$lang_envs = D("LangConf")->findAll();
			foreach($lang_envs as $lang_item)
			{
				$vo['memo_'.$lang_item['id']] = L('PROMOTE_PROMOTE_TYPE_ID_'.intval($promote_type_id));
			}	

			$vo['priority'] = $model-> max("priority") + 1; //优先级 由大到小;
			$vo['promote_begin_time'] = gmtTime();
			$vo['promote_end_time'] = gmtTime() + 24 * 3600 * 15;	

			$vo['order_price_min'] = 0;
			$vo['order_price_max'] = 999999;
			$vo['status'] = 1;
		}else{
			$vo = $model->getById ( $id );
		}
		
		$vo['promote_type_id'] = $promote_type_id;
		
		$vo['promote_begin_time'] = timeToLocalStr($vo['promote_begin_time'], 'Y-m-d');
		$vo['promote_end_time'] = timeToLocalStr($vo['promote_end_time'], 'Y-m-d');		
		
		if ($promote_type_id == 1 || $promote_type_id == 2 || $promote_type_id == 3 || $promote_type_id == 8){
			$module_name = "Goods";
		}elseif($promote_type_id == 5){
			$module_name = "Promote";
		}else{
			$module_name = "";
		}
		$this->assign ( "module_name", $module_name );
		
		//dump($module_name);
		
		//会员列表	
		$userGroupList = D("UserGroup")-> field('id,name_'.DEFAULT_LANG_ID.' as name')-> findAll();
		$this->assign ( "userGroupList", $userGroupList );
		
		//允许参加会员等级 promote_user_group
		$this->assign ( "selectUserGroupList", D( "PromoteUserGroup" )->where("promote_id=".intval($id))-> field('user_group_id')->findAll());
		
		//$sql = 'select 
		$sql_str = 'select a.module_name, a.rec_id, b.name_'.DEFAULT_LANG_ID.' as name, a.score'.
				   '  from '.C("DB_PREFIX").'Promote_child a'.
				   '  left outer join '.C("DB_PREFIX").'goods b on b.id = a.rec_id'.
				   ' where a.module_name = \'Goods\' and a.promote_id = '.$id.
				   ' union all '.
				   'select a.module_name, a.rec_id, b.card_name_'.DEFAULT_LANG_ID.' as name, a.score'.
				   '  from '.C("DB_PREFIX").'Promote_child a'.
				   '  left outer join '.C("DB_PREFIX").'Promote b on b.id = a.rec_id'.
				   ' where a.module_name = \'Promote\' and a.promote_id = '.$id;
		//dump($sql_str);		
		$rs = $model->query($sql_str, false);
		$this->assign ( "promoteChildList", $rs);
				
		$this->assign('vo',$vo);
		
		$this->display("Promote:edit");
	}
		
	public function save_detail(&$model,$id){
		//==================================保存子表=====================================
		//允许参加会员等级 promote_user_group		
		$UserGroupArray = array();
		$UserGroupArray = $_REQUEST['user_group_id'];
		$total = count($UserGroupArray);
		//dump($total);
		$sql_str = 'delete from '.C("DB_PREFIX").'promote_user_group where promote_id = '.$id;
		$model->execute($sql_str);
		for($i=0;$i<$total;$i++){
			$sql_str = 'insert into '.C("DB_PREFIX").'promote_user_group(promote_id,user_group_id) values('.$id.','.$UserGroupArray[$i].')';
			//dump($sql_str);
			$model->execute($sql_str);			
		}	

		
		//允许参加会员等级 fanwe_promote_child
		$module_nameArray = $_REQUEST['module_nameList'];
		$scoreArray = $_REQUEST['scoreList'];
		$rec_idArray = $_REQUEST['rec_idList'];	
		$total = count($rec_idArray);	
		$sql_str = 'delete from '.C("DB_PREFIX").'promote_child where promote_id = '.$id;
		$model->execute($sql_str);
		for($i=0;$i<$total;$i++){
			$sql_str = 'insert into '.C("DB_PREFIX").'promote_child(promote_id,module_name,rec_id, score) values('.$id.',\''.$module_nameArray[$i].'\','.$rec_idArray[$i].','.$scoreArray[$i].')';
			//dump($sql_str);
			$model->execute($sql_str);			
		}		
	}
	
	
	public function save(){
		$model = D ("Promote");
		if(false === $vo = $model->create()) {
			$this->error($model->getError());
		}
		$vo['promote_begin_time'] = ! empty ( $_POST ['promote_begin_time'] ) ? localStrToTimeMin( $_POST ['promote_begin_time'] ) : 0;
		$vo['promote_end_time'] = ! empty ( $_POST ['promote_end_time'] ) ? localStrToTimeMax( $_POST ['promote_end_time'] ) : 0;
				
		if (intval($_REQUEST['id']) == 0){
			$vo['id'] = $model->add($vo);
			
			//dump($model->getLastSql());
			if ($vo['id']!==false) { //保存成功
				$this->save_detail($model, $vo['id']);
				
				$this->saveLog(1,$vo['id']);
				
				if ($vo['promote_type_id'] == 8){
					$this->assign ( 'jumpUrl', u('Promote/promoteScore'));
				}else{
					if ($vo['is_card'] == 1){
						$this->assign ( 'jumpUrl', u('Promote/promoteCard'));
					}else{
						$this->assign ( 'jumpUrl', u('Promote/promoteModule'));
					}					
				}
				$this->success (L('ADD_SUCCESS'));
			} else {
				//失败提示
				$this->saveLog(0,$vo['id']);
				$this->error (L('ADD_FAILED'));
			}			
		}else{
			// 更新数据
			$list = $model->save($vo);
			//dump($model->getLastSql());
			if (false !== $list) {
				$this->save_detail($model, $vo['id']);
				
				//成功提示
				$this->saveLog(1);
				
				if ($vo['promote_type_id'] == 8){
					$this->assign ( 'jumpUrl', u('Promote/promoteScore'));
				}else{
					if ($vo['is_card'] == 1){
						$this->assign ( 'jumpUrl', u('Promote/promoteCard'));
					}else{
						$this->assign ( 'jumpUrl', u('Promote/promoteModule'));
					}					
				}
								
				$this->success (L('EDIT_SUCCESS'));
			} else {
				//错误提示
				$this->saveLog(0);
				$this->error (L('EDIT_FAILED'));
			}			
		}
		
	}
	
	
	public function getChildData(){
		if($_REQUEST['ids']!='')
		{
			$ids = json_decode($_REQUEST['ids']);
			$module_name = $_REQUEST['module_name'];//Goods/BindGoods/Promote
			
			$condition = array("id"=>array ('in', $ids));
			if ($module_name == 'Goods')
			  $GoodsList = D('Goods')->where($condition)-> field('id,name_'.DEFAULT_LANG_ID.' as name,\''.$module_name.'\' as module_name, score')-> findAll();
			else
			  $GoodsList = D('Promote')->where($condition)-> field('id,card_name_'.DEFAULT_LANG_ID.' as name,\''.$module_name.'\' as module_name, 0 as score')-> findAll();
			  	
			//echo D("Goods")->getLastSql(); exit;
			echo json_encode($GoodsList);
		}		
	}
	
	public function foreverdelete() {
		//删除指定记录
		$model = D ('Promote');
		if (! empty ( $model )) {
			$id = $_REQUEST ['id'];
			if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$items = $model->where($condition)->findAll();				

				if (false !== $model->where ( $condition )->delete ()) {
					foreach($items as $item)
					{
						$sql_str = 'delete from '.C("DB_PREFIX").'promote_user_group where promote_id = '.$item['id'];
						$model->execute($sql_str);
						$sql_str = 'delete from '.C("DB_PREFIX").'promote_child where promote_id = '.$item['id'];
						$model->execute($sql_str);	

						$sql_str = 'delete from '.C("DB_PREFIX").'promote_card where promote_id = '.$item['id'];
						$model->execute($sql_str);						
					}
					//echo $model->getlastsql();
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
	
	public function listCard() {
		//列表过滤器，生成查询Map对象
		$promote_id = intval($_REQUEST['promote_id']);
		//$map = array();
		//$map ['promote_id'] = $promote_id;
		$model = D ("PromoteCard");
		
		$sql_str = "select a.*, b.card_name_".DEFAULT_LANG_ID." as card_name_n from ".C("DB_PREFIX")."promote_card a left outer join ".C("DB_PREFIX")."promote b on b.id = a.promote_id where a.promote_id =".$promote_id; 
		//dump($sql_str);
		$this->_Sql_list($model, $sql_str);

		//$this->_list ( $model, $map );
		
		$this->assign ( 'promote_id', $promote_id);
		$this->display("Promote:listCard");
		return;
	}	

	//生成优惠卡
	public function releasePromoteCard(){
		$promote_id = intval($_REQUEST['promote_id']);
		$num = intval($_REQUEST['num']);
		//
		$p_vo = D ('Promote')->getById ( $promote_id );
		//$cardarray = array();
		$cardarray = '';
		$model = D ('PromoteCard');
		for($i=0;$i<$num;$i++){
			$vo = $model->create();
			$vo['id'] = null;
			$vo['promote_id'] = $promote_id;
			$vo['card_limit'] = $p_vo['card_limit'];
			$vo['card_used'] = 0;
			$id = $model->add($vo);
			
			$vo['id'] = $id;
			$vo['card_code'] = buildCard($id);
			$model->save($vo);
			
			if ($cardarray == ''){
				$cardarray = $vo['card_code'];
			}else{
				$cardarray = $cardarray.','.$vo['card_code'];
			}
		}

		$sql_str = 'update '.C("DB_PREFIX").'promote set card_total = (select count(*) from '.C("DB_PREFIX").'promote_card where promote_id = '.$promote_id.') where id ='.$promote_id;
		$model->execute($sql_str);
		
		echo json_encode($cardarray);
	}
	
}
?>