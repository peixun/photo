<?php
class CreditSettingLwModel extends LW_Model{
     public $table_name = "credit_setting";
    /**
     * 获得积分规则
     * @param <type> $userId 用户id
     * @param <type> $action 动作
     * @return <type>
     */
     function getCredit($userId,$action) {
        $user           =	TS_D('User');
        $setting	=	$this->where("action='$action'")->find();
        $userInfo       =       $user->where('id='.$userId)->field('id')->find();

        if(empty ($userId) || !$userInfo) return 'not_user'; //用户id错误
        if(!$setting) return 'no_have_action'; //没有这个动作
        return $setting;
    }

    public function getAllCredit(){
            return $this->findAll();
    }

    public function getCreditType(){
        $cache = ts_cache('credit_type');

        if(!$cache){
                 $cache = $this->getAllType();
                 //存储缓存
                 ts_cache('credit_type',$cache);
        }
        return $cache;
    }
    private function getAllType(){
           //得到所有数据
        $data = $this->table(C('DB_PREFIX').'credit_type')->findAll();
        //重组数据
        $cache = array();
        foreach ( $data as $value ){
            $cache[$value['name']]=$value['alias'];
        }
        return $cache;
    }
}
?>
