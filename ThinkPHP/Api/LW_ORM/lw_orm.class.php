<?php
class lw_orm  {



    const DB_FETCH_ASSOC    = 1;
    const DB_FETCH_ARRAY    = 3;
    const DB_FETCH_ROW      = 2;
    const DB_FETCH_DEFAULT  = self::DB_FETCH_ASSOC;

    public static $db;
    protected static $db_type = array('mysql' => 'MySQL', 'oracle' => 'Oracle');


	protected static $lw_db_configs = array(
											"common"=>array(
												"db_host"	=>	 "localhost",
												"db_name"	=>	 "ts_2",
												"db_user"	=>	 "root",
												"db_pass"	=>	 "",
												"db_type"	=>	 "mysql",
												"db_char"	=>	 "utf8",
												"db_prefix"	=>   "ts_",
											),
										 );



    protected $u_conn;
    protected $q_conn;
    protected $dsn;
    protected $db_key;
    protected $fecth_mode;
    protected $sql;
    protected $sqls;
    protected $qrs;
    protected $urs;
    protected $u_sqls;
    protected $q_sqls;
    protected $query_num;
    protected $update_num;
	protected $last_sql;
	protected $db_prefix;



	/*---------------------------------------------------------------------------------------------------------------------
	= 一、初始化部分
	---------------------------------------------------------------------------------------------------------------------*/


    /**
     * mysql构造函数
     *
     * @param array $db_info 数据库配置信息
     * @param string $db_key db的key
     * @param 返回的数据格式 $fetch_mode
     */
    public function __construct(& $db_info, $db_key, $fetch_mode) {
        $this->db_key = $db_key;
        $this->dsn =& $db_info;
        $this->fecth_mode = $fetch_mode;
    }



    /**
     * DB初始化
     *
     * @param array $dsn 配置文件中的DB信息
     * @param string $db_key 配置中的数据库KEY名
     * @param const $fetch_mode 返回数据的KEY类型
     * @return array|DB DB对象
     */
    public static function &init(& $dsn = "" , $db_key = "common", $fetch_mode = self::DB_FETCH_ASSOC) {
		if(!$dsn) $dsn = self::$lw_db_configs;
        $key = explode('.', $db_key);
        $key = "['" . implode("']['" , $key) . "']";

        eval('$flag = isset(self::$db' . $key . ');');
        eval("\$db_info = \$dsn" . $key . ";");

        if (!$flag) {
//            $class_name = 'LW_DB_' . self::$db_type[strtolower($db_info['db_type'])];
//            $obj = new $class_name($db_info, $db_key, $fetch_mode);
			$obj = new lw_orm($db_info, $db_key, $fetch_mode);
            eval('self::$db' . $key . ' =& $obj;');
//			var_dump('self::$db' . $key . ' =& $obj;');
            unset($obj);
        }
		
        return self::$db[$db_key];
    }



    /**
     * 连接数据库
     *
     * 连接数据库之前可能需要改变DSN，一般不建议使用此方法
     *
     * @param string $type 选择连接主服务器或者从服务器
     * @return boolean
     */
    public function connect($type = "slave") {
        if ($type == "master" || !isset($this->dsn["slave"])) {

            $db_host = isset($this->dsn["master"]) ? $this->dsn["master"]["db_host"] : $this->dsn["db_host"];
            $db_name = isset($this->dsn["master"]) ? $this->dsn["master"]["db_name"] : $this->dsn["db_name"];
            $db_user = isset($this->dsn["master"]) ? $this->dsn["master"]["db_user"] : $this->dsn["db_user"];
            $db_pass = isset($this->dsn["master"]) ? $this->dsn["master"]["db_pass"] : $this->dsn["db_pass"];
			$db_char = isset($this->dsn["master"]) ? $this->dsn["master"]["db_char"] : $this->dsn["db_char"];


            $this->u_conn = mysql_connect($db_host, $db_user, $db_pass);
			mysql_query("SET NAMES " . $db_char . " ;");
            
			if (!$this->u_conn) {
                exit('更新数据库连接失败');
            }
            if (!mysql_select_db($db_name)) {
                exit('更新数据库选择失败');
            }
            if (!isset($this->dsn["slave"])) {
                $this->q_conn =& $this->u_conn;
            }
        } else {
            if (empty($this->dsn["slave"])) {
                $this->connect('master');
                return $this->q_conn =& $this->u_conn;
            }
            if (empty($_COOKIE[COOKIE_PREFIX . $this->db_key . '_db_no'])) {
                $db_no = array_rand($this->dsn["slave"]);
                setcookie(COOKIE_PREFIX . $this->db_key . '_db_no', $db_no, null, COOKIE_PATH, COOKIE_DOMAIN);
            } else {
                $db_no = $_COOKIE[COOKIE_PREFIX . $this->db_key . '_db_no'];
            }
            $db_info = $this->dsn["slave"][$db_no];
            $db_host = $db_info["db_host"];
            $db_name = $db_info["db_name"];
            $db_user = $db_info["db_user"];
            $db_pass = $db_info["db_pass"];
			$db_char = $db_info["db_char"];

            $this->q_conn = mysql_connect($db_host, $db_user, $db_pass);
			mysql_query("SET NAMES " . $db_char . " ;");

            if (!$this->q_conn) {
                if (!$this->u_conn) {
                    $this->connect('slave');
                }
                $this->q_conn =& $this->u_conn;
                if (!$this->q_conn) {
                    exit('查询数据库连接失败');
                }
            } else {
                if (!mysql_select_db($this->q_conn, $db_name)) {
                    exit('查询数据库选择失败');
                }
            }
        }
        return true;
    }

