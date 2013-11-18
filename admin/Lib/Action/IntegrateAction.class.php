<?php
// +----------------------------------------------------------------------
// | Fanwe 多语商城建站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:
// +----------------------------------------------------------------------

class IntegrateAction extends CommonAction{
	/**
	 * 获得所有模块的名称以及链接地址
	 *
	 * @access      public
	 * @param       string      $directory      插件存放的目录
	 * @return      array
	 */
	function read_modules($directory = '.')
	{
	    $dir         = @opendir($directory);
	    $set_modules = true;
	    $modules     = array();

	    while (false !== ($file = @readdir($dir)))
	    {
	        if (preg_match("/^.*?\.php$/", $file))
	        {
	            include_once($directory. '/' .$file);
	        }
	    }
	    @closedir($dir);
	    unset($set_modules);

	    foreach ($modules AS $key => $value)
	    {
	        ksort($modules[$key]);
	    }
	    ksort($modules);

	    return $modules;
	}

	/**
	 *  返回字符集列表数组
	 *
	 * @access  public
	 * @param
	 *
	 * @return void
	 */
	function get_charset_list()
	{
	    return array(
	        'utf8'   => 'UTF-8',
	    	'gbk' => 'GB2312/GBK',
	        'big5'   => 'BIG5',
	    );
	}

	/**
	 * 文件或目录权限检查函数
	 *
	 * @access          public
	 * @param           string  $file_path   文件路径
	 * @param           bool    $rename_prv  是否在检查修改权限时检查执行rename()函数的权限
	 *
	 * @return          int     返回值的取值范围为{0 <= x <= 15}，每个值表示的含义可由四位二进制数组合推出。
	 *                          返回值在二进制计数法中，四位由高到低分别代表
	 *                          可执行rename()函数权限、可对文件追加内容权限、可写入文件权限、可读取文件权限。
	 */
	function file_mode_info($file_path)
	{
	    /* 如果不存在，则不可读、不可写、不可改 */
	    if (!file_exists($file_path))
	    {
	        return false;
	    }

	    $mark = 0;

	    if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
	    {
	        /* 测试文件 */
	        $test_file = $file_path . '/cf_test.txt';

	        /* 如果是目录 */
	        if (is_dir($file_path))
	        {
	            /* 检查目录是否可读 */
	            $dir = @opendir($file_path);
	            if ($dir === false)
	            {
	                return $mark; //如果目录打开失败，直接返回目录不可修改、不可写、不可读
	            }
	            if (@readdir($dir) !== false)
	            {
	                $mark ^= 1; //目录可读 001，目录不可读 000
	            }
	            @closedir($dir);

	            /* 检查目录是否可写 */
	            $fp = @fopen($test_file, 'wb');
	            if ($fp === false)
	            {
	                return $mark; //如果目录中的文件创建失败，返回不可写。
	            }
	            if (@fwrite($fp, 'directory access testing.') !== false)
	            {
	                $mark ^= 2; //目录可写可读011，目录可写不可读 010
	            }
	            @fclose($fp);

	            @unlink($test_file);

	            /* 检查目录是否可修改 */
	            $fp = @fopen($test_file, 'ab+');
	            if ($fp === false)
	            {
	                return $mark;
	            }
	            if (@fwrite($fp, "modify test.\r\n") !== false)
	            {
	                $mark ^= 4;
	            }
	            @fclose($fp);

	            /* 检查目录下是否有执行rename()函数的权限 */
	            if (@rename($test_file, $test_file) !== false)
	            {
	                $mark ^= 8;
	            }
	            @unlink($test_file);
	        }
	        /* 如果是文件 */
	        elseif (is_file($file_path))
	        {
	            /* 以读方式打开 */
	            $fp = @fopen($file_path, 'rb');
	            if ($fp)
	            {
	                $mark ^= 1; //可读 001
	            }
	            @fclose($fp);

	            /* 试着修改文件 */
	            $fp = @fopen($file_path, 'ab+');
	            if ($fp && @fwrite($fp, '') !== false)
	            {
	                $mark ^= 6; //可修改可写可读 111，不可修改可写可读011...
	            }
	            @fclose($fp);

	            /* 检查目录下是否有执行rename()函数的权限 */
	            if (@rename($test_file, $test_file) !== false)
	            {
	                $mark ^= 8;
	            }
	        }
	    }
	    else
	    {
	        if (@is_readable($file_path))
	        {
	            $mark ^= 1;
	        }

	        if (@is_writable($file_path))
	        {
	            $mark ^= 14;
	        }
	    }

	    return $mark;
	}


	/**
	 *
	 *
	 * @access  public
	 * @param
	 *
	 * @return void
	 */
	function save_integrate_config ($code, $cfg)
	{
	    $sql = "SELECT COUNT(*) as number FROM ".C("DB_PREFIX")."sys_conf WHERE name = 'INTEGRATE_CODE'";
		$number = M()->query($sql);

	    if (intval($number[0]['number']) == 0)
	    {
	        $sql = "INSERT INTO ".C("DB_PREFIX")."sys_conf(name, is_show, status, val) VALUES ('INTEGRATE_CODE', 0, 1,'$code')";
	    }
	    else
	    {
	        $sql = "SELECT val FROM ".C("DB_PREFIX")."sys_conf WHERE name = 'INTEGRATE_CODE'";
	        $tmp = M()->query($sql);

	        if ($code != $tmp[0]['val'])
	        {
	            /* 有缺换整合插件，需要把积分设置也清除 */
	            $sql = "UPDATE ".C("DB_PREFIX")."sys_conf SET val = '' WHERE name = 'POINTS_RULE'";
	            M()->query($sql);
	        }
	        $sql = "UPDATE ".C("DB_PREFIX")."sys_conf SET val = '$code' WHERE name = 'INTEGRATE_CODE'";
	    }

	    M()->query($sql);

	    /* 当前的域名 */
	    if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
	    {
	        $cur_domain = $_SERVER['HTTP_X_FORWARDED_HOST'];
	    }
	    elseif (isset($_SERVER['HTTP_HOST']))
	    {
	        $cur_domain = $_SERVER['HTTP_HOST'];
	    }
	    else
	    {
	        if (isset($_SERVER['SERVER_NAME']))
	        {
	            $cur_domain = $_SERVER['SERVER_NAME'];
	        }
	        elseif (isset($_SERVER['SERVER_ADDR']))
	        {
	            $cur_domain = $_SERVER['SERVER_ADDR'];
	        }
	    }

	    /* 整合对象的域名 */
	    $int_domain = str_replace(array('http://', 'https://'), array('', ''), $cfg['integrate_url']);
	    if (strrpos($int_domain, '/'))
	    {
	        $int_domain = substr($int_domain, 0, strrpos($int_domain, '/'));
	    }

	    if ($cur_domain != $int_domain)
	    {
	        $same_domain    = true;
	        $domain         = '';

	        /* 域名不一样，检查是否在同一域下 */
	        $cur_domain_arr = explode(".", $cur_domain);
	        $int_domain_arr = explode(".", $int_domain);

	        if (count($cur_domain_arr) != count($int_domain_arr) || $cur_domain_arr[0] == '' || $int_domain_arr[0] == '')
	        {
	            /* 域名结构不相同 */
	            $same_domain = false;
	        }
	        else
	        {
	            /* 域名结构一致，检查除第一节以外的其他部分是否相同 */
	            $count = count($cur_domain_arr);

	            for ($i = 1; $i < $count; $i++)
	            {
	                if ($cur_domain_arr[$i] != $int_domain_arr[$i])
	                {
	                    $domain         = '';
	                    $same_domain    = false;
	                    break;
	                }
	                else
	                {
	                    $domain .= ".$cur_domain_arr[$i]";
	                }
	            }
	        }

	        if ($same_domain == false)
	        {
	            /* 不在同一域，设置提示信息 */
	            $cfg['cookie_domain']   = '';
	            $cfg['cookie_path']     = '/';
	        }
	        else
	        {
	            $cfg['cookie_domain']   = $domain;
	            $cfg['cookie_path']     = '/';
	        }
	    }
	    else
	    {
	        $cfg['cookie_domain']   = '';
	        $cfg['cookie_path']     = '/';
	    }


	    $sql = "SELECT COUNT(*) as number FROM ".C("DB_PREFIX")."sys_conf WHERE name = 'INTEGRATE_CONFIG'";
	    $number = M()->query($sql);
	    if (intval($number[0]['number']) == 0)
	    {
	        $sql =  "INSERT INTO ".C("DB_PREFIX")."sys_conf (name, is_show, status, val) ".
	                "VALUES ('INTEGRATE_CONFIG', 0, 1, '" . serialize($cfg) . "')";
	    }
	    else
	    {
	        $sql = "UPDATE ".C("DB_PREFIX")."sys_conf SET val='". serialize($cfg) ."' ".
	                "WHERE name='INTEGRATE_CONFIG'";
	    }

	    M()->query($sql);

	    return true;
	}

