<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 邮件模块
class EmailAction extends CommonAction{
//	public function demo()
//	{
//		$mail = new Mail();	
//
//		$mail->AddAddress("fzmatthew@163.com","收件人名称");
//		$mail->IsHTML(true); // 设置邮件格式为 HTML
//		$mail->Subject = "测试发送邮件"; // 标题
//		$mail->Body = '<B>这是一封测试邮件</B>'; // 内容
//		
//		if(!$mail->Send())
//		{
//			echo "发送失败 <p>";
//			echo "错误信息: " . $mail->ErrorInfo;
//			exit;
//		}
//		
//		echo "发送成功";
//	}

	public function template()
	{
		$this->assign("template_list",D("MailTemplate")->findAll());
		$this->display();
	}
	public function loadTemplate()
	{

		$id = intval($_REQUEST['id']);
		$template_info = D("MailTemplate")->getById($id);
		//dump(D("MailTemplate")->findAll());
		//header('Content-Type: text/html; charset=utf-8');
		$str = $template_info['mail_content'];
		$str = str_replace("\n", "", $str);
		$str = str_replace("\r", "", $str);
		$template_info['mail_content'] = $str;
		
		$str = $template_info['mail_title'];
		$str = str_replace("\n", "", $str);
		$str = str_replace("\r", "", $str);
		$template_info['mail_title'] = $str;
				
		$this->ajaxReturn($template_info,'',1,'JSON');
		//echo json_encode($template_info);
	}
	public function updateTemplate()
	{
		
		$model = D ("MailTemplate");
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save ();
		if (false !== $list) {
			//成功提示
			$this->saveLog(1);
//			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('EDIT_SUCCESS'));
		} else {
			//错误提示
			$this->saveLog(0);
			$this->error (L('EDIT_FAILED'));
		}	
		/*
		header('Content-Type: text/html; charset=utf-8');
		
		
		D("MailTemplate")->create();
		if(D("MailTemplate")->save())
		{
			$this->success (L('EDIT_SUCCESS'));
		}
		else 
		{
			$this->error (L('EDIT_FAILED'));
		}
		*/
	}
	
	//邮件地址列表相关方法
	public function addressList()
	{
	//列表过滤器，生成查询Map对象
		$map = $this->_search ("MailAddressList");
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}

