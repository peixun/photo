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
<script type="text/javascript" src="__TMPL__ThemeFiles/Js/jquery.js"></script>
<script type="text/javascript" src="__TMPL__ThemeFiles/Js/jquery.json.js"></script>
<script type="text/javascript" src="__TMPL__ThemeFiles/Js/model.js"></script>
<script type="text/javascript" src="__TMPL__ThemeFiles/Js/calendar.php?lang=zh-cn" ></script>
<link rel="stylesheet" type="text/css" href="__TMPL__ThemeFiles/Js/calendar/calendar.css" />
<script type="text/javascript" src="__TMPL__ThemeFiles/Js/swfupload/swfupload.js"></script>
<script type="text/javascript" src="__TMPL__ThemeFiles/Js/swfupload/goodsupload/swfupload.queue.js"></script>
<script type="text/javascript" src="__TMPL__ThemeFiles/Js/swfupload/goodsupload/fileprogress.js"></script>
<script type="text/javascript" src="__TMPL__ThemeFiles/Js/swfupload/goodsupload/handlers.js"></script>
<script type="text/javascript" src="__TMPL__ThemeFiles/Js/goods.js"></script>
<link rel="stylesheet" type="text/css" href="__TMPL__ThemeFiles/Css/jqModal.css" />
<script type="text/javascript" src="__TMPL__ThemeFiles/Js/jqModal.js"></script>

<script type="text/javascript">
var session_id = '<?php echo ($session_id); ?>';
var default_lang_id = '<?php echo ($default_lang_id); ?>';
var lang_ids = '<?php echo ($lang_ids); ?>';
var lang_names = '<?php echo ($lang_names); ?>';
var ATTR_TIPS = '<?php echo L("ATTR_TIPS");?>';
var EDIT_SUCCESS = '<?php echo L("EDIT_SUCCESS");?>';
var SELECT_SPEC_TYPE = '==<?php echo L("SELECT_SPEC_TYPE");?>==';

var GOODS_SPEC_ITEM_EXIST = '<?php echo L("GOODS_SPEC_ITEM_EXIST");?>';
var EXIST_SAME_SPEC = '<?php echo L("EXIST_SAME_SPEC");?>';
var CLOSE = '<?php echo L("CLOSE");?>';


var DEFAULT_LANG_ID = '<?php echo ($DEFAULT_LANG_ID); ?>';var lang_ids = '<?php echo ($lang_ids); ?>';
var lang_names = '<?php echo ($lang_names); ?>';
var DIY_URL = '<?php echo L("DIY_URL");?>';
</script>

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
	  oTr.insertCell(0).innerHTML = "<span>图片：</span><input id='file' name='images[]' type=file   style=\"border:#CCC solid 1px; height:25px;\" /> <input type=button onclick='return delFj(this)' value='删除' class=\"button small\"><br /><br /><span>说明：<input maxLength=50  type='text' name='imgname[]' name=photo_narrate   class=\"bLeft\"  /><br /><br /><div id=tip class=red></div><div id=preview></div>";
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
	 
	  var num = parseInt(document.qqfrom.fjCnts.value)+1; 
	  var no = parseInt(document.qqfrom.fjCnts.value);
	  document.qqfrom.fjCnts.value=num;
	  oTr.insertCell(0).innerHTML = "<span>qq号码：<input  type=\"text\" name='qq[]'  class=\"bLeft\"  style=\"line-height:20px;height:20px;\"   /> <input type=button onclick='return delFjs(this)' value='删除' class=\"button small\"> </span>";
      count ++;
	  return false;
	}else
	{
		alert("最多只能上传8张图片!");
		}
	}
	function delFjs(obj,No){
		var num = parseInt(document.qqfrom.fjCnts.value);
		var new_tr = obj.parentNode.parentNode.parentNode;
		new_tr.removeChild(obj.parentNode.parentNode);
		if (num == No){
			document.qqfrom.fjCnts.value=num-1;
		}
		count --;
		return false;
	}
	

  </script>


<SCRIPT LANGUAGE="JavaScript">
<!--
//指定当前组模块URL地址 
var PUBLIC = '__PUBLIC__';
//-->
</SCRIPT>


<div id="main" class="main" >
<div class="content">
<div class="title">添加在线QQ[ <a href="<?php echo u($module_name.'/index');?>"><?php echo (L("BACK_LIST")); ?></a> ]</div>
<div id="result" class="result none"></div>
 <p class="title_show" style="margin-left:30px; font-size:14px; font-weight:800;">在线QQ列表</p> 
  <table border=0 cellspacing=0  width="400">
  <?php if(is_array($qq)): $i = 0; $__LIST__ = $qq;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><tr>
   	<td width="30"></td>
    <td align="left" style="width:200px;"><?php echo ($vo["qq"]); ?></td>
     <td><a href="<?php echo u('Company/delqq',array('id'=>$vo['id']));?>">删除</a></td>
  </tr><?php endforeach; endif; else: echo "" ;endif; ?>
  </table>
 
  <form name="qqfrom" id="qqfrom" action="<?php echo u('Company/insertQQ');?>"  method="post">

<table id="tb2" border=0 cellspacing=0 cellpadding=3 >
	 
	<tr> 
	<td>
	qq号码：<input  type="text" name='qq[]' style="line-height:20px;height:20px;"  class="bLeft"    /> 
	</td>
  
	</tr> 

	</table>
    <p><input type="button" onclick="return addFjs()" class="button small" value="添加qq"  /><input type="hidden" name="fjCnts" value="" /></p> <input type="hidden" value="<?php echo ($id); ?>" name="id" />
    <input type="submit" value="<?php echo (L("SAVE_DATA")); ?>"  class="button small"> </p>
</form></div>