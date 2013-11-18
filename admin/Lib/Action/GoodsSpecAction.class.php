<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 商品规格
class GoodsSpecAction extends CommonAction{
	public function index()
	{
		$lang_id = D("LangConf")->where("lang_name='".C("DEFAULT_LANG")."'")->getField("id");
		$goods_id = intval($_REQUEST['id']);
		$this->assign('goods_id',$goods_id);
		//开始取出规格类型与预设配置
		$res  =  D("GoodsSpec")->where("goods_id=".$goods_id)->findAll();
		foreach($res as $k=>$v)
		{
			$spec[$v['spec_type_id']]['spec_list'][] = $v;
			$spec[$v['spec_type_id']]['spec_type'] = D("SpecType")->getById($v['spec_type_id']);
			$spec[$v['spec_type_id']]['spec_type']['name'] = $spec[$v['spec_type_id']]['spec_type']['name_'.$lang_id];
		}
		
		$this->assign("spec_list",$spec);		
		
		$spec_item_list = D("GoodsSpecItem")->where("goods_id=".$goods_id)->findAll();
		foreach($spec_item_list as $k=>$v)
		{
			$spec_item_list[$k]['spec_conf'] = unserialize($v['spec_conf']);
			$spec_conf = array();
			if($spec_item_list[$k]['spec_conf']!='')
			{				
				foreach($spec_item_list[$k]['spec_conf'] as $kk=>$vv)
				{
					$spec_conf[$kk]['spec_item_id'] = $vv;
					$spec_item = D("GoodsSpec")->getById($vv);
					$spec_conf[$kk]['spec_item_name'] = $spec_item['spec_name_'.$lang_id];
				}
			}
			$spec_item_list[$k]['spec_conf'] = $spec_conf;
		}
		
		$this->assign("spec_item_list",$spec_item_list);
		
		$this->display();
	}
	

	
	public function listType()
	{
		if($this->checkEditable(intval($_REQUEST['goods_id'])))
		{
			$session_id = Session::id();
			$goods_id = intval($_REQUEST['goods_id']);
			$exist_spec = D("GoodsSpec")->where("goods_id=".$goods_id." and session_id='".$session_id."'")->findAll();

			$exist_spec_type_id = array();
			if($exist_spec)
			{
				foreach($exist_spec as $item)
				{
					array_push($exist_spec_type_id,$item['spec_type_id']);
				}
				$exist_spec_type_id = implode(",",$exist_spec_type_id);
			}
			else 
			{
				$exist_spec_type_id = 0;
			}
			
			$type_list = D("SpecType")->where("id not in(".$exist_spec_type_id.")")->findAll();
			$this->assign("type_list",$type_list);
			$this->assign("idx",intval($_REQUEST['idx']));
			$this->display();
		}
		else 
		echo L("GOODS_SPEC_ITEM_EXIST");
	}
	
	public function listSpec()
	{
		$type_id = $_REQUEST['id'];
		$spec_list = D("Spec")->where("spec_type_id=".$type_id)->findAll();
		$langs = D("LangConf")->findAll();
		foreach($spec_list as $k=>$item)
		{
			foreach($langs as $lang_item)
			{
				$spec_list[$k]['spec_name'][] = $item['spec_name_'.$lang_item['id']]; 
			}
		}
		echo json_encode($spec_list);
	}
	
	//生成当前商品的可选规格
	public function makeGoodsSpec()
	{
		$idx = intval($_REQUEST['idx']);
		$goods_id = intval($_REQUEST['goods_id']);	
		$session_id = Session::id();	
		$res = '';
		$lang_id = D("LangConf")->where("lang_name='".C("DEFAULT_LANG")."'")->getField("id");
		
			$spec_type_id = $_REQUEST['spec_type'];
			$delete_list = D("GoodsSpec")->where("goods_id=".$goods_id." and idx=".$idx." and session_id='".$session_id."'")->findAll();
			foreach($delete_list as $row)
			{
				if(D("Spec")->where("img='".$row['img']."'")->count()==0)
				{
					@unlink($this->getRealPath().$row['img']);
				}
			}
			D("GoodsSpec")->where("goods_id=".$goods_id." and idx=".$idx." and session_id='".$session_id."'")->delete();
			$type_info = D("SpecType")->getById($spec_type_id);
			$type_info['name'] = $type_info['name_'.$lang_id];
			$res['type_info'] = $type_info;			
			$spec_list = D("Spec")->where("spec_type_id=".$spec_type_id)->findAll();
			//将选中的规格同步到goods_spec表中
			foreach($spec_list as $k=>$item)
			{
				$spec_list[$k]['goods_id']  = $goods_id;
				$spec_list[$k]['id'] = 0;
				$spec_list[$k]['spec_id'] = $item['id'];
				$spec_list[$k]['idx'] = $idx;
				$spec_list[$k]['session_id'] = $session_id;
				D("GoodsSpec")->add($spec_list[$k]);				
				$spec_type = $item['spec_type_id'];
			}			
			$goods_info = D("Goods")->getById($goods_id);
			$spec_type_arr = explode("|",$goods_info['spec_type']);
			if(!in_array($spec_type,$spec_type_arr))
			{
				$spec_type_arr[] = $spec_type;
			}
			$spec_type_arr = implode("|",$spec_type_arr);
			D("Goods")->where("id=".$goods_id)->setField("spec_type",$spec_type_arr);
			$res['spec_list'] = $spec_list;
			$o_spec_type_list =  D("SpecType")->where("id<>".$spec_type_id)->findAll();
			foreach($o_spec_type_list as $k=>$r)
			{
				$o_spec_type_list[$k]['name'] = $r['name_'.DEFAULT_LANG_ID];
			}
			$res['spec_type_list'] = $o_spec_type_list;
		
		
		echo json_encode($res);
	}
	
