<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="家居,装潢,室内设计,360得利网" /> 
<meta name="description" content="家居,装潢,室内设计,360得利网" /> 
<link href="__PUBLIC__/css/global.css" rel="stylesheet" type="text/css" />
<link href="__PUBLIC__/css/anli.css" rel="stylesheet" type="text/css" />
<title><?php echo ($vo["name_1"]); ?>-热点促销-得利家居</title>
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
</style>
<script>
function addAttention(id){
	$.post('__APP__/Tool/addAttention',{id:id,model:'news','rand':Math.random()},function(aa){
		if(aa=='nologin'){
			alert("请登录，再收藏!");
			window.location.href = '__APP__/Public/login';
			
		}else if(aa=='failed'){
			
			alert("收藏失败!");
		}else if(aa=='ok'){
			
			alert("收藏成功!");
		}else{
			alert("已收藏,不需要重复收藏!");
		}
	});
}

</script>
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
	<div class="al_lf">
		<h2>热点促销</h2>
		<div class="al_lfcont">
		<div><img src="__PUBLIC__/images/zs_tit.jpg" /></div>	
			<div class="al_lf_c" >
				<div class="hdms_c" style="min-height:658px;">
                <h3 style="text-align:center; line-height:35px; border-bottom:#6b1b04 solid 1px;"><?php echo ($vo["name_1"]); ?></h3>
                <div style="margin:-20px 0 20px 80px;">　<!-- Baidu Button BEGIN -->
                <div class="clear"></div>
   <div style="float:right; width:240px; text-align:right;">
    <div id="bdshare" class="bdshare_t bds_tools get-codes-bdshare" >
        <span class="bds_more" style="line-height:16px;">分享到：</span>
        <a class="bds_qzone"></a>
        <a class="bds_tsina"></a>
        <a class="bds_tqq"></a>
        <a class="bds_renren"></a>
		<a class="shareCount"></a>
    </div>
<script type="text/javascript" id="bdshare_js" data="type=tools" ></script>
<script type="text/javascript" id="bdshell_js"></script>
<script type="text/javascript">
	document.getElementById("bdshell_js").src = "http://bdimg.share.baidu.com/static/js/shell_v2.js?cdnversion=" + new Date().getHours();
</script>
<!-- Baidu Button END --> </div>
<div style="float:left; width:340px; text-align:right;">发布时间：<?php echo (toDate($vo["create_time"],"Y-m-d")); ?>　阅读数：<?php echo ($vo["click_count"]); ?>　【<a href="javascript:void(0);" onclick="addAttention(<?php echo ($vo["id"]); ?>);">收藏</a>】</div>
        </div>
                <div class="clear"></div>
        
<div class="clear"></div>
                <div> <?php echo ($vo["content_1"]); ?></div>
                <div class="clear"></div>
                
                </div>
			</div>
			<div><img src="__PUBLIC__/images/zs_bot.jpg" /></div>	
			
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
				<?php if(is_array($toplist)): $art = 0; $__LIST__ = $toplist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$article): ++$art;$mod = ($art % 2 )?><p <?php if(($art)  ==  "6"): ?>style="border-bottom:none;"<?php endif; ?>><a href="__APP__/News/<?php echo ($article["id"]); ?>.shtml"><?php echo (utf_substr($article["name_1"],28)); ?></a></p><?php endforeach; endif; else: echo "" ;endif; ?>
				
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