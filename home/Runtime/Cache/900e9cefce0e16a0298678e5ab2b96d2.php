<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="家居,装潢,室内设计,360得利网" /> 
<meta name="description" content="家居,装潢,室内设计,360得利网" /> 
<link href="__PUBLIC__/css/global.css" rel="stylesheet" type="text/css" />
<link href="__PUBLIC__/css/info.css" rel="stylesheet" type="text/css" />
<title>活动管理</title>
<script src="__PUBLIC__/js/jquery-1.7.1.min.js"></script> 

<!--[if IE 6]> 
<script src="__PUBLIC__/js/DD_belatedPNG.js"></script> 
<script> 
DD_belatedPNG.fix('.top_k,.top_menu li a,.top_menu,.kuang_m,.date_pre,.date_next,.kuang_f,.link_main,img'); 
</script> 
<![endif]--> 
<link rel="stylesheet" href="__PUBLIC__/kindeditor/themes/default/default.css" />
<script charset="utf-8" src="__PUBLIC__/kindeditor/kindeditor.js"></script>
<script charset="utf-8" src="__PUBLIC__/kindeditor/lang/zh_CN.js"></script>

<script type="text/javascript">
	KindEditor.ready(function(K) {
		K.create('textarea[name="content_1"]', {
			allowFileManager : true
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
			<div class="info_tit">公司信息管理</div>
            <p class="clk"><a href="__APP__/Company/news">活动管理</a></p>
			<p ><a href="__APP__/Company/mycase">案例管理</a></p>
			<p><a href="__APP__/Company/designer">设计师管理</a></p>	
            <p><a href="__APP__/Company/construction">在建工地</a></p> <p><a href="__APP__/Company/reservation">预约工地</a></p> <p><a href="__APP__/Company/book">预约管理</a></p><p><a href="__APP__/Company/watchlist">收藏管理</a></p>   <p><a href="__APP__/Company/certificate">证书管理</a></p> <p><a href="__APP__/Company/service">服务承诺</a></p> <p><a href="__APP__/Company/contact">联系方式</a></p>			
			<div class="info_tit">账号管理</div>
			<p ><a href="__APP__/Company/management">公司信息</a></p>
			<p><a href="__APP__/Company/password">修改密码</a></p>
		</div>
	</div>
	<div class="info_rt">
		<div class="info_rtit">
			<div class="inrt_n">活动管理</div>
			<div class="inrt_r">首页&nbsp;&nbsp; &gt; <a href="javascript:void(0);">个人中心</a> &gt; <a href="javascript:void(0);" >活动管理</a></div>
		</div>
		<div class="info_msg"><span class="i_msg2" style="float:right; width:100px;"> <a href="__APP__/Company/news">返回活动列表</a></span><span class="i_msg1"><b>修改活动信息</b></span></div>
		<div class="info_cont" style="min-height:300px;">
			 <form action="__APP__/Company/updateNews" method="post" id="from2" name="form2" enctype="multipart/form-data">
<table  cellpadding="0" cellspacing="0" border="0" class="info_bd" width="100%">
  <tr>
    <td width="80" align="left">活动标题</td>
    <td><input type="text" name="name_1" class="bg_txt"  value="<?php echo ($vo["name_1"]); ?>"  /></td>
  </tr>
  <tr>
  	<td  width="80" align="left">属于社区</td>
    <td> <div id="uboxstyle"><select  name="cate_id" id="cate_id" class="bg_txt"  >
                  <option value="0">全部社区</option>
                  <?php if(is_array($cate)): $i = 0; $__LIST__ = $cate;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$cate): ++$i;$mod = ($i % 2 )?><option   
                    <?php if(($vo["cate_id"])  ==  $cate["id"]): ?>selected<?php endif; ?> value="<?php echo ($cate["id"]); ?>"><?php echo ($cate["name_1"]); ?>
                    </option><?php endforeach; endif; else: echo "" ;endif; ?> 
              </select>
              </div>
              </td>
  </tr>
   <tr>
    <td  width="80" align="left">标题图片</td>
    <td><input type="file" name="image"  class="bg_txt" /><?php if(($vo["image"])  !=  ""): ?><img src="__PUBLIC__/upload/news/small/<?php echo ($vo["image"]); ?>"  /><?php endif; ?></td>
  </tr>
  <tr>
    <td  width="80" align="left">活动简介</td>
    <td><textarea name="brief_1" class="bg_textarea" ><?php echo ($vo["brief_1"]); ?></textarea></td>
  </tr>
  <tr>
    <td  width="80" align="left">活动内容</td>
    <td><textarea id='content_1' name='content_1' style='width:620px;height:300px;visibility:hidden;' ><?php echo ($vo["content_1"]); ?></textarea></td>
  </tr>
 
  <tr>
    <td>&nbsp;</td>
    <td><input type="hidden" name="id" value="<?php echo ($vo["id"]); ?>" /><input type="submit" value="修改" class="log_btn11" />&nbsp;<input type="reset" value="取消" class="log_btn11" /></td>
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