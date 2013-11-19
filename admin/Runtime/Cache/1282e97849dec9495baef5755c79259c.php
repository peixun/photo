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
<!-- 菜单区域  -->

<!-- 主页面开始 -->
<div id="main" class="main" >
<script type="text/javascript">
	function showdetail(id){
		location.href = APP+'?m=User&a=showdetail&user_id='+id;
	}	
	function export_user()
	{
		var user_name = document.getElementById("c_user_name").value;
		var email = document.getElementById("c_email").value;
		var city_id = document.getElementById("c_city_id").value;
		var mobile_phone = document.getElementById("c_mobile_phone").value;
		var url = APP+'?m=User&a=expusercsv&user_name='+user_name+'&email='+email+'&city_id='+city_id+'&mobile_phone='+mobile_phone;
		
		location.href = url;
	}
</script>
<!-- 主体内容  -->
<div class="content" >
<div class="title"><?php echo (L("DATA_LIST")); ?></div>
<!--  功能操作区域  -->
<form method='get' action="__APP__">
<div class="operate" style="float:left" >
<input type="button" id="" name="add" value="<?php echo (L("_ADD_DATA")); ?>" onclick="addData()" class="add imgButton">
<input type="button" id="" name="edit" value="<?php echo (L("_EDIT_DATA")); ?>" onclick="editData()" class="edit imgButton">
<input type="button" id="" name="delete" value="<?php echo (L("_DELETE_DATA")); ?>" onclick="foreverdel()" class="delete imgButton">
<!-- 查询区域 -->


</div>
<div class="operate">
				昵称: <input type="text" name="user_name" title="<?php echo (L("USER_NAME")); ?>" class="medium" id="c_user_name" value="<?php echo trim($_REQUEST['nickname']);?>" style="width:90px;" >  
				邮件: <input type="text" name="email" class="medium"  id="c_email" value="<?php echo trim($_REQUEST['email']);?>"  style="width:90px;" />
				手机: <input type="text" name="mobile" class="medium"  id="c_mobile_phone" value="<?php echo trim($_REQUEST['mobile_phone']);?>"  style="width:90px;" />
				
			
			
<input type="hidden" name="m" value="User" />
<input type="hidden" name="a" value="index" />
<input type="hidden" name="SEARCH_TYPE" value="like" />
<input type="submit" id="" name="search" value="<?php echo (L("_SEARCH_DATA")); ?>" onclick="" class="search imgButton">


</div>
<!-- 高级查询区域 -->

</form>

<!-- 功能操作区域结束 -->

<!-- 列表显示区域  -->
<div class="list" >
<!-- Think 系统列表组件开始 -->
<table id="checkList" class="list" cellpadding=0 cellspacing=0 ><tr><td height="5" colspan="8" class="topTd" ></td></tr><tr class="row" ><th width="8"><input type="checkbox" id="check" onclick="CheckAll('checkList')"></th><th width="8%"><a href="javascript:sortBy('id','<?php echo ($sort); ?>','index')" title="按照<?php echo (L("ID")); ?><?php echo ($sortType); ?> "><?php echo (L("ID")); ?><?php if(($order)  ==  "id"): ?><img src="__TMPL__ThemeFiles/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('mobile','<?php echo ($sort); ?>','index')" title="按照手机号码<?php echo ($sortType); ?> ">手机号码<?php if(($order)  ==  "mobile"): ?><img src="__TMPL__ThemeFiles/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_name','<?php echo ($sort); ?>','index')" title="按照昵称<?php echo ($sortType); ?> ">昵称<?php if(($order)  ==  "user_name"): ?><img src="__TMPL__ThemeFiles/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('email','<?php echo ($sort); ?>','index')" title="按照<?php echo (L("EMAIL")); ?><?php echo ($sortType); ?> "><?php echo (L("EMAIL")); ?><?php if(($order)  ==  "email"): ?><img src="__TMPL__ThemeFiles/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('create_time','<?php echo ($sort); ?>','index')" title="按照<?php echo (L("REG_TIME")); ?><?php echo ($sortType); ?> "><?php echo (L("REG_TIME")); ?><?php if(($order)  ==  "create_time"): ?><img src="__TMPL__ThemeFiles/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('status','<?php echo ($sort); ?>','index')" title="按照状态<?php echo ($sortType); ?> ">状态<?php if(($order)  ==  "status"): ?><img src="__TMPL__ThemeFiles/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th >操作</th></tr><?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$user): ++$i;$mod = ($i % 2 )?><tr class="row" onmouseover="over(event)" onmouseout="out(event)" onclick="change(event)" ><td><?php if(($user['level'] == '0')or($user['level'] > 0)): ?><?php else: ?><input type="checkbox" class="key" name="key"	value="<?php echo ($user["id"]); ?>" <?php if($user['checked']): ?>checked="checked"<?php endif; ?> ><?php endif; ?></td><td><?php echo ($user["id"]); ?></td><td><a href="javascript:edit('<?php echo (addslashes($user["id"])); ?>')"><?php echo ($user["mobile"]); ?></a></td><td><?php echo ($user["user_name"]); ?></td><td><a href="javascript:edit('<?php echo (addslashes($user["id"])); ?>')"><?php echo ($user["email"]); ?></a></td><td><?php echo (toDate($user["create_time"])); ?></td><td><?php echo (getStatus($user["status"])); ?></td><td> <?php echo (showStatus($user["status"],$user['id'])); ?>&nbsp;<a href="javascript:edit('<?php echo ($user["id"]); ?>')"><?php echo (L("_EDIT_DATA")); ?></a>&nbsp;<a href="javascript:userforeverdel('<?php echo ($user["id"]); ?>')"><?php echo (L("_DELETE_DATA")); ?></a>&nbsp;</td></tr><?php endforeach; endif; else: echo "" ;endif; ?><tr><td height="5" colspan="8" class="bottomTd"></td></tr></table>
<!-- Think 系统列表组件结束 -->
 
</div>
<!--  分页显示区域 -->
<div class="page"><?php echo ($page); ?></div>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->