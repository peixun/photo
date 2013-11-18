<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="家居,装潢,室内设计,360得利网" /> 
<meta name="description" content="家居,装潢,室内设计,360得利网" /> 
<link href="__PUBLIC__/css/global.css" rel="stylesheet" type="text/css" />
<link href="__PUBLIC__/css/anli.css" rel="stylesheet" type="text/css" />
<link href="__PUBLIC__/css/casefocus.css" rel="stylesheet" type="text/css" />

<title>案例详情</title>
<script src="__PUBLIC__/js/jquery-1.7.1.min.js"></script> 
<!--[if IE 6]> 
<script src="__PUBLIC__/js/DD_belatedPNG.js"></script> 
<script> 
DD_belatedPNG.fix('.top_k,.top_menu li a,.top_menu,.kuang_m,.date_pre,.date_next,.kuang_f,.link_main,img'); 
</script> 
<![endif]--> 
<script>
function addAttention(id,com_id){
	$.post('__APP__/Tool/addAttention',{id:id,model:'case',com_id:com_id,'rand':Math.random()},function(aa){
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

function add_design(id,com_id,designer_id){
	$.post('__APP__/Tool/addDesignBook',{id:id,com_id:com_id,designer_id:designer_id,'rand':Math.random()},function(aa){
		if(aa=='nologin'){
			alert("未登陆,不能预约本案设计!");
			window.location.href = '__APP__/Public/login';
		}else if(aa=='ok'){
			alert("预约成功!");
		}else if(aa=='have'){
			alert("已经预约本案设计师了!");
		}else{
			alert("预约失败!");
		}
	});
}
	
</script>
<script>
	function caseshow(sid)
      {
        var subitem=document.getElementById("caselist"+sid);
		var subitem1=document.getElementById("caselist1"+sid);

      //subitem.style.display=subitem.style.display=='none'?'':'none';
      if(subitem.style.display=='none'){
        	subitem.style.display='';
			subitem1.style.display='none';

       }
       else{
       subitem.style.display='none';
	   subitem1.style.display='';

      }
     }
	 function say(){
		alert("所有的案例都显示了");	 
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
	<div class="al_lf">
		<h2><?php echo ($Case["name_1"]); ?>【<a href="javascript:void(0);" onclick="addAttention(<?php echo ($Case["id"]); ?>,<?php echo ($comid); ?>);"><font style="color:#FFFFFF;">收藏</font></a>】</h2>
		<div class="al_lfcont">
			<div class="base_msg">
				<div class="bm_pic">
                <div id="banner"> 
                    <div id="banner_bg"></div> 
                    
                    <div id="banner_info"></div> 
                    <ul> 
                    	<?php if(is_array($pics1)): $i = 0; $__LIST__ = $pics1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$pics1): ++$i;$mod = ($i % 2 )?><li><?php echo ($i); ?></li><?php endforeach; endif; else: echo "" ;endif; ?>
                    </ul> 
                    <div id="banner_list"> 
                    	<?php if(is_array($pics)): $i = 0; $__LIST__ = $pics;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><a href="javascript:void(0);" >                 
                        <img src="__PUBLIC__/upload/case/l_<?php echo ($vo["images"]); ?>" /></a><?php endforeach; endif; else: echo "" ;endif; ?>
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
				<div class="bm_txt">
					<h3>本案基本情况：</h3>
					<p>社区：<em><a href="__APP__/Shequ/<?php echo ($Case["cate_pid"]); ?>"><?php echo (getHuxing($Case["cate_pid"])); ?></a></em></p>
					<p>户型：<em><?php echo (getHuxing($Case["cate_id"])); ?></em></p>
					<p>面积：<em><?php echo ($Case["area"]); ?></em></p>    
					<p>风格：<em><?php echo ($Case["styles"]); ?></em></p>
					<p>预算：<em><?php echo ($Case["budget"]); ?></em></p>
					<p>设计师：<em><?php echo (getDesinger($Case["desinger_id"])); ?></em></p>
					<div class="bm_btn"><a href="__APP__/Booking/company/id/<?php echo ($comid); ?>"><img src="__PUBLIC__/images/free_yy.jpg" /> </a></div>
				</div>
			</div>
			<div><img src="__PUBLIC__/images/alcs.jpg" /></div>
			
			<div class="al_lf_c" style="text-align:left; padding:10px; margin-bottom:20px; line-height:20px;"><?php echo ($Case["content_1"]); ?></div>
			<div><img src="__PUBLIC__/images/xsaltit.jpg" /></div>
			<div class="al_lf_c">
				<div class="xs_mod" id="caselist1">
					<?php if(is_array($gxlist)): $i = 0; $__LIST__ = $gxlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): ++$i;$mod = ($i % 2 )?><div class="xs_mod_m">
						<a href="__APP__/BusinessChannel/caseshow/id/<?php echo ($sub["uid"]); ?>_<?php echo ($sub["id"]); ?>.shtml"><img src="__PUBLIC__/upload/case/s_<?php echo (getCasePic($sub["id"])); ?>" /></a>
						<p><a href="__APP__/BusinessChannel/caseshow/id/<?php echo ($sub["uid"]); ?>_<?php echo ($sub["id"]); ?>.shtml"><?php echo ($sub["name_1"]); ?></a></p>
					</div><?php endforeach; endif; else: echo "" ;endif; ?>
					<div class="sub_more"><?php if(($caseshow)  ==  "2"): ?><a href="javascript:void(0);" onclick="say()">显示全部案例</a><?php else: ?><a href="javascript:void(0);" onclick="caseshow(1)">显示全部案例</a><?php endif; ?></div>
				</div>
                <div class="xs_mod" id="caselist11" style="display:none">
					<?php if(is_array($gxlist1)): $i = 0; $__LIST__ = $gxlist1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): ++$i;$mod = ($i % 2 )?><div class="xs_mod_m">
						<a href="__APP__/BusinessChannel/caseshow/id/<?php echo ($sub["uid"]); ?>_<?php echo ($sub["id"]); ?>.shtml"><img src="__PUBLIC__/upload/case/s_<?php echo (getCasePic($sub["id"])); ?>" /></a>
						<p><a href="__APP__/BusinessChannel/caseshow/id/<?php echo ($sub["uid"]); ?>_<?php echo ($sub["id"]); ?>.shtml"><?php echo ($sub["name_1"]); ?></a></p>
					</div><?php endforeach; endif; else: echo "" ;endif; ?>
					<div class="sub_more"><a href="javascript:void(0);" onclick="caseshow(1)">收回全部案例</a></div>
				</div>
			</div>
			<div><img src="__PUBLIC__/images/zs_bot.jpg" /></div>
			<div class="gs_tit"><span>（共 <em><?php echo ($count); ?></em> 条）</span><?php echo ($company["company_name"]); ?>公司点评</div>
			<div class="al_lf_c">
				<div class="mess_mod">
					<?php if(is_array($commentlist)): $i = 0; $__LIST__ = $commentlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$commentlist): ++$i;$mod = ($i % 2 )?><div class="mess_m_line">
						<div class="mess_m_pic"><img src="__PUBLIC__/images/house.jpg" /></div>
						<div class="mess_m_txt">
							<div class="mtit"><span><?php echo (toDate($commentlist["create_time"],"Y-m-d")); ?><em> </em></span><?php echo (getUname($commentlist["uid"])); ?></div>
							<div class="mcont"> <?php echo ($commentlist["content"]); ?></div>
						</div>
					</div><?php endforeach; endif; else: echo "" ;endif; ?>
                    
					<div id="pagavation"><?php echo ($page); ?></div>
				</div>
				<div class="wypl_m">
                <form name="from12" id="form12" action="__APP__/BusinessChannel/inserComment" method="post" onsubmit="return checkformMessage();">
					<div class="wyplm_c">
						<h4>我要评论</h4>
						<div class="mes_k">
							<div class="m_txtt">留言内容</div>
							<div class="m_txtc"><textarea class="mk_tax" id="content" name="content" onclick="if(this.value == '请留言')this.value=''">请留言</textarea></div>
						</div>
						<div class="mk_btn"><input name="id" type="hidden" value="<?php echo ($Case["id"]); ?>" /><input type="submit" class="mkbtn" value="案例评论" /></div>
					</div>
                  </form>
				</div>				
			</div>
			<div><img src="__PUBLIC__/images/zs_bot.jpg" /></div>
		</div>
	</div>
	<div class="al_rt">
		<div class="rt_log">
			<a href="__APP__/Public/login" class="rl1">会员登录</a><a href="__APP__/Public/reg" class="rl2">注册</a><a href="__APP__/Booking" class="rl3">预约</a>
		</div>
		<div  class="rt_c">
			<h5><?php echo ($company["company_name"]); ?></h5>
			<div class="lx_one">
				<div><img src="__PUBLIC__/images/lx_top.jpg" /></div>
				<div class="lx_one_c">
					<div class="lxphone"> <?php echo ($company["mobile"]); ?></div>
				</div>
				<div><img src="__PUBLIC__/images/lx_line.jpg" /></div>
				<div class="lx_one_c">
					<div class="qq2">
						<div class="qq2_c">
                        <?php if(($showdiv)  ==  "1"): ?><p>客服1：<a href="javascript;"><img src="__PUBLIC__/images/zx_qq.jpg" align="absbottom" /></a></p>
                        <?php else: ?>
						 <?php if(is_array($qq)): $i = 0; $__LIST__ = $qq;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$qq): ++$i;$mod = ($i % 2 )?><p style="line-height:24px; height:24px;">客服<?php echo ($i); ?>：<a href="http://wpa.qq.com/msgrd?V=1&amp;Uin=<?php echo ($qq["qq"]); ?>&amp;Site=得利网&amp;Menu=yes" target="_blank"><img src="http://wpa.qq.com/pa?p=2:<?php echo ($qq["qq"]); ?>:42" height="20" border="0" alt="<?php echo ($qq["qq"]); ?>" align="middle" /></a>
