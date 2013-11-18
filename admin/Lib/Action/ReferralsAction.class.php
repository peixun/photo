<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 *///会员等级分组
class ReferralsAction extends CommonAction
{
	public function index()
	{
		$map = $this->_search ();	
		
		$page = intval($_REQUEST[C("VAR_PAGE")]);
		
	    if($page==0)
	    	$page = 1;
			
		$listrow = C("PAGE_LISTROWS");
			
		$user_name  = trim($_REQUEST['user_name']);
		$parent_name  = trim($_REQUEST['parent_name']);
		$is_pay  = $_REQUEST['is_pay'];
		$goods_id = intval($_REQUEST['goods_id']);
		$city_id = intval($_REQUEST['city_id']);
		
		$default_lang_id = D("LangConf")->where("lang_name='".C('DEFAULT_LANG')."'")->field("id")->find();
		$default_lang_id = $default_lang_id['id'];  //默认语言的ID
		$select_dispname = "g.name_".$default_lang_id;
		
		$where = " r.id is not null";
		if($is_pay == "")
			$is_pay = -1;
			
		$map['is_pay'] = $is_pay;
		$this->assign("map",$map);
		if($is_pay != -1)
			$where .= " and r.is_pay = '$is_pay'";
			
		if(!empty($user_name))
			$where .= " and u.user_name like '%$user_name%'";
			
		if(!empty($parent_name))
			$where .= " and p.user_name like '%$parent_name%'";
			
		if($goods_id>0)
		{
			$where .= " and r.goods_id=".$goods_id;
		}
		if($city_id>0)
		{
			$where .= " and r.city_id=".$city_id;
		}
		else 
		{
			if(!$_SESSION['all_city'])
			$where .= " and r.city_id in (".implode(",",$_SESSION['admin_city_ids']).")";
		}
		$order = "id";
		$sort = "desc";
		
		if(!empty($_REQUEST["_order"]))
			$order = $_REQUEST["_order"];
			
		if($_REQUEST["_sort"] == 1)
			$sort = "asc";
			
		$sql = "select count(*) as c from ".C("DB_PREFIX")."referrals as r left join ".C("DB_PREFIX")."goods  as g on g.id = r.goods_id left join ".C("DB_PREFIX")."user as u on u.id = r.user_id left join ".C("DB_PREFIX")."user as p on p.id = r.parent_id where".$where;
		$count = M()->query($sql);
		$count = intval($count[0]['c']);
		
		
		
		if(ceil($count / $listrow ) < intval($page))
			$page = ceil($count / $listrow );
		
		$limit = ($page-1)* $listrow .", $listrow";
		
		$sql = "select r.city_id,r.create_time,r.pay_time,r.id,r.is_pay,r.score,r.money,$select_dispname as goods_name,u.user_name,p.user_name as parent_name from ".C("DB_PREFIX")."referrals as r left join ".C("DB_PREFIX")."goods  as g on g.id = r.goods_id left join ".C("DB_PREFIX")."user as u on u.id = r.user_id left join ".C("DB_PREFIX")."user as p on p.id = r.parent_id where".$where." group by r.id order by $order $sort LIMIT $limit";

		$list = M()->query($sql);
		
		$this->assign("city_list",D("GroupCity")->where(array("status"=>1,"id"=>array("in",$_SESSION['admin_city_ids'])))->order("is_defalut desc,id asc")->findAll());
		if(!$_SESSION['all_city'])
		$this->assign("goods_list",D("Goods")->where(array("city_id"=>array("in",$_SESSION['admin_city_ids'])))->order("sort asc")->findAll());
		else
		$this->assign("goods_list",D("Goods")->order("sort asc")->findAll());
		$page = new Page($count,$listrow);   //初始化分页对象 		
		$p =  $page->show();
	    $this->assign('pages',$p);
		
		$this->assign("list",$list);
		
		//开始输出统计
		$sql_total = "select sum(r.money) as pay_money,sum(r.score) as pay_score from ".C("DB_PREFIX")."referrals as r left join ".C("DB_PREFIX")."goods  as g on g.id = r.goods_id left join ".C("DB_PREFIX")."user as u on u.id = r.user_id left join ".C("DB_PREFIX")."user as p on p.id = r.parent_id where".$where;
		$rs = M()->query($sql_total);
		$rs = $rs[0];
		$this->assign('sum',$rs);
		$this->display ();
	}
	
