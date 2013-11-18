<?php
class MemberLwModel extends LW_Model{
        /**
         * getNewNum
         * 获得最新的记录条数
         * @access public
         * @return void
         */
        public function getMember( $uid ){

            $map['id'] = intval( $uid );

	    $r1 = $this->where($map)->field("username")->find();
        echo $this->getlastsql();
            return $r1['username'];
        }
}
?>
