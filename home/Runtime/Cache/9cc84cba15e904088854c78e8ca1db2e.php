<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="家居,装潢,室内设计,360得利网" /> 
<meta name="description" content="家居,装潢,室内设计,360得利网" /> 
<link href="__PUBLIC__/css/global.css" rel="stylesheet" type="text/css" />
<link href="__PUBLIC__/css/anli.css" rel="stylesheet" type="text/css" />
<title>案例展示</title>
<script src="__PUBLIC__/js/jquery-1.7.1.min.js"></script> 

<!--[if IE 6]> 
<script src="__PUBLIC__/js/DD_belatedPNG.js"></script> 
<script> 
DD_belatedPNG.fix('.top_k,.top_menu li a,.top_menu,.kuang_m,.date_pre,.date_next,.kuang_f,.link_main,img'); 
</script> 
<![endif]--> 

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
	<div class="anli_gg"><?php if(($$coms["logo"])  ==  ""): ?><img src="__PUBLIC__/upload/logo/thum/<?php echo ($coms["logo"]); ?>" /><?php else: ?><img src="__PUBLIC__/images/al_logo.jpg" /><?php endif; ?></div>
	<div class="al_nav">
		<ul>
			<li><a href="__APP__/BusinessChannel/show/id/<?php echo ($comid); ?>.shtml">频道首页</a></li>
			<li><a href="__APP__/BusinessChannel/company/id/<?php echo ($comid); ?>.shtml">企业简介</a></li>
			<li><a href="__APP__/BusinessChannel/caselist/id/<?php echo ($comid); ?>.shtml">案例展示</a></li>
			<li><a href="__APP__/BusinessChannel/designerlist/id/<?php echo ($comid); ?>.shtml">设计师</a></li>
			<li><a href="__APP__/BusinessChannel/constructionlist/id/<?php echo ($comid); ?>.shtml">在建工地</a></li>
			<li><a href="__APP__/BusinessChannel/discountslist/id/<?php echo ($comid); ?>.shtml">优惠促销</a></li>
			<li><a href="__APP__/BusinessChannel/service/id/<?php echo ($comid); ?>.shtml">服务承诺</a></li>
			<li><a href="__APP__/BusinessChannel/contact/id/<?php echo ($comid); ?>.shtml">联系方式</a></li>                                                               
		</ul>
	</div>
	<div class="al_lf" >
		<h2>案例展示</h2>
		<div class="al_lfcont" style="height:920px;">
			
			<div><img src="__PUBLIC__/images/zs_tit.jpg" /></div>
			<div class="al_lf_c">
				<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): ++$i;$mod = ($i % 2 )?><div class="aldetail_mod">
					<div class="al_detm">
						<div class="ald_pic"><a href="__APP__/BusinessChannel/caseshow/id/<?php echo ($sub["uid"]); ?>_<?php echo ($sub["id"]); ?>.shtml"><img src="__PUBLIC__/upload/case/s_<?php echo (getCasePic($sub["id"])); ?>" /></a></div>
						<div class="ald_txt">
							<p>社区：<em><?php echo (getHuxing($sub["cate_pid"])); ?></em></p>
							<p>户型：<em><?php echo (getHuxing($sub["cate_id"])); ?></em></p>
							<p>价格：<em><?php echo ($sub["budget"]); ?></em></p>
							<p>风格：<em><?php echo ($sub["styles"]); ?></em></p>
							<p>设计师：<em><?php echo (getDesinger($sub["desinger_id"])); ?></em></p>
						</div>
						<div class="zs_dtmsg"><a href="__APP__/BusinessChannel/caseshow/id/<?php echo ($sub["uid"]); ?>_<?php echo ($sub["id"]); ?>.shtml"><?php echo ($sub["name_1"]); ?></a></div>
					</div>
				</div><?php endforeach; endif; else: echo "" ;endif; ?>
		
				
			</div>
			<div><img src="__PUBLIC__/images/zs_bot.jpg" /></div>	
		<div class="al_pages">
			<div class="al_plf">共有<em><?php echo ($counts); ?></em>套案例</div>
			<div id="pagavation"><?php echo ($page); ?></div>
		</div>		
		</div>
	</div>
	<div class="al_rt">
		<div class="rt_log">
			<a href="__APP__/Public/login" class="rl1">会员登录</a><a href="__APP__/Public/reg" class="rl2">注册</a><a href="__APP__/Booking" class="rl3">预约</a>
		</div>
		<div  class="rt_c">
			<h5><?php echo ($coms["company_name"]); ?></h5>
			<div class="lx_one">
				<div><img src="__PUBLIC__/images/lx_top.jpg" /></div>
				<div class="lx_one_c">
					<div class="qq1"> <?php echo ($coms["tel"]); ?></div>
				</div>
				<div><img src="__PUBLIC__/images/lx_line.jpg" /></div>
				<div class="lx_one_c">
					<div class="qq2">
						<div class="qq2_c">
                        <?php if(is_array($qqs)): $i = 0; $__LIST__ = $qqs;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$qq): ++$i;$mod = ($i % 2 )?><p style="line-height:24px; height:24px;">客服<?php echo ($i); ?>：<a href="http://wpa.qq.com/msgrd?V=1&amp;Uin=<?php echo ($qq["qq"]); ?>&amp;Site=得利网&amp;Menu=yes" target="_blank"><img src="http://wpa.qq.com/pa?p=2:<?php echo ($qq["qq"]); ?>:42" height="20" border="0" alt="<?php echo ($qq["qq"]); ?>" align="middle" /></a>
</p><?php endforeach; endif; else: echo "" ;endif; ?>
						</div>
					<div><img src="__PUBLIC__/images/qq2bot.jpg" /></div>
					</div>
				</div>
				<div><img src="__PUBLIC__/images/lx_line.jpg" /></div>
				<div class="lx_one_c">
					<div class="lxdadd"><?php echo ($coms["address"]); ?></div>
				</div>
				<div><img src="__PUBLIC__/images/lx_bot.jpg" /></div>
                <div class="gd_tip">
				<?php if(($Addressdiv)  ==  "1"): ?><?php if(is_array($comaddress)): $i = 0; $__LIST__ = $comaddress;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><div class="gd_mo"><a href="__APP__/BusinessChannel/contact/id/<?php echo ($comid); ?>#<?php echo ($vo["name"]); ?>"><?php echo ($vo["name"]); ?></a></div><?php endforeach; endif; else: echo "" ;endif; ?><?php endif; ?>
                </div>
				<div class="sjzx_d"><a href="__APP__/Booking/company/id/<?php echo ($comid); ?>"><img src="__PUBLIC__/images/freeonline.jpg" /></a></div>
			</div>
		</div>
		
		<div class="al_rm" style="height:250px;">广告位置1</div>
		<div class="clear11"></div>
		<div class="al_rm" style="height:250px;">广告位置2</div>
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