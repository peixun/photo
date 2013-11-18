<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo eyooC('SHOP_NAME');?> - 管理系统 - <?php echo (L("SYS_LOGIN")); ?></title>
<link rel="stylesheet" type="text/css" href="__TMPL__ThemeFiles/Css/style.css" />
<script type="text/javascript" src="__TMPL__ThemeFiles/Js/Base.js"></script>
<script type="text/javascript" src="__TMPL__ThemeFiles/Js/prototype.js"></script>
<script type="text/javascript" src="__TMPL__ThemeFiles/Js/mootools.js"></script>
<script type="text/javascript" src="__TMPL__ThemeFiles/Js/Ajax/ThinkAjax.js"></script>
<script type="text/javascript" src="__TMPL__ThemeFiles/Js/common.js"></script>
 <script language="JavaScript">
<!--
var PUBLIC	 =	 '__TMPL__ThemeFiles';
ThinkAjax.image = [	 '__TMPL__ThemeFiles/Images/loading2.gif', '__TMPL__ThemeFiles/Images/ok.gif','__TMPL__ThemeFiles/Images/update.gif' ]
ThinkAjax.updateTip	=	'<?php echo (L("LOGIN_TIP")); ?>';
function loginHandle(data,status){
	if (status==1)
	{
		$('result').innerHTML	=	'<span style="color:blue"><img SRC="__TMPL__ThemeFiles/Images/ok.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="" align="absmiddle" > <?php echo (L("SUCCESS_JUMP_3_SEC")); ?></span>';
		$('form1').reset();
		if(top.location == self.location)
		 {
			window.location.href = '__APP__';
		 }
		 else
		 {
			 window.location.href = '__APP__';
		 }
		
	}
	else
	{
		fleshVerify();
	}
}
function keydown(e){
	var e = e || event;
	if (e.keyCode==13)
	{
	ThinkAjax.sendForm('form1','<?php echo u('Public/checkLogin');?>',loginHandle,'result');
	}
}
function fleshVerify(){ 
//重载验证码
var timenow = new Date().getTime();
$('verifyImg').src= '<?php echo u('Verify/verify');?>&type=gif&rand='+timenow;
//$('verifyImg').src= '<?php echo u('Public/verify');?>/rand/'+timenow;

}
//重载窗口
function resetwindow()
{
	if(top.location != self.location)
	{
		top.location.href = self.location.href;
		return 
	}
}
//-->
</script>
</head>
<body onLoad="resetwindow();document.login.adm_name.focus()" class="loginbg" >
<form method='post' name="login" id="form1" >
	<input type="hidden" name="ajax" value="1">
	<div style="padding-top:118px;">
<table id="__01" width="699" height="337" border="0" cellpadding="0" cellspacing="0" style="margin:0px auto; border:0px; background:none;">
	<tr>
		<td rowspan="11">
			<img src="__TMPL__ThemeFiles/Images/login/logins_01.png" width="330" height="337" alt=""></td>
		<td rowspan="11">
			<img src="__TMPL__ThemeFiles/Images/login/logins_02.png" width="52" height="337" alt=""></td>
		<td colspan="3" class="resultbox">
			<div id="result" class="msgbox none"></div>
		</td>
		<td rowspan="11">
			<img src="__TMPL__ThemeFiles/Images/login/logins_04.png" width="155" height="337" alt=""></td>
		<td>
			<img src="__TMPL__ThemeFiles/Images/login/spacer.gif" width="1" height="99" alt=""></td>
	</tr>
	<tr>
		<td colspan="3" class="adm_name" >
		<input type="text" class="txtbox" warning="<?php echo (L("ADM_NAME_TIP")); ?>" name="adm_name" />
		</td>
		<td>
			<img src="__TMPL__ThemeFiles/Images/login/spacer.gif" width="1" height="27" alt=""></td>
	</tr>
	<tr>
		<td colspan="3">
			<img src="__TMPL__ThemeFiles/Images/login/logins_06.png" width="161" height="12" alt=""></td>
		<td>
			<img src="__TMPL__ThemeFiles/Images/login/spacer.gif" width="1" height="12" alt=""></td>
	</tr>
	<tr>
		<td colspan="3" class="adm_pwd" >
		<input type="password" class="pwdbox" warning="<?php echo (L("ADM_PWD_TIP")); ?>" name="adm_pwd" />	
		</td>
		<td>
			<img src="__TMPL__ThemeFiles/Images/login/spacer.gif" width="1" height="27" alt=""></td>
	</tr>
	<tr>
		<td colspan="3">
			<img src="__TMPL__ThemeFiles/Images/login/logins_08.png" width="161" height="13" alt=""></td>
		<td>
			<img src="__TMPL__ThemeFiles/Images/login/spacer.gif" width="1" height="13" alt=""></td>
	</tr>
	<tr>
		<td rowspan="3"  class="adm_verify" >
		<input type="text" class="verifybox" warning="<?php echo (L("VERIFY_CODE_TIP")); ?>" name="verify" onKeyDown="keydown(event)"  />
		</td>
		<td colspan="2">
			<img src="__TMPL__ThemeFiles/Images/login/logins_10.png" width="60" height="2" alt=""></td>
		<td>
			<img src="__TMPL__ThemeFiles/Images/login/spacer.gif" width="1" height="2" alt=""></td>
	</tr>
	<tr>
		<td rowspan="5">
			<img src="__TMPL__ThemeFiles/Images/login/logins_11.png" width="10" height="157" alt=""></td>
		<td>
		<img id="verifyImg" SRC="<?php echo u('Verify/verify');?>" onClick="fleshVerify(1)" BORDER="0" ALT="<?php echo (L("FRESH_VERIFY_TIP")); ?>" style="cursor:pointer" align="absmiddle">	
		</td>
		<td>
			<img src="__TMPL__ThemeFiles/Images/login/spacer.gif" width="1" height="22" alt=""></td>
	</tr>
	<tr>
		<td rowspan="4">
			<img src="__TMPL__ThemeFiles/Images/login/logins_13.png" width="50" height="135" alt=""></td>
		<td>
			<img src="__TMPL__ThemeFiles/Images/login/spacer.gif" width="1" height="3" alt=""></td>
	</tr>
	<tr>
		<td>
			<img src="__TMPL__ThemeFiles/Images/login/logins_14.png" width="101" height="14" alt=""></td>
		<td>
			<img src="__TMPL__ThemeFiles/Images/login/spacer.gif" width="1" height="14" alt=""></td>
	</tr>
	<tr>
		<td>
			<img src="__TMPL__ThemeFiles/Images/login/logins_15.png" width="101" height="29" onclick="ThinkAjax.sendForm('form1','<?php echo u('Public/checkLogin');?>',loginHandle,'result');" style="cursor:pointer;"  /></td>
		<td>
			<img src="__TMPL__ThemeFiles/Images/login/spacer.gif" width="1" height="29" alt=""></td>
	</tr>
	<tr>
		<td>
			<img src="__TMPL__ThemeFiles/Images/login/logins_16.png" width="101" height="89" alt=""></td>
		<td>
			<img src="__TMPL__ThemeFiles/Images/login/spacer.gif" width="1" height="89" alt=""></td>
	</tr>
</table>
</div>
</form>
</body>
</html>