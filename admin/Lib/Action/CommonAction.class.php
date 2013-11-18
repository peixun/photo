<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  Index Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */
class CommonAction extends Action {
	function _initialize() {

		//		if(intval(eyooC("EXPIRED_TIME"))>0&&Session::isExpired())
		//		{
		//			unset($_SESSION[C('USER_AUTH_KEY')]);
		//			unset($_SESSION);
		//			session_destroy();
		//		}
		//		if(intval(eyooC("EXPIRED_TIME"))>0)
		//		Session::setExpire(time()+eyooC("EXPIRED_TIME")*60);
		// 用户权限检查
		if (C ( 'USER_AUTH_ON' ) && ! in_array ( MODULE_NAME, explode ( ',', C ( 'NOT_AUTH_MODULE' ) ) )) {
			import ( '@.ORG.RBAC' );
			if (! RBAC::AccessDecision ()) {
				//检查认证识别号
				if (! $_SESSION [C ( 'USER_AUTH_KEY' )]) {
					//跳转到认证网关
					redirect ( PHP_FILE . C ( 'USER_AUTH_GATEWAY' ) );
				}
				// 没有权限 抛出错误
				if (C ( 'RBAC_ERROR_PAGE' )) {
					// 定义权限错误页面
					redirect ( C ( 'RBAC_ERROR_PAGE' ) );
				} else {
					if (C ( 'GUEST_AUTH_ON' )) {
						$this->assign ( 'jumpUrl', PHP_FILE . C ( 'USER_AUTH_GATEWAY' ) );
					}
					// 提示错误信息
					if (intval ( $_REQUEST ['ajax'] ) == 2) {
						echo L ( '_VALID_ACCESS_' );
						exit ();
					} else {
						$this->assign ( "jumpUrl", u ( "Index/main" ) );
						$this->error ( L ( '_VALID_ACCESS_' ) );
					}
				}
			}
		}
		$this->assign ( "module_name", MODULE_NAME );
         // 读取系统配置参数

	}
	//返回当前目录
	public function getRealPath() {
		return getcwd ();
	}

	public function _before_index() {
		//前置列表时删除相关未用到的商品图片
		$list = D ( "GoodsGallery" )->where ( "session_id='" . $_SESSION ['verify'] . "' and goods_id = 0" )->findAll ();
		foreach ( $list as $item ) {
			@unlink ( $this->getRealPath () . $item ['small_img'] );
			@unlink ( $this->getRealPath () . $item ['big_img'] );
			@unlink ( $this->getRealPath () . $item ['origin_img'] );
		}
		D ( "GoodsGallery" )->where ( "session_id='" . $_SESSION ['verify'] . "' and goods_id = 0" )->delete ();
	}