	public function index() {
		$modules = $this->read_modules(VENDOR_PATH.'integrates');

		$code = eyooC('INTEGRATE_CODE');
		//dump($code);

	    for ($i = 0; $i < count($modules); $i++)
	    {
	         $modules[$i]['installed'] = ($modules[$i]['code'] == $code) ? 1 : 0;
	    }

	    $allow_set_points = eyooC('INTEGRATE_CODE') == 'fanwe' ? 0 : 1;

	    $allow_set_points = 0;
	    $this->assign('allow_set_points',  $allow_set_points);
	    $this->assign('modules', $modules);

	    //dump($modules);

	    //assign_query_info();
	    //$this->display('integrates_list.htm');
		$this->display ();
	}

	/*------------------------------------------------------ */
	//-- 安装会员数据整合插件
	/*------------------------------------------------------ */
	public function install()
	{
	    /* 增加ucenter设置时先检测uc_client与uc_client/data是否可写 */
	    if ($_GET['code'] == 'ucenter')
	    {
	        $uc_client_dir = $this->file_mode_info(VENDOR_PATH. 'uc_client/data');
	        if ($uc_client_dir === false)
	        {
	            $this->error('uc_client目录不存在，请先把uc_client目录上传到 ThinkPHP\Vendor 目录下再进行整合');
	            //return;
	        }
	        if ($uc_client_dir < 7)
	        {
	            $this->error ('uc_client/data目录不可写，请先把uc_client/data目录权限设置为777');
	            //return;
	        }
	        //eval()

	        $a = M()->query('select user_name from '.C("DB_PREFIX").'user group by user_name having count(*) > 1');
	        if (!empty($a)){
	        	$this->error ('会员用户名不唯一，无法进行整合.<br>'.M('User')->getLastSql());
	        }
	    }
	    if ($_GET['code'] == 'fanwe')
	    {
	        D("SysConf")->where("status=1 and name='INTEGRATE_CODE'")->setField("val", "fanwe");
	        D("SysConf")->where("status=1 and name='POINTS_RULE'")->setField("val", "");
	        //clear_cache_files();

	        $this->assign ('jumpUrl', u('Integrate/index'));
	        $this->success ( '设置会员数据整合插件已经成功。');
	        return;
	    }
	    else
	    {
	    	D("User")->where("sync_flag > 0")->setField(array("sync_flag","nickname"), array("0",""));

	        $set_modules = true;
	        include_once(VENDOR_PATH."integrates/".$_GET['code'].".php");
	        $set_modules = false;

            $cfg = $modules[0]['default'];
            $cfg['integrate_url'] = "http://";

            if (empty($cfg['db_charset']))
            	$cfg['db_charset'] = 'UTF-8';

	        $this->assign('cfg',      $cfg);
	        $this->assign('save',     0);
	      	$this->assign('set_list', $this->get_charset_list());
	        $this->assign('code',     $_GET['code']);

	        //dump($this->get_charset_list());
	        $this->display();
	    }
	}

	/*------------------------------------------------------ */
	//-- 检查用户填写资料
	/*------------------------------------------------------ */
	public function check_config()
	{
	    $code = $_POST['code'];


	    dump($_POST['cfg']['uc_connect']);

		if ($_POST['cfg']['uc_connect'] == 'mysql'){
		    include_once(VENDOR_PATH."integrates/".$code.".php");
		    $_POST['cfg']['quiet'] = 1;

		    $cls_user = new $code ($_POST['cfg']);

		     //dump($_POST['cfg']);

			if ($cls_user->error)
		    {
		        if ($cls_user->error == 1)
		        {
		            $this->error('数据库地址、用户或密码不正确');
		        }
		        elseif ($cls_user->error == 2)
		        {
		            $this->error('整合论坛关键数据表不存在，你填写的信息有误');
		        }
		        elseif ($cls_user->error ==  1049)
		        {
		            $this->error('数据库不存在');
		        }
		        else
		        {
		            $this->error($cls_user->db->error());
		        }
		    }

		    if ($cls_user->db->version >= '4.1')
		    {
		        // 检测数据表字符集
		        $sql = "SHOW TABLE STATUS FROM `" . $cls_user->db_name . "` LIKE '" . $cls_user->prefix . $cls_user->user_table . "'";
		        $row = $cls_user->db->getRow($sql);

		        //dump($row);

		        if (isset($row['Collation']))
		        {
		            $db_charset = trim(substr($row['Collation'], 0, strpos($row['Collation'], '_')));

		            if ($db_charset == 'latin1')
		            {
		                if (empty($_POST['cfg']['is_latin1']))
		                {
		                    $this->error('整合数据库检测到是lantin1编码！请重新选择');
		                }
		            }
		            else
		            {
		                $user_db_charset = $_POST['cfg']['db_charset'] == 'GB2312' ? 'GBK' : $_POST['cfg']['db_charset'];
		                $user_db_charset = strtoupper((str_replace('-', '', 'UTF-8')));
		                if (!empty($_POST['cfg']['is_latin1']))
		                {
		                   $this->error('整合数据库检测到不是latin1编码！请重新选择');
		                }

		                if (strtoupper($user_db_charset) != strtoupper($db_charset))
		                {
		                    $this->error('整合数据库检测到是'.strtoupper($db_charset).' 字符集，而非'.strtoupper($user_db_charset).' 字符集');
		                }
		            }
		        }
		    }
		    // 中文检测
		    $test_str = '测试中文字符';
		    $user_db_charset = $_POST['cfg']['db_charset'] == 'GB2312' ? 'GBK' : $_POST['cfg']['db_charset'];
		    $user_db_charset = strtoupper((str_replace('-', '', 'UTF-8')));
		    if ($user_db_charset != 'UTF8')
		    {
		    	include_once(VENDOR_PATH."iconv.php");
		        $test_str = iconv('UTF8', $_POST['cfg']['db_charset']);
		    }

		    $sql = "SELECT " . $cls_user->field_name .
		           " FROM " . $cls_user->table($cls_user->user_table) .
		           " WHERE " . $cls_user->field_name . " = '$test_str'";
		    $test = $cls_user->db->query($sql, 'SILENT');

		    if (!$test)
		    {
		        $this->error('你填写的整合信息会导致严重错误，无法完成整合');
		    }
		}

	    if ($_POST['save'] == 1)
	    {
	        /* 直接保存修改 */
	    	if ($this->save_integrate_config($code, $_POST['cfg']))
	        {
	            $this->assign ('jumpUrl', U('Integrate/index'));
	            $this->success ('保存成功!');
	        }
	        else
	        {
	            $this->assign ('jumpUrl', U('Integrate/index'));
	            $this->error('保存出错!');
	        }
	    }

	    $sql = "SELECT COUNT(*) as num FROM ".C("DB_PREFIX")."user";
	    $total = M()->query($sql);
	    if (intval($total[0]['num']) == 0)
	    {
	        /* 商城没有用户时，直接保存完成整合 */
	        $this->save_integrate_config($_POST['code'], $_POST['cfg']);
	        $this->assign ('jumpUrl', U('Integrate/complete'));
	        $this->success ('恭喜您。整合成功!');
	        exit;
	    }

	    /* 检测成功临时保存论坛配置参数 */
	    $_SESSION['cfg'] = $_POST['cfg'];
	    $_SESSION['code'] = $code;

	    $size = 100;

	    $this->assign('domain', '@fanwe');
	    $this->assign('lang_total', '商城共有 '.intval($total[0]['num']).' 个用户待检查');
	    $this->assign('size', $size);
	    $this->display('check');
	}