    /**
     * 关闭数据库连接
     *
     * 一般不需要调用此方法
     */
    public function close() {
        if ($this->u_conn === $this->q_conn) {
            if (is_object($this->u_conn)) {
                mysql_close($this->u_conn);
            }
        } else {
            if (is_object($this->u_conn)) {
                mysql_close($this->u_conn);
            }
            if (is_object($this->q_conn)) {
                mysql_close($this->q_conn);
            }
        }
    }




	/*---------------------------------------------------------------------------------------------------------------------
	= 二、适用于MYSQL的数据库操作封装
	---------------------------------------------------------------------------------------------------------------------*/




    /**
     * 执行一个SQL查询
     *
     * 本函数仅限于执行SELECT类型的SQL语句
     *
     * @param string $sql SQL查询语句
     * @return resource 返回查询结果资源句柄
     */
    public function query($sql) {

        $this->sqls[] = $sql;
        $this->q_sqls[] = $sql;
        $this->sql = $sql;

        if (!$this->q_conn) {
            $this->connect("slave");
        }
        $this->qrs = mysql_query($sql);

        if (!$this->qrs) {
			var_dump($sql);
            exit('<br/>查询失败:' . mysql_error($this->q_conn));
        } else {
            $this->query_num++;
            return $this->qrs;
        }
    }



//DIY START

	public function insert($sql) {
	
		$this -> query($sql);
	
		return mysql_insert_id($this->u_conn);

	}

	public function update($sql){
		
		    $this -> query($sql);
			return mysql_affected_rows($this->q_conn);	
	}

	public function del($sql){
		$this -> query($sql);
		return mysql_affected_rows($this->q_conn);	
	}

	public function delAll($sql){
		$this -> query($sql);
		return mysql_affected_rows($this->q_conn);	
	}

	public function getOne($sql) {
		$query = $this -> query($sql);
		return $this -> fetch($query);
	}

	public function getAll($sql){
		$arr = array();
		$query = $this -> query($sql);
		while ($val = $this -> fetch($query)) {
			$arr[] = $val;
		} 
		return $arr;	
	}

	public function fetch($query = null, $type = 1)
	{
		!$query && $query = $this -> _rs;
		if ($type == 0) {
			return mysql_fetch_object($query);
		} else {
			return mysql_fetch_array($query, $type);
		} 
	} 
//DIY END



    /**
     * 返回SQL语句执行结果集中的第一列数据
     *
     * @param string $sql 需要执行的SQL语句
     * @param mixed $limit 整型或者字符串类型，如10|10,10
     * @return array 结果集数组
     */
    public function getCol($sql, $limit = null) {
        if (!$rs = $this->query($sql, $limit, true)) {
            return false;
        }
        $result = array();
        while ($rows = $this->fetch($rs, self::DB_FETCH_ROW)) {
            $result[] = $rows[0];
        }
        $this->free($rs);
        return $result;
    }

    /**
     * 返回SQL语句执行结果中的第一行数据
     *
     * @param string $sql 需要执行的SQL语句
     * @param const $fetch_mode 返回的数据格式
     * @return array 结果集数组
     */
    public function getRow($sql, $fetch_mode = self::DB_FETCH_DEFAULT) {
        if (!$rs = $this->query($sql, 1, true)) {
            return false;
        }
        $row = $this->fetch($rs, $fetch_mode);
        $this->free($rs);
        return $row;
    }



    /**
     * 返回最近一次查询返回的结果集条数
     *
     * @return int
     */
    public function rows() {
        return mysql_num_rows($this->qrs);
    }

