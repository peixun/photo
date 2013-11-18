<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="家居,装潢,室内设计,360得利网" /> 
<meta name="description" content="家居,装潢,室内设计,360得利网" /> 
<link href="__PUBLIC__/css/global.css" rel="stylesheet" type="text/css" />
<link href="__PUBLIC__/css/info.css" rel="stylesheet" type="text/css" />
<title>修改信息</title>
<script src="__PUBLIC__/js/jquery-1.7.js"></script>

<!--[if IE 6]> 
<script src="__PUBLIC__/js/DD_belatedPNG.js"></script> 
<script> 
DD_belatedPNG.fix('.top_k,.top_menu li a,.top_menu,.kuang_m,.date_pre,.date_next,.kuang_f,.link_main,img'); 
</script> 
<![endif]--> 
<style>
.title_show{
	background:#EEEAE6;
    color: #272727;
    font-size: 14px;
    font-weight: bold;
    height: 24px;
    margin-top: 10px;
    padding: 6px 0 0 10px;
}
.company_content{
	line-height:20px;
	margin-top:8px;
	margin-bottom:8px;
}
.qy_pic{ 
	float:left; 
	width:120px;
	margin-bottom:10px;
	text-align:center;
}
</style>


<script type="text/javascript">
 var count=1;
	function addFj(){   
	if(count<8)
	{
	  var oTb = document.getElementById("tb1");
	
	  var oTr = oTb.insertRow(-1);
	 
	  var num = parseInt(document.regForm.fjCnt.value)+1; 
	  var no = parseInt(document.regForm.fjCnt.value);
	  document.regForm.fjCnt.value=num;
	  oTr.insertCell(0).innerHTML = "<span>图片：</span><input id='file' name='images[]' type=file   style=\"border:#CCC solid 1px; height:25px;\" /> <input type=button onclick='return delFj(this)' value='删除' class=\"log_btn11\"><br /><br /><span>说明：<input maxLength=50  type='text' name='imgname[]' name=photo_narrate   class=\"bg_txt\"  /><br /><br /><div id=tip class=red></div><div id=preview></div>";
      count ++;
	  return false;
	}else
	{
		alert("最多只能上传8张图片!");
		}
	}
	function delFj(obj,No){
		var num = parseInt(document.regForm.fjCnt.value);
		var new_tr = obj.parentNode.parentNode.parentNode;
		new_tr.removeChild(obj.parentNode.parentNode);
		if (num == No){
			document.regForm.fjCnt.value=num-1;
		}
		count --;
		return false;
	}
	

  </script>
  
  <script type="text/javascript">
 var counts=1;
	function addFjs(){   
	if(counts<8)
	{
	  var oTb = document.getElementById("tb2");
	
	  var oTr = oTb.insertRow(-1);
	 
	  var num = parseInt(document.regForm.fjCnts.value)+1; 
	  var no = parseInt(document.regForm.fjCnts.value);
	  document.regForm.fjCnts.value=num;
	  oTr.insertCell(0).innerHTML = "<span>qq号码：<input  type=\"text\" name='qq[]'  class=\"bg_txt\"   /> <input type=button onclick='return delFjs(this)' value='删除' class=\"log_btn11\"> <br /></span>";
      count ++;
	  return false;
	}else
	{
		alert("最多只能上传8张图片!");
		}
	}
	function delFjs(obj,No){
		var num = parseInt(document.regForm.fjCnts.value);
		var new_tr = obj.parentNode.parentNode.parentNode;
		new_tr.removeChild(obj.parentNode.parentNode);
		if (num == No){
			document.regForm.fjCnts.value=num-1;
		}
		count --;
		return false;
	}
	

  </script>


<SCRIPT LANGUAGE="JavaScript">
<!--
//指定当前组模块URL地址 
var URL = '__URL__';
var APP	 =	 '__APP__';
var PUBLIC = '__PUBLIC__';
//-->
</SCRIPT>
<script type="text/javascript">

