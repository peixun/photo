<?php
class UserAppLwModel extends LW_Model{
    public $table_name = "user_app";
    
    function getUserAppId($uid){
    	$result = array();
    	$user_app = $this->where("uid=".$uid)->field("appid")->findAll();
    	if($user_app){
	    	foreach($user_app as $key=>$v) {
	            $result[] = $v["appid"];
	        }
    	}
    	return $result;
    }
}

?>
