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
<script type="text/javascript" src="__TMPL__ThemeFiles/Js/jquery.js"></script>
<script type="text/javascript" src="__TMPL__ThemeFiles/Js/jquery.json.js"></script>
<script type="text/javascript" src="__TMPL__ThemeFiles/Js/tabs.js"></script>
<script type="text/javascript">
	function sendDemo()
	{
		var mail_address = $("#mail_address").val();
		var smtp_server = $(".SMTP_SERVER").val();
		var smtp_account = $(".SMTP_ACCOUNT").val();
		var smtp_password = $(".SMTP_PASSWORD").val();
		var smtp_port = $(".SMTP_PORT").val();
		var smtp_auth = $(".SMTP_AUTH").val();
		var is_ssl = $(".IS_SSL:checked").val();
		
		var form_param = "mail_address="+mail_address+"&smtp_server="+smtp_server+"&smtp_account="+smtp_account+"&smtp_password="+smtp_password+"&smtp_port="+smtp_port+"&smtp_auth="+smtp_auth+"&is_ssl="+is_ssl;

		$.ajax({
		  url: APP+"?"+VAR_MODULE+"=Email&"+VAR_ACTION+"=sendDemo&"+form_param,
		  cache: false,
		  success:function(data)
		  {
			alert(data);
		  }
		}); 
	}
	
</script>
<style type="text/css">
	.conf_btns{ padding:0px; margin:0px;}
	.conf_btns li{ font-size:12px; display:inline-block; width:80px; height:23px; line-height:23px; float:left;
		background:#f1f1f1; margin:1px; text-align:center; padding:2px 5px; cursor:pointer;
	}
	.conf_btns li.current{ background:#56B2DD; color:#fff;}
	.conf_tabs table.conf_table{ margin:10px auto; }
</style>
<div id="main" class="main" >
<div class="content">
<div class="title"><?php echo (L("SYSCONF")); ?></div>
<div id="result" class="result none"></div>
<form method='post' id="form" name="form" action="<?php echo u('SysConf/update');?>"  enctype="multipart/form-data">

<ul class="conf_btns">
<?php if(is_array($conf_list)): foreach($conf_list as $key=>$vo): ?><li class="<?php echo ($key); ?>"><?php echo ($key); ?></li><?php endforeach; endif; ?>
</ul>
<br clear="all" />
<div class="conf_tabs">
<?php if(is_array($conf_list)): foreach($conf_list as $key=>$vo): ?><table cellpadding=0 cellspacing=0 class="conf_table dataEdit" >
	<?php if(is_array($vo)): foreach($vo as $key=>$vo_item): ?><tr>
		<td class="tRight" width="250"><?php echo sysConfL("TITLE_".$vo_item['name']);?>：</td>
		<td class="tLeft" >
			<?php if($vo_item['list_type'] == 0): ?><!-- 手动输入 -->
				<input type="text" name="<?php echo ($vo_item["name"]); ?>" class="<?php echo ($vo_item["name"]); ?>" value="<?php echo ($vo_item["val"]); ?>" <?php if($vo_item['name'] == 'SYS_ADMIN'): ?>onblur="checkAdm(this);"<?php endif; ?> <?php if($vo_item['name'] == 'ADMIN_FILE_NAME'): ?>onblur="checkFile(this);"<?php endif; ?> /><?php endif; ?>
			<?php if($vo_item['list_type'] == 1): ?><?php if(is_array($vo_item['val_arr'])): foreach($vo_item['val_arr'] as $key=>$val_item): ?><!-- 单选 -->
				<label><?php echo sysConfL("TITLE_".$vo_item['name']."_".$val_item);?>：<input type="radio" name="<?php echo ($vo_item["name"]); ?>" class="<?php echo ($vo_item["name"]); ?>" value="<?php echo ($val_item); ?>" <?php if($vo_item['name'] == 'IS_SSL'): ?>onclick="checkSSL(this);"<?php endif; ?> <?php if($val_item == $vo_item['val']): ?>checked="checked"<?php endif; ?> /></label><?php endforeach; endif; ?><?php endif; ?>
			<?php if($vo_item['list_type'] == 2): ?><?php if($vo_item['name'] == 'DEFAULT_USER_GROUP'): ?><select name="<?php echo ($vo_item["name"]); ?>" class="<?php echo ($vo_item["name"]); ?>">
						<?php if(is_array($vo_item['val_arr'])): foreach($vo_item['val_arr'] as $key=>$val_item): ?><!-- 下拉 -->
						<option value="<?php echo ($val_item); ?>" <?php if($val_item == $vo_item['val']): ?>selected="selected"<?php endif; ?>><?php echo userGroupName($val_item);?></option><?php endforeach; endif; ?>
					</select>
				<?php else: ?>
					<select name="<?php echo ($vo_item["name"]); ?>" class="<?php echo ($vo_item["name"]); ?>">
						<?php if(is_array($vo_item['val_arr'])): foreach($vo_item['val_arr'] as $key=>$val_item): ?><!-- 下拉 -->
						<option value="<?php echo ($val_item); ?>" <?php if($val_item == $vo_item['val']): ?>selected="selected"<?php endif; ?>><?php echo sysConfL("TITLE_".$vo_item['name']."_".$val_item);?></option><?php endforeach; endif; ?>
					</select><?php endif; ?><?php endif; ?>
			<?php if($vo_item['list_type'] == 3): ?><!-- 文本域 -->
				<textarea rows="3" cols="30" name="<?php echo ($vo_item["name"]); ?>" class="<?php echo ($vo_item["name"]); ?>"><?php echo stripslashes($vo_item['val']);?></textarea><?php endif; ?>
			<?php if($vo_item['list_type'] == 5): ?><!-- 编辑器 -->
				<script type='text/javascript'>KE.show({id : '_editor',cssPath : '__TMPL__ThemeFiles/Css/style.css',skinType: 'tinymce',allowFileManager : true});</script><div  style='margin-bottom:5px;widht:100%;  '><textarea id='_editor' name='<?php echo ($vo_item["name"]); ?>' style='width:80%;height:250px;visibility:hidden;' ><?php echo ($vo_item['val']); ?></textarea> </div><?php endif; ?>
			<?php if($vo_item['list_type'] == 4): ?><!-- 图片域 -->
				<input type="file"  name="<?php echo ($vo_item["name"]); ?>" class="<?php echo ($vo_item["name"]); ?>" /> 
				<?php if($vo_item['val'] != ''): ?><a href="__ROOT__<?php echo ($vo_item["val"]); ?>" target="_blank" ><?php echo sysConfL("VIEW");?></a><?php endif; ?><?php endif; ?>
		</td>
	</tr><?php endforeach; endif; ?>
	<?php if($vo_item['group_id'] == 7): ?><tr>
	<td class="tRight" >发送测试邮件：</td>
	<td class="tLeft" >
		邮箱地址：<input type="text" name="mail_address" id="mail_address" class="bLeft" /> <input type="button" value="发送测试邮件" onclick="sendDemo();" />
	</td>
	</tr><?php endif; ?>
</table><?php endforeach; endif; ?>
</div>
<br clear="all" />
<table cellpadding=3 cellspacing=3 width=100%>
<tr>
	<td width=250>&nbsp;</td>
	<td class="center"><div style="width:85%;margin:5px">
	<input type="submit" value="<?php echo (L("SAVE_DATA")); ?>"  class="button small"> &nbsp; <input type="reset" class="button small" onclick="resetEditor()" value="<?php echo (L("RESET_DATA")); ?>" > 
	</div></td>
</tr>

</table>
</form>
</div>
</div>