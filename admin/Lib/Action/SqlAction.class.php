<?php
/**
 +------------------------------------------------------------------------------
 * 数据库管理
 +------------------------------------------------------------------------------
 * @package   core
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Ver$
 +------------------------------------------------------------------------------
 */
class SqlAction extends CommonAction { //类定义开始
	

	protected $db = NULL;
	
	/**
     +----------------------------------------------------------
	 * 初始化操作
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	public function __construct() {
		// 获取数据库对象实例
		parent::__construct ();
		$this->db = Db::getInstance ();
	
	}
	
	/**
     +----------------------------------------------------------
	 * 数据库管理首页
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	public function index() {
		// 获取数据库列表
		$this->getDbList ();
		// 获取当前数据库
		$dbName = $this->getUseDb ();
		// 获取当前库的数据表
		$tables = $this->db->getTables ( $dbName );
		$this->assign ( 'tables', $tables );
		$this->display ();
		return;
	}
	
	/**
     +----------------------------------------------------------
	 * Ajax方式获取数据库的表列表
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	public function getTables() {
		$dbName = $_POST ['db'];
		Session::set ( 'useDb', $dbName );
		// 获取数据库的表列表
		$tables = $this->db->getTables ( $dbName );
		$this->ajaxReturn ( $tables, '数据表获取完成', 1 );
	}
	
	/**
     +----------------------------------------------------------
	 * 复制数据表
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	public function copyTable() {
		// 获取数据库列表
		$this->getDbList ();
		$this->display ();
	}
	
	/**
     +----------------------------------------------------------
	 * 创建新的数据表
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	public function createTable() {
		$tableName = $_POST ['tableName'];
		$dbName = $_POST ['dbName'];
		$sourceTable = $_POST ['sourceTable'];
		$sourceDb = Session::get ( 'useDb' );
		$info = $this->db->query ( "SHOW CREATE TABLE {$sourceDb}.`$sourceTable`" );
		$sql = $info [0] ['Create Table'];
		$sql = preg_replace ( '/CREATE TABLE\s`' . $sourceTable . '`/is', 'CREATE TABLE `' . $tableName . '`', $sql );
		// 开始复制
		$this->db->execute ( 'USE ' . $dbName );
		$result = $this->db->execute ( $sql );
		if (false !== $result) {
			if (1 == $_POST ['option']) {
				// 复制表数据
				$sql = "INSERT INTO `{$dbName}`.`{$tableName}` SELECT * FROM `{$sourceDb}`.`{$sourceTable}` ;";
				$result = $this->db->execute ( $sql );
				if (false === $result) {
					$this->error ( '数据复制错误！' );
				}
			}
			$this->success ( '数据表复制成功！' );
		} else {
			$this->error ( '复制错误！' );
		}
	}
	
	/**
     +----------------------------------------------------------
	 * 移动数据表
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	public function moveTable() {
		// 获取数据库列表
		$this->getDbList ();
		$this->display ();
	}
	
	/**
     +----------------------------------------------------------
	 * 移动数据表
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	public function transfTable() {
		$tableName = $_POST ['tableName'];
		$dbName = $_POST ['dbName'];
		$sourceTable = $_POST ['sourceTable'];
		$sourceDb = Session::get ( 'useDb' );
		$info = $this->db->query ( "SHOW CREATE TABLE {$sourceDb}.`$sourceTable`" );
		$sql = $info [0] ['Create Table'];
		$sql = preg_replace ( '/CREATE TABLE\s`' . $sourceTable . '`/is', 'CREATE TABLE `' . $tableName . '`', $sql );
		// 创建新表
		$this->db->execute ( 'USE ' . $dbName );
		$result = $this->db->execute ( $sql );
		if (false !== $result) {
			$sql = "INSERT INTO `{$dbName}`.`{$tableName}` SELECT * FROM `{$sourceDb}`.`{$sourceTable}` ;";
			$result = $this->db->execute ( $sql );
			// 删除原来表
			$this->db->execute ( 'USE ' . Session::get ( 'useDb' ) );
			$result = $this->db->execute ( 'DROP TABLE `' . $sourceTable . '`' );
			if (false === $result) {
				$this->error ( '当前表删除失败！' );
			}
			$this->success ( '数据表移动成功！' );
		} else {
			$this->error ( '移动错误！' );
		}
	}
	
	/**
     +----------------------------------------------------------
	 * 显示数据表结构
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	public function showTable() {
		$table = $_GET ['table'];
		$this->db->execute ( 'USE ' . Session::get ( 'useDb' ) );
		$list = $this->db->query ( "SHOW FULL COLUMNS FROM $table" );
		$json = array ();
		foreach ( $list as $key => $val ) {
			$attribute = explode ( ' ', $val ['Type'] );
			$type = explode ( '(', $attribute [0] );
			$val ['Type'] = strtoupper ( $type [0] );
			if (isset ( $type [1] )) {
				$val ['Length'] = substr ( $type [1], 0, - 1 );
			} else {
				$val ['Length'] = '';
			}
			$val ['Sign'] = isset ( $attribute [1] ) ? strtoupper ( $attribute [1] ) : '';
			if (is_null ( $val ['Default'] )) {
				$val ['Default'] = 'NULL';
			}
			$list [$key] = $val;
			$json [$val ['Field']] = $val;
		}
		$this->assign ( 'json', json_encode ( $json ) );
		$this->assign ( 'list', $list );
		$this->display ();
	}
	
	/**
     +----------------------------------------------------------
	 * 添加字段
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	public function addField() {
		$table = $_POST ['table'];
		$len = count ( $_POST ['name'] );
		$sql = "ALTER TABLE `{$table}` ";
		for($i = 0; $i < $len; $i ++) {
			if (! empty ( $_POST ['name'] [$i] )) {
				$field = $_POST ['name'] [$i];
				$type = $_POST ['type'] [$i];
				$length = $_POST ['length'] [$i];
				$attribute = $_POST ['attribute'] [$i];
				$null = $_POST ['null'] [$i];
				$default = $_POST ['default'] [$i];
				$autoinc = $_POST ['autoinc'] [$i];
				$comment = $_POST ['comment'] [$i];
				$after = $_POST ['after'] [$i];
				$sql .= " ADD `{$field}` {$type}";
				if (! empty ( $length )) {
					$sql .= "( {$length} )";
				}
				if (! empty ( $attribute )) {
					$sql .= " {$attribute} ";
				}
				$createSql .= " {$null} ";
				if (! empty ( $default )) {
					$sql .= " DEFAULT '{$default}'";
				}
				if (! empty ( $autoinc )) {
					$sql .= " {$autoinc} ";
				}
				if (! empty ( $comment )) {
					$sql .= " COMMENT '{$comment}'";
				}
				if (! empty ( $after )) {
					$sql .= "AFTER `{$after}` ";
				}
				$sql .= ',';
				$alter = true;
			}
		}
		if (empty ( $alter )) {
			$this->error ( '字段为空' );
		}
		$sql = substr ( $sql, 0, - 1 );
		$this->db->execute ( 'USE ' . Session::get ( 'useDb' ) );
		$result = $this->db->execute ( $sql );
		if (false !== $result) {
			$this->success ( '字段增加成功！' );
		} else {
			$this->error ( '字段添加失败！' );
		}
	}
	
	/**
     +----------------------------------------------------------
	 * 编辑字段
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	public function editField() {
		$table = $_GET ['table'];
		$name = $_GET ['name'];
	}
	
	/**
     +----------------------------------------------------------
	 * 更新字段
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	public function updateField() {
		if (empty ( $_POST ['name'] )) {
			$this->error ( '字段为空' );
		}
		$table = $_POST ['table'];
		$sql = "ALTER TABLE `{$table}` ";
		$len = count ( $_POST ['name'] );
		for($i = 0; $i < $len; $i ++) {
			if (! empty ( $_POST ['name'] [$i] )) {
				$name = $_POST ['change'] [$i];
				$field = $_POST ['name'] [$i];
				$type = $_POST ['type'] [$i];
				$length = $_POST ['length'] [$i];
				$attribute = $_POST ['attribute'] [$i];
				$null = $_POST ['null'] [$i];
				$default = $_POST ['default'] [$i];
				$autoinc = $_POST ['autoinc'] [$i];
				$comment = $_POST ['comment'] [$i];
				$sql .= " CHANGE `{$name}` `{$field}` {$type}";
				if (! empty ( $length )) {
					$sql .= "( {$length} )";
				}
				if (! empty ( $attribute )) {
					$sql .= " {$attribute} ";
				}
				$sql .= " {$null} ";
				if (! empty ( $default )) {
					$sql .= " DEFAULT '{$default}'";
				}
				if (! empty ( $autoinc )) {
					$sql .= " {$autoinc} ";
				}
				if (! empty ( $comment )) {
					$sql .= " COMMENT '{$comment}'";
				}
				$sql .= ',';
			}
		}
		$sql = substr ( $sql, 0, - 1 );
		$this->db->execute ( 'USE ' . Session::get ( 'useDb' ) );
		$result = $this->db->execute ( $sql );
		if (false !== $result) {
			$this->success ( '字段修改成功！' );
		} else {
			$this->error ( '字段修改失败！' );
		}
	}
	
	/**
     +----------------------------------------------------------
	 * 删除字段
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	public function dropField() {
		$table = $_POST ['table'];
		$name = $_POST ['name'];
		$sql = "ALTER TABLE `{$table}` DROP `{$name}` ";
		$this->db->execute ( 'USE ' . Session::get ( 'useDb' ) );
		$result = $this->db->execute ( $sql );
		if (false !== $result) {
			$this->success ( '字段删除成功！' );
		} else {
			$this->error ( '字段删除失败！' );
		}
	}
	
	/**
     +----------------------------------------------------------
	 * 添加唯一
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	public function addUnique() {
		$table = $_POST ['table'];
		$name = $_POST ['name'];
		$sql = "ALTER TABLE `{$table}` ADD UNIQUE (`{$name}` )";
		$this->db->execute ( 'USE ' . Session::get ( 'useDb' ) );
		$result = $this->db->execute ( $sql );
		if (false !== $result) {
			$this->success ( '设置成功！' );
		} else {
			$this->error ( '设置失败！' );
		}
	}
	
	/**
     +----------------------------------------------------------
	 * 添加索引
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	public function addIndex() {
		$table = $_POST ['table'];
		$name = $_POST ['name'];
		$sql = "ALTER TABLE `{$table}` ADD INDEX (`{$name}` )";
		$this->db->execute ( 'USE ' . Session::get ( 'useDb' ) );
		$result = $this->db->execute ( $sql );
		if (false !== $result) {
			$this->success ( '设置成功！' );
		} else {
			$this->error ( '设置失败！' );
		}
	}
	
	/**
     +----------------------------------------------------------
	 * 添加全文搜索
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	public function addFulltext() {
		$table = $_POST ['table'];
		$name = $_POST ['name'];
		$sql = "ALTER TABLE `{$table}` ADD FULLTEXT (`{$name}` )";
		$this->db->execute ( 'USE ' . Session::get ( 'useDb' ) );
		$result = $this->db->execute ( $sql );
		if (false !== $result) {
			$this->success ( '设置成功！' );
		} else {
			$this->error ( '设置失败！' );
		}
	}
	
	/**
     +----------------------------------------------------------
	 * 修改数据表
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	public function editTable() {
		$table = $_GET ['table'];
		$dbName = Session::get ( 'useDb' );
		$result = $this->db->query ( 'SHOW TABLE STATUS FROM ' . $dbName . ' WHERE Name="' . $table . '"' );
		$vo = $result [0];
		$this->assign ( 'vo', $vo );
		$this->display ();
	}
	
	/**
     +----------------------------------------------------------
	 * 更新数据表
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	public function updateTable() {
		$Name = $_POST ['Name'];
		$Engine = $_POST ['Engine'];
		$Comment = $_POST ['Comment'];
		$Charset = $_POST ['Charset'];
		$Collation = $_POST ['Collation'];
		$result = $this->db->execute ( "ALTER TABLE `$Name` COMMENT = '$Comment' ENGINE = $Engine DEFAULT CHARACTER SET $Charset COLLATE $Collation" );
		if (false === $result) {
			$this->error ( '更新错误！' );
		} else {
			$this->success ( '更新表成功！' );
		}
	}
	
	/**
     +----------------------------------------------------------
	 * 高级模式数据库管理
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	public function adv() {
		// 获取数据库列表
		$this->getDbList ();
		// 获取当前数据库
		$dbName = $this->getUseDb ();
		$result = $this->db->query ( 'SHOW TABLE STATUS FROM ' . $dbName );
		$this->assign ( 'list', $result );
		$this->display ();
	}
	
	/**
     +----------------------------------------------------------
	 * 浏览数据表的数据 支持分页
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	public function selectTable() {
		$table = Session::get ( 'useDb' ) . '.' . $_GET ['table'];
		$where = array ();
		if ($_GET ['map']) {
			$where ['_string'] = base64_decode ( $_GET ['map'] );
		}
		$fields = $this->db->getFields ( $table );
		$Model = new Model ();
		$count = $Model->table ( $table )->where ( $where )->count ();
		$list [] = array_keys ( $fields );
		$this->assign ( 'fieldCount', count ( $fields ) + 1 );
		if ($_GET ['bench']) {
			$this->db->execute ( 'SET PROFILING=1;' );
		}
		import ( "ORG.Util.Page" );
		//创建分页对象
		if (! empty ( $_REQUEST ['listRows'] )) {
			$listRows = $_REQUEST ['listRows'];
		} else {
			$listRows = 25;
		}
		//排序字段 默认为主键名
		if (isset ( $_REQUEST ['_order'] )) {
			$order = $_REQUEST ['_order'];
		} else {
			$order = ! empty ( $sortBy ) ? $sortBy : $list [0] [0];
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		$p = new Page ( $count, $listRows );
		//分页查询数据
		$voList = $Model->table ( $table )->where ( $where )->order ( $order . ' ' . $sort )->limit ( $p->firstRow . ',' . $p->listRows )->select ();
		if ($_GET ['bench']) {
			$data = $this->db->query ( 'SHOW PROFILE' );
			$fields = array_keys ( $data [0] );
			$a [] = $fields;
			foreach ( $data as $key => $val ) {
				$val = array_values ( $val );
				$a [] = $val;
			}
			$this->assign ( 'bench', $a );
		}
		//分页显示
		$page = $p->show ();
		//列表排序显示
		$sortImg = $sort; //排序图标
		$sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
		$sort = $sort == 'desc' ? 1 : 0; //排序方式
		//模板赋值显示
		$this->assign ( 'list', array_merge ( $list, $voList ) );
		$this->assign ( 'sort', $sort );
		$this->assign ( 'order', $order );
		$this->assign ( 'sortImg', $sortImg );
		$this->assign ( 'sortType', $sortAlt );
		$this->assign ( "page", $page );
		$this->display ( 'table' );
	}
	
	/**
     +----------------------------------------------------------
	 * 导入文件
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	public function import() {
		// 获取数据库列表
		$this->getDbList ();
		$this->assign ( 'useDb', Session::get ( 'useDb' ) );
		$this->display ();
	}
	
	/**
     +----------------------------------------------------------
	 * 导入SQL文件
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	public function importSql() {
		if (! empty ( $_FILES ['sqlFile'] ['name'] )) {
			// 判断文件后缀
			$pathinfo = pathinfo ( $_FILES ['sqlFile'] ['name'] );
			$ext = $pathinfo ['extension'];
			if (! in_array ( $ext, array ('sql', 'rar', 'gz', 'txt' ) )) {
				$this->error ( '文件格式不符合！' );
			}
			// 导入SQL文件
			$sql = file_get_contents ( $_FILES ['sqlFile'] ['tmp_name'] );
		} elseif (! empty ( $_POST ['sql'] )) {
			$sql = $_POST ['sql'];
		} else {
			$this->error ( '选择要导入的文件' );
		}
		$sql = str_replace ( "\r\n", "\n", $sql );
		$sql = auto_charset ( $sql, $_POST ['charset'], 'utf-8' );
		unlink ( $_FILES ['sqlFile'] ['tmp_name'] );
		if (false === $this->patchExecute ( $sql )) {
			$this->error ( '导入错误！' );
		} else {
			$this->success ( '导入完成！' );
		}
	}
	
	// 批量执行SQL语句
	protected function patchExecute($querySql) {
		if (is_string ( $querySql )) {
			$querySql = explode ( ";\n", trim ( $querySql ) );
		}
		$ret = array ();
		$num = 0;
		foreach ( $querySql as $query ) {
			$queries = explode ( "\n", trim ( $query ) );
			foreach ( $queries as $query ) {
				$ret [$num] .= $query [0] == '#' || $query [0] . $query [1] == '--' ? '' : $query;
			}
			$num ++;
		}
		if (isset ( $_POST ['dbName'] )) {
			$dbName = $_POST ['dbName'];
		} else {
			$dbName = Session::get ( 'useDb' );
		}
		$this->db->execute ( 'USE ' . $dbName );
		foreach ( $ret as $query ) {
			if (! empty ( $query )) {
				$result = $this->db->execute ( $query );
				if (false === $result) {
					return false;
				}
			}
		}
		return $result;
	}
	
	/**
     +----------------------------------------------------------
	 * 导出
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	public function output() {
		$tables = $this->db->getTables ( Session::get ( 'useDb' ) );
		$this->assign ( 'tables', $tables );
		$this->display ();
	}
	
	/**
     +----------------------------------------------------------
	 * 导出SQL文件
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	public function outputData() {
		if (empty ( $_POST ['tableName'] )) {
			// 默认导出所有表
			$tables = $this->db->getTables ( Session::get ( 'useDb' ) );
		} else {
			// 导出指定表
			$tables = $_POST ['tableName'];
		}
		$this->db->execute ( 'USE ' . Session::get ( 'useDb' ) );
		// 组装导出SQL
		$sql = "-- ThinkPHP SQL Dump\n-- http://www.thinkphp.cn\n\n";
		foreach ( $tables as $key => $table ) {
			$sql .= "-- \n-- 表的结构 `$table`\n-- \n";
			$info = $this->db->query ( "SHOW CREATE TABLE  $table" );
			$sql .= $info [0] ['Create Table'];
			$sql .= ";\n-- \n-- 导出表中的数据 `$table`\n--\n";
			$result = $this->db->query ( "SELECT * FROM $table " );
			foreach ( $result as $key => $val ) {
				foreach ( $val as $k => $field ) {
					if (is_string ( $field )) {
						$val [$k] = '\'' . $this->db->escape_string ( $field ) . '\'';
					} elseif (empty ( $field )) {
						$val [$k] = 'NULL';
					}
				}
				$sql .= "INSERT INTO `$table` VALUES (" . implode ( ',', $val ) . ");\n";
			}
		}
		$filename = empty ( $_POST ['tableName'] ) ? Session::get ( 'useDb' ) : implode ( ',', $_POST ['tableName'] );
		import ( "ORG.Net.Http" );
		if (empty ( $_POST ['zip'] )) {
			file_put_contents ( TEMP_PATH . $filename . '.sql', trim ( $sql ) );
			Http::download ( TEMP_PATH . $filename . '.sql' );
		} else {
			$zip = new ZipArchive ();
			if ($zip->open ( TEMP_PATH . $filename . '.zip', ZIPARCHIVE::CREATE ) !== TRUE) {
				exit ( "cannot open <$filename>\n" );
			}
			$zip->addFromString ( $filename . '.sql', trim ( $sql ) );
			//$zip->addFile(TEMP_PATH.'thinkcms.sql',"ddd/test.sql");
			$zip->close ();
			Http::download ( TEMP_PATH . $filename . '.zip' );
		}
	}
	
	/**
     +----------------------------------------------------------
	 * 生成数据表
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	public function buildTable() {
		// 组装SQL
		$tableName = $_POST ['tableName'];
		$tableComment = $_POST ['tableComment'];
		$tableType = $_POST ['tableType'];
		$tableCharset = $_POST ['tableCharset'];
		if (empty ( $tableName )) {
			$this->error ( '数据表名称必须！' );
		}
		$createSql = "CREATE TABLE `$tableName` (";
		$len = count ( $_POST ['name'] );
		for($i = 0; $i < $len; $i ++) {
			if (! empty ( $_POST ['name'] [$i] )) {
				$field = $_POST ['name'] [$i];
				$type = $_POST ['type'] [$i];
				$length = $_POST ['length'] [$i];
				$attribute = $_POST ['attribute'] [$i];
				$null = $_POST ['null'] [$i];
				$default = $_POST ['default'] [$i];
				$autoinc = $_POST ['autoinc'] [$i];
				$comment = $_POST ['comment'] [$i];
				$createSql .= "`{$field}` {$type}";
				if (! empty ( $length )) {
					$createSql .= "( {$length} )";
				}
				if (! empty ( $attribute )) {
					$createSql .= " {$attribute} ";
				}
				$createSql .= " {$null} ";
				if (! empty ( $default )) {
					$createSql .= " DEFAULT '{$default}'";
				}
				if (! empty ( $autoinc )) {
					$createSql .= " {$autoinc} ";
				}
				if (! empty ( $comment )) {
					$createSql .= " COMMENT '{$comment}'";
				}
				$createSql .= ',';
				$valid = true;
			}
		}
		if (empty ( $valid )) {
			$this->error ( '没有定义任何字段！' );
		}
		for($i = 0; $i < $len; $i ++) {
			if (! empty ( $_POST ['extra'] [$i] )) {
				$createSql .= "{$_POST['extra'][$i]} ( `{$_POST['name'][$i]}`) ,";
			}
		}
		$createSql = substr ( $createSql, 0, - 1 );
		$createSql .= ") ENGINE = {$tableType} CHARACTER SET {$tableCharset}  COMMENT = '{$tableComment}' ";
		$this->db->execute ( 'USE ' . Session::get ( 'useDb' ) );
		if (false !== $this->db->execute ( $createSql )) {
			$this->success ( '表创建成功' );
		} else {
			$this->error ( '表创建错误！' . $this->db->getlastsql () );
		}
	}
	
	/**
     +----------------------------------------------------------
	 * 创建数据库
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	public function createDb() {
		$dbName = $_POST ['dbName'];
		$charset = $_POST ['charset'];
		$collation = $_POST ['db_collation'];
		$result = $this->db->execute ( 'CREATE DATABASE `' . $dbName . '` DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collation . ';' );
		if (false === $result) {
			$this->error ( '创建失败！' );
		} else {
			$this->success ( '创建成功！' );
		}
	}
	
	/**
     +----------------------------------------------------------
	 * 删除数据表中的某个记录
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	public function deleteData() {
		//删除指定记录
		$model = new Model ();
		if (! empty ( $model )) {
			$id = $_REQUEST ['id'];
			$table = $_REQUEST ['table'];
			if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				if ($model->table ( Session::get ( 'useDb' ) . '.' . $table )->where ( $condition )->delete ()) {
					$this->success ( L ( '_DELETE_SUCCESS_' ) );
				} else {
					$this->error ( L ( '_DELETE_FAIL_' ) );
				}
			} else {
				$this->error ( '非法操作' );
			}
		}
	}
	
	/**
     +----------------------------------------------------------
	 * 执行SQL语句
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	public function execute() {
		$sql = trim ( $_REQUEST ['sql'] );
		if (MAGIC_QUOTES_GPC) {
			$sql = stripslashes ( $sql );
		}
		if (empty ( $sql )) {
			$this->error ( 'SQL不能为空！' );
		}
		$this->db->execute ( 'USE ' . Session::get ( 'useDb' ) );
		if (! empty ( $_POST ['bench'] )) {
			$this->db->execute ( 'SET PROFILING=1;' );
		}
		$startTime = microtime ( TRUE );
		
		/* 解析查询项  - 如果是变更操作 */
		if (!preg_match ( "/^(?:UPDATE|DELETE|TRUNCATE|ALTER|DROP|FLUSH|INSERT|REPLACE|SET|CREATE)\\s+/i", $sql )) {
			$sqlx = str_replace ( "\r", '', $sql );
			$query_items = explode ( ";\n", $sqlx );
			foreach ( $query_items as $key => $value ) {
				if (empty ( $value )) {
					unset ( $query_items [$key] );
				}
			}
			/* 如果是多条语句，拆开来执行 */
			
			if (count ( $query_items ) > 1) {
				// 记录执行SQL语句
				foreach ( $query_items as $key => $value ) {
					$this->db->query ( $value );
				}
				$runtime = number_format ( (microtime ( TRUE ) - $startTime), 6 );
				
				Log::write ( 'RunTime:' . $runtime . 's SQL = ' . $sql, Log::SQL );
				$this->ajaxReturn ( $array, 'SQL执行结束！', 1 );
				return; //退出函数
			}
		}
		
