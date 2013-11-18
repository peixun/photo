<?php
	if (!defined('THINK_PATH')) exit();
	if(file_exists('./config/db_config.php'))
	$db_config	=	require './config/db_config.php';
	$sys_config = require './config.inc.php';
	$global_config = require './config/global_config.php';
    //echo BASE_PATH;
	function getConfVal($keys)
	{
		if(file_exists('./config/db_config.php'))
		$db_config	=	require './config/db_config.php';
		$link = @mysql_connect($db_config['DB_HOST'].":".$db_config['DB_PORT'], $db_config['DB_USER'], $db_config['DB_PWD']);
		@mysql_select_db($db_config['DB_NAME'],$link);
		$sql = "SELECT name,val FROM ".$db_config['DB_PREFIX']."sys_conf WHERE status=1 and name in(".$keys.")";
		$result = mysql_query($sql);
		$res = array();
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
	       $res[$row[0]] = $row[1];
	    }
		mysql_close($link);
	    return $res;
	}

	$ext_config = S("EXT_CONFIG");
	if($ext_config===false)
	{
		if(is_array($db_config))
		{
			$ext_config = getConfVal("'URL_MODEL','PAGE_LISTROWS','TOKEN_ON'");
			S("EXT_CONFIG",$ext_config);
		}
	}
	$array=array(
		'TOKEN_NAME'=>'_eyoo_hash__',    // 令牌验证的表单隐藏字段名称
        'URL_MODEL'=>1,
        //'APP_DEBUG'=>true,
        'ACTION_404_TMPL'=>"Public:404",
	    'APP_FILE_CASE' =>true, //检查文件大小写对windows平台有效
        'UPLOAD_FILE_RULE'		=>	'uniqid',
	    /* 静态缓存设置 */

	    'HTML_CACHE_ON'			=> true,   // 默认关闭静态缓存
	    'HTML_CACHE_TIME'		=> 60,      // 静态缓存有效期
	    'HTML_READ_TYPE'        => 0,       // 静态缓存读取方式 0 readfile 1 redirect
	    'HTML_FILE_SUFFIX'      => '.shtml',// 默认静态文件后缀
'TMPL_CACHE_ON'			=> false,
   'ATTACHDIR'=>'Public',
'ATTACHSIZE'=>112097192,
'ATTACHEXT'=>'jpg,gif,png',
'THUMBMAXWIDTH'=>300,
'THUMBMAXHEIGHT'=>300,
 'URL_HTML_SUFFIX'=>'.shtml',
'THUMBSUFFIX'=>'_thumb',
//用户头像
'AVATAR'=>'Public/upload/Avatar/',
//图片资源路径
'imglink'=>'__ROOT__/Public/Resource/images/',
'MAX_UPLOAD'=>'3333333330',
'ALLOW_UPLOAD_EXTS'=>'jpg,gif,png,bmp',
'AUTO_GEN_IMAGE'=>'2',
'MAX_UPLOAD'=>'3333333330',
'AVATAR_SIZE' => 80,

    'SECURE_CODE'       =>  'SECURE17845',
	);
	if(is_array($db_config))
	$config = array_merge($sys_config,$global_config,$db_config,$ext_config,$array);
	else
	$config = array_merge($sys_config,$global_config,$array);
	return $config;
?>