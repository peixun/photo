<?php
$GLOBALS['startTime'] = microtime(TRUE);
@session_start();
error_reporting(E_ALL ^ E_NOTICE);

$m_name = strtolower($_REQUEST['m']);
$a_name = strtolower($_REQUEST['a']);
if (!function_exists('json_encode')) {
     function format_json_value(&$value)
    {
        if(is_bool($value)) {
            $value = $value?'true':'false';
        }elseif(is_int($value)) {
            $value = intval($value);
        }elseif(is_float($value)) {
            $value = floatval($value);
        }elseif(defined($value) && $value === null) {
            $value = strval(constant($value));
        }elseif(is_string($value)) {
            $value = '"'.addslashes($value).'"';
        }
        return $value;
    }

    function json_encode($data)
    {
        if(is_object($data)) {
            //对象转换成数组
            $data = get_object_vars($data);
        }else if(!is_array($data)) {
            // 普通格式直接输出
            return format_json_value($data);
        }
        // 判断是否关联数组
        if(empty($data) || is_numeric(implode('',array_keys($data)))) {
            $assoc  =  false;
        }else {
            $assoc  =  true;
        }
        // 组装 Json字符串
        $json = $assoc ? '{' : '[' ;
        foreach($data as $key=>$val) {
            if(!is_null($val)) {
                if($assoc) {
                    $json .= "\"$key\":".json_encode($val).",";
                }else {
                    $json .= json_encode($val).",";
                }
            }
        }
        if(strlen($json)>1) {// 加上判断 防止空数组
            $json  = substr($json,0,-1);
        }
        $json .= $assoc ? '}' : ']' ;
        return $json;
    }
}
if (!function_exists('json_decode')) {
    function json_decode($json,$assoc=false)
    {
        // 目前不支持二维数组或对象
        $begin  =  substr($json,0,1) ;
        if(!in_array($begin,array('{','[')))
            // 不是对象或者数组直接返回
            return $json;
        $parse = substr($json,1,-1);
        $data  = explode(',',$parse);
        if($flag = $begin =='{' ) {
            // 转换成PHP对象
            $result   = new stdClass();
            foreach($data as $val) {
                $item    = explode(':',$val);
                $key =  substr($item[0],1,-1);
                $result->$key = json_decode($item[1],$assoc);
            }
            if($assoc)
                $result   = get_object_vars($result);
        }else {
            // 转换成PHP数组
            $result   = array();
            foreach($data as $val)
                $result[]  =  json_decode($val,$assoc);
        }
        return $result;
    }
}
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
        define('__ROOT__',   (($_root=='/' || $_root=='\\')?'':$_root));
    }


//首页
if (empty($m_name) && empty($a_name)){
	$m_name = 'index';
	$a_name = 'index';
}
/*
echo "==========HtmlCache==========<br>";
	echo $m_name."<br/>";
	echo $a_name."<br/>";
	echo 'user_id:'.$_SESSION['user_id']."<br/>";
	echo 'fanwe_user_id:'.unserialize(base64_decode($_COOKIE['fanwe_user_id']))."<br/>";
echo "==========HtmlCache==========<br>";
*/