</p><?php endforeach; endif; else: echo "" ;endif; ?><?php endif; ?>
						</div>
					<div><img src="__PUBLIC__/images/qq2bot.jpg" /></div>
					</div>
				</div>
				<div><img src="__PUBLIC__/images/lx_line.jpg" /></div>
				<div class="lx_one_c">
					<div class="lxdadd" style="text-align:left;"><?php echo ($company["address"]); ?></div>
				</div>
				<div><img src="__PUBLIC__/images/lx_bot.jpg" /></div>
                <div class="gd_tip">
				<?php if(($Addressdiv)  ==  "1"): ?><?php if(is_array($comaddress)): $i = 0; $__LIST__ = $comaddress;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><div class="gd_mo"><a href="__APP__/BusinessChannel/contact/id/<?php echo ($comid); ?>#<?php echo ($vo["name"]); ?>"><?php echo ($vo["name"]); ?></a></div><?php endforeach; endif; else: echo "" ;endif; ?><?php endif; ?>
                </div>
			</div>
		</div>
		<div  class="rt_c">
			<h5>本案设计师</h5>
			<div class="sjs_sq">
				<div class="sjs_m"><a href="__APP__/BusinessChannel/designershow/id/<?php echo ($comid); ?>_<?php echo ($desinger["id"]); ?>.shtml"><?php if(($desinger["image"])  ==  ""): ?><img src="__PUBLIC__/images/header1.jpg" /><?php else: ?><img src="__PUBLIC__/upload/designer/small/<?php echo ($desinger["image"]); ?>" /><?php endif; ?></a></div>
				<div class="sj_tit">设计师：<em><a href="__APP__/BusinessChannel/designershow/id/<?php echo ($comid); ?>_<?php echo ($desinger["id"]); ?>.shtml"><?php echo ($desinger["name_1"]); ?></a></em></div>
				<p>所属公司：<?php echo ($company["company_name"]); ?></p>
				<p>擅长风格：
                <?php echo ($desinger["good_style"]); ?>
                </p>
				<p>成功案例：<?php echo (getDesignCase($desinger["id"])); ?>套</p>
				<div class="sj_yj"><a href="javascript:add_design(<?php echo ($Case["id"]); ?>,<?php echo ($comid); ?>,<?php echo ($desinger["id"]); ?>);"><img src="__PUBLIC__/images/yj_btn.jpg" /></a></div>
			</div>
		</div>
		<div class="al_rm" style="height:250px;">sfd</div>
		<div class="clear11"></div>
		<div class="rt_c">
			<h1>促销活动</h1>
			<div class="al_sq" style="height:150px;">
				<?php if(is_array($article)): $i = 0; $__LIST__ = $article;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$article): ++$i;$mod = ($i % 2 )?><p <?php if(($i)  ==  "6"): ?>style="border-bottom:none;"<?php endif; ?>><a href="__APP__/BusinessChannel/discountshow/id/<?php echo ($article["uid"]); ?>_<?php echo ($article["id"]); ?>.shtml"><?php echo (utf_substr($article["name_1"],30)); ?></a></p><?php endforeach; endif; else: echo "" ;endif; ?>
			</div>
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
<script>
	function checkformMessage()
	{
	var title = $("#content").attr("value");
	if(title==='')
	{ 	
		alert("评论内容不能为空！");
		return false;
	}
		if(title==='请留言')
	{ 	
		alert("请正确的输入留言信息！");
		return false;
	}
	
	}
</script>
</body>
</html>