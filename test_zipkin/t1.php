<?php
include_once '../include/zipkin/phpClient/Trace.php'; 


ZKTrace::clientSend("phpspan49");
	ZKTrace::clientSend("phpspansub49");
		ZKTrace::clientSend("phpspansubeub49");
		ZKTrace::clientReceive();
	ZKTrace::clientReceive();

	ZKTrace::clientSend("phpspansub59");
	ZKTrace::clientReceive();
ZKTrace::clientReceive();
//for($i=1;$i<=10;$i++){
//  echo Demo::add()."<br/>";
//
//SpanBuilder::clientSend("1");
//SpanBuilder::clientReceive();

?>
