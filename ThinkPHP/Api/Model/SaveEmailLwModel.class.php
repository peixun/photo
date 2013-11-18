<?php

class SaveEmailLwModel extends LW_Model {
	function saveEmail($toEmail,$title,$content,$mid,$name='',$type=''){
		$result = false;
		
		if($toEmail){
            $map['title'] = $title;
            $map['content'] = $content;
            $map['uid'] = $mid;
            $api_name = TS_D( 'User' )->getInfo( $mid,'name' );
            $map['userName'] = !empty($name) ? $name : $api_name['name'];

            if( is_string( $toEmail ) && false !== strpos($toEmail,',') ){
                $toEmail = implode( ',',$toEmail );
            }

            false == is_array( $toEmail ) && $toEmail = array( $toEmail );
            foreach($toEmail as $k=>$v){
                $map['toemail']=$v;
                $value_map = $this->__stringToMarks( $map );
                $value[] = "(".implode( ',',$value_map ).")";
                $field = "(".implode( ',',array_keys( $value_map ) ).")";
            }

            $sql = "INSERT INTO `ts_saveemail` ".$field.
                   " VALUES ".implode( ',',$value );
            $result = $this->db->query( $sql );
        }
		return $result;
	}

    /**
     * __addAll 
     * 批量添加的sql
     * @param mixed $data 
     * @access private
     * @return void
     */
    private function __stringToMarks( $data ){
        $result = $data;
        foreach ( $result as $key=>$value ){
            if( is_string( $value ) ){
                $value = addslashes( $value );
                $result[$key] = "\"$value\"";
            }
            if( is_null( $value ) ){
                $result[$key] = 'null';
            }
        }
        return $result;
    }
}


?>
