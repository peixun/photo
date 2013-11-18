<?php
define('CACHE_ROOT_PATH', str_replace('services/Utils/Cache.php', '', str_replace('\\', '/', __FILE__)));
// 取得对象实例 支持调用类的静态方法
function get_instance_of_s($name,$method='',$args=array())
{
    static $_instance = array();
    $identify   =   empty($args)?$name.$method:$name.$method.to_guid_string_s($args);
    if (!isset($_instance[$identify])) {
        if(class_exists($name)){
            $o = new $name();
            if(method_exists($o,$method)){
                if(!empty($args)) {
                    $_instance[$identify] = call_user_func_array(array(&$o, $method), $args);
                }else {
                    $_instance[$identify] = $o->$method();
                }
            }
            else
                $_instance[$identify] = $o;
        }
        else
            halt('类不存在:'.$name);
    }
    return $_instance[$identify];
}
// 根据PHP各种类型变量生成唯一标识号
function to_guid_string_s($mix)
{
    if(is_object($mix) && function_exists('spl_object_hash')) {
        return spl_object_hash($mix);
    }elseif(is_resource($mix)){
        $mix = get_resource_type($mix).strval($mix);
    }else{
        $mix = serialize($mix);
    }
    return md5($mix);
}
// 循环创建目录
function mk_dir_s($dir, $mode = 0755)
{
  if (is_dir($dir) || @mkdir($dir,$mode)) return true;
  if (!mk_dir_s(dirname($dir),$mode)) return false;
  return @mkdir($dir,$mode);
}
class CacheService 
{//类定义开始

    /**
     +----------------------------------------------------------
     * 是否连接
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    protected $connected  ;

    /**
     +----------------------------------------------------------
     * 操作句柄
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    protected $handler    ;

    /**
     +----------------------------------------------------------
     * 缓存存储前缀
     +----------------------------------------------------------
     * @var string
     * @access protected
     +----------------------------------------------------------
     */
    protected $prefix='~@';

    /**
     +----------------------------------------------------------
     * 缓存连接参数
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    protected $options = array();

    /**
     +----------------------------------------------------------
     * 缓存类型
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    protected $type       ;

    /**
     +----------------------------------------------------------
     * 缓存过期时间
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    protected $expire     ;

    /**
     +----------------------------------------------------------
     * 连接缓存
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $type 缓存类型
     * @param array $options  配置数组
     +----------------------------------------------------------
     * @return object
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    public function connect($type='',$options=array())
    {
        if(empty($type))  $type = "File";
        $cachePath = dirname(__FILE__).'/Cache/';
        $cacheClass = 'Cache'.ucwords(strtolower(trim($type)))."Service";
        require_once $cacheClass.'.php';
        if(class_exists($cacheClass))
            $cache = new $cacheClass($options);
        else
            throw_exception('缓存初始化失败:'.$type);
        return $cache;
    }

    public function __get($name) {
        return $this->get($name);
    }

    public function __set($name,$value) {
        return $this->set($name,$value);
    }

    public function __unset($name) {
        $this->rm($name);
    }
    public function setOptions($name,$value) {
        $this->options[$name]   =   $value;
    }

    public function getOptions($name) {
        return $this->options[$name];
    }
    /**
     +----------------------------------------------------------
     * 取得缓存类实例
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    static function getInstance()
    {
       $param = func_get_args();
        return get_instance_of_s(__CLASS__,'connect',$param);
    }

    // 读取缓存次数
    public function Q($times='') {
        static $_times = 0;
        if(empty($times))
            return $_times;
        else
            $_times++;
    }

    // 写入缓存次数
    public  function W($times='') {
        static $_times = 0;
        if(empty($times))
            return $_times;
        else
            $_times++;
    }
}//类定义结束
?>