	/* 显示整合成功信息 */
	public function complete()
	{
	    $this->assign ('jumpUrl', U('Integrate/index'));
	    $this->success ('恭喜您。整合成功!');
	}
	/*------------------------------------------------------ */
	//-- 保存UCenter设置
	/*------------------------------------------------------ */
	public function setup_ucenter()
	{
		require_once(VENDOR_PATH.'transport.php');
	    $result = array('status' => 0, 'info' => '', 'data' => '');


	    $app_type   = 'OTHER';
	    $app_name   = 'FANWE';//eyooC('SHOP_NAME'); //$db->getOne('SELECT value FROM ' . $ecs->table('shop_config') . " WHERE code = 'shop_name'");
	    $app_url    = 'http://'.$_SERVER['HTTP_HOST'].__ROOT__;
	    $app_charset = 'UTF-8';
	    $app_dbcharset = strtolower((str_replace('-', '', 'UTF-8')));
		$ucapi = trim($_REQUEST['ucapi']);

	    $ucfounderpw = trim($_REQUEST['ucfounderpw']);
	    $app_tagtemplates = 'apptagtemplates[template]='.urlencode('<a href="{url}" target="_blank">{goods_name}</a>').'&'.
	        'apptagtemplates[fields][goods_name]='.urlencode('商品名称').'&'.
	        'apptagtemplates[fields][uid]='.urlencode('用户ID').'&'.
	        'apptagtemplates[fields][username]='.urlencode('添加标签者').'&'.
	        'apptagtemplates[fields][dateline]='.urlencode('日期').'&'.
	        'apptagtemplates[fields][url]='.urlencode('商品地址').'&'.
	        'apptagtemplates[fields][image]='.urlencode('商品图片').'&'.
	        'apptagtemplates[fields][goods_price]='.urlencode('商品价格');
	    $postdata ="m=app&a=add&ucfounder=&ucfounderpw=".urlencode($ucfounderpw)."&apptype=".urlencode($app_type).
	        "&appname=".urlencode($app_name)."&appurl=".urlencode($app_url)."&appip=&appcharset=".$app_charset.
	        '&appdbcharset='.$app_dbcharset.'&apptagtemplates='.$app_tagtemplates;
	    $t = new transport;

	    $ucconfig = $t->request($ucapi.'/index.php', $postdata);
	    $ucconfig = $ucconfig['body'];
	    if(empty($ucconfig))
	    {
	        //ucenter 验证失败
	        $result['error'] = 1;
	        $result['info'] = '验证失败';

	    }
	    elseif($ucconfig == '-1')
	    {
	        //管理员密码无效
	        $result['error'] = 1;
	        $result['info'] = '创始人密码错误';
	    }
	    else
	    {
	        list($appauthkey, $appid) = explode('|', $ucconfig);
	        if(empty($appauthkey) || empty($appid))
	        {
	            //ucenter 安装数据错误
	            $result['error'] = 1;
	            $result['info'] = '安装数据错误';
	        }
	        else
	        {
	            $result['error'] = 0;
	            $result['data'] = $ucconfig;
	            $result['info'] = '服务器通信连接成功！';
	        }
	    }
		echo json_encode($result);
	}

	/*------------------------------------------------------ */
	//-- 第一次保存UCenter安装的资料
	/*------------------------------------------------------ */
	public function save_uc_config_first()
	{
	    $code = $_REQUEST['code'];

	    /**
	    include_once(VENDOR_PATH."integrates/".$code.".php");
	    $_POST['cfg']['quiet'] = 1;
	    $cls_user = new $code ($_REQUEST['cfg']);

	    if ($cls_user->error)
	    {
	        if ($cls_user->error == 1)
	        {
	            $this->error('数据库地址、用户或密码不正确');
	        }
	        elseif ($cls_user->error == 2)
	        {
	            $this->error('整合论坛关键数据表不存在，你填写的信息有误');
	        }
	        elseif ($cls_user->error ==  1049)
	        {
	            $this->error('数据库不存在');
	        }
	        else
	        {
	            sys_msg($cls_user->db->error());
	        }
	    }
*/

	    list($appauthkey, $appid, $ucdbhost, $ucdbname, $ucdbuser, $ucdbpw, $ucdbcharset, $uctablepre, $uccharset, $ucapi, $ucip) = explode('|', $_POST['ucconfig']);
	    $uc_url = !empty($ucapi)? $ucapi : trim($_REQUEST['uc_url']);
	    $cfg = array(
	                    'uc_id' => $appid,
	                    'uc_key' => $appauthkey,
	                    'uc_url' => $uc_url,
	                    'uc_ip' => '',
	                    'uc_connect' => 'post',
	                    'uc_charset' => $uccharset,
	                    'db_host' => $ucdbhost,
	                    'db_user' => $ucdbuser,
	                    'db_name' => $ucdbname,
	                    'db_pass' => $ucdbpw,
	                    'db_pre' => $uctablepre,
	                    'db_charset' => strtolower($ucdbcharset),
	                );
	    /* 增加UC语言项 */
	    //$cfg['uc_lang'] = $_LANG['uc_lang'];

	    /* 检测成功临时保存论坛配置参数 */
	    $_SESSION['cfg'] = $cfg;
	    $_SESSION['code'] = $code;

	    /* 直接保存修改 */
	    if ($_POST['save'] == 1)
	    {
	        if ($this->save_integrate_config($code, $cfg))
	        {
	            $this->assign ('jumpUrl', U('Integrate/index'));
	            $this->success ('保存成功!');
	        }
	        else
	        {
	            $this->assign ('jumpUrl', U('Integrate/index'));
	            $this->error('保存出错!');
	        }
	    }
	    M("User")->max("id");
	    //$query = M()->query("select max(id) as maxid from " . C("DB_PREFIX") . "user");
	    $user_maxid = intval(M("User")->max("id"));

	    /* 保存完成整合 */
	    $this->save_integrate_config($code, $cfg);

	    include_once(VENDOR_PATH."mysql.php");
	    $ucdb = new cls_mysql($cfg['db_host'], $cfg['db_user'], $cfg['db_pass'], $cfg['db_name'], $cfg['db_charset'], null, 1);
	    $maxuid = $this->getmaxuid($ucdb, $cfg['db_pre']."members"); //最大的uc会员ID
	    if ($maxuid < $user_maxid)
	    	$maxuid = $user_maxid;

	    $user_startid_intro = "强烈要求合并会员时，进行数据备份！";

	    /*
	    	会员合并时，方维会员ID变更规则：
			1、用户在uc中不存在，则直接插入到UC中。新方维会员ID = 新插入的UC会员ID = 旧方维会员ID + 最大的UC会员ID($maxuid)
			2、用户在uc中存在，但密码不同：新方维会员ID = 旧方维会员ID + 最大的UC会员ID($maxuid)
			3、用户在uc中存在，且密码相同：新方维会员ID = UC会员ID
	    */
		$user_startid_intro = "会员合并时，方维会员ID变更规则：<br>".
				 			  "1、用户在uc中不存在，则直接插入到UC中。新方维会员ID = 旧方维会员ID + 最大的UC会员ID($maxuid)<br>".
							  "2、用户在uc中存在，但密码不同：新方维会员ID = 旧方维会员ID + 最大的UC会员ID($maxuid)<br>".
							  "3、用户在uc中存在，且密码相同：新方维会员ID = UC会员ID";
	    $this->assign('user_startid_intro', $user_startid_intro);
	    //$this->assign('user_startid_intro', "方维会员起始ID为".$user_maxid."; UC会员起始ID为".$maxuid."。<br>如原 ID 为 888 的会员将变为 ".$maxuid."+888 的值。");
	    $this->display('uc_import');
	}


