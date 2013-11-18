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

<!-- 主体内容  -->
<form method='post' action="<?php echo u($module_name.'/index');?>">
<div class="content" >
<div class="title"><?php echo (L("DATA_LIST")); ?></div>
<!--  功能操作区域  -->
<div class="operate" >
<input type="button" id="" name="add" value="<?php echo (L("_ADD_DATA")); ?>" onclick="addData()" class="add imgButton">

<input type="button" id="" name="edit" value="<?php echo (L("_EDIT_DATA")); ?>" onclick="editData()" class="edit imgButton">
<input type="button" id="" name="delete" value="<?php echo (L("_DELETE_DATA")); ?>" onclick="foreverdel()" class="delete imgButton">

<!-- 查询区域 -->

<div class="fLeft"><span id="key"></span></div>

<input type="text" name="name" title="" class="medium" >
<input type="hidden" name="<?php echo c('VAR_MODULE');?>" value="Category" />
<input type="hidden" name="<?php echo c('VAR_ACTION');?>" value="index" />

<input type="hidden" name="SEARCH_TYPE" value="like" />
<input type="submit" id="" name="search" value="<?php echo (L("_SEARCH_DATA")); ?>" onclick="" class="search imgButton">


</div>
<!-- 高级查询区域 -->
<div  id="searchM" class=" none search cBoth" >
</div>
</form>
</div>
<!-- 功能操作区域结束 -->

<!-- 列表显示区域  -->
<div class="list" >
<!-- Think 系统列表组件开始 -->
<table id="checkList" class="list" cellpadding=0 cellspacing=0 ><tr><td height="5" colspan="8" class="topTd" ></td></tr><tr class="row" ><th width="8"><input type="checkbox" id="check" onclick="CheckAll('checkList')"></th><th width="8%"><a href="javascript:sortBy('id','<?php echo ($sort); ?>','index')" title="按照编号<?php echo ($sortType); ?> ">编号<?php if(($order)  ==  "id"): ?><img src="__TMPL__ThemeFiles/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('name_1','<?php echo ($sort); ?>','index')" title="按照小区楼盘名称<?php echo ($sortType); ?> ">小区楼盘名称<?php if(($order)  ==  "name_1"): ?><img src="__TMPL__ThemeFiles/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('city_id','<?php echo ($sort); ?>','index')" title="按照城市<?php echo ($sortType); ?> ">城市<?php if(($order)  ==  "city_id"): ?><img src="__TMPL__ThemeFiles/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('area_id','<?php echo ($sort); ?>','index')" title="按照地区<?php echo ($sortType); ?> ">地区<?php if(($order)  ==  "area_id"): ?><img src="__TMPL__ThemeFiles/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('create_time','<?php echo ($sort); ?>','index')" title="按照创建时间<?php echo ($sortType); ?> ">创建时间<?php if(($order)  ==  "create_time"): ?><img src="__TMPL__ThemeFiles/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_top','<?php echo ($sort); ?>','index')" title="按照置顶<?php echo ($sortType); ?> ">置顶<?php if(($order)  ==  "is_top"): ?><img src="__TMPL__ThemeFiles/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th >操作</th></tr><?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$brand): ++$i;$mod = ($i % 2 )?><tr class="row" onmouseover="over(event)" onmouseout="out(event)" onclick="change(event)" ><td><?php if(($brand['level'] == '0')or($brand['level'] > 0)): ?><?php else: ?><input type="checkbox" class="key" name="key"	value="<?php echo ($brand["id"]); ?>" <?php if($brand['checked']): ?>checked="checked"<?php endif; ?> ><?php endif; ?></td><td><?php echo ($brand["id"]); ?></td><td><a href="javascript:edit('<?php echo (addslashes($brand["id"])); ?>')"><?php echo ($brand["name_1"]); ?></a></td><td><?php echo (getPidName($brand["city_id"])); ?></td><td><?php echo (getPidName($brand["area_id"])); ?></td><td><?php echo (toDate($brand["create_time"],'Y-m-d')); ?></td><td><?php echo (showArticle($brand["is_top"])); ?></td><td> <?php echo (showArticleStatus($brand["is_top"],$brand['id'])); ?>&nbsp;<a href="javascript:edit('<?php echo ($brand["id"]); ?>')"><?php echo (L("_EDIT_DATA")); ?></a>&nbsp;<a href="javascript:foreverdel('<?php echo ($brand["id"]); ?>')"><?php echo (L("_DELETE_DATA")); ?></a>&nbsp;</td></tr><?php endforeach; endif; else: echo "" ;endif; ?><tr><td height="5" colspan="8" class="bottomTd"></td></tr></table>
<!-- Think 系统列表组件结束 -->
 
</div>


<div class="page"><?php echo ($page); ?></div>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->