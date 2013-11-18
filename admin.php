<?php

define('BASE_PATH','./');
define('THINK_PATH', './ThinkPHP');
//定义项目名称和路径
define('APP_NAME', 'admin');
define('APP_PATH', './admin');


// 加载框架入口文件
require(THINK_PATH."/ThinkPHP.php");


//实例化一个网站应用实例
$App = new App();
//初始化d
$App->run();
?>