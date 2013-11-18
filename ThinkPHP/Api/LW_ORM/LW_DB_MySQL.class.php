<?php
class LW_DB_MySQL extends LW_DB {
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
			$db_char = isset($this->dsn["master"]) ? $this->dsn["master"]["db_pass"] : $this->dsn["db_char"];

            $this->u_conn = mysql_connect($db_host, $db_user, $db_pass);
			mysql_query("SET NAMES " . $db_char . " ;");

			if (!$this->u_conn) {
                throw new LW_DB_Exception('更新数据库连接失败');
            }
            if (!mysql_select_db($db_name)) {
                throw new LW_DB_Exception('更新数据库选择失败');
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
                    throw new LW_DB_Exception('查询数据库连接失败');
                }
            } else {
                if (!mysql_select_db($this->q_conn, $db_name)) {
                    throw new LW_DB_Exception('查询数据库选择失败');
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
			ts_dump($sql);
            throw new LW_DB_Exception('查询失败:' . mysql_error($this->q_conn));
        } else {
            $this->query_num++;
            return $this->qrs;
        }
    }

    /**
     * 执行一个SQL查询
     *
     * 本函数用于执行INSERT UPDATE类型的SQL语句
     *
     * @param string $sql SQL查询语句
     * @return boolean 返回查询结果资源句柄
     */
    public function execute($sql) {

        $this->sqls[] = $sql;
        $this->q_sqls[] = $sql;
        $this->sql = $sql;

        if (!$this->q_conn) {
            $this->connect("slave");
        }
        $this->qrs = mysql_query($sql);

        if (!$this->qrs) {
			return false;
        } else {
            return true;
        }
    }

//DIY START

	public function insert($sql) {

		$result	=	$this -> execute($sql);
		if(!$result){
			return false;
		}else{
			//return mysql_insert_id($this->u_conn);
			return mysql_insert_id();
		}
	}

	public function update($sql){

		$result	=	$this -> execute($sql);
		if(!$result){
			return false;
		}else{
			return mysql_affected_rows($this->q_conn);
		}
	}

	public function del($sql){
		$result	=	$this -> execute($sql);
		if(!$result){
			return false;
		}else{
			return mysql_affected_rows($this->q_conn);
		}
	}

	public function delAll($sql){
		$result	=	$this -> execute($sql);
		if(!$result){
			return false;
		}else{
			return mysql_affected_rows($this->q_conn);
		}
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
}

?>