	/*------------------------------------------------------ */
	//-- 保存UCenter填写的资料
	/*------------------------------------------------------ */
	public function save_uc_config()
	{
	    $code = $_POST['code'];

	    $cfg = unserialize(eyooC('INTEGRATE_CONFIG'));
	    if ($_POST['cfg']['uc_connect'] == 'mysql'){
	    	 include_once(VENDOR_PATH."integrates/".$code.".php");
		    $_POST['cfg']['quiet'] = 1;
		    $cls_user = new $code ($_POST['cfg']);

		    if ($cls_user->error)
		    {
		        if ($cls_user->error == 1)
		        {
		            $this->error('数据库地址、用户或密码不正确');
		        }
		        elseif ($cls_user->error == 2)
		        {
		            $this->error('整合论坛关键数据表不存在，你填写的信息有误');
		        }
		        elseif ($cls_user->error == 1049)
		        {
		            $this->error('数据库不存在');
		        }
		        else
		        {
		            sys_msg($cls_user->db->error());
		        }
		    }
	    }


	    /* 合并数组，保存原值 */
	    $cfg = array_merge($cfg, $_POST['cfg']);

	    /* 直接保存修改 */
	    if ($this->save_integrate_config($code, $cfg))
	    {
	        $this->assign ('jumpUrl', U('Integrate/index'));
	        $this->success('保存成功!');
	    }
	    else
	    {
	        $this->assign ('jumpUrl', U('Integrate/index'));
	        $this->error('保存出错!');
	    }
	}


	function getmaxuid($ucdb, $db_table) {
		$query = $ucdb->query("SHOW CREATE TABLE ".$db_table);
		$data = $ucdb->fetch_array($query);
		$data = $data['Create Table'];
		//dump($data);
		if(preg_match('/AUTO_INCREMENT=(\d+?)[\s|$]/i', $data, $a)) {
			$maxid = $a[1] - 1;
		} else {
			$maxid = 0;
		}

		if ($maxid <= 0){
			$maxid = $ucdb->getOne("select max(uid) as maxid from " . $db_table);
		}

		return $maxid + 1;
	}

