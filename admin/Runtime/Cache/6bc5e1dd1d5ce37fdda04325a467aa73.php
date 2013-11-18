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
<div class="title">新增数据 [ <a href="<?php echo u($module_name.'/index');?>"><?php echo (L("BACK_LIST")); ?></a> ]</div>
<div id="result" class="result none"></div>
  
<script type="text/javascript" src="__PUBLIC__/js/jquery-1.3.2.min.js"></script>
   <script type="text/javascript">
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
  <script type="text/javascript">
     
            $(document).ready(function() {
	$("#cate_pid").change(function(){
				var id = $(this).val();
				if(id != ''){
						$.post("admin.php?m=Case&a=get_huxing_name", {id:id}, function(res){
									
									$("#cate_id").html(res);
							});
					}
		});
	$("#com_id").change(function(){
				var id = $(this).val();
				if(id != ''){
						$.post("admin.php?m=Case&a=get_desinger", {id:id}, function(res){
									
									$("#desinger_id").html(res);
							});
					}
		});

}); 

</script>
 
<form method='post' id="form1" name="form1" action="<?php echo u('Case/insert');?>"  enctype="multipart/form-data">
<table cellpadding=0 cellspacing=0 class="dataEdit" >
<tr>
	<td class="tRight" width="120">案例名称：</td>
	<td class="tLeft" >
    
	<div  style='margin-bottom:5px; '><input type='text' name='name_1' id='_1' class='bLeftRequire' value='' /> </div>
	</td>
</tr>
   <tr>
        
	<td class="tRight">选择社区：</td>
	<td class="tLeft" >
		<select   name="cate_pid" id="cate_pid" class="bLeftRequire">
		<option value="0">请选择社区</option>
		<?php if(is_array($cate)): foreach($cate as $key=>$cate_item): ?><option value="<?php echo ($cate_item["id"]); ?>"><?php echo ($cate_item[$select_dispname]); ?></option><?php endforeach; endif; ?>
		</select>
         <select name="cate_id" id="cate_id" class="bLeftRequire">
                  <option  value='-1'>选择户型</option>            
                </select> 
	</td>
</tr>

<tr>
        
	<td class="tRight">选择公司：</td>
	<td class="tLeft" >
		<select name="uid"  id="com_id" class="bLeftRequire">
		<option value="0">请选择公司</option>
		<?php if(is_array($user_list)): foreach($user_list as $key=>$user_list): ?><option value="<?php echo ($user_list["id"]); ?>"><?php echo (getCompany($user_list["id"])); ?></option><?php endforeach; endif; ?>
		</select>
         <select name="desinger_id" id="desinger_id" class="bLeftRequire">
                  <option  value='-1'>选择设计师</option>            
                </select> 
	</td>
</tr>
<tr>
	<td class="tRight" width="120">预算价格：</td>
	<td class="tLeft" >
    
	<input type='text' name='budget' id='' class='bLeft' value=''  />
	</td>
</tr>
<tr>
	<td class="tRight" width="120">面积：</td>
	<td class="tLeft" >
    
	<input type='text' name='area' id='' class='bLeft' value=''  />
	</td>
</tr>
<tr>
	<td class="tRight" width="120">风格：</td>
	<td class="tLeft" >
    
	<input type='text' name='styles' id='' class='bLeft' value=''  />
	</td>
</tr>
<tr>
	<td class="tRight" width="120">案例描述：</td>
	<td class="tLeft" >	<script type='text/javascript'>KE.show({id : 'content_1',cssPath : '__TMPL__ThemeFiles/Css/style.css',skinType: 'tinymce',allowFileManager : true});</script><div  style='margin-bottom:5px;widht:100% '><textarea id='content_1' name='content_1' style='width:700px;height:350px;visibility:hidden;' ></textarea> </div>
	</td>
</tr><tr>
    <td class="tRight" width="120">案例图片：</td>
    <td colSpan=2>
	<table id="tb1" border=0 cellspacing=0 cellpadding=3 >
	
	<tr>
	<td>
	<span>图片：</span><input id="Filedata"  name="images[]" size=70 type=file value="上 传" name="Filedata " />   <input type=button onclick='return delFj(this)' value='删除'><br />
	说明：<input maxLength=50  type="text" name='imgname[]' size=70 name=photo_narrate    /> 
	<div id="tip" class=red></div>
	<div id="preview"></div>
	</td>
	</tr> 
	
	</table>
    <a href="#" onclick="return addFj()">添加一张图片</a><input type="hidden" name="fjCnt" value="" />
	</td>
  </tr>&nbsp;</td>
	  </tr>
	<td></td>
	  <td class="center"><div style="width:85%;margin:5px"><input type="hidden" name="status"  value="1" />
	    <input type="submit" value="<?php echo (L("SAVE_DATA")); ?>"  class="button small"> <input type="reset" class="button small" onclick="resetEditor()" value="<?php echo (L("RESET_DATA")); ?>" >
	    </div></td>
</tr>

</table>
</form>

</div>
</div>