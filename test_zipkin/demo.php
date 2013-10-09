<?php
include_once '../include/zipkin/phpClient/Trace.php'; 


ZKTrace::getInstance()->clientSend("rp_php_phpspan49");
//	ZKTrace::clientSend("phpspansub49");
//		ZKTrace::clientSend("phpspansubeub49");
//		ZKTrace::clientReceive();
//	ZKTrace::clientReceive();

	ZKTrace::getInstance()->clientSend("rp_php_phpspan59");
	ZKTrace::getInstance()->clientReceive();
ZKTrace::getInstance()->clientReceive();
//for($i=1;$i<=10;$i++){
//  echo Demo::add()."<br/>";
//
//SpanBuilder::clientSend("1");
//SpanBuilder::clientReceive();




?>