		$model = D ("MailAddressList");
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display();
	}
	public function forbid() {
		$name=$this->getActionName();
		$model = D ("MailAddressList");
		$pk = $model->getPk ();
		$id = $_REQUEST [$pk];
		$condition = array ($pk => array ('in', $id ) );
		$list=$model->forbid ( $condition );
		if ($list!==false) {			
			$this->saveLog(1);
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( L('FORBID_SUCCESS') );
		} else {
			$this->saveLog(0);
			$this->error  (  L('FORBID_FAILED') );
		}
	}
	public function resume() {
		//恢复指定记录
		$name=$this->getActionName();
		$model = D ("MailAddressList");
		$pk = $model->getPk ();
		$id = $_GET [$pk];
		$condition = array ($pk => array ('in', $id ) );
		if (false !== $model->resume ( $condition )) {
			$this->saveLog(1);
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( L('RESUME_SUCCESS') );
		} else {
			$this->saveLog(0);
			$this->error ( L('RESUME_FAILED') );
		}
	}
	public function addMailAddress()
	{
		$this->assign('city_list',M("GroupCity")->where("status=1")->findAll());
		$this->display();
	}
	public function editMailAddress()
	{
		$model = M ( "MailAddressList" );
		$id = $_REQUEST [$model->getPk ()];
		$vo = $model->getById ( $id );
		$this->assign ( 'vo', $vo );
		$this->assign('city_list',M("GroupCity")->where("status=1")->findAll());
		$this->display ();
	}
	public function insertMailAddress()
	{
		//B('FilterString');
		$model = D ("MailAddressList");
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$list=$model->add ();
		if ($list!==false) { //保存成功
			$this->saveLog(1,$list);
//			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('ADD_SUCCESS'));
		} else {
			//失败提示
			$this->saveLog(0,$list);
			$this->error (L('ADD_FAILED'));
		}
	}
	public function updateMailAddress() {
		//B('FilterString');
		$model = D ( "MailAddressList" );
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save ();
		if (false !== $list) {
			//成功提示
			$this->saveLog(1);
//			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('EDIT_SUCCESS'));
		} else {
			//错误提示
			$this->saveLog(0);
			$this->error (L('EDIT_FAILED'));
		}
	}
	public function foreverdeleteMailAddress() {
		//删除指定记录
		$model = D ("MailAddressList");
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if (false !== $model->where ( $condition )->delete ()) {
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
	
	
	//邮件列表
	public function mailList()
	{
		//列表过滤器，生成查询Map对象
		if($_SESSION['all_city'])
		$sql = "select * from ".C("DB_PREFIX")."mail_list";
		else
		$sql = "select m.* from ".C("DB_PREFIX")."mail_list as m left join ".C("DB_PREFIX")."goods as g on m.goods_id = g.id where m.goods_id = 0 or g.city_id in (".implode(",",$_SESSION['admin_city_ids']).")";

		$model = D ("MailList");
		if (! empty ( $model )) {
			$this->_Sql_list ( $model, $sql );
		}
		$this->display();
	}
	public function addMail()
	{
		$this->assign("goods_list",D("Goods")->where(array("city_id"=>array("in",$_SESSION['admin_city_ids'])))->order("sort asc")->findAll());
		$this->assign('now',toDate(gmtTime(),"Y-m-d H:i:s"));
		$this->display();
	}
	
	public function insertMail()
	{
		//B('FilterString');
		$model = D ("MailList");
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$list=$model->add ();
		if ($list!==false) { //保存成功
			$this->saveLog(1,$list);
//			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('ADD_SUCCESS'));
		} else {
			//失败提示
			$this->saveLog(0,$list);
			$this->error (L('ADD_FAILED'));
		}
	}
	public function editMail()
	{
		$model = M ( "MailList" );
		$this->assign("goods_list",D("Goods")->where(array("city_id"=>array("in",$_SESSION['admin_city_ids'])))->order("sort asc")->findAll());
		$id = $_REQUEST [$model->getPk ()];
		$vo = $model->getById ( $id );
		$this->assign ( 'vo', $vo );
		$this->display ();
	}
	public function updateMail() {
		//B('FilterString');
		$model = D ( "MailList" );
		$data = $model->create ();
		if (false === $data) {
			$this->error ( $model->getError () );
		}
		// 更新数据

		$list=$model->save ($data);

		if (false !== $list) {
			//成功提示
			$this->saveLog(1);
			//pushMail();
//			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('EDIT_SUCCESS'));
		} else {
			//错误提示
			$this->saveLog(0);
			$this->error (L('EDIT_FAILED'));
		}
	}
	public function foreverdeleteMail() {
		//删除指定记录
		$model = D ("MailList");
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if (false !== $model->where ( $condition )->delete ()) {
					//echo $model->getlastsql();
					D("MailAddressSendList")->where("mail_id=".$id)->delete();
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
	
	
	//邮件的发送地址列表
	public function sendAddressList()
	{
		$this->assign("city_list",M("GroupCity")->findAll());
		$map['status'] = 1;
		if(intval($_REQUEST['city_id'])!=0)
		$map['city_id'] = intval($_REQUEST['city_id']);
		if(isset($_REQUEST['mail_address'])&&$_REQUEST['mail_address']!='')
		$map['mail_address'] = $_REQUEST['mail_address'];
		$model = D ("MailAddressList");
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$list = $this->get("list");
		$id = intval($_REQUEST['id']);
		$mail_send_address_list = D("MailAddressSendList")->where("mail_id=".$id)->findAll();
		
		foreach($list as $k=>$v)
		{
			foreach($mail_send_address_list as $kk=>$vv)
			{
				if($v['id']==$vv['mail_address_id'])
				{
					$list[$k]['checked'] = true;
					$list[$k]['is_push'] = $mail_send_address_list[$kk]['is_push'];
					break;
				}
				
			}
			
		}
		$this->assign('mail_id',$id);

		$this->assign("list",$list);
		$this->display();
	}
	
	public function saveSendAddress()
	{
		$mail_id = intval($_REQUEST['mail_id']);
		$hiddenids = $_REQUEST['hiddenid'];
		if(D("MailList")->where("id=".$mail_id)->count()==0)
		{
			$this->error (L('MAIL_NOT_EXIST'));
		}
		else 
		{
			D("MailAddressSendList")->where(array("mail_address_id"=>array("in",$hiddenids),"mail_id"=>$mail_id))->delete();
			$mail_address_ids = array();
			if($_REQUEST['check_all'])
			{
				D("MailAddressSendList")->where(array("mail_id"=>$mail_id))->delete();
				if(intval($_REQUEST['city_id'])==0)
				$mail_address_list = D ("MailAddressList")->where("status=1")->findAll();
				else
				$mail_address_list = D ("MailAddressList")->where("status=1 and city_id=".intval($_REQUEST['city_id']))->findAll();
				foreach($mail_address_list as $mail_address_item)
				{
					array_push($mail_address_ids,$mail_address_item['id']);
				}
				
				foreach($mail_address_ids as $mail_address_id)
				{
						$data['mail_id'] = $mail_id;
						$data['mail_address_id'] = $mail_address_id;
						
						D("MailAddressSendList")->add($data);
				}
			}
			else
			{
				D("MailAddressSendList")->where(array("mail_address_id"=>array("in",$hiddenids),"mail_id"=>$mail_id))->delete();
				$mail_address_ids = $_POST['key'];
			
				foreach($hiddenids as $mail_address_id)
				{
					
					if(in_array($mail_address_id,$mail_address_ids))
					{
						$data['mail_id'] = $mail_id;
						$data['mail_address_id'] = $mail_address_id;
						
						D("MailAddressSendList")->add($data);
					}
					else
					{
						D("MailAddressSendList")->where("mail_address_id=".$mail_address_id)->delete();
					}
				}
			}
			pushMail();
			$this->success(L("EDIT_SUCCESS"));
		}
	}
	

	
	public function tgemail()
	{
		$arr =  Dir::getList($this->getRealPath()."/Public/mail_template/");
		foreach($arr as $item)
		{
			if($item!='..'&&$item!='.')
			{
				$tmpl['id'] = $item;
				$tmpl['name'] = $item;				
				$template[] = $tmpl;
			}
		}
		$this->assign("list",$template);
		$this->display();
	}
	
	public function edit()
	{
		$name = $_REQUEST['id'];
		$this->assign("name",$name);
		$file = $this->getRealPath()."/Public/mail_template/".$name."/".$name.".html";
		//开始获取模板内容
		$content = file_get_contents($file);
		$this->assign("content",$content);
		$this->display();
	}
	
	public function updateTmpl()
	{
		$name = $_REQUEST['name'];
		if(MAGIC_QUOTES_GPC)
		{
			$content = stripslashes($_REQUEST['content']);
		}
		else
		{
			$content = $_REQUEST['content'];
		}
		$file = $this->getRealPath()."/Public/mail_template/".$name."/".$name.".html";
		@file_put_contents($file,$content);
		$this->success("更新成功");
	}
	
	public function useTmpl()
	{
		$name = $_REQUEST['name'];
		M("SysConf")->where("name='GROUP_MAIL_TMPL'")->setField("val",$name);
		$this->success("切换成功",1);
	}
	//导出邮件列表
	function expmail($page=1){
		set_time_limit(0);
		$limit = (($page - 1)*500).",".(500);
		$list = M("MailAddressList")->order("id asc")->limit($limit)->findAll();
		if($list)
		{
			register_shutdown_function(array(&$this, 'expmail'), $page+1);
			foreach($list as $k=>$v)
			{
				$list[$k]['mail_address'] = $v['mail_address'];
				$list[$k]['status'] = $v['status']==1?'启用':'禁用';
				$list[$k]['user_name'] = M("User")->where("id=".$v['user_id'])->getField('user_name');
				$list[$k]['city'] = M("GroupCity")->where("id=".$v['city_id'])->getField('name');
			}
			//dump($list);exit;
			//dump($sql);
	    	/* csv文件数组 */
			
	    	$mail_value = array('mail_address'=>'""', 'status'=>'""', 'user_name'=>'""', 'city'=>'""');
	    	$content = utf8ToGB("邮箱地址,状态,用户名,订阅城市");
	    	
	    	
	    	$content = $content . "\n";
	    	
			foreach($list as $k=>$v)
			{
				
				$mail_value['mail_address'] = utf8ToGB('"' . $v['mail_address'] . '"');
				$mail_value['status'] = utf8ToGB('"' . $v['status'] . '"');
				$mail_value['user_name'] = utf8ToGB('"' . $v['user_name'] . '"');
				$mail_value['city'] = utf8ToGB('"' . $v['city'] . '"');
				
				
				
				$content .= implode(",", $mail_value) . "\n";
			}	
			
			//dump($content);exit;
	    	header("Content-Disposition: attachment; filename=mail_list.csv");
	    	//header("Content-Type: application/octet-stream");
	    	//die();
	    	echo $content; 
		}  
	}
	public function sendDemo()
	{
		$smtp_item = $_REQUEST;
		$smtp_item['id'] = 0;
		$mail = new Mail($smtp_item);		
		$mail->AddAddress($smtp_item['mail_address']);
		$mail->IsHTML(1); 
		$mail->Subject = '测试邮件'; // 标题
		$mail->Body = '这是一封测试邮件'; // 内容
		$mail->Send();
		if($mail->ErrorInfo!='')
		{
				echo $mail->ErrorInfo;
		}
		else
		{
			echo '发送测试邮件成功';
		}
	}
	public function showMailDemo()
	{
		$goods_id = intval($_REQUEST['id']);
		$goods_info = M("Goods")->getById($goods_id);
		if($goods_info)
		{
			//开始编译邮件内容
			$time = gmtTime();
  			$tpl = Think::instance('ThinkTemplate');
								$mail_tpl = file_get_contents(getcwd()."/Public/mail_template/".eyooC("GROUP_MAIL_TMPL")."/".eyooC("GROUP_MAIL_TMPL").".html");  //邮件群发的模板				
								$mail_tpl = str_replace(eyooC("GROUP_MAIL_TMPL")."_files/",eyooC("SHOP_URL")."/Public/mail_template/".eyooC("GROUP_MAIL_TMPL")."/".eyooC("GROUP_MAIL_TMPL")."_files/",$mail_tpl);
			
								//开始定义模板变量
								$v = $goods_info;
								
								//$city_name
								$city_name = M("GroupCity")->where("id=".$v['city_id'])->getField("name");
								
								//$shop_name
								$shop_name = SHOP_NAME;
								
								//$cancel_url
								$cancel_url = eyooC("SHOP_URL")."/index.php?m=Index&a=unSubScribe&email=".$address_item['mail_address'];
								
								//$sender_email
								$sender_email = eyooC("REPLY_ADDRESS");
								
								//$send_date 
								$send_date = toDate(gmtTime(),'Y年m月d日');
								$weekarray = array("日","一","二","三","四","五","六");
								$send_date .= " 星期".$weekarray[toDate(gmtTime(),"w")];
								
								
								//$shop_url
								$shop_url = eyooC("SHOP_URL");
								
								//$tel_number
								$tel_number = eyooC("TEL");
								
								//$tg_info
								$tg_info = D("Goods")->getGoodsItem($v['id'],$v['city_id']);
								$tg_info['title'] = $tg_info['name_1'];
								$tg_info['price'] = $tg_info['shop_price_format'];
								$tg_info['origin_price'] = $tg_info['market_price_format'];
								$tg_info['discount'] = $tg_info['discountfb'];
								$tg_info['save_money'] = $tg_info['save'];
								$tg_info['big_img'] = eyooC("SHOP_URL").$tg_info['big_img'];
								$tg_info['desc'] = str_replace("./Public/",eyooC("SHOP_URL")."/Public/",$tg_info['goods_desc_1']);
								
								//$sale_info
								$sale_info['title'] = $tg_info['suppliers']['name'];
								$sale_info['url'] = $tg_info['suppliers']['web'];
								$sale_info['tel_num'] = $tg_info['suppliers']['tel'];
								$sale_info['map_url'] = $tg_info['suppliers']['map'];
								
								//$referral
								$referral['amount'] = eyooC("REFERRALS_MONEY");
								
								if(eyooC("REFERRAL_TYPE") == 0)
								{
									$referral['amount'] = formatPrice($referral['amount']);
								}
								else
								{
									$referral['amount'] = $referral['amount']."".L("SCORE_UNIT");
								}
							
								if(eyooC("URL_ROUTE")==0)
								$referral['url'] = eyooC("SHOP_URL")."/index.php?m=Referrals&a=index";
								else
								$referral['url'] = eyooC("SHOP_URL")."/Referrals-index.html";
								
								
								ob_start();
								eval('?' . '>' .$tpl->parse($mail_tpl));
								$content = ob_get_clean();	
								
								$mail_content = $content;
								
								$this->ajaxReturn($mail_content,'',true);
			//编译结束
		}
		else
		{
			$this->ajaxReturn('','不存在的商品编号',false);
		}
	}

}
?>