<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 后台首页
class IndexAction extends CommonAction{
	public function __construct()
	{
		parent::__construct();
		if (! $_SESSION [C ( 'USER_AUTH_KEY' )]) {
					//跳转到认证网关
					redirect ( PHP_FILE . C ( 'USER_AUTH_GATEWAY' ) );
		}
	}
	public function index()
	{
		$this->display();
	}

// 顶部页面
	public function top() {
		C('SHOW_RUN_TIME',false);			// 运行时间显示
		C('SHOW_PAGE_TRACE',false);
		$model	=	M("RoleNav");
		if($_SESSION[C("ADMIN_AUTH_KEY")])
			$list	=	$model->where('status=1')->field('id,name')->order("sort")->findAll();
		else
			$list	=	$model->where('status=1')->field('id,name')->order("sort")->findAll();

       // dump($list);
        //echo $model->getlastsql();
		$this->assign('roleNav',$list);
		$this->display();
	}
	// 尾部页面
	public function footer() {
		C('SHOW_RUN_TIME',false);			// 运行时间显示
		C('SHOW_PAGE_TRACE',false);
		$this->display();
	}
	// 菜单页面
	public function menu() {

        if(isset($_SESSION[C('USER_AUTH_KEY')])) {
            //显示菜单项
            $id	=	intval($_REQUEST['tag'])==0?2:intval($_REQUEST['tag']);

            $menu  = array();
            if($id>0)
            {
//	            if(isset($_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')].$id])) {
//
//	                //如果已经缓存，直接读取缓存
//	                $menu   =  $_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')].$id];
//	            }else {
	                //读取数据库模块列表生成菜单项
	                $node_model    =   M("RoleNode");
					$where['status']=1;
					$where['nav_id']=$id;
	                $list	=	$node_model->where($where)->field('id,action,action_name,module,module_name')->order('sort asc')->select();
	                foreach($list as $key=>$action) {
	                   $menu[$action['module']]['navs'][] = $action;
	                   $menu[$action['module']]['name']	= $action['module_name'];
	                }
	                //缓存菜单访问
	                //$_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')].$id]	=	$menu;
//	            }
            }
            $this->assign('menu',$menu);
		}
		C('SHOW_RUN_TIME',false);			// 运行时间显示
		C('SHOW_PAGE_TRACE',false);
		$this->display();
	}

	public function menumain() {

        if(isset($_SESSION[C('USER_AUTH_KEY')])) {
            //显示菜单项
            $id	=	intval($_REQUEST['tag'])==0?1:intval($_REQUEST['tag']);


					$node_model    =   M("RoleNode");
					$where['status']=1;
					$where['nav_id']=$id;
	                $main_page	=	$node_model->where($where)->field('id,action,action_name,module,module_name')->order('sort asc')->find();
	         C('SHOW_RUN_TIME',false);			// 运行时间显示
			 C('SHOW_PAGE_TRACE',false);
			 $this->redirect(U($main_page['module']."/".$main_page['action']));
		}

	}

