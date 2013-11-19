<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="__PUBLIC__/css/global.css" type="text/css" rel="stylesheet" />
<link href="__PUBLIC__/css/anli.css" type="text/css" rel="stylesheet" />
<title>登录</title>
<!--[if IE 6]> 
<script src="__PUBLIC__/js/DD_belatedPNG.js"></script> 
<script> 
DD_belatedPNG.fix('.main,.menu_topk,.tishi_content,.tishi_menufoot,.title_c h1,.footer,.left_menu,.check_db a,.title_c2 h1,.zhek_list li .zhe_k,.tis_top,.tis_content,img'); 
</script> 
<![endif]--> 
<script src="__PUBLIC__/js/jquery-1.7.js"></script>
<SCRIPT src="__PUBLIC__/js/reg.js" type=text/javascript></SCRIPT>

<SCRIPT language=javascript>
var APP	 =	 '__APP__';
var PUBLIC	 =	 '__PUBLIC__';
function fleshVerify(){ 
//重载验证码
var timenow = new Date().getTime();
$('#verifyImg').attr('src','__APP__/Public/Verify/rand/'+timenow);

}
</SCRIPT>

</head>
<body style="background:none;">
<div class="container">
	<div class="login_m">
		<div class="login_logo"><a href="__APP__/"><img src="__PUBLIC__/images/logo_dl.jpg" /></a></div>
		<div class="login_cont">
			<div class="log_cc"> <form name="frm1" method="post" action="__APP__/Public/checkLogins" onsubmit="return checklogins();">
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="log_table">
					<tr>
						<td width="17%">用户名</td>
						<td width="83%"><input type="text" class="log_txt" id="user_name" name="user_name" value="请输入Email" onclick="if(this.value=='请输入Email')this.value='';" onblur="if(this.value=='')this.value='请输入Email';" /></td>
					</tr>
					<tr>
						<td width="17%">密&nbsp;&nbsp;码</td>
						<td width="83%"><input type="password" class="log_txt"  name="user_pwd" id="user_pwd"/></td>
					</tr>
					<tr>
						<td width="17%">验证码</td>
						<td width="83%" ><input  class="log_txt" style="width:85px;" name="verify" type="text"   id="verify"   />&nbsp;<IMG id="verifyImg" SRC="__APP__/Public/verify/" onClick="fleshVerify(1)" BORDER="0" ALT="点击刷新验证码" style="cursor:pointer" align="absmiddle"> 看不清？<a href="javascript:fleshVerify(1);" style="color:#1B98DB;">换一张</a></td>
					</tr>
					<tr>
						<td width="17%">&nbsp;</td>
						<td width="83%" class="fz12"><input name="indexlogin" type="hidden" value="1" /><input type="submit" value="登录"/> <a href="__APP__/Public/forgetpw">忘记密码？</a></td>
					</tr>
				</table>
                </form>
				<p style="margin-top:20px;">还不是得利网会员？<a href="__APP__/Public/reg">点这里注册</a></p>
			</div>
		</div>
        <div class="clear"></div>
	</div>
	<div class="login_f">
		<p><?php if(is_array($vo_page)): $i = 0; $__LIST__ = $vo_page;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><a href="__APP__/Help/<?php echo ($vo["id"]); ?>"><?php echo ($vo["name_1"]); ?></a><?php endforeach; endif; else: echo "" ;endif; ?></p>
		<p>Copyright © 1996 - 2010 DeDeng Corporation, All Rights Reserved</p>
	</div>
  
</div>
<div style="clear:both;"></div>
</body>
</html>