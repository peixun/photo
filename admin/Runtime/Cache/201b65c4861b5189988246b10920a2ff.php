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
<div class="title"><?php echo (L("EDIT_DATA")); ?> [ <a href="<?php echo u($module_name.'/index');?>"><?php echo (L("BACK_LIST")); ?></a> ]</div>
<div id="result" class="result none"></div>
<form method='post' id="form" name="form" action="<?php echo u('RoleNode/update');?>"  enctype="multipart/form-data">
<table cellpadding=0 cellspacing=0 class="dataEdit" >
<tr>
	<td class="tRight" width="180"><?php echo (L("ROLE_NODE_TIP")); ?>：</td>
	<td class="tLeft" >

		<?php echo (L("ROLE_NODE_TIP_2")); ?><br />
		<?php echo (L("ROLE_NODE_TIP_3")); ?><br />
	</td>
</tr>
<tr>
	<td class="tRight"><?php echo (L("ACTION")); ?>：</td>
	<td class="tLeft" >
		<input type='text' name='action' id='' class='bLeft' value='index'  /><br /><?php echo (L("ACTION_TIP")); ?>
	</td>
</tr>
<tr>
	<td class="tRight" ><?php echo (L("ACTION_NAME")); ?>：</td>
	<td class="tLeft" >
		<input type='text' name='action_name' id='' class='bLeft' value='课程报名列表'  />
	</td>
</tr>

 <tr>
	<td class="tRight" ><?php echo (L("MODULE")); ?>：</td>
	<td class="tLeft" >
		<input type='text' name='module' id='' class='bLeft' value='Booking'  /><br /><?php echo (L("MODULE_TIP")); ?>
	</td>
</tr>
<tr>
	<td class="tRight" ><?php echo (L("MODULE_NAME")); ?>：</td>
	<td class="tLeft" >
		<input type='text' name='module_name' id='' class='bLeft' value='课程报名管理'  />
	</td>
</tr>

<tr>
	<td class="tRight" ><?php echo (L("BELONG_TO_NAV")); ?>：</td>
	<td class="tLeft" >
		<select name="nav_id" class="bLeft">
			<option value="0" <?php if($vo['nav_id'] == 0): ?>selected="selected"<?php endif; ?>><?php echo (L("NO_BELONG_TO_NAV")); ?></option>
			<?php if(is_array($nav_list)): foreach($nav_list as $key=>$nav_item): ?><option value="<?php echo ($nav_item["id"]); ?>" <?php if($vo['nav_id'] == $nav_item['id']): ?>selected="selected"<?php endif; ?>><?php echo ($nav_item["name"]); ?> </option><?php endforeach; endif; ?>
		</select>
	</td>
</tr> 

<tr>
	<td class="tRight" ><?php echo (L("SORT")); ?>：</td>
	<td class="tLeft" >
		<input type="text" name="sort" class="bLeft" value="<?php echo ($vo["sort"]); ?>"  />
	</td>
</tr>   
<tr>
	<td>&nbsp;</td>
	<td class="center"><div style="width:85%;margin:5px">
	<input type="hidden" name="id" value="<?php echo ($vo["id"]); ?>" />
	<input type="submit" value="<?php echo (L("SAVE_DATA")); ?>"  class="button small"> <input type="reset" class="button small" onclick="resetEditor()" value="<?php echo (L("RESET_DATA")); ?>" > 
	</div></td>
</tr>

</table>
</form>
</div>
</div>