define('ROOT_PATH', str_replace('HtmlCache.php', '', str_replace('\\', '/', __FILE__)));
require ROOT_PATH.'home/Lib/ORG/IpLocation.class.php';
$htmls = require ROOT_PATH.'home/Conf/htmls.php';
$htmls = array_change_key_case($htmls);
if(!empty($htmls)) {
  // 静态规则文件定义格式 actionName=>array(‘静态规则’,’缓存时间’,’附加规则')
  // 'read'=>array('{id},{name}',60,'md5') 必须保证静态规则的唯一性 和 可判断性
  // 检测静态规则
  if(isset($htmls[$m_name.':'.$a_name])) {
    $html   =   $htmls[$m_name.':'.$a_name];   // 某个模块的操作的静态规则
  }elseif(isset($htmls[$m_name.':'])){// 某个模块的静态规则
    $html   =   $htmls[$m_name.':'];
  }elseif(isset($htmls[$a_name])){
    $html   =   $htmls[$a_name]; // 所有操作的静态规则
  }elseif(isset($htmls['*'])){
    $html   =   $htmls['*']; // 全局静态规则
  }
    //dump($html);
  if(!empty($html)){

	 require(ROOT_PATH.'services/html_cache_init.php');

	 /*del by chenfq 2010-09-19
	 if(!file_exists(ROOT_PATH.'app/Runtime/caches/'))
		mkdir(ROOT_PATH.'app/Runtime/caches/');

	$convention = require(ROOT_PATH.'Public/global_config.php');
	if(function_exists('date_default_timezone_set'))
		date_default_timezone_set($convention['DEFAULT_TIMEZONE']);

	 $db_config	=	require ROOT_PATH.'Public/db_config.php';
	 define('DB_PREFIX', $db_config['DB_PREFIX']);
	 if(intval($db_config['DB_PCONNECT'])==1)
	 $pconnect = true;
	 else
	 $pconnect = false;
	 $db = new mysql_db($db_config['DB_HOST'].":".$db_config['DB_PORT'], $db_config['DB_USER'],$db_config['DB_PWD'],$db_config['DB_NAME'],'utf8',$pconnect);
 	*/

     if (!empty($_GET['ru']))
	 {
		$parent_id = intval($_GET['ru']);
		//if($db->getOne("SELECT last_ip FROM ".DB_PREFIX."user WHERE id=".$parent_id) != get_ip()||$db->getOne("select val from ".DB_PREFIX."sys_conf where name ='REFERRALS_IP_LIMIT'")==0)
		if($GLOBALS['db']->getOne("SELECT last_ip FROM ".DB_PREFIX."user WHERE id=".$parent_id) != get_ip()||eyooC2("REFERRALS_IP_LIMIT")==0)
		{
			setcookie('referrals_uid',base64_encode(serialize($parent_id)));
			$_SESSION['referrals_uid'] = $parent_id;
		}
	 }

//	 $langname = $GLOBALS['db']->getOne("SELECT val FROM ".DB_PREFIX."sys_conf WHERE name='DEFAULT_LANG'");
	 $langname = eyooC2("DEFAULT_LANG");
	 if (empty($langname)) $langname = 'zh-cn';

	 //echo ROOT_PATH.'app/Lang/'.$langname.'/htmlcache.php';

	 $Ln = require ROOT_PATH.'home/Lang/'.$langname.'/htmlcache.php';
	 $Ln_common = require ROOT_PATH.'app/Lang/'.$langname.'/common.php';
	 $Ln_xy = require ROOT_PATH.'home/Lang/'.$langname.'/xy_lang.php';

	 $Ln = array_merge($Ln,$Ln_common,$Ln_xy);

	 //开始自动登录 by hc
  	if($_SESSION['user_id'] == 0&&isset($_COOKIE['email']) && isset($_COOKIE['password']))
	{
				$cookie_user['email'] = trim(unserialize(base64_decode($_COOKIE['email'])));
				$cookie_user['user_pwd'] = trim(unserialize(base64_decode($_COOKIE['password'])));

				$userinfo = $GLOBALS['db']->getRow("SELECT `id`,`user_name`,`user_pwd`,`nickname`,`status`,`create_time`,`update_time`,`last_ip`,`sex`,`email`,`qq`,`msn`,`alim`,`address`,`group_id`,`fix_phone`,`fax_phone`,`mobile_phone`,`zip`,`pwd_question`,`pwd_answer`,`score`,`money`,`city_id`,`subscribe`,`active_sn`,`reset_sn`,`parent_id`,`sync_flag`,`birthday`,`buy_count`,`is_receive_sms`,`ucenter_id`,`ucenter_id_tmp` FROM ".DB_PREFIX."user WHERE email='".$cookie_user['email']."' and user_pwd='".$cookie_user['user_pwd']."'");

				if($userinfo)
				{
					if($userinfo['status'])
					{
						setcookie('email',base64_encode(serialize($userinfo['email'])),time()+365*60*60*24);
						setcookie('password',base64_encode(serialize($userinfo['user_pwd'])),time()+365*60*60*24);
						$_SESSION['user_name'] = $userinfo['user_name'];
						$_SESSION['user_id'] = $userinfo['id'];
						$_SESSION['group_id'] = $userinfo['group_id'];
						$_SESSION['user_email'] = $userinfo['email'];
						$_SESSION['score'] = $userinfo['score'];

						$GLOBALS['db']->query("UPDATE ".DB_PREFIX."user set last_ip='".get_ip()."' where id=".$userinfo['id']);

					}
				}
	}


	 //需要生成，静态文件
	 define('REQUIRE_CACHE', true);

     //解读静态规则
      $rule  = $html[0];
      //以$_开头的系统变量
      $rule  = preg_replace('/{\$(_\w+)\.(\w+)\|(\w+)}/e',"\\3(\$\\1['\\2'])",$rule);
      $rule  = preg_replace('/{\$(_\w+)\.(\w+)}/e',"\$\\1['\\2']",$rule);
      //{ID|FUN} GET变量的简写
      $rule  = preg_replace('/{(\w+)\|(\w+)}/e',"\\2(\$_GET['\\1'])",$rule);
      $rule  = preg_replace('/{(\w+)}/e',"\$_GET['\\1']",$rule);
      // 特殊系统变量
      $rule  = str_ireplace(array('{:module}','{:action}'),array(ucfirst($m_name),$a_name),$rule);
      //{|FUN} 单独使用函数
      $rule  = preg_replace('/{|(\w+)}/e',"\\1()",$rule);
	  if(!empty($html[2])) $rule    =   $html[2]($rule); // 应用附加函数
      $cacheTime = isset($html[1])?$html[1]:60; // 缓存有效期
      //当前缓存文件
      define("C_CITY_ID",getHideParam());
      $rule = C_CITY_ID.'#'.$rule.".shtml";
      //dump(HTML_PATH . $rule.C('HTML_FILE_SUFFIX'));
      define('HTML_FILE_NAME',ROOT_PATH.'home/Runtime/Html/'.$rule);

      if (file_exists(HTML_FILE_NAME) && checkHTMLCache(HTML_FILE_NAME, $cacheTime)){
      	/*
      	echo filemtime(HTML_FILE_NAME)."<br>";
   		echo time()."<br>";
   		echo date('Y-m-d', time())."<br>";
   		echo (strtotime(date('Y-m-d', time())) - 1)."<br>";
   		echo date('Y-m-d', strtotime(date('Y-m-d', time())) - 1)."<br>";
   		*/
     	readHTMLCache();
      }
      else
      {

      }
   }
}

    /**
     +----------------------------------------------------------
     * 检查静态HTML文件是否有效
     * 如果无效需要重新更新
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $cacheFile  静态文件名
     * @param integer $cacheTime  缓存有效期
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
function checkHTMLCache($cacheFile='',$cacheTime='')
{
   if(!is_file($cacheFile)){
       return false;
   }elseif(!is_numeric($cacheTime) && function_exists($cacheTime)){
      return $cacheTime($cacheFile);
   }elseif ($cacheTime != -1 && time() > filemtime($cacheFile) + $cacheTime) {
      // 文件是否在有效期
      return false;
   }elseif (filemtime($cacheFile) < strtotime(date('Y-m-d', time()))){//隔天无效
   	  return false;
   }
   else{  // 商品到期时自动更新
   		$file_time = filemtime($cacheFile) - date('Z');
   		//先定位当前访问页
   		if((!isset($_REQUEST['m'])&&!isset($_REQUEST['a']))
   			||($_REQUEST['m']=='Index'&&$_REQUEST['a']=='index')
   			||($_REQUEST['m']=='Goods'&&$_REQUEST['a']=='index')
   			||($_REQUEST['m']=='Goods'&&$_REQUEST['a']=='showcate')
   			||($_REQUEST['m']=='BelowLine'&&$_REQUEST['a']=='index')
   			||($_REQUEST['m']=='Advance'&&$_REQUEST['a']=='index')
   			||($_REQUEST['m']=='Goods'&&$_REQUEST['a']=='show'))
   		{
   			//首页，往期团购，线下团购，团购预告页判断当前团购商品 需要做清除缓存的验证
   			$city_id = C_CITY_ID;
   			$time = get_gmt_time();
   			//检测算法。 在当前时间$time与缓存生成时间$file_time之间，有商品上线或下线即返回false
   			//取得时间段时的上线商品
   			$sql = "select count(*) from ".DB_PREFIX."goods ".
   				   " where promote_begin_time between $file_time and $time ".
   				   " or promote_end_time between $file_time and $time";
   			$change_goods_count = $GLOBALS['db']->getOne($sql);
   			if($change_goods_count>0)return false;
   		}
   }
   //静态文件有效
   return true;
}
function get_gmt_time()
{
	return (time() - date('Z'));
}
    /**
     +----------------------------------------------------------
     * 读取静态缓存
     +----------------------------------------------------------
     * @access static
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
function readHTMLCache()
{
  $code = eyooC2("INTEGRATE_CODE");
  $fanwe_user_id = intval(unserialize(base64_decode($_COOKIE['fanwe_user_id'])));
  $user_id =  intval($_SESSION['user_id']);
  if (empty($code)) $code = 'eyoo';

  if ($user_id == 0 && $code == 'ucenter' && $fanwe_user_id > 0){
    $userinfo = $GLOBALS['db']->getRow("SELECT `id`,`user_name`,`user_pwd`,`nickname`,`status`,`create_time`,`update_time`,`last_ip`,`sex`,`email`,`qq`,`msn`,`alim`,`address`,`group_id`,`fix_phone`,`fax_phone`,`mobile_phone`,`zip`,`pwd_question`,`pwd_answer`,`score`,`money`,`city_id`,`subscribe`,`active_sn`,`reset_sn`,`parent_id`,`sync_flag`,`birthday`,`buy_count`,`is_receive_sms`,`ucenter_id`,`ucenter_id_tmp` FROM ".DB_PREFIX."user where id = '$fanwe_user_id'");
	$_SESSION['user_name'] = $userinfo['user_name'];
	$_SESSION['user_id'] = $userinfo['id'];
	$_SESSION['group_id'] = $userinfo['group_id'];
	$_SESSION['user_email'] = $userinfo['email'];
	$_SESSION['score'] = $userinfo['score'];
  }

  if ($user_id > 0 && $code == 'ucenter' && $fanwe_user_id == 0){
  	 unset($_SESSION['user_name']);
	 unset($_SESSION['user_id']);
	 unset($_SESSION['group_id']);
	 unset($_SESSION['user_email']);
	 unset($_SESSION['other_sys']);
	 unset($_COOKIE['email']);
	 unset($_COOKIE['password']);
	 unset($_COOKIE['fanwe_user_id']);
   }

   $user_id =  intval($_SESSION['user_id']);
   $fp = fopen(HTML_FILE_NAME, "r");
   $content = @fread($fp, filesize(HTML_FILE_NAME));//读文件
   fclose($fp);
   $content = preg_replace('|(<div class="sysmsg-tip-top"></div>)(.*)(<div class="sysmsg-tip-bottom"></div>)|Us','',$content);
   if (($GLOBALS['m_name'] == 'index') && ($GLOBALS['a_name'] == 'index')){
   		$str = '<input id="enter-address-mail" name="email" class="f-input f-mail" type="text" value="'.$_SESSION['user_email'].'" size="20" />';
   		$content = preg_replace('|(<input id="enter-address-mail")(.*)(/>)|Us',$str,$content);
   }

    preg_match_all("/var(\s+)GOODS_ID(\s*)=(\s*)(\d+)/i",$content,$matches);
    $goods_id = intval($matches[4][0]);


	//loader_error_msg的加载
    $rs = preg_match_all ("/<loader_error_msg([^>]*)>/i", $content, $loaders);
    if($rs > 0)
    {
	    $result = base64_decode(base64_decode($_REQUEST['msg']));
    	$content = preg_replace("/<loader_error_msg([^>]*)>/i",$result,$content);
    }


    //loader_goods_info的加载
    $rs = preg_match_all ("/<loader_goods_info([^>]*)>/i", $content, $loaders);
    if($rs > 0)
    {
		$result = getGoodsStatus($goods_id);
    	$goods_info = $result['statusHTML'];
    	$content = preg_replace("/<loader_goods_info([^>]*)>/i",$goods_info,$content);
    }
	//loader_goods_btn的加载
    $rs = preg_match_all ("/<loader_goods_btn([^>]*)>/i", $content, $loaders);
    if($rs > 0)
    {
    	if(!$result)
    	{
			$result = getGoodsStatus($goods_id);
    	}
    	$goods_btn = $result['btnHTML'];
    	$content = preg_replace("/<loader_goods_btn([^>]*)>/i",$goods_btn,$content);
    }

	//loader_referrals_url的加载
    $rs = preg_match_all ("/<loader_referrals_url([^>]*)>/i", $content, $loaders);
    if($rs > 0)
    {
	    $result = getReferralsGoods($goods_id,intval($_SESSION['user_id']));
    	$content = preg_replace("/<loader_referrals_url([^>]*)>/i",$result,$content);
    }

	//loader_referrals_text的加载
    $rs = preg_match_all ("/<loader_referrals_text([^>]*)>/i", $content, $loaders);
    if($rs > 0)
    {
    	$goods_id = intval($_REQUEST['id']);
    	if(eyooC2("URL_ROUTE")==0)
	    $result = eyooC2("SHOP_URL")."/index.php?m=Goods&a=show&id=".$goods_id."&ru=".intval($_SESSION['user_id']);
	    else
	    $result = eyooC2("SHOP_URL")."/tg-".$goods_id."-ru-".intval($_SESSION['user_id']).".html";
    	$content = preg_replace("/<loader_referrals_text([^>]*)>/i",$result,$content);
    }



	//loader_redirect的加载
    $rs = preg_match_all ("/<loader_redirect([^>]*)>/i", $content, $loaders);
    if($rs > 0)
    {
	    $result = $_SERVER['HTTP_REFERER'];
    	$content = preg_replace("/<loader_redirect([^>]*)>/i",$result,$content);
    }

    //以下开始加载购物车页Cart-index的相关标签
    if($_REQUEST['m']=='Cart'&&$_REQUEST['a']=='index')
    {
    	$goods_info = $GLOBALS['cache']->get("CACHE_CART_GOODS_CACHE_".$goods_id);
     	if($goods_info===false)
    	{
    		$goods_info = getGoodsItem($goods_id);
    		$GLOBALS['cache']->set("CACHE_CART_GOODS_CACHE_".$goods_id, $goods_info);
    	}
   		if(intval($_SESSION['user_id']) > 0)
		{
				$sql = "select sum(og.number) as num from ".DB_PREFIX."order_goods as og left join ".DB_PREFIX."order as o on og.order_id = o.id where og.rec_id = ".intval($goods_info['id'])." and o.user_id=".intval($_SESSION['user_id']);
				$num = $GLOBALS['db']->getOne($sql);
				$goods_info['userBuyCount'] = intval($num);

		}

    	//loader_cart_error 的加载  错误
	    $rs = preg_match_all ("/<loader_cart_error([^>]*)>/i", $content, $loaders);
	    if($rs > 0)
	    {
	    	$err = base64_decode(base64_decode($_REQUEST['err']));
		    $GLOBALS['tpl']->assign("error",$err);
		    $GLOBALS['tpl']->assign("lang",$GLOBALS['Ln']);
		    $result = $GLOBALS['tpl']->fetch('Inc/goods/cart_error.tpl');
	    	$content = preg_replace("/<loader_cart_error([^>]*)>/i",$result,$content);
	    }

    	//loader_supplus_count的加载   剩余数量
	    $rs = preg_match_all ("/<loader_supplus_count([^>]*)>/i", $content, $loaders);
	    if($rs > 0)
	    {
		    $result = $goods_info['surplusCount'];
	    	$content = preg_replace("/<loader_supplus_count([^>]*)>/i",$result,$content);
	    }

    	//loader_goods_stock的加载   库存
	    $rs = preg_match_all ("/<loader_goods_stock([^>]*)>/i", $content, $loaders);
	    if($rs > 0)
	    {
		    $result = $goods_info['stock'];
	    	$content = preg_replace("/<loader_goods_stock([^>]*)>/i",$result,$content);
	    }

   		//loader_goods_shop_price的加载   价格
	    $rs = preg_match_all ("/<loader_goods_shop_price([^>]*)>/i", $content, $loaders);
	    if($rs > 0)
	    {
		    $result = $goods_info['shop_price'];
	    	$content = preg_replace("/<loader_goods_shop_price([^>]*)>/i",$result,$content);
	    }

    	//loader_attr_price的加载   属性价格
	    $rs = preg_match_all ("/<loader_attr_price([^>]*)>/i", $content, $loaders);
	    if($rs > 0)
	    {
		    $attrPrice = 0;
		    if($goods_info['attrlist'])
		    {
			foreach($goods_info['attrlist'] as $attrlist)
			{
				foreach($attrlist['attr_value'] as $attr)
				{
					$attrPrice+=$attr["price"];
					break;
				}
			}
		    }
	    	$content = preg_replace("/<loader_attr_price([^>]*)>/i",$attrPrice,$content);
	    }

    	//loader_max_bought的加载   最大购买数
	    $rs = preg_match_all ("/<loader_max_bought([^>]*)>/i", $content, $loaders);
	    if($rs > 0)
	    {
		    $result = $goods_info['max_bought'];
	    	$content = preg_replace("/<loader_max_bought([^>]*)>/i",$result,$content);
	    }

   	    //loader_buy_count的加载   已购数量
	    $rs = preg_match_all ("/<loader_buy_count([^>]*)>/i", $content, $loaders);
	    if($rs > 0)
	    {
		    $result = $goods_info['userBuyCount'];
	    	$content = preg_replace("/<loader_buy_count([^>]*)>/i",$result,$content);
	    }
    }

    //以下的cart-check验证
    if($_REQUEST['m']=='Cart'&&$_REQUEST['a']=='check')
    {
    	if(intval($_SESSION['user_id'])==0)
    	{
    		if(eyooC2("URL_ROUTE")==0)
    		{
    			$cart_login_url = __ROOT__."/index.php?m=Cart&a=cartLogin";
    		}
    		else
    		{
    			$cart_login_url = __ROOT__."/Cart-cartLogin.html";
    		}
    		header("Location:".$cart_login_url);
    	}
    	else
    	{
    		//购物车提交页的静态页处理
    		//以下对购物车进行检测
	    	$goods_id = intval($_REQUEST['id']);
	   		$goods_info = $GLOBALS['cache']->get("CACHE_CART_GOODS_CACHE_".$goods_id);
			if($goods_info===false)
			{
					$goods_info =getGoodsItem($goods_id);
					$GLOBALS['cache']->set("CACHE_CART_GOODS_CACHE_".$goods_id,$goods_info);
			}

	   		if(!$goods_info || ($goods_info['promote_begin_time'] > get_gmt_time() && $goods_info['type_id'] != 2))
	   		{
	   			header("Location:".eyooC2("SHOP_URL"));
	   			exit;
	   		}


	   		$session_id = session_id();
			$rec_id = intval($_REQUEST['goods']);  //购买的ID
	   		$rec_module = "PromoteGoods";  //购买的模块
			$number = intval($_REQUEST['quantity']);  //购买数量
			$goods_attr = $_REQUEST['goods_attr'];

			$attrStr = "";
			$_REQUEST['id'] = $rec_id;

    		if(intval($_SESSION['user_id']) > 0)
		   {
				$sql = "select sum(og.number) as num from ".DB_PREFIX."order_goods as og left join ".DB_PREFIX."order as o on og.order_id = o.id where og.rec_id = ".intval($goods_info['id'])." and o.user_id=".intval($_SESSION['user_id']);
				$num = $GLOBALS['db']->getOne($sql);
				$goods_info['userBuyCount'] = intval($num);
			}

	    	if($goods_info['promote_begin_time'] > get_gmt_time() && $goods_info['type_id'] != 2)
	   		{
	   			header("Location:".__ROOT__."/index.php?m=Cart&a=index&id=".$goods_id."&err=". base64_encode(base64_encode($GLOBALS['Ln']['HC_GROUPON_NOT_BEGIN'])));
				exit;
	   		}

	    	if($number < 1)
	   		{
				header("Location:".__ROOT__."/index.php?m=Cart&a=index&id=".$goods_id."&err=".base64_encode(base64_encode($GLOBALS['Ln']['HC_BUYCOUNT_LESS_ONE'])));
				exit;
	   		}

	    	$bln = false;
			$err = "";
			$userBuyCount = intval($goods_info['userBuyCount']);
			$maxBought    = intval($goods_info['max_bought']);
			$surplusCount = intval($goods_info['surplusCount']);
			$goodsStock   = intval($goods_info['stock']);

			if($number + $userBuyCount > $maxBought && $maxBought > 0)
			{
				$number = $maxBought - $userBuyCount;
				$bln = true;
			}

			if($number > $surplusCount && $goodsStock > 0)
			{
				$number = $surplusCount;
				$bln = true;
			}

			if($bln)
			{
				if($maxBought > 0)
					$err.=sprintf($GLOBALS['Ln']['HC_USER_MAX_BUYCOUNT'],$maxBought);

				if($goodsStock > 0)

					$err.=sprintf($GLOBALS['Ln']['HC_ONLY_LESS_COUNT'],$surplusCount).(($err == "") ? $GLOBALS['Ln']['HC_GOODS'] : "")."，";

				$err.= sprintf($GLOBALS['Ln']['HC_HASBUYCOUNT_LESSCOUNT'],$userBuyCount,$number);

				header("Location:".__ROOT__."/index.php?m=Cart&a=index&id=".$goods_id."&err=".base64_encode(base64_encode($err)));
				exit;
			}


			$now = get_gmt_time();

			if($goods_info['type_id'] == 2)
				$unit_price = floatval($goods_info['earnest_money']);
			else
	   			$unit_price = floatval($goods_info['shop_price']);

			if(is_array($goods_attr))
			{
				foreach($goods_attr as $attr)
				{
					$sql ="select ga.attr_value_1 as attr_value,ga.price,gta.name_1 as name from ".DB_PREFIX."goods_attr as ga left join ".DB_PREFIX."goods_type_attr as gta on gta.id = ga.attr_id where ga.id = ".intval($attr)." and ga.goods_id = ".$goods_info['id'];

					$attrItem = $GLOBALS['db']->getRow($sql);

					$unit_price += floatval($attrItem['price']);

					if(empty($attrStr))
						$attrStr.=$attrItem['name']."：".$attrItem['attr_value'];
					else
						$attrStr.= "\n".$attrItem['name']."：".$attrItem['attr_value'];
				}
			}

			$cart_item = $GLOBALS['db']->getRow("select id from ".DB_PREFIX."cart where session_id='".$session_id."'");

			if($cart_item['id'] > 0)
			{
				$sql_upd = "update ".DB_PREFIX."cart set pid =0,rec_id=".$rec_id.",rec_module='".$rec_module."'".
					   ",session_id='".$session_id."'".
					   ",user_id='".intval($_SESSION['user_id'])."'".
					   ",number='".$number."'".
					   ",data_unit_price='".floatval($unit_price)."'".
					   ",data_score='".$goods_info['score']."'".
					   ",data_total_score='".(intval($goods_info['score'])*$number)."'".
					   ",data_total_price='".($unit_price*$number)."'".
					   ",create_time='".$now."'".
					   ",update_time='".$now."'".
					   ",data_name='".$goods_info['name_1']."'".
					   ",data_sn='".$goods_info['sn']."'".
					   ",data_weight='".$goods_info['weight']."'".
					   ",data_total_weight='".(floatval($goods_info['weight'])*$number)."'".
					   ",is_inquiry='".$goods_info['is_inquiry']."'".
					   ",goods_type='".$goods_info['type_id']."'".
					   ",attr='".$attrStr.
					   "' where id = ".$cart_item['id'];
				$GLOBALS['db']->query($sql_upd);
			}
			else
			{
				$sql_ins = "insert into ".DB_PREFIX."cart (`id`,`pid`,`rec_id`,`rec_module`,`session_id`,`user_id`,`number`,`data_unit_price`,`data_score`,`data_promote_score`,`data_total_score`,`data_total_price`,`create_time`,`update_time`,`data_name`,`data_sn`,`data_weight`,`data_total_weight`,`is_inquiry`,`goods_type`,`attr`)".
					   " values (0,0,'".$rec_id."','".$rec_module."','".$session_id."','".intval($_SESSION['user_id'])."','".$number."','".floatval($unit_price)."','".$goods_info['score']."',0,'".(intval($goods_info['score'])*$number)."','".($unit_price*$number)."','".$now."','".$now."','".$goods_info['name_1']."','".$goods_info['sn']."','".$goods_info['weight']."','".(floatval($goods_info['weight'])*$number)."','".$goods_info['is_inquiry']."','".$goods_info['type_id']."','".$attrStr."')";
				$GLOBALS['db']->query($sql_ins);
			}
			$cart_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."cart where session_id='".$session_id."'");
	   		//end 购物车检测

	    	//loader_user_info_money的加载   会员余额
		    $rs = preg_match_all ("/<loader_user_info_money([^>]*)>/i", $content, $loaders);
		    if($rs > 0)
		    {
			    $user_info = $GLOBALS['cache']->get("CACHE_USER_INFO_".intval($_SESSION['user_id']));
				if($user_info===false)
				{
					$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id =".intval($_SESSION['user_id']));
					$GLOBALS['cache']->set("CACHE_USER_INFO_".intval($_SESSION['user_id']),$user_info);
				}
			    $result = $user_info['money'];
		    	$content = preg_replace("/<loader_user_info_money([^>]*)>/i",$result,$content);
		    }

    		//loader_cart_total_price的加载  购物车总价
		    $rs = preg_match_all ("/<loader_cart_total_price([^>]*)>/i", $content, $loaders);
		    if($rs > 0)
		    {
			    $result = $cart_item['data_total_price'];
		    	$content = preg_replace("/<loader_cart_total_price([^>]*)>/i",$result,$content);
		    }

    		//loader_cart_item_attr的加载  购物车属性
		    $rs = preg_match_all ("/<loader_cart_item_attr([^>]*)>/i", $content, $loaders);
		    if($rs > 0)
		    {
			    $result = nl2br($attrStr);
		    	$content = preg_replace("/<loader_cart_item_attr([^>]*)>/i",$result,$content);
		    }

    		//loader_cart_item_number的加载  购买数量
		    $rs = preg_match_all ("/<loader_cart_item_number([^>]*)>/i", $content, $loaders);
		    if($rs > 0)
		    {
			    $result = $number;
		    	$content = preg_replace("/<loader_cart_item_number([^>]*)>/i",$result,$content);
		    }

    		//loader_cart_item_unit_price的加载 单价
		    $rs = preg_match_all ("/<loader_cart_item_unit_price([^>]*)>/i", $content, $loaders);
		    if($rs > 0)
		    {
		    	if($cart_item['data_unit_price']==0)
		    	{
		    		$result = $GLOBALS['Ln']['JJ_FREE'];
		    	}
		    	else
			    $result = format_price($cart_item['data_unit_price']);
		    	$content = preg_replace("/<loader_cart_item_unit_price([^>]*)>/i",$result,$content);
		    }

    		//loader_cart_item_total_price 的加载 总价
		    $rs = preg_match_all ("/<loader_cart_item_total_price([^>]*)>/i", $content, $loaders);
		    if($rs > 0)
		    {
		    	if($cart_item['data_total_price']==0)
		    	{
		    		$result = $GLOBALS['Ln']['JJ_FREE'];
		    	}
		    	else
			    $result = format_price($cart_item['data_total_price']);
		    	$content = preg_replace("/<loader_cart_item_total_price([^>]*)>/i",$result,$content);
		    }

		    $GLOBALS['tpl']->assign("lang",$GLOBALS['Ln']); //输出语言包

    		//loader_cart_delivery_order 的加载 订单拼运单
		    $rs = preg_match_all ("/<loader_cart_delivery_order([^>]*)>/i", $content, $loaders);
		    if($rs > 0)
		    {
			    //开始输出可以拼运单的订单

				$is_combine = $GLOBALS['cache']->get("IS_COMBINE_".intval($cart_item['rec_id']));
				if($is_combine===false)
				{
					$is_combine = $GLOBALS['db']->getOne("select allow_combine_delivery from ".DB_PREFIX."goods where id =".$cart_item['rec_id']);
					$GLOBALS['cache']->set("IS_COMBINE_".intval($cart_item['rec_id']),$is_combine);
				}
				if($is_combine == 1)
				{
					$order_deliverys = $GLOBALS['cache']->get("CACHE_ORDER_DELIVERYS_".intval($_SESSION['user_id']));
					if($order_deliverys===false)
					{
						$sql = "select o.*,g.allow_combine_delivery as acd from ".DB_PREFIX."order as o left join ".DB_PREFIX."order_goods as og on o.id = og.order_id left join ".DB_PREFIX."goods as g on og.rec_id = g.id ".
							" where o.delivery > 0 and o.money_status = 2 and o.goods_status = 0 and o.user_id = ".intval($_SESSION['user_id'])." and g.allow_combine_delivery = 1";
						$order_deliverys = $GLOBALS['db']->getALl($sql);
						foreach($order_deliverys as $k=>$v)
						{

							$order_deliverys[$k]['region_lv1_name'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."region_conf where id=".intval($v['region_lv1']));
							$order_deliverys[$k]['region_lv2_name'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."region_conf where id=".intval($v['region_lv2']));
							$order_deliverys[$k]['region_lv3_name'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."region_conf where id=".intval($v['region_lv3']));
							$order_deliverys[$k]['region_lv4_name'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."region_conf where id=".intval($v['region_lv4']));
							$order_deliverys[$k]['delivery_name'] = $GLOBALS['db']->getOne("select name_1 from ".DB_PREFIX."delivery where id=".intval($v['delivery']));

						}
						$GLOBALS['cache']->set("CACHE_ORDER_DELIVERYS_".intval($_SESSION['user_id']),$order_deliverys);
					}
					$GLOBALS['tpl']->assign("order_deliverys",$order_deliverys);

				}
		    	$result = $GLOBALS['tpl']->fetch('Inc/goods/cart_delivery_order.tpl');
		    	$content = preg_replace("/<loader_cart_delivery_order([^>]*)>/i",$result,$content);
		    }



    		//loader_cart_consignee_info 的加载  配送地址
		    $rs = preg_match_all ("/<loader_cart_consignee_info([^>]*)>/i", $content, $loaders);
		    if($rs > 0)
		    {
			    $consignee_id = intval($_REQUEST['consignee_id']);
				//add by chenfq 2010-04-21 默认取最后一次添加的地址
				if ($consignee_id <= 0 && $cart_item['user_id'] > 0){
					$sql = "select max(id) as maxid from ".DB_PREFIX."user_consignee where user_id = ".$cart_item['user_id'];
					$tmp = $GLOBALS['db']->getOne($sql);
					$consignee_id = intval($tmp);
				}


		   		$consignee_info = $GLOBALS['cache']->get("CACHE_CONSIGNEE_".$consignee_id);
				if($consignee_info===false)
				{

						$consignee_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_consignee where id =".$consignee_id);
						if($consignee_info)
						{
							$consignee_info['region_lv1_info'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."region_conf where id =".intval($item['region_lv1']));
							$consignee_info['region_lv2_info'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."region_conf where id =".intval($item['region_lv2']));
							$consignee_info['region_lv3_info'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."region_conf where id =".intval($item['region_lv3']));
							$consignee_info['region_lv4_info'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."region_conf where id =".intval($item['region_lv4']));
						}

						if(!$consignee_info)$consignee_info = array();
						$GLOBALS['cache']->set("CACHE_CONSIGNEE_".$consignee_id,$consignee_info);
				}



			    if($consignee_info)
				{
					$consignee_info['qq'] = $user_info['qq'];
					$consignee_info['msn'] = $user_info['msn'];
					$consignee_info['alim'] = $user_info['alim'];
					$consignee_info['email'] = $user_info['email'];
					$GLOBALS['tpl']->assign("consignee_info",$consignee_info);

					//输出一级地区

					$region_lv1_list = $GLOBALS['cache']->get("CACHE_REGION_LIST_0");
					if($region_lv1_list===false)
					{
						$region_lv1_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where pid = 0 order by name asc");
						$GLOBALS['cache']->set("CACHE_REGION_LIST_0",$region_lv1_list);
					}

					$GLOBALS['tpl']->assign("region_lv1_list",$region_lv1_list);

					//输出二级地区

					$region_lv2_list = $GLOBALS['cache']->get("CACHE_REGION_LIST_".$consignee_info['region_lv1']);
					if($region_lv2_list===false)
					{
						$region_lv2_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where pid = ".$consignee_info['region_lv1']." order by name asc");
						if(!$region_lv2_list)$region_lv2_list=array();
						$GLOBALS['cache']->set("CACHE_REGION_LIST_".$consignee_info['region_lv1'],$region_lv2_list);
					}
					$GLOBALS['tpl']->assign("region_lv2_list",$region_lv2_list);

					//输出三级地区
					$region_lv3_list = $GLOBALS['cache']->get("CACHE_REGION_LIST_".$consignee_info['region_lv2']);
					if($region_lv3_list===false)
					{
						$region_lv3_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where pid = ".$consignee_info['region_lv2']." order by name asc");
						if(!$region_lv3_list)$region_lv3_list=array();
						$GLOBALS['cache']->set("CACHE_REGION_LIST_".$consignee_info['region_lv2'],$region_lv3_list);
					}
					$GLOBALS['tpl']->assign("region_lv3_list",$region_lv3_list);

					//输出四级地区
					$region_lv4_list = $GLOBALS['cache']->get("CACHE_REGION_LIST_".$consignee_info['region_lv3']);
					if($region_lv4_list===false)
					{
						$region_lv4_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where pid = ".$consignee_info['region_lv3']." order by name asc");
						if(!$region_lv4_list)$region_lv4_list=array();
						$GLOBALS['cache']->set("CACHE_REGION_LIST_".$consignee_info['region_lv3'],$region_lv4_list);
					}
					$GLOBALS['tpl']->assign("region_lv4_list",$region_lv4_list);
				}
				else
				{
					$user_info['consignee'] = $user_info['nickname'];
					$GLOBALS['tpl']->assign("consignee_info",$user_info);

					//输出一级地区
					$region_lv1_list = $GLOBALS['cache']->get("CACHE_REGION_LIST_0");
					if($region_lv1_list===false)
					{
						$region_lv1_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where pid = 0 order by name asc");
						$GLOBALS['cache']->set("CACHE_REGION_LIST_0",$region_lv1_list);
					}
					$GLOBALS['tpl']->assign("region_lv1_list",$region_lv1_list);
				}
				$result = $GLOBALS['tpl']->fetch('Inc/goods/cart_consignee_info.tpl');
		    	$content = preg_replace("/<loader_cart_consignee_info([^>]*)>/i",$result,$content);
		    }

    		//loader_cart_delivery_list 的加载 配送列表
		    $rs = preg_match_all ("/<loader_cart_delivery_list([^>]*)>/i", $content, $loaders);
		    if($rs > 0)
		    {
			    if($consignee_info)
				{
					if($consignee_info['region_lv4']>0)
					{
						$end_region_id = $consignee_info['region_lv4'];
					}
					elseif($consignee_info['region_lv3']>0)
					{
						$end_region_id = $consignee_info['region_lv3'];
					}
					elseif($consignee_info['region_lv2']>0)
					{
						$end_region_id = $consignee_info['region_lv2'];
					}
					elseif($consignee_info['region_lv1']>0)
					{
						$end_region_id = $consignee_info['region_lv1'];
					}
				}
				else
				{
					$end_region_id = 0;
				}


				$delivery_ids = $GLOBALS['cache']->get("CACHE_DELIVERY_IDS_".$end_region_id);
				if($delivery_ids===false)
				{
						//获取支持的配送地区列表
						$delivery_ids = array();
				   		$delivery_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."delivery where status = 1");

				   		foreach($delivery_list as $v)
				   		{
				   			if($v['allow_default'] == 1&&$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."delivery_region where delivery_id = ".$v['id'])==0)
				   			{
				   				//允许默认
				   				array_push($delivery_ids,$v['id']);
				   			}
				   			else
				   			{
				   				$delivery_region = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."delivery_region where delivery_id = ".$v['id']);
				   				$tag = true; //是否未查询到
				   				foreach($delivery_region as $vv)
				   				{
				   					$region_ids = explode(",",$vv['region_ids']);
				   					$tmp_id = $end_region_id;
				   					while(intval($GLOBALS['db']->getOne("select region_level from ".DB_PREFIX."region_conf where id = ".$tmp_id))>0)
				   					{
				   						if(in_array($tmp_id,$region_ids))
				   						{
				   							array_push($delivery_ids,$v['id']);
					   						$tag = false;
					   						break;
				   						}
				   						else
				   						{
				   							$tmp_id = intval($GLOBALS['db']->getOne("select pid from ".DB_PREFIX."region_conf where id = ".$tmp_id));
				   						}
				   					}

				   				}
				   				if($tag)
				   				{
				   					if($v['allow_default'] == 1)
				   					{
				   						//允许默认
						   				array_push($delivery_ids,$v['id']);
				   					}
				   				}
				   			}

				   		}
						//end 获取结束
						$GLOBALS['cache']->set("CACHE_DELIVERY_IDS_".$end_region_id,$delivery_ids);
				}


				$delivery_list = $GLOBALS['cache']->get("CACHE_DELIVERY_LIST");
				if($delivery_list===false)
				{
						$delivery_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."delivery where status = 1");
						$GLOBALS['cache']->set("CACHE_DELIVERY_LIST",$delivery_list);
				}

				foreach($delivery_list as $k=>$v)
				{
					if(!in_array($v['id'],$delivery_ids))
					{
						unset($delivery_list[$k]);
					}
					else
					{
						$delivery_list[$k]['protect_radio'] = floatval($v['protect_radio'])."%";
						$delivery_list[$k]['protect_price'] = format_price($v['protect_price']);
					}
				}
				$GLOBALS['tpl']->assign('delivery_list',$delivery_list);
				$result = $GLOBALS['tpl']->fetch('Inc/goods/cart_delivery_list.tpl');
		    	$content = preg_replace("/<loader_cart_delivery_list([^>]*)>/i",$result,$content);
		    }

    		//loader_cart_payment 的加载 支付方式
		    $rs = preg_match_all ("/<loader_cart_payment([^>]*)>/i", $content, $loaders);
		    if($rs > 0)
		    {
		    	//支付方式
				if ($cart_item['data_total_price'] < 0){
					$isAccountpay = 1;

					$payment = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name = 'Accountpay'");
					$currency_item = $GLOBALS['cache']->get("CACHE_CURRENCY_".intval($payment['currency']));
					if($currency_item===false)
					{
						//$currency_item = M("Currency")->getById($payment['currency']);
						$currency_item = array('unit'=>eyooC2("BASE_CURRENCY_UNIT"),'radio'=>1);
						$GLOBALS['cache']->set("CACHE_CURRENCY_".intval($payment['currency']),$currency_item);
					}

			    	$payment['currency_type'] = $currency_item['name_1'];
			    	if($payment['fee_type']==0)
			    		$payment['fee_format'] = format_price($payment['fee']);
			    	else
			    		$payment['fee_format'] = floatval($payment['fee'])."%";

			    	$GLOBALS['tpl']->assign("accountpay",$payment);
					$GLOBALS['tpl']->assign("isAccountpay",$isAccountpay);
				}else{
					$isAccountpay = 0;

					$payment_list = $GLOBALS['cache']->get("CACHE_PAYMENT_LIST");
					if($payment_list===false)
					{
						 $payment_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."payment where status = 1");
						 $GLOBALS['cache']->set("CACHE_PAYMENT_LIST",$payment_list);
					}
			    	foreach($payment_list as $kk=>$vv)
			    	{
						if($vv['class_name'] == "Accountpay")
							$isAccountpay = 1;
			    		//$currency_item = M("Currency")->getById($vv['currency']);
			    		$currency_item = array('id'=>1,'unit'=>eyooC2("BASE_CURRENCY_UNIT"),'radio'=>1);
			    		$payment_list[$kk]['currency_type'] = $currency_item['name_1'];
			    		if($vv['fee_type']==0)
			    			$payment_list[$kk]['fee_format'] = format_price($vv['fee']);
			    		else
			    			$payment_list[$kk]['fee_format'] = floatval($vv['fee'])."%";
			    	}
			    	$GLOBALS['tpl']->assign("payment_list",$payment_list);
					$GLOBALS['tpl']->assign("isAccountpay",$isAccountpay);
				}
				$GLOBALS['tpl']->assign('TAX_RADIO',eyooC2("TAX_RADIO"));
				$GLOBALS['tpl']->assign('user_info_money',format_price($user_info['money']));
				$GLOBALS['tpl']->assign("PAY_SHOW_TYPE",eyooC2("PAY_SHOW_TYPE"));
				$GLOBALS['tpl']->assign("ROOT_PATH",__ROOT__);

				if (($cart_item['goods_type']==1&&$cart_item['data_total_price'] >= 0)||($cart_item['goods_type']!=1&&$cart_item['data_total_price'] > 0))
				$GLOBALS['tpl']->assign("SHOW_PAYMENT_LIST",1);
				else
				$GLOBALS['tpl']->assign("SHOW_PAYMENT_LIST",0);

				$GLOBALS['tpl']->assign("user_info",$user_info);
		    	//end 支付方式
		    	$result = $GLOBALS['tpl']->fetch('Inc/goods/cart_payment_list.tpl');
		    	$content = preg_replace("/<loader_cart_payment([^>]*)>/i",$result,$content);
		    }

    		//loader_cart_mobile_phone的加载 单价
		    $rs = preg_match_all ("/<loader_cart_mobile_phone([^>]*)>/i", $content, $loaders);
		    if($rs > 0)
		    {
		    	$GLOBALS['tpl']->assign("user_info",$user_info);
		    	$GLOBALS['tpl']->assign("lang",$GLOBALS['Ln']);
		    	$result = $GLOBALS['tpl']->fetch('Inc/goods/cart_mobile_phone.tpl');
		    	$content = preg_replace("/<loader_cart_mobile_phone([^>]*)>/i",$result,$content);
		    }

    	}
    }
	ob_gzip2($content);
	exit();
}

function ob_gzip2($content)
{
//return $content;
	header("Content-type: text/html; charset=utf-8");
    header("Cache-control: private");  //支持页面回跳
//	$gzip = $GLOBALS['db']->getOne("select val from ".DB_PREFIX."sys_conf where name ='GZIP_ON'");
	$gzip = eyooC2("GZIP_ON");
	if( $gzip==1 )
	{
		if(!headers_sent()&&extension_loaded("zlib")&&preg_match("/gzip/i",$_SERVER["HTTP_ACCEPT_ENCODING"]))
		{
			//ob_clean();
			$content = gzencode($content,9);
			header("Content-Encoding: gzip");
			header("Content-Length: ".strlen($content));
			echo $content;
		}
		else
		echo $content;
	}else{
		echo $content;
	}

}

function U2($module, $action){
	$url = 'index.php?m='.$module.'&a='.$action;
	return $url;
}

/**
 +----------------------------------------------------------
 * 字符串截取，支持中文和其他编码
 +----------------------------------------------------------
 * @static
 * @access public
 +----------------------------------------------------------
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function msubstr2($str, $start=0, $length, $charset="utf-8", $suffix=true)
{
    if(function_exists("mb_substr"))
        return mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        return iconv_substr($str,$start,$length,$charset);
    }
    $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("",array_slice($match[0], $start, $length));
    if($suffix) return $slice."…";
    return $slice;
}

    //静态化时，有些公共静态值要返回，比如：城市ID
 function getHideParam(){

	if(!empty($_REQUEST['cityname']))
	{
		$cityName = trim($_REQUEST['cityname']);
		$currentCity = $GLOBALS['db']->getRow("SELECT id,py FROM ".DB_PREFIX."group_city where py = '".$cityName."' and verify=1");
		if($currentCity){
			$cityID = $currentCity['id'];
			//Cookie::set('cityID',$currentCity['id'],60*60*24);
			//Session::set("cityID",$currentCity['id']);
			//Session::set("cityName",$currentCity['py']);
			setcookie('cityID',base64_encode(serialize($currentCity['id'])));
			$_SESSION['cityID'] = $currentCity['id'];
			$_SESSION['cityName'] = $currentCity['py'];

			return $currentCity['id'];
		}else{
			$cityID = 0;
		}
	}

	$cityID = intval($_SESSION["cityID"]);
	if($cityID > 0){
		return $cityID;
	}


	if ($cityID==0){
		$cityID = intval(unserialize(base64_decode($_COOKIE['cityID'])));
	}

 	//动态定位
 	if($cityID==0)
		{
			$ip =  get_ip();
			$iplocation = new IpLocation();

			$address=$iplocation->getaddress($ip);

			$city_list = $GLOBALS['db']->getAll("SELECT id,name FROM ".DB_PREFIX."group_city where verify = 1");
			foreach ($city_list as $city)
			{
				if(@strpos($address['area1'],$city['name']))
				{
					$cityID = $city['id'];
					break;
				}
			}
	}

	if($cityID > 0)
		$currentCity = $GLOBALS['db']->getRow("SELECT id,py FROM ".DB_PREFIX."group_city where id = $cityID and verify=1");

	if(empty($currentCity))
		$currentCity = $GLOBALS['db']->getRow("SELECT id,py FROM ".DB_PREFIX."group_city where is_defalut=1 and verify=1");

	setcookie('cityID',base64_encode(serialize($currentCity['id'])));
	$_SESSION['cityID'] = $currentCity['id'];
	$_SESSION['cityName'] = $currentCity['py'];

	return $currentCity['id'];
}

function get_ip() {
	if (getenv ( "HTTP_CLIENT_IP" ) && strcasecmp ( getenv ( "HTTP_CLIENT_IP" ), "unknown" ))
		$ip = getenv ( "HTTP_CLIENT_IP" );
	else if (getenv ( "HTTP_X_FORWARDED_FOR" ) && strcasecmp ( getenv ( "HTTP_X_FORWARDED_FOR" ), "unknown" ))
		$ip = getenv ( "HTTP_X_FORWARDED_FOR" );
	else if (getenv ( "REMOTE_ADDR" ) && strcasecmp ( getenv ( "REMOTE_ADDR" ), "unknown" ))
		$ip = getenv ( "REMOTE_ADDR" );
	else if (isset ( $_SERVER ['REMOTE_ADDR'] ) && $_SERVER ['REMOTE_ADDR'] && strcasecmp ( $_SERVER ['REMOTE_ADDR'], "unknown" ))
		$ip = $_SERVER ['REMOTE_ADDR'];
	else
		$ip = "unknown";

	return ($ip);
}
function get_domain()
{
	/* 协议 */
	$protocol = get_http();

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
/**
 * 获得 ECSHOP 当前环境的 HTTP 协议方式
 *
 * @access  public
 *
 * @return  void
 */
