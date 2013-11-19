<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="家居,装潢,室内设计,360得利网" /> 
<meta name="description" content="家居,装潢,室内设计,360得利网" /> 
<link href="__PUBLIC__/css/global.css" rel="stylesheet" type="text/css" />
<link href="__PUBLIC__/css/info.css" rel="stylesheet" type="text/css" />
<title><?php echo ($title); ?></title>
<script src="__PUBLIC__/js/jquery-1.7.1.min.js"></script> 

<!--[if IE 6]> 
<script src="__PUBLIC__/js/DD_belatedPNG.js"></script> 
<script> 
DD_belatedPNG.fix('.top_k,.top_menu li a,.top_menu,.kuang_m,.date_pre,.date_next,.kuang_f,.link_main,img'); 
</script> 
<![endif]--> 

<script type="text/javascript">
     $(document).ready(function() {
	$("#province_id").change(function(){
				var id = $(this).val();
				if(id != ''){
						$.post("__APP__/Member/get_city", {id:id}, function(res){
									
									$("#city_id").html(res);
							});
					}
		});

$("#city_id").click(function(){	 
			var city_id = $(this).val();
			$.post('__APP__/Member/get_area',{city_id:city_id}, function(res){
									
									$("#area_id").html(res);
							});
			
					});

}); 


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
	<div class="info_lf">
		<h2><img src="__PUBLIC__/images/mypage.jpg" /></h2>
		<div class="info_c">
			<div class="info_tit">预约管理</div>
               <p ><a href="__APP__/Member/myAnnounce">促销公告&nbsp;<?php echo getDayNewcout();?></a></p>
            <p><a href="__APP__/Member/news">已报名活动</a></p>
			<p ><a href="__APP__/Member/myConstruction">已预约工地</a></p>
            <p ><a href="__APP__/Member/bookDesigner">已预约设计师</a></p>
			<p><a href="__APP__/Member/myAttention">我的收藏</a></p>		
			<div class="info_tit">账号管理</div>
			<p class="clk"><a href="__APP__/Member/index">个人信息</a></p>
			<p><a href="__APP__/Member/password">修改密码</a></p>
		</div>
	</div>
	<div class="info_rt">
	<div class="info_rtit">
			<div class="inrt_n">完善用户信息</div>
			<div class="inrt_r">首页&nbsp;&nbsp; &gt; <a href="__APP__/Member">个人中心</a> &gt; <a href="javascript:void(0)">完善用户信息</a></div>
		</div>
		<div class="info_mtit"><a href="javascript:void(0)">完善用户信息</a></div>    
		<div class="info_main">
			<form name="forme23" id="form23" action="__APP__/Member/doInformation" method="post">
            <table cellpadding="0" cellspacing="0" border="0" class="info_bd">
				<tr>
					<td width="40%" align="right">真实姓名：</td>
					<td width="60%"><input type="text"  name="real_name" class="bg_txt_small"  value="<?php echo ($user["real_name"]); ?>"/></td>
				</tr>
				<tr>
					<td width="40%" align="right">性别：</td>
					<td width="60%"><input name="sex" type="radio" value="1" <?php if(($user["sex"])  ==  "1"): ?>checked="checked"<?php endif; ?> />男 <input name="sex" type="radio" value="2"  <?php if(($user["sex"])  ==  "2"): ?>checked="checked"<?php endif; ?> />女 <input name="sex" type="radio" value="0" <?php if(($user["sex"])  ==  "0"): ?>checked="checked"<?php endif; ?> /> 保密</td>
				</tr>
                
				<tr>
					<td width="40%" align="right">年龄：</td>
					<td width="60%"><input type="text" class="bg_txt_small" name="age" value="<?php echo ($user["age"]); ?>"/></td>
				</tr>
                 <tr>
					<td width="40%" align="right">邮编：</td>
					<td width="60%"><input type="text" class="bg_txt_small" name="zip" value="<?php echo ($user["zip"]); ?>"/></td>
				</tr>
                <tr>
					<td width="40%" align="right">所在地区：</td>
					<td width="60%">
                 <div id="uboxstyle"><select name="towns" id="area_id" style="width:100px;" class="bLeft">
                  <option  value='-1'>选择地区</option> 
                  <?php if(is_array($city)): $i = 0; $__LIST__ = $city;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$city): ++$i;$mod = ($i % 2 )?><option  value='<?php echo ($city["id"]); ?>' <?php if(($city["id"])  ==  $user["towns"]): ?>selected<?php endif; ?>><?php echo ($city["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>         
                </select>
                </div>
                </td>
				</tr>
                 <tr>
					<td width="40%" align="right">所在楼盘：</td>
					<td width="60%"><input type="text" class="bg_txt" name="shequ" value="<?php echo ($user["shequ"]); ?>"/></td>
				</tr>
                
                 <tr>
					<td width="40%" align="right">收件地址：</td>
					<td width="60%"><input type="text" class="bg_txt" name="address"  value="<?php echo ($user["address"]); ?>" /></td>
				</tr>
                <tr>
					<td width="40%" align="right"></td>
					<td width="60%"><input type="submit" class="log_btn11" value="确定"/> &nbsp; <input type="reset" class="log_btn11" value="取消"/></td>
				</tr>
				
			</table>
            </form>
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