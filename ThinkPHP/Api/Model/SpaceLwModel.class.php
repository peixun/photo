<?php 
class SpaceLwModel extends LW_Model{
        public function changeCount( $appname,$count,$uid = null){
        	if(isset($uid)){
        		$mid = $uid;
        	}else{
        		$userLwDao    = TS_D("User");
				$mid          = $userLwDao->getLoggedInUser();
        	}


            $map['value'] = intval( $count );


            $where['appname'] = $appname;
            $where['uid']     = $mid;
            $where['variable'] = 'count';

            if( $find = $this->where( $where )->find() ){
                $result = $this->where( $where )->save($map);
            }else{
                $map['appname']  = $appname;
                $map['variable'] = 'count';
                $map['uid']      = $mid;
                $map['name']     = $uid;
                $map['value']    = $count;
                $result = $this->add( $map );
            }
            return $result;
        }


        public function getCount( $uid,$appname = null){
            if( false == isset( $appname ) ){
                $where['uid']     = $uid;
                $where['variable']    = 'count';
                
                $result = $this->where( $where )->findAll();
                foreach ( $result as $value ){
                    $data[$value['appname']] = $value['value'];
                }
            }else{
                $where['appname'] = $appname;
                $where['uid']     = $uid;
                $where['variable']    = 'count';

                $data = $this->where( $where)->find();
            }
            return $data;
        }
}
?>
