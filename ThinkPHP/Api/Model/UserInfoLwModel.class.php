<?php
class UserInfoLwModel extends LW_Model{
	    public $table_name = "member";
        private $uid = 0 ;
        private $mid = 0 ;

        public function addCredit($uid,$credit){
			$map['credit'] = serialize($credit);
			if(!$this->where('uid='.$uid)->find()){
				$map['uid'] = $uid;
				return $this->add($map);
			}else{
				return $this->where('uid='.$uid)->save($map);
			}
        }
        public function getCredit($uid){
                $request = $this->where('uid='.$uid)->field('credit')->find();
                $result = unserialize(stripslashes($request['credit']));
                return $result;
        }


        /**
         * get
         * 获得指定类型的个人资料
         * @param mixed $type
         * @access public
         * @return void
         */
        public function get( $uid,$type ){
            //初始化变量
            $result = array();
            $dao    = "";

            //初始化USER表对象并得到当前登录者id
            $userLwDao	 =	TS_D("User");
            $this->mid   = $userLwDao->getLoggedInUser();
            $this->uid   = $uid;

            //构建统一的查询条件
            $map['uid'] = $this->uid;

            //如果参数type的值为字符串，并且没有包含逗号.则只是对一种个人资料进行处理
            if( is_string( $type ) && false === strpos( $type,',' ) ){
                //查询得到结果
                $request         = $this->where( $map )->field( $type )->find();
                //反序列化所得数据集并赋值给变量
                $data = unserialize( $request[$type] );

                //将上述变量中的结构进行重组得到正常的返回数组
                $result = $this->__reformData( $data,$type );

            //如果参数type的值为数组。或者为包含有逗号分割的字符串。则是几组个人资料
            }elseif( is_array( $type ) || false !== strpos( $type,',' ) ){

                //构建查询条件:如果是数组，则以逗号分隔转换数组成字符串
                $type = is_array( $type )?implode( ',',$type ):$type;
                $map['type'] = array( 'in',$type );

                //查询得到结果
                $request  = $this->where( $map )->find();

                //循环数据集开始
                foreach( $type as $value ){
                    //反序列化所得到的数据集中的数组成员并赋值给变量
                    $data = unserialize( $data[$value] );
                    //将上述变量中的结构进行重组得到正常的返回数组
                    $result[] = $this->__reformData( $data,$value );
                }
            }
            //返回结果
            return $result;
        }

        /**
         * __reformData
         * 重组数据
         * @param mixed $data
         * @access private
         * @return void
         */
        private function __reformData( $data,$type ){
            //TODO 换成后台配置的信息
            $item["address"]       = "地址";
            $item["postcode"]      = "邮编";
            $item["phone"]         = "电话";
            $item["cellphone"]     = "手机";
            $item["qq"]            = "QQ";
            $item["msn"]           = "MSN";
            $item["birthday"]      = "生日";
            $item["jiejiao"]       = "我想结交";
            $item["interest"]      = "兴趣爱好";
            $item["book"]          = "喜欢的书";
            $item["film"]          = "喜欢的电影";
            $item["idol"]          = "偶像";
            $item["motto"]         = "座右铭";
            $item["wish"]          = "最近心愿";
            $item["summary"]       = "我的简介";
            $item["education"]     = "教育信息";
            $item["career"]        = "工作信息";
            $item["ts_areaval"]    = "居住地区";
            $item["ts_hometown"]   = "家乡";
            $item["sex"]           = "性别";
            $item["bloodtype"]     = "血型";
            $item["birthday_stro"] = "星座";

            $temp_field  = array( 'intro','contact');
            $temp_field2 = array('education','career' );

            $temp_result = array_filter( $data,array( $this,'filterPrivacy' ) );

            switch( $type ){
                case 'intro':
                        //个人情况中的更多
                        if( isset( $temp_result['more']) ){
                            $temp = $temp_result['more'];
                            unset( $temp_result['more'] );
                            $more = unserialize( $temp[2] );
                            $temp_more = array_filter( $more,array( $this,'filterPrivacy' ) );
                            foreach ( $temp_more as $key=>$v ){
                                $result[$v['name']] = $v['value'];
                            }
                        }

                        //重组数据
                        foreach ( $temp_result as $key=>$v){
                            $result[$item[$key]] = $v[2];
                        }
                    break;
                case 'contact':
                        //重组数据
                        foreach ( $temp_result as $key=>$v){
                            $result[$item[$key]] = $v[2];
                        }
                    break;
                case 'education':
                    foreach ( $temp_result as $key=>$v ){
                        $result[$item[$value]][] = sprintf( '%s %s %s年入学',$v['school'],$v['class'],$v['year'] );
                    }
                    break;
                case 'career':
                    foreach ( $temp_result as $key=>$v ){
                        $result[$item[$value]][] = sprintf( '%s %s %s-%s',$v['company'],$v['position'],date( 'Y年m月',$v['begin'] ),date( 'Y年m月',0==$v['end']?time():$v['end'] ) );
                    }
                    break;
                case 'info':
                    $areaval  = $this->__paramAddress( $temp_result['ts_areaval'][2] );
                    $hometown = $this->__paramAddress( $temp_result['ts_hometown'][2] );
                    isset( $areaval ) && $temp_result['ts_areaval'][2] = $areaval;
                    isset( $hometown ) && $temp_result['ts_hometown'][2] = $hometown;

                    foreach( $temp_result as $key=>$v ){
                        $result[$item[$key]] = $v[2];
                    }
                    break;
            }
        return $result;
    }


    private function filterPrivacy($value){
        //基本信息过滤
        if( isset( $value['privacy'] ) ){
            $privacy = $value['privacy'];
            $home    = isset( $value['display'] )?$value['display']:$value['home'];
        }else{
            $privacy = $value[0];
            $home    = $value[1];
        }
        if( $this->uid != $this->mid && (1 == $privacy && false == TS_D( 'Friend' )->areFriends( $this->uid,$this->mid )) ) return false;
        if( 1 == $this->home && 1 != $home )  return false;
        if( $this->uid != $this->mid && $privacy == 2 ) return false;

        return true;
    }


    private function __paramAddress( $param ){
        //$result = $input =  $param;
        //if( isset( $input ) ){
            //list( $province,$city ) = explode( ',',$input );
            //isset( $input ) && $result = getAreaInfo( $province)." ".getAreaInfo($city);
        //}
        //return $result;
    }
}
?>
