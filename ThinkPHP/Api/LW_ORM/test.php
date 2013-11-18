<?php

define('THINK_PATH',1);

$pub_config    =	 require '../../../config.inc.php';



//$lw_db_configs['common']["db_host"]  =	$pub_config["DB_HOST"];
//$lw_db_configs['common']["db_name"]  =	$pub_config["DB_NAME"];
//$lw_db_configs['common']["db_user"]  =	$pub_config["DB_USER"];
//$lw_db_configs['common']["db_pass"]  =	$pub_config["DB_PWD"];
//$lw_db_configs['common']["db_type"]  =	$pub_config["DB_TYPE"];
//$lw_db_configs['common']["db_char"]  =	$pub_config["DB_CHARSET"];
//$lw_db_configs['common']["db_pefix"] =	$pub_config["DB_PREFIX"];
//
//$lw_db_configs = array(
//					"common"=>array(
//						"db_host"	=>	 "localhost",
//						"db_name"	=>	 "ts_2",
//						"db_user"	=>	 "root",
//						"db_pass"	=>	 "",
//						"db_type"	=>	 "utf8",
//						"db_char"	=>	 "mysql",
//						"db_pefix"	=>   "ts_",
//					),
//				 );



include "lw_orm.class.php";

//$orm = new lw_orm($lw_db_configs);

$orm = lw_orm::init();

$sql = "select * from ts_user";

//c
$data["name"] = "aaa";

//$xxx = $orm->table("ts_userd")->add($data);

//r
$xxx = $orm->table("ts_user")->where()->findAll();



dump($xxx);

dump($orm);









dump($lw_db_configs);














function dump($var, $echo=true,$label=null, $strict=true)
{
	echo '<div style="border:1px solid #dbdbdb; padding:5px; margin:5px; width:auto; color:#003300;background:#fff;text-align:left;">';
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
		echo '</div>';
    }
}

?>