	public function loadSpecConf()
	{
		$idx = intval($_REQUEST['idx']);
		$goods_id = intval($_REQUEST['goods_id']);	
		$session_id = Session::id();
		$res = D("GoodsSpec")->where("goods_id=".$goods_id." and idx=".$idx." and session_id='".$session_id."'")->findAll();
		foreach($res as $k=>$v)
		{
			$spec[$v['spec_type_id']]['spec_list'][] = $v;
			$spec[$v['spec_type_id']]['spec_type'] = D("SpecType")->getById($v['spec_type_id']);
			$spec[$v['spec_type_id']]['spec_type']['name'] = $spec[$v['spec_type_id']]['spec_type']['name_'.$lang_id];
		}
		$this->assign('idx',$idx);
		$this->assign("spec_list",$spec);
		$str = $this->display();		
		exit;
	}
	
	//修改当前商品的可选规格配置
	public function saveGoodsSpec()
	{		
		$spec_id_array = $_POST['spec_item_id']?$_POST['spec_item_id']:0;
		//先删除不存在的规格
		$goods_id = intval($_POST['goods_id']);
		$not_exist_list = D("GoodsSpec")->where(array("id"=>array("not in",$spec_id_array),"goods_id"=>$goods_id))->findAll();
		foreach($not_exist_list as $item)
		{
			if($item['define_img']==1)
			{
				@unlink($this->getRealPath().$item['img']);
			}
		}
		D("GoodsSpec")->where(array("id"=>array("not in",$spec_id_array),"goods_id"=>$goods_id))->delete();
		$lang_envs = D("LangConf")->findAll();
		$spec_img = $this->uploadFile(0,'spec');  //上传重定新制的规格小图
		$spec_type_arr = array();
		foreach($_POST['spec_item_id'] as $k=>$spec_item_id)
		{
			$data = array();
			$data['id'] = $spec_item_id;
			foreach($lang_envs as $lang_item)
			{
				$data['spec_name_'.$lang_item['id']] = $_POST['spec_name_'.$lang_item['id']][$k];				
			}
			if($_FILES['img']['name'][$k]!='')
			{
				foreach($spec_img as $img_item)
				{
					if($img_item['name'] == $_FILES['img']['name'][$k])
					{
						$data['img'] = $img_item['recpath'].$img_item['savename'];
						$data['define_img'] = 1;
						$origin_data = D("GoodsSpec")->getById($data['id']);
						if($origin_data)
						{
							if($origin_data['define_img']==1)
							@unlink($this->getRealPath().$origin_data['img']);
						}
						break;
					}
				}
			}
			$data['spec_type_id'] = $_POST['spec_type_id'][$k];
			if(!in_array($data['spec_type_id'],$spec_type_arr))
			{
				$spec_type_arr[] = $data['spec_type_id'];
			}
			$data['goods_id'] = $goods_id;
			if($data['id']==0)
			{
				D("GoodsSpec")->add($data);
			}
			else 
			{				
				D("GoodsSpec")->save($data);
			}
		}
		$spec_type_arr = implode("|",$spec_type_arr);
		D("Goods")->where("id=".$goods_id)->setField("spec_type",$spec_type_arr);
		$this->success(L("EDIT_SUCCESS"));
	}
	
	public function checkEditable($goods_id)
	{
		$is_ajax = intval($_REQUEST['is_ajax']);
		$goods_id = $goods_id?$goods_id:intval($_REQUEST['goods_id']);
		if(D("GoodsSpecItem")->where("goods_id=".$goods_id." and (spec1_id<>0 or spec2_id<>0)")->count()>0)
		{
			if($is_ajax)
			echo 0;
			else
			return false;
		}
		else 
		{
			if($is_ajax)
			echo 1;
			else
			return true;
		}
	}
	
	public function checkDelAble()
	{
		$goods_id = intval($_REQUEST['goods_id']);
		$spec_id = intval($_REQUEST['spec_id']);
		if(D("GoodsSpecItem")->where("goods_id=".$goods_id." and (spec1_id=".$spec_id." or spec2_id=".$spec_id.")")->count()>0)
		{
			echo 0;
		}
		else 
		{
			echo 1;
		}
	}
	
	public function addSpecItem()
	{
		$goods_id = intval($_REQUEST['goods_id']);
		$goods_info = D("Goods")->getById($goods_id);
		$spec_item['goods_id'] = $goods_info['id'];
		$spec_item['sn'] = $goods_info['sn']."_".(D("GoodsSpecItem")->where("goods_id=".$goods_id)->count()+1);
		$spec_item['shop_price'] = $goods_info['shop_price'];
		$spec_item['weight'] = $goods_info['weight'];
		$spec_item['stock'] = 1;
			
		$rs = D("GoodsSpecItem")->add($spec_item);
		D("Goods")->where("id=".$goods_id)->setField("stock",D("GoodsSpecItem")->where("goods_id=".$goods_id)->sum("stock"));
		echo $rs;
	}
	
	public function getGoodsSpec()
	{
		$lang_id = D("LangConf")->where("lang_name='".C("DEFAULT_LANG")."'")->getField("id");
		$goods_id = intval($_REQUEST['goods_id']);
		$spec_type_id = intval($_REQUEST['spec_type_id']);
		$speclist = D("GoodsSpec")->where("goods_id=".$goods_id." and spec_type_id=".$spec_type_id)->findAll();
		foreach($speclist as $k=>$v)
		{
			$speclist[$k]['spec_name'] = $v['spec_name_'.$lang_id];
		}
		echo json_encode($speclist);
	}
	