	public function import_user_old()
	{
		//导入前，检查会员名，是否有重复的，有重复的不能执行导入
		ini_set("memory_limit","100M");

	    $cfg = unserialize(eyooC('INTEGRATE_CONFIG'));// INTEGRATE_CONFIG $_SESSION['cfg'];

	    include_once(VENDOR_PATH."mysql.php");
	    $ucdb = new cls_mysql($cfg['db_host'], $cfg['db_user'], $cfg['db_pass'], $cfg['db_name'], $cfg['db_charset'], null, 1);

	    $maxuid = $this->getmaxuid($ucdb, $cfg['db_pre']."members"); //最大的uc会员ID
	    $user_maxid = intval(M("User")->max("id")); //最大的会员ID
	    Log::record("==================uc会员整合 begin======================");
	    Log::record("maxuid:".$maxuid);
	    Log::record("user_maxid:".$user_maxid);

	    if ($maxuid < $user_maxid){
	    	$maxuid = $user_maxid;
	    }
	    $lastuid = $maxuid;

	    Log::record("maxuid:".$maxuid);
	    Log::record("user_maxid:".$user_maxid);
	    Log::record("lastuid:".$lastuid);

	    $merge_uid = array();
	    $uc_uid = array();
	    $repeat_user = array();

	    $merge_method = 1;//1:将与UC用户名和密码相同的用户强制为同一用户

	    $item_list = M()->query("SELECT * FROM " . C("DB_PREFIX") . "user ORDER BY `id` ASC");
	    foreach ($item_list AS $data)
	    {
	        $salt = rand(100000, 999999);
	        $password = md5($data['user_pwd'].$salt); //uc口令方式：md5(md5(明文)+随机值)
	        if (strtolower($cfg['db_charset']) == 'gbk'){
	        	$data['username'] = addslashes(utf8ToGB($data['user_name']));
	        }else{
	        	$data['username'] = addslashes($data['user_name']);
	        }


	        $lastuid = $data['id'] + $maxuid;//新插入的UC会员ID = 当前方维会员ID + 最大的UC会员ID
	        $uc_userinfo = $ucdb->getRow("SELECT `uid`, `password`, `salt` FROM ".$cfg['db_pre']."members WHERE `username`='$data[username]'");
	        //dump($uc_userinfo);
	        if(!$uc_userinfo) //用户在uc中，不存在，则直接插入到UC中
	        {
	        	//新插入的UC会员ID = 当前方维会员ID + 最大的UC会员ID
	        	//dump("INSERT LOW_PRIORITY INTO ".$cfg['db_pre']."members SET uid='$lastuid', username='$data[username]', password='$password', email='$data[email]', regip='$data[last_ip]', regdate='$data[create_time]', salt='$salt'");
	            $ucdb->query("INSERT LOW_PRIORITY INTO ".$cfg['db_pre']."members SET uid='$lastuid', username='$data[username]', password='$password', email='$data[email]', regip='$data[last_ip]', regdate='$data[create_time]', salt='$salt'", 'SILENT');
	            $ucdb->query("INSERT LOW_PRIORITY INTO ".$cfg['db_pre']."memberfields SET uid='$lastuid'",'SILENT');

	            Log::record("INSERT LOW_PRIORITY INTO ".$cfg['db_pre']."members SET uid='$lastuid', username='$data[username]', password='$password', email='$data[email]', regip='$data[last_ip]', regdate='$data[create_time]', salt='$salt'");
	            Log::record("INSERT LOW_PRIORITY INTO ".$cfg['db_pre']."memberfields SET uid='$lastuid'");

	            //M()->query("UPDATE " . C("DB_PREFIX") . "user SET `id`= $lastuid "." where id = ".$data['id']);
	        }
	        else
	        {
	            if ($merge_method == 1)//1:将与UC用户名和密码相同的用户强制为同一用户
	            {
	                if (md5($data['user_pwd'].$uc_userinfo['salt']) == $uc_userinfo['password'])
	                {
	                    //$merge_uid[] = $data['id'];
	                    $uc_uid[] = array('user_id' => $data['id'],   	//旧会员ID
	                    				  'uid' => $uc_userinfo['uid']	//新会员ID
	                    				  );
	                    continue;
	                }
	            }
	            $ucdb->query("REPLACE INTO ".$cfg['db_pre']."mergemembers SET appid='".UC_APPID."', username='$data[username]'", 'SILENT');
	            Log::record("REPLACE INTO ".$cfg['db_pre']."mergemembers SET appid='".UC_APPID."', username='$data[username]'");
	            $repeat_user[] = $data;
	        }
	    }
	    $ucdb->query("ALTER TABLE ".$cfg['db_pre']."members AUTO_INCREMENT=".($lastuid + 1), 'SILENT');
	    //需要更新user_id的表
	    $up_user_table = array('user_consignee','user_incharge','user_money_log','user_score_log','user_uncharge','collect','ecv','group_bond','mail_address_list','message','order','order_goods','promote_user_card','referrals');
	    // 清空的表
	    $truncate_user_table = array('cart', 'cart_card');

	    // 更新FANWE表
	    //dump($merge_uid);
	    //dump($uc_uid);
	    /*
	    	会员合并时，方维会员ID变更规则：
			1、用户在uc中不存在，则直接插入到UC中。新方维会员ID = 新插入的UC会员ID = 旧方维会员ID + 最大的UC会员ID($maxuid)
			2、用户在uc中存在，但密码不同：新方维会员ID = 旧方维会员ID + 最大的UC会员ID($maxuid)
			3、用户在uc中存在，且密码相同：新方维会员ID = UC会员ID
	    */
	    M()->query("UPDATE " . C("DB_PREFIX") . "user SET `id`=`id`+ $maxuid ");//处理完1，2； 新方维会员ID = 旧方维会员ID + 最大的UC会员ID($maxuid)
	    //dump(M()->getLastSql());
	    Log::record(M()->getLastSql());

	    M()->query("UPDATE " . C("DB_PREFIX") . "user SET `parent_id`=`parent_id`+ $maxuid "." where parent_id > 0");//处理完1，2； 新方维会员ID = 旧方维会员ID + 最大的UC会员ID($maxuid)
	    //dump(M()->getLastSql());
	    Log::record(M()->getLastSql());
		foreach ($uc_uid as $uid)
	    {
	       //处理完1，2后，方维会员中的所有ID值，都大于【最大的UC会员ID($maxuid)】，所以可以处理等3步
	       M()->query("UPDATE " . C("DB_PREFIX"). "user SET `id`='" . $uid['uid'] . "' WHERE `id`='" . ($uid['user_id'] + $maxuid) . "'");
	       //dump(M()->getLastSql());
	       Log::record(M()->getLastSql());


	       M()->query("UPDATE " . C("DB_PREFIX"). "user SET `parent_id`='" . $uid['uid'] . "' WHERE `parent_id`='" . ($uid['user_id'] + $maxuid) . "'");
	       //dump(M()->getLastSql());
	       Log::record(M()->getLastSql());

	       //dump("UPDATE " . C("DB_PREFIX").$table . " SET `user_id`='" . $uid['uid'] . "' WHERE `user_id`='" . ($uid['user_id'] + $maxuid) . "'");
	    }

	    foreach ($up_user_table as $table)
	    {
	        M()->query("UPDATE " . C("DB_PREFIX").$table . " SET `user_id`=`user_id`+ $maxuid ");//处理完1，2； 新方维会员ID = 旧方维会员ID + 最大的UC会员ID($maxuid)
	        //dump(M()->getLastSql());
	        Log::record(M()->getLastSql());

	    	if ($table == 'referrals'){
	        	M()->query("UPDATE " . C("DB_PREFIX").$table . " SET `parent_id`=`parent_id`+ ". $maxuid . "' WHERE parent_id > 0");
	            //dump(M()->getLastSql());
	            Log::record(M()->getLastSql());
	        }

	        foreach ($uc_uid as $uid)
	        {
	        	//处理完1，2后，方维会员中的所有ID值，都大于【最大的UC会员ID($maxuid)】，所以可以处理等3步
	            M()->query("UPDATE " . C("DB_PREFIX").$table . " SET `user_id`='" . $uid['uid'] . "' WHERE `user_id`='" . ($uid['user_id'] + $maxuid) . "'");
	            //dump(M()->getLastSql());
	            Log::record(M()->getLastSql());

	            if ($table == 'referrals'){
	            	M()->query("UPDATE " . C("DB_PREFIX").$table . " SET `parent_id`='" . $uid['uid'] . "' WHERE `parent_id`='" . ($uid['user_id'] + $maxuid) . "' and parent_id > 0");
	            	//dump(M()->getLastSql());
	            	Log::record(M()->getLastSql());
	            }

	            //dump("UPDATE " . C("DB_PREFIX").$table . " SET `user_id`='" . $uid['uid'] . "' WHERE `user_id`='" . ($uid['user_id'] + $maxuid) . "'");
	        }
	    }
	    //dump($uc_uid);
	    foreach ($truncate_user_table as $table)
	    {
	    	//dump("TRUNCATE TABLE " . C("DB_PREFIX").$table);
	        M()->query("TRUNCATE TABLE " . C("DB_PREFIX").$table);
	        //dump(M()->getLastSql());
	        Log::record(M()->getLastSql());
	    }

	    Log::record("==================uc会员整合 end======================");
	    Log::save();
	    /*
	    // 保存重复的用户信息
	    if (!empty($repeat_user))
	    {
	        @file_put_contents(__ROOT__ . 'Public/repeat_user.php', json_encode($repeat_user));
	    }
	    */

	    //$result['error'] = 0;
	    //$result['message'] = '成功将会员数据导入到 UCenter';
	    //dump($result);
	    //echo json_encode($result);

		$this->assign ('jumpUrl', u('Integrate/index'));
		$this->success ('成功将会员数据导入到 UCenter');
	}