	public function list_ref()
	{
		//列表过滤器，生成查询Map对象
		$map = array();
		if($_REQUEST['user_name']!='')
		$map['user_name'] = $_REQUEST['user_name'];
		if($_REQUEST['parent_name']!='')
		$map['parent_id'] = M("User")->where("user_name = '".$_REQUEST['parent_name']."'")->getField("id");
		
		$begin_time = parseToTimeSpan($_REQUEST['begin_time']);
		if($begin_time>0)
		$begin_time = $begin_time - date('Z');
		
		
		
		$end_time = parseToTimeSpan($_REQUEST['end_time']);
		if($end_time>0)
		$end_time = $end_time - date('Z');
		else
		$end_time = gmtTime();
		
		$this->assign("begin_time",toDate($begin_time,"Y-m-d"));
		$this->assign("end_time",toDate($end_time,"Y-m-d"));
		$model = D ("User");

				//排序字段 默认为主键名
		if (isset ( $_REQUEST ['_order'] )) {
			$order = $_REQUEST ['_order'];
		} else {
			$order = ! empty ( $sortBy ) ? $sortBy : "u.id";
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		//取得满足条件的记录数
		
			$sql = "select count(distinct(ru.parent_id)) as ct from ".C("DB_PREFIX")."user as ru where ru.status = 1 ";
			if($begin_time>0&&$end_time>0)
			$sql.=" and ru.create_time between $begin_time and $end_time";
			if($begin_time==0&&$end_time>0)
			$sql.=" and ru.create_time <= $end_time";
			if($begin_time>0&&$end_time==0)
			$sql.=" and ru.create_time >= $begin_time";
			if($map['parent_id'])
			{
				$sql.=" and ru.parent_id = '".$map['parent_id']."'";
			}
			//$sql.=" and u.city_id in (".implode(",",$_SESSION['admin_city_ids']).") ";
			$sql.=" and ru.parent_id<>0";
		
		$count = $model->query($sql);
		$count = $count[0]['ct'];
			
		if ($count > 0) {
			import ( "@.ORG.Page" );
			//创建分页对象
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $count, $listRows );
			//分页查询数据

			$sql = "select u.id as id,u.user_name as parent_name, ru.parent_id, count(ru.parent_id) as scount from ".C("DB_PREFIX")."user as ru left join ".C("DB_PREFIX")."user as u on u.id = ru.parent_id where ru.status = 1 ";
			if($begin_time>0&&$end_time>0)
			$sql.=" and ru.create_time between $begin_time and $end_time";
			if($begin_time==0&&$end_time>0)
			$sql.=" and ru.create_time <= $end_time";
			if($begin_time>0&&$end_time==0)
			$sql.=" and ru.create_time >= $begin_time";
			if($map['parent_id'])
			{
				$sql.=" and ru.parent_id = '".$map['parent_id']."'";
			}
			//$sql.=" and u.city_id in (".implode(",",$_SESSION['admin_city_ids']).") ";
			$sql.=" group by ru.parent_id  having ru.parent_id<>0 order by ".$order." ".$sort." limit ".$p->firstRow.",".$p->listRows;
			
			$voList = $model->query($sql);
			//$voList = $model->where($map)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->findAll ( );

			//echo $model->getlastsql();
			//分页跳转的时候保证查询条件
			foreach ( $map as $key => $val ) {
				if (! is_array ( $val )) {
					$p->parameter .= "$key=" . urlencode ( $val ) . "&";
				}
			}
			//分页显示
			$page = $p->show ();
			//列表排序显示
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? L('SORT_ASC') : L('SORT_DESC'); //排序提示
			$sort = $sort == 'desc' ? 1 : 0; //排序方式
			$assign_array = array();
			//模板赋值显示
			$assign_array = $voList;
			$this->assign ( 'list', $assign_array );
			$this->assign ( 'sort', $sort );
			$this->assign ( 'order', $order );
			$this->assign ( 'sortImg', $sortImg );
			$this->assign ( 'sortType', $sortAlt );
			$this->assign ( "page", $page );
		}
		Cookie::set ( '_currentUrl_', U($this->getActionName()."/index") );
		
		
		$this->display ();
		return;		
	}
	public function payStatus($isPay,$id)
	{
		if($isPay)
			return "<span><a href='javascript:;' onclick='referralsUnPay($id,this);'>退回返利</a></span>";
		else
			return "<span><a href='javascript:;' onclick='referralsPay($id,this);'>返利</a></span>";
	}
	
	public function pay()
	{
		$id = intval($_REQUEST['id']);
		$referrals = D("Referrals")->getById($id);
		if ($referrals)
		{
			payReferrals($id);
			$user_name = M("User")->where("id=".$referrals['user_id'])->getField("user_name");
			$msg = "会员:".$user_name."返利已发放";
			$this->saveLog(1,0,$msg);
			echo "<span><a href='javascript:;' onclick='referralsUnPay($id,this);'>退回返利</a></span>";
		}
	}
	
	public function unPay()
	{
		$id = intval($_REQUEST['id']);
		$referrals = D("Referrals")->getById($id);
		if ($referrals)
		{
			unPayReferrals($id);
			
			$user_name = M("User")->where("id=".$referrals['user_id'])->getField("user_name");
			$msg = "会员:".$user_name."返利已退回";
			$this->saveLog(0,0,$msg);
			echo "<span><a href='javascript:;' onclick='referralsPay($id,this);'>返利</a></span>";
		}
	}
	
	public function delete($id)
	{
			return "<span><a href='javascript:;' onclick='referralsDelete($id,this);'>删除</a></span>";
	}
	
	public function deleteReferrals()
	{
		$id = intval($_REQUEST['id']);
		if(D("Referrals")->where("id=$id")->delete())
			echo 1;
		else
			echo 0;
	}
}
?>