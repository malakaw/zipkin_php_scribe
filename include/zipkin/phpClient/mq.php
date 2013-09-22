<?php

class MessageQueue {
    
    static private  $MESSAGE_QUENEN = NULL;
    static private  $instance       = NULL;

    private function  __construct() 
    {
        //echo "--init--";
        self::$MESSAGE_QUENEN = msg_get_queue(ftok(__FILE__, 'a'), 0666);
    }

    public static function getInstance ()
    {
        if (is_null(self::$instance)) {
            self::$instance = new MessageQueue();
        }
        return self::$instance;
    }
   

    public  function removeMQ()
    {
        msg_remove_queue(self::$MESSAGE_QUENEN);
    }


    public  function  push($mess)
    {
        msg_send(self::$MESSAGE_QUENEN, 1, $mess);
    }

    public  function pop()
    {
        msg_receive(self::$MESSAGE_QUENEN, 0, $message_type, 2024, $message, true, MSG_IPC_NOWAIT);
        return $message;
    }

  
}

?>
