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

<script type="text/javascript" src="__TMPL__ThemeFiles/Js/jquery.js"></script>
<script type="text/javascript" src="__TMPL__ThemeFiles/Js/calendar.php?lang=zh-cn" ></script>
<link rel="stylesheet" type="text/css" href="__TMPL__ThemeFiles/Js/calendar/calendar.css" />
 <script>
 function addFj(){ 
	  var oTb = document.getElementById("tb1");
	
	  var oTr = oTb.insertRow(-1);
	 
	  var num = parseInt(document.form1.fjCnt.value)+1; 
	  var no = parseInt(document.form1.fjCnt.value);
	  document.form1.fjCnt.value=num;
	  oTr.insertCell(0).innerHTML = "<span>图片：</span><input id='file' name='images[]' type=file  size='70' /> <input type=button onclick='return delFj(this)' value='删除'><br /><span>说明：</span><input maxLength=50  type='text' name='imgname[]' size=70 name=photo_narrate    /> <br /><div id=tip class=red></div><div id=preview></div>";
	  return false;
	}
	function delFj(obj,No){
		var num = parseInt(document.form1.fjCnt.value);
		var new_tr = obj.parentNode.parentNode.parentNode;
		new_tr.removeChild(obj.parentNode.parentNode);
		if (num == No){
			document.form1.fjCnt.value=num-1;
		}
		return false;
	}
 </script>

<div class="title"><?php echo (L("EDIT_DATA")); ?> [ <a href="<?php echo u($module_name.'/index');?>"><?php echo (L("BACK_LIST")); ?></a> ]</div>
<div id="result" class="result none"></div>
<form method='post' id="form1" name="form1" action="<?php echo u('Huxing/update');?>"  enctype="multipart/form-data">

<table cellpadding=0 cellspacing=0 class="dataEdit" >
<TR> 
	<TD class="tRight" width="10%">所属小区：</TD>
	<TD class="tLeft" > <div class="row_right" style="margin-top:5px;"><select  name="cate_id" id="cate_id" class='bLeftRequire' style="width:208px;" >
                  <option value="-1">选择小区楼盘</option>
                  <?php if(is_array($cate)): $i = 0; $__LIST__ = $cate;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$cate): ++$i;$mod = ($i % 2 )?><option <?php if(($vo["pid"])  ==  $cate["id"]): ?>selected<?php endif; ?>  value="<?php echo ($cate["id"]); ?>">|--&nbsp;&nbsp;<?php echo ($cate["name_1"]); ?>
                    </option><?php endforeach; endif; else: echo "" ;endif; ?> 
           </select></div>
                </TD> 
</TR> <TR>
	<TD class="tRight" width="10%">户型名称：</TD>
	<TD class="tLeft" ><input type="text" name="name_1" value="<?php echo ($vo["name_1"]); ?>"  class='bLeftRequire' /></TD>
</TR>
<tr>
	<td class="tRight" width="80">SEO关键词：</td>
	<td class="tLeft" >
		<div style='margin-bottom:5px; '><textarea name='seokeyword_1' class='bLeft' rows='2' cols='50' ><?php echo ($vo["seokeyword_1"]); ?></textarea> </div>
	</td>
</tr>
<tr>
	<td class="tRight" width="80">SEO描述：</td>
	<td class="tLeft" >
		<div style='margin-bottom:5px; '><textarea name='seocontent_1'  class='bLeft' rows='2' cols='50' ><?php echo ($vo["seocontent_1"]); ?></textarea> </div>
	</td>
</tr>

<tr>
	<td class="tRight" width="80">户型介绍：</td>

	<td class="tLeft" >
			<script type='text/javascript'>KE.show({id : 'content_1',cssPath : '__TMPL__ThemeFiles/Css/style.css',skinType: 'tinymce',allowFileManager : true});</script><div  style='margin-bottom:5px;widht:100% '><textarea id='content_1' name='content_1' style='width:650px;height:300px;visibility:hidden;' ><?php echo ($vo["content_1"]); ?></textarea> </div>

	</td>
</tr>
<tr>
	  <td class="tRight" width="120">图片列表</td>
       
	  <td colSpan=2> <?php if(is_array($photoList)): $i = 0; $__LIST__ = $photoList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$photoList): ++$i;$mod = ($i % 2 )?><div  style="float:left; margin-right:10px;"><img width="80px" height="80px" src="__PUBLIC__/upload/huxing/<?php echo ($photoList["images"]); ?>" /><p align="center"><?php echo ($photoList["img_name"]); ?>&nbsp;</p><p align="center"><a href="admin.php?m=Huxing&a=deletePic&id=<?php echo ($photoList["id"]); ?>" >删除</a></p></div>　<?php endforeach; endif; else: echo "" ;endif; ?></td>
      	  
	  </tr>
	<tr>
    <td class="tRight" width="120">小区图片：</td>
    <td colSpan=2>
	<table id="tb1" border=0 cellspacing=0 cellpadding=3 >
	 
	<tr  >
   
	<td  >
	<span>图片：</span><input id="Filedata"   name="images[]" size=70 type=file value="上 传" name="Filedata " />   <input type=button onclick='return delFj(this)' value='删除'><br><br>
	说明：<input maxLength=50  type="text" name='imgname[]' size=70 name=photo_narrate     /> 
	<div id="tip" class=red></div>
	<div id="preview"></div>
	</td>
  
	</tr> 

	</table>
    <a href="#" onclick="return addFj()">添加一张图片</a><input type="hidden" name="fjCnt" value="" />
	</td>
  </tr>&nbsp;</td>
	  </tr>

<tr>
	<td></td>
	<td class="center"><div style="width:85%;margin:5px">
	
    <INPUT TYPE="hidden" class="huge bLeftRequire" NAME="id" value="<?php echo ($vo["id"]); ?>">
	<input type="submit" value="<?php echo (L("SAVE_DATA")); ?>"  class="button small"> <input type="reset" class="button small" onclick="resetEditor()" value="<?php echo (L("RESET_DATA")); ?>" > 
	</div></td>
</tr>
</table>

</form>

</div>
</div>