	public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name = $this->getActionName ();
		$model = D ( $name );
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}

		$this->display ();
		return;
	}
	/**
     +----------------------------------------------------------
	 * 取得操作成功后要返回的URL地址
	 * 默认返回当前模块的默认操作
	 * 可以在action控制器中重载
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return string
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	function getReturnUrl() {
		return __URL__ . '?' . C ( 'VAR_MODULE' ) . '=' . MODULE_NAME . '&' . C ( 'VAR_ACTION' ) . '=' . C ( 'DEFAULT_ACTION' );
	}

	/**
     +----------------------------------------------------------
	 * 根据表单生成查询条件
	 * 进行列表过滤
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @param string $name 数据对象名称
     +----------------------------------------------------------
	 * @return HashMap
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	protected function _search($name = '') {
		$lang_conf = C ( "LANG_CONF" );
		$lang_envs = D ( "LangConf" )->findAll ();
		//生成查询条件
		if (empty ( $name )) {
			$name = $this->getActionName ();
		}
		$multi_lang_fields = $lang_conf [parse_name ( $name )]; //当前如存在的多语方字段列
		$model = D ( $name );
		$map = array ();
		foreach ( $model->getDbFields () as $key => $val ) {
			if (isset ( $_REQUEST [$val] ) && $_REQUEST [$val] != '') {
				if (isset ( $_REQUEST ['SEARCH_TYPE'] ) && $_REQUEST ['SEARCH_TYPE'] == 'like')
					$map [$val] = array ("like", "%" . $_REQUEST [$val] . "%" );
				else
					$map [$val] = $_REQUEST [$val];
			}
			//加入多语言自动识别字段
			if ($multi_lang_fields) {
				foreach ( $multi_lang_fields as $field => $v ) {
					if (isset ( $_REQUEST [$field] ) && $_REQUEST [$field] != '') {

						foreach ( $lang_envs as $lang_item ) {
							if (isset ( $_REQUEST ['SEARCH_TYPE'] ) && $_REQUEST ['SEARCH_TYPE'] == 'like')
								$map_complex [$field . "_" . $lang_item ['id']] = array ("like", "%" . $_REQUEST [$field] . "%" );
							else
								$map_complex [$field . "_" . $lang_item ['id']] = $_REQUEST [$field];
						}
						$map_complex ['_logic'] = 'or';
						$map ['_complex'] = $map_complex;
					}
				}
			}
		}

		return $map;

	}

	/**
     +----------------------------------------------------------
	 * 根据表单生成查询条件
	 * 进行列表过滤
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @param Model $model 数据对象
	 * @param HashMap $map 过滤条件
	 * @param string $sortBy 排序
	 * @param boolean $asc 是否正序
	 * @param boolean $_child 是否包含子分类树
	 * @param string $pk 主键
	 * @param string $pid  关联外键的字段
	 * @param array $dispname_arr   用于分类树显示的字段集合
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	protected function _list($model, $map, $sortBy = '', $asc = false, $_child = false, $pk = 'id', $pid = 'pid', $dispname_arr = array('title')) {
		//排序字段 默认为主键名
		if (isset ( $_REQUEST ['_order'] )) {
			$order = $_REQUEST ['_order'];
		} else {
			$order = ! empty ( $sortBy ) ? $sortBy : $model->getPk ();
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'desc' : 'asc';
		}
		//取得满足条件的记录数
		$count = $model->where ( $map )->count ( 'id' );
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


			$voList = $model->where ( $map )->order ( "`" . $order . "` " . $sort )->limit ( $p->firstRow . ',' . $p->listRows )->findAll ();
			//echo $model->getlastsql();
			//dump($voList);
			//分页跳转的时候保证查询条件
			foreach ( $map as $key => $val ) {
				if (! is_array ( $val )) {
					$p->parameter .= "$key=" . urlencode ( $val ) . "&";
				}
			}
			if ((! empty ( $p->parameter )) && (substr ( $p->parameter, 1, 1 ) != '&')) { //add by chenfq 2010-06-19 添加分页条件连接缺少 & 问题
				$p->parameter = '&' . $p->parameter;
			}
			//分页显示
			$page = $p->show ();
			//列表排序显示
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? L ( 'SORT_ASC' ) : L ( 'SORT_DESC' ); //排序提示
			$sort = $sort == 'desc' ? 1 : 0; //排序方式
			$assign_array = array ();
			//模板赋值显示
			if ($_child) {

				foreach ( $voList as $row => $voItem ) {
					$childIds = D ( MODULE_NAME )->getChildIds ( $voItem [$pk], $pk, $pid );
					$childIds_str = implode ( ",", $childIds );
					$sub_list = D ( MODULE_NAME )->where ( $pk . " in( " . $childIds_str . ")" )->findAll ();
					$sub_list = D ( MODULE_NAME )->toFormatTree ( $sub_list, $dispname_arr );

					$assign_array [] = $voItem;
					foreach ( $sub_list as $sub_item ) {
						$assign_array [] = $sub_item;
					}
				}
			} else {
				$assign_array = $voList;
			}

			$this->assign ( 'list', $assign_array );
			$this->assign ( 'sort', $sort );
			$this->assign ( 'order', $order );
			$this->assign ( 'sortImg', $sortImg );
			$this->assign ( 'sortType', $sortAlt );
			$this->assign ( "page", $page );
		}
		Cookie::set ( '_currentUrl_', U ( $this->getActionName () . "/index" ) );
		return;
	}

	/**
     +----------------------------------------------------------
	 * 根据表单生成查询条件
	 * 进行列表过滤
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @param Model $model 数据对象
	 * @param string $sql_str Sql语句 不含排序字段的SQL语句
	 * @param string $parameter 分页跳转的时候保证查询条件
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	function _Sql_list($model, $sql_str, $parameter = '', $sortBy = '', $asc = false) {
		//排序字段 默认为主键名
		if (isset ( $_REQUEST ['_order'] )) {
			$order = $_REQUEST ['_order'];
		} else {
			$order = $sortBy;
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}

		//取得满足条件的记录数
		$sql_tmp = 'select count(*) as tpcount from (' . $sql_str . ') as a';
		//dump($sql_tmp);
		$rs = $model->query ( $sql_tmp, false );
		//dump($rs);


		$count = intval ( $rs [0] ['tpcount'] );
		//dump($count);
		if ($count > 0) {
			//创建分页对象
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}

			import ( "@.ORG.Page" );
			$p = new Page ( $count, $listRows );
			//分页跳转的时候保证查询条件
			//dump($parameter);
			if ((! empty ( $parameter )) && (substr ( $parameter, 1, 1 ) != '&')) { //add by chenfq 2010-06-19 添加分页条件连接缺少 & 问题
				$parameter = '&' . $parameter;
			}
			$p->parameter = $parameter;

			//排序
			if (! empty ( $order ))
				$sql_str .= ' ORDER BY ' . $order . ' ' . $sort;

		//分页查询数据
			$sql_str .= ' LIMIT ' . $p->firstRow . ',' . $p->listRows;

			//dump($sql_str);
			$voList = $model->query ( $sql_str, false );
			//dump($voList);
			//分页显示
			$page = $p->show ();
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? L ( 'SORT_ASC' ) : L ( 'SORT_DESC' ); //排序提示
			$sort = $sort == 'desc' ? 1 : 0; //排序方式


			$this->assign ( 'sort', $sort );
			$this->assign ( 'order', $order );
			$this->assign ( 'sortImg', $sortImg );
			$this->assign ( 'sortType', $sortAlt );
			$this->assign ( 'list', $voList );
			$this->assign ( "page", $page );
		}
		Cookie::set ( '_currentUrl_', U ( $this->getActionName () . "/index" ) );
		return $voList;
	}
	function insert() {
		//B('FilterString');
		$name = $this->getActionName ();
		$model = D ( $name );
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}

		//保存当前数据对象
		$list = $model->add ();

		if ($list !== false) { //保存成功
			$this->saveLog ( 1, $list );
	 		$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success ( L ( 'ADD_SUCCESS' ) );
		} else {
			//失败提示
			$this->saveLog ( 0, $list );
			$this->error ( L ( 'ADD_FAILED' ) );
		}
	}

	public function add() {
		$this->display ();
	}

	function read() {
		$this->edit ();
	}

	function edit() {
		$name = $this->getActionName ();
		$model = M ( $name );
		$id = $_REQUEST [$model->getPk ()];
		$vo = $model->getById ( $id );
		$this->assign ( 'vo', $vo );
		$this->display ();
	}



	function update() {
		//B('FilterString');
		$name = $this->getActionName ();
		$model = D ( $name );

		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}

		// 更新数据
		$list = $model->save ();

		if (false !== $list) {
			//成功提示
			$this->saveLog ( 1 );
			//			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success ( L ( 'EDIT_SUCCESS' ) );
		} else {
			//错误提示
			$this->saveLog ( 0 );
			$this->error ( L ( 'EDIT_FAILED' ) );
		}
	}
	/**
     +----------------------------------------------------------
	 * 默认删除操作
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return string
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	public function delete() {
		//删除指定记录
		$name = $this->getActionName ();
		$model = M ( $name );
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];

			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				$list = $model->where ( $condition )->setField ( 'status', - 1 );

				if ($list !== false) {
					//$this->saveLog ( 1 );
					$this->success ( L ( 'DEL_SUCCESS' ) );
				} else {
					//$this->saveLog ( 0 );
					$this->error ( L ( 'DEL_FAILED' ) );
				}
			} else {
				$this->saveLog ( 0 );
				$this->error ( L ( 'INVALID_OP' ) );
			}
		}
	}
	public function foreverdelete() {
		//删除指定记录
		$name = $this->getActionName ();
		$model = D ( $name );
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if (false !== $model->where ( $condition )->delete ()) {
					//$this->saveLog ( 1 );
					$this->success ( L ( 'DEL_SUCCESS' ) );
				} else {
					//$this->saveLog ( 0 );
					$this->error ( L ( 'DEL_FAILED' ) );
				}
			} else {
				//$this->saveLog ( 0 );
				$this->error ( L ( 'INVALID_OP' ) );
			}
		}
		$this->forward ();
	}
public function foreverdeletevvideo() {
		//删除指定记录
		$VVideo = D("VVideo");
		if (! empty ( $VVideo )) {
			$pk = $VVideo->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if (false !== $VVideo->where ( $condition )->delete ()) {
					//$this->saveLog ( 1 );
					$this->success ( L ( 'DEL_SUCCESS' ) );
				} else {
					//$this->saveLog ( 0 );
					$this->error ( L ( 'DEL_FAILED' ) );
				}
			} else {
				//$this->saveLog ( 0 );
				$this->error ( L ( 'INVALID_OP' ) );
			}
		}
		$this->forward ();
	}
public function foreverdeletevideo() {
		//删除指定记录

		$Video = D("Video");
		if (! empty ( $Video )) {

			$pk = $Video->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if (false !== $Video->where ( $condition )->delete ()) {
					//$this->saveLog ( 1 );
					$this->success ( L ( 'DEL_SUCCESS' ) );
				} else {
					//$this->saveLog ( 0 );
					$this->error ( L ( 'DEL_FAILED' ) );
				}
			} else {
				//$this->saveLog ( 0 );
				$this->error ( L ( 'INVALID_OP' ) );
			}
		}
		$this->forward ();
	}
public function foreverdeletevphoto() {
		//删除指定记录
		$PicGallery = D("PicGallery");
		if (! empty ( $PicGallery )) {
			$pk = $PicGallery->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if (false !== $PicGallery->where ( $condition )->delete ()) {
					//$this->saveLog ( 1 );
					$this->success ( L ( 'DEL_SUCCESS' ) );
				} else {
					//$this->saveLog ( 0 );
					$this->error ( L ( 'DEL_FAILED' ) );
				}
			} else {
				//$this->saveLog ( 0 );
				$this->error ( L ( 'INVALID_OP' ) );
			}
		}
		$this->forward ();
	}
	public function clear() {
		//删除指定记录
		$name = $this->getActionName ();
		$model = D ( $name );
		if (! empty ( $model )) {
			if (false !== $model->where ( 'status=1' )->delete ()) {
				$this->saveLog ( 1 );
				$this->assign ( "jumpUrl", $this->getReturnUrl () );
				$this->success ( L ( 'DEL_SUCCESS' ) );
			} else {
				$this->saveLog ( 1 );
				$this->error ( L ( 'DEL_FAILED' ) );
			}
		}
		$this->forward ();
	}
	/**
     +----------------------------------------------------------
	 * 默认禁用操作
	 *
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return string
     +----------------------------------------------------------
	 * @throws FcsException
     +----------------------------------------------------------
	 */
	public function forbid() {
		$name = $this->getActionName ();
		$model = D ( $name );
		$pk = $model->getPk ();
		$id = $_REQUEST [$pk];
		$condition = array ($pk => array ('in', $id ) );
		$list = $model->forbid ( $condition );
		if ($list !== false) {
			$this->saveLog ( 1 );
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( L ( 'FORBID_SUCCESS' ) );
		} else {
			$this->saveLog ( 0 );
			$this->error ( L ( 'FORBID_FAILED' ) );
		}
	}
	public function checkPass() {
		$name = $this->getActionName ();
		$model = D ( $name );
		$pk = $model->getPk ();
		$id = $_GET [$pk];
		$condition = array ($pk => array ('in', $id ) );
		if (false !== $model->checkPass ( $condition )) {
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( L ( 'PASS_SUCCESS' ) );
		} else {
			$this->error ( L ( 'PASS_FAILED' ) );
		}
	}

	public function recycle() {
		$name = $this->getActionName ();
		$model = D ( $name );
		$pk = $model->getPk ();
		$id = $_GET [$pk];
		$condition = array ($pk => array ('in', $id ) );
		if (false !== $model->recycle ( $condition )) {
			$this->saveLog ( 1 );
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( L ( 'RECYCLE_SUCCESS' ) );

		} else {
			$this->saveLog ( 0 );
			$this->error ( L ( 'RECYCLE_FAILED' ) );
		}
	}

	public function recycleBin() {
		$map = $this->_search ();
		$map ['status'] = - 1;
		$name = $this->getActionName ();
		$model = D ( $name );
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
	}

	/**
     +----------------------------------------------------------
	 * 默认恢复操作
	 *
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return string
     +----------------------------------------------------------
	 * @throws FcsException
     +----------------------------------------------------------
	 */
	function resume() {
		//恢复指定记录
		$name = $this->getActionName ();
		$model = D ( $name );
		$pk = $model->getPk ();
		$id = $_GET [$pk];
		$condition = array ($pk => array ('in', $id ) );
		if (false !== $model->resume ( $condition )) {
			$this->saveLog ( 1 );
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( L ( 'RESUME_SUCCESS' ) );
		} else {
			$this->saveLog ( 0 );
			$this->error ( L ( 'RESUME_FAILED' ) );
		}
	}

    /**
     +----------------------------------------------------------
	 * 默认恢复操作
	 *
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return string
     +----------------------------------------------------------
	 * @throws FcsException
     +----------------------------------------------------------
	 */
	function resumeActive() {
		//恢复指定记录
		$name = $this->getActionName ();
		$model = D ( $name );
		$pk = $model->getPk ();
		$id = $_GET [$pk];
		$condition = array ($pk => array ('in', $id ) );
		if (false !== $model->resume ( $condition ,'active')) {
			$this->saveLog ( 1 );
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( L ( 'RESUME_SUCCESS' ) );
		} else {
			$this->saveLog ( 0 );
			$this->error ( L ( 'RESUME_FAILED' ) );
		}
	}

    public function checkPassActive() {
		$name = $this->getActionName ();
		$model = D ( $name );
		$pk = $model->getPk ();
		$id = $_GET [$pk];
		$condition = array ($pk => array ('in', $id ) );
		if (false !== $model->checkPass ( $condition,'active')) {
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( L ( 'PASS_SUCCESS' ) );
		} else {
			$this->error ( L ( 'PASS_FAILED' ) );
		}
	}

    public function forbidActive() {
		$name = $this->getActionName ();
		$model = D ( $name );
		$pk = $model->getPk ();
		$id = $_REQUEST [$pk];
		$condition = array ($pk => array ('in', $id ) );
		$list = $model->forbid ( $condition ,'active');
		if ($list !== false) {
			$this->saveLog ( 1 );
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( L ( 'FORBID_SUCCESS' ) );
		} else {
			$this->saveLog ( 0 );
			$this->error ( L ( 'FORBID_FAILED' ) );
		}
	}
	function saveSort() {
		$seqNoList = $_POST ['seqNoList'];
		if (! empty ( $seqNoList )) {
			//更新数据对象
			$name = $this->getActionName ();
			$model = D ( $name );
			$col = explode ( ',', $seqNoList );
			//启动事务
			$model->startTrans ();
			foreach ( $col as $val ) {
				$val = explode ( ':', $val );
				$model->id = $val [0];
				$model->sort = $val [1];
				$result = $model->save ();
				if (! $result) {
					break;
				}
			}
			//提交事务
			$model->commit ();
			if ($result !== false) {
				//采用普通方式跳转刷新页面
				$this->success ( L ( 'EDIT_SUCCESS' ) );
			} else {
				$this->error ( $model->getError () );
			}
		}
	}

	public function swBestStatus() {
		$name = $this->getActionName ();
		$status = $_REQUEST ['status'];
		$model = D ( $name );
		$pk = $model->getPk ();
		$id = $_REQUEST [$pk];
		$item = $model->getById ( $id );
		if ($status) {
			$list = $model->where ( $pk . "=" . $id )->setField ( 'is_best', 0 );
		} else {
			$list = $model->where ( $pk . "=" . $id )->setField ( 'is_best', 1 );
		}
		if ($list !== false) {
			$this->saveLog ( 1 );
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( L ( 'FORBID_SUCCESS' ) );
		} else {
			$this->saveLog ( 0 );
			$this->error ( L ( 'FORBID_FAILED' ) );
		}
	}

	public function swHotStatus() {
		$name = $this->getActionName ();
		$status = $_REQUEST ['status'];
		$model = D ( $name );
		$pk = $model->getPk ();
		$id = $_REQUEST [$pk];
		$item = $model->getById ( $id );
		if ($status) {
			$list = $model->where ( $pk . "=" . $id )->setField ( 'is_recommend', 0 );
		} else {
			$list = $model->where ( $pk . "=" . $id )->setField ( 'is_recommend', 1 );
		}
		if ($list !== false) {
			//$this->saveLog ( 1 );
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ('操作成功!');
		} else {
			//$this->saveLog ( 0 );
			$this->error ('操作失败!');
		}
	}
	public function swNewStatus() {
		$name = $this->getActionName ();
		$status = $_REQUEST ['status'];
		$model = D ( $name );
		$pk = $model->getPk ();
		$id = $_REQUEST [$pk];
		$item = $model->getById ( $id );
		if ($status) {
			$list = $model->where ( $pk . "=" . $id )->setField ( 'is_new', 0 );
		} else {
			$list = $model->where ( $pk . "=" . $id )->setField ( 'is_new', 1 );
		}
		if ($list !== false) {
			$this->saveLog ( 1 );
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( L ( 'FORBID_SUCCESS' ) );
		} else {
			$this->saveLog ( 0 );
			$this->error ( L ( 'FORBID_FAILED' ) );
		}
	}
		public function swTopStatus() {
			$name=$this->getActionName();
			$status = $_REQUEST['status'];
			$model = D ($name);
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			$item = $model->getById($id);

				$list = $model->where($pk."=".$id)->setField('is_top',1);

			if ($list!==false) {
				//$this->saveLog(1);
				$this->assign ( "jumpUrl", $this->getReturnUrl () );
				$this->success ('置顶成功！');
			} else {
				$this->saveLog(0);
				$this->error  ('置顶失败!');
			}
		}

		public function uTopStatus() {
			$name=$this->getActionName();
			$status = $_REQUEST['status'];
			$model = D ($name);
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			$item = $model->getById($id);

				$list = $model->where($pk."=".$id)->setField('is_top',0);

			if ($list!==false) {
				//$this->saveLog(1);
				$this->assign ( "jumpUrl", $this->getReturnUrl () );
				$this->success ('取消置顶成功！');
			} else {
				$this->saveLog(0);
				$this->error  ('取消置顶失败!');
			}
		}
	public function doChangeSort() {
		$name = $this->getActionName ();
		$model = D ( $name );
		$pk = $model->getPk ();
		$id = $_REQUEST [$pk];
		$sort = intval ( $_REQUEST ['sort'] );
		$item = $model->getById ( $id );
		$list = $model->where ( $pk . "=" . $id )->setField ( 'sort', $sort );
		if ($list !== false) {
			$this->saveLog ( 1 );
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( L ( 'EDIT_SUCCESS' ) );
		} else {
			$this->saveLog ( 0 );
			$this->error ( L ( 'EDIT_FAILED' ) );
		}
	}
	protected function saveLog($result = "1", $dataId = 0, $msg = "") {
		if (eyooC ( "APP_LOG" )) {
			$log_app = C ( "LOG_APP" );
			$log_module = MODULE_NAME;
			$log_action = $_REQUEST [c ( "VAR_ACTION" )];
			if (in_array ( $log_action, $log_app [$log_module] )) {
				$logData ['log_module'] = $log_module;
				$logData ['log_action'] = $log_action;
				if (! $dataId) {
					$pk = M ( MODULE_NAME )->getPk ();
					$dataId = intval ( $_REQUEST [$pk] );
				}
				$logData ['data_id'] = $dataId;
				$logData ['log_time'] = gmttime ();
				$logData ['adm_id'] = intval ( $_SESSION [C ( "USER_AUTH_KEY" )] );
				$logData ['ip'] = get_client_ip ();
				$logData ['log_result'] = $result;
				$logData ['log_msg'] = $msg;
				D ( "Log" )->add ( $logData );
			}
		}
	}
	protected function uploadFile($water = 0, $dir = "attachment", $uploadType = 0, $showstatus = FALSE) {
		$water_mark = $this->getRealPath () . eyooC ( "WATER_IMAGE" );
		//dump( eyooC);
		$alpha = eyooC ( "WATER_ALPHA" );
		$place = eyooC ( "WATER_POSITION" );
		import ( "@.ORG.UploadFile" );
		$upload = new UploadFile ();
		$upload->maxSize = eyooC ( "MAX_UPLOAD" );
		$upload->allowExts = explode ( ",", eyooC ( "ALLOW_UPLOAD_EXTS" ) );
		if ($uploadType) {
			$save_rec_Path = "/Public/upload/" . $dir . "/origin/" . todate ( gmttime (), "Ym" ) . "/";
		} else {
			$save_rec_Path = "/Public/upload/" . $dir . "/" . todate ( gmttime (), "Ym" ) . "/";
		}
		$savePath = $this->getRealPath () . $save_rec_Path;
		if (! is_dir ( $savePath )) {
			mk_dir ( $savePath );
		}
		$upload->saveRule = "uniqid";
		$upload->savePath = $savePath;
		if ($upload->upload ()) {
			$uploadList = $upload->getUploadFileInfo ();
			foreach ( $uploadList as $k => $fileItem ) {
				if ($uploadType) {
					$big_width = eyooC ( "BIG_WIDTH" );
					$big_height = eyooC ( "BIG_HEIGHT" );
					$small_width = eyooC ( "SMALL_WIDTH" );
					$small_height = eyooC ( "SMALL_HEIGHT" );
					//echo 	$big_width;
					//echo 	$big_height;
					//echo 	$small_width;
					//echo 	$small_height;
					$file_name = $fileItem ['savepath'] . $fileItem ['savename'];
					$big_save_path = str_replace ( "origin", "big", $savePath );
					if (! is_dir ( $big_save_path )) {
						mk_dir ( $big_save_path );
					}
					$big_file_name = str_replace ( "origin", "big", $file_name );
					if (eyooC ( "AUTO_GEN_IMAGE" ) == 1) {
						Image::thumb ( $file_name, $big_file_name, "", $big_width, $big_height );
					} else {
						@copy ( $file_name, $big_file_name );
					}
					if ($water && file_exists ( $water_mark ) && eyooC ( "AUTO_GEN_IMAGE" ) == 1) {
						Image::water ( $big_file_name, $water_mark, $big_file_name, $alpha, $place );
					}
					$small_save_path = str_replace ( "origin", "small", $savePath );
					if (! is_dir ( $small_save_path )) {
						mk_dir ( $small_save_path );
					}
					$small_file_name = str_replace ( "origin", "small", $file_name );
					Image::thumb ( $file_name, $small_file_name, "", $small_width, $small_height );
					$big_save_rec_Path = str_replace ( "origin", "big", $save_rec_Path );
					$small_save_rec_Path = str_replace ( "origin", "small", $save_rec_Path );
					$uploadList [$k] ['recpath'] = $save_rec_Path;
					$uploadList [$k] ['bigrecpath'] = $big_save_rec_Path;
					$uploadList [$k] ['smallrecpath'] = $small_save_rec_Path;
				} else {
					$uploadList [$k] ['recpath'] = $save_rec_Path;
					$file_name = $fileItem ['savepath'] . $fileItem ['savename'];
					if (! $water && ! file_exists ( $water_mark )) {
						Image::water ( $file_name, $water_mark, $file_name, $alpha, $place );
					}
				}
			}
			if ($showstatus) {
				$result ['status'] = TRUE;
				$result ['uploadList'] = $uploadList;
				$result ['msg'] = "";
				return $result;
			}
			return $uploadList;
		}
		if ($showstatus) {
			$result ['status'] = FALSE;
			$result ['uploadList'] = FALSE;
			$result ['msg'] = $upload->getErrorMsg ();
			return $result;
		}
		return $uploadList;
	}

       protected function uploadFiles($water = 0, $dir = "attachment", $uploadType = 0, $big_width, $big_height, $small_width, $small_height, $showstatus = FALSE) {
		$water_mark = $this->getRealPath () . eyooC ( "WATER_IMAGE" );
		//dump( eyooC);
		$alpha = eyooC ( "WATER_ALPHA" );
		$place = eyooC ( "WATER_POSITION" );
		import ( "@.ORG.UploadFile" );
		$upload = new UploadFile ();
		$upload->maxSize = eyooC ( "MAX_UPLOAD" );
		$upload->allowExts = explode ( ",", eyooC ( "ALLOW_UPLOAD_EXTS" ) );
		if ($uploadType) {
			$save_rec_Path = "/Public/upload/" . $dir . "/origin/";
		} else {
			$save_rec_Path = "/Public/upload/" . $dir . "/";
		}
		$savePath = $this->getRealPath () . $save_rec_Path;
		if (! is_dir ( $savePath )) {
			mk_dir ( $savePath );
		}

		$upload->saveRule = "uniqid";
		$upload->savePath = $savePath;
		if ($upload->upload ()) {
			$uploadList = $upload->getUploadFileInfo ();
			foreach ( $uploadList as $k => $fileItem ) {
				if ($uploadType) {
				   $big_width = $big_width;
					$big_height = $big_height;
					$small_width = $small_width;
					$small_height = $small_height ;
					//echo 	$big_width;
					//echo 	$big_height;
					//echo 	$small_width;
					//echo 	$small_height;
					$file_name = $fileItem ['savepath'] . $fileItem ['savename'];
					$big_save_path = str_replace ( "origin", "big", $savePath );
					if (! is_dir ( $big_save_path )) {
						mk_dir ( $big_save_path );
					}
					$big_file_name = str_replace ( "origin", "big", $file_name );
					if (eyooC ( "AUTO_GEN_IMAGE" ) == 1) {
						Image::thumb ( $file_name, $big_file_name, "", $big_width, $big_height );
					} else {
						@copy ( $file_name, $big_file_name );
					}
					if ($water && file_exists ( $water_mark ) && eyooC ( "AUTO_GEN_IMAGE" ) == 1) {
						Image::water ( $big_file_name, $water_mark, $big_file_name, $alpha, $place );
					}
					$small_save_path = str_replace ( "origin", "small", $savePath );
					if (! is_dir ( $small_save_path )) {
						mk_dir ( $small_save_path );
					}
					$small_file_name = str_replace ( "origin", "small", $file_name );
					Image::thumb ( $file_name, $small_file_name, "", $small_width, $small_height );
					$big_save_rec_Path = str_replace ( "origin", "big", $save_rec_Path );
					$small_save_rec_Path = str_replace ( "origin", "small", $save_rec_Path );
					$uploadList [$k] ['recpath'] = $save_rec_Path;
					$uploadList [$k] ['bigrecpath'] = $big_save_rec_Path;
					$uploadList [$k] ['smallrecpath'] = $small_save_rec_Path;
				} else {
					$uploadList [$k] ['recpath'] = $save_rec_Path;
					$file_name = $fileItem ['savepath'] . $fileItem ['savename'];
					if (! $water && ! file_exists ( $water_mark )) {
						Image::water ( $file_name, $water_mark, $file_name, $alpha, $place );
					}
				}
			}
			if ($showstatus) {
				$result ['status'] = TRUE;
				$result ['uploadList'] = $uploadList;
				$result ['msg'] = "";
				return $result;
			}
			return $uploadList;
		}
		if ($showstatus) {
			$result ['status'] = FALSE;
			$result ['uploadList'] = FALSE;
			$result ['msg'] = $upload->getErrorMsg ();
			return $result;
		}
		return $uploadList;
	}
}
?>