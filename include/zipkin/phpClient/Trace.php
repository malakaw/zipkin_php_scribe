<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author Administrator
 */

$GLOBALS['THRIFT_ROOT_'] = '/usr/local/web/apache/htdocs/include';   
include_once 'util.php';
include_once 'mq.php';
include_once $GLOBALS['THRIFT_ROOT_'].'/zipkin/Thrift.php';    
require_once $GLOBALS['THRIFT_ROOT_'].'/zipkin/packages/zipkinCore/zipkinCore_constants.php';   
require_once $GLOBALS['THRIFT_ROOT_'].'/zipkin/transport/TMemoryBuffer.php';  


class ZKTrace
{
    static private  $SPAN_BUILDER = NULL;
    
    static private  $instance  = NULL;

    
    //构建span,肯能是topspan,也可能不是
    public static function clientSend($span_name)
    {
        SpanBuilder::clientSend($span_name);
    }
    
    public static function clientReceive()
    {
        SpanBuilder::clientReceive();
    }
}

class SpanBuilder{

    private static $STACK  = NULL ;

    

    //$GLOBALS['STACK']
    private static function init()
    {
        //echo "--init--";
        self::$STACK = new SplStack();
        
    }
    public static function clientSend($span_name)
    {
        if (is_null(self::$STACK)) {
            self::init();
        }
        $span = new Span();
        $span->name     = $span_name;
        $span->trace_id = SpanContext::getCurrentTraceId();     
        $span->id       = SpanContext::getCurrentSpanId();
        $span->parent_id= SpanContext::getParentSpanId();
        
        $span->annotations = array(AnnotationBuilder::clientSendAnnotation($span_name),AnnotationBuilder::serverReceAnnotation($span_name)); 
        self::$STACK->push($span);
    }
    public static function clientReceive()
    {
        if (is_null(self::$STACK)) {
            self::init();
            throw new Exception("STACK  is null ,when call clientReceive function before  The clientSend has  be called");
        }
        if(self::$STACK->count() == 0)
        {
            throw new Exception("STACK  is empty ,when call clientReceive function before  The clientSend has  be called");
        }

        $span =  self::$STACK->pop();
        SpanContext::clearSpanID();
        //echo gettype($span->annotations);
        $span->annotations =  array_merge($span->annotations,array(AnnotationBuilder::serverSendAnnotation($span->name),AnnotationBuilder::clientReceAnnotation($span->name))); 
        //$span->annotations->push(AnnotationBuilder::serverSendAnnotation($span->name)); 
        //$span->annotations->push(AnnotationBuilder::clientReceAnnotation($span->name)); 

        MessageQueue::getInstance()->push($span);
        //echo "over-";
    

    }

}

class SpanContext
{
    private static $ID_STACK      = NULL;
    //private static $IS_TOP_SPAN   = TRUE;
     
    public static function getCurrentTraceId()
    {
        if(is_null($GLOBALS['TRACEID']))
        {
            $GLOBALS['TRACEID'] = Util::getRandInt();
        }
        return  $GLOBALS['TRACEID'];   
    }
    
    public static function getCurrentSpanId()
    {
        if(is_null(self::$ID_STACK))
        {
            self::$ID_STACK = new SplStack(); 
            self::$ID_STACK->push($GLOBALS['TRACEID']);
            echo "<br/>getCurrentSpanId1 - >".count($ID_STACK);
            return $GLOBALS['TRACEID'];
        }
        else
        {
           $id = Util::getRandInt();
           self::$ID_STACK->push($id); 
           echo "<br/>getCurrentSpanId2 - >".count($ID_STACK);
           return $id;
        }
    }
    
    public static function getParentSpanId()
    {
        if(self::isTopSpan())
        {
            return NULL;
        }
        else
        {
            $cid = self::$ID_STACK->pop(); 
            $pid = self::$ID_STACK->pop(); 
            self::$ID_STACK->push($pid);
            self::$ID_STACK->push($cid);
            return $pid; 
        }
        
    }
    
    public static function isTopSpan()
    {
        if(is_null($GLOBALS['TRACEID']) || is_null(self::$ID_STACK))
        {
            echo "<br/>orrrr";
            return TRUE;
        }
        else
        {
            if(count(self::$ID_STACK)>1)
            {
                echo "<br/>stack >1";
                return FALSE;
            }
            else
            {
                echo "<br/> !stack >1".count($ID_STACK);
                return TRUE;
            }
        }
    }

    public static function clearSpanID()
    {
        self::$ID_STACK->pop(); 
    }
    
}



class AnnotationBuilder
{
    
    public static function clientSendAnnotation($spanName)
    {
        return self::makeAnnotation($GLOBALS['zipkinCore_CONSTANTS']['CLIENT_SEND'], $spanName);
    }
    public static function  serverReceAnnotation($spanName)
    {
        return self::makeAnnotation($GLOBALS['zipkinCore_CONSTANTS']['SERVER_RECV'], $spanName);
    }
    public static function  serverSendAnnotation($spanName)
    {
        return self::makeAnnotation($GLOBALS['zipkinCore_CONSTANTS']['SERVER_SEND'], $spanName);
    }
    public static function  clientReceAnnotation($spanName)
    {
        return self::makeAnnotation($GLOBALS['zipkinCore_CONSTANTS']['CLIENT_RECV'], $spanName);
    }
    
    
    
    
    private static function makeAnnotation($type,$spanName)
    {
        $ann  = new Annotation ();
        $ann->timestamp = ( microtime(true)*1000000000);
        $ann->host      = EndpointBuilder::newDefaultEndpoint($spanName);
        $ann->value     = $type;
        return $ann;
    }
    
    
    
    
    
    
    
    
}

class EndpointBuilder {
    
    public static function newDefaultEndpoint($spanName) 
        {
                $epo = new Endpoint();
                $epo->ipv4 = $long = ip2long($_SERVER["REMOTE_ADDR"]);;       
                $epo->port = 9091;
                $epo->service_name = $spanName;
                
        return $epo;
    }
    
    public static function newEndpoint($spanName,$port)
        {
        $epo = new Endpoint();
                $epo->ipv4 = $long = ip2long($_SERVER["REMOTE_ADDR"]);;       
                $epo->port = $port;
                $epo->service_name = $spanName;
                
        return $epo;
    }
}

?>
