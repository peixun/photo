<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="家居,装潢,室内设计,360得利网" /> 
<meta name="description" content="家居,装潢,室内设计,360得利网" /> 
<link href="__PUBLIC__/css/global.css" rel="stylesheet" type="text/css" />
<link href="__PUBLIC__/css/focus.css" rel="stylesheet" type="text/css" />
<title>家居</title>
<script src="__PUBLIC__/js/jquery-1.7.1.min.js"></script> 

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
		<div class="hdp">
        <div style="width:980px; margin:0 auto;">
        <div id="banner"> 
                    <div id="banner_bg"></div> 
                    
                    <div id="banner_info"></div> 
                    <ul> 
                    	<?php if(is_array($flashimg)): $i = 0; $__LIST__ = $flashimg;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$flashimg): ++$i;$mod = ($i % 2 )?><li><?php echo ($i); ?></li><?php endforeach; endif; else: echo "" ;endif; ?>
                    </ul> 
                    <div id="banner_list"> 
                    	<?php if(is_array($flashimgs)): $i = 0; $__LIST__ = $flashimgs;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><a href="<?php echo ($vo["url"]); ?>" >                 
                        <img src="__PUBLIC__/upload/Flashimg/m_<?php echo ($vo["image"]); ?>" /></a><?php endforeach; endif; else: echo "" ;endif; ?>
                    </div> 
          </div>      
         <script type="text/javascript">
				var t = n = 0, count; 
				$(document).ready(function(){ 
				count=$("#banner_list a").length; 
				$("#banner_list a:not(:first-child)").hide(); 
				$("#banner_info").html($("#banner_list a:first-child").find("img").attr('alt')); 
				$("#banner_info").click(function(){window.open($("#banner_list a:first-child").attr('href'), "_blank")}); 
				$("#banner li").click(function() { 
				var i = $(this).text() - 1;//获取Li元素内的值，即1，2，3，4 
				n = i; 
				if (i >= count) return; 
					$("#banner_info").html($("#banner_list a").eq(i).find("img").attr('alt')); 
					$("#banner_info").unbind().click(function(){window.open($("#banner_list a").eq(i).attr('href'), "_blank")}) 
					$("#banner_list a").filter(":visible").fadeOut(500).parent().children().eq(i).fadeIn(1000); 
					$(this).css({"background":"#631b03",'color':'#fff'}).siblings().css({"background":"#a26721",'color':'#fff'}); 
				}); 
					t = setInterval("showAuto()", 4000); 
					$("#banner").hover(function(){clearInterval(t)}, function(){t = setInterval("showAuto()", 4000);}); 
				}) 
				
				function showAuto() 
				{ 
				n = n >=(count - 1) ? 0 : ++n; 
					$("#banner li").eq(n).trigger('click'); 
				} 
			</script> 
                    </div>
        </div>
		<div class="new_c">
			<h1><a href="__APP__/News/<?php echo ($recommend_news["id"]); ?>.shtml"><?php echo (utf_substr($recommend_news["name_1"],28)); ?></a></h1>
			<div class="new_cont">
            	<?php if(is_array($news)): $i = 0; $__LIST__ = $news;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$news): ++$i;$mod = ($i % 2 )?><p>
				 	<em>2012-04-12</em> 
					<a href="__APP__/News/<?php echo ($news["id"]); ?>.shtml"><?php echo ($news["name_1"]); ?></a>
				</p><?php endforeach; endif; else: echo "" ;endif; ?>
			</div>
		</div>
		<div class="clear10"></div>
		<div class="adertline">
			<div class="adert1"><img src="__PUBLIC__/images/adert1.png" /></div>
			<div class="adert2"><img src="__PUBLIC__/images/adert2.png" /></div>
		</div>
		<div class="clear10"></div>
		<div class="adertit"><img src="__PUBLIC__/images/cert1.png" /></div>
		<div class="adpic"><img src="__PUBLIC__/images/apic.png" /></div>
		<div class="clear10"></div>
		<div class="adertit"><img src="__PUBLIC__/images/kctit.png" /></div>
		<div class="clear10"></div>
		<div class="ksclass">
			<div class="kstit">认真考试类</div>
			<div class="kstot">
				<p><a href="">CISSP</a><a href="">BS25999</a><a href="">ISO202</a></p>
				<p><a href="">CISSP</a><a href="">LA</a><a href="">IA/NA</a></p>
				<p><a href="">CISSA</a><a href="">ITIL</a></p>
				<p><a href="">COBIT</a><a href="">IOS20071</a></p>
				<p><a href="">PMP</a><a href="">IA/LA</a></p>
			</div>
		</div>
		<div class="clear10"></div>
		<div class="adertit"><img src="__PUBLIC__/images/adert3.png" /></div>
		<div class="clear10"></div>
		
		<div class="ksclass">
			<div class="kstit">认真考试类</div>
			<div class="kstot">
				<p><a href="">CISSP</a><a href="">BS25999</a><a href="">ISO202</a></p>
				<p><a href="">CISSP</a><a href="">LA</a><a href="">IA/NA</a></p>
				<p><a href="">CISSA</a><a href="">ITIL</a></p>
				<p><a href="">COBIT</a><a href="">IOS20071</a></p>
				<p><a href="">PMP</a><a href="">IA/LA</a></p>
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
</body>
</html>