	public function import_user()
	{
		//导入前，检查会员名，是否有重复的，有重复的不能执行导入
		ini_set("memory_limit","100M");

	    $cfg = unserialize(eyooC('INTEGRATE_CONFIG'));// INTEGRATE_CONFIG $_SESSION['cfg'];
        dump($cfg);
        exit();
	    include_once(VENDOR_PATH."mysql.php");
	    $ucdb = new cls_mysql($cfg['db_host'], $cfg['db_user'], $cfg['db_pass'], $cfg['db_name'], $cfg['db_charset'], null, 1);

	    Log::record("==================uc会员整合 begin======================");

	    $merge_method = 1;//1:将与UC用户名和密码相同的用户强制为同一用户

	    $item_list = M()->query("SELECT id,user_name,user_pwd, ucenter_id, ucenter_id_tmp FROM " . C("DB_PREFIX") . "user ORDER BY `id` ASC");
	    foreach ($item_list AS $data)
	    {
	        $salt = rand(100000, 999999);
	        $password = md5($data['user_pwd'].$salt); //uc口令方式：md5(md5(明文)+随机值)
	        if (strtolower($cfg['db_charset']) == 'gbk'){
	        	$data['username'] = addslashes(utf8ToGB($data['user_name']));
	        }else{
	        	$data['username'] = addslashes($data['user_name']);
	        }

	        $uc_userinfo = $ucdb->getRow("SELECT `uid`, `password`, `salt` FROM ".$cfg['db_pre']."members WHERE `username`='$data[username]'");
	        dump($uc_userinfo);
            exit;
	        if(!$uc_userinfo) //用户在uc中，不存在，则直接插入到UC中
	        {
	            $ucdb->query("INSERT INTO ".$cfg['db_pre']."members SET username='$data[username]', password='$password', email='$data[email]', regip='$data[last_ip]', regdate='$data[create_time]', salt='$salt'", 'SILENT');
	            $lastuid = $ucdb->getOne("SELECT MAX(uid) as uid FROM ".$cfg['db_pre']."members");
	            $ucdb->query("INSERT INTO ".$cfg['db_pre']."memberfields SET uid='$lastuid'",'SILENT');

	            M()->query("UPDATE " . C("DB_PREFIX") . "user SET `ucenter_id_tmp`='" . $lastuid . "' WHERE `id`='" . $data['id'] . "'");


	            Log::record("INSERT INTO ".$cfg['db_pre']."members SET username='$data[username]', password='$password', email='$data[email]', regip='$data[last_ip]', regdate='$data[create_time]', salt='$salt'");
	            Log::record("INSERT INTO ".$cfg['db_pre']."memberfields SET uid='$lastuid'");

	            //M()->query("UPDATE " . C("DB_PREFIX") . "user SET `id`= $lastuid "." where id = ".$data['id']);
	        }
	        else
	        {
	        	M()->query("UPDATE " . C("DB_PREFIX") . "user SET `ucenter_id_tmp`='" . $uc_userinfo['uid'] . "' WHERE `id`='" . $data['id'] . "'");
	        	/*
	            if ($merge_method == 1)//1:将与UC用户名和密码相同的用户强制为同一用户
	            {
	                if (md5($data['user_pwd'].$uc_userinfo['salt']) == $uc_userinfo['password'])
	                {
	                    //$merge_uid[] = $data['id'];
	                    $uc_uid[] = array('user_id' => $data['id'],   	//旧会员ID
	                    				  'uid' => $uc_userinfo['uid']	//新会员ID
	                    				  );
	                    continue;
	                }
	            }
	            */
	            $ucdb->query("REPLACE INTO ".$cfg['db_pre']."mergemembers SET appid='".UC_APPID."', username='$data[username]'", 'SILENT');
	            Log::record("REPLACE INTO ".$cfg['db_pre']."mergemembers SET appid='".UC_APPID."', username='$data[username]'");
	        }
	    }

		M()->query("UPDATE " . C("DB_PREFIX") . "user SET `ucenter_id`= ucenter_id_tmp");

	    Log::record("==================uc会员整合 end======================");
	    Log::save();


		$this->assign ('jumpUrl', u('Integrate/index'));
		$this->success ('成功将会员数据导入到 UCenter');
	}

	/*------------------------------------------------------ */
	//-- 用户重名检查
	/*------------------------------------------------------ */
	public function check_user()
	{
	    $code = $_SESSION['code'];
	    include_once(VENDOR_PATH."integrates/".$code.".php");
	    $cls_user = new $code ($_SESSION['cfg']);

	    $start = empty($_GET['start']) ? 0 : intval($_GET['start']);
	    $size = empty($_GET['size']) ? 100 : intval($_GET['size']);
	    $method = empty($_GET['method']) ? 1 : intval($_GET['method']);
	    $domain = empty($_GET['domain']) ? '@fanwe' : trim($_GET['domain']);
	    if ($size <2)
	    {
	        $size = 2;
	    }
	    $_SESSION['domain'] = $domain;

	    $sql = "SELECT COUNT(*) as num FROM " . C("DB_PREFIX") . "user";
	    $total = M()->query($sql);
		$total = intval($total[0]['num']);

	    $result = array('error'=>0, 'message'=>'', 'start'=>0, 'size'=>$size, 'content'=>'','method'=>$method, 'domain'=>$domain, 'is_end'=>0);

	    $sql = "SELECT user_name FROM " . C("DB_PREFIX") . "user LIMIT $start, $size";
	    $user_list = getCol($sql, 'user_name');
	    $post_user_list = $cls_user->test_conflict($user_list);
	    //dump($post_user_list);

	    if ($post_user_list)
	    {
	        /* 标记重名用户 */
	        if ($method == 2)
	        {
	            $sql = "UPDATE " . C("DB_PREFIX") . "user SET sync_flag = '$method', nickname = CONCAT(user_name, '$domain') WHERE " . db_create_in($post_user_list, 'user_name');
	        }
	        else
	        {
	            $sql = "UPDATE " . C("DB_PREFIX") . "user SET sync_flag = '$method' WHERE " . db_create_in($post_user_list, 'user_name');
	        }


	        if ($method == 2 )
	        {
	            /* 需要改名,验证是否能成功改名 */
	            $count = count($post_user_list);
	            $test_user_list = array();
	            for ($i=0; $i<$count; $i++)
	            {
	                $test_user_list[] = $post_user_list[$i] . $domain;
	            }
	            /* 检查改名后用户是否和论坛用户有重名 */
	            $error_user_list = $cls_user->test_conflict($test_user_list);   //检查
	            if ($error_user_list)
	            {
	                $domain_len = 0 - str_len($domain);
	                $count = count($error_user_list);
	                for ($i=0; $i < $count; $i++)
	                {
	                    $error_user_list[$i] = substr($error_user_list[$i], 0, $domain_len);
	                }
	                /* 将用户标记为改名失败 */
	                $sql = "UPDATE " . C("DB_PREFIX") . "user SET sync_flag = '1' WHERE " . db_create_in($error_user_list, 'user_name');
	                M()->query($sql);
	            }

	            /* 检查改名后用户是否与商城用户重名 */
	            $sql = "SELECT user_name FROM " .C("DB_PREFIX") . "user WHERE " . db_create_in($test_user_list, 'user_name');
	            $error_user_list = getCol($sql, 'user_name');
	            if ($error_user_list)
	            {
	                $domain_len = 0 - str_len($domain);
	                $count = count($error_user_list);
	                for ($i=0; $i < $count; $i++)
	                {
	                    $error_user_list[$i] = substr($error_user_list[$i], 0, $domain_len);

	                }
	                /* 将用户标记为改名失败 */
	                $sql = "UPDATE " .C("DB_PREFIX") . "user SET sync_flag = '1' WHERE " . db_create_in($error_user_list, 'user_name');
	                M()->query($sql);
	            }
	        }
	    }

	    if (($start + $size) < $total)
	    {
	        $result['start'] = $start + $size;
	        $result['content'] = sprintf('已经检查 %s / %s ', $result['start'], $total);
	    }
	    else
	    {
	        $start = $total;
	        $result['content'] = '检查完成';
	        $result['is_end'] = 1;

	        /* 查找有无重名用户,无重名用户则直接同步，有则查看重名用户 */
	        $sql = "SELECT COUNT(*) as num FROM " . C("DB_PREFIX") . "user WHERE sync_flag > 0 ";
	        $total = M()->query($sql);
	        if (intval($total[0]['num']) > 0)
	        {
	            $result['href'] = U('Integrate/modify');
	        }
	        else
	        {
	            $result['href'] = U('Integrate/sync');
	        }
	    }
	    echo json_encode($result);
	}