	public function setGoodsSpec()
	{
		$spec_item_id = intval($_REQUEST['spec_item_id']);
		$spec_id = intval($_REQUEST['spec_id']);
		$spec_type_id = intval($_REQUEST['spec_type_id']);
		$spec_item_info = D("GoodsSpecItem")->getById($spec_item_id);
		$spec_conf = unserialize($spec_item_info['spec_conf']);
		if($spec_conf=='')$spec_conf = array();
		$spec_conf[$spec_type_id] = $spec_id;
		$spec_conf_data = serialize($spec_conf);
		
		//开始将当前的规格配置进行比较，如已有存在同配置，退出
		$is_exist = false;  //初始为不存在该配置
		$spec_item_list = D("GoodsSpec_item")->where("goods_id=".$spec_item_info['goods_id'])->findAll();
		foreach($spec_item_list as $item)
		{
			$spec_com_conf = unserialize($item['spec_conf']);
			
			if($spec_com_conf)
			{
				$all_same = true;  //预设全部相同				
				if(count($spec_conf)>=count($spec_com_conf))
				{
					foreach($spec_conf as $spec_type_id_new => $spec_id_new)
					{				
						if($spec_com_conf[$spec_type_id_new] != $spec_id_new)
						{
							$all_same = false;  //存在一个不同，退出当前规格条的判断
							break;
						}
					}
				}
				else 
				{
					foreach($spec_com_conf as $spec_type_id_com => $spec_id_com)
					{				
						if($spec_conf[$spec_type_id_com] != $spec_id_com)
						{
							$all_same = false;  //存在一个不同，退出当前规格条的判断
							break;
						}
					}
				}
				
				
	
				if($all_same)
				{
					$is_exist = true;
					break;
				}
			}
		}
		
		
		if($is_exist)
		{
			echo '0';
		}
		else 
		{
			$sql = "update ".C("DB_PREFIX")."goods_spec_item set spec_conf='".$spec_conf_data."' where id=".$spec_item_id;
			D("GoodsSpecItem")->query($sql);
			echo '1';
		}	
	}
	
	public function delSpecItem()
	{
		$spec_item_id  = intval($_REQUEST['spec_item_id']);
		$delete_item = D("GoodsSpecItem")->getById($spec_item_id);
		$goods_stock = D("Goods")->where("id=".$delete_item['goods_id'])->getField("stock");
		D("Goods")->where("id=".$delete_item['goods_id'])->setField("stock",$goods_stock-$delete_item['stock']);
		D("GoodsSpecItem")->where("id=".$spec_item_id)->delete();		
		D("UserGroupPrice")->where("spec_item_id=".$spec_item_id)->delete();
		//删除同步
		$spec_item_list = D("GoodsSpecItem")->where("goods_id=".$delete_item['goods_id'])->findAll();
		$exist_spec_ids = array();
		foreach($spec_item_list as $k=>$v)
		{
			array_push($exist_spec_ids,$v['spec1_id']);
			array_push($exist_spec_ids,$v['spec2_id']);
		}
		if(count($exist_spec_ids)==0)
		{
			$exist_spec_ids=0;
		}
		else
		{
			$exist_spec_ids = implode(",",$exist_spec_ids);
		}

		$spec_list = D("GoodsSpec")->where("goods_id=".$delete_item['goods_id']." and id not in(".$exist_spec_ids.") and session_id=''")->findAll();
		foreach($spec_list as $spec_item)
			{
				if(D("Spec")->where("img='".$spec_item['img']."'")->count()==0)
				{
					@unlink($this->getRealPath().$spec_item['img']);
				}
			}
	 	D("GoodsSpec")->where("goods_id=".$delete_item['goods_id']." and id not in(".$exist_spec_ids.") and session_id=''")->delete();
	 	//输出商品规格列表组合
	 	
	 	
	 	//取出当前商品的规格类型ID
		$spec_type1 = D("SpecType")->getById(intval(D("GoodsSpec")->where("goods_id=".$delete_item['goods_id']." and idx=1")->getField("spec_type_id")));
		$spec_type2 = D("SpecType")->getById(intval(D("GoodsSpec")->where("goods_id=".$delete_item['goods_id']." and idx=2")->getField("spec_type_id")));
		$this->assign("spec_type1",$spec_type1);
		$this->assign("spec_type2",$spec_type2);
		
		//输出规格一
		$idx = 1;
		$session_id = Session::id();
		//插入不存在的预设
		$res = D("GoodsSpec")->where("goods_id=".$delete_item['goods_id']." and idx=".$idx)->findAll();
		$exist_spec_conf1_ids = array();
		foreach($res as $k=>$v)
		{
			array_push($exist_spec_conf1_ids,$v['spec_id']);
		}
		if(count($exist_spec_conf1_ids)==0)
		{
			$exist_spec_conf1_ids = 0;
		}
		else 
		{
			$exist_spec_conf1_ids = implode(",",$exist_spec_conf1_ids);
		}
		$not_exist_spec = D("Spec")->where("spec_type_id=".intval($spec_type1['id'])." and id not in(".$exist_spec_conf1_ids.")")->findAll();
		
		foreach ($not_exist_spec as $item)
		{
			$item['idx'] = $idx;
			$item['goods_id'] = $delete_item['goods_id'];
			$item['session_id'] = $session_id;
			$item['spec_id'] = $item['id'];
			$item['id'] = 0;			
			D("GoodsSpec")->add($item);
		}
		$res = D("GoodsSpec")->where("goods_id=".$delete_item['goods_id']." and idx=".$idx)->findAll();
		foreach($res as $k=>$v)
		{
			$spec1[$v['spec_type_id']]['spec_type'] = D("SpecType")->getById($v['spec_type_id']);
			$spec1[$v['spec_type_id']]['spec_type']['name'] = $spec1[$v['spec_type_id']]['spec_type']['name_'.DEFAULT_LANG_ID];
			$spec1[$v['spec_type_id']]['spec_list'][] = $v;			
		}		
		$this->assign('idx',$idx);
		$this->assign("spec_list",$spec1);
		
		$spec_list1_str = $this->fetch("GoodsSpec:loadSpecConf");			
		


		//输出规格二
		$idx = 2;
		$session_id = Session::id();
		//插入不存在的预设
		$res = D("GoodsSpec")->where("goods_id=".$delete_item['goods_id']." and idx=".$idx)->findAll();
		$exist_spec_conf2_ids = array();
		foreach($res as $k=>$v)
		{
			array_push($exist_spec_conf2_ids,$v['spec_id']);
		}
		if(count($exist_spec_conf2_ids)==0)
		{
			$exist_spec_conf2_ids = 0;
		}
		else 
		{
			$exist_spec_conf2_ids = implode(",",$exist_spec_conf2_ids);
		}
		$not_exist_spec = D("Spec")->where("spec_type_id=".intval($spec_type2['id'])." and id not in(".$exist_spec_conf2_ids.")")->findAll();
		
		foreach ($not_exist_spec as $item)
		{
			$item['idx'] = $idx;
			$item['goods_id'] = $delete_item['goods_id'];
			$item['session_id'] = $session_id;
			$item['spec_id'] = $item['id'];
			$item['id'] = 0;			
			D("GoodsSpec")->add($item);
		}
		$res = D("GoodsSpec")->where("goods_id=".$delete_item['goods_id']." and idx=".$idx)->findAll();
		foreach($res as $k=>$v)
		{
			$spec2[$v['spec_type_id']]['spec_type'] = D("SpecType")->getById($v['spec_type_id']);
			$spec2[$v['spec_type_id']]['spec_type']['name'] = $spec2[$v['spec_type_id']]['spec_type']['name_'.DEFAULT_LANG_ID];
			$spec2[$v['spec_type_id']]['spec_list'][] = $v;			
		}		
		$this->assign('idx',$idx);
		$this->assign("spec_list",$spec2);
		
		$spec_list2_str = $this->fetch("GoodsSpec:loadSpecConf");			
		echo $spec_list1_str."|".$spec_list2_str;
	}
	
