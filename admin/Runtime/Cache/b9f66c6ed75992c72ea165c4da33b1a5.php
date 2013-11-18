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
<script type="text/javascript" src="__TMPL__ThemeFiles/Js/calendar.php?lang=zh-cn" ></script>
<link rel="stylesheet" type="text/css" href="__TMPL__ThemeFiles/Js/calendar/calendar.css" />

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
<!--<input type="button" id="" name="add" value="<?php echo (L("_ADD_DATA")); ?>" onclick="addData()" class="add imgButton">-->
<input type="button" id="" name="edit" value="<?php echo (L("_EDIT_DATA")); ?>" onclick="editData()" class="edit imgButton">
<input type="button" id="" name="delete" value="<?php echo (L("_DELETE_DATA")); ?>" onclick="foreverdel()" class="delete imgButton">
<!-- 查询区域 -->


</div>
<div style="clear:both"></div>
<script>
function caseshow(sid)
      {
        var subitem=document.getElementById("caselist"+sid);
		var subitem1=document.getElementById("caselist1"+sid);

      //subitem.style.display=subitem.style.display=='none'?'':'none';
      if(subitem.style.display=='none'){
        	subitem.style.display='';
			subitem1.style.display='none';

       }
       else{
       subitem.style.display='none';
	   subitem1.style.display='';

      }
     }
</script>
<Div class="clear"></Div>
<div style="padding-top:10px"><a href="javascript:void(0);" onclick="caseshow(1)"><img src="__PUBLIC__/images/daochu.gif" /></a></div>
<div style="display:none;" id="caselist1">
			<p>	报名类型: <select name="type" class="bLeft">
			<option value="">请选择类型</option>
			<option value="1">免费量房</option>
            <option value="2">免费预算</option>
            <option value="3">免费设计</option>
            <option value="4">免费咨询</option>
            <option value="5">活动</option>				
		</select>
        </p>
        <p>
		公司名称: <select name="company_id" class="bLeft">
			<option value="0">本站</option>
			<?php if(is_array($company)): $i = 0; $__LIST__ = $company;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$company): ++$i;$mod = ($i % 2 )?><option value="<?php echo ($company["id"]); ?>"><?php echo ($company["company_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>		
		</select></p>
<p>
		开始时间:  <INPUT TYPE="text"  name="promote_begin_time" id="promote_begin_time"  class="bLeft" value="<?php echo ($default_begin_time); ?>">
				<input type="button" name="btn_promote_begin_time" id="btn_promote_begin_time" onclick="return showCalendar('promote_begin_time', '%Y-%m-%d %H:%M', false, false, 'btn_promote_begin_time');" value="<?php echo (L("SELECT")); ?>" class="button"/></p>

 <p> 	   结束时间:  <INPUT TYPE="text"  name="promote_end_time" id="promote_end_time"  class="bLeft" value="<?php echo ($default_end_time); ?>">
				<input type="button" name="btn_promote_end_time" id="btn_promote_end_time" onclick="return showCalendar('promote_end_time', '%Y-%m-%d %H:%M', false, false, 'btn_promote_end_time');" value="<?php echo (L("SELECT")); ?>" class="button"/></p>
<p>			
<input type="hidden" name="m" value="Booking" />
<input type="hidden" name="a" value="expbookingcsv" /><br />
<input type="image" src="__PUBLIC__/images/daochu.gif"  style="border:0"/>
</p>


</div>
<!-- 高级查询区域 -->

</form>

<!-- 功能操作区域结束 -->

<!-- 列表显示区域  -->
<div class="list" >
<!-- Think 系统列表组件开始 -->
<table id="checkList" class="list" cellpadding=0 cellspacing=0 ><tr><td height="5" colspan="9" class="topTd" ></td></tr><tr class="row" ><th width="8"><input type="checkbox" id="check" onclick="CheckAll('checkList')"></th><th width="8%"><a href="javascript:sortBy('id','<?php echo ($sort); ?>','index')" title="按照<?php echo (L("ID")); ?><?php echo ($sortType); ?> "><?php echo (L("ID")); ?><?php if(($order)  ==  "id"): ?><img src="__TMPL__ThemeFiles/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('mobile','<?php echo ($sort); ?>','index')" title="按照手机号码<?php echo ($sortType); ?> ">手机号码<?php if(($order)  ==  "mobile"): ?><img src="__TMPL__ThemeFiles/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('user_name','<?php echo ($sort); ?>','index')" title="按照昵称<?php echo ($sortType); ?> ">昵称<?php if(($order)  ==  "user_name"): ?><img src="__TMPL__ThemeFiles/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('email','<?php echo ($sort); ?>','index')" title="按照<?php echo (L("EMAIL")); ?><?php echo ($sortType); ?> "><?php echo (L("EMAIL")); ?><?php if(($order)  ==  "email"): ?><img src="__TMPL__ThemeFiles/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('create_time','<?php echo ($sort); ?>','index')" title="按照报名时间<?php echo ($sortType); ?> ">报名时间<?php if(($order)  ==  "create_time"): ?><img src="__TMPL__ThemeFiles/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('type','<?php echo ($sort); ?>','index')" title="按照报名类型<?php echo ($sortType); ?> ">报名类型<?php if(($order)  ==  "type"): ?><img src="__TMPL__ThemeFiles/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('company_id','<?php echo ($sort); ?>','index')" title="按照报名公司<?php echo ($sortType); ?> ">报名公司<?php if(($order)  ==  "company_id"): ?><img src="__TMPL__ThemeFiles/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th >操作</th></tr><?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$user): ++$i;$mod = ($i % 2 )?><tr class="row" onmouseover="over(event)" onmouseout="out(event)" onclick="change(event)" ><td><?php if(($user['level'] == '0')or($user['level'] > 0)): ?><?php else: ?><input type="checkbox" class="key" name="key"	value="<?php echo ($user["id"]); ?>" <?php if($user['checked']): ?>checked="checked"<?php endif; ?> ><?php endif; ?></td><td><?php echo ($user["id"]); ?></td><td><?php echo ($user["mobile"]); ?></td><td><?php echo ($user["user_name"]); ?></td><td><a href="javascript:edit('<?php echo (addslashes($user["id"])); ?>')"><?php echo ($user["email"]); ?></a></td><td><?php echo (toDate($user["create_time"])); ?></td><td><?php echo (getTypes($user["type"])); ?></td><td><?php echo (getCompanys($user["company_id"])); ?></td><td><a href="javascript:edit('<?php echo ($user["id"]); ?>')"><?php echo (L("_EDIT_DATA")); ?></a>&nbsp;<a href="javascript:userforeverdel('<?php echo ($user["id"]); ?>')"><?php echo (L("_DELETE_DATA")); ?></a>&nbsp;</td></tr><?php endforeach; endif; else: echo "" ;endif; ?><tr><td height="5" colspan="9" class="bottomTd"></td></tr></table>
<!-- Think 系统列表组件结束 -->
 
</div>
<!--  分页显示区域 -->
<div class="page"><?php echo ($page); ?></div>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->