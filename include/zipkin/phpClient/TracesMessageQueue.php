<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author Administrator
 */
class TracesMessageQueue {
    
    function  test()
    {
        $message_queue_key = ftok(__FILE__, 'a');
 
        $message_queue = msg_get_queue($message_queue_key, 0666);
        var_dump($message_queue);

        $message_queue_status = msg_stat_queue($message_queue);
        print_r($message_queue_status);


        msg_send($message_queue, 1, "Hello,World!");

        $message_queue_status = msg_stat_queue($message_queue);
        print_r($message_queue_status);

  
        msg_receive($message_queue, 0, $message_type, 1024, $message, true, MSG_IPC_NOWAIT);
        print_r($message."\r\n");

        msg_remove_queue($message_queue);
    }
}

?>