	/*------------------------------------------------------ */
	//-- 重名用户处理
	/*------------------------------------------------------ */
	public function modify()
	{
	    /* 检查是否有改名失败的用户 */
	    $sql = "SELECT COUNT(*) as num FROM " . C("DB_PREFIX") . "user WHERE sync_flag = 1";
	    $total = M()->query($sql);
	    if (intval($total[0]['num']) > 0)
	    {
	        $_REQUEST['flag'] = 1;
	        $this->assign('default_flag', 1);
	    }
	    else
	    {
	        $_REQUEST['flag'] = 0;
	        $this->assign('default_flag', 0);
	    }

	    /* 显示重名用户及处理方法 */
	    $flags = array(0=>$_LANG['all_user'], 1=>$_LANG['error_user'], 2=>$_LANG['rename_user'], 3=>$_LANG['delete_user'], 4=>$_LANG['ignore_user'] );
	    $this->assign('flags',      $flags);

	    $arr = conflict_userlist();

	    $this->assign('domain',       '@fanwe');
	    $this->assign('list',         $arr['list']);
	    $this->assign('filter',       $arr['filter']);
	    $this->assign('record_count', $arr['record_count']);
	    $this->assign('page_count',   $arr['page_count']);
	    $this->assign('full_page',    1);

	    $this->display('integrates_modify.htm');
	}


	/*------------------------------------------------------ */
	//-- 重名用户处理过程
	/*------------------------------------------------------ */
	public function act_modify()
	{
	    /* 先处理要改名的用户，改名用户要先检查是否有重名情况，有则标记出来 */
	    $alias = array();
	    foreach ($_POST['opt'] AS $user_id=>$val)
	    {
	        if ($val = 2)
	        {
	            $alias[] = $_POST['alias'][$user_id];
	        }
	    }
	    if ($alias)
	    {
	        /* 检查改名后用户名是否会重名 */
	        $sql = 'SELECT user_name FROM ' . $GLOBALS['ecs']->table('users') . ' WHERE ' . db_create_in($alias, 'user_name');
	        $ecs_error_list = $db->getCol($sql);

	        /* 检查和商城是否有重名 */
	        $code = $_SESSION['code'];
	        include_once(ROOT_PATH."includes/modules/integrates/".$code.".php");
	        $cls_user = new $code ($_SESSION['cfg']);

	        $bbs_error_list = $cls_user->test_conflict($alias);

	        $error_list = array_unique(array_merge($ecs_error_list, $bbs_error_list));

	        if ($error_list)
	        {
	            /* 将重名用户标记 */
	            foreach ($_POST['opt'] AS $user_id=>$val)
	            {
	                if ($val = 2)
	                {
	                    if (in_array($_POST['alias'][$user_id], $error_list))
	                    {
	                        /* 重名用户，需要标记 */
	                        $sql = "UPDATE " . $GLOBALS['ecs']->table('users') . " SET flag = 1,  alias='' WHERE user_id = '$user_id'";
	                    }
	                    else
	                    {
	                        /* 用户名无重复，可以正常改名 */
	                        $sql = "UPDATE " . $GLOBALS['ecs']->table('users').
	                         " SET flag = 2, alias = '" . $_POST['alias'][$user_id] . "'".
	                         " WHERE user_id = '$user_id'";
	                    }
	                    $db->query($sql);
	                }
	            }
	        }
	        else
	        {
	            /* 处理没有重名的情况 */
	            foreach ($_POST['opt'] AS $user_id=>$val)
	            {
	                $sql = "UPDATE " . $GLOBALS['ecs']->table('users').
	                       " SET flag = 2, alias = '" . $_POST['alias'][$user_id] . "'".
	                       " WHERE user_id = '$user_id'";
	                $db->query($sql);
	            }
	        }
	    }

	    /* 处理删除和保留情况 */
	    foreach ($_POST['opt'] as $user_id=>$val)
	    {
	        if ($val == 3 || $val == 4)
	        {
	            $sql = "UPDATE " . $GLOBALS['ecs']->table('users') . " SET flag='$val' WHERE user_id='$user_id'";
	            $db->query($sql);
	        }
	    }

	    /* 跳转  */
	    ecs_header("Location: integrate.php?act=modify");
	    exit;
	}



	/*------------------------------------------------------ */
	//-- 将商城数据同步到论坛
	/*------------------------------------------------------ */
	public function sync()
	{
	    $size = 100;
	    $total = $db->getOne("SELECT COUNT(*) FROM " . $ecs->table("users"));
	    $task_del = $db->getOne("SELECT COUNT(*) FROM " . $ecs->table("users") . " WHERE flag = 3");
	    $task_rename = $db->getOne("SELECT COUNT(*) FROM " . $ecs->table("users") . " WHERE flag = 2");
	    $task_ignore = $db->getOne("SELECT COUNT(*) FROM " . $ecs->table("users") . " WHERE flag = 4");
	    $task_sync = $total - $task_del - $task_ignore;

	    $_SESSION['task'] = array('del'=>array('total'=>$task_del, 'start'=>0), 'rename'=>array('total'=>$task_rename, 'start'=>0), 'sync'=>array('total'=>$task_sync, 'start'=>0));

	    $del_list    = "";
	    $rename_list = "";
	    $ignore_list = "";

	    $tasks = array();
	    if ($task_del > 0)
	    {
	        $tasks[] = array('task_name'=>sprintf($_LANG['task_del'], $task_del),'task_status'=>'<span id="task_del">' . $_LANG['task_uncomplete'] . '<span>');
	        $sql = "SELECT user_name FROM " . $ecs->table('users') . " WHERE flag = 2";
	        $del_list = $db->getCol($sql);
	    }

	    if ($task_rename > 0)
	    {
	        $tasks[] = array('task_name'=>sprintf($_LANG['task_rename'], $task_rename),'task_status'=>'<span id="task_rename">' . $_LANG['task_uncomplete'] . '</span>');
	        $sql = "SELECT user_name, alias FROM " . $ecs->table('users') . " WHERE flag = 3";
	        $rename_list = $db->getAll($sql);
	    }

	    if ($task_ignore >0)
	    {
	        $sql = "SELECT user_name FROM " . $ecs->table('users') . " WHERE flag = 4";
	        $ignore_list = $db->getCol($sql);
	    }

	    if ($task_sync > 0)
	    {
	         $tasks[] = array('task_name'=>sprintf($_LANG['task_sync'], $task_sync),'task_status'=>'<span id="task_sync">' . $_LANG['task_uncomplete'] . '</span>');
	    }

	    $tasks[] = array('task_name'=>$_LANG['task_save'],'task_status'=>'<span id="task_save">' . $_LANG['task_uncomplete'] . '</span>');

	    /* 保存修改日志 */
	    $fp = @fopen(ROOT_PATH . DATA_DIR . '/integrate_' . $_SESSION['code'] . '_log.php', 'wb');
	    $log = '';
	    if (isset($del_list))
	    {
	        $log .= '$del_list=' . var_export($del_list,true) . ';';
	    }
	    if (isset($rename_list))
	    {
	        $log .= '$rename_list=' . var_export($rename_list, true) . ';';
	    }
	    if (isset($ignore_list))
	    {
	        $log .= '$ignore_list=' . var_export($ignore_list, true) . ';';
	    }
	    fwrite($fp, $log);
	    fclose($fp);

	    $smarty->assign('tasks', $tasks);
	    $smarty->assign('ur_here',$_LANG['user_sync']);
	    $smarty->assign('size', $size);
	    $smarty->display('integrates_sync.htm');
	}


