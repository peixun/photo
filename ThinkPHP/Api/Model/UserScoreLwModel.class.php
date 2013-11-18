<?php
class UserScoreLwModel extends LW_Model {
    public $table_name = "user_score";
    private $credit = array();
    /**
     * 设置用户的积分
     * @param int $uid  用户id
     * @param int $score 积分操作数
     * @param type $type 积分种类
     */
    public function setScore ($uid,$credit,$type = null){
        //获取缓存的积分种类
        $dao = TS_D('CreditSetting');
        $cache_type = $dao->getCreditType();
        $array = array('action','id','type','info','actioncn');
        foreach ($credit as $key=>$value){
            if(!in_array($key,$array)){
                if(!$this->checkScore($uid,$key,$value)) continue;
                $request[$key]['number'] = $value;
                $request[$key]['uid'] = intval($uid);
                $request[$key]['cTime'] = time();
            }
        }

        foreach ($request as $key=>$map){
            $map['type'] = $key;
            $map['action'] = $credit['action'];
            $map['info'] = $this->__paramInfo($cache_type, $credit, $map);
            $result[$key] = $this->add($map);
        }

        foreach ($cache_type as $key=>$value){
                 $old_score = $this->getScore($uid,$key);
                 $this->credit[$key] = isset($old_score)?$old_score:0;
        }

        //将积分记录到用户信息表
        $dao = TS_D('UserInfo');
        $dao->addCredit($uid,$this->credit);
        $this->credit = null;
        return $result;
    }
    private function __paramInfo($cache_type,$credit,$map){
        $search = array('{action}','{score}','{typecn}','{sign}');
        $sign = $map['number']>0?"增加":"减少";
        $replace = array($credit['actioncn'],abs($map['number']),$cache_type[$map['type']],$sign);
       return  str_replace($search, $replace , $credit['info']);
    }

    public function checkScore($uid,$score_type,$value){
        $old_score = $this->getScore($uid,$score_type);

        if(0 > $value){
            if($old_score <= 0) return false;
            if($old_score - abs($value) <0 ) return false;
        }
        if(0 == $value) return 0;
         return true;
    }
    public function getScore($uid,$type = 'score'){
        $map['uid'] = $uid;
        $map['type'] = $type;
        $result = $this->where($map)->field("sum(number) as sum")->find();
        return $result['sum'];
    }
}

?>
