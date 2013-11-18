<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="家居,装潢,室内设计,360得利网" /> 
<meta name="description" content="家居,装潢,室内设计,360得利网" /> 
<link href="__PUBLIC__/css/global.css" rel="stylesheet" type="text/css" />
<link href="__PUBLIC__/css/anli.css" rel="stylesheet" type="text/css" />
<title>家居</title>
<script src="__PUBLIC__/js/jquery-1.3.2.min.js"></script> 
  <script type="text/javascript" src="__PUBLIC__/js/hd.js"></script>
<!--[if IE 6]> 
<script src="__PUBLIC__/js/DD_belatedPNG.js"></script> 
<script> 
DD_belatedPNG.fix('.top_k,.top_menu li a,.top_menu,.kuang_m,.date_pre,.date_next,.kuang_f,.link_main,img'); 
</script> 
<![endif]--> 
<style>
.caselist{
	padding: 20px 32px;
}
.qyzl_c p a.selected{ background:#841907; color:#FFF;}
</style>
</head>
<body>
 <SCRIPT src="__PUBLIC__/js/superfish.js" type=text/javascript></SCRIPT>
<script type="text/javascript" src="__PUBLIC__/js/jquery.bgiframe.min.js"></script> 
<div class="header">
<form method="post" name="frm" id="frm" action="__APP__/Index/dosearch" >
<div class="hd_top">
	<div class="logo"><a href="__APP__/"><img src="__PUBLIC__/images/logo.png" /></a></div>
	<div class="search">
		<div id="tm2008style" style="width:104px; float:left; overflow:hidden;">
		<select name="type" id="fontbox" style="height:28px; width:100px;">
			<option value="1">搜索案例</option>
			<option value="2" >搜索社区</option>
			<option value="3" >搜索公司</option>
			<option value="4" >搜索资讯</option>	
		</select>
	</div>
	<DIV class=searchBar>            
		<DIV class=text>
			<INPUT class=s_t  onblur="if(this.value==''){this.value='请输入关键字';this.style.color='#aaa'}"  onfocus="if(this.value=='请输入关键字'){this.value='';this.style.color='#666'}" value="请输入关键字" name="keyword">
		</DIV>
		<div style="float:right;"><input type="image" src="__PUBLIC__/images/btn.gif" /></div>
	</DIV>
</div>
</div>
</form>   
</div>
<DIV id=toplink>
	<UL class=sf-menu>
	  <LI><A href="__APP__/" >首页</A> </LI>
	  <LI><A href="__APP__/Case" >认证考试</A></LI>
	  <LI><A href="__APP__/Company/showlist" >意思管理</A></LI>
	  <LI><A href="__APP__/Shequ" >学历研修</A></LI>
	  <LI><A href="__APP__/News">行业定制</A></LI>
	  <LI><A href="__APP__/Booking" >活动专区</A> </LI>
	  <LI><A href="__APP__/Booking" >招聘专区</A> </LI>
	</UL>
</DIV>
<div class="container">
	<div class="con_lf">
		<div class="al_lf" style="height:785px;">
			<h2>装修公司</h2>
			<div class="al_lfcont caselist" style="height:700px;padding: 10px 32px;">   
            <div class="seach_show" style="margin-left:0; margin-bottom:15px;"><?php if(($searchshow)  ==  "1"): ?>下面是您搜索的内容：<span><?php echo ($keyword); ?></span><?php endif; ?> <?php if(($searchshow)  ==  "2"): ?>没有找<span><?php echo ($keyword); ?></span>相关的内容<?php endif; ?></div>
            	<div class="qyzl_c" style="border-top: 1px solid #DACCC0; background:#FFF;">
						<p>服务区域：<a href="__APP__/Company/showlist/service_area/1" <?php if(($_SESSION['service_area'])  ==  ""): ?>class="selected"<?php endif; ?>>不限</a> <a href="__APP__/Company/showlist/service_area/长宁" <?php if(($_SESSION['service_area'])  ==  "长宁"): ?>class="selected"<?php endif; ?>>长宁</a> <a href="__APP__/Company/showlist/service_area/闸北" <?php if(($_SESSION['service_area'])  ==  "闸北"): ?>class="selected"<?php endif; ?>>闸北</a> <a href="__APP__/Company/showlist/service_area/徐汇" <?php if(($_SESSION['service_area'])  ==  "徐汇"): ?>class="selected"<?php endif; ?>>徐汇</a> <a href="__APP__/Company/showlist/service_area/静安" <?php if(($_SESSION['service_area'])  ==  "静安"): ?>class="selected"<?php endif; ?>>静安</a> 	<a href="__APP__/Company/showlist/service_area/黄浦" <?php if(($_SESSION['service_area'])  ==  "黄浦"): ?>class="selected"<?php endif; ?>>黄浦</a> <a href="__APP__/Company/showlist/service_area/杨浦" <?php if(($_SESSION['service_area'])  ==  "杨浦"): ?>class="selected"<?php endif; ?>>杨浦</a> <a href="__APP__/Company/showlist/service_area/普陀" <?php if(($_SESSION['service_area'])  ==  "普陀"): ?>class="selected"<?php endif; ?>>普陀</a> <a href="__APP__/Company/showlist/service_area/虹口" <?php if(($_SESSION['service_area'])  ==  "虹口"): ?>class="selected"<?php endif; ?>>虹口</a> <a href="__APP__/Company/showlist/service_area/浦东" <?php if(($_SESSION['service_area'])  ==  "浦东"): ?>class="selected"<?php endif; ?>>浦东</a>  <a href="__APP__/Company/showlist/service_area/松江" <?php if(($_SESSION['service_area'])  ==  "松江"): ?>class="selected"<?php endif; ?>>松江</a> <a href="__APP__/Company/showlist/service_area/嘉定" <?php if(($_SESSION['service_area'])  ==  "嘉定"): ?>class="selected"<?php endif; ?>>嘉定</a> <a href="__APP__/Company/showlist/service_area/宝山" <?php if(($_SESSION['service_area'])  ==  "宝山"): ?>class="selected"<?php endif; ?>>宝山</a> <a href="__APP__/Company/showlist/service_area/青浦" <?php if(($_SESSION['service_area'])  ==  "青浦"): ?>class="selected"<?php endif; ?>>青浦</a> <a href="__APP__/Company/showlist/service_area/金山" <?php if(($_SESSION['service_area'])  ==  "金山"): ?>class="selected"<?php endif; ?>>金山</a> <a href="__APP__/Company/showlist/service_area/奉贤" <?php if(($_SESSION['service_area'])  ==  "奉贤"): ?>class="selected"<?php endif; ?>>奉贤</a><a href="__APP__/Company/showlist/service_area/崇明" <?php if(($_SESSION['service_area'])  ==  "崇明"): ?>class="selected"<?php endif; ?>>崇明</a></p>
						<p>主打价位：<a href="__APP__/Company/showlist/main_price/10" <?php if(($_SESSION['main_price'])  ==  ""): ?>class="selected"<?php endif; ?>>不限</a> <a href="__APP__/Company/showlist/main_price/1" <?php if(($_SESSION['main_price'])  ==  "1"): ?>class="selected"<?php endif; ?>>8万以下</a> <a href="__APP__/Company/showlist/main_price/2" <?php if(($_SESSION['main_price'])  ==  "2"): ?>class="selected"<?php endif; ?>>8-15万</a> <a href="__APP__/Company/showlist/main_price/3" <?php if(($_SESSION['main_price'])  ==  "3"): ?>class="selected"<?php endif; ?>>15-30万</a> <a href="__APP__/Company/showlist/main_price/4" <?php if(($_SESSION['main_price'])  ==  "4"): ?>class="selected"<?php endif; ?>>30-100万</a> <a href="__APP__/Company/showlist/main_price/5" <?php if(($_SESSION['main_price'])  ==  "5"): ?>class="selected"<?php endif; ?>>100万以上</a> </p>
                        <p>擅长风格：<a href="__APP__/Company/showlist/good_style/3" <?php if(($_SESSION['good_style'])  ==  ""): ?>class="selected"<?php endif; ?>>不限</a> <a href="__APP__/Company/showlist/good_style/现代简约" <?php if(($_SESSION['good_style'])  ==  "现代简约"): ?>class="selected"<?php endif; ?>> 现代简约</a> <a href="__APP__/Company/showlist/good_style/田园" <?php if(($_SESSION['good_style'])  ==  "田园"): ?>class="selected"<?php endif; ?>> 田园</a> <a href="__APP__/Company/showlist/good_style/欧美式" <?php if(($_SESSION['good_style'])  ==  "欧美式"): ?>class="selected"<?php endif; ?>>  欧美式</a> <a href="__APP__/Company/showlist/good_style/中式风格" <?php if(($_SESSION['good_style'])  ==  "中式风格"): ?>class="selected"<?php endif; ?>>  中式风格</a> <a href="__APP__/Company/showlist/good_style/地中海" <?php if(($_SESSION['good_style'])  ==  "地中海"): ?>class="selected"<?php endif; ?>>  地中海</a> <a href="__APP__/Company/showlist/good_style/混搭" <?php if(($_SESSION['good_style'])  ==  "混搭"): ?>class="selected"<?php endif; ?>>混搭</a></p>
                        <p>业务范围：<a href="__APP__/Company/showlist/business_scope/4"  <?php if(($_SESSION['business_scope'])  ==  ""): ?>class="selected"<?php endif; ?>>不限</a> <a href="__APP__/Company/showlist/business_scope/新房装修" <?php if(($_SESSION['business_scope'])  ==  "新房装修"): ?>class="selected"<?php endif; ?>>新房装修</a> <a href="__APP__/Company/showlist/business_scope/商铺装修" <?php if(($_SESSION['business_scope'])  ==  "商铺装修"): ?>class="selected"<?php endif; ?>>商铺装修</a> <a href="__APP__/Company/showlist/business_scope/工装" <?php if(($_SESSION['business_scope'])  ==  "工装"): ?>class="selected"<?php endif; ?>>工装</a></p>
						<p>装修模式：<a href="__APP__/Company/showlist/decoration_pattern/5" <?php if(($_SESSION['decoration_pattern'])  ==  ""): ?>class="selected"<?php endif; ?>>不限</a> <a href="__APP__/Company/showlist/decoration_pattern/半包" <?php if(($_SESSION['decoration_pattern'])  ==  "半包"): ?>class="selected"<?php endif; ?>>半包</a> <a href="__APP__/Company/showlist/decoration_pattern/全包" <?php if(($_SESSION['decoration_pattern'])  ==  "全包"): ?>class="selected"<?php endif; ?>>全包</a> <a href="__APP__/Company/showlist/decoration_pattern/装修监理" <?php if(($_SESSION['decoration_pattern'])  ==  "装修监理"): ?>class="selected"<?php endif; ?>> 装修监理</a></p>
						
					</div>   
                    <div class="clear"></div> 
                   
						<?php if(is_array($top)): $i = 0; $__LIST__ = $top;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): ++$i;$mod = ($i % 2 )?><div class="sub_a_mod">
						<div class="sub_a_pic"><a href="__APP__/BusinessChannel/show/id/<?php echo ($sub["uid"]); ?>.shtml"><img src="__PUBLIC__/upload/company/<?php echo (getComImg($sub["com_id"])); ?>" /></a></div>
						<p><a href="__APP__/BusinessChannel/show/id/<?php echo ($sub["uid"]); ?>.shtml"><?php echo ($sub["company_name"]); ?></a></p>
						<p>主打价位：<?php echo (getPriceT($sub["main_price"])); ?></p>
						<p>设计风格：<?php echo ($sub["good_style"]); ?></p>
					</div><?php endforeach; endif; else: echo "" ;endif; ?>
                   
                    <div class="clear"></div>
				<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): ++$i;$mod = ($i % 2 )?><div class="sub_a_mod" style="margin-top:20px;">
						<div class="sub_a_pic"><a href="__APP__/BusinessChannel/show/id/<?php echo ($sub["uid"]); ?>.shtml"><img src="__PUBLIC__/upload/company/<?php echo (getComImg($sub["com_id"])); ?>" /></a></div>
						<p><a href="__APP__/BusinessChannel/show/id/<?php echo ($sub["uid"]); ?>.shtml"><?php echo ($sub["company_name"]); ?></a></p>
						<p>主打价位：<?php echo (getPriceT($sub["main_price"])); ?></p>
						<p>设计风格：<?php echo (utf_substr($sub["good_style"],12)); ?></p>
					</div><?php endforeach; endif; else: echo "" ;endif; ?>
                    <div class="clear"></div>
             		<div class="al_prt"><span><?php echo ($page); ?></span></div>
                <div class="clear"></div>
			</div>
		</div>
	</div>
	<div class="con_rt">
		<div class="login">
			<h1>用户中心</h1>
             <?php if(($_SESSION['uid'] == '') or($_SESSION['uid'] == null) ): ?><div class="log_cont">
                <form name="frm1" method="post" action="__APP__/Public/checkLogins">
				<?php if(($_COOKIE['remarkpass'])  ==  "1"): ?><table cellpadding="0" border="0" cellspacing="0" class="tab_01">
					<tr>
						<td align="right" width="24%">用户名</td>
						<td width="76%"><input type="text" name="user_name" class="txt_02" value="<?php echo ($_COOKIE['user_name']); ?>"  /></td>
					</tr>
					<tr>
						<td align="right" width="24%">密&nbsp;&nbsp;码</td>
						<td width="76%"><input type="password" class="txt_02" name="user_pwd" value="<?php echo ($_COOKIE['password']); ?>"  /></td>
					</tr>
					<tr>
						<td align="right" width="24%">&nbsp;</td>
						<td width="76%"><input type="checkbox" name="remarkpass" value="1" class="clk_check"/>&nbsp;&nbsp;<u class="u_clk">记住登录状态</u>&nbsp;&nbsp; <a href="__APP__/Public/forgetpw">忘记密码</a></td>
					</tr>
					<tr>
						<td align="right" width="24%" class="log">&nbsp;</td>
						<td width="76%" class="log"><input type="submit" class="log_btn11" value="登 录" /></td>
					</tr>
				</table>
                <?php else: ?>
                       <table cellpadding="0" border="0" cellspacing="0" class="tab_01">
					<tr>
						<td align="right" width="24%">用户名</td>
						<td width="76%"><input type="text" name="user_name" class="txt_02" value="请输入手机号码" onclick="if(this.value=='请输入手机号码')this.value='';" onblur="if(this.value=='')this.value='请输入手机号码';" /></td>
					</tr>
					<tr>
						<td align="right" width="24%">密&nbsp;&nbsp;码</td>
						<td width="76%"><input type="password" class="txt_02" name="user_pwd" /></td>
					</tr>
					<tr>
						<td align="right" width="24%">&nbsp;</td>
						<td width="76%"><input type="checkbox" name="remarkpass" value="1" class="clk_check"/>&nbsp;&nbsp;<u class="u_clk">记住登录状态</u>&nbsp;&nbsp; <a href="__APP__/Public/forgetpw">忘记密码</a></td>
					</tr>
					<tr>
						<td align="right" width="24%" class="log">&nbsp;</td>
						<td width="76%" class="log"><input type="submit" class="log_btn11" value="登 录" /></td>
					</tr>
				</table><?php endif; ?>
                </form>
			</div>
			<p>还没有账号？<a href="__APP__/Public/reg">立即注册</a></p>
            <?php else: ?>
            <div class="log_cont">
            <p style=" color:#514034; font-size:14px;"><script language="javaScript"> 
