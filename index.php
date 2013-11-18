<?php
// +----------------------------------------------------------------------
// | ThinkPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2008 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

// 定义ThinkPHP框架路径
//4、静态化后的界面，将不再启用ThinkPHP框架，而是直接返回静态页面

//if(file_exists(getcwd()."/Public/install.lock"))
//{
//include "HtmlCache.php";
//}


//echo "========APP============<br>";

define('BASE_PATH','./');
define('THINK_PATH', './ThinkPHP');
//定义项目名称和路径
define('APP_NAME', 'home');
define('APP_PATH', './home');

// 加载框架入口文件
require(THINK_PATH."/ThinkPHP.php");

//实例化一个网站应用实例
$AppWeb = new App();
//应用程序初始化
$AppWeb->run();
?>