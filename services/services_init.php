<?php
// 用于分离框架的程序所需的引用

//定义__ROOT__常量
 if(!defined('_PHP_FILE_')) {
        if(IS_CGI) {
            //CGI/FASTCGI模式下
            $_temp  = explode('.php',$_SERVER["PHP_SELF"]);
            define('_PHP_FILE_',  rtrim(str_replace($_SERVER["HTTP_HOST"],'',$_temp[0].'.php'),'/'));
        }else {
            define('_PHP_FILE_',    rtrim($_SERVER["SCRIPT_NAME"],'/'));
        }
    }
if(!defined('__ROOT__')) {
        // 网站URL根目录
        if( strtoupper(APP_NAME) == strtoupper(basename(dirname(_PHP_FILE_))) ) {
            $_root = dirname(dirname(_PHP_FILE_));
        }else {
            $_root = dirname(_PHP_FILE_);
        }
        $_root = (($_root=='/' || $_root=='\\')?'':$_root);
        $_root = str_replace("/services","",$_root);
        define('__ROOT__', $_root  );
    }
define('ROOT_PATH', str_replace('services/services_init.php', '', str_replace('\\', '/', __FILE__)));
//end 定义__ROOT__常量    

//引入数据库的系统配置及定义配置函数

if(file_exists(ROOT_PATH."/Public/sys_config.php"))
$sys_config = require ROOT_PATH.'Public/sys_config.php';
function conf($name)
{
		if($name == 'SHOP_URL')
			return "http://".$_SERVER['HTTP_HOST'].__ROOT__;
		else
		return $GLOBALS['sys_config'][$name];
}
//end 引入数据库的系统配置及定义配置函数

//引入时区配置及定义时间函数
$time_conf = require(ROOT_PATH.'Public/global_config.php');
if(function_exists('date_default_timezone_set'))
	date_default_timezone_set($convention['DEFAULT_TIMEZONE']);
function get_gmt_time()
{
	return (time() - date('Z'));
}
//end 引入时区配置及定义时间函数

//定义缓存
require('Utils/Cache.php');
$cache = CacheService::getInstance("File");
//end 定义缓存

//定义DB
require('db.php');
$db_config	=	require ROOT_PATH.'Public/db_config.php';
define('DB_PREFIX', $db_config['DB_PREFIX']); //add by chenfq 2010-09-19
if(intval($db_config['DB_PCONNECT'])==1)	
$pconnect = true;
else
$pconnect = false;
$db = new mysql_db($db_config['DB_HOST'].":".$db_config['DB_PORT'], $db_config['DB_USER'],$db_config['DB_PWD'],$db_config['DB_NAME'],'utf8',$pconnect);
//end 定义DB

//定义模板引擎
$langSet = conf("DEFAULT_LANG");
$langItem = $db->getRow("SELECT `id`,`lang_name`,`show_name`,`time_zone`,`tmpl`,`seokeyword`,`seocontent`,`shop_title`,`shop_name`,`default`,`currency` FROM ".DB_PREFIX."lang_conf WHERE lang_name='$langSet'");
require('template.php');
if(!file_exists(ROOT_PATH.'app/Runtime/caches/'))
	mkdir(ROOT_PATH.'app/Runtime/caches/');
	
if(!file_exists(ROOT_PATH.'app/Runtime/compiled/'))
	mkdir(ROOT_PATH.'app/Runtime/compiled/');
$tpl = new template;
define('TMPL_PATH', 'app/Tpl/');
$tpl->template_dir   = ROOT_PATH . TMPL_PATH . $langItem['tmpl'];
$tpl->cache_dir      = ROOT_PATH . 'app/Runtime/caches';
$tpl->compile_dir    = ROOT_PATH . 'app/Runtime/compiled';
$tpl->direct_output = false;
$tpl->force_compile = false;
//end 定义模板引擎

//语言包
$langname = conf("DEFAULT_LANG");
if (empty($langname)) $langname = 'zh-cn';

$Ln = require ROOT_PATH.'app/Lang/'.$langname.'/htmlcache.php';
$Ln_common = require ROOT_PATH.'app/Lang/'.$langname.'/common.php';
$Ln_xy = require ROOT_PATH.'app/Lang/'.$langname.'/xy_lang.php';
	 
$Ln = array_merge($Ln,$Ln_common,$Ln_xy);
//end 语言包

?>