now = new Date(),hour = now.getHours() 
if(hour < 6){document.write("凌晨好！")} 
else if (hour < 9){document.write("早上好！")} 
else if (hour < 12){document.write("上午好！")} 
else if (hour < 14){document.write("中午好！")} 
else if (hour < 17){document.write("下午好！")} 
else if (hour < 19){document.write("傍晚好！")} 
else if (hour < 22){document.write("晚上好！")} 
else {document.write("深夜好！")} 

</script>

</p>
            <p style="font-size:14px;">欢迎您光临得利网站</p>
            <p style="font-size:14px;">您好！<span style="color:#514034; font-weight:bold;"><?php echo ($_SESSION['user_name']); ?></span></p>
            </div>
            <p><?php if(($_SESSION['type'])  ==  "1"): ?><a href="__APP__/Member/index.shtml"><?php else: ?><a href="__APP__/Company/management.shtml"><?php endif; ?>我的得利</a> 　　<a href="__APP__/Public/logout">退出</a></p><?php endif; ?>
		</div>
		<div class="rt_c" style="margin-bottom:0;">
			<h1>促销活动</h1>
			<div class="sub_sq">
				<?php if(is_array($newslist)): $art = 0; $__LIST__ = $newslist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$article): ++$art;$mod = ($art % 2 )?><p <?php if(($art)  ==  "6"): ?>style="border-bottom:none;"<?php endif; ?>><a href="__APP__/News/<?php echo ($article["id"]); ?>.shtml"><?php echo (utf_substr($article["name_1"],28)); ?></a></p><?php endforeach; endif; else: echo "" ;endif; ?>
				
			</div>
		</div>
	</div>
	<div class="clear11"></div>