	public function userPrice()
	{
		$price_str = $_REQUEST['price'];
		
		$price_str = explode('|',$price_str);
		
		foreach($price_str as $item)
		{
			$item_arr = explode("_",$item);
			$price_list[$item_arr[0]] =  $item_arr[1];
		}
		$this->assign('price_list',$price_list);
		
		$lang_id = D("LangConf")->where("lang_name='".C("DEFAULT_LANG")."'")->getField("id");
		$user_group = D("UserGroup")->findAll();		
		$spec_item_id = intval($_REQUEST['spec_item_id']);
		$user_group_price_list = D("UserGroupPrice")->where("spec_item_id=".$spec_item_id)->findAll();
		
		foreach($user_group as $k=>$v)
		{
			$user_group[$k]['name'] = $v['name_'.$lang_id];
		}
		
		foreach($user_group_price_list as $k=>$v)
		{
			$user_group_price[$v['user_group_id']] = $v['user_price'];
		}
		$this->assign("spec_item_id",$spec_item_id);
		$this->assign('user_group',$user_group);
		$this->assign('user_group_price',$user_group_price);
		$this->display();
	}
	
	public function saveSpecUserPrice()
	{
		$goods_id = intval($_REQUEST['goods_id']);
		$user_price_data = $_REQUEST['user_price_data'];
		$user_price_data = json_decode($user_price_data,true);
		
		foreach($user_price_data as $k=>$item)
		{
			$data['goods_id'] = $goods_id;
			$data['user_group_id'] = $item['user_group_id'];
			$data['user_price'] = $item['user_price'];
			$data['spec_item_id'] = $item['spec_item_id'];
			if(D("UserGroupPrice")->where("goods_id=".$data['goods_id']." and user_group_id=".$data['user_group_id']." and spec_item_id=".$data['spec_item_id'])->count()>0)
			{
				$data['id'] = D("UserGroupPrice")->where("goods_id=".$data['goods_id']." and user_group_id=".$data['user_group_id']." and spec_item_id=".$data['spec_item_id'])->getField("id");
				D("UserGroupPrice")->save($data);
			}
			else
			{
				D("UserGroupPrice")->add($data);
			}

		}
	}
	
	public function saveSpecItem()
	{
		$goods_id = intval($_REQUEST['goods_id']);
		$spec_item_id = $_POST['spec_item_id'];
		$spec_item_sn = $_POST['spec_item_sn'];
		$spec_item_weight = $_POST['spec_item_weight'];
		$spec_item_stock = $_POST['spec_item_stock'];
		$spec_item_shop_price = $_POST['spec_item_shop_price'];
		$total_stock = 0;
		foreach($spec_item_id as $k=>$id)
		{
			if(D("GoodsSpecItem")->where("sn='".$spec_item_sn[$k]."' and id<>".$id)->count()>0||D("Goods")->where("sn='".$spec_item_sn[$k]."'")->count()>0)
			{
				$this->error(L("GOODS_SN_EXIST"));
				exit;
			}
			$total_stock += $spec_item_stock[$k];
			$snSql = "update ".C("DB_PREFIX")."goods_spec_item set sn='".$spec_item_sn[$k]."' where id=".$id;
			$stockSql = "update ".C("DB_PREFIX")."goods_spec_item set stock='".$spec_item_stock[$k]."' where id=".$id;
			$shopPriceSql = "update ".C("DB_PREFIX")."goods_spec_item set shop_price='".$spec_item_shop_price[$k]."' where id=".$id;
			$weightSql = "update ".C("DB_PREFIX")."goods_spec_item set weight='".$spec_item_weight[$k]."' where id=".$id;
			D("GoodsSpecItem")->query($snSql);
			D("GoodsSpecItem")->query($stockSql);
			D("GoodsSpecItem")->query($shopPriceSql);
			D("GoodsSpecItem")->query($weightSql);
		}
		if($total_stock>0) D("Goods")->where("id=".$goods_id)->setField("stock",$total_stock);
		$this->success(L("EDIT_SUCCESS"));
	}
	
