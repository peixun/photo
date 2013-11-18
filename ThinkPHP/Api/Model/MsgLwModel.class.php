<?php 
class MsgLwModel extends LW_Model{
        /**
         * getNewNum 
         * 获得最新的记录条数
         * @access public
         * @return void
         */
        public function getNewNum( $uid ){
            $map['is_read']  = 0;
            $map['toUserId'] = intval( $uid );
            $map['is_del']   =  0;
	    $r1 = $this->where($map)->field("count(*)")->find();
            return $r1['count(*)'];
        }
}
?>
