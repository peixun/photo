<?php
// soso文件生成
require_once('init.php');
define("SDD_CODE","35_24");
define("SDD_FILE",ROOT_PATH."sdd/create.sdd");
define("SDD_DIR",ROOT_PATH."sdd/");

if(!file_exists(SDD_DIR))
	mkdir(SDD_DIR);

getIsCreate();

function getIsCreate()
{
	$now = gmtTime();
	$time = 0;
	
	if(file_exists(SDD_FILE))
	{
		$content = @file_get_contents(SDD_FILE);
		if($content !== false)
		{
			$time = intval($content);
		}
	}
	
	if(($now - $time) > 3600)
	{
		createSDDIndex();
	}
}

function eyooCSDD($name)
{
	if($name == 'SHOP_URL')
		return "http://".$_SERVER['HTTP_HOST'].__ROOT__;
	else
		return $GLOBALS['db']->getOne("select val from ".DB_PREFIX."sys_conf where name='".$name."'");
}

function createSDDIndex()
{
	$now = gmtTime();
	$sql = "SELECT count(id) FROM ".DB_PREFIX."goods where status = 1 and promote_begin_time <= $now and (promote_end_time >= $now or ($now - promote_end_time) < 10800)";
	$count = $GLOBALS['db']->getOne($sql);

	if(intval($count) > 0)
	{
		define("SDD_INDEX_FILE",ROOT_PATH."SDD_index.XML");
		
		$content = @file_get_contents(SDD_INDEX_FILE);
		if($content === false || empty($content))
		{
			$xmlBegin="<?xml version=\"1.0\" encoding=\"gbk\"?>\r\n";
			$xmlBegin.="<sddindex>\r\n";
		}
		else
		{
			preg_match("/(.*?)<\/sddindex>/s", $content, $xmlContent);
			$xmlBegin=$xmlContent[1];
		}
		
		$domain = getDomainSDD();
		
		$indexXml = createSDDXML($xmlBegin);
		
		@file_put_contents(SDD_INDEX_FILE,$indexXml."</sddindex>");
		
		@file_put_contents(SDD_FILE,$now);
	}
	else
		@file_put_contents(SDD_FILE,$now);
}

