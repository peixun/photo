<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */
if (! defined ( 'THINK_PATH' ))
	exit ();

	///echo 'ok';
require './global/common.php';

function getTabeSize($a, $b)
{
	return byte_format ( $a + $b );
}
function byte_format($size, $dec = 2)
{
	$a = array ("B", "KB", "MB", "GB", "TB", "PB" );
	$pos = 0;
	while ( $size >= 1024 )
	{
		$size /= 1024;
		$pos ++;
	}
	return round ( $size, $dec ) . " " . $a [$pos];
}
//公共函数


function sysConfL($key)
{
	if (preg_match ( "/TITLE_DEFAULT_LANG_/", $key, $res ))
	{
		$key = str_replace ( "TITLE_DEFAULT_LANG_", "", $key );
		return $key;
	}
	return L ( $key );
}
function get_client_ip()
{
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

// 缓存文件
function cmssavecache($name = '', $fields = '')
{
	$Model = D ( $name );
	$list = $Model->select ();
	$data = array ();
	foreach ( $list as $key => $val )
	{
		if (empty ( $fields ))
		{
			$data [$val [$Model->getPk ()]] = $val;
		} else
		{
			// 获取需要的字段
			if (is_string ( $fields ))
			{
				$fields = explode ( ',', $fields );
			}
			if (count ( $fields ) == 1)
			{
				$data [$val [$Model->getPk ()]] = $val [$fields [0]];
			} else
			{
				foreach ( $fields as $field )
				{
					$data [$val [$Model->getPk ()]] [] = $val [$field];
				}
			}
		}
	}
	$savefile = cmsgetcache ( $name );
	// 所有参数统一为大写
	$content = "<?php\nreturn " . var_export ( array_change_key_case ( $data, CASE_UPPER ), true ) . ";\n?>";
	file_put_contents ( $savefile, $content );
}

function cmsgetcache($name = '')
{
	return DATA_PATH . '~' . strtolower ( $name ) . '.php';
}
function getStatus($status, $imageShow = true)
{
	switch ($status)
	{
		case 0 :
			$showText = L ( "FORBID" );
			$showImg = '<IMG SRC="' . APP_TMPL_PATH . '/ThemeFiles/Images/locked.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="' . L ( "FORBID" ) . '">';
			break;

		case 1 :
			$showText = L ( "NORMAL" );
			$showImg = '<IMG SRC="' . APP_TMPL_PATH . '/ThemeFiles/Images/ok.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="' . L ( "NORMAL" ) . '">';
			break;
	}
	return ($imageShow === true) ? $showImg : $showText;

}

function getMailStatus($status)
{
	switch ($status)
	{
		case 0 :
			$showText = L ( "MAIL_STATUS_0" );
			break;

		case 1 :
			$showText = L ( "MAIL_STATUS_1" );
			break;
	}
	return $showText;

}

function IP($ip = '', $file = 'UTFWry.dat')
{
	$_ip = array ();
	if (isset ( $_ip [$ip] ))
	{
		return $_ip [$ip];
	} else
	{
		import ( "ORG.Net.IpLocation" );
		$iplocation = new IpLocation ( $file );
		$location = $iplocation->getlocation ( $ip );
		$_ip [$ip] = $location ['country'] . $location ['area'];
	}
	return $_ip [$ip];
}

function getNodeName($id)
{
	if (Session::is_set ( 'nodeNameList' ))
	{
		$name = Session::get ( 'nodeNameList' );
		return $name [$id];
	}
	$Group = D ( "Node" );
	$list = $Group->getField ( 'id,name' );
	$name = $list [$id];
	Session::set ( 'nodeNameList', $list );
	return $name;
}

function showStatus($status, $id)
{
	switch ($status)
	{
		case 0 :
			$info = '<a href="javascript:resume(' . $id . ')">' . L ( "RESUME" ) . '</a>';
			break;
		case 2 :
			$info = '<a href="javascript:pass(' . $id . ')">' . L ( "PASS" ) . '</a>';
			break;
		case 1 :
			$info = '<a href="javascript:forbid(' . $id . ')">' . L ( "FORBID" ) . '</a>';
			break;
		case - 1 :
			$info = '<a href="javascript:recycle(' . $id . ')">' . L ( "RECYCLE" ) . '</a>';
			break;
	}
	return $info;
}

function showActive($status, $id)
{
	switch ($status)
	{
		case 2 :
			$info = '<a href="javascript:resumeActive(' . $id . ')">' . L ( "RESUME" ) . '</a>';
			break;
		case 0 :
			$info = '<a href="javascript:passActive(' . $id . ')">' . L ( "PASS" ) . '</a>';
			break;
		case 1 :
			$info = '<a href="javascript:forbidActive(' . $id . ')">' . L ( "FORBID" ) . '</a>';
			break;
		case - 1 :
			$info = '<a href="javascript:recycleActive(' . $id . ')">' . L ( "RECYCLE" ) . '</a>';
			break;
	}
	return $info;
}

function showStatusJq($status, $id)
{
	switch ($status)
	{
		case 0 :
			$info = '<a href="javascript:resumeJq(' . $id . ')">' . L ( "RESUME" ) . '</a>';
			break;
		case 2 :
			$info = '<a href="javascript:passJq(' . $id . ')">' . L ( "PASS" ) . '</a>';
			break;
		case 1 :
			$info = '<a href="javascript:forbidJq(' . $id . ')">' . L ( "FORBID" ) . '</a>';
			break;
		case - 1 :
			$info = '<a href="javascript:recycleJq(' . $id . ')">' . L ( "RECYCLE" ) . '</a>';
			break;
	}
	return $info;
}

function getInputType($status)
{
	switch ($status)
	{
		case 0 :
			$info = L ( "INPUT_TYPE_0" );
			break;
		case 1 :
			$info = L ( "INPUT_TYPE_1" );
			break;
	}
	return $info;
}

/**
 +----------------------------------------------------------
 * 获取登录验证码 默认为4位数字
 +----------------------------------------------------------
 * @param string $fmode 文件名
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function build_verify($length = 4, $mode = 1)
{
	return rand_string ( $length, $mode );
}

function sort_by($array, $keyname = null, $sortby = 'asc')
{
	$myarray = $inarray = array ();
	# First store the keyvalues in a seperate array
	foreach ( $array as $i => $befree )
	{
		$myarray [$i] = $array [$i] [$keyname];
	}
	# Sort the new array by
	switch ($sortby)
	{
		case 'asc' :
			# Sort an array and maintain index association...
			asort ( $myarray );
			break;
		case 'desc' :
		case 'arsort' :
			# Sort an array in reverse order and maintain index association
			arsort ( $myarray );
			break;
		case 'natcasesor' :
			# Sort an array using a case insensitive "natural order" algorithm
			natcasesort ( $myarray );
			break;
	}
	# Rebuild the old array
	foreach ( $myarray as $key => $befree )
	{
		$inarray [] = $array [$key];
	}
	return $inarray;
}

/**
	 +----------------------------------------------------------
 * 产生随机字串，可用来自动生成密码
 * 默认长度6位 字母和数字混合 支持中文
	 +----------------------------------------------------------
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
	 +----------------------------------------------------------
 * @return string
	 +----------------------------------------------------------
 */
function rand_string($len = 6, $type = '', $addChars = '')
{
	$str = '';
	switch ($type)
	{
		case 0 :
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
			break;
		case 1 :
			$chars = str_repeat ( '0123456789', 3 );
			break;
		case 2 :
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
			break;
		case 3 :
			$chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
			break;
		default :
			// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
			$chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
			break;
	}
	if ($len > 10)
	{ //位数过长重复字符串一定次数
		$chars = $type == 1 ? str_repeat ( $chars, $len ) : str_repeat ( $chars, 5 );
	}
	if ($type != 4)
	{
		$chars = str_shuffle ( $chars );
		$str = substr ( $chars, 0, $len );
	} else
	{
		// 中文随机字
		for($i = 0; $i < $len; $i ++)
		{
			$str .= msubstr ( $chars, floor ( mt_rand ( 0, mb_strlen ( $chars, 'utf-8' ) - 1 ) ), 1 );
		}
	}
	return $str;
}
function pwdHash($password, $type = 'md5')
{
	return md5 ( $password );
}

function checkUrl($url)
{
	if (strtolower ( substr ( $url, 0, 7 ) ) == "http://")
		return TRUE;
	else
		return false;
}
function gtZero($id)
{
	return $id > 0;
}


/**
 * 将一个用户自定义时区的日期转为GMT时间戳
 *
 * @access  public
 * @param   string      $str
 *
 * @return  integer
 */
function localStrToTime($str)
{
	$timezone = intval ( eyooC ( 'TIME_ZONE' ) );
	//$timezone = 8;
	$time = strtotime ( $str ) - $timezone * 3600;
	return $time;
}

/**
 * 当天的最大日期数字值
 *
 * @param unknown_type $str
 * @return unknown
 */
function localStrToTimeMax($str)
{
	if ($str != '')
	{
		$str = date ( "Y-m-d H:i:s", strtotime ( $str ) );
		return localStrToTime ( $str );
	} else
	{
		return 0;
	}
}

/**
 * 当天的最小日期数字值
 *
 * @param unknown_type $str
 * @return unknown
 */
function localStrToTimeMin($str)
{
	if ($str != '')
	{
		$str = date ( "Y-m-d H:i:s", strtotime ( $str ) );
		return localStrToTime ( $str );
	} else
	{
		return 0;
	}

}

/**
 * 将日期数字 返回成日期格式
 *
 * @param unknown_type $str
 * @return unknown
 */
function timeToLocalStr($time, $format = 'Y-m-d H:i:s')
{
	return toDate ( $time, $format );
}


function checkDateFormat($dateStr)
{
	if (preg_match ( "/\b\d{4}-\d{2}-\d{2}\b/i", $dateStr ) == 1)
		return true;
	else
		return false;
}

function parseToTimeSpan($dateStr)
{
	if ($dateStr)
	{
		$dataArr = explode ( "-", $dateStr );
		return mktime ( 0, 0, 0, intval ( $dataArr [1] ), intval ( $dataArr [2] ), intval ( $dataArr [0] ) );
	} else
	{
		return 0;
	}
}

function parseToTimeSpanFull($dateStr)
{
	if ($dateStr)
	{
		$arr = explode ( " ", $dateStr );
		$dataArr = explode ( "-", $arr [0] );
		$timeArr = explode ( ":", $arr [1] );

		return mktime ( intval ( $timeArr [0] ), intval ( $timeArr [1] ), intval ( $timeArr [2] ), intval ( $dataArr [1] ), intval ( $dataArr [2] ), intval ( $dataArr [0] ) );
	} else
	{
		return 0;
	}
}

function getArticleCateType($type)
{
	switch ($type)
	{
		case '0' :
			return L ( "ARTICLE_CATE_TYPE_0" );
		case '1' :
			return L ( "ARTICLE_CATE_TYPE_1" );
		case '2' :
			return L ( "ARTICLE_CATE_TYPE_2" );
		case '3' :
			return L ( "ARTICLE_CATE_TYPE_3" );
		case '4' :
			return L ( "ARTICLE_CATE_TYPE_4" );
	}
}



function priceFormat($num)
{
	return eyooC ( "BASE_CURRENCY_UNIT" ) . number_format ( round ( $num, 2 ), 2 );
}
function priceVal($num)
{
	return str_replace ( ",", "", number_format ( round ( $num, 2 ), 2 ) );
}
function getRegionName($arr)
{
	return $arr ['name'];
}
function getNavType($type)
{
	switch ($type)
	{
		case '1' :
			return L ( 'NAV_TYPE_1' );
		case '2' :
			return L ( 'NAV_TYPE_2' );
		case '3' :
			return L ( 'NAV_TYPE_3' );

	}
}
function getLinkType($type)
{
	switch ($type)
	{
		case '1' :
			return L ( 'LINK_TYPE_1' );
		case '2' :
			return L ( 'LINK_TYPE_2' );
		case '0' :
			return L ( 'LINK_TYPE_0' );

	}
}

function getLogResult($rs)
{
	if ($rs == 1)
		return L ( 'LOG_SUCCESS' );
	else
		return L ( 'LOG_FAILED' );
}

function getLang($var)
{
	return L ( "LOG_" . $var );
}

function getAuthType($type)
{
	switch ($type)
	{
		case '1' :
			return L ( 'AUTH_TYPE_1' );
		case '2' :
			return L ( 'AUTH_TYPE_2' );
		case '0' :
			return L ( 'AUTH_TYPE_0' );

	}
}
function getNode($arr, $field)
{
	if ($field == "auth_type")
	{
		return getAuthType ( $arr [$field] );
	} else
		return $arr [$field];
}
function getAdvType($type)
{
	switch ($type)
	{
		case '1' :
			return L ( 'ADV_TYPE_1' );
		case '2' :
			return L ( 'ADV_TYPE_2' );
		case '3' :
			return L ( 'ADV_TYPE_3' );

	}
}
function getUserName($user_id)
{
	if (D ( "User" )->where ( "id=" . $user_id )->getField ( "user_name" ))
	{
		return D ( "User" )->where ( "id=" . $user_id )->getField ( "user_name" );
	} else
	{

		return L ( "NO_USER" );
	}
}
function getRUserName($user_id)
{
	if (D ( "User" )->where ( "id=" . $user_id )->getField ( "user_name" ))
	{
		return D ( "User" )->where ( "id=" . $user_id )->getField ( "user_name" );
	} else
	{
		return "无推荐人";
	}
}
function check_mail($mail)
{
	if (! preg_match ( "/\w+@\w+\.\w{2,}\b/", $mail ))
	{
		return false;
	} else
	{
		return true;
	}
}

function check_time($timestr)
{
	if (preg_match ( "/\d{4}-\d{1,2}-\d{1,2} \d{1,2}:\d{1,2}:\d{1,2}/", $timestr ))
	{
		return true;
	} else
	{
		return false;
	}
}
function formatScore($score)
{
	return $score . " " . L ( "SCORE_UNIT" );
}

function countScore($score)
{
	return $score * eyooC ( "SCORE_RADIO" );

}
function showStatusIncharge($status, $id)
{
	switch ($status)
	{
		case 0 :
			$info = '<a href="javascript:resumeIncharge(' . $id . ')">' . L ( "CONFIRM" ) . '</a>';
			break;
		case 1 :
			$info = '<a href="javascript:forbidIncharge(' . $id . ')">' . L ( "CANCEL" ) . '</a>';
			break;
	}
	return $info;
}

function unescape($str, $charcode = "")
{
	$text = preg_replace_callback ( "/%u[0-9A-Za-z]{4}/", "toUtf8", $str );
	if (empty ( $charcode ))
	{
		return $text;
	} else
	{
		return mb_convert_encoding ( $text, $charcode, "utf-8" );
	}
}
function toUtf8($ar)
{
	$c = "";
	foreach ( $ar as $val )
	{
		$val = intval ( substr ( $val, 2 ), 16 );
		if ($val < 0x7F)
		{ // 0000-007F
			$c .= chr ( $val );
		} elseif ($val < 0x800)
		{ // 0080-0800
			$c .= chr ( 0xC0 | ($val / 64) );
			$c .= chr ( 0x80 | ($val % 64) );
		} else
		{ // 0800-FFFF
			$c .= chr ( 0xE0 | (($val / 64) / 64) );
			$c .= chr ( 0x80 | (($val / 64) % 64) );
			$c .= chr ( 0x80 | ($val % 64) );
		}
	}
	return $c;
}
function showNavStatus($status, $id)
{
	//	if(D("Nav")->where("id=".$id)->getField("is_fix")==0)
	//	{
	switch ($status)
	{
		case 0 :
			$info = '<a href="javascript:resume(' . $id . ')">' . L ( "RESUME" ) . '</a>';
			break;
		case 2 :
			$info = '<a href="javascript:pass(' . $id . ')">' . L ( "PASS" ) . '</a>';
			break;
		case 1 :
			$info = '<a href="javascript:forbid(' . $id . ')">' . L ( "FORBID" ) . '</a>';
			break;
		case - 1 :
			$info = '<a href="javascript:recycle(' . $id . ')">' . L ( "RECYCLE" ) . '</a>';
			break;
	}
	return $info;

		//	}
//	else
//	{
//		return '';
//	}
}

function showNavDel($id)
{
	//	if(D("Nav")->where("id=".$id)->getField("is_fix")==0)
	//	{
	$info = '<a href="javascript:foreverdel(' . $id . ')">' . L ( "DELETE" ) . '</a>';
	return $info;

		//	}
//	else
//	{
//		return '';
//	}
}
function getAdmName($id)
{
	return M ( "Admin" )->where ( "id=" . $id )->getField ( "adm_name" );
}

function getHiddenBox($id)
{
	return $id . "<input type='hidden' name='hiddenid[]' value='" . $id . "' />";
}


function formatPrice($price, $radio)
{
	$price = round ( floatval ( $price ), 2 );
	$price = eyooC ( "BASE_CURRENCY_UNIT" ) . $price;
	return $price;
}
function checkArticleUName($uname)
{
	$id = intval ( $_REQUEST ['id'] );
	if (M ( "Article" )->where ( "u_name='" . $uname . "' and id <> $id" )->count () == 0)
	{
		return true;
	} else
		return false;
}
function get_role_name($id)
{
	$admin = M ( "Admin" )->getById ( $id );
	if ($admin ['adm_name'] == eyooC ( "SYS_ADMIN" ))
	{
		return "<span style='color:#f30;'>默认管理员</span>";
	} else
		return M ( "Role" )->where ( "id=" . $admin ['role_id'] )->getField ( "name" );
}
function getArticleCate($type)
{
	$class = D ( "Article_cate" )->where ( "id=" . $type )->find ();
	return $class ['name_1'];
}

function getImages($img){

    return '<img src="__PUBLIC__/upload/Flashimg/'.$img.'" width="200" />';
}

function getPid($id) {
	$RegionConf = D ( "RegionConf" );
	$regWhere ["id"] = $id;
	$regionConf = $RegionConf->where ( $regWhere )->find ();
	return $regionConf ["pid"];
}

function getPidName($id) {
	$RegionConf = D ( "RegionConf" );
	$regWhere ["id"] = $id;
	$regionConf = $RegionConf->where ( $regWhere )->find ();
	return $regionConf ["name"];
}
function getSort($sort, $goods_id) {
	$str = "<span onclick='changeSort(this," . $goods_id . ")' title='" . L ( "CLICK_TO_CHANGE" ) . "'>" . $sort . "</span>";
	return $str;
}

function showArticle($status){
	switch ($status) {
		case 1:
			$showImg = '<IMG SRC="__PUBLIC__/images/allow.gif"   BORDER="0" ALT="正常">';
			break;
	  }
	return $showImg;
}
function showArticleStatus($status, $id) {
	switch ($status) {
		case 0 :
			$info = '<a href="javascript:onTop(' . $id . ')">置顶</a>';
			break;
		case 1 :
			$info = '<a href="javascript:unTop(' . $id . ')">取消置顶</a>';
			break;
	}
	return $info;
}

function showRStatus($status, $id) {
	switch ($status) {
		case 0 :
			$info = '<a href="javascript:swHotStatus(' . $id . ','.$status.')">推荐</a>';
			break;
		case 1 :
			$info = '<a href="javascript:swHotStatus(' . $id . ','.$status.')">取消推荐</a>';
			break;
	}
	return $info;
}

function getTypes($type) {
	switch ($type) {
		case 1 :
			$info = '免费量房';
			break;
		case 2 :
			$info = '免费预算';
			break;
        case 3 :
			$info = '免费设计';
			break;
        case 4 :
			$info = '免费咨询';
			break;
        case 5 :
			$info = '活动';
			break;
	}
	return $info;
}
function getCatePid($id) {
	$RegionConf = D ( "Category" );
	$regWhere ["id"] = $id;
	$regionConf = $RegionConf->where ( $regWhere )->find ();
	return $regionConf ["pid"];
}

function getCatePName($id) {
	$RegionConf = D ( "Category" );
	$regWhere ["id"] = $id;
	$regionConf = $RegionConf->where ( $regWhere )->find ();
	return $regionConf ["name_1"];
}
function getCompany($id){
    $Company =D("Company");
    $cmmp['uid']=$id;
    $comp=$Company->where($cmmp)->find();
    //dump($comp);
    return $comp['company_name'];
}
function getCompanys($id){
    if($id==0){
        return '本站';
    }else{
        $Company =D("Company");
        $cmmp['id']=$id;
        $comp=$Company->where($cmmp)->find();
        //dump($comp);
        return $comp['company_name'];
    }
}
function getDesinger($id){

    $Desinger =D("Designer");
    //$cmmp['id']=$id;
    $comp=$Desinger->getById($id);
   // echo $Desinger->getlastsql();
   // dump($comp);
    return $comp['name_1'];
}

function getCaseName($id){

    $Desinger =D("Case");
    //$cmmp['id']=$id;
    $comp=$Desinger->getById($id);
   // echo $Desinger->getlastsql();
   // dump($comp);
    return $comp['name_1'];
}

function getCompanyUid($id){
    $Company =D("Company");
    $cmmp['id']=$id;
    $comp=$Company->where($cmmp)->find();
    //dump($comp);
    return $comp['uid'];
}
?>