function stopSends(name){
	//alert(name);
	document.getElementById('cover_image').value = name;
	$("#blog_img").attr("src",PUBLIC+"/upload/logo/thum/"+name);
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
	<div class="info_lf">
		<h2><img src="__PUBLIC__/images/mypage.jpg" /></h2>
			<div class="info_c">
			<div class="info_tit">公司信息管理</div>
            <p><a href="__APP__/Company/news">活动管理</a></p>
			<p ><a href="__APP__/Company/mycase">案例管理</a></p>
			<p><a href="__APP__/Company/designer">设计师管理</a></p>	
            <p><a href="__APP__/Company/construction">在建工地</a></p> <p><a href="__APP__/Company/reservation">预约工地</a></p> <p><a href="__APP__/Company/book">预约管理</a></p><p><a href="__APP__/Company/watchlist">收藏管理</a></p>   <p><a href="__APP__/Company/certificate">证书管理</a></p> <p><a href="__APP__/Company/service">服务承诺</a></p> <p><a href="__APP__/Company/contact">联系方式</a></p>			
			<div class="info_tit">账号管理</div>
			<p class="clk" ><a href="__APP__/Company/management">公司信息</a></p>
			<p ><a href="__APP__/Company/password">修改密码</a></p>
		</div>
	</div>
	<div class="info_rt">
		<div class="info_rtit">
			<div class="inrt_n">个人信息</div>
			<div class="inrt_r">首页&nbsp;&nbsp; &gt; <a href="__APP__/Member">个人中心</a> &gt; <a href="javascript:void(0)">个人信息</a></div>
		</div>
		<div class="info_mtit"><a href="javascript:void(0)">修改资料</a></div>      
		<div class="info_main">
     		

			<table cellpadding="0" cellspacing="0" border="0" class="info_bd" width="100%">
				<tr>
					<td width="100" align="left">公司名称：</td>
					<td ><b><?php echo ($com["company_name"]); ?></b>　</td>
				</tr>
               
				<tr>
					<td width="100" align="left">手机号码：</td>
					<td ><?php echo ($com["mobile"]); ?></td>
				</tr>
				
				<tr>
					<td width="100" align="left">联系电话：</td>
					<td ><?php echo ($com["tel"]); ?></td>
				</tr>
                </table>
                	<table cellpadding="0" cellspacing="0" border="0" class="info_bd" width="100%">
                <tr>
					<td width="100" align="left">企业logo：<br />
						尺寸宽990px<br />
                         高99px</td>
					<td ><form action="__APP__/Tool/upload_album" id="asdfa" name="asdfa" enctype="multipart/form-data" method="post" target="albums">
                   <input type="file" name="img"  onchange="javascript:document.getElementById('asdfa').submit();"   style="border:#CCC solid 1px; height:24px;" /><br />
<div style=" margin-top:5px;"><?php if(($com["logo"])  ==  ""): ?><img id='blog_img' width="500" height="50"src="__PUBLIC__/upload/logo/m.jpg"  class="pic_shadow" /><?php else: ?><img id='blog_img' width="500" height="50" src="__PUBLIC__/upload/logo/thum/<?php echo ($com["logo"]); ?>"  class="pic_shadow" /><?php endif; ?></div>  </form> <iframe  width="0" height="0" style="display:none;" name="albums"></iframe></td>
				</tr>
               </table><form name="regForm" method="post" id="regForm" action="__APP__/Company/update" enctype="multipart/form-data" onsubmit="return checkform();">

               	<table cellpadding="0" cellspacing="0" border="0" class="info_bd" width="100%">
                 <tr>
					<td width="100" align="left">服务区域：</td>
					<td ><input type="text" name="service_area" class="bg_txt"   value="<?php echo ($com["service_area"]); ?>" />例如 卢湾 徐汇 长宁 静安 普陀 闸北 等等</td>
				</tr>
                 <tr>
					<td width="100" align="left">业务范围：</td>
					<td >
                 
						<input name="business_scope[]" type="checkbox" value="新房装修" <?php if (strpos ($com['business_scope'],'新房装修')>-1){ ?>checked="checked" <?php } ?> /> 新房装修 <input name="business_scope[]" type="checkbox"  value="商铺装修" <?php if (strpos ($com ['business_scope'], '商铺装修' )>-1){ ?>checked="checked" <?php } ?>  /> 商铺装修 <input name="business_scope[]" type="checkbox" value="工装" <?php if (strpos ($com ['business_scope'], '工装' )>-1){ ?>checked="checked" <?php } ?>  /> 工装
                     
             
                 
                   </td>
				</tr>
                 <tr>
					<td width="100" align="left">主打价位：</td>
					<td >
                       <div id="uboxstyle">
                    <select name="main_price"  style="width:100px;" class="bLeft">
                  <option  value='-1'>选择主打价位</option>              
                  <option  value='1' <?php if(($com["main_price"])  ==  "1"): ?>selected<?php endif; ?>> 8万以下</option>     
                  <option  value='2' <?php if(($com["main_price"])  ==  "2"): ?>selected<?php endif; ?>> 8-15万</option>     
                  <option  value='3' <?php if(($com["main_price"])  ==  "3"): ?>selected<?php endif; ?>> 15-30万</option>     
                  <option  value='4' <?php if(($com["main_price"])  ==  "4"): ?>selected<?php endif; ?>> 30万-100万</option>     
                  <option  value='5' <?php if(($com["main_price"])  ==  "5"): ?>selected<?php endif; ?>> 100万以上</option>     
                    
                </select>
                </div>
                   </td>
				</tr>
			 	<tr>
					<td width="100" align="left">擅长风格：</td>
					<td >
                    		<input name="good_style[]" type="checkbox" value="现代简约" <?php if (strpos ($com ['good_style'], '现代简约')>-1){ ?>checked="checked" <?php } ?>  /> 现代简约 <input name="good_style[]" type="checkbox" value="田园" <?php if (strpos ($com ['good_style'], '田园')>-1){ ?>checked="checked" <?php } ?>  /> 田园 <input name="good_style[]" type="checkbox" value="欧美式" <?php if (strpos ($com ['good_style'], '欧美式')>-1){ ?>checked="checked" <?php } ?> /> 欧美式 <input name="good_style[]" type="checkbox" value="中式风格" <?php if (strpos ($com ['good_style'], '中式风格')>-1){ ?>checked="checked" <?php } ?> /> 中式风格 <input name="good_style[]" type="checkbox" value="地中海" <?php if (strpos ($com ['good_style'], '地中海')>-1){ ?>checked="checked" <?php } ?> /> 地中海 <input name="good_style[]" type="checkbox" value="混搭" <?php if (strpos ($com ['good_style'], '混搭')>-1){ ?>checked="checked" <?php } ?> /> 混搭
                    
                 </td>
				</tr>
                <tr>
					<td width="100" align="left">装修模式：</td>
					<td >
                    	<input name="decoration_pattern[]" type="checkbox" value="半包" <?php if (strpos ($com ['decoration_pattern'], '半包')>-1){ ?>checked="checked" <?php } ?>  /> 半包 <input name="decoration_pattern[]" type="checkbox" value="全包" <?php if (strpos ($com ['decoration_pattern'], '全包')>-1){ ?>checked="checked" <?php } ?> /> 全包 <input name="decoration_pattern[]" type="checkbox" value="装修监理" <?php if (strpos ($com ['decoration_pattern'], '装修监理')>-1){ ?>checked="checked" <?php } ?> /> 装修监理
                  
                    </td>
				</tr>
                <tr>
					<td width="100" align="left">总店地址：</td>
					<td ><input type="text" name="address" class="bg_txt"   value="<?php echo ($com["address"]); ?>" /></td>
				</tr>
			</table>
            
            <p class="title_show">企业简介</p>
            <p class="company_content"><textarea name="desc" class="bg_txt" style="width:718px; height:180px;"><?php echo ($com["desc"]); ?></textarea></p>
            <p class="title_show">企业介绍</p>
            <link rel="stylesheet" href="__PUBLIC__/kindeditor/themes/default/default.css" />
<script charset="utf-8" src="__PUBLIC__/kindeditor/kindeditor.js"></script>
<script charset="utf-8" src="__PUBLIC__/kindeditor/lang/zh_CN.js"></script>

<script type="text/javascript">
	KindEditor.ready(function(K) {
		K.create('textarea[name="content"]', {
			allowFileManager : true
		});
	});
</script>
<textarea id='content' name='content' style='width:720px;height:200px;visibility:hidden;' ><?php echo ($com["content"]); ?></textarea>
            
         	<p class="title_show">在线QQ</p>
         

  <table id="tb2" border=0 cellspacing=0 cellpadding=3 >
	 
	<tr> 
	<td>
   <span style="margin-bottom:10px; height:30px; line-height:30px;">qq号码：<input  type="text" name='qq[]'  class="bg_txt"    /> <br /></span>
	</td>
  
	</tr> 

	</table>
    <p style="margin-top:8px;"><input type="button" onclick="return addFjs()" class="log_btn11" value="添加qq"  /><input type="hidden" name="fjCnts" value="" /></p>
          
         <p class="title_show">企业照片</p> 
        
         <div class="clear"></div>
      

         <table id="tb1" border=0 cellspacing=0 cellpadding=3 >
	 
	<tr  > 
	<td  >
	<span>图片：</span><input id="Filedata"   name="images[]"  type=file value="上 传"  style="border:#CCC solid 1px; height:24px;" />&nbsp;<input type=button onclick='return delFj(this)' class="log_btn11" value='删除'><br><br>
	说明：<input  type="text" name='imgname[]'   class="bg_txt"   /> <br />
<br />
	</td>
  
	</tr> 

	</table>
    
    <p style="margin-top:8px;">  <input type="button" onclick="return addFj()" class="log_btn11" value="添加照片" /><input type="hidden" name="fjCnt" value="" /></p>
 <p style="margin-top:10px;"> <input type="hidden" name="logo" id="cover_image" value="<?php echo ($com["logo"]); ?>" />  
<input type="hidden" name="id"  value="<?php echo ($com["id"]); ?>" />  

<input type="submit" value="提交" class="log_btn11" /></p>

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

</body>
</html>