function get_http()
{
	return (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';
}

// 用于读取htmlcache相关数据的函数
function getGoodsStatus($id)
{
	$lang = include(ROOT_PATH.'home/Lang/'.LANG.'/xy_lang.php');
	$GLOBALS['tpl']->assign('lang',$lang);
	$result = array("dateHTML"=>"","statusHTML"=>"","btnHTML"=>"");
	//$id = intval($_REQUEST['goodsID']);
	$sql = "SELECT `id`,`name_1`,`sn`,`cate_id`,`city_id`,`suppliers_id`,`click_count`,`cost_price`,`shop_price`,`market_price`,`promote_price`,`promote_begin_time`,`promote_end_time`,`create_time`,`update_time`,`type_id`,`goods_type`,`stock`,`brief_1`,`brand_id`,`is_best`,`is_hot`,`is_new`,`status`,`sort`,`seokeyword_1`,`seocontent_1`,`goods_desc_1`,`small_img`,`big_img`,`origin_img`,`define_small_img`,`is_define_small_img`,`is_inquiry`,`weight`,`spec_type`,`weight_unit`,`score`,`web_reviews`,`goods_reviews`,`min_user_time`,`special_note`,`max_bought`,`is_group_fail`,`complete_time`,`buy_count`,`group_user`,`user_count`,`earnest_money`,`group_bond_end_time`,`expand1`,`expand2`,`expand3`,`expand4`,`u_name`,`referrals`,`close_referrals`,`goods_short_name`,`fail_buy_count`,`free_delivery_amount`,`allow_combine_delivery`,`allow_sms` FROM ".$GLOBALS['db_config']['DB_PREFIX']."goods WHERE id=$id";
	$goods = $GLOBALS['db']->getRow($sql);
	$goods['name'] = $goods['name_'.$GLOBALS['langItem']['id']];//modify by chenfq 2010-05-30 $langItem['id'] ==> $GLOBALS['langItem']['id']
	$goods['market_price'] = floatval($goods['market_price']);
	$goods['shop_price'] = floatval($goods['shop_price']);
	$goods['earnest_money'] = floatval($goods['earnest_money']);
	$goods['market_price_format'] = a_formatPrice(floatval($goods['market_price']));
	$goods['shop_price_format'] = a_formatPrice(floatval($goods['shop_price']));
	$goods['earnest_money_format'] = (floatval($goods['earnest_money']) == 0) ? '免费' :a_formatPrice(floatval($goods['earnest_money']));

	if(intval($goods['promote_end_time']) < a_gmtTime())
		$goods['is_end'] = true;

	if(intval($goods['stock']) > 0)
	{
		$goods['surplusCount'] = intval($goods['stock']) - intval($goods['buy_count']);
		if($goods['surplusCount'] <= 0)
			$goods['is_none'] = true;

		$goods['stockbfb'] = ($goods['surplusCount'] / intval($goods['stock'])) * 100;
	}

	if($goods['promote_end_time'] < a_gmtTime())
	{
		if (($goods['group_user'] >= 0 && $goods['group_user'] > $goods['buy_count']))
		{
			$goods['is_group_fail'] = 1;
			$goods['complete_time'] = a_gmtTime();
		}
		else
		{
			$goods['is_group_fail'] = 2;
			$goods['complete_time'] = a_gmtTime();
		}
	}

	if($goods['complete_time'] > 0)
		$goods['complete_time_format'] = a_toDate($goods['complete_time'],$lang['XY_TIMES_MOD_2']);
	else
		$goods['complete_time_format'] = "";

	$goods['rest_count'] = $goods['group_user'] - $goods['buy_count'];


	$GLOBALS['tpl']->assign('url',__ROOT__."/index.php?m=Cart&a=index&id=".$id);
	$GLOBALS['tpl']->assign('goods',$goods);
	$GLOBALS['tpl']->assign('cityID',intval($_SESSION['cityID']));

	$result['btnHTML'] = $GLOBALS['tpl']->fetch('Inc/goods/goods_btn_info.tpl');
	$result['dateHTML'] = $GLOBALS['tpl']->fetch('Inc/goods/goods_date_info.tpl');
	$result['statusHTML'] = $GLOBALS['tpl']->fetch('Inc/goods/goods_status_info.tpl');
	$result['tooltipHTML'] = $GLOBALS['tpl']->fetch('Inc/goods/goods_tooltip.tpl');
	//return '';

	return $result;
}



function getReferralsGoods($goodsID = 0,$uid = 0)
	{
		$db_config = $GLOBALS['db_config'];
		$curr_lang_id = 1;
		$time = a_gmtTime();
		if($goodsID == 0)
		{
			$where = " status = 1 AND promote_begin_time <= $time AND promote_end_time >= $time ";

			if($cityID == 0)
			{
				$sql = "select id from ".$db_config['DB_PREFIX']."group_city where status = 1 order by is_defalut desc,id asc limit 1";
				$cityID = $GLOBALS['db']->getOne($sql);
				$where .= " AND city_id = $cityID";
			}
			else
			{
				$where .= " AND city_id = $cityID";
			}

			$item = $GLOBALS['db']->getRow("select name_1,goods_short_name,u_name,id,brief_1 from ".$db_config['DB_PREFIX']."goods where ".$where." order by sort desc,id desc limit 1");
			//$item = $this->where($where)->field("name_1,goods_short_name,u_name,id")->order("sort desc,id desc")->find();
		}
		else{
			$item = $GLOBALS['db']->getRow("select name_1,goods_short_name,u_name,id,brief_1 from ".$db_config['DB_PREFIX']."goods where id=$goodsID and status = 1");
		}
		//dump(eyooC("URL_ROUTE"));

		if($item)
		{
//			$url_route = $GLOBALS['db']->getOne("select val from ".$db_config['DB_PREFIX']."sys_conf where name = 'URL_ROUTE'");
			$url_route = eyooC2("URL_ROUTE");
			if($url_route==1)
			{
				if($item['u_name']!='')
				{
					$item['url'] = "g-".rawurlencode($item['u_name'])."-ru-".intval($uid).".html";
					$item['share_url'] = "g-".($item['u_name'])."-ru-".intval($uid).".html";
				}
				else
				{
					$item['url'] = "tg-".$item['id']."-ru-".intval($uid).".html";
					$item['share_url'] = "tg-".$item['id']."-ru-".intval($uid).".html";
				}
			}
			else
			{
				$item['url'] = rawurlencode("index.php?m=Goods&a=show&id=".$item['id']."&ru=".intval($uid));
				$item['share_url'] = ("index.php?m=Goods&a=show&id=".$item['id']."&ru=".intval($uid));
			}
			//$mail = D("MailTemplate")->where("name = 'share'")->find();
			$mail = $GLOBALS['db']->getRow("select `id`,`name`,`mail_title`,`mail_content`,`is_html` from ".$db_config['DB_PREFIX']."mail_template where name ='share'");
			$mail['mail_title'] = str_replace('{$title}',$item['name_'.$curr_lang_id], $mail['mail_title']);
			$mail['mail_content'] = str_replace('{$title}',$item['name_'.$curr_lang_id], $mail['mail_content']);
			$item['urlgbname'] = urlencode(a_utf8ToGB($mail['mail_title']));
			$item['urlgbbody'] = urlencode(a_utf8ToGB($mail['mail_content']));
			$item['urlname'] = urlencode($item['name_'.$curr_lang_id]);
			$item['urlbrief'] = urlencode($item['brief_'.$curr_lang_id]);
		}

		//print_r($item);
		//exit;

		$urllink = a_getDomain().__ROOT__."/".$item['url'];
    	$base_urllink = a_getDomain().__ROOT__."/".$item['share_url'];

    	$tmpl_content = @file_get_contents(getcwd()."/Public/fx.html");
    	//print_r($goods);exit;
    	if($_REQUEST['m']=='Referrals'&&$_REQUEST['a']=='index')
    	$GLOBALS['tpl']->assign('is_referrals_page',1);
    	$GLOBALS['tpl']->assign('goods',$item);
    	$GLOBALS['tpl']->assign('urllink',$urllink);
    	$GLOBALS['tpl']->assign('base_urllink',$base_urllink);
		$content = $GLOBALS['tpl']->fetch_str($tmpl_content);
		$content = $GLOBALS['tpl']->_eval($content);
    	//echo $content;
		return $content;
	}
	function getGoodsItem($id)
	{
		$curr_lang_id = 1;
		$time = get_gmt_time();
		$item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."goods where id = ".$id." and status = 1");
		//dump(eyooC("URL_ROUTE"));

		if($item)
		{
			$item['update_time_format']  = a_toDate($item['update_time']);
			$item['create_time_format']  = a_toDate($item['create_time']);
			$item['promote_begin_time_format']  = a_toDate($item['promote_begin_time']);
			$item['promote_end_time_format']  = a_toDate($item['promote_end_time']);
			$item['brief'] = $item['brief_'.$curr_lang_id];
			$item['earnest_money_format'] = (floatval($item['earnest_money']) == 0) ? "免费" :formatPrice(floatval($item['earnest_money']));



			if(intval($item['promote_end_time']) <  $time)
				$item['is_end'] = true;

			if($item['market_price']>0)
			$item['discountfb'] = round(($item['shop_price'] / $item['market_price']) * 10,2);
			$item['save'] = (floatval($item['market_price'] - $item['shop_price']));
			$item['endtime'] = $item['promote_end_time'] - get_gmt_time();
			$item['user_count'] = intval($item['user_count']);
			$item['userBuyCount'] = 0;

			if(intval($_SESSION['user_id']) > 0)
			{
				$sql = "select sum(og.number) as num from ".DB_PREFIX."order_goods as og left join ".DB_PREFIX."order as o on og.order_id = o.id where og.rec_id = ".intval($item['id'])." and o.user_id=".intval($_SESSION['user_id']);
				$num = $GLOBALS['db']->getOne($sql);
				$item['userBuyCount'] = intval($num);
			}


			$item['surplusCount'] = 0;

			if(intval($item['stock']) > 0)
			{
				$item['surplusCount'] = intval($item['stock']) - intval($item['buy_count']);
				if($item['surplusCount'] <= 0)
					$item['is_none'] = true;

				$item['stockbfb'] = ($item['surplusCount'] / intval($item['stock'])) * 100;
			}




			$list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."goods_attr where goods_id=".$id." and attr_value_1 <> ''");
			if($list)
			{
				foreach($list as $k=>$v)
				{
					$vv = $v['attr_value_1'];
					$attr_type = $GLOBALS['db']->getOne("select type_id from ".DB_PREFIX."goods_type_attr where id=".$v['attr_id']);

					$param = array(
						'attr_search'	=>	array(
							$v['attr_id'] => urlencode($vv)
						)
					);
					$value_item['value'] = $vv;


					$v['value_list'][] = $value_item;
					$v['value'] = $vv;

					$result[$v['attr_id']]['attr_value'][] = $v;
				}
				foreach($result as $k=>$v)
				{
					$result[$k]['attr_info'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."goods_type_attr where id=".$k);
					$result[$k]['attr_info']['name'] = $result[$k]['attr_info']['name_1'];
				}
			}

			$item['attrlist'] = $result;
		}

		return $item;
	}
?>