	public function setSpecValue()
	{
		$spec_id = intval($_REQUEST['spec_id']);
		$field = $_REQUEST['field'];
		$value = unescape($_REQUEST['value']);		
		D("GoodsSpec")->where("id=".$spec_id)->setField($field,$value);

	}
	
	public function addSpecRow()
	{
		$spec_id = intval($_REQUEST['spec_id']);
		$spec_item = D("GoodsSpec")->getById($spec_id);
		$spec_item['spec_type'] = D("SpecType")->getById($spec_item['spec_type_id']);
		$spec_item['id'] = 0;
		$spec_item['spec_id'] = 0;
		$spec_item['session_id'] = Session::id();
		$spec_item['img'] = '';
		$id = D("GoodsSpec")->add($spec_item);
		$spec_item['id'] = $id;
		$this->assign("spec_item",$spec_item);
		$rs['html'] = $this->fetch("GoodsSpec:specRow");
		$rs['idx'] = $spec_item['idx'];
		$rs['id'] = $spec_item['id'];
		echo json_encode($rs);
	}
	
	public function delSpecRow()
	{
		$spec_id = intval($_REQUEST['spec_id']);
		$spec_item = D("GoodsSpec")->getById($spec_id);
		if(D("Spec")->where("img='".$spec_item['img']."'")->count()==0)
		{
			@unlink($this->getRealPath().$spec_item['img']);
		}
		D("GoodsSpec")->where("id=".$spec_id)->delete();
	}
	
	
	
