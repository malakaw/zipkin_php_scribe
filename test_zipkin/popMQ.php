<?php

include_once '../include/zipkin/phpClient/Trace.php'; 
include_once '../include/zipkin/phpClient/mq.php'; 
//MessageQueue::getInstance()->removeMQ();
$span = MessageQueue::getInstance()->pop();
echo "f-pop:".$span->name."<br/>";

//ZKTrace::clientSend("testphpclient1");
//ZKTrace::clientReceive();


?>