    /**
     * 释放当前查询结果资源句柄
     *
     */
    public function free($rs) {
        if ($rs) {
            return mysql_free_result($rs);
        }
    }

    /**
     * 转义需要插入或者更新的字段值
     *
     * 在所有查询和更新的字段变量都需要调用此方法处理数据
     *
     * @param mixed $str 需要处理的变量
     * @return mixed 返回转义后的结果
     */
    public function escape($str) {
        return addslashes($str);
    }

    /**
     * 析构函数，暂时不需要做什么处理
     *
     */
    public function __destruct() {
    }



	/*---------------------------------------------------------------------------------------------------------------------
	= 三、ORM部分
	---------------------------------------------------------------------------------------------------------------------*/

//   public function __construct($lw_db_configs) {
//		 $db2 =  lw_orm::init($lw_db_configs, 'common', 1);
//		 $this->db = $db2['common'];
//		 $this -> _options['table'] = $lw_db_configs['common']["db_pefix"].strtolower($this->getModelName());
//   }



//    public function getModelName()
//    {
//		$this->model_name = $this->table_name;
//        if(empty($this->model_name)) {
//            $this->model_name =   substr(get_class($this),0,-7);
//        }
//        return $this->model_name;
//    }

	/*-------------------------------------
	= 添加 
	-------------------------------------*/
	public function add($data = array())
	{
		$cols = array();
		$vals = array();
		$one = reset($data);
		if (is_array($one)) {
			$cols = implode(',', $this -> deal_field(array_keys($one)));
			foreach($data as $val) {
				$vals[] = '(' . implode(',', $this -> deal_value($val)) . ')' ;
			} 
			$vals = implode(',', $vals);
		} else {
			$cols = implode(',', $this -> deal_field(array_keys($data)));
			$vals = '(' . implode(',', $this -> deal_value($data)) . ')';
		} 
		$sql = "INSERT INTO " .  $this -> parse_table() . " ( {$cols} ) VALUES {$vals}";
		//ts_dump($sql);
		$this->last_sql = $sql;
		return $this->insert($sql);
	} 

	
	/*-------------------------------------
	= 更新
	-------------------------------------*/
	public function save($data = null)
	{
		$table = $this -> parse_table();
		$where = $this -> parse_where();
		if (!empty($where)) {
			$set = array();

			foreach ($data as $col => $val) {
				$set[] = $this -> deal_field($col) . ' = ' . $this -> deal_value($val);
			} 
			$set = implode(',', $set);
			$where = $this -> deal_where($where);
			!empty($where) && $where = " WHERE {$where}";
			$sql = "UPDATE " . $this -> deal_field($table) . " SET {$set} {$where}";
			$this->last_sql = $sql;
			return $this->update($sql);


		} else {
			return $this -> add($data);
		} 
	} 


	/*-------------------------------------
	= 删除
	-------------------------------------*/
	public function delete()
	{
		$table = $this -> parse_table();
		$where = $this -> parse_where();
		if (!$where) {
			throw new DB_Exception('删除时的查询为空!');
		} 
		$where = $this -> deal_where($where);
		!empty($where) && $where = " WHERE {$where}";
		$sql = "DELETE FROM " . $this -> deal_field($table) . " {$where}";
		$this->last_sql = $sql;

		return $this->del($sql);


	} 

	public function deleteAll()
	{
		$table = $this -> parse_table();

		$sql = "DELETE FROM " . $this -> deal_field($table) ;
		$this->last_sql = $sql;

		return $this->delAll($sql);

	} 

	/*-------------------------------------
	= 查询
	-------------------------------------*/
	public function find($id=null)
	{
		if($id) $this -> _options['where'] = "id=".intval($id);
		$sql = $this -> parse_select();
		$this->last_sql = $sql;

		return $this->getOne($sql);


	} 
	public function findAll()
	{
		$sql = $this -> parse_select();
		$this->last_sql = $sql;
		return $this->getAll($sql);
	} 
//
//	public function count() {
//		$sql = $this -> parse_select();
//		$this->last_sql = $sql;
//		return $this->getOne($sql);
//	}

	/*-------------------------------------
	= 条件处理 --- 添加，更新
	-------------------------------------*/
	public function deal_field($str = '')
	{
		if (is_array($str)) {
			$str = array_map(array(__CLASS__, __METHOD__), $str);
			return $str;
		} 
		$str && $str = "`{$str}`";
		return $str;
	} 
	public function deal_value($str = '')
	{
		if (is_array($str)) {
			$str = array_map(array(__CLASS__, __METHOD__), $str);
			return $str;
		} 
	
		$str = addslashes($str);
		$str = is_string($str)?"'{$str}'":"{$str}";
		return $str;
	} 