function createSDDXML($xmlBegin)
{
	$host = getDomainSDD(false);
	$now = gmtTime();
	
	define("FANWE_LANG_ID",$GLOBALS['langItem']['id']);
	define("SHOP_NAME",$GLOBALS['langItem']['shop_name']);
	
	$goodsname = "name_".FANWE_LANG_ID;
	$brief = "brief_".FANWE_LANG_ID;
	$seokeyword = "seokeyword_".FANWE_LANG_ID;
	$seocontent = "seocontent_".FANWE_LANG_ID;
	$catename = "name_".FANWE_LANG_ID;
	$domain = getDomainSDD();
	
	$sql = "SELECT g.id,g.suppliers_id,g.u_name,g.city_id,g.$goodsname as goods_name,g.$seokeyword as seokeyword,g.$seocontent as seocontent,".
				"g.group_user,g.stock,g.is_group_fail,".
				"g.small_img,g.big_img,g.origin_img,g.shop_price,g.market_price,g.promote_begin_time,g.promote_end_time,".
				"g.$brief as goodsbrief,gc.name as city_name,s.name as suppliers_name,g.buy_count,ca.$catename as cate_name ".
				'FROM '.DB_PREFIX.'goods as g '.
				'left join '.DB_PREFIX.'goods_cate as ca on ca.id = g.cate_id '.
				'left join '.DB_PREFIX.'group_city as gc on gc.id = g.city_id '.
				'left join '.DB_PREFIX.'suppliers as s on s.id = g.suppliers_id '.
				"where g.status = 1 and g.promote_begin_time <= $now and (g.promote_end_time >= $now or ($now - g.promote_end_time) < 10800) group by g.id order by g.sort desc,g.id desc";
	
	$list = $GLOBALS['db']->getAll($sql);
	$version = eyooCSDD("SYS_VERSION");
	$route = eyooCSDD("URL_ROUTE");
	
	foreach($list as $item)
	{
		$xml="<?xml version=\"1.0\" encoding=\"gbk\"?>\r\n";
		$xml.="<sdd>\r\n";
		$xml.="<provider>".SHOP_NAME."</provider>\r\n";
		$xml.="<version>$version</version>\r\n";
		$xml.="<dataServiceId>".SDD_CODE."</dataServiceId>\r\n";
		$xml.="<meta>\r\n";
		$xml.="<description>".SHOP_NAME."今日团购</description>\r\n";
		$xml.="<fields>\r\n";
		$xml.="<field>\r\n";
		$xml.="<name>imageaddress1</name>\r\n";
		$xml.="<description>团购图片地址</description>\r\n";
		$xml.="</field>\r\n";
		$xml.="<field>\r\n";
		$xml.="<name>imagealt1</name>\r\n";
		$xml.="<description>团购图片提示</description>\r\n";
		$xml.="</field>\r\n";
		$xml.="<field>\r\n";
		$xml.="<name>imagelink1</name>\r\n";
		$xml.="<description>团购图片链接</description>\r\n";
		$xml.="</field>\r\n";
		$xml.="<field>\r\n";
		$xml.="<name>content1</name>\r\n";
		$xml.="<description>团购名称</description>\r\n"; 
		$xml.="</field>\r\n";
		$xml.="<field>\r\n";
		$xml.="<name>linktext1</name>\r\n";
		$xml.="<description>团购文本</description>\r\n";
		$xml.="</field>\r\n";
		$xml.="<field>\r\n";
		$xml.="<name>linktarget1</name>\r\n";
		$xml.="<description>团购文本链接</description>\r\n";
		$xml.="</field>\r\n";
		$xml.="<field>\r\n";
		$xml.="<name>content2</name>\r\n";
		$xml.="<description>原价</description>\r\n";
		$xml.="</field>\r\n";
		$xml.="<field>\r\n";
		$xml.="<name>content3</name>\r\n";
		$xml.="<description>现价</description>\r\n";
		$xml.="</field>\r\n";
		$xml.="<field>\r\n";
		$xml.="<name>content4</name>\r\n";
		$xml.="<description>折扣</description>\r\n";
		$xml.="</field>\r\n";
		$xml.="<field>\r\n";
		$xml.="<name>content5</name>\r\n";
		$xml.="<description>类别</description>\r\n";
		$xml.="</field>\r\n";
		$xml.="<field>\r\n";
		$xml.="<name>content6</name>\r\n";
		$xml.="<description>城市</description>\r\n";
		$xml.="</field>\r\n";
		$xml.="<field>\r\n";
		$xml.="<name>content7</name>\r\n";
		$xml.="<description>已抢购人数</description>\r\n";
		$xml.="</field>\r\n"; 
		$xml.="<field>\r\n";
		$xml.="<name>content8</name>\r\n";
		$xml.="<description>开始时间</description>\r\n";
		$xml.="<type>date</type>\r\n";
		$xml.="<format>YYYY-mm-dd HH:MM:SS</format>\r\n";
		$xml.="</field>\r\n";
		$xml.="<field>\r\n";
		$xml.="<name>content9</name>\r\n";
		$xml.="<description>结束时间</description>\r\n";
		$xml.="<type>date</type>\r\n"; 
		$xml.="<format>YYYY-mm-dd HH:MM:SS</format>\r\n"; 
		$xml.="</field>\r\n";
		$xml.="<field>\r\n";
		$xml.="<name>content10</name>\r\n";
		$xml.="<description>商家名称(可为空)</description>\r\n";
		$xml.="</field>\r\n";
		$xml.="<field>\r\n";
		$xml.="<name>content11</name>\r\n";
		$xml.="<description>团购状态：成功、失败、进行、卖光</description>\r\n";
		$xml.="</field>\r\n";
		$xml.="<field>\r\n";
		$xml.="<name>content12</name>\r\n";
		$xml.="<description>商家活动截止日期等说明补充信息(可为空)</description>\r\n";
		$xml.="</field>\r\n";
		$xml.="<field>\r\n";
		$xml.="<name>content13</name>\r\n";
		$xml.="<description>团购最小人数(可为空)</description>\r\n";
		$xml.="</field>\r\n";
		$xml.="<field>\r\n";
		$xml.="<name>content14</name>\r\n";
		$xml.="<description>店面地址(可为空)</description>\r\n";
		$xml.="</field>\r\n";
		$xml.="<field>\r\n";
		$xml.="<name>content15</name>\r\n";
		$xml.="<description>电话(可为空)</description>\r\n";
		$xml.="</field>\r\n";
		$xml.="<field>\r\n";
		$xml.="<name>linktext2</name>\r\n";
		$xml.="<description>网站名称</description>\r\n";
		$xml.="</field>\r\n";
		$xml.="<field>\r\n";
		$xml.="<name>linktarget2</name>\r\n";
		$xml.="<description>网站链接</description>\r\n";
		$xml.="</field>\r\n";
		$xml.="</fields>\r\n";
		$xml.="</meta>\r\n";
		$xml.="<updatemethod>all</updatemethod>\r\n";
		$xml.="<datalist>\r\n";
		$xml.="<item>\r\n";
		
		$keywords = $item['seokeyword'];
		if(empty($keywords))
			$keywords = $item['goods_name'];
		
		if($route == 1)
		{
			if($item['u_name']!='')
				$url = "/g/".rawurlencode($item['u_name']).".html";
			else
				$url = "/tg/".$item['id'].".html";
		}
		else
			$url = "/index.php?m=Goods&a=show&id=".$item['id'];
			
		$depart = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."suppliers_depart where supplier_id = '$item[suppliers_id]' order by is_main desc,id asc");
		
		if(intval($item['stock']) > 0)
		{
			$item['surplusCount'] = intval($item['stock']) - intval($item['buy_count']);
			if($item['surplusCount'] <= 0)
				$item['is_none'] = true;
		}
		
		if($item['promote_end_time'] < gmtTime())
		{
			if (($item['group_user'] > 0 && $item['group_user'] > $item['buy_count']))
				$item['is_group_fail'] = 1;
			else
				$item['is_group_fail'] = 2;
		}
		
		if($item['is_group_fail'] == 1)
			$status = "失败";
		elseif($item['is_group_fail'] == 2)
			$status = "成功";
		elseif($item['is_none'])
			$status = "卖光";
		else
			$status = "进行";
		
		$xml.="<keyword><![CDATA[$keywords]]></keyword>\r\n";
		$xml.="<url><![CDATA[".$domain.$url."]]></url>\r\n";
		$xml.="<creator>$host</creator>\r\n";
		$xml.="<title><![CDATA[".SHOP_NAME.$item['goods_name']."]]></title>\r\n";
		$xml.="<publishdate>".toDate($now)."</publishdate>\r\n";
		$xml.="<imageaddress1>".$domain.$item['small_img']."</imageaddress1>\r\n";
		$xml.="<imagealt1><![CDATA[".$item['goods_name']."]]></imagealt1>\r\n";
		$xml.="<imagelink1><![CDATA[".$domain.$url."]]></imagelink1>\r\n";
		$xml.="<content1><![CDATA[".$item['goods_name']."]]></content1>\r\n";
		$xml.="<linktext1><![CDATA[".$item['goods_name']."]]></linktext1>\r\n";
		$xml.="<linktarget1><![CDATA[".$domain.$url."]]></linktarget1>\r\n";
		$xml.="<content2>".floatval($item['market_price'])."元</content2>\r\n";
		$xml.="<content3>".floatval($item['shop_price'])."元</content3>\r\n";
		$xml.="<content4>".round((floatval($item['shop_price']) / floatval($item['market_price'])) * 10,2)."折</content4>\r\n";
		$xml.="<content5>$item[cate_name]</content5>\r\n";
		$xml.="<content6>$item[city_name]</content6>\r\n";
		$xml.="<content7>$item[buy_count]</content7>\r\n";
		$xml.="<content8>".toDate($item['promote_begin_time'])."</content8>\r\n";
		$xml.="<content9>".toDate($item['promote_end_time'])."</content9>\r\n";
		$xml.="<content10>$depart[depart_name]</content10>\r\n";
		$xml.="<content11>$status</content11>\r\n";
		$xml.="<content12><![CDATA[".preg_replace("|&.+?;|",'',strip_tags($item['goodsbrief']))."]]></content12>\r\n";
		$xml.="<content13>$item[group_user]</content13>\r\n";
		$xml.="<content14>$depart[address]</content14>\r\n";
		$xml.="<content15>$depart[tel]</content15>\r\n";
		$xml.="<linktext2>".SHOP_NAME."</linktext2>\r\n";
		$xml.="<linktarget2>".$domain."</linktarget2>\r\n";
		$xml.="<valid>1</valid>\r\n";
		$xml.="</item>\r\n";
		$xml.="</datalist>\r\n";
		$xml.="</sdd>";
		if(file_exists(ROOT_PATH."sdd/sdd_".$item['id'].".xml") && strpos($xmlBegin, "/sdd/sdd_".$item['id'].".xml") !== false)
		{
			$replace = "/sdd/sdd_".$item['id'].".xml</loc>\r\n<lastmod>".toDate($now)."</lastmod>";
			$xmlBegin = preg_replace("|\/sdd\/sdd_".$item['id']."\.xml<\/loc>\r\n<lastmod>.*?<\/lastmod>|s",$replace,$xmlBegin);
		}
		else
		{
			$xmlBegin.="<sdd>\r\n";		
			$xmlBegin.="<loc>".$domain."/sdd/sdd_".$item['id'].".xml</loc>\r\n";
			$xmlBegin.="<lastmod>".toDate($now)."</lastmod>\r\n";
			$xmlBegin.="</sdd>\r\n";
		}
		
		@file_put_contents(ROOT_PATH."sdd/sdd_".$item['id'].".xml",utf8ToGB($xml));
	}

	return $xmlBegin;
}

/**
 * 取得当前的域名
 *
 * @access  public
 *
 * @return  string      当前的域名
 */
function getDomainSDD($isPro = true)
{
	/* 协议 */
	$protocol = getHttpSDD();

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
	
	if($isPro)
		return $protocol . $host;
	else
		return $host;
}

/**
 * 获得当前环境的 HTTP 协议方式
 *
 * @access  public
 *
 * @return  void
 */
function getHttpSDD()
{
	return (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';
}
?>