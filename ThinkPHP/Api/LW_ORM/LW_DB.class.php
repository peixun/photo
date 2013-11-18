<?php
abstract class LW_DB {
    const DB_FETCH_ASSOC    = 1;
    const DB_FETCH_ARRAY    = 3;
    const DB_FETCH_ROW      = 2;
    const DB_FETCH_DEFAULT  = self::DB_FETCH_ASSOC;

    public static $db;
    protected static $db_type = array('mysql' => 'MySQL', 'oracle' => 'Oracle');

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

    protected function __construct() {
    }

    /**
     * DB初始化
     *
     * @param array $dsn 配置文件中的DB信息
     * @param string $db_key 配置中的数据库KEY名
     * @param const $fetch_mode 返回数据的KEY类型
     * @return array|DB DB对象
     */
    public static function &init(& $dsn, $db_key, $fetch_mode = self::DB_FETCH_ASSOC) {
        $key = explode('.', $db_key);
        $key = "['" . implode("']['" , $key) . "']";

        eval('$flag = isset(self::$db' . $key . ');');
        eval("\$db_info = \$dsn" . $key . ";");

        if (!$flag) {
            $class_name = 'LW_DB_' . self::$db_type[strtolower($db_info['db_type'])];
            $obj = new $class_name($db_info, $db_key, $fetch_mode);
            eval('self::$db' . $key . ' =& $obj;');
            unset($obj);
        }
        return self::$db;
    }


    public abstract function connect($type = "slave");
    public abstract function close();
    public abstract function query($sql);
  //  public abstract function update($sql);
//    public abstract function getOne($sql);
//	public abstract function getAll($sql);
    public abstract function getCol($sql, $limit = null);
    public abstract function getRow($sql, $fetch_mode = self::DB_FETCH_DEFAULT);
   // public abstract function getAll($sql, $limit = null, $fetch_mode = self::DB_FETCH_DEFAULT);
}


?>