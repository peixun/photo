<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="家居,装潢,室内设计,360得利网" /> 
<meta name="description" content="家居,装潢,室内设计,360得利网" /> 
<link href="__PUBLIC__/css/global.css" rel="stylesheet" type="text/css" />
<link href="__PUBLIC__/css/anli.css" rel="stylesheet" type="text/css" />
<title>企业频道</title>
<script src="__PUBLIC__/js/jquery-1.7.1.min.js"></script> 

<!--[if IE 6]> 
<script src="__PUBLIC__/js/DD_belatedPNG.js"></script> 
<script> 
DD_belatedPNG.fix('.top_k,.top_menu li a,.top_menu,.kuang_m,.date_pre,.date_next,.kuang_f,.link_main,img'); 
</script> 
<![endif]--> 
<script>
	var APP="__APP__";
</script>
<script type="text/javascript">
function add_d(id,com_id){
	$.post('__APP__/Tool/addReservation',{id:id,com_id:com_id},function(aa){
		if(aa=='nologin'){
			alert("未登陆不能预约在建工地!");
			window.location.href = '__APP__/Public/login';
		}else if(aa=='ok'){
			
			alert("预约成功!");
		}else if(aa=='have'){
			alert("已经预约!");
		}else{
			alert("预约失败!");
		}
	});
}

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
	
	
	function designshow(sid)
      {
        var subitem=document.getElementById("design"+sid);
		var subitem1=document.getElementById("design1"+sid);

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
	 function says(){
		alert("所有的设计师都显示了");	 
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
		<div class="al_lfcont">
			<div class="pd_lfc">
				<div class="pd_big"><img src="__PUBLIC__/upload/company/l_<?php echo ($pics1['0']['images']); ?>" id="companypic"  /></div>
				<div class="pd_smll">
                <?php if(is_array($pics1)): $i = 0; $__LIST__ = $pics1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$piclist): ++$i;$mod = ($i % 2 )?><div class="pdsmll"><a href="javascript:void(0);" id="xwFocus1_<?php echo ($i); ?>"  class="<?php if(($i)  ==  "1"): ?>sdsm<?php endif; ?>"  onmouseover="javascript:cssShow('isXwFocus1_',<?php echo ($i); ?>,3,'xwFocus1_','sdsm|mid2_1uli2');"><img src="__PUBLIC__/upload/company/s_<?php echo ($piclist["images"]); ?>" class="casesshow" title="__PUBLIC__/upload/company/l_<?php echo ($piclist["images"]); ?>" /></a></div><?php endforeach; endif; else: echo "" ;endif; ?>
				</div>
				<div class="yhmsg">
                	<div style="padding-top:35px; padding-left:10px; padding-right:10px;">
                    	<?php if(is_array($alist)): $i = 0; $__LIST__ = $alist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$alist): ++$i;$mod = ($i % 2 )?><p style="height:25px; line-height:25px; border-bottom:#CCC dashed 1px;"><span style="float:right"><?php echo (toDate($alist["create_time"],"Y-m-d")); ?></span><a href="__APP__/BusinessChannel/discountshow/id/<?php echo ($alist["uid"]); ?>_<?php echo ($alist["id"]); ?>.shtml"><?php echo (utf_substr($alist["name_1"],34)); ?></a></p><?php endforeach; endif; else: echo "" ;endif; ?>
                    </div>
                </div>
			</div>
			<div class="pd_rtc">
				<div><img src="__PUBLIC__/images/gsqy.jpg" /></div>
				<div class="pd_qyc">
					<p><?php echo (utf_substr($vo["desc"],600)); ?></p>
					<!--<div class="pd_qymsg">
						<div class="pd_qypic"><img src="__PUBLIC__/images/qypic3.jpg" /></div>
						<div class="pd_txpic"></div>
					</div>-->
				</div>
				<div><img src="__PUBLIC__/images/qy_bot.jpg" /></div>
				<div class="qy_tablec">
					<table width="100%" border="0" cellpadding="0" cellspacing="0" class="gd_table_01">
						<tr>
							<td class="tit" align="center" width="35%">在建工地</td>
							<td class="tit" align="center" width="15%">户型</td>                
							<td class="tit" align="center" width="25%">参观时间</td>
							<td class="tit" align="right" width="25%"><a href="__APP__/BusinessChannel/constructionlist/id/<?php echo ($comid); ?>.shtml"><img src="__PUBLIC__/images/gdmore.jpg" align="bottom" /></a></td>
						</tr>
                        <?php if(is_array($conlist)): $i = 0; $__LIST__ = $conlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$conlist): ++$i;$mod = ($i % 2 )?><tr>
							<td align="" width="35%">&nbsp;&nbsp;<?php echo ($conlist["name_1"]); ?></td>
							<td align="center" width="15%"><?php echo ($conlist["root_unit"]); ?></td>
							<td align="center" width="25%"><?php echo (utf_substr($conlist["visit_time"],10)); ?></td>
							<td align="center" width="25%"><a href="javascript:add_d(<?php echo ($conlist["id"]); ?>,<?php echo ($vo["uid"]); ?>);"><img src="__PUBLIC__/images/book_sg.jpg" align="bottom" /></a></td>
						</tr><?php endforeach; endif; else: echo "" ;endif; ?>
					</table>
				</div>
			</div>	
			<div><img src="__PUBLIC__/images/alzstit.jpg"></div>
			<div class="al_lf_c">
				<div class="xs_mod"  id="caselist1">
                <?php if(is_array($caselist)): $i = 0; $__LIST__ = $caselist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$caselist): ++$i;$mod = ($i % 2 )?><div class="xs_mod_m">
						<a href="__APP__/BusinessChannel/caseshow/id/<?php echo ($caselist["uid"]); ?>_<?php echo ($caselist["id"]); ?>.shtml"><img src="__PUBLIC__/upload/case/s_<?php echo (getCasePic($caselist["id"])); ?>" /></a>
						<p><a href="__APP__/BusinessChannel/caseshow/id/<?php echo ($caselist["uid"]); ?>_<?php echo ($caselist["id"]); ?>.shtml"><?php echo ($caselist["name_1"]); ?></a></p>
					</div><?php endforeach; endif; else: echo "" ;endif; ?>
                    
					<div class="sub_more"><?php if(($caseshow)  ==  "1"): ?><a href="javascript:void(0);" onclick="say()">显示全部案例</a><?php else: ?><a href="javascript:void(0);" onclick="caseshow(1)">显示全部案例</a><?php endif; ?></div>
				</div> 
                <div class="xs_mod"  id="caselist11" style="display:none">
                <?php if(is_array($caselists)): $i = 0; $__LIST__ = $caselists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$caselist): ++$i;$mod = ($i % 2 )?><div class="xs_mod_m">
						<a href="__APP__/BusinessChannel/caseshow/id/<?php echo ($caselist["uid"]); ?>_<?php echo ($caselist["id"]); ?>.shtml"><img src="__PUBLIC__/upload/case/s_<?php echo (getCasePic($caselist["id"])); ?>" /></a>
						<p><a href="__APP__/BusinessChannel/caseshow/id/<?php echo ($caselist["uid"]); ?>_<?php echo ($caselist["id"]); ?>.shtml"><?php echo ($caselist["name_1"]); ?></a></p>
					</div><?php endforeach; endif; else: echo "" ;endif; ?>
                    
					<div class="sub_more"><a href="javascript:void(0);" onclick="caseshow(1)">收回全部案例</a></div>
				</div>
			</div>
			<div><img src="__PUBLIC__/images/zs_bot.jpg"></div>
			<div><img src="__PUBLIC__/images/sjs.jpg"></div>
			<div class="al_lf_c">
				<div class="sjs_modc" id="design1">
					
                    <?php if(is_array($designer)): $i = 0; $__LIST__ = $designer;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$designer): ++$i;$mod = ($i % 2 )?><div class="sjs_modle">
						<a href="__APP__/BusinessChannel/designershow/id/<?php echo ($designer["uid"]); ?>_<?php echo ($designer["id"]); ?>.shtml"><?php if(($designer["image"])  ==  ""): ?><img src="__PUBLIC__/images/header2.jpg" /><?php else: ?><img src="__PUBLIC__/upload/designer/small/<?php echo ($designer["image"]); ?>" /><?php endif; ?></a>
						<h5><a href="__APP__/BusinessChannel/designershow/id/<?php echo ($designer["uid"]); ?>_<?php echo ($designer["id"]); ?>.shtml">[ 详细 ]</a><?php echo ($designer["name_1"]); ?></h5>
						<p>案例：<em><?php echo (getDesignCase($designer["id"])); ?></em>篇</p>
						<p>费用：<code></code><?php echo ($designer["cost"]); ?></p>
					</div><?php endforeach; endif; else: echo "" ;endif; ?>					
					<div class="sjs_more"><?php if(($desgnershow)  ==  "1"): ?><a href="javascript:void(0);" onclick="says()">显示全部设计师</a><?php else: ?><a href="javascript:void(0);" onclick="designshow(1)">显示全部案例</a><?php endif; ?></div>
				</div>
                
                <div class="sjs_modc" id="design11" style="display:none">
              
					<?php if(is_array($designers)): $i = 0; $__LIST__ = $designers;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$designer): ++$i;$mod = ($i % 2 )?><div class="sjs_modle">
						<a href="__APP__/BusinessChannel/designershow/id/<?php echo ($designer["uid"]); ?>_<?php echo ($designer["id"]); ?>.shtml"><?php if(($designer["image"])  ==  ""): ?><img src="__PUBLIC__/images/header2.jpg" /><?php else: ?><img src="__PUBLIC__/upload/designer/small/<?php echo ($designer["image"]); ?>" /><?php endif; ?></a>
						<h5><a href="__APP__/BusinessChannel/designershow/id/<?php echo ($designer["uid"]); ?>_<?php echo ($designer["id"]); ?>.shtml">[ 详细 ]</a><?php echo ($designer["name_1"]); ?></h5>
						<p>案例：<em><?php echo (getDesignCase($designer["id"])); ?></em>篇</p>
						<p>费用：<code></code><?php echo ($designer["cost"]); ?></p>
					</div><?php endforeach; endif; else: echo "" ;endif; ?>					
					<div class="sjs_more"><a href="javascript:void(0);" onclick="designshow(1)">收回全部设计师</a></div>
				</div>
			</div>
			<div><img src="__PUBLIC__/images/zs_bot.jpg"></div>
		</div>
	</div>
	<div class="al_rt">
		<div class="rt_log">
			<a class="rl1" href="__APP__/Public/login">会员登录</a><a class="rl2" href="__APP__/Public/reg">注册</a><a class="rl3" href="__APP__/Booking/company/id/<?php echo ($comid); ?>">预约</a>
		</div>
		<div class="rt_c">
			<h5>基本信息</h5>
			<div class="zx_jbmsg">
				<p><em><?php echo (getPriceT($coms["main_price"])); ?></em></p>
				<p><?php echo (utf_substr($coms["good_style"],10)); ?></p>
				<p><code>19201</code> 位</p>
			</div>
		</div>
		<div class="rt_c">
			<h5>联系方式</h5>
			<div class="lx_one">
				<div><img src="__PUBLIC__/images/lx_top.jpg" /></div>
				<div class="lx_one_c">
					<div class="lxphone"> <?php echo ($coms["tel"]); ?></div>
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
			</div>
		</div>
		<div class="rt_c">
			<h5>热门预约的工地</h5>
			<div class="jgd_c">
            	<div class="jgdc_2" id="wb_conts" style="height:215px;">
                <ul class="wb_list" >
                    <?php if(is_array($resite)): $i = 0; $__LIST__ = $resite;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$resite): ++$i;$mod = ($i % 2 )?><li  class="bm_content">
                        	<span style="width:100px; float:right;"><?php echo ($resite["user_name"]); ?></span><span style=""><em><a href="__APP__/BusinessChannel/constructionlist/id/<?php echo ($comid); ?>#<?php echo ($resite["name_1"]); ?>"><?php echo ($resite["name_1"]); ?></a></em></span>
                        </li><?php endforeach; endif; else: echo "" ;endif; ?>
                    </ul>
                
				</div>
                <div class="jgdp"><a href="__APP__/BusinessChannel/constructionlist/id/<?php echo ($comid); ?>.shtml"><img src="__PUBLIC__/images/free_cg.jpg"></a></div>
			</div>
          
            	
		</div>
		<div class="rt_c">
			<h5><a href="__APP__/BusinessChannel/certificatelist/id/<?php echo ($comid); ?>.shtml">[ 查看更多证书 ]</a>荣誉证书</h5>
			<div class="qy_c">
				<?php if(is_array($certi)): $i = 0; $__LIST__ = $certi;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$certi): ++$i;$mod = ($i % 2 )?><div class="qy_mod" style="height:112px;">
					<a href="__APP__/BusinessChannel/certificateshow/id/<?php echo ($certi["uid"]); ?>_<?php echo ($certi["id"]); ?>.shtml"><img src="__PUBLIC__/upload/certificate/small/<?php echo ($certi["image"]); ?>" width="84" height="74"></a>
					<p><a href="__APP__/BusinessChannel/certificateshow/id/<?php echo ($certi["uid"]); ?>_<?php echo ($certi["id"]); ?>.shtml"><?php echo ($certi["name"]); ?></a></p>
				</div><?php endforeach; endif; else: echo "" ;endif; ?>
			</div>
		</div>
	</div>
<script src="__PUBLIC__/js/slider.js"></script> 

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
 $(document).ready(function() {
           
			
			$(".casesshow").mouseenter(
                function(){
                   var a= $(this).attr("title");
				   $("#companypic").attr("src",a);
            });
	
})
</script>
<script>
function cssShow(itemName,showId,num,bgItemName,clsName)       //(itemName+num)系列栏目名称，showID需要显示的编号
{
	var clsNameArr=new Array(2)
	if(clsName.indexOf("|")<=0){
		clsNameArr[0]=clsName
		clsNameArr[1]=""
	}else{
		clsNameArr[0]=clsName.split("|")[0]
		clsNameArr[1]=clsName.split("|")[1]
	}
	for(i=1;i<=num;i++)
	{ 
//	alert(i);
		if(document.getElementById(bgItemName+i)!=null)
			document.getElementById(bgItemName+i).className=clsNameArr[1]
		
		if(i==showId)
		{
			
			if(document.getElementById(bgItemName+i)!=null)
				document.getElementById(bgItemName+i).className=clsNameArr[0]
		}
	}
}

</script>
</body>
</html>