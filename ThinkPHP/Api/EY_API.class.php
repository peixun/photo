<?php

require(THINK_PATH."/Api/LW_ORM/LW_ORM.php");

class EY_API {

	public static $dao;

	public function __call($fun,$args){
       // echo 'ok';
		$fun = explode("_",$fun);
		$model  = ucfirst($fun[0]);
		$method = $fun[1];
		if(EY_API::$dao->model_name != $model )	EY_API::$dao    = EY_D($model);
        //dump(call_user_func_array(array(TS_API::$dao, $method),$args));
		return  (call_user_func_array(array(EY_API::$dao, $method),$args));
	}
}

?>