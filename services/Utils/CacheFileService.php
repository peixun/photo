<?php
require_once 'CacheFileService.php';
class CacheFileService extends CacheService
{//类定义开始

    /**
     +----------------------------------------------------------
     * 架构函数
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    public function __construct($options='')
    {
        if(!empty($options['temp'])){
            $this->options['temp'] = $options['temp'];
        }else {
            $this->options['temp'] = CACHE_ROOT_PATH."app/Runtime/Temp/";
        }
        $this->expire = isset($options['expire'])?$options['expire']:'-1';
        if(substr($this->options['temp'], -1) != "/")    $this->options['temp'] .= "/";
        //$this->connected = is_dir($this->options['temp']) && is_writeable($this->options['temp']);
        $this->connected = is_dir($this->options['temp']); //修改 by hc 去除is_writeable判断。有时不准确导至CacheFileService connect失败 
        $this->type = strtoupper(substr(__CLASS__,6));
        $this->init();

    }

    /**
     +----------------------------------------------------------
     * 初始化检查
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    private function init()
    {
		error_reporting(0);
        $stat = stat($this->options['temp']);
        $dir_perms = $stat['mode'] & 0007777; // Get the permission bits.
        $file_perms = $dir_perms & 0000666; // Remove execute bits for files.

        // 创建项目缓存目录
        if (!is_dir($this->options['temp'])) {
            if (!  mkdir($this->options['temp']))
                return false;
             chmod($this->options['temp'], 0777);
        }
		error_reporting(1);
    }

    /**
     +----------------------------------------------------------
     * 是否连接
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    private function isConnected()
    {
        return $this->connected;
    }

    /**
     +----------------------------------------------------------
     * 取得变量的存储文件名
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @param string $name 缓存变量名
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    private function filename($name)
    {
        $name	=	md5($name);
        if(true) {
            // 使用子目录
            $dir   ='';
            for($i=0;$i<2;$i++) {
                $dir	.=	$name{$i}.'/';
            }
            if(!is_dir($this->options['temp'].$dir)) {
                mk_dir_s($this->options['temp'].$dir);
            }
            $filename	=	$dir.$this->prefix.$name.'.php';
        }else{
            $filename	=	$this->prefix.$name.'.php';
        }
        return $this->options['temp'].$filename;
    }

    /**
     +----------------------------------------------------------
     * 读取缓存
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name 缓存变量名
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function get($name)
    {
        $filename   =   $this->filename($name);
        if (!$this->isConnected() || !is_file($filename)) {
           return false;
        }
        $this->Q(1);
        $content    =   file_get_contents($filename);
        if( false !== $content) {
            $expire  =  (int)substr($content,8, 12);
            if($expire != -1 && time() > filemtime($filename) + $expire) {
                //缓存过期删除缓存文件
                unlink($filename);
                return false;
            }
            
            $content   =  substr($content,20, -3);
            $content    =   unserialize($content);
            return $content;
        }
        else {
            return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 写入缓存
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     * @param int $expire  有效时间 -1 为永久
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    public function set($name,$value,$expire='')
    {
        $this->W(1);
        if('' === $expire) {
            $expire =  $this->expire;
        }
        $filename   =   $this->filename($name);
        $data   =   serialize($value);
         $check  =  '';
        $data    = "<?php\n//".sprintf('%012d',$expire).$check.$data."\n?>";
        $result  =   file_put_contents($filename,$data);
        if($result) {
            clearstatcache();
            return true;
        }else {
            return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 删除缓存
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name 缓存变量名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    public function rm($name)
    {
        return unlink($this->filename($name));
    }

    /**
     +----------------------------------------------------------
     * 清除缓存
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name 缓存变量名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
    public function clear()
    {
        $path   =  $this->options['temp'];
        if ( $dir = opendir( $path ) )
        {
            while ( $file = readdir( $dir ) )
            {
                $check = is_dir( $file );
                if ( !$check )
                    unlink( $path . $file );
            }
            closedir( $dir );
            return true;
        }
    }

}//类定义结束
?>