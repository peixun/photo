<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
<script type='text/javascript' charset='utf-8' src='__TMPL__ThemeFiles/Js/kindeditor/kindeditor.js'></script>
<script language="JavaScript">
<!--
//指定当前组模块URL地址 
var URL = '__URL__';
var ROOT_PATH = '__ROOT__';
var admin_file = '<?php echo eyooC("ADMIN_FILE_NAME");?>';
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
<script language="JavaScript">
//定义JS中使用的语言变量
var VIEW = '<?php echo (L("VIEW")); ?>';
var CONFIRM_DELETE = '<?php echo (L("CONFIRM_DELETE")); ?>';
var CONFIRM_DELETE_IMAGE = '<?php echo (L("CONFIRM_DELETE_IMAGE")); ?>';
var NO_SELECT = '<?php echo (L("NO_SELECT")); ?>';
var CHOOSE_RECYCLE_ITEM = '<?php echo (L("CHOOSE_RECYCLE_ITEM")); ?>';
var SELECT_EDIT_ITEM = '<?php echo (L("SELECT_EDIT_ITEM")); ?>';
var SELECT_DEL_ITEM	=	'<?php echo (L("SELECT_DEL_ITEM")); ?>';
var CONFIRM_DELETE_FILE = '<?php echo (L("CONFIRM_DELETE_FILE")); ?>';
var CONFIRM_FOREVER_DELETE = '<?php echo (L("CONFIRM_FOREVER_DELETE")); ?>';
var CONFIRM_DELETE_USER_DATA = '<?php echo (L("CONFIRM_DELETE_USER_DATA")); ?>';
var CONFIRM_RESTORE = '<?php echo (L("CONFIRM_RESTORE")); ?>';
var ATTR_PRICE	=	'<?php echo L("ATTR_PRICE");?>';
var ATTR_STOCK	=	'<?php echo L("ATTR_STOCK");?>';

//ThinkAjax.send(ROOT_PATH+'/services/ajax.php?run=autoSendMail','',doDelete);
//ThinkAjax.send(ROOT_PATH+'/services/ajax.php?run=autoSend','',doDelete);
</script>
</head>

<body onload="loadBar(0)">

<div id="loader" ><?php echo (L("PAGE_LOADING")); ?></div>
<div id="main" class="main" >
<div class="content">
<div class="title"><?php echo (L("ADD_DATA")); ?> [ <a href="<?php echo u($module_name.'/index');?>"><?php echo (L("BACK_LIST")); ?></a> ]</div>
<div id="result" class="result none"></div>
<form method='post' id="form" name="form" action="<?php echo u('User/insert');?>"  enctype="multipart/form-data">
<table cellpadding=0 cellspacing=0 class="dataEdit" >
<tr>
	<td class="tRight" ><?php echo (L("MOBILE_PHONE")); ?>：</td>
	<td class="tLeft" >
		<input type="text" name="mobile" class="bLeftRequire" />
	</td>
</tr>

<tr>
	<td class="tRight" ><?php echo (L("USER_PWD")); ?>：</td>
	<td class="tLeft" >
		<input type="password" name="user_pwd" class="bLeftRequire" />
	</td>
</tr>

 <tr>
	<td class="tRight" ><?php echo (L("USER_PWD_CONFIRM")); ?>：</td>
	<td class="tLeft" >
		<input type="password" name="user_pwd_confirm" class="bLeftRequire" />
	</td>
</tr

><tr>
	<td class="tRight" >用户类型：</td>
	<td class="tLeft" >
		<select name="type" class="bLeft">
		
			<option value="1">个人</option>
            <option value="2">企业</option>
				
		</select>
	</td>
</tr> 
<tr>
	<td class="tRight" width="120">用户昵称：</td>
	<td class="tLeft" >
		<input type='text' name='user_name' id='' class='bLeftRequire' value=''  />个人类型请填写
	</td>
</tr>
<tr>
	<td class="tRight" width="120">公司名称：</td>
	<td class="tLeft" >
		<input type="text" name="company_name" class="bLeftRequire" />公司类似请填写
	</td>
</tr>
<tr>
	<td class="tRight" width="120">企业负责人：</td>
	<td class="tLeft" >
		<input type="text" name="name" class="bLeftRequire" />公司类似请填写
	</td>
</tr>
<tr>
	<td class="tRight" width="120">公司电话：</td>
	<td class="tLeft" >
		<input type="text" name="tel" class="bLeftRequire" />公司类似请填写
	</td>
</tr>

<tr>
	<td class="tRight" ><?php echo (L("EMAIL")); ?>：</td>
	<td class="tLeft" >
		<input type="text" name="email" class="bLeftRequire" />
	</td>
</tr>

<tr>
	<td></td>
	<td class="center"><div style="width:85%;margin:5px">
	<input type="submit" value="<?php echo (L("SAVE_DATA")); ?>"  class="button small"> <input type="reset" class="button small" onclick="resetEditor()" value="<?php echo (L("RESET_DATA")); ?>" > 
	</div></td>
</tr>
</form>
</table>
</div>
</div>