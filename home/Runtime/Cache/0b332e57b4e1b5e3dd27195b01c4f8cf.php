<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="家居,装潢,室内设计,360得利网" /> 
<meta name="description" content="家居,装潢,室内设计,360得利网" /> 
<link href="__PUBLIC__/css/global.css" rel="stylesheet" type="text/css" />
<link href="__PUBLIC__/css/anli.css" rel="stylesheet" type="text/css" />
<link href="__PUBLIC__/css/info.css" rel="stylesheet" type="text/css" />
<link href="__PUBLIC__/ss/Booking.css" rel="stylesheet" type="text/css" />
<title><?php echo ($title); ?></title>
<script src="__PUBLIC__/js/jquery-1.7.1.min.js"></script> 
<SCRIPT language=javascript>
var APP	 =	 '__APP__';
var PUBLIC	 =	 '__PUBLIC__';
</SCRIPT>
<SCRIPT src="__PUBLIC__/js/reg.js" type=text/javascript></SCRIPT>
<!--[if IE 6]> 
<script src="__PUBLIC__/js/DD_belatedPNG.js"></script> 
<script> 
DD_belatedPNG.fix('.top_k,.top_menu li a,.top_menu,.kuang_m,.date_pre,.date_next,.kuang_f,.link_main,img'); 
</script> 
<![endif]--> 
<script src="__PUBLIC__/js/slider.js"></script> 

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
	
	<div class="info_rt1">
		
		<div class="info_mtit"><a href="javascript:void(0);">预约报名</a></div>      
		<div class="info_main"><form action="__APP__/Booking/BookInset" method="post" id="regForm12">
		<div class="bm_left">
        <div class="baoming_type"><img src="__PUBLIC__/images/bm01.gif" /><p><input name="type" type="radio" value="1" <?php if(($type)  ==  "1"): ?>checked="checked"<?php endif; ?> />&nbsp;免费量房</p></div>
            <div class="baoming_type"><img src="__PUBLIC__/images/bm03.gif" /><p><input name="type" type="radio" value="2" <?php if(($type)  ==  "2"): ?>checked="checked"<?php endif; ?> />&nbsp;免费预算</p></div>
            <div class="baoming_type"><img src="__PUBLIC__/images/bm02.gif" /><p><input name="type" type="radio" value="3" <?php if(($type)  ==  "3"): ?>checked="checked"<?php endif; ?> />&nbsp;免费设计</p></div>
            <div class="baoming_type"><img src="__PUBLIC__/images/bm04.gif" /><p><input name="type" type="radio" value="4" <?php if(($type)  ==  "4"): ?>checked="checked"<?php endif; ?> />&nbsp;免费咨询</p></div>
			<table cellpadding="0" cellspacing="0" border="0" class="info_bd">
				<tr>
					<td width="150" align="right">手机号码：</td>
					<td width="400"><input type="text" class="bg_txt" name="mobile"  id="mobile" /><span id="mobile_tips"></span></td>
				</tr>
                <tr>
					<td width="150" align="right">设置密码：</td>
					<td width="400"><input type="password" class="bg_txt"  name="user_pwd" id="password" /><span id="password_tips"></span></td>
				</tr>
                <tr>
					<td width="150" align="right">确认密码：</td>
					<td width="400"><input type="password" class="bg_txt"  name="repassword" id="repassword" /><span id="repassword_tips"></span></td>
				</tr>
				<tr>
					<td width="150" align="right">您的称呼：</td>
					<td width="400"><input type="text" class="bg_txt"  name="user_name" id="user_name" value="<?php echo ($user["user_name"]); ?>"/><span id="user_name_tips"></span></td>
				</tr>
                <tr>
					<td width="150" align="right">email地址：</td>
					<td width="400"><input type="text" class="bg_txt"  name="email" id="email" /><span id="email_tips"></span></td>
				</tr>
				<tr>
					<td width="150" align="right"></td>
					<td width="400"> <input type="image" src="__PUBLIC__/images/bman.jpg" /></td>
				</tr>
			</table>
            </div>
            </form>
            <div class="bm_right">
            <div class="rt_c">
			<div class="web_bm1">
				<div class="wb_tit"><img src="__PUBLIC__/images/tip1.jpg" /></div>
				<div class="wb_tt"><span>最新报名<em><?php echo ($bookcout); ?></em>人</span><span>30天报名<em><?php echo ($bookcout); ?></em>人</span></div>
				<div class="wb_cont" id="wb_conts">
                	<ul class="wb_list" >
                    	<?php if(is_array($book)): $i = 0; $__LIST__ = $book;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><li  class="bm_content">
                        	<span style="width:130px; float:right;"><?php echo (StringsubMobile($vo["mobile"])); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo (toDate($vo["create_time"],"m/d")); ?></span><span style=""><?php echo (Stringsubname($vo["user_name"])); ?></span>
                        </li><?php endforeach; endif; else: echo "" ;endif; ?>
                    </ul>
					
				</div>
				
			</div>
		</div>
            </div>
            <div class="clear"></div>
		</div>
	</div>
</div>
<div class="clear10"></div>
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
<script>
 $(document).ready(function() {
            $(".sq_model").mouseenter(
                function(){
                    $(this).addClass("sq_mod");
            });

            $(".sq_model").mouseleave(
                function(){
                    $(this).removeClass("sq_mod");
            });
})
</script>
</body>
</html>