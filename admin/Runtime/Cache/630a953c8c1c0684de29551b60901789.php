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
<div class="content" >
<div class="title"><?php echo (L("DATA_LIST")); ?></div>
<!--  功能操作区域  -->
<form method='get' action="<?php echo u($module_name.'/index');?>">
<div class="operate" >
<input type="button" id="" name="add" value="<?php echo (L("_ADD_DATA")); ?>" onclick="addData()" class="add imgButton">
<input type="button" id="" name="edit" value="<?php echo (L("_EDIT_DATA")); ?>" onclick="editData()" class="edit imgButton">
<input type="button" id="" name="delete" value="<?php echo (L("_DELETE_DATA")); ?>" onclick="foreverdel()" class="delete imgButton">

<!-- 查询区域 -->



<?php echo L("_SEARCH_DATA");?>：
<select name="cate_id">
				<option value="0"><?php echo (L("NO_CATE_SELECT")); ?></option>
				<?php if(is_array($cate_list)): foreach($cate_list as $key=>$cate_item): ?><option value="<?php echo ($cate_item["id"]); ?>" <?php if($cate_id == $cate_item['id']): ?>selected="selected"<?php endif; ?>><?php echo ($cate_item[$select_dispname]); ?></option><?php endforeach; endif; ?>
</select>
<input type="text" name="name" title="<?php echo (L("ARTICLE_NAME")); ?>" class="medium" >
<input type="submit" id="" name="search" value="<?php echo (L("_SEARCH_DATA")); ?>" onclick="" class="search imgButton">
</div>
<!-- 高级查询区域 -->

<input type="hidden" name="status" value="1" />
<input type="hidden" name="SEARCH_TYPE" value="like" />

<input type="hidden" name="<?php echo c('VAR_MODULE');?>" value="News" />
<input type="hidden" name="<?php echo c('VAR_ACTION');?>" value="index" />
</form>

<!-- 功能操作区域结束 -->

<!-- 列表显示区域  -->
<div class="list" >
<!-- Think 系统列表组件开始 -->
<table id="checkList" class="list" cellpadding=0 cellspacing=0 ><tr><td height="5" colspan="10" class="topTd" ></td></tr><tr class="row" ><th width="8"><input type="checkbox" id="check" onclick="CheckAll('checkList')"></th><th width="8%"><a href="javascript:sortBy('id','<?php echo ($sort); ?>','index')" title="按照<?php echo (L("ID")); ?><?php echo ($sortType); ?> "><?php echo (L("ID")); ?><?php if(($order)  ==  "id"): ?><img src="__TMPL__ThemeFiles/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('name_1','<?php echo ($sort); ?>','index')" title="按照<?php echo (L("ARTICLE_NAME")); ?><?php echo ($sortType); ?> "><?php echo (L("ARTICLE_NAME")); ?><?php if(($order)  ==  "name_1"): ?><img src="__TMPL__ThemeFiles/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('cate_id','<?php echo ($sort); ?>','index')" title="按照资讯分类<?php echo ($sortType); ?> ">资讯分类<?php if(($order)  ==  "cate_id"): ?><img src="__TMPL__ThemeFiles/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('update_time','<?php echo ($sort); ?>','index')" title="按照<?php echo (L("UPDATE_TIME")); ?><?php echo ($sortType); ?> "><?php echo (L("UPDATE_TIME")); ?><?php if(($order)  ==  "update_time"): ?><img src="__TMPL__ThemeFiles/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('click_count','<?php echo ($sort); ?>','index')" title="按照<?php echo (L("CLICK_COUNT")); ?><?php echo ($sortType); ?> "><?php echo (L("CLICK_COUNT")); ?><?php if(($order)  ==  "click_count"): ?><img src="__TMPL__ThemeFiles/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('status','<?php echo ($sort); ?>','index')" title="按照审核<?php echo ($sortType); ?> ">审核<?php if(($order)  ==  "status"): ?><img src="__TMPL__ThemeFiles/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_recommend','<?php echo ($sort); ?>','index')" title="按照推荐<?php echo ($sortType); ?> ">推荐<?php if(($order)  ==  "is_recommend"): ?><img src="__TMPL__ThemeFiles/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('is_top','<?php echo ($sort); ?>','index')" title="按照置顶<?php echo ($sortType); ?> ">置顶<?php if(($order)  ==  "is_top"): ?><img src="__TMPL__ThemeFiles/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th >操作</th></tr><?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$Article): ++$i;$mod = ($i % 2 )?><tr class="row" onmouseover="over(event)" onmouseout="out(event)" onclick="change(event)" ><td><?php if(($Article['level'] == '0')or($Article['level'] > 0)): ?><?php else: ?><input type="checkbox" class="key" name="key"	value="<?php echo ($Article["id"]); ?>" <?php if($Article['checked']): ?>checked="checked"<?php endif; ?> ><?php endif; ?></td><td><?php echo ($Article["id"]); ?></td><td><a href="javascript:edit('<?php echo (addslashes($Article["id"])); ?>')"><?php echo ($Article["name_1"]); ?></a></td><td><?php echo (getArticleCate($Article["cate_id"])); ?></td><td><?php echo (toDate($Article["update_time"])); ?></td><td><?php echo ($Article["click_count"]); ?></td><td><?php echo (getStatus($Article["status"])); ?></td><td><?php echo (showArticle($Article["is_recommend"])); ?></td><td><?php echo (showArticle($Article["is_top"])); ?></td><td> <?php echo (showArticleStatus($Article["is_top"],$Article['id'])); ?>&nbsp; <?php echo (showRStatus($Article["is_recommend"],$Article['id'])); ?>&nbsp; <?php echo (showStatus($Article["status"],$Article['id'])); ?>&nbsp;<a href="javascript:edit('<?php echo ($Article["id"]); ?>')"><?php echo (L("_EDIT_DATA")); ?></a>&nbsp;<a href="javascript:foreverdel('<?php echo ($Article["id"]); ?>')"><?php echo (L("_DELETE_DATA")); ?></a>&nbsp;</td></tr><?php endforeach; endif; else: echo "" ;endif; ?><tr><td height="5" colspan="10" class="bottomTd"></td></tr></table>
<!-- Think 系统列表组件结束 -->
 
</div>
<!--  分页显示区域 -->
<div class="page"><?php echo ($page); ?></div>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->