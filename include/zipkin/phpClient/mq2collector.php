<?php



try{   



    //包含thrift客户端库文件    
    $GLOBALS['THRIFT_ROOT_'] = '/usr/local/web/apache/htdocs/include/zipkin';   
    //$GLOBALS['THRIFT_ROOT'] = '../include/zipkin';     
    require_once $GLOBALS['THRIFT_ROOT_'].'/Thrift.php';    
    require_once $GLOBALS['THRIFT_ROOT_'].'/protocol/TBinaryProtocol.php';    
    require_once $GLOBALS['THRIFT_ROOT_'].'/transport/TSocket.php';    
    require_once $GLOBALS['THRIFT_ROOT_'].'/transport/TPhpStream.php'; 
    require_once $GLOBALS['THRIFT_ROOT_'].'/transport/THttpClient.php';    
    require_once $GLOBALS['THRIFT_ROOT_'].'/transport/TFramedTransport.php'; 
    require_once $GLOBALS['THRIFT_ROOT_'].'/transport/TMemoryBuffer.php';   
    error_reporting(E_NONE);    
   
   //包含helloworld接口文件  
    require_once $GLOBALS['THRIFT_ROOT_'].'/packages/zipkinCollector/ZipkinCollector.php';    
    require_once $GLOBALS['THRIFT_ROOT_'].'/packages/zipkinCore/zipkinCore_types.php';  
    require_once $GLOBALS['THRIFT_ROOT_'].'/phpClient/testLogEntry.php';
    include_once $GLOBALS['THRIFT_ROOT_'].'/phpClient/mq.php'; 
    error_reporting(E_ALL);    
     
    $span = MessageQueue::getInstance()->pop();

    //echo "-->".gettype($span);
    if(!is_null($span) && gettype($span)!= 'boolean')
    {
         //echo "pop:".$span->name."<br/>";
    	echo gettype($span);

	    $socket = new TSocket('10.101.0.91', 9410);    
	    $transport = new TFramedTransport($socket, 6024, 6024);    
	    $protocol = new TBinaryProtocol($transport);    
	    $client = new ZipkinCollectorClient($protocol);    
	      
	    $my_log = new LogDemo();

	     
	    echo "-----------------------------";
	     	$transport->open();   
	      	$buf = new TMemoryBuffer();
	        $buf->open();
	        $transport2 = new TFramedTransport($buf,true,FALSE);
	        $protocol2 = new TBinaryProtocol($transport2, true, true);

	        $span->write($protocol2);  
	           
	        $lentry = new LogEntry();
	        $lentry->category = "zipkin";
	        $lentry->message  = base64_encode($buf->getBuffer()); 

	        $lognetry_array = array($lentry);
	        $buf->close(); 


	     $client->Log($lognetry_array);
	     $transport->close();   
	    echo "========================";   
    }
    
	
     
     
    }catch(TException $tx){    
        print 'TException: '.$tx->getMessage()."/n";    
        echo  'TException: '.$tx->getMessage()."/n";    
    }  

?>
