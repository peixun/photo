<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

header('Cache-control: private');
header('Content-type: text/html; charset=utf-8');
define('ROOT_PATH', str_replace('services/init.php', '', str_replace('\\', '/', __FILE__)));
define('TMPL_PATH', 'app/Tpl/');
define('TIME',gmtTime());

require('db.php');
require('template.php');
require(ROOT_PATH.'ThinkPHP/Common/compat.php');
$sys_config = require ROOT_PATH.'Public/sys_config.php';
$convention = require(ROOT_PATH.'Public/global_config.php');

if(function_exists('date_default_timezone_set'))
	date_default_timezone_set($convention['DEFAULT_TIMEZONE']);
	
if(!file_exists(ROOT_PATH.'app/Runtime/caches/'))
	mkdir(ROOT_PATH.'app/Runtime/caches/');
	
if(!file_exists(ROOT_PATH.'app/Runtime/compiled/'))
	mkdir(ROOT_PATH.'app/Runtime/compiled/');
	
$db_config	=	require ROOT_PATH.'Public/db_config.php';

	 if(intval($db_config['DB_PCONNECT'])==1)	
	 $pconnect = true;
	 else
	 $pconnect = false;
	 $db = new mysql_db($db_config['DB_HOST'].":".$db_config['DB_PORT'], $db_config['DB_USER'],$db_config['DB_PWD'],$db_config['DB_NAME'],'utf8',$pconnect);
 
//$timezone = intval($GLOBALS['db']->getOne("SELECT val FROM ".$GLOBALS['db_config']['DB_PREFIX']."sys_conf WHERE name='TIME_ZONE'"));//
$timezone = eyooC("TIME_ZONE");
//$langSet = $db->getOne("SELECT val FROM ".$db_config['DB_PREFIX']."sys_conf WHERE name='DEFAULT_LANG'");
$langSet = eyooC("DEFAULT_LANG");
$langItem = $db->getRow("SELECT `id`,`lang_name`,`show_name`,`time_zone`,`tmpl`,`seokeyword`,`seocontent`,`shop_title`,`shop_name`,`default`,`currency` FROM ".$db_config['DB_PREFIX']."lang_conf WHERE lang_name='$langSet'");

define('LANG',$langSet);


$tpl = new template;
$tpl->template_dir   = ROOT_PATH . TMPL_PATH . $langItem['tmpl'];
$tpl->cache_dir      = ROOT_PATH . 'app/Runtime/caches';
$tpl->compile_dir    = ROOT_PATH . 'app/Runtime/compiled';
$tpl->direct_output = false;
$tpl->force_compile = false;

$tpl->assign('TMPL_PATH', TMPL_PATH . $langItem['tmpl']);
$tpl->assign('TIME',gmtTime());

define("DB_PREFIX",$db_config['DB_PREFIX']);

// 当前文件名
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
 
function gmtTime()
{
	return (time() - date('Z'));
}



function dotran($str)
{
	$str = str_replace("\r\n",'',$str);
	$str = str_replace("\t",'',$str);
	$str = str_replace("\b",'',$str);
	return $str;
}
/**
 * 格式化时间
**/
function toDate($time, $format = 'Y-m-d H:i:s')
{
	if (empty ($time))
		return '';
	
	$time = $time + $GLOBALS['timezone'] * 3600; 
	$format = str_replace ('#',':',$format );
	return date ($format,$time );
}
function a_toDate($time, $format = 'Y-m-d H:i:s')
{
	if (empty ($time))
		return '';
	
	$time = $time + $GLOBALS['timezone'] * 3600; 
	$format = str_replace ('#',':',$format );
	return date ($format,$time );
}
/**
 * 创建像这样的查询: "IN('a','b')";
 *
 * @access   public
 * @param    mix      $item_list      列表数组或字符串
 * @param    string   $field_name     字段名称
 *
 * @return   void
 */
function db_create_in($item_list, $field_name = '')
{
	if (empty($item_list))
	{
		return $field_name . " IN ('') ";
	}
	else
	{
		if (!is_array($item_list))
		{
			$item_list = explode(',', $item_list);
		}
		$item_list = array_unique($item_list);
		$item_list_tmp = '';
		foreach ($item_list AS $item)
		{
			if ($item !== '')
			{
				$item_list_tmp .= $item_list_tmp ? ",'$item'" : "'$item'";
			}
		}
		if (empty($item_list_tmp))
		{
			return $field_name . " IN ('') ";
		}
		else
		{
			return $field_name . ' IN (' . $item_list_tmp . ') ';
		}
	}
}

function utf8ToGB($str)
{
	include_once(ROOT_PATH."ThinkPHP/Vendor/iconv.php");
	$chinese = new Chinese();
	return $chinese->Convert("UTF-8","GBK",$str);
}

function gbToUTF8($str)
{
	include_once(ROOT_PATH."ThinkPHP/Vendor/iconv.php");
	$chinese = new Chinese();
	return $chinese->Convert("GBK","UTF-8",$str);
}
function formatPrice($price)
{
//	$unit = $GLOBALS['db']->getOne("select val from ".DB_PREFIX."sys_conf where name = 'BASE_CURRENCY_UNIT'");
	$unit = eyooC("BASE_CURRENCY_UNIT");
	return $unit.round(floatval($price),2);
}
function getHttp()
{
	return (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';
}
function getDomain()
{
	/* 协议 */
	$protocol = getHttp();

	/* 域名或IP地址 */
	if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
	{
		$host = $_SERVER['HTTP_X_FORWARDED_HOST'];
	}
	elseif (isset($_SERVER['HTTP_HOST']))
	{
		$host = $_SERVER['HTTP_HOST'];
	}
	else
	{
		/* 端口 */
		if (isset($_SERVER['SERVER_PORT']))
		{
			$port = ':' . $_SERVER['SERVER_PORT'];

			if ((':80' == $port && 'http://' == $protocol) || (':443' == $port && 'https://' == $protocol))
			{
				$port = '';
			}
		}
		else
		{
			$port = '';
		}

		if (isset($_SERVER['SERVER_NAME']))
		{
			$host = $_SERVER['SERVER_NAME'] . $port;
		}
		elseif (isset($_SERVER['SERVER_ADDR']))
		{
			$host = $_SERVER['SERVER_ADDR'] . $port;
		}
	}

	return $protocol . $host;
}
//services 用的eyooC
function eyooC($name)
	{
		if($name == 'SHOP_URL')
			return "http://".$_SERVER['HTTP_HOST'].__ROOT__;
		else
//			return $GLOBALS['db']->getOne("select val from ".DB_PREFIX."sys_conf where name='".$name."'");
		return $GLOBALS['sys_config'][$name];
}
?>