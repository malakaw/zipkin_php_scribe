#!/bin/bash
PIDS=`ps -ef |grep mq2collector_zk |grep -v grep | awk '{print $2}'`
if [ "$PIDS" != "" ]; then
echo "myprocess is runing!"
else
/opt/httpd/htdocs/include/zipkin/phpClient/mq2coll.sh
fi