	/*------------------------------------------------------ */
	//-- 完成任务
	/*------------------------------------------------------ */
	public function task()
	{
	    if (empty($_GET['size']) || $_GET['size'] < 0)
	    {
	        $size = 100;
	    }
	    else
	    {
	        $size = intval($_GET['size']);
	    }

	    include_once(ROOT_PATH . 'includes/cls_json.php');
	    $json = new JSON();
	    $result = array('message'=>'', 'error'=>0, 'content'=>'', 'id'=>'', 'end'=>0, 'size'=>$size);

	    if ($_SESSION['task']['del']['start'] < $_SESSION['task']['del']['total'])
	    {
	        /* 执行操作 */
	        /* 查找要删除用户 */
	        $arr = $db->getCol("SELECT user_name FROM " . $ecs->table('users') . " WHERE flag = 3 LIMIT " . $_SESSION['task']['del']['start'] . ',' . $result['size']);
	        $db->query("DELETE FROM " . $ecs->table('users') . " WHERE " . db_create_in($arr,'user_name'));

	        /* 保存设置 */
	        $result['id'] = 'task_del';
	        if ($_SESSION['task']['del']['start'] + $result['size'] >= $_SESSION['task']['del']['total'])
	        {
	            $_SESSION['task']['del']['start'] = $_SESSION['task']['del']['total'];
	            $result['content'] = $_LANG['task_complete'];
	        }
	        else
	        {
	            $_SESSION['task']['del']['start'] += $result['size'];
	            $result['content'] = sprintf($_LANG['task_run'], $_SESSION['task']['del']['start'], $_SESSION['task']['del']['total']);
	        }

	        die($json->encode($result));
	    }
	    else if ($_SESSION['task']['rename']['start'] < $_SESSION['task']['rename']['total'])
	    {
	        /* 查找要改名用户 */
	        $arr = $db->getCol("SELECT user_name FROM " . $ecs->table('users') . " WHERE flag = 2 LIMIT " . $_SESSION['task']['del']['start'] . ',' . $result['size']);
	        $db->query("UPDATE " . $ecs->table('users') . " SET user_name=alias, alias='' WHERE " . db_create_in($arr,'user_name'));

	        /* 保存设置 */
	        $result['id'] = 'task_rename';
	        if ($_SESSION['task']['rename']['start'] + $result['size'] >= $_SESSION['task']['rename']['total'])
	        {
	            $_SESSION['task']['rename']['start'] = $_SESSION['task']['rename']['total'];
	            $result['content'] = $_LANG['task_complete'];
	        }
	        else
	        {
	            $_SESSION['task']['rename']['start'] += $result['size'];
	            $result['content'] = sprintf($_LANG['task_run'], $_SESSION['task']['rename']['start'], $_SESSION['task']['rename']['total']);
	        }
	        die($json->encode($result));
	    }
	    else if ($_SESSION['task']['sync']['start'] < $_SESSION['task']['sync']['total'])
	    {
	        $code = $_SESSION['code'];
	        include_once(ROOT_PATH."includes/modules/integrates/".$code.".php");
	        $cls_user = new $code ($_SESSION['cfg']);
	        $cls_user->need_sync = false;

	        $sql = "SELECT user_name, password, email, sex, birthday, reg_time ".
	                "FROM " . $ecs->table('users') . " LIMIT " . $_SESSION['task']['del']['start'] . ',' . $result['size'];
	        $arr = $db->getAll($sql);
	        foreach ($arr as $user)
	        {
	            @$cls_user->add_user($user['user_name'], '', $user['email'], $user['sex'], $user['birthday'], $user['reg_time'], $user['password']);
	        }

	        /* 保存设置 */
	        $result['id'] = 'task_sync';
	        if ($_SESSION['task']['sync']['start'] + $result['size'] >= $_SESSION['task']['sync']['total'])
	        {
	            $_SESSION['task']['sync']['start'] = $_SESSION['task']['sync']['total'];
	            $result['content'] = $_LANG['task_complete'];
	        }
	        else
	        {
	            $_SESSION['task']['sync']['start'] += $result['size'];
	            $result['content'] = sprintf($_LANG['task_run'], $_SESSION['task']['sync']['start'], $_SESSION['task']['sync']['total']);
	        }
	        die($json->encode($result));
	    }
	    else
	    {
	        /* 记录合并用户 */

	        /* 插入code到shop_config表 */
	        $sql = "SELECT COUNT(*) FROM " .$ecs->table('shop_config'). " WHERE code = 'integrate_code'";

	        if ($db->GetOne($sql) == 0)
	        {
	            $sql = "INSERT INTO " .$ecs->table('shop_config'). " (code, value) ".
	                    "VALUES ('integrate_code', '$_SESSION[code]')";
	        }
	        else
	        {
	            $sql = "UPDATE " .$ecs->table('shop_config'). " SET value = '$_SESSION[code]' WHERE code = 'integrate_code'";
	        }
	        $db->query($sql);

	        /* 序列化设置信息，并保存到数据库 */
	        save_integrate_config($_SESSION['code'], $_SESSION['cfg']);

	        $result['content'] = $_LANG['task_complete'];
	        $result['id'] = 'task_save';
	        $result['end'] = 1;

	        /* 清理多余信息 */
	        unset($_SESSION['cfg']);
	        unset($_SESSION['code']);
	        unset($_SESSION['task']);
	        unset($_SESSION['domain']);
	        $sql = "UPDATE " . $ecs->table('users') . " set flag = 0, alias = '' WHERE flag > 0";
	        $db->query($sql);
	        die($json->encode($result));
	    }
	}
	/*------------------------------------------------------ */
	//-- 设置会员数据整合插件
	/*------------------------------------------------------ */

	public function setup()
	{

	    if ($_GET['code'] == 'fanwe')
	    {
	        $this->assign ('jumpUrl', U('Integrate/index'));
	        $this->error('当您采用FANWE会员系统时，无须进行设置。');
	    }
	    else
	    {
	        $cfg = unserialize(eyooC('INTEGRATE_CONFIG'));
	        $this->assign('save', 1);
	        $this->assign('set_list', $this->get_charset_list());
	        $this->assign('code',     $_GET['code']);
	        $this->assign('cfg',      $cfg);
	        //dump($this->get_charset_list());
	        $this->display('install');
	    }
	}
}
?>