    // 后台首页 查看系统信息
    public function main() {

        $info = array(
            '系统名称'=>eyooC('SHOP_NAME')."网站后台管理系统",
            L('SYS_VERSION')=>eyooC('SYS_VERSION')."&nbsp;&nbsp;<span id='version_tip'></span>&nbsp;&nbsp;<span id='public_tip'></span>",
            '系统时间'=>date("Y年n月j日 H:i:s",time()),
            'UTC标准时间'=>date("Y".L('YEAR')."n".L('MONTH')."j".L('DAY')." H:i:s",gmtTime()),
            '技术服务团队'=>"<a href='http://www.top-serve.com'  target='_blank'>上海信创网站建设</a>",
            );
        $this->assign('info',$info);
        /****
        //开始输出订单提醒
        if($_SESSION['all_city'])
        $pay_order = D("Order")->where("money_status = 2")->count();  //未处理订单数
        else {
        $pay_order = M()->query("select count(*) as tpcount from ".C("DB_PREFIX")."order as o left join ".C("DB_PREFIX")."order_goods as og on o.id = og.order_id left join ".C("DB_PREFIX")."goods as g on og.rec_id = g.id where o.money_status = 2 and g.city_id in (".implode(",",$_SESSION['admin_city_ids']).")");
        $pay_order = $pay_order[0]['tpcount'];
        }
        if($_SESSION['all_city'])
        $nopay_order = D("Order")->where("money_status = 0")->count();  //未处理订单数
        else {
        $nopay_order = M()->query("select count(*) as tpcount from ".C("DB_PREFIX")."order as o left join ".C("DB_PREFIX")."order_goods as og on o.id = og.order_id left join ".C("DB_PREFIX")."goods as g on og.rec_id = g.id where o.money_status = 0 and g.city_id in (".implode(",",$_SESSION['admin_city_ids']).")");
        $nopay_order = $nopay_order[0]['tpcount'];
        }


        if($_SESSION['all_city'])
        $delivery_count = M()->query("select count(distinct(og.order_id)) as total_count from ".C("DB_PREFIX")."order_goods as og left join ".C("DB_PREFIX")."order as o on o.id = og.order_id where og.rec_id in (select id from ".C("DB_PREFIX")."goods where type_id = 1)  and o.money_status = 2 and o.goods_status in (0)");
        else
        $delivery_count = M()->query("select count(distinct(og.order_id)) as total_count from ".C("DB_PREFIX")."order_goods as og left join ".C("DB_PREFIX")."order as o on o.id = og.order_id where og.rec_id in (select id from ".C("DB_PREFIX")."goods where type_id = 1 and city_id in (".implode(",",$_SESSION['admin_city_ids'])."))  and o.money_status = 2 and o.goods_status in (0)");
        $delivery_count = intval($delivery_count[0]['total_count']);

        if($_SESSION['all_city'])
        $nodelivery_count = M()->query("select count(distinct(og.order_id)) as total_count from ".C("DB_PREFIX")."order_goods as og left join ".C("DB_PREFIX")."order as o on o.id = og.order_id where og.rec_id in (select id from ".C("DB_PREFIX")."goods where type_id <> 1 )  and o.money_status = 2 and o.goods_status in (5)");
        else
        $nodelivery_count = M()->query("select count(distinct(og.order_id)) as total_count from ".C("DB_PREFIX")."order_goods as og left join ".C("DB_PREFIX")."order as o on o.id = og.order_id where og.rec_id in (select id from ".C("DB_PREFIX")."goods where type_id <> 1 and city_id in (".implode(",",$_SESSION['admin_city_ids'])."))  and o.money_status = 2 and o.goods_status in (5)");
        $nodelivery_count = intval($nodelivery_count[0]['total_count']);
        $order_memo = array(
        	'未付款定单'	=>	"您有 (<span style='color:#f30;'>".$nopay_order."</span>) 个订单未付款, <a href='".U("Order/index",array('money_status'=>0))."'>是否立即去处理？</a>",
        	'已付款定单'	=>	"您有 (<span style='color:#f30;'>".$pay_order."</span>) 个已付款的定单, <a href='".U("Order/index",array('money_status'=>2))."'>是否立即去处理？</a>",
        	'已付款需发货的定单'	=>	"您有 (<span style='color:#f30;'>".$delivery_count."</span>) 要发货了, <a href='".U("Order/index",array('money_status'=>2,'goods_status'=>0))."'>是否立即去处理？</a>",
        	'已付款无需发货的定单'	=>	"您有 (<span style='color:#f30;'>".$nodelivery_count."</span>) 团购券/线下团购定单已支付了, <a href='".U("Order/index",array('money_status'=>2,'goods_status'=>5))."'>是否立即去查看？</a>",


        );
        $this->assign("order_memo",$order_memo);

        //开始输出留言提醒

        //输出供应商留言
        $suplliers = intval(D("Message")->where("rec_module='Seller' and create_time > ".(gmtTime()-3*24*3600))->count());

		//退款
        //$OrderReConsignment = intval(D("Message")->where("rec_module='OrderReConsignment' and create_time > ".(gmtTime()-3*24*3600))->count());
        $sql = "select count(*) as tpcount from ".
        		C("DB_PREFIX")."message as m left join ".C("DB_PREFIX").
        		"order as o on o.id = m.rec_id left join ".C("DB_PREFIX").
        		 "order_goods as og on o.id = og.order_id left join ".C("DB_PREFIX").
        		 "goods as g on g.id = og.rec_id where m.rec_module='OrderReConsignment' and m.create_time >".(gmtTime()-3*24*3600);
        if(!$_SESSION['all_city'])
        $sql.= " and g.city_id in (".implode(",",$_SESSION['admin_city_ids']).")";
        $OrderReConsignment = M()->query($sql);
        $OrderReConsignment = $OrderReConsignment[0]['tpcount'];
        //退货
        //$OrderUncharge = intval(D("Message")->where("rec_module='OrderUncharge' and create_time > ".(gmtTime()-3*24*3600))->count());

        $sql = "select count(*) as tpcount from ".
        		C("DB_PREFIX")."message as m left join ".C("DB_PREFIX").
        		"order as o on o.id = m.rec_id left join ".C("DB_PREFIX").
        		 "order_goods as og on o.id = og.order_id left join ".C("DB_PREFIX").
        		 "goods as g on g.id = og.rec_id where m.rec_module='OrderUncharge' and m.create_time >".(gmtTime()-3*24*3600);
         if(!$_SESSION['all_city'])
        $sql.= " and g.city_id in (".implode(",",$_SESSION['admin_city_ids']).")";
        $OrderUncharge = M()->query($sql);
        $OrderUncharge = $OrderUncharge[0]['tpcount'];
        //付款通知
       // $Payment = intval(D("Message")->where("rec_module='Payment' and create_time > ".(gmtTime()-3*24*3600))->count());

        $sql = "select count(*) as tpcount from ".
        		C("DB_PREFIX")."message as m left join ".C("DB_PREFIX").
        		"order as o on o.id = m.rec_id left join ".C("DB_PREFIX").
        		 "order_goods as og on o.id = og.order_id left join ".C("DB_PREFIX").
        		 "goods as g on g.id = og.rec_id where m.rec_module='Payment' and m.create_time >".(gmtTime()-3*24*3600);
         if(!$_SESSION['all_city'])
        $sql.= " and g.city_id in (".implode(",",$_SESSION['admin_city_ids']).")";
        $Payment = M()->query($sql);
        $Payment = $Payment[0]['tpcount'];
        //冲值申请
        $incharge = intval(D("UserIncharge")->where("status=0")->count());
        //提现申请
        $uncharge = intval(D("UserUncharge")->where("status=0")->count());
        $message_memo = array(
        	'提供团购信息'	=>	"最近三天共有(<span style='color:#f30;'>".$suplliers."</span>)人提供了团购信息，<a href='".U("Message/typeSeller")."'>去查看</a>？",
        	'退款申请'	=>	"最近三天共有(<span style='color:#f30;'>".$OrderReConsignment."</span>)人提交了退款申请，<a href='".U("Message/typeOrderReConsignment")."'>去查看</a>？",
        	'退货申请'	=>	"最近三天共有(<span style='color:#f30;'>".$OrderUncharge."</span>)人提交了退货申请，<a href='".U("Message/typeOrderUncharge")."'>去查看</a>？",
        	'地面团购付款通知'	=>	"最近三天共有(<span style='color:#f30;'>".$Payment."</span>)人提交了地面团购付款通知，<a href='".U("Message/typePayment")."'>去查看</a>？",
        	'充值申请'	=>	"当前共有(<span style='color:#f30;'>".$incharge."</span>)单未支付的充值单,<a href='".U("UserMoney/incharge",array('status'=>0))."'>去查看？</a>",
        	'提现申请'	=>	"当前共有(<span style='color:#f30;'>".$uncharge."</span>)单未确认的提现单,<a href='".U("UserMoney/uncharge",array('status'=>0))."'>去查看？</a>",
        );
        **/
        $this->assign("message_memo",$message_memo);
        $this->display();
    }

// 更换密码
    public function changePwd()
    {
        //对表单提交处理进行处理或者增加非表单数据
		if(md5($_POST['verify'])	!= $_SESSION['verify']) {
			$this->error(L('VERIFY_ERROR'));
		}
		$map	=	array();
        $map['adm_pwd']= pwdHash($_POST['oldpassword']);
        if(isset($_POST['adm_name'])) {
            $map['adm_name']	 =	 $_POST['adm_name'];
        }elseif(isset($_SESSION[C('USER_AUTH_KEY')])) {
            $map['id']		=	$_SESSION[C('USER_AUTH_KEY')];
        }
        //检查用户
        $User    =   new AdminModel();
        $result = $User->create();
        if (!$result){
		// 如果创建失败 表示验证没有通过 输出错误提示信息
			$this->error($User->getError());
		}

        if(!$User->where($map)->field('id')->find()) {
            $this->error(L('PWD_OR_NAME_ERROR'));
        }else {
			$User->adm_pwd	=	pwdHash($_POST['adm_pwd']);
			$User->save();
			$this->success(L('CHANGE_PWD_SUCCESS'));
         }
    }
}
?>