		$queryIps = 'INSERT|UPDATE|DELETE|REPLACE|' . 'CREATE|DROP|' . 'LOAD DATA|SELECT .* INTO|COPY|' . 'ALTER|GRANT|TRUNCATE|REVOKE|' . 'LOCK|UNLOCK';
		if (preg_match ( '/^\s*"?(' . $queryIps . ')\s+/i', $sql )) {
			$result = $this->db->execute ( $sql );
			$type = 'execute';
		} else {
			$result = $this->db->query ( $sql );
			$type = 'query';
		}
		$runtime = number_format ( (microtime ( TRUE ) - $startTime), 6 );
		if (! empty ( $_POST ['record'] )) {
			// 记录执行SQL语句
			Log::write ( 'RunTime:' . $runtime . 's SQL = ' . $sql, Log::SQL );
		}
		if (false !== $result) {
			$array [] = $runtime . 's';
			if (! empty ( $_POST ['bench'] )) {
				$data = $this->db->query ( 'SHOW PROFILE' );
				$fields = array_keys ( $data [0] );
				$a [] = $fields;
				foreach ( $data as $key => $val ) {
					$val = array_values ( $val );
					$a [] = $val;
				}
				$array [] = $a;
			} else {
				$array [] = '';
			}
			if ($type == 'query') {
				if (empty ( $result )) {
					$this->ajaxReturn ( $array, 'SQL执行成功！', 1 );
				}
				$fields = array_keys ( $result [0] );
				$array [] = $fields;
				foreach ( $result as $key => $val ) {
					$val = array_values ( $val );
					$array [] = $val;
				}
				$this->ajaxReturn ( $array, 'SQL执行成功！', 1 );
			} else {
				$this->ajaxReturn ( $array, 'SQL执行成功！', 1 );
			}
		} else {
			$this->error ( 'SQL错误！' );
		}
	}
	
	/**
     +----------------------------------------------------------
	 * 获取数据库列表
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	
	protected function getDbList() {
		if (! $dbs = Session::get ( '_databaseList_' )) {
			$dbs = $this->db->query ( 'show databases' );
			Session::set ( '_databaseList_', $dbs );
		}
		$this->assign ( 'dbs', $dbs );
	}
	
	/**
     +----------------------------------------------------------
	 * 获取当前操作的数据库
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @return string
     +----------------------------------------------------------
	 */
	protected function getUseDb() {
		if (isset ( $_GET ['dbName'] )) {
			$dbName = $_GET ['dbName'];
			Session::set ( 'useDb', $dbName );
		} elseif (Session::get ( 'useDb' )) {
			$dbName = Session::get ( 'useDb' );
		} else {
			$dbName = C ( 'DB_NAME' );
			Session::set ( 'useDb', $dbName );
		}
		$this->assign ( 'useDb', $dbName );
		return $dbName;
	}

} //类定义结束
?>