</div>

 <div class="footer">
	<div class="foot">
		<div class="ftmodel">
			<div><img src="__PUBLIC__/images/tip4.png" /></div>
			<p><em>购物指南</em></p>
			<p><a href="">怎样购物</a></p>
			<p><a href="">会员俱乐部</a></p>
			<p><a href="">积分知道</a></p>
			<p><a href="">优惠券使用</a></p>
			<p><a href="">订单状态说明</a></p>
		</div>
		<div class="ftmodel">
			<div><img src="__PUBLIC__/images/tip5.png" /></div>
			<p><em>购物指南</em></p>
			<p><a href="">怎样购物</a></p>
			<p><a href="">会员俱乐部</a></p>
			<p><a href="">积分知道</a></p>
			<p><a href="">优惠券使用</a></p>
			<p><a href="">订单状态说明</a></p>
		</div>
		<div class="ftmodel">
			<div><img src="__PUBLIC__/images/tip6.png" /></div>
			<p><em>购物指南</em></p>
			<p><a href="">怎样购物</a></p>
			<p><a href="">会员俱乐部</a></p>
			<p><a href="">积分知道</a></p>
			<p><a href="">优惠券使用</a></p>
			<p><a href="">订单状态说明</a></p>
		</div>
		<div class="ftmodel">
			<div><img src="__PUBLIC__/images/tip7.png" /></div>
			<p><em>购物指南</em></p>
			<p><a href="">怎样购物</a></p>
			<p><a href="">会员俱乐部</a></p>
			<p><a href="">积分知道</a></p>
			<p><a href="">优惠券使用</a></p>
			<p><a href="">订单状态说明</a></p>
		</div>
		<div class="ftmodel">
			<div><img src="__PUBLIC__/images/header.png" /></div>
			<p><em>购物指南</em></p>
			<p><a href="">怎样购物</a></p>
			<p><a href="">会员俱乐部</a></p>
			<p><a href="">积分知道</a></p>
			<p><a href="">优惠券使用</a></p>
			<p><a href="">订单状态说明</a></p>
		</div>
		
		<div class="copy">Copyright © 1996 - 2010 DeDeng Corporation, All Rights Reserved</div>
	</div>
</div>

</body>
</html>