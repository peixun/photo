<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 商品
class GoodsAction extends CommonAction{
	//查询
	public function search() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		$map['status'] = array('in','0,1');


		if(intval($_REQUEST['cate_id'])!=0)
		{
			$cate_ids = D("GoodsCate")->getChildIds(intval($_REQUEST['cate_id']));
			$cate_ids[] = intval($_REQUEST['cate_id']);
			$map['cate_id'] = array("in",$cate_ids);
		}
		else
		unset($map['cate_id']);

		$this->assign("cate_id",$_REQUEST['cate_id']);

		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}

		if(intval($_REQUEST['city_id'])==0)
		   $map['city_id'] = array('in',$_SESSION['admin_city_ids']);

		if(intval($_REQUEST['suppliers_id'])==0)
		   unset($map['suppliers_id']);

		$this->assign("cate_id",$_REQUEST['cate_id']);
		$this->assign("city_id",$_REQUEST['city_id']);
		$this->assign("suppliers_id",$_REQUEST['suppliers_id']);
		$this->assign("goods_name",$_REQUEST['name']);

		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->assign("map",$map);
		$lang_envs = D("LangConf")->findAll();
        echo 'ok';
        dump($lang_envs);
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

		//商品分类
		$city_list = D("GroupCity")->where("status=1")->order("is_defalut desc,id asc")->findAll();
		$this->assign("city_list",$city_list);
		//供应商家
		$suppliers_list = D("Suppliers")->where("status=1")->findAll();
		$this->assign("suppliers_list",$suppliers_list);

		$this->display ("Goods:search");
		return;
	}
	//列表
	public function index() {
		//列表过滤器，生成查询Map对象
		if(!isset($_REQUEST['is_group_fail']))$_REQUEST['is_group_fail'] = 3;
		$map = $this->_search ();

		$map['status'] = array('in','0,1');
		$map['city_id'] = $_REQUEST['city_id'];
		if(intval($_REQUEST['cate_id'])!=0)
		{
			$cate_ids = D("GoodsCate")->getChildIds(intval($_REQUEST['cate_id']));
			$cate_ids[] = intval($_REQUEST['cate_id']);
			$map['cate_id'] = array("in",$cate_ids);
		}
		else
		unset($map['cate_id']);



		if(intval($_REQUEST['city_id'])==0)
		{
			if($_SESSION['all_city'])
			{
				unset($map['city_id']);
			}
			else
		   $map['city_id'] = array('in',$_SESSION['admin_city_ids']);
		}

		if(intval($_REQUEST['suppliers_id'])==0)
		   unset($map['suppliers_id']);

		if($_REQUEST['is_group_fail'] == 3)
			unset($map['is_group_fail']);

		$this->assign("cate_id",$_REQUEST['cate_id']);
		$this->assign("city_id",$_REQUEST['city_id']);
		$this->assign("suppliers_id",$_REQUEST['suppliers_id']);
		$this->assign("is_group_fail",$_REQUEST['is_group_fail']);
		$this->assign("goods_name",$_REQUEST['name']);

		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
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


		//商品分类
		$city_list = D("GroupCity")->where(array("status"=>1,"id"=>array("in",$_SESSION['admin_city_ids'])))->order("is_defalut desc,id asc")->findAll();
		$this->assign("city_list",$city_list);
		//供应商家
		$suppliers_list = D("Suppliers")->where("status=1")->findAll();
		$this->assign("suppliers_list",$suppliers_list);
       // echo '222';

		$this->display ();
		return;
	}
	//回收站列表
	public function trash() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		$map['status'] = -1;
		if(!$_SESSION['all_city'])
        $map['city_id'] = array('in',$_SESSION['admin_city_ids']);
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}

		$this->display ();
		return;
	}
	//增
	public function add()
	{
        //echo Session::id();
		$spec_list = D("GoodsSpec")->where("session_id='".Session::id()."' and goods_id=0")->findAll();
			foreach($spec_list as $spec_item)
			{
				if(D("Spec")->where("img='".$spec_item['img']."'")->count()==0)
				{
					@unlink($this->getRealPath().$spec_item['img']);
				}
			}
			D("GoodsSpec")->where("session_id='".Session::id()."' and goods_id=0")->delete();


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
		$lang_ids = implode(",",$lang_ids);
		$this->assign("lang_ids",$lang_ids);
		$lang_names = implode(",",$lang_names);
		$this->assign("lang_names",$lang_names);

		$cate_list = D("GoodsCate")-> where("status=1")-> findAll();
		$cate_list = D("GoodsCate")-> toFormatTree($cate_list,$dispname_arr);

		$type_list = D("GoodsType")->findAll();

		$brand_list = D("Brand")->findAll();

		$default_lang_id = D("LangConf")->where("lang_name='".C('DEFAULT_LANG')."'")->field("id")->find();
		$default_lang_id = $default_lang_id['id'];  //默认语言的ID
		$select_dispname = "name_".$default_lang_id;


		$new_sort = D(MODULE_NAME)->where("status=1") -> max("sort") + 1;

		//输出重量单位
		$weight_list = D("Weight")->findAll();
		foreach($weight_list as $kk=>$vv)
		{
			$weight_list[$kk]['name'] = $vv['name_'.$default_lang_id];
		}
		$this->assign('weight_list',$weight_list);

		//会员等级列表
		$user_group = D("UserGroup")->where("status=1")->findAll();
		$lang_curr_id = D("LangConf")->where("lang_name='".FANWE_LANG_SET."'")->getField("id");
		foreach($user_group as $k=>$v)
		{
			$user_group[$k]['name'] = $v['name_'.$lang_curr_id];
		}


		//输出规格类型
		$spec_type_list = D("SpecType")->findAll();
		foreach($spec_type_list as $k=>$type_item)
		{
			$spec_type_list[$k]['name'] = $type_item['name_'.DEFAULT_LANG_ID];
		}

		$suppliers_list = D("Suppliers")->where("status=1")->findAll();
		$city_list = D("GroupCity")->where(array("status"=>1,"id"=>array("in",$_SESSION['admin_city_ids'])))->order("is_defalut desc,id asc")->findAll();
		$this->assign("city_list",$city_list);
		$this->assign("spec_type_list",$spec_type_list);
		$this->assign("suppliers_list",$suppliers_list);
		$this->assign("user_group",$user_group);
		$this->assign('new_sort',$new_sort);
		$this->assign("select_dispname",$select_dispname);
		$this->assign("default_lang_id",$default_lang_id);
		$this->assign("cate_list",$cate_list);
		$this->assign("type_list",$type_list);
		$this->assign("brand_list",$brand_list);
		$this->assign("goods_id",0);
		$this->assign("session_id",$_SESSION['verify']);

		//输出默认开始与结束时间
		$this->assign("default_begin_time",toDate(gmtTime(),"Y-m-d H:i"));
		$this->assign("default_end_time",toDate(gmtTime()+3600*24*15,"Y-m-d H:i"));
		$this->display();
	}

	public function insert()
	{

		//开始检测货号
		if(D("Goods")->where("sn='".$_POST['sn']."'")->count()>0||D("GoodsSpecItem")->where("sn='".$_POST['sn']."'")->count()>0)
		{
			$this->error(L("GOODS_SN_EXIST"));
		}
		$_POST['weight'] = toBaseWeight($_POST['weight'],$_POST['weight_unit']);  //转换重量

		$name=$this->getActionName();
		$model = D ($name);
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}

		//保存当前数据对象
		$goods_id = $model->add ($data);

		if($goods_id)
		{
			$mail_msg = '';

			//属性
			D("GoodsAttr")->where("goods_id=".$goods_id)->delete();
			$attr_value = $_REQUEST['attr_value'];
			$attr_price = $_REQUEST['attr_price'];
			$attr_stock = $_REQUEST['attr_stock'];
		    if($attr_value)
			{

				foreach($attr_value as $attr_id=>$attr_list)
				{
					$attr_item = array();
					foreach($attr_list as $lang_id=>$val_list)
					{
						foreach($val_list as $row_idx=>$val)
						{
							$attr_item[$row_idx]['attr_id'] = $attr_id;
							$attr_item[$row_idx]['goods_id'] = $goods_id;
							$attr_item[$row_idx]['attr_value_'.$lang_id] = $val;
							$attr_item[$row_idx]['price'] = floatval($attr_price[$attr_id][$row_idx]);
							$attr_item[$row_idx]['stock'] = intval($attr_stock[$attr_id][$row_idx]);
						}
					}
					foreach ($attr_item as $val_item)
					{
						D("GoodsAttr")->add($val_item);
					}
				}
			}

			//图库
			$defalut_gallery_id = $_REQUEST['gallery_id'];
			if($_REQUEST['goods_gallerys'])
			{
				$goods_gallery = $_REQUEST['goods_gallerys'];
				foreach($goods_gallery as $gallery_id)
				{
					$gallery_item = D("GoodsGallery")->getById($gallery_id);
					$gallery_item['goods_id'] = $goods_id;
					if($gallery_item['id']==$defalut_gallery_id)
					{
						$gallery_item['is_default'] = 1;
					}
					D("GoodsGallery")->save($gallery_item);
				}
			}

			$goods_info = D("Goods")->getById($goods_id);

			//发送短信
			if(intval($_REQUEST['send_sms'])==1)
			{
				$goods_name = empty($goods_info['goods_short_name']) ? $goods_info['name_'.DEFAULT_LANG_ID] : $goods_info['goods_short_name'];

				$smsSend['send_title'] = "商品短信通知：".$goods_name;
				$smsSend['send_type'] = 1;
				$smsSend['type'] = 2;
				$smsSend['rec_id'] = $goods_id;

				$smsSend['send_content'] = "商品通知短信，发送时根据模板自动生成内容";

				$smsSend['user_group'] = 0;

				$send_time = empty($_REQUEST['sms_send_time']) ? 0 : localStrToTime($_REQUEST['sms_send_time']);

				$smsSend['send_time'] = $send_time;

				D("SmsSend")->add($smsSend);
			}

			//商品默认图片
			$goods_img_info = D("GoodsGallery")->getById($defalut_gallery_id);
			$goods_info['origin_img'] = $goods_img_info['origin_img'];
			$goods_info['big_img'] = $goods_img_info['big_img'];
			$goods_info['small_img'] = $goods_img_info['small_img'];


			$upload_list = $this->uploadFile(0,"goods/define_small_img");
			if($upload_list)
			{
				$define_small_img = $upload_list[0]['recpath'].$upload_list[0]['savename'];
			}
			else
			{
				$define_small_img = '';
			}

			$goods_info['define_small_img'] = $define_small_img;

			D("Goods")->save($goods_info);

			$default_spec_item['sn'] = $goods_info['sn'];
			$default_spec_item['goods_id'] = $goods_info['id'];
			$default_spec_item['shop_price'] = $goods_info['shop_price'];
			$default_spec_item['stock'] = $goods_info['stock'];
			$default_spec_item['weight'] = $goods_info['weight'];
			$default_spec_item['cost_price'] = $goods_info['cost_price'];
			$default_spec_item['spec1_type_id'] = 0;
			$default_spec_item['spec2_type_id'] = 0;
			$default_spec_item['spec1_id'] = 0;
			$default_spec_item['spec2_id'] = 0;
			$default_spec_id = D("GoodsSpecItem")->add($default_spec_item);


			$grcs = $_REQUEST['goods_reviews_content'];
			$grus = $_REQUEST['goods_reviews_user'];
			$grus1 = $_REQUEST['goods_reviews_url'];
			$grws = $_REQUEST['goods_reviews_web'];
			foreach($grcs as $k => $grc)
			{
				$grdata = array(
							"goods_id"=>$goods_info['id'],
							"user_name"=>$grus[$k],
							"url"=>$grus1[$k],
							"webname"=>$grws[$k],
							"content"=>$grc
						);

				D("GoodsReviews")->add($grdata);
			}

			if(intval($_REQUEST['send_email'])==1)
			{
				$mail_msg = D("MailTemplate")->sendGroupInfoMail(array($goods_id))?"发送邮件成功":"";
			}
			$msg = '添加团购'.$goods_info['name_'.DEFAULT_LANG_ID];
			$this->saveLog(1,$goods_id,$msg);
			$this->success (L('ADD_SUCCESS')." ".$mail_msg);

		  	if(C('HTML_CACHE_ON')) //开启静态缓存，则自动清空缓存 add by chenfq 2010-06-01
  			{
            	HtmlCache::delHtmlCache('Index','index', $goods_info['city_id']);
            	HtmlCache::delHtmlCache('Goods','show', $goods_info['city_id']);
  			}
		}
		else
		{
			$msg = '添加团购'.$goods_info['name_'.DEFAULT_LANG_ID];
			$this->saveLog(0,$goods_id,$msg);
			$this->error (L('ADD_FAILED'));
		}
	}

	//改
	public function edit()
	{
		$name=$this->getActionName();
		$model = M ( $name );
		$id = $_REQUEST [$model->getPk ()];
		if(!$_SESSION['all_city'])
		$vo = $model->where(array("id"=>$id,"city_id"=>array("in",$_SESSION['admin_city_ids'])))->find();
		else
		$vo = $model->getById($id);
		$vo['weight'] = fromBaseWeight($vo['weight'],$vo['weight_unit']);
		$this->assign ( 'vo', $vo );

		$gallery_list = D("GoodsGallery")->where("goods_id=".$vo['id'])->findAll();
		$this->assign("gallery_list",$gallery_list);
		$default_gallery = D("GoodsGallery")->where("goods_id=".$vo['id']." and is_default=1")->find();
		$this->assign("default_gallery",$default_gallery);

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
		$lang_ids = implode(",",$lang_ids);
		$this->assign("lang_ids",$lang_ids);
		$lang_names = implode(",",$lang_names);
		$this->assign("lang_names",$lang_names);

		$cate_list = D("GoodsCate")-> where("status=1")-> findAll();
		$cate_list = D("GoodsCate")-> toFormatTree($cate_list,$dispname_arr);

		$brand_list = D("Brand")->findAll();

		$default_lang_id = D("LangConf")->where("lang_name='".C('DEFAULT_LANG')."'")->field("id")->find();
		$default_lang_id = $default_lang_id['id'];  //默认语言的ID
		$select_dispname = "name_".$default_lang_id;

		//输出重量单位
		$weight_list = D("Weight")->findAll();
		foreach($weight_list as $kk=>$vv)
		{
			$weight_list[$kk]['name'] = $vv['name_'.$default_lang_id];
		}
		$this->assign('weight_list',$weight_list);
		$type_list = D("GoodsType")->findAll();

		$suppliers_list = D("Suppliers")->findAll();
		$reviews_list = D("GoodsReviews")->where("goods_id=".$id)->findAll();
		$city_list = D("GroupCity")->where(array("status"=>1,"id"=>array("in",$_SESSION['admin_city_ids'])))->order("is_defalut desc,id asc")->findAll();
		$this->assign("type_list",$type_list);
		$this->assign("city_list",$city_list);
		$this->assign("suppliers_list",$suppliers_list);
		$this->assign("reviews_list",$reviews_list);
		$this->assign("select_dispname",$select_dispname);
		$this->assign("default_lang_id",$default_lang_id);
		$this->assign("cate_list",$cate_list);
		$this->assign("brand_list",$brand_list);
		$this->assign("goods_id",$id);
		$this->assign("has_spec",D("GoodsSpecItem")->where("goods_id=".$id." and (spec1_id<>0 or spec2_id<>0)")->count());
		$this->assign("session_id",$_SESSION['verify']);
		$this->display();
	}

	public function update()
	{

		$_POST['weight'] = toBaseWeight($_POST['weight'],$_POST['weight_unit']);  //转换重量
		$name=$this->getActionName();
		$model = D ( $name );
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save ($data);

		$goods_id = intval($_REQUEST['id']); //商品ID

		if($list)
		{
			$mail_msg = '';
			if(intval($_REQUEST['send_email'])==1)
			{
				$mail_msg = D("MailTemplate")->sendGroupInfoMail(array($goods_id))?"发送邮件成功":"";
			}

			//属性
			D("GoodsAttr")->where("goods_id=".$goods_id)->delete();
			$attr_value = $_REQUEST['attr_value'];
			$attr_price = $_REQUEST['attr_price'];
			$attr_stock = $_REQUEST['attr_stock'];
		    if($attr_value)
			{

				foreach($attr_value as $attr_id=>$attr_list)
				{
					$attr_item = array();
					foreach($attr_list as $lang_id=>$val_list)
					{
						foreach($val_list as $row_idx=>$val)
						{
							$attr_item[$row_idx]['attr_id'] = $attr_id;
							$attr_item[$row_idx]['goods_id'] = $goods_id;
							$attr_item[$row_idx]['attr_value_'.$lang_id] = $val;
							$attr_item[$row_idx]['price'] = floatval($attr_price[$attr_id][$row_idx]);
							$attr_item[$row_idx]['stock'] = intval($attr_stock[$attr_id][$row_idx]);
						}
					}
					foreach ($attr_item as $val_item)
					{
						D("GoodsAttr")->add($val_item);
					}
				}
			}

			//图库
			$defalut_gallery_id = $_REQUEST['gallery_id'];
			if($_REQUEST['goods_gallerys'])
			{
				$goods_gallery = $_REQUEST['goods_gallerys'];
				D("GoodsGallery")->where("goods_id=".$goods_id)->setField('is_default','0');  //先初始化默认图

				foreach($goods_gallery as $gallery_id)
				{
					$gallery_item = D("GoodsGallery")->getById($gallery_id);
					$gallery_item['goods_id'] = $goods_id;
					if($gallery_item['id']==$defalut_gallery_id)
					{
						$gallery_item['is_default'] = 1;
					}
					D("GoodsGallery")->save($gallery_item);
				}
			}



			$goods_info = D("Goods")->getById($goods_id);

			//发送短信
			if(intval($_REQUEST['send_sms'])==1)
			{
				$goods_name = empty($goods_info['goods_short_name']) ? $goods_info['name_'.DEFAULT_LANG_ID] : $goods_info['goods_short_name'];

				$smsSend['send_title'] = "商品短信通知：".$goods_name;
				$smsSend['send_type'] = 1;
				$smsSend['type'] = 2;
				$smsSend['rec_id'] = $goods_id;

				$smsSend['send_content'] = "商品通知短信，发送时根据模板自动生成内容";

				$smsSend['user_group'] = 0;

				$send_time = empty($_REQUEST['sms_send_time']) ? 0 : localStrToTime($_REQUEST['sms_send_time']);

				$smsSend['send_time'] = $send_time;

				$ssid = D("SmsSend")->where("type = 2 and rec_id = $goods_id")->getField("id");

				if(intval($ssid) > 0)
				{
					$smsSend['id'] = $ssid;
					D("SmsSend")->save($smsSend);
				}
				else
					D("SmsSend")->add($smsSend);
			}

			//商品默认图片
			$goods_img_info = D("GoodsGallery")->getById($defalut_gallery_id);
			$goods_info['origin_img'] = $goods_img_info['origin_img'];
			$goods_info['big_img'] = $goods_img_info['big_img'];
			$goods_info['small_img'] = $goods_img_info['small_img'];

			$upload_list = $this->uploadFile(0,"goods/define_small_img");
			if($upload_list)
			{
				@unlink($this->getRealPath().$goods_info['define_small_img']);
				$define_small_img = $upload_list[0]['recpath'].$upload_list[0]['savename'];
			}
			else
			{
				$define_small_img = $goods_info['define_small_img'];
			}

			$goods_info['define_small_img'] = $define_small_img;

			D("Goods")->save($goods_info);

			$exist_spec_item = D("GoodsSpecItem")->where("goods_id=".$goods_id." and spec1_id = 0 and spec2_id = 0")->find();
			$exist_spec_item['sn'] = $goods_info['sn'];
			$exist_spec_item['stock'] = $goods_info['stock'];
			$exist_spec_item['shop_price'] = $goods_info['shop_price'];
			$exist_spec_item['weight'] = $goods_info['weight'];
			$exist_spec_item['cost_price'] = $goods_info['cost_price'];
			D("GoodsSpecItem")->save($exist_spec_item);

			$grids = $_REQUEST['goods_reviews_id'];
			$grcs = $_REQUEST['goods_reviews_content'];
			$grus = $_REQUEST['goods_reviews_user'];
			$grus1 = $_REQUEST['goods_reviews_url'];
			$grws = $_REQUEST['goods_reviews_web'];
			foreach($grids as $k => $grid)
			{
				if($grid > 0)
				{
					$grdata = array(
							"id" =>$grid,
							"goods_id"=>$goods_info['id'],
							"user_name"=>$grus[$k],
							"url"=>$grus1[$k],
							"webname"=>$grws[$k],
							"content"=>$grcs[$k]
						);
					D("GoodsReviews")->save($grdata);
				}
				else
				{
					$grdata = array(
							"goods_id"=>$goods_info['id'],
							"user_name"=>$grus[$k],
							"url"=>$grus1[$k],
							"webname"=>$grws[$k],
							"content"=>$grcs[$k]
						);
					D("GoodsReviews")->add($grdata);
				}
			}
			$msg = '修改团购'.$goods_info['name_'.DEFAULT_LANG_ID];
			$this->saveLog(1,$goods_id,$msg);
			$this->success (L('EDIT_SUCCESS')." ".$mail_msg);

  			if(C('HTML_CACHE_ON')) //开启静态缓存，则自动清空缓存 add by chenfq 2010-06-01
  			{
            	HtmlCache::delHtmlCache('Index','index', $goods_info['city_id']);
            	HtmlCache::delHtmlCache('Goods','show', $goods_info['city_id']);
  			}
		}
		else
		{
			$msg = '修改团购'.$goods_info['name_'.DEFAULT_LANG_ID];
			$this->saveLog(0,$goods_id,$msg);
			$this->error (L('EDIT_FAILED'));
		}
	}
	public function delete() {
		//删除指定记录
		$name=$this->getActionName();
		$model = M ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			$ids = explode ( ',', $id );
			$names = '';
			foreach($ids as $idd)
			{
				$names .= M("Goods")->where("id=".$idd)->getField("name_".DEFAULT_LANG_ID).",";
			}
			if($names!='')
			{
				$names = substr($names,0,strlen($names)-1);
			}
			if (isset ( $id )) {
				if(!$_SESSION['all_city'])
				$condition = array ($pk => array ('in', explode ( ',', $id ) ),"city_id"=>array("in",$_SESSION['admin_city_ids']) );
				else
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				$list=$model->where ( $condition )->setField ( 'status', - 1 );
				if ($list!==false) {
					$msg = '删除团购:'.$names;
					$this->saveLog(1,0,$msg);
					$this->success ( L('DEL_SUCCESS') );
				} else {
					$msg = '删除团购:'.$names;
					$this->saveLog(0,0,$msg);
					$this->error (L('DEL_FAILED'));
				}
			} else {
				$msg = '彻底删除团购,ID:'.$id;
				$this->saveLog(0,0,$msg);
				$this->error ( L('INVALID_OP') );
			}
		}
	}

	//永久删除
	public function foreverdelete() {
		//删除指定记录
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			$ids = explode ( ',', $id );
			$names = '';
			foreach($ids as $idd)
			{
				$names .= M("Goods")->where("id=".$idd)->getField("name_".DEFAULT_LANG_ID).",";
			}
			if($names!='')
			{
				$names = substr($names,0,strlen($names)-1);
			}
			if (isset ( $id )) {
				if(!$_SESSION['all_city'])
				$condition = array ($pk => array ('in', explode ( ',', $id ) ),"city_id"=>array("in",$_SESSION['admin_city_ids']) );
				else
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				$order_goods_list = M("OrderGoods")->where(array ("rec_id" => array ('in', explode ( ',', $id ) ) ))->findAll();
				foreach($order_goods_list as $orderitem)
				{
						M("Order")->where("id=".$orderitem['order_id'])->delete();
						D("OrderGoods")->where("order_id=".$orderitem['order_id'])->delete();
						M("OrderIncharge")->where("order_id=".$orderitem['order_id'])->delete();
						M("OrderUncharge")->where("order_id=".$orderitem['order_id'])->delete();
						M("OrderLog")->where("order_id=".$orderitem['order_id'])->delete();
						$order_re_consignment = M("OrderReConsignment")->where("order_id=".$orderitem['order_id'])->findAll();
						M("OrderReConsignment")->where("order_id=".$orderitem['order_id'])->delete();
						foreach($order_re_consignment as $reconsignment)
						{
							M("OrderReConsignmentGoods")->where("order_re_consignment_id = ".$reconsignment['id'])->delete();
						}
				}
				$goods_list = $model->where ( $condition )->findAll();  //要删除商品列表
				if (false !== $model->where ( $condition )->delete ()) {
					//echo $model->getlastsql();
					foreach($goods_list as $goods_item)
					{
						@unlink($this->getRealPath().$goods_item['define_small_img']);
						D("GoodsAttr")->where("goods_id=".$goods_item['id'])->delete();
						$gallery_list = D("GoodsGallery")->where("goods_id=".$goods_item['id'])->findAll();
						foreach($gallery_list as $item)
						{
							@unlink($this->getRealPath().$item['small_img']);
							@unlink($this->getRealPath().$item['big_img']);
							@unlink($this->getRealPath().$item['origin_img']);
						}
						D("GoodsGallery")->where("goods_id=".$goods_item['id'])->delete();
					}
					//开始删除相关的留言
					$msgList = D("Message")->where(array ("rec_id" => array ('in', explode ( ',', $id ) ),'rec_module'=>'Goods' ))->findAll();
					D("Message")->where(array ("rec_id" => array ('in', explode ( ',', $id ) ),'rec_module'=>'Goods' ))->delete();
					foreach($msgList as $msgItem)
					{
						D("Message")->where("pid=".$msgItem['id'])->delete();
					}

					//开始删除相关会员价
					D("UserGroupPrice")->where(array ("goods_id" => array ('in', explode ( ',', $id ) ) ))->delete();

					//开始删除相关规格
					D("GoodsSpecItem")->where(array ("goods_id" => array ('in', explode ( ',', $id ) ) ))->delete();
					$spec_img_list = D("GoodsSpec")->where(array ("goods_id" => array ('in', explode ( ',', $id ) ) ))->findAll();
					foreach($spec_img_list as $img_item)
					{
						if($img_item['define_img'] == 1)
						{
							@unlink($this->getRealPath().$img_item['img']);
						}
					}
					D("GoodsSpec")->where(array ("goods_id" => array ('in', explode ( ',', $id ) ) ))->delete();
					D("GoodsReviews")->where(array ("goods_id" => array ('in', explode ( ',', $id ) ) ))->delete();


					//删除方维卷
					D("GroupBond")->where(array ("goods_id" => array ('in', explode ( ',', $id ) ) ))->delete();

					$msg = '彻底删除团购:'.$names."ID:".$id;
					$this->saveLog(1,0,$msg);
					$this->success (L('DEL_SUCCESS'));
				} else {
					$msg = '彻底删除团购:'.$names."ID:".$id;
					$this->saveLog(0,0,$msg);
					$this->error (L('DEL_FAILED'));
				}
			} else {
				$msg = '彻底删除团购:'.$names;
				$this->saveLog(0,0,$msg);
				$this->error ( L('INVALID_OP') );
			}
		}
		$this->forward ();
	}
	//移动商品至分类
	public function moveGoods()
	{
		$name=$this->getActionName();
			$model = M ($name);
			if (! empty ( $model )) {
				$pk = $model->getPk ();
				$id = $_REQUEST [$pk];
				$ids = explode ( ',', $id );
				$names = '';
				foreach($ids as $idd)
				{
					$names .= M("Goods")->where("id=".$idd)->getField("name_".DEFAULT_LANG_ID).",";
				}
				if($names!='')
				{
					$names = substr($names,0,strlen($names)-1);
				}
				$cate_id = intval($_REQUEST['cate_id']);
				$city_id = intval($_REQUEST['city_id']);
				$suppliers_id = intval($_REQUEST['suppliers_id']);

				if (isset ( $id ) && ($cate_id > 0 || $city_id >0 || $suppliers_id > 0)) {

					if(!$_SESSION['all_city'])
					$condition = array ($pk => array ('in', explode ( ',', $id ) ),"city_id"=>array("in",$_SESSION['admin_city_ids']) );
					else
					$condition = array ($pk => array ('in', explode ( ',', $id ) ) );

					if ($cate_id > 0)
						$list = $model->where ( $condition )->setField('cate_id', $cate_id);

					if(!$_SESSION['all_city'])
					{
					if ($city_id > 0&&in_array($city_id,$_SESSION['admin_city_ids']))
						$list = $model->where ( $condition )->setField('city_id', $city_id);
					}
					else
					{
						$list = $model->where ( $condition )->setField('city_id', $city_id);
					}

					if ($suppliers_id > 0)
						$list = $model->where ( $condition )->setField('suppliers_id', $suppliers_id);


					if ($list!==false) {
						$msg = '移动团购:'.$names;
						$this->saveLog(1,0,$msg);
						$this->success ( L('MOVE_SUCCESS') );
					} else {
						$msg = '移动团购:'.$names;
						$this->saveLog(0,0,$msg);
						$this->error (L('MOVE_FAILED'));
					}
				} else {
					$msg = '移动团购:'.$names;
					$this->saveLog(0,0,$msg);
					$this->error ( L('INVALID_OP') );
				}
			}
		$this->forward ();
	}
	//获取图片信息
	public function setGallery()
	{
		$id = $_REQUEST['id'];
		$gallery = D("GoodsGallery")->getById($id);
		echo json_encode($gallery);
	}
	//删除图片
	public function delGallery()
	{
		$id = $_REQUEST['id'];
		$item = D("GoodsGallery")->getById($id);
		@unlink($this->getRealPath().$item['origin_img']);
		@unlink($this->getRealPath().$item['big_img']);
		@unlink($this->getRealPath().$item['small_img']);
		D("GoodsGallery")->where('id='.$id)->delete();

	}

	//获取类型的属性列表
	public function getTypeAttr()
	{
		$lang_envs = D("LangConf")->findAll();
		$type_id = $_REQUEST['type_id'];
		$goods_id = $_REQUEST['goods_id'];
		$attr_list = D("GoodsTypeAttr")->where("type_id=".$type_id)->findAll();
		if($attr_list)
		{
			foreach($attr_list as $k=>$attr_item)
			{
				$value_list = D("GoodsAttr")->where("attr_id=".$attr_item['id']." and goods_id=".$goods_id)->findAll();
				//获取出当前属性下的所有属性值

				$attr_list[$k]['row_count'] = D("GoodsAttr")->where("attr_id=".$attr_item['id']." and goods_id=".$goods_id)->count();

				foreach($value_list as $value_key => $value_row)
				{
					foreach($lang_envs as $lang_item)
					{
						//已有值
						$attr_list[$k]['value_'.$lang_item['id']][$value_key] = $value_row['attr_value_'.$lang_item['id']]?trim($value_row['attr_value_'.$lang_item['id']]):"";
						$attr_list[$k]['price'][$value_key] = $value_row['price']?$value_row['price']:"";
						//$attr_list[$k]['stock'][$value_key] = $value_row['stock']?$value_row['stock']:"";
					}
				}

				foreach($lang_envs as $lang_item)
				{
					//可选值
					$attr_list[$k]['attr_value_'.$lang_item['id']] = explode("\n",$attr_item['attr_value_'.$lang_item['id']]);
					foreach($attr_list[$k]['attr_value_'.$lang_item['id']] as $kkk=>$vvv)
					{
						$attr_list[$k]['attr_value_'.$lang_item['id']][$kkk] = trim($vvv);
					}
				}
			}
		}
		else
		{
			$attr_list = array();
		}

		echo json_encode($attr_list);
	}

	//删除点评
	public function delReviews()
	{
		$id = $_REQUEST['id'];
		D("GoodsReviews")->where('id='.$id)->delete();
		echo "1";
	}

	public function order()
	{
		$goodsID = intval($_REQUEST['goodsID']);
		if(!$_SESSION['all_city'])
		$goods = D("Goods")->where(array("id"=>$goodsID,"city_id"=>array("in",$_SESSION['admin_city_ids'])))->find();
		else
		$goods = D("Goods")->getById($goodsID);
		$time = gmtTime();
		$typeID = $goods['type_id'];

		if($goods['is_group_fail'] == 1)
		{

		}
		elseif((intval($goods['promote_end_time']) < $time) || ($goods['buy_count'] >= $goods['group_user']))
		{
			if($typeID == 0 || $typeID == 2)
			{

				$default_lang_id = D("LangConf")->where("lang_name='".C('DEFAULT_LANG')."'")->field("id")->find();
				$default_lang_id = $default_lang_id['id'];  //默认语言的ID
				//$select_dispname = "name_".$default_lang_id;


				$sql = "select o.*,og.number from ".C("DB_PREFIX")."order as o left join ".C("DB_PREFIX")."order_goods  as og on og.order_id = o.id where og.rec_id = '$goodsID' and o.money_status = 2";
				$orderList = M()->query($sql);

				//计算需要几张团购卷
				$sql = "select sum(og.number) as number from ".C("DB_PREFIX")."order as o left join ".C("DB_PREFIX")."order_goods  as og on og.order_id = o.id where og.rec_id = '$goodsID' and o.money_status = 2";
				$number = M()->query($sql);
				$total = intval($number[0]['number']);

				//已经有的团购卷
				M("GroupBond")->where("goods_id = '$goodsID' and status = 0 and user_id > 0")->setField('status',1);
				$groupBonds = D("GroupBond")->where("goods_id = '$goodsID'")->findAll();
				$bond_count = intval(count($groupBonds));

				$goods_name = D("Goods")->where('id='.$goodsID)->getField("goods_short_name");//修改 by hc， 团购券名称默认取简称
				if($goods_name=='')
				$goods_name = D("Goods")->where('id='.$goodsID)->getField("name_".$default_lang_id);
				//自动补全团购卷数量
				$groupBond_m = D ("GroupBond");
				for ($i = $bond_count; $i < $total; $i++){
					$groupBond = $groupBond_m->create();

					$tempsn = gen_groupbond_sn($goodsID);
					//$groupBond['id'] = null;
					$groupBond['goods_id'] = $goodsID;
					$groupBond['goods_name'] = $goods_name;
					$groupBond['sn'] = $tempsn;
					$password = unpack('H8',str_shuffle(md5(uniqid())));
					$groupBond['password'] = $password[1];
					$groupBond['create_time'] = gmtTime();
					if (!empty($goods['group_bond_end_time'])){
						$groupBond['end_time'] = $goods['group_bond_end_time'];
					}else{
						$groupBond['end_time'] = gmtTime() + 3600 * 24 * 30; //设置一个月后过期
					}

					$groupBond['status'] = 0;
					$groupBond_m->add($groupBond);
					//dump($groupBond_m->getLastSql());
				}

				//查询未被使用的团购卷
				$groupBonds = D("GroupBond")->where("goods_id = '$goodsID' and user_id = 0")->findAll();
				$gbindex = 0;
				//dump($groupBonds);
				foreach($orderList as $order)
				{
					$number = intval($order['number']);//需要分配的团购卷数量
					//已经分配的团购卷数量
					//dump($number);
					$num = D("GroupBond")->where("goods_id = ".intval($goodsID)." and user_id = ".intval($order['user_id'])." and order_id = '".$order['sn']."'")->count();
					//dump(D("GroupBond")->getLastSql());
					//dump(intval($num));
					//还未分配的 = 需要分配的团购卷数量 - 已经分配的团购卷数量
					$number = $number - intval($num);
					//dump(intval($number));
					for ($i = 0; $i < $number; $i++)
					{
						$groupBonds[$gbindex]['user_id'] = $order['user_id'];
						$groupBonds[$gbindex]['order_id'] = $order['sn'];
						$groupBonds[$gbindex]['status'] = 1;
						$groupBonds[$gbindex]['buy_time'] = $order['create_time'];
						$groupBonds[$gbindex]['is_valid'] = 1;
						if($order['attr']!='')
						$groupBonds[$gbindex]['goods_name'] = $groupBonds[$gbindex]['goods_name']."(".str_replace("\n",",",$order['attr']).")";
						 D("GroupBond")->save($groupBonds[$gbindex]);


						//发放团购卷时，自动短信通知
						if (eyooC('AUTO_SEND_SMS')){
							send_sms($order['user_id'], $groupBonds[$gbindex]['id']);
							//dump('AUTO_SEND_SMS');
						}

						if(eyooC("MAIL_ON")==1&&eyooC("SEND_GROUPBOND_MAIL") ==1)
						{
							send_grounp_bond_mail($order['user_id'],$groupBonds[$gbindex]['id']);
						}

						$gbindex++;

						if ($gbindex > count($groupBonds)){
							break;
						}
					}
					$order['goods_status'] = 5;//不需配送的商品，直接设置成：无需配送  add by chenfq 2010-05-06
					$order['status']=0;

					D("Order")->save($order);

					if ($gbindex > count($groupBonds)){
						break;
					}
				}
			}
		}

		$this->success ("处理完成！");
	}

	public function getOrderEditLink($gid)
	{
		$goods = D("Goods")->where("id = '$gid'")->find();
		$time = gmtTime();

		//if($goods['is_group_fail'] == 1)
		//{
		//	return "团购失败";
			//return "<a href='".u('Goods/order',array("goodsID"=>$gid))."'>退款到余额</a>";
		//}

		if (intval($goods['promote_begin_time']) > $time){//团购时间未开始
			return "团购未开始";
		}

		//团购时间到期 或 团购时间到期前，已经成功团购
		if((intval($goods['promote_end_time']) < $time) || (($goods['is_group_fail'] == 2) && ($goods['buy_count'] >= $goods['group_user'])))
		{
			if (($goods['type_id'] == 0 || $goods['type_id'] == 2) && ($goods['buy_count'] >= $goods['group_user']) && ($goods['buy_count'] > 0))
			{
				$orderGoodsCountArr = M()->query("select COALESCE(sum(og.number),0) as orderGoodsCount from ".M("OrderGoods")->getTableName()." as og left join ".M("Order")->getTableName()." as o on o.id = og.order_id AND o.money_status = 2 where og.rec_id = '$gid' and og.id is not null and o.id is not null");

				$orderGoodsCount =intval($orderGoodsCountArr[0]['orderGoodsCount']);

				//团购时间到期
				if(intval($goods['promote_end_time']) < $time)
					$str = "团购结束(成功)<br/>";
				else
					$str = "团购成功(未结束)<br/>";


				$str .= "需生成".$orderGoodsCount." 团购券 <br/>";

				$groupBondCount = D("GroupBond")->where("goods_id = '$gid'")->count();
				$str .= "已生成".$groupBondCount." 团购券 <br/>";

				$groupBondAssignCount = D("GroupBond")->where("goods_id = '$gid' and user_id > 0")->count();
				$str .= "已分配".$groupBondAssignCount." 团购券 <br/>";

				//$groupBondActiveCount = D("GroupBond")->where("goods_id = '$gid' and status > 0")->count();
				//$str .= "已激活".$groupBondActiveCount." 团购券 <br/>";

				if($orderGoodsCount > $groupBondCount || $orderGoodsCount> $groupBondAssignCount){
					$str .="<a href='".u('Goods/order',array("goodsID"=>$gid))."'>补全团购券</a>";
				}

//				if($orderGoodsCount > $groupBondCount)
//				{
//					//$str .="<a href='".u('GroupBond/index',array("goods_id"=>$gid))."'>手动生成团购券</a>";
//					$str .="<a href='".u('Goods/order',array("goodsID"=>$gid))."'>自动生成分配团购券</a>";
//				}
//				else
//				{
//					$str .="<a href='".u('Goods/order',array("goodsID"=>$gid))."'>".$gastr."分配团购券</a>";
//					//if($groupBondAssignCount > 0)
//					//	$str .="<br/><a href='".u('Goods/activeGroupBond',array("goodsID"=>$gid))."'>激活分配的团购券</a>";
//				}

				return $str;
			}
			else
			{
				//团购时间结束，且购买人数大于最低购买人数，则团购结束（成功）
				if (intval($goods['promote_end_time']) < $time && ($goods['buy_count'] == 0))//团购结束，未有人购买
					return "团购结束(失败)";
				else if(intval($goods['promote_end_time']) < $time && ($goods['buy_count'] >= $goods['group_user']))
					return "团购结束(成功)";
				else if (intval($goods['promote_end_time']) < $time && ($goods['buy_count'] < $goods['group_user']))//团购时间期
				{
					$orderCountArr = M()->query("select count(distinct og.order_id) as orderCount from ".M("OrderGoods")->getTableName()." as og left join ".M("Order")->getTableName()." as o on o.id = og.order_id AND o.money_status in(1,2,3) where og.rec_id = '$gid' and og.id is not null and o.id is not null and o.user_id >0");
					//dump(M()->getLastSql());
					$orderCount =intval($orderCountArr[0]['orderCount']);
					if ($orderCount > 0){
						return "团购结束(失败)<br/><a href='javascript:batchUncharge($gid)')>批量退款【订单数".$orderCount."】</a>";
					}else{
						return "团购结束(失败)<br/>未有付款订单";
					}
				}
				else if($goods['is_group_fail'] == 0)
					return "团购进行中";
				else
					return "团购进行中，已成功";
			}
		}else if (intval($goods['promote_end_time']) >= $time){//团购时间未到期
			return "团购进行中";
		}
	}

	public function activeGroupBond()
	{
		$goodsID = intval($_REQUEST['goodsID']);
		if(!$_SESSION['all_city'])
		$goodsID = D("Goods")->where(array("id"=>$goodsID,"city_id"=>array("in",$_SESSION['admin_city_ids'])))->getField("id");
		//dump($goodsID);
		D("GroupBond")->where("goods_id = '$goodsID' and user_id > 0")->setField("status",1);
		D("GroupBond")->query("update ".C("DB_PREFIX")."order set goods_status = 5,status=1 where sn in (SELECT order_id FROM ".C("DB_PREFIX")."group_bond WHERE goods_id = '$goodsID' and status = 1 group by order_id)");


		//发放团购卷时，自动短信通知 add by chenfq 2010-04-02
		if (eyooC('AUTO_SEND_SMS'))
		{

			$groupBondList = D("GroupBond")->where("goods_id = '$goodsID' and user_id > 0 and status = 1")->findAll();
			//dump($groupBondList);
			foreach($groupBondList as $groupBond){
				send_sms($groupBond['user_id'], $groupBond['id']);
			}
		}

		//dump(C('TMPL_ACTION_SUCCESS'));
		$this->assign ('jumpUrl', u('Goods/index'));
		$this->success (L('EDIT_SUCCESS'));
	}

	public function getGroupBondLink($typeID,$goodsID)
	{
		if($typeID == 0 || $typeID == 2)
		{
			return "<a href='".u('GroupBond/index',array("goods_id"=>$goodsID))."'>团购券管理</a><br><a href='".u('GroupBond/printGroupBond',array("goods_id"=>$goodsID))."' target='_blank'>打印</a>";
		}
	}

	public function showOrderList($buy_count, $goodsID)
	{
		return "<a href='".u('Order/index',array("money_status"=>"2","goods_id"=>"$goodsID"))."' target='_blank'>".$buy_count."</a>";
	}


	public function sendMail()
	{
		set_time_limit(0);
		$sendTime = $_REQUEST['sendTime'];
		$time = localStrToTime($sendTime);
		if($time==0)
		$time = gmtTime();
		$id = $_REQUEST ['id'];
		$goods_ids = explode ( ',', $id );

			$names = '';
			foreach($goods_ids as $idd)
			{
				$names .= M("Goods")->where("id=".$idd)->getField("name_".DEFAULT_LANG_ID).",";
			}
			if($names!='')
			{
				$names = substr($names,0,strlen($names)-1);
			}
		$rs = D("MailTemplate")->sendGroupInfoMail(explode ( ',', $id ),$time);
		if($rs)
		{

		$msg = '发送了团购邮件通知:'.$names;
		$this->saveLog(1,0,$msg);
		$this->success("邮件发送成功");
		}
		else
		{
			$msg = '发送了团购邮件通知:'.$names;
			$this->saveLog(0,0,$msg);
			$this->error("邮件发送失败");
		}
	}

	function statistics(){
		$id = intval($_REQUEST ['id']);
		if(!$_SESSION['all_city'])
		$id = D("Goods")->where(array("id"=>$id,"city_id"=>array("in",$_SESSION['admin_city_ids'])))->getField("id");
		$page = intval($_REQUEST["p"]);
    	if($page==0)
    		$page = 1;




		$vo = D('Goods')->getById ( $id );


		$sql =  'select sum(a.order_total_price) as order_total_price, '.
				'sum(a.order_incharge) as order_incharge, sum(a.ecv_money) as ecv_money, count(*) as num from '.C("DB_PREFIX").'order a '.
				'left join '.C("DB_PREFIX").'order_goods g on g.order_id = a.id '.
				'where g.rec_id = '.$id.' and a.money_status in (1,2,3)';

		$o_vo = M()->query($sql);
		$o_vo = $o_vo[0];

		$vo['order_total_price'] = $o_vo['order_total_price'];
		$vo['order_incharge'] = $o_vo['order_incharge'];
		$vo['ecv_money'] = $o_vo['ecv_money'];
		$vo['order_num'] = $o_vo['num'];


		$sql =  'select sum(a.order_total_price) as order_total_price, '.
				'sum(a.ecv_money) as ecv_money, count(*) as num from '.C("DB_PREFIX").'order a '.
				'left join '.C("DB_PREFIX").'order_goods g on g.order_id = a.id '.
				'where g.rec_id = '.$id.' and (a.repay_status in (1,2) or a.money_status in (1,2,3))';

		$o_vo = M()->query($sql);
		$o_vo = $o_vo[0];
		$vo['pay_total_price'] = $o_vo['order_total_price'];
		$vo['pay_order_num'] = $o_vo['num'];

		$sql = "select sum(oi.money) as money from ".C("DB_PREFIX")."order_incharge as oi ".
			   "left join ".C("DB_PREFIX")."order as o on o.id = oi.order_id ".
			   "left join ".C("DB_PREFIX")."order_goods g on g.order_id = o.id ".
			   'where g.rec_id = '.$id;
		$o_vo = M()->query($sql);
		$oimoney = $o_vo[0]['money'];

		$sql = "select sum(ou.money) as money from ".C("DB_PREFIX")."order_uncharge as ou ".
			   "left join ".C("DB_PREFIX")."order as o on o.id = ou.order_id ".
			   "left join ".C("DB_PREFIX")."order_goods g on g.order_id = o.id ".
			   'where g.rec_id = '.$id;
		$o_vo = M()->query($sql);
		$oumoney = $o_vo[0]['money'];

		$vo['pay_incharge'] = $oimoney - $oumoney;



		//已返利
		$sql = "select sum(r.score) as score, sum(r.money) as money ".
			   "from ".C("DB_PREFIX")."referrals as r ".
			   "where r.goods_id = ".$id." and r.is_pay = 1";
		$tmp_vo = M()->query($sql);
		$tmp_vo = $tmp_vo[0];
		$vo['ref_score_1'] = $tmp_vo['score'];
		$vo['ref_money_1'] = $tmp_vo['money'];

		//待返利
		$sql = "select sum(r.score) as score, sum(r.money) as money ".
			   "from ".C("DB_PREFIX")."referrals as r ".
			   "where r.goods_id = ".$id." and r.is_pay = 0";
		$tmp_vo = M()->query($sql);
		$tmp_vo = $tmp_vo[0];
		$vo['ref_score_0'] = $tmp_vo['score'];
		$vo['ref_money_0'] = $tmp_vo['money'];


		$this->assign("vo", $vo);



		$sql_payment = "select * from ".C("DB_PREFIX")."payment";
		$payment_list = M()->query($sql_payment);

		foreach($payment_list as $k=>$v)
		{


			$sql_oi = "select sum(oi.money) as oi_sum from ".C("DB_PREFIX")."order_incharge as oi left join ".C("DB_PREFIX")."order as o on o.id = oi.order_id left join ".C("DB_PREFIX")."order_goods as og on og.order_id = o.id where oi.payment_id=".$v['id']." and og.rec_id = ".$id;
			$oi = M()->query($sql_oi);
			$oi = $oi[0]['oi_sum'];

			$sql_ou = "select sum(ou.money) as ou_sum from ".C("DB_PREFIX")."order_uncharge as ou left join ".C("DB_PREFIX")."order as o on o.id = ou.order_id left join ".C("DB_PREFIX")."order_goods as og on og.order_id = o.id where ou.payment_id=".$v['id']." and og.rec_id = ".$id;
			$ou = M()->query($sql_ou);
			$ou = $ou[0]['ou_sum'];

			if($oi==0&&$ou==0)
			{
				unset($payment_list[$k]);
			}
			else
			{
				$or = $oi - $ou;

				$payment_list[$k]['name'] =  $v['name_1'];
				$payment_list[$k]['oimoney'] =  $oi;
				$payment_list[$k]['oumoney'] =  $ou;
				$payment_list[$k]['money'] =  $or;
			}
		}

		$this->assign("list", $payment_list);
		/*  未优化，大数据量时会造成卡死
		$sql =  'select a.payment_id,
		 				  (select name_1 as name from '.C("DB_PREFIX").'payment as p where p.id = a.payment_id) as name,
					       sum(a.oimoney) as oimoney,
					       sum(a.oumoney) as oumoney,
					       sum(a.oimoney) - sum(a.oumoney) as money
				 from (select oi.payment_id, sum(oi.money) as oimoney, 0 as oumoney
					          from '.C("DB_PREFIX").'order a
					          left join '.C("DB_PREFIX").'order_goods g on g.order_id = a.id
					          left join '.C("DB_PREFIX").'order_incharge oi on oi.order_id = a.id
					         where g.rec_id = '.$id.' and a.money_status in (1,2,3)
					         group by oi.payment_id
					        union all
					        select ou.payment_id, 0, sum(ou.money) as oumoney
					          from '.C("DB_PREFIX").'order a
					          left join '.C("DB_PREFIX").'order_goods g on g.order_id = a.id
					          left join '.C("DB_PREFIX").'order_uncharge ou on ou.order_id = a.id
					         where g.rec_id = '.$id.' and a.money_status in (1,2,3)
					         group by ou.payment_id) a where a.payment_id is not null
					 group by a.payment_id';

		$list = M()->query($sql);
		$this->assign("list", $list);
		*/
		//返利
		$page_count = C("PAGE_LISTROWS");
		$limit = ($page-1)*$page_count.",".$page_count;

		$sql = "select r.id,r.is_pay,r.score,r.money,u.user_name,p.user_name as parent_name ".
			   "from ".C("DB_PREFIX")."referrals as r ".
			   "left join ".C("DB_PREFIX")."user as u on u.id = r.user_id ".
			   "left join ".C("DB_PREFIX")."user as p on p.id = r.parent_id ".
			   "where r.goods_id = ".$id." order by r.id limit ".$limit;
		//dump($sql);
		$sql_count = "select count(*) as tt ".
			   "from ".C("DB_PREFIX")."referrals as r ".
			   "left join ".C("DB_PREFIX")."user as u on u.id = r.user_id ".
			   "left join ".C("DB_PREFIX")."user as p on p.id = r.parent_id ".
			   "where r.goods_id = ".$id;
		$list = M()->query($sql);
		$total = M()->query($sql_count);
		$total = $total[0]['tt'];
		$this->assign("referrals_list", $list);
		//dump($list);

			//分页
		$page = new Page($total,C("PAGE_LISTROWS"));   //初始化分页对象
		$p  =  $page->show();
        $this->assign('pages',$p);

		$this->display();
	}

	function hao123api(){
		$id = intval($_REQUEST ['id']);
		$vo = D('Goods')->getById ( $id );
		/**
		 * API接口数据格式：
		<?xml version="1.0" encoding="utf-8" ?>
		<urlset>
		　<url>
		　　 <loc>http://xxx.baidu.com/xxxxxx</loc>
		　　 <!-- 商品URLurl 256 bytes ［必填］-->
		　　 <data>
		　　 　<display>
		　　 　　 <website>美团</website>
		　　 　　 <!-- 站点名称 50 bytes ［必填］-->
		　　 　　 <siteurl>http://xxx.baidu.com</siteurl>
		　　 　　 <!-- 站点名称 256 bytes ［必填］-->
		　　 　　 <city>北京</city>
		　　 　　 <!-- 城市名称（城市名称不需要附带省、市、区、县等字，如果是全国范围请指明：全国） 16 bytes ［必填］ -->
		　　 　<title>原价188元的“玛雅蛋糕”仅售49元！</title>
		　　 　　 <!-- 商品标题 512 bytes［必填］ -->
		　　 　　 <image>http://maya.xxx.com/xxx.gif</image>
		　　 　　 <!-- 商品图片url 256 bytes ［必填］ -->
		　　 　　 <startTime>1275926400</startTime>
		　　 　　 <!-- 商品开始时间 10 bytes ［必填］-->
		　　 　　 <endTime>1291910399</endTime>
		　　 　　 <!-- 商品结束时间 10 bytes ［必填］-->
		　　 　　 <value>188.00</value>
		　　 　　 <!-- 商品原价 12 bytes ［必填］-->
		　　 　　 <price>49.00</price>
		　　 　　 <!-- 商品现价 12 bytes ［必填］-->
		　　 　　 <rebate>2.6</rebate>
		　　 　　 <!-- 商品折扣 6 bytes ［必填］-->
		　　 　　 <bought>100</bought>
		　　 　　 <!-- 已购买人数 10 bytes ［必填］-->
		　　 　</display>
		　　 </data>
		　</url>
		</urlset>
		 */

		//站点名称
		$shop_name = D('LangConf')->where("id=1")->getField('shop_name');
		//城市名称
		$city_name = D('GroupCity')->where("id=".intval($vo['city_id']))->getField('name');
		//商品名称
		$goods_name = $vo['name_1'];
		if(MAGIC_QUOTES_GPC){
			$shop_name = stripslashes(trim($shop_name));
			$city_name = stripslashes(trim($city_name));
			$goods_name = stripslashes(trim($goods_name));
		}


		//商品折扣
		if ($vo['market_price'] > 0)
			$rebate = number_format($vo['shop_price']/$vo['market_price'] * 10, 1);
		else
			$rebate = 0;

		//已购买人数
		$orderGoodsCountArr = M()->query("select COALESCE(sum(og.number),0) as orderGoodsCount from ".M("OrderGoods")->getTableName()." as og left join ".M("Order")->getTableName()." as o on o.id = og.order_id AND o.money_status = 2 where og.rec_id = '$id' and og.id is not null and o.id is not null");
		$bought =intval($orderGoodsCountArr[0]['orderGoodsCount']);
		/*
		$hao123api = '<?xml version="1.0" encoding="utf-8" ?>';
		$hao123api .= '<urlset>';
		$hao123api .= '<url>';
		$hao123api .= '<loc>http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/index.php?m=Goods&a=show&id='.$id.'</loc>';
		$hao123api .= '<data>';
		$hao123api .= '<display>';
		$hao123api .= '<website>'.D('LangConf')->where("id=1")->getField('show_name').'</website>';
		$hao123api .= '<siteurl>http://'.$_SERVER['HTTP_HOST'].__ROOT__.'</siteurl>';
		$hao123api .= '<city>'.D('GroupCity')->where("id=".intval($vo['city_id']))->getField('name').'</city>';
		$hao123api .= '<title>'.$vo['name_1'].'</title>';
		$hao123api .= '<image>http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/'.$vo['big_img'].'</image>';
		$hao123api .= '<startTime>'.$vo['promote_begin_time'].'</startTime>';
		$hao123api .= '<endTime>'.$vo['promote_end_time'].'</endTime>';
		$hao123api .= '<value>'.number_format($vo['market_price'],2).'</value>';
		$hao123api .= '<price>'.number_format($vo['shop_price'],2).'</price>';
		$hao123api .= '<rebate>'.$rebate.'</rebate>';
		$hao123api .= '<bought>'.$bought.'</bought>';
		$hao123api .= '</display>';
		$hao123api .= '</data>';
		$hao123api .= '</url>';
		$hao123api .= '</urlset>';
		*/
		$title = toDate(gmtTime(),'Y-m-d').'【'.$shop_name.'】提交【'.$city_name.'】团购商品API资料';
		$this->assign("title", $title);

$hao123api ='
<?xml version="1.0" encoding="utf-8" ?>
<urlset>
	<url>
		<loc>http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/index.php?m=Goods&a=show&id='.$id.'</loc>
		<data>
			<display>
				<website>'.$shop_name.'</website>
				<siteurl>http://'.$_SERVER['HTTP_HOST'].__ROOT__.'</siteurl>
				<city>'.$city_name.'</city>
				<title>'.$goods_name.'</title>
				<image>http://'.$_SERVER['HTTP_HOST'].__ROOT__.$vo['big_img'].'</image>
				<startTime>'.$vo['promote_begin_time'].'</startTime>
				<endTime>'.$vo['promote_end_time'].'</endTime>
				<value>'.number_format($vo['market_price'], 2).'</value>
				<price>'.number_format($vo['shop_price'], 2).'</price>
				<rebate>'.$rebate.'</rebate>
				<bought>'.$bought.'</bought>
			</display>
		</data>
	</url>
</urlset>';

		$this->assign("hao123api", $hao123api);

		$this->display();
	}

	function sendApiEmail(){
		$mail_content = trim($_POST['hao123Api']);
		$mail_title = trim($_POST['title']);
		$mail_address = trim($_POST['mail_address']);

		$mail = new Mail();
		$mail->IsHTML(0);
		$mail->Subject = $mail_title; // 标题
		$mail->Body =  $mail_content; // 内容
		$mail->AddAddress($mail_address);
		if(!$mail->Send())
		{
			$this->error($mail->ErrorInfo);
		}else{
			$this->success(L("SEND_SUCCESS"));
		}
	}

	function getGoodsSmsContent($goods_info = "")
	{
		$isReturn = true;

		if(empty($goods_info))
		{
			$goods_info['name_'.DEFAULT_LANG_ID] = $_REQUEST['name'];
			$goods_info['goods_short_name'] = $_REQUEST['short_name'];
			$goods_info['promote_begin_time'] = localStrToTimeMin($_REQUEST['begin_time']);
			$isReturn = false;
		}

		$goods_name = empty($goods_info['goods_short_name']) ? $goods_info['name_'.DEFAULT_LANG_ID] : $goods_info['goods_short_name'];

		$mail_template = D("MailTemplate")->where("name = 'goods_sms'")->find();

		if($mail_template)
		{
			$mt_vars = array("goods_name"=>$goods_name,"begin_time"=>toDate($goods_info['promote_begin_time'],'Y-m-d'));
			$send_content = templateFetch($mail_template['mail_content'],$mt_vars);
		}
		else
			$send_content = $goods_name;

		if($isReturn)
			return $send_content;
		else
			echo $send_content;
	}
}
?>