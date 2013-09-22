<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

   

class LogDemo
{
    
    
    function makeLog()
    {
            $buf = new TMemoryBuffer();
            $buf->open();
            $transport2 = new TFramedTransport($buf,true,FALSE);
            $protocol2 = new TBinaryProtocol($transport2, true, true);
            $span = $this->makeSpan();
            $span->write($protocol2);  
           
            $lentry = new LogEntry();
            $lentry->category = "zipkin";
            $lentry->message  = base64_encode($buf->getBuffer()); 

            $lognetry_array = array($lentry);
            $buf->close();
            return $lognetry_array;
    }
    
    function makesubLog()
    {
            $buf = new TMemoryBuffer();
            $buf->open();
            $transport2 = new TFramedTransport($buf,true,FALSE);
            $protocol2 = new TBinaryProtocol($transport2, true, true);
            $span = $this->makesubSpan();
            $span->write($protocol2);  
           
            $lentry = new LogEntry();
            $lentry->category = "zipkin";
            $lentry->message  = base64_encode($buf->getBuffer()); 

            $lognetry_array = array($lentry);
            $buf->close();
            return $lognetry_array;
    }
    
    function makeSpan()
    {
        $span = new Span();
        $span->name     = "spannew7";
        $span->trace_id = 12234;
        $span->id       = 12234;
         sleep(1);
        $an1 = $this->makeAnnotation("cs");
         sleep(3);
        $an2 = $this->makeAnnotation("sr");
         sleep(1);
        $an3 = $this->makeAnnotation("ss");
         sleep(2);
        $an4 = $this->makeAnnotation("cr");
        
        $annn = array($an1,$an2,$an3,$an4);
        $span->annotations = $annn;
        $bannn =  array($this->makeBAnn("200"),$this->makeBAnn("400"));
        $span->binary_annotations =$bannn;
        //$span->parent_id = 0;
        return $span;
        
    }
    
    function makesubSpan()
    {
        $span = new Span();
        $span->name     = "subspannew7";
        $span->trace_id = 12234;
        $span->id       = 1224;
        
        $an1 = $this->makeAnnotation("cs");
         sleep(2);
        $an2 = $this->makeAnnotation("sr");
         sleep(1);
        $an3 = $this->makeAnnotation("ss");
         sleep(1);
        $an4 = $this->makeAnnotation("cr");
        $annn = array($an1,$an2,$an3,$an4);
        
        //$annn = array($this->makeAnnotation("cs"),$this->makeAnnotation("sr"),$this->makeAnnotation("ss"),$this->makeAnnotation("cr"));
        $span->annotations = $annn;
        $bannn =  array($this->makeBAnn("200"));
        $span->binary_annotations =$bannn;
        $span->parent_id = 12234;
        
        return $span;
        
    }
    
    
    
    function makeAnnotation($sc_type)
    {
        
        $ann  = new Annotation ();
        //list($s1, $s2) = explode(' ', microtime());
        //echo intval($s2)*1000000;
        $time = microtime(true);
        $ann->timestamp = ($time*1000000000);
        $ann->host      = $this->makeEndPoint();
        $ann->value     = $sc_type;
        return $ann;
    }   
    
    function makeEndPoint()
    {
            $epo = new Endpoint();
            $epo->ipv4 = 174325967;
                         
            $epo->port = 9091;
            $epo->service_name = "nginx1";
            return $epo;
             
    }
    
    function   makeBAnn($valu)
    {
            $bann = new BinaryAnnotation();
            $bann->key = "phpss";
            $bann->value = serialize($valu);
            
            //$ant = new AnnotationType();
            $bann->annotation_type = $GLOBALS['E_AnnotationType']['STRING'];
            $bann->host = $this->makeEndPoint();
            return $bann;
            
    }
    
    function show()
    {
        try
        {
            $ann  = new Annotation ();
            $ann->timestamp = 987;
            $ann->value     = "login";
            $epo = new Endpoint();
            $epo->ipv4 = 324234234;
            $epo->port = 9090;
            $epo->service_name = "test service";

            $ann->host      =$epo;
            $bann = new BinaryAnnotation();
            $bann->key = "https";
            $bann->value = "200";
            //$ant = new AnnotationType();
            $bann->annotation_type = $GLOBALS['E_AnnotationType']['BOOL'];

            $my_array = array($ann);
            $bann_array = array($bann);

            $span = new Span();
            $span->trace_id = 111;
            $span->name     = "cs";
            $span->id       = 999;

            $span->annotations = $my_array;
            //$span->binary_annotations = $bann_array;




          //  $buf = new TPhpStream(TPhpStream::MODE_R | TPhpStream::MODE_W);
            $buf = new TMemoryBuffer();
            $buf->open();
            $transport2 = new TFramedTransport($buf,true,FALSE);
            $protocol2 = new TBinaryProtocol($transport2, true, true);
            $span->write($protocol2);  
            echo  "length:".$buf->available()."<br/>";
           
            $lentry = new LogEntry();
            $lentry->category = "zipkin";
            $lentry->message  = base64_encode($buf->getBuffer()); 
            //$buf->write($buf);
            
          
            //$client->send_Log("phpmessage");
            $lognetry_array = array($lentry);
            $buf->close();
            return $lognetry_array;
         }
         catch(TException $tx)
        {    
            print 'TException: '.$tx->getMessage()."/n";    
            echo  'TException: '.$tx->getMessage()."/n";    
        }  
        
    }
}
?>
