<?php

include_once '../include/zipkin/phpClient/Trace.php'; 
include_once '../include/zipkin/phpClient/mq.php'; 

MessageQueue::getInstance()->push("tom");
echo "f-pop:".MessageQueue::getInstance()->pop()."<br/>";



?>