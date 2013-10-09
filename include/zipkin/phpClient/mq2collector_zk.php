<?php

    $ini_array = parse_ini_file("config.ini");

    

    //包含thrift客户端库文件    
    $GLOBALS['THRIFT_ROOT_'] = $ini_array["includepath"].'/zipkin';   
    //$GLOBALS['THRIFT_ROOT'] = '../include/zipkin';     
    require_once $GLOBALS['THRIFT_ROOT_'].'/Thrift.php';    
    require_once $GLOBALS['THRIFT_ROOT_'].'/protocol/TBinaryProtocol.php';    
    require_once $GLOBALS['THRIFT_ROOT_'].'/transport/TSocket.php';    
    require_once $GLOBALS['THRIFT_ROOT_'].'/transport/TPhpStream.php'; 
    require_once $GLOBALS['THRIFT_ROOT_'].'/transport/THttpClient.php';    
    require_once $GLOBALS['THRIFT_ROOT_'].'/transport/TFramedTransport.php'; 
    require_once $GLOBALS['THRIFT_ROOT_'].'/transport/TMemoryBuffer.php';   
    //error_reporting(E_NONE);    
   
   //包含helloworld接口文件  
    require_once $GLOBALS['THRIFT_ROOT_'].'/packages/zipkinCollector/ZipkinCollector.php';    
    require_once $GLOBALS['THRIFT_ROOT_'].'/packages/zipkinCore/zipkinCore_types.php';  
    require_once $GLOBALS['THRIFT_ROOT_'].'/phpClient/testLogEntry.php';
    include_once $GLOBALS['THRIFT_ROOT_'].'/phpClient/mq.php'; 
    //error_reporting(E_ALL);    
    include_once 'shm.php';
    


class ZookeeperDemo extends Zookeeper {
  
  public function watcher( $i, $type, $key ) {
    echo "Insider Watcher\n";
    $shmopobj = new shared();
    //观察者如果各道消息，那么向共享内存中存信息，告诉ZKTrace不要再收集了。
    if($this->get('/zipkin_php')== "119")
    {
        echo "get----119";
        
        $shmopobj->database = "off";
    }
    else
    {
        echo "not----119";
        $shmopobj->database = "on";
    }
    // Watcher gets consumed so we need to set a new one
    try{
        $this->get( '/zipkin_php', array($this, 'watcher' ) );    
    }
    catch(Exception  $ex)
    {
        echo "get zookeeper /zipkin_php error ".$ex;
    }
    
  }
 
}
class MQsend
{

    public function smq($coll_ip,$coll_port)
    {
        try
        {   
        $span = MessageQueue::getInstance()->pop();

        //echo "-->".gettype($span);
        if(!is_null($span) && gettype($span)!= 'boolean')
        {
             //echo "pop:".$span->name."<br/>";
            //echo gettype($span);

            $socket = new TSocket($coll_ip, $coll_port);    
            $transport = new TFramedTransport($socket,  true, true);    
            $protocol = new TBinaryProtocol($transport);    
            $client = new ZipkinCollectorClient($protocol);    
              
            //$my_log = new LogDemo();

             
            
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
            echo "=send MQ=";   
        }   
        }catch(TException $tx){    
            print 'TException: '.$tx->getMessage()."/n";    
            echo  'TException: '.$tx->getMessage()."/n";    
        }  
    }

    public function smq_batch($coll_ip,$coll_port,$size)
    {
        try
        {   
            $i = 0;
            $array_span = array(); 
            while($i<$size)
            {
              $span = MessageQueue::getInstance()->pop();

              if(!is_null($span) && gettype($span)!= 'boolean')
              {
                   array_push($array_span, $span); 
              }   
              $i++;
            }

            $array_log = array();
            foreach ($array_span as $span_v) {               
             
                $buf = new TMemoryBuffer();
                $buf->open();
                $transport2 = new TFramedTransport($buf,true,FALSE);
                $protocol2 = new TBinaryProtocol($transport2, true, true);
                $span_v->write($protocol2);                     
                $lentry = new LogEntry();
                $lentry->category = "zipkin";
                $lentry->message  = base64_encode($buf->getBuffer()); 
                array_push($array_log, $lentry); 
                $buf->close(); 
                
            }
            if(count($array_log) > 0)
            {
                $socket = new TSocket($coll_ip, $coll_port);    
                $transport = new TFramedTransport($socket, true, true);    
                $protocol = new TBinaryProtocol($transport);    
                $client = new ZipkinCollectorClient($protocol);    
                $transport->open(); 
                $client->Log($array_log);
                $transport->close();   
                echo "=send MQ[".count($array_log)."]=";    
            }
            

        
        }catch(TException $tx){    
            print 'TException: '.$tx->getMessage()."/n";    
           //echo  'TException: '.$tx->getMessage()."/n";    
        }  
    }
}

$zoo = new ZookeeperDemo($ini_array["zookeeperip"].":".$ini_array["zookeeperport"]);
$zoo->get( '/zipkin_php', array($zoo, 'watcher' ) );

while( true ) {
  echo '.';
  sleep(2);

  $ms = new MQsend();
  $ms->smq_batch($ini_array["collectorip"],$ini_array["collectorport"],$ini_array["batch_size"]);
}






?>
