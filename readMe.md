zip kin php scribe
===================================

����
-----------------------------------
֧��zipkin version 1.1.0,zipkin���ص�ַ��
[https://github.com/twitter/zipkin/releases](https://github.com/twitter/zipkin/releases)<br />����[https://github.com/twitter/zipkin](https://github.com/twitter/zipkin)<br />�����zipkin php scribe��Ҫ��������phpҳ������㣬Ȼ���ռ����͸�collector;
����ķ���������ģ�飬һ�������ģ�Ȼ���͸�MQ(system V IPC,���Բο�[http://www.ibm.com/developerworks/cn/linux/l-ipc/](http://www.ibm.com/developerworks/cn/linux/l-ipc/)<br />)����һ���Ǵ�MQ�ռ���Ϣ���͸�collector.


����
-----------------------------------
php��Ҫ��װsysvsem ��
###���Բο��ҵ�php��װ(apache����)
		configure  --prefix=/usr/local/web/apache   --enable-shared=max --enable-module=rewirte   --enable-module=so  --with-pcre=/usr/local/pcre --with-apr=/usr/local/apr --with-apr-util=/usr/local/apr-util --enable-sysvmsg --with-apxs2=/usr/local/web/apache/bin/apxs

����
-----------------------------------
###���
(include/zipkin/phpClient/Trace.php)
�޸�$GLOBALS['THRIFT_ROOT_'] ������include�ľ���Ŀ¼����������$GLOBALS['THRIFT_ROOT_'] = '/usr/local/web/apache/htdocs/include';   


###�ռ�MQ,���͸�collector
(include/zipkin/phpClient/mq2collector.php)
�޸�$socket = new TSocket('10.101.0.91', 9410);    collector��ip�Ͷ˿ڡ�

example
-----------------------------------

###��㣬���Բο��ļ�test_zipkin/t1.php
		ZKTrace::clientSend("phpspansubeub49");
		ZKTrace::clientReceive();
		
�鿴MQ ����linux shell��:ipcs<br/>
![github](https://raw.github.com/malakaw/zipkin_php_scribe/master/img/ipcs.png "ipcs")
<br/>
��Ҫ�ǲ鿴Message Queues

###�ռ�MQ,���͸�collector
		/usr/local/php/bin/php /usr/local/web/apache/htdocs/include/zipkin/phpClient/mq2collector.php







