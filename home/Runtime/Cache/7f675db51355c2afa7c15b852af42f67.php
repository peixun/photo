<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="__PUBLIC__/css/global.css" rel="stylesheet" type="text/css" />
<link href="__PUBLIC__/css/anli.css" rel="stylesheet" type="text/css" />
<title>注册</title>
<meta name="keywords" content="家居,装潢,室内设计,360得利网" /> 
<meta name="description" content="家居,装潢,室内设计,360得利网" /> 

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
<script>
	function maishow(){
		document.getElementById('maifang').style.display='block';
		document.getElementById('smaifang').style.display='none';
	}
	
	function smaishow(){
		document.getElementById('smaifang').style.display='block';
		document.getElementById('maifang').style.display='none';
   }

</script>

</head>
<body style=" background:#efeae7 url(__PUBLIC__/images/reg_line.jpg) repeat-x;">
<div class="container">
	<div class="reg_c">
		<div><a href="__APP__/"><img src="__PUBLIC__/images/zc_tit.jpg" /></a></div>
		<div class="reg_cc">
			<div><img src="__PUBLIC__/images/use_zctit.jpg" /></div>
            <div style="font-size:14px; margin-top:10px;padding-left:160px; color:#180C08">
		<form name="regForm" method="post" id="regForm" action="__APP__/Public/insertUser">
			<div class="reg_clf" id="maifang">
				<table border="0" class="reg_table" cellpadding="0" cellspacing="0" width="100%">
                <tr>
						<td align="right" width="28%">E-mail：</td>
						<td width="72%"><input type="text" class="log_txt" name="email" id="email"   value="请输入Email" onclick="if(this.value=='请输入Email')this.value='';" onblur="if(this.value=='')this.value='请输入Email';" /><span id="email_tips"></span></td>
					</tr>
					<tr>
						<td align="right" width="28%">手机号码：</td>
						<td width="72%"><input type="text" class="log_txt" name="mobile" id="mobile" /><span id="mobile_tips"></span></td>
					</tr>
					<tr>
						<td align="right" width="28%">设置密码：</td> 
						<td width="72%"><input type="password" class="log_txt" name="user_pwd" id="password" type="password" /><span id="password_tips"></span></td>
					</tr>
					<tr>
						<td align="right" width="28%">密码确认：</td> 
						<td width="72%"><input type="password" class="log_txt" name="re_pwd" id="repassword" type="password" /><span id="repassword_tips"></span></td>
					</tr>
					<tr>
						<td align="right" width="28%">昵称：</td>
						<td width="72%"><input type="text" class="log_txt"  name="user_name" id="user_name"  /></td>
					</tr>
					
					<tr>
						<td align="right" width="28%">&nbsp;</td>
						<td width="72%" style="font-size:12px;"><input id="agreement" type="checkbox" value="1" checked="checked" /> 我已看过同意<a href="__APP__/Help/7">《注册服务条款》</a></td>
					</tr>
					<tr>
						<td colspan="2" height="0"> </td>
					</tr>
					<tr>
						<td align="right" width="28%">&nbsp;</td>
						<td width="72%" > <input name="type"  value="1" type="hidden"/><input type="submit"  value="注册" /></td>
					</tr>					
				</table>
			</div>
            </form>
           
			
	</div>
</div>
</body>
</html>