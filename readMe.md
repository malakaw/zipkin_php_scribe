zip kin php scribe
===================================

概述
-----------------------------------
支持zipkin version 1.1.0,zipkin下载地址：
[https://github.com/twitter/zipkin/releases](https://github.com/twitter/zipkin/releases)<br />或者[https://github.com/twitter/zipkin](https://github.com/twitter/zipkin)<br />这里的zipkin php scribe主要功能是在php页面中埋点，然后收集发送给collector;
这里的分两个功能模块，一个是埋点的，然后发送给MQ(system V IPC,可以参考[http://www.ibm.com/developerworks/cn/linux/l-ipc/](http://www.ibm.com/developerworks/cn/linux/l-ipc/)<br />)，另一个是从MQ收集信息发送给collector.


环境
-----------------------------------
php需要安装sysvsem ；
###可以参考我的php安装(apache服务)
		configure  --prefix=/usr/local/web/apache   --enable-shared=max --enable-module=rewirte   --enable-module=so  --with-pcre=/usr/local/pcre --with-apr=/usr/local/apr --with-apr-util=/usr/local/apr-util --enable-sysvmsg --with-apxs2=/usr/local/web/apache/bin/apxs

配置
-----------------------------------
###埋点
(include/zipkin/phpClient/Trace.php)
修改$GLOBALS['THRIFT_ROOT_'] ，设置include的绝对目录；就像这样$GLOBALS['THRIFT_ROOT_'] = '/usr/local/web/apache/htdocs/include';   


###收集MQ,发送给collector
(include/zipkin/phpClient/mq2collector.php)
修改$socket = new TSocket('10.101.0.91', 9410);    collector的ip和端口。

example
-----------------------------------

###埋点，可以参考文件test_zipkin/t1.php
		ZKTrace::clientSend("phpspansubeub49");
		ZKTrace::clientReceive();
		
查看MQ ，在linux shell下:ipcs<br/>
![github](https://raw.github.com/malakaw/zipkin_php_scribe/master/img/ipcs.png "ipcs")
<br/>
主要是查看Message Queues

###收集MQ,发送给collector
		/usr/local/php/bin/php /usr/local/web/apache/htdocs/include/zipkin/phpClient/mq2collector.php







