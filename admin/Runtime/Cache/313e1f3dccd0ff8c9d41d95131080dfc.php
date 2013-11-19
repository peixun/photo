<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo eyooC('SHOP_NAME');?>管理系统</title>
<link rel="stylesheet" type="text/css" href="__TMPL__ThemeFiles/Css/style.css" />
<script type="text/javascript" src="__TMPL__ThemeFiles/Js/Base.js"></script>
<script type="text/javascript" src="__TMPL__ThemeFiles/Js/prototype.js"></script>
<script type="text/javascript" src="__TMPL__ThemeFiles/Js/mootools.js"></script>
<script type="text/javascript" src="__TMPL__ThemeFiles/Js/Ajax/ThinkAjax.js"></script>
<script type="text/javascript" src="__TMPL__ThemeFiles/Js/common.js"></script>
<script type="text/javascript" src="__TMPL__ThemeFiles/Js/Util/ImageLoader.js"></script>

<script language="JavaScript">
<!--
//指定当前组模块URL地址
var URL = '__URL__';
var ROOT_PATH = '__ROOT__';
var APP	 =	 '__APP__';
var PUBLIC = '__TMPL__ThemeFiles';
ThinkAjax.image = [	 '__TMPL__ThemeFiles/Images/loading2.gif', '__TMPL__ThemeFiles/Images/ok.gif','__TMPL__ThemeFiles/Images/update.gif' ]
ImageLoader.add("__TMPL__ThemeFiles/Images/bgline.gif","__TMPL__ThemeFiles/Images/bgcolor.gif","__TMPL__ThemeFiles/Images/titlebg.gif");
ImageLoader.startLoad();
var VAR_MODULE = '<?php echo c('VAR_MODULE');?>';
var VAR_ACTION = '<?php echo c('VAR_ACTION');?>';
var CURR_MODULE = '<?php echo ($module_name); ?>';
//-->
</script>
<base target="main" />
</head>
<body>
<!-- 头部区域 -->
<div id="header" class="header">
<div class="headTitle" style="margin:10px 5px 5px 30px; font-size:26px; font-family:Tahoma, Geneva, sans-serif">
	<?php echo eyooC('SHOP_NAME');?>
</div>
<div id="mail_run" style="display:none; color:#ccc; font-size:12px; position:absolute; right:12px; top:25px;">邮件队列群发中</div>
<div id="send_run" style="display:none; color:#ccc; font-size:12px; position:absolute; right:122px; top:25px;">业务队列群发中</div>
<div id="sms_run"  style="display:none; color:#ccc; font-size:12px; position:absolute; right:232px; top:25px;">短信队列群发中</div>

<!-- 功能导航区 -->
<div class="topmenu">
<ul id="top_menu">
<?php if(is_array($roleNav)): foreach($roleNav as $key=>$vo): ?><li><span><a href="#" onClick="sethighlight(<?php echo ($key); ?>); parent.menu.location='<?php echo u('Index/menu?tag='.$vo['id'].'&title='.$vo['name']);?>'; parent.main.location='<?php echo u('Index/menumain?tag='.$vo['id'].'&title='.$vo['name']);?>'; return false;"><?php echo ($vo["name"]); ?></a></span></li><?php endforeach; endif; ?>

</ul>
</div>
	<div class="nav">

	<?php echo (L("WELCOME")); ?><?php echo ($_SESSION['adm_name']); ?>

	<a href="<?php echo u('Index/password');?>"><img src="__TMPL__ThemeFiles/Images/checked_out.png" width="16" height="16" border="0" alt="" align="absmiddle"> <?php echo (L("CHANGE_PASSWORD")); ?></a>
	<a href="__ROOT__/" target="_blank"><img src="__TMPL__ThemeFiles/Images/gohome.gif" width="16" height="16" border="0" alt="" align="absmiddle"> <?php echo (L("GO_HOME")); ?></a>
	<a href="javascript:;" onclick="clearCache();"><img src="__TMPL__ThemeFiles/Images/clear.gif" width="16" height="16" border="0" alt="" align="absmiddle"> <?php echo (L("CLEAR_CACHE")); ?></a>

	<a href="<?php echo u('Public/logout');?>" target="_top"><img SRC="__TMPL__ThemeFiles/Images/error.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="" align="absmiddle"> <?php echo (L("LOGOUT")); ?></a>

    </div>
    <div class="ajaxmessage"></div>
</div>
<script type="text/javascript" src="__TMPL__ThemeFiles/Js/jquery.js"></script>
<script>
jQuery.noConflict();

function sethighlight(n) {
	var lis = document.getElementById("top_menu").getElementsByTagName('span');
	for(var i = 0; i < lis.length; i++) {
		lis[i].className = '';
	}
	lis[n].className = 'current';
}
sethighlight(0);




</script>
</body>
</html>