	/*-------------------------------------
	= 条件处理 --- 查询
	-------------------------------------*/
	public function parse_select()
	{
		if (!empty($this -> _options['sql'])) {
			$sql = $this -> _options['sql'];
			unset($this -> _options['sql']);
		} else {
			$field = '*';
			if (!empty($this -> _options['field'])) {
				if (is_array($this -> _options['field'])) {
					$field = implode(',', $this -> deal_field($this -> _options['field']));
				} else {
					$field = $this -> _options['field'];
				} 
				unset($this -> _options['field']);
			} 
			$table = $this -> parse_table();
			$where = $this -> parse_where();
			!empty($where) && $where = ' WHERE ' . $where;
			$order = '';
			if (!empty($this -> _options['order'])) {
				$order = ' ORDER BY ' . $this -> _options['order'];
				unset($this -> _options['order']);
			} 
			$limit = '';
			if (!empty($this -> _options['limit'])) {
				$limit = ' LIMIT ' . $this -> _options['limit'];
				unset($this -> _options['limit']);
			} 
			$sql = "SELECT {$field} FROM {$table}{$where}{$order}{$limit}";
		} 
		return $this -> deal_table($sql);
	} 

	protected function parse_where()
	{
		$where = '';
		if (!empty($this -> _options['where'])) {
			if (is_array($this -> _options['where'])) {
				$where = $this -> deal_where($this -> _options['where']);
			} else {
				$where = $this -> _options['where'];
			} 
			unset($this -> _options['where']);
		} 
		return $where;
	} 

  
	public function deal_where($where)
	{
		if (is_array($where)) {
			if (array_key_exists('_logic', $where)) {
				$logic = strtoupper($where['_logic']);
				unset($where['_logic']);
			} else {
				$logic = 'AND';
			} 
			foreach ($where as $key=>$term) {
				if(!is_array($term)){
					$term2 = is_string($term)?"(`" . $key. "` = '" .$term . "')":"(`" . $key. "` = " .$term . ")";
				}else{
					switch(strtoupper($term[0])) {
						case "IN":
							$in = $this->parse_in($term[1]);
							$term2 = "(`" . $key. "` IN " .$in . ")";
							break;		
						case "GT":
							$term2 = "(`" . $key. "` > " .$term[1] . ")";
							break;	
						case "LT":
							$term2 = "(`" . $key. "` < " .$term[1] . ")";
							break;	
						case "NEQ":
							$term2 = "(`" . $key. "` != " .$term[1] . ")";
							break;	
						case "LIKE":
							$term2 = "(`" . $key. "` LIKE '" .$term[1] . "')";
							break;	
						case "BETWEEN":
							$between = implode(" and ",$term[1]);
							$term2 = "(`" . $key. "` BETWEEN " .$between . ")";
							break;	
					}
				}
				
				$where2[] = $term2;
			} 

			$where = implode(' ' . $logic . ' ', $where2);
		} 
		return $where;
	} 

	protected function parse_in($in_arr){

		$in_str = "(";
		foreach($in_arr as $key=>$v){
			$in_str .= $v.",";
		}
		$in_str = rtrim($in_str,",");
		$in_str .= ")";
		return $in_str;
	
	}


	protected function parse_table()
	{
		$table = $this -> _table[1];
		if (!empty($this -> _options['table'])) {
			if (is_numeric($this -> _options['table'])) {
				$table = $this -> _table[$this -> _options['table']];
			} else {
				$table = $this -> _options['table'];
			} 
		//	unset($this -> _options['table']);
		} 
		return $table;
	} 
	protected function deal_table($sql)
	{
		return preg_replace_callback('/#(\d+)/', array(__CLASS__, 'deal_table_callback'), $sql);
	} 
	protected function deal_table_callback($tab)
	{
		return $this -> _table[$tab[1]];
	} 

	public function where($where=null)
	{
		$this -> _options['where'] = $where;
		return $this;
	} 
	public function order($order=null)
	{
		$this -> _options['order'] = $order;
		return $this;
	} 
	public function table($table=null)
	{
		$this -> _options['table'] = $table;
		return $this;
	} 
	public function field($field=null)
	{
		$this -> _options['field'] = $field;
		return $this;
	} 
	public function limit($limit=null)
	{
		$this -> _options['limit'] = $limit;
		return $this;
	} 

	public function getLastSql(){
		return $this->last_sql;
	}







}

?>