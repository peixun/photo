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
<form method='post' id="form" name="form" action="<?php echo u('User/update');?>"  enctype="multipart/form-data">
<table cellpadding=0 cellspacing=0 class="dataEdit" >
<tr>
	<td class="tRight" width="120"><?php echo (L("USER_NAME")); ?>：</td>
	<td class="tLeft" >
		<?php echo ($vo["user_name"]); ?>
	</td>
</tr>
<tr>
	<td class="tRight" ><?php echo (L("NEW_USER_PWD")); ?>：</td>
	<td class="tLeft" >
		<input type="password" name="new_user_pwd" class="bLeft" />
	</td>
</tr>

 <tr>
	<td class="tRight" ><?php echo (L("NEW_USER_PWD_CONFIRM")); ?>：</td>
	<td class="tLeft" >
		<input type="password" name="new_user_pwd_confirm" class="bLeft" />
	</td>
</tr> 

<tr>
	<td class="tRight" ><?php echo (L("USER_GROUP")); ?>：</td>
	<td class="tLeft" >
		<select name="group_id" class="bLeft">
		<?php if(is_array($group_list)): foreach($group_list as $key=>$group_item): ?><option value="<?php echo ($group_item["id"]); ?>" <?php if($vo['group_id'] == $group_item['id']): ?>selected="selected"<?php endif; ?> ><?php echo ($group_item[$select_dispname]); ?></option><?php endforeach; endif; ?>			
		</select>
	</td>
</tr> 

<tr>
	<td class="tRight" ><?php echo (L("EMAIL")); ?>：</td>
	<td class="tLeft" >
		<?php echo ($vo["email"]); ?><input type="hidden" name="email" class="bLeftRequire" value="<?php echo ($vo["email"]); ?>" />
	</td>
</tr>

<tr>
	<td class="tRight" ><?php echo (L("MOBILE_PHONE")); ?>：</td>
	<td class="tLeft" >
		<input type="text" name="mobile_phone" class="bLeft" value="<?php echo ($vo["mobile_phone"]); ?>" />
	</td>
</tr>


<tr>
	<td class="tRight" >地区：</td>
	<td class="tLeft" >
		<select name="city_id">
		<?php if(is_array($city_list)): foreach($city_list as $key=>$city_item): ?><option value="<?php echo ($city_item["id"]); ?>" <?php if($vo['city_id'] == $city_item['id']): ?>selected="selected"<?php endif; ?>><?php echo ($city_item["name"]); ?></option><?php endforeach; endif; ?>
		</select>
	</td>
</tr>

<?php if(is_array($extend_fields)): foreach($extend_fields as $key=>$field_item): ?><tr>
		<td class="tRight" ><?php echo ($field_item["field_show_name"]); ?>：</td>
		<td class="tLeft" >
			<?php if($field_item['type'] == 0): ?><input type="text" name="<?php echo ($field_item["field_name"]); ?>" class="bLeft" value="<?php echo ($field_item["value"]); ?>" /><?php endif; ?>
			<?php if($field_item['type'] == 1): ?><select name="<?php echo ($field_item["field_name"]); ?>">
				<?php if(is_array($field_item["val_scope"])): foreach($field_item["val_scope"] as $key=>$val): ?><option value="<?php echo ($val); ?>" <?php if($field_item['value'] == $val): ?>selected="selected"<?php endif; ?>><?php echo ($val); ?></option><?php endforeach; endif; ?>
				</select><?php endif; ?>
		</td>
	</tr><?php endforeach; endif; ?>

<tr>
	<th colspan=2><?php echo L("ACCOUNT_INFO");?>	</th>
</tr>
<tr>
	<td class="tRight" ><?php echo (L("USER_SCORE")); ?>：</td>
	<td class="tLeft" >
		<input type='text' name='score' id='' class='bLeft' value='0'  />
	</td>
</tr>
<tr>
	<td class="tRight" ><?php echo (L("USER_MONEY")); ?>：</td>
	<td class="tLeft" >
		<?php echo eyooC("BASE_CURRENCY_UNIT");?> <input type="text" class="bLeft" name="money" value="<?php echo (priceVal($vo["money"])); ?>" />
	</td>
</tr>
<tr>
	<td></td>
	<td class="center"><div style="width:85%;margin:5px">
	<input type="hidden" name="id" value="<?php echo ($vo["id"]); ?>" />
	<input type="submit" value="<?php echo (L("SAVE_DATA")); ?>"  class="button small"> <input type="reset" class="button small" onclick="resetEditor()" value="<?php echo (L("RESET_DATA")); ?>" > 
	</div></td>
</tr>
</form>
</table>
</div>
</div>