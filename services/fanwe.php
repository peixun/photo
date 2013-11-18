<?php
include "Zend/Amf/Server.php";
include "FanweServices.php";

$server = new Zend_Amf_Server();
$server -> setClass('FanweServices');

echo $server -> handle();
?>