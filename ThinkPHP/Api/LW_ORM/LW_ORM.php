<?php

/*-------------------------------------
= 定义ORM类库和Model目录
-------------------------------------*/

define('LW_ORM_DIR',THINK_PATH.'/Api/LW_ORM');
define('MODEL_DIR',THINK_PATH.'/Api/Model');

require(LW_ORM_DIR."/LW_Model.class.php");



/*-------------------------------------
= 公共函数
-------------------------------------*/
function EY_D($model_name=null){


	$pub_config    =	 require THINK_PATH.'/../config.inc.php';


	$lw_db_configs['common']["db_host"]  =	$pub_config["DB_HOST"];
	$lw_db_configs['common']["db_name"]  =	$pub_config["DB_NAME"];
	$lw_db_configs['common']["db_user"]  =	$pub_config["DB_USER"];
	$lw_db_configs['common']["db_pass"]  =	$pub_config["DB_PWD"];
	$lw_db_configs['common']["db_type"]  =	$pub_config["DB_TYPE"];
	$lw_db_configs['common']["db_char"]  =	$pub_config["DB_CHARSET"];
	$lw_db_configs['common']["db_pefix"] =	$pub_config["DB_PREFIX"];

	$model_file = MODEL_DIR."/".$model_name."LwModel.class.php";
	$modle_class = $model_name."LwModel";

	if($model_name){
		if (!class_exists($modle_class)) {
			require($model_file);
		}
		return new $modle_class($lw_db_configs);
	}else{
		return new LW_Model($lw_db_configs);
	}

}

function ey_dump($var, $echo=true,$label=null, $strict=true)
{
    echo '<div style="border:1px solid #dbdbdb; padding:5px; margin:5px; width:auto; color:#003300">';
    $label = ($label===null) ? '' : rtrim($label) . ' ';
    if(!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = "<pre>".$label.htmlspecialchars($output,ENT_QUOTES)."</pre>";
        } else {
            $output = $label . " : " . print_r($var, true);
        }
    }else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if(!extension_loaded('xdebug')) {
            $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
            $output = '<pre>'
                    . $label
                    . htmlspecialchars($output, ENT_QUOTES)
                    . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
		echo '</div>';
        return null;
    }else {
        return $output;
    }
}
?>