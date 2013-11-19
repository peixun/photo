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
	  oTr.insertCell(0).innerHTML = "<span>图片：</span><input id='file' name='images[]' type=file   style=\"border:#CCC solid 1px; height:25px;\" /> <input type=button onclick='return delFj(this)' value='删除' class=\"log_btn11\"><br /><br /><span>说明：<input maxLength=50  type='text' name='imgname[]' name=photo_narrate   class=\"bg_txt\"  /><br /><br /><div id=tip class=red></div><div id=preview></div>";
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
	 
	  var num = parseInt(document.regForm.fjCnts.value)+1; 
	  var no = parseInt(document.regForm.fjCnts.value);
	  document.regForm.fjCnts.value=num;
	  oTr.insertCell(0).innerHTML = "<span>qq号码：<input  type=\"text\" name='qq[]'  class=\"bg_txt\"   /> <input type=button onclick='return delFjs(this)' value='删除' class=\"log_btn11\"> <br /></span>";
      count ++;
	  return false;
	}else
	{
		alert("最多只能上传8张图片!");
		}
	}
	function delFjs(obj,No){
		var num = parseInt(document.regForm.fjCnts.value);
		var new_tr = obj.parentNode.parentNode.parentNode;
		new_tr.removeChild(obj.parentNode.parentNode);
		if (num == No){
			document.regForm.fjCnts.value=num-1;
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
<script type="text/javascript">

function stopSends(name){
	alert(name);
	document.getElementById('cover_image').value = name;
	$("#blog_img").attr("src",PUBLIC+"/upload/logo/thum/"+name);
}

</script>



<div id="main" class="main" >
<div class="content">
<div class="title"><?php echo (L("EDIT_DATA")); ?> [ <a href="<?php echo u($module_name.'/index');?>"><?php echo (L("BACK_LIST")); ?></a> ]</div>
<div id="result" class="result none"></div>
<form method='post' id="regForm" name="form" action="<?php echo u('Company/update');?>"  enctype="multipart/form-data">
<table cellpadding="0" cellspacing="0" width=100% class="dataEdit">
	
		<tr>
			<td class="tRight" style="width:115px;">公司名称：</td>
			<td class="tLeft" >
				<?php echo ($vo["company_name"]); ?>
			</td>
		</tr>
        

	<td class="tRight">企业logo：</td>
	<td class="tLeft" >
		<input type="file" name="logo"   style="border:#CCC solid 1px; height:24px;" /> <img src="__ROOT__/Public/upload/logo/thum/<?php echo ($vo["logo"]); ?>" width="630" height="60"> <?php if($vo['logo'] != ''): ?><a href="__ROOT__/Public/upload/logo/thum/<?php echo ($vo["logo"]); ?>" target="_blank"><?php echo L("VIEW");?></a><?php endif; ?>
</td>
</tr>		
 <tr>
					<td width="100" align="left">服务区域：</td>
					<td ><input type="text" name="service_area" class="bLeft" value="<?php echo ($vo["service_area"]); ?>" />例如 卢湾 徐汇 长宁 静安 普陀 闸北 等等</td>
				</tr>
                 <tr>
					<td width="100" align="left">业务范围：</td>
					<td ><input type="text" name="business_scope" class="bLeft" value="<?php echo ($vo["business_scope"]); ?>" />例如 新房装修 商铺装修 工装</td>
				</tr>
                 <tr>
					<td width="100" align="left">主打价位：</td>
					<td ><select name="main_price"  style="width:100px;" class="bLeft" />
                  <option  value='-1'>选择主打价位</option> 
                  
                  <option  value='1' <?php if(($vo["main_price"])  ==  "1"): ?>selected<?php endif; ?>> 8万以下</option>     
                  <option  value='2' <?php if(($vo["main_price"])  ==  "2"): ?>selected<?php endif; ?>> 8-15万</option>     
                  <option  value='3' <?php if(($vo["main_price"])  ==  "3"): ?>selected<?php endif; ?>> 15-30万</option>     
                  <option  value='4' <?php if(($vo["main_price"])  ==  "4"): ?>selected<?php endif; ?>> 30万-100万</option>     
                  <option  value='5' <?php if(($vo["main_price"])  ==  "5"): ?>selected<?php endif; ?>> 100万以上</option>     
                    
                </select>
                   </td>
				</tr>
			 	<tr>
					<td width="100" align="left">擅长风格：</td>
					<td ><input type="text" name="good_style"  class="bLeft"    value="<?php echo ($vo["good_style"]); ?>" /></td>
				</tr>
                <tr>
					<td width="100" align="left">装修模式：</td>
					<td ><input type="text" name="decoration_pattern"  class="bLeft"   value="<?php echo ($vo["decoration_pattern"]); ?>" />例如 半包 全包</td>
				</tr>
                <tr>
					<td width="100" align="left">总店地址：</td>
					<td ><input type="text" name="address" class="bLeft"  value="<?php echo ($vo["address"]); ?>" /></td>
				</tr>
<tr>
			<td class="tRight" style="width:115px;">企业简介：</td>
			<td class="tLeft" >
				<textarea name='desc' id='' class='bLeft' rows='' cols='' style='width:650px;height:200px;' >“同济经典设计”多年来坚持“以人为本的理念，以满足客户需求为目标，以个性化设计为核心，以高品质施工工艺为基础，以6S金钻工程为保障”的服务理念，秉承“弘扬海派设计风格，引领装修流行趋势”的宗旨，始终站在国际高端家居设计的前沿，在设计中融入个性表达，引导高品位生活方式，提供从装前咨询到室内空间设计、软装搭配、园林、设备、智能家居、施工、装后服务的全程优质服务。以至高品质、至尊品位的追求引领着时尚、创新、个性化的品质生活方式，让上海广大客户真正享受到至尊生活的家居环境。同时定期举办装饰知识专家讲座，引导品质装修装饰世界领先潮流。作为上海十大家装品牌企业、上海家装设计领军企业，“同济经典设计”在上海乃至整个中国的装饰装修行业中具有一定的影响力。“同济经典设计”将最大限度的发挥创意与技术力量，以先进的设计理念、精致典雅的设计作品，安全环保施工工艺、完善周到的装后服务，为客户缔造人性、个性和谐统一的品质生活空间。</textarea>
			</td>
		</tr>
		<tr>
			<td class="tRight" >企业介绍：</td>
			<td class="tLeft" >
            <script type='text/javascript'>KE.show({id : 'content',cssPath : '__TMPL__ThemeFiles/Css/style.css',skinType: 'tinymce',allowFileManager : true});</script><div  style='margin-bottom:5px;widht:100%;  '><textarea id='content' name='content' style='width:700px;height:400px;visibility:hidden;' ></textarea> </div>
			</td>
		</tr>
	
		</td>
		<td valign="top" style="padding-left:50px;">
		<!-- 图片上传块 -->
		</td>
	</tr>
</table>
<table cellpadding=0 cellspacing=0 class="dataEdit" >
<tr>
	<td></td>
	<td class="center"><div style="width:85%;margin:5px">
	<input name="id" id="id" value="<?php echo ($vo["id"]); ?>" type="hidden" />
	<input type="submit" value="<?php echo (L("SAVE_DATA")); ?>"  class="button small"> <input type="reset" class="button small" onclick="resetEditor()" value="<?php echo (L("RESET_DATA")); ?>" > 
	</div></td>
</tr>

</table>
</form>
</div>