	public function makeSpecItemTable()
	{
		$goods_shop_price = $_REQUEST['shop_price'];
		$goods_weight = $_REQUEST['weight'];
		$goods_cost_price = $_REQUEST['cost_price'];
		$goods_sn = $_REQUEST['sn'];
		
		$spec_conf = stripslashes($_REQUEST['spec_conf']);
		$spec_conf = json_decode($spec_conf);
		
		$spec1_count = count($spec_conf[0]);
		$spec2_count = count($spec_conf[1]);
		if($spec1_count>0)
		{
			$spec_type1_id = D("GoodsSpec")->where("id=".intval($spec_conf[0][0]))->getField("spec_type_id");
		}
		if($spec2_count>0)
		{
			$spec_type2_id = D("GoodsSpec")->where("id=".intval($spec_conf[1][0]))->getField("spec_type_id");
		}
		
		$spec_type1 = D("SpecType")->getById(intval($spec_type1_id));
		$spec_type2 = D("SpecType")->getById(intval($spec_type2_id));
		$this->assign("spec_type1",$spec_type1);
		$this->assign("spec_type2",$spec_type2);
		
		$spec_list = array();
		
		$user_group = D("UserGroup")->findAll();	
		if($spec1_count>0&&$spec2_count>0) //两维都有规格
		{
			$goods_id = intval(D("GoodsSpec")->where("id=".$spec_conf[0][0])->getField("goods_id"));
			$weight_unit = D("Goods")->where("id=".$goods_id)->getField("weight_unit");
			$count=0;
			for($i=0;$i<$spec1_count;$i++)
			{
				for($j=0;$j<$spec2_count;$j++)
				{
					$count++;
					$spec_item = array();
					if($goods_id>0)
					{
						$goods_spec_item = D("GoodsSpecItem")->where("spec1_id=".$spec_conf[0][$i]." and spec2_id=".$spec_conf[1][$j]." and goods_id=".$goods_id)->find();
						if($goods_spec_item)
						{
							$spec_item['id'] = $goods_spec_item['id'];
							$spec_item['sn'] = $goods_spec_item['sn'];
							$spec_item['weight'] = fromBaseWeight($goods_spec_item['weight'],$weight_unit);
							$spec_item['stock'] = $goods_spec_item['stock'];
							$spec_item['shop_price'] = $goods_spec_item['shop_price'];
							$spec_item['cost_price'] = $goods_spec_item['cost_price'];
							$spec_item['spec_type1'] = D("GoodsSpec")->getById($goods_spec_item['spec1_id']);
							$spec_item['spec_type2'] = D("GoodsSpec")->getById($goods_spec_item['spec2_id']);
							foreach($user_group as $k=>$v)
							{
								$user_group[$k][$goods_spec_item['id']]['price'] = D("UserGroupPrice")->where("user_group_id=".$v['id']." and spec_item_id=".$goods_spec_item['id'])->getField("user_price");
							}
						}
						else
						{
							$spec_item['sn'] = D("Goods")->where("id=".$goods_id)->getField("sn")."_".(D("GoodsSpecItem")->where("goods_id=".$goods_id)->count()+$count);
							$spec_item['weight'] = fromBaseWeight(D("Goods")->where("id=".$goods_id)->getField("weight"),$weight_unit);
							$spec_item['shop_price'] = D("Goods")->where("id=".$goods_id)->getField("shop_price");
							$spec_item['cost_price'] = D("Goods")->where("id=".$goods_id)->getField("cost_price");
						}
					}
					else 
					{
						if($goods_sn!='')
						$spec_item['sn'] = $goods_sn."_".$count;
						else
						$spec_item['sn'] = '';
						$spec_item['weight'] = $goods_weight;
						$spec_item['shop_price'] = $goods_shop_price;
						$spec_item['cost_price'] = $goods_cost_price;
					}
					$spec_item['spec_type1'] = D('GoodsSpec')->getById($spec_conf[0][$i]);
					$spec_item['spec_type2'] = D('GoodsSpec')->getById($spec_conf[1][$j]);
					$spec_item['stock'] = 1;
					$spec_list[] = $spec_item;
				}
			}
		}
		elseif($spec1_count>0)
		{
			$goods_id = intval(D("GoodsSpec")->where("id=".$spec_conf[0][0])->getField("goods_id"));
			$weight_unit = D("Goods")->where("id=".$goods_id)->getField("weight_unit");
			for($i=0,$count=1;$i<$spec1_count;$i++,$count++)
			{
				$spec_item = array();
				if($goods_id>0)
				{
						$goods_spec_item = D("GoodsSpecItem")->where("spec1_id=".intval($spec_conf[0][$i])." and spec2_id=".intval($spec_conf[1][$j])." and goods_id=".$goods_id)->find();
						if($goods_spec_item)
						{
							$spec_item['id'] = $goods_spec_item['id'];
							$spec_item['sn'] = $goods_spec_item['sn'];
							$spec_item['weight'] = fromBaseWeight($goods_spec_item['weight'],$weight_unit);
							$spec_item['stock'] = $goods_spec_item['stock'];
							$spec_item['shop_price'] = $goods_spec_item['shop_price'];
							$spec_item['cost_price'] = $goods_spec_item['cost_price'];
							$spec_item['spec_type1'] = D("GoodsSpec")->getById($goods_spec_item['spec1_id']);
							$spec_item['spec_type2'] = D("GoodsSpec")->getById($goods_spec_item['spec2_id']);
							foreach($user_group as $k=>$v)
							{
								$user_group[$k][$goods_spec_item['id']]['price'] = D("UserGroupPrice")->where("user_group_id=".$v['id']." and spec_item_id=".$goods_spec_item['id'])->getField("user_price");
							}
						}
						else
						{
							$spec_item['sn'] = D("Goods")->where("id=".$goods_id)->getField("sn")."_".(D("GoodsSpecItem")->where("goods_id=".$goods_id)->count()+$count);
							$spec_item['weight'] = fromBaseWeight(D("Goods")->where("id=".$goods_id)->getField("weight"),$weight_unit);
							$spec_item['shop_price'] = D("Goods")->where("id=".$goods_id)->getField("shop_price");
							$spec_item['cost_price'] = D("Goods")->where("id=".$goods_id)->getField("cost_price");
						
						}
				}
				else 
				{
						if($goods_sn!='')
						$spec_item['sn'] = $goods_sn."_".$count;
						else
						$spec_item['sn'] = '';
						$spec_item['weight'] = $goods_weight;
						$spec_item['shop_price'] = $goods_shop_price;
						$spec_item['cost_price'] = $goods_cost_price;
				}
				$spec_item['spec_type1'] = D('GoodsSpec')->getById($spec_conf[0][$i]);
				$spec_item['stock'] = 1;
				$spec_list[] = $spec_item;
			}
		}
		else 
		{
			$goods_id = intval(D("GoodsSpec")->where("id=".$spec_conf[1][0])->getField("goods_id"));
			$weight_unit = D("Goods")->where("id=".$goods_id)->getField("weight_unit");
			for($j=0,$count=1;$j<$spec2_count;$j++,$count++)
			{
				$spec_item = array();
				if($goods_id>0)
				{
					$goods_spec_item = D("GoodsSpecItem")->where("spec1_id=".intval($spec_conf[0][$i])." and spec2_id=".intval($spec_conf[1][$j])." and goods_id=".$goods_id)->find();
					if($goods_spec_item)
					{
							$spec_item['id'] = $goods_spec_item['id'];
							$spec_item['sn'] = $goods_spec_item['sn'];
							$spec_item['weight'] = fromBaseWeight($goods_spec_item['weight'],$weight_unit);
							$spec_item['stock'] = $goods_spec_item['stock'];
							$spec_item['shop_price'] = $goods_spec_item['shop_price'];
							$spec_item['cost_price'] = $goods_spec_item['cost_price'];
							$spec_item['spec_type1'] = D("GoodsSpec")->getById($goods_spec_item['spec1_id']);
							$spec_item['spec_type2'] = D("GoodsSpec")->getById($goods_spec_item['spec2_id']);
							foreach($user_group as $k=>$v)
							{
								$user_group[$k][$goods_spec_item['id']]['price'] = D("UserGroupPrice")->where("user_group_id=".$v['id']." and spec_item_id=".$goods_spec_item['id'])->getField("user_price");
							}
					}
					else
					{
						$spec_item['sn'] = D("Goods")->where("id=".$goods_id)->getField("sn")."_".(D("GoodsSpecItem")->where("goods_id=".$goods_id)->count()+$count);
						$spec_item['weight'] = fromBaseWeight(D("Goods")->where("id=".$goods_id)->getField("weight"),$weight_unit);
						$spec_item['shop_price'] = D("Goods")->where("id=".$goods_id)->getField("shop_price");
						$spec_item['cost_price'] = D("Goods")->where("id=".$goods_id)->getField("cost_price");
					}
				}
				else 
				{
					if($goods_sn!='')
					$spec_item['sn'] = $goods_sn."_".$count;
					else
					$spec_item['sn'] = '';
					$spec_item['weight'] = $goods_weight;
					$spec_item['shop_price'] = $goods_shop_price;
					$spec_item['cost_price'] = $goods_cost_price;
				}
				$spec_item['spec_type2'] = D('GoodsSpec')->getById($spec_conf[1][$j]);
				$spec_item['stock'] = 1;
				$spec_list[] = $spec_item;
			}
		}
		$this->assign("spec_list",$spec_list);		

		
		
		foreach($user_group as $k=>$v)
		{
			$user_group[$k]['name'] = $v['name_'.DEFAULT_LANG_ID];
		}
				
		foreach($user_group as $k=>$v)
		{
			$user_group[$k][0]['price'] = D("UserGroupPrice")->where("goods_id=".$goods_id." and user_group_id=".$v['id']." and spec_item_id=0")->getField("user_price");			
		}
		$this->assign('spec_item_user_group',$user_group);
		$this->display();
		
	}


   //商品规格选择
    public function search()
    {
		import('ORG.Util.HashMap');
		
		if(intval($_REQUEST['cate_id'])!=0)
		{
			$cate_ids = D("GoodsCate")->getChildIds(intval($_REQUEST['cate_id']));
			$cate_ids[] = intval($_REQUEST['cate_id']);
			$map['cate_id'] = array("in",$cate_ids);
		}
		else 
		unset($map['cate_id']);
		
		$this->assign("cate_id",$_REQUEST['cate_id']);
		
		$this->assign("map",$map);
		$lang_envs = D("LangConf")->findAll();
		$lang_ids = array();
		$dispname_arr = array();
		$lang_names = array();
		foreach($lang_envs as $lang_item)
		{
			$dispname_arr[] = "name_".$lang_item['id'];
			$lang_ids[]=$lang_item['id'];
			$lang_names[] = $lang_item['lang_name'];
		}
		
		$default_lang_id = D("LangConf")->where("lang_name='".C('DEFAULT_LANG')."'")->field("id")->find();
		$default_lang_id = $default_lang_id['id'];  //默认语言的ID
		$select_dispname = "name_".$default_lang_id;
		
		$lang_ids = implode(",",$lang_ids);
		$this->assign("lang_ids",$lang_ids);
		$lang_names = implode(",",$lang_names);
		$this->assign("lang_names",$lang_names);

		$cate_list = D("GoodsCate")-> where("status=1")-> findAll();
		$cate_list = D("GoodsCate")-> toFormatTree($cate_list,$dispname_arr);
		
		$this->assign("select_dispname",$select_dispname);
		$this->assign("default_lang_id",$default_lang_id);
		$this->assign("cate_list",$cate_list);		
		
		

		$Brand = D ( "Brand" );
		$BrandList = $Brand->findAll ( '', 'id,name', 'id' );
		$this->assign ( "brandList", $BrandList );
				
		$LANG_ID = DEFAULT_LANG_ID;
		$parameter = null;
		$sql_str = 'SELECT GoodsSpec.id AS id,'.
					'       GoodsSpec.sn AS goods_sn,'.
					'       GoodsSpec.goods_id AS goods_id,'.
					'       GoodsSpec.spec1_type_id AS spec1_type_id,'.
					'       GoodsSpec.spec1_id AS spec1_id,'.
					'       GoodsSpec.spec2_type_id AS spec2_type_id,'.
					'       GoodsSpec.spec2_id AS spec2_id,'.
					'       GoodsSpec.shop_price AS shop_price,'.
					'       GoodsSpec.cost_price AS cost_price,'.
					'       GoodsSpec.stock AS goods_stock,'.
					'       GoodsSpec.weight AS goods_weight,'.
					'       Goods.name_'.$LANG_ID.' AS goods_name,'.
					'       Goods.brand_id AS brand_id,'.
					'       Goods.cate_id AS cate_id,'.
					'       Goods.status AS status,'.
					'       Goods.score AS score,'.
					'       GoodsSpecDetail_A.spec_name_'.$LANG_ID.' AS spec_name1,'.
					'       GoodsSpecDetail_B.spec_name_'.$LANG_ID.' AS spec_name2,'.
					'       concat(CASE'.
					'                WHEN GoodsSpecDetail_A.spec_name_'.$LANG_ID.' IS NULL THEN'.
					'                 \'\''.
					'                ELSE'.
					'                 GoodsSpecDetail_A.spec_name_'.$LANG_ID.''.
					'              END,'.
					'              CASE'.
					'                WHEN GoodsSpecDetail_b.spec_name_'.$LANG_ID.' IS NULL THEN'.
					'                 \'\''.
					'                ELSE'.
					'                 concat(\'、\', GoodsSpecDetail_b.spec_name_'.$LANG_ID.')'.
					'              END) as specname'.
					'  FROM fanwe_goods_spec_item GoodsSpec'.
					'  LEFT OUTER JOIN fanwe_goods Goods ON Goods.id = GoodsSpec.goods_id'.
					'  LEFT OUTER JOIN fanwe_goods_spec GoodsSpecDetail_A ON GoodsSpecDetail_A.id ='.
					'                                                        GoodsSpec.spec1_id'.
					'  LEFT OUTER JOIN fanwe_goods_spec GoodsSpecDetail_B ON GoodsSpecDetail_B.id ='.
					'                                                        GoodsSpec.spec2_id'.
					' WHERE Goods.status = 1';
		
		//dump($sql_str);
		
		$where = ' and 1=1';
		
		
		
		if (!empty($_REQUEST ['search_name'])){
			$where .= " and Goods.goods_name like '%'.$_REQUEST ['search_name'].'%'";
			
			$parameter	.= 'search_name='.urlencode($_REQUEST ['search_name']).'&';
			$this->assign ( "search_name", $_REQUEST['search_name']);
		}
		
    	if (!empty($_REQUEST ['search_category_id'])){
			$where .= ' and Goods.category_id = '.$_REQUEST ['search_category_id'];
			
			$parameter	.= 'search_category_id='.urlencode($_REQUEST ['search_category_id'])."&";
			$this->assign ( "search_category_id", $_REQUEST['search_category_id']);
		}		
		
        if (!empty($_REQUEST ['search_brand_id'])){
			$where .= ' and Goods.brand_id = '.$_REQUEST ['search_brand_id'];
			
			$parameter	.= 'search_brand_id='.urlencode($_REQUEST ['search_brand_id'])."&";
			$this->assign ( "search_brand_id", $_REQUEST['search_brand_id']);
		}		
         //dump($condition);
        
		$sql_str .= $where .' ORDER BY GoodsSpec.id asc';
		
         //创建数据对象
        $model = D($this->name);		
        $voList = $this->_Sql_list($model, $sql_str, $parameter);
        foreach($voList as $k=>$vo){
        	if (empty($vo['specname']))
        	{
        		$voList[$k]['specname'] = $vo['goods_name'];
        	}else{
        		$voList[$k]['specname'] = $vo['goods_name'].'('.$vo['specname'].')';
        	}	
        }
        $this->assign('list', $voList);
		//dump(count($rs));
		//dump($rs);
         //创建数据对象
        //$model = D("GoodsSpecView");
        //查找满足条件的列表数据
        //$this->_list($model,$condition,'',true,$parameter);
        
        $this->display();
    }	
    
    //输出预设
	public function showDefault()
	{
		$is_default = intval($_REQUEST['is_default']);
		$goods_id = intval($_REQUEST['goods_id']);
		$spec_item_list = D("GoodsSpecItem")->where("goods_id=".$goods_id)->findAll();
		$exist_spec_ids = array();
		foreach($spec_item_list as $k=>$v)
		{
			array_push($exist_spec_ids,$v['spec1_id']);
			array_push($exist_spec_ids,$v['spec2_id']);
		}
		if(count($exist_spec_ids)==0)
		{
			$exist_spec_ids=0;
		}
		else
		{
			$exist_spec_ids = implode(",",$exist_spec_ids);
		}

		$spec_list = D("GoodsSpec")->where("goods_id=".$goods_id." and id not in(".$exist_spec_ids.") and session_id=''")->findAll();
		foreach($spec_list as $spec_item)
			{
				if(D("Spec")->where("img='".$spec_item['img']."'")->count()==0)
				{
					@unlink($this->getRealPath().$spec_item['img']);
				}
			}
	 	D("GoodsSpec")->where("goods_id=".$goods_id." and id not in(".$exist_spec_ids.") and session_id=''")->delete();
	 	//输出商品规格列表组合
	 	
	 	
	 	//取出当前商品的规格类型ID
		$spec_type1 = D("SpecType")->getById(intval(D("GoodsSpec")->where("goods_id=".$goods_id." and idx=1")->getField("spec_type_id")));
		$spec_type2 = D("SpecType")->getById(intval(D("GoodsSpec")->where("goods_id=".$goods_id." and idx=2")->getField("spec_type_id")));
		$this->assign("spec_type1",$spec_type1);
		$this->assign("spec_type2",$spec_type2);
		
		//输出规格一
		$idx = 1;
		$session_id = Session::id();
		//插入不存在的预设
		$res = D("GoodsSpec")->where("goods_id=".$goods_id." and idx=".$idx)->findAll();
		$exist_spec_conf1_ids = array();
		foreach($res as $k=>$v)
		{
			array_push($exist_spec_conf1_ids,$v['spec_id']);
		}
		if(count($exist_spec_conf1_ids)==0)
		{
			$exist_spec_conf1_ids = 0;
		}
		else 
		{
			$exist_spec_conf1_ids = implode(",",$exist_spec_conf1_ids);
		}
		$not_exist_spec = D("Spec")->where("spec_type_id=".intval($spec_type1['id'])." and id not in(".$exist_spec_conf1_ids.")")->findAll();
		if($is_default==0)
		{
			D("GoodsSpec")->where("goods_id=".$goods_id." and idx=".$idx." and session_id!=''")->delete();
		}
		else
		foreach ($not_exist_spec as $item)
		{
			$item['idx'] = $idx;
			$item['goods_id'] = $goods_id;
			$item['session_id'] = $session_id;
			$item['spec_id'] = $item['id'];
			$item['id'] = 0;			
			D("GoodsSpec")->add($item);
		}
		$res = D("GoodsSpec")->where("goods_id=".$goods_id." and idx=".$idx)->findAll();
		foreach($res as $k=>$v)
		{
			$spec1[$v['spec_type_id']]['spec_type'] = D("SpecType")->getById($v['spec_type_id']);
			$spec1[$v['spec_type_id']]['spec_type']['name'] = $spec1[$v['spec_type_id']]['spec_type']['name_'.DEFAULT_LANG_ID];
			$spec1[$v['spec_type_id']]['spec_list'][] = $v;			
		}		
		$this->assign('idx',$idx);
		$this->assign("spec_list",$spec1);
		
		$spec_list1_str = $this->fetch("GoodsSpec:loadSpecConf");			
		


		//输出规格二
		$idx = 2;
		$session_id = Session::id();
		//插入不存在的预设
		$res = D("GoodsSpec")->where("goods_id=".$goods_id." and idx=".$idx)->findAll();
		$exist_spec_conf2_ids = array();
		foreach($res as $k=>$v)
		{
			array_push($exist_spec_conf2_ids,$v['spec_id']);
		}
		if(count($exist_spec_conf2_ids)==0)
		{
			$exist_spec_conf2_ids = 0;
		}
		else 
		{
			$exist_spec_conf2_ids = implode(",",$exist_spec_conf2_ids);
		}
		$not_exist_spec = D("Spec")->where("spec_type_id=".intval($spec_type2['id'])." and id not in(".$exist_spec_conf2_ids.")")->findAll();
		
		if($is_default==0)
		{
			D("GoodsSpec")->where("goods_id=".$goods_id." and idx=".$idx." and session_id!=''")->delete();
		}
		else
		foreach ($not_exist_spec as $item)
		{
			$item['idx'] = $idx;
			$item['goods_id'] = $goods_id;
			$item['session_id'] = $session_id;
			$item['spec_id'] = $item['id'];
			$item['id'] = 0;			
			D("GoodsSpec")->add($item);
		}
		$res = D("GoodsSpec")->where("goods_id=".$goods_id." and idx=".$idx)->findAll();
		foreach($res as $k=>$v)
		{
			$spec2[$v['spec_type_id']]['spec_type'] = D("SpecType")->getById($v['spec_type_id']);
			$spec2[$v['spec_type_id']]['spec_type']['name'] = $spec2[$v['spec_type_id']]['spec_type']['name_'.DEFAULT_LANG_ID];
			$spec2[$v['spec_type_id']]['spec_list'][] = $v;			
		}		
		$this->assign('idx',$idx);
		$this->assign("spec_list",$spec2);
		
		$spec_list2_str = $this->fetch("GoodsSpec:loadSpecConf");			
		echo $spec_list1_str."|".$spec_list2_str;
	}
	
}
?>