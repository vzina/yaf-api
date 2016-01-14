#Yaf-api
###功能介绍
- yaf框架为基础
- 支持简单模型操作
- 支持多数据库操作和主从操作
- 支持缓存操作
- 支持yar并行rpc
- 支持纯php的定时任务，精确的秒级

###yaf安装：
```shell
sudo yum -y install gcc gcc-c++ make automake autoconf

wget http://pecl.php.net/get/yaf-2.2.9.tgz && tar zxvf yaf-2.2.9.tgz && cd yaf-2.2.9

/path/to/php/bin/phpize 

./configure --with-php-config=/path/to/php/bin/php-config

make && make install
```

###yaf配置php.ini
```
vi /usr/local/php/etc/php.ini

extension=yaf.so //关键步骤:载入yaf.so 

[yaf]
yaf.environ = product
yaf.library = NULL
yaf.cache_config = 0
yaf.name_suffix = 1
yaf.name_separator = ""
yaf.forward_limit = 5
yaf.use_namespace = 1
yaf.use_spl_autoload = 0

```

###nginx配置：
```
    server {
        listen       80;
        server_name  yaf.central.com;
        index index.html index.htm index.php;
        root  /home/wwwroot/Laboratory/all/yaf-api/public;
    
	location / {
		if (!-e $request_filename) {
        	    rewrite ^(.*)$ /index.php?$1 last;
        	}
	}	

        location ~ .*\.php?$
        {
                include fastcgi_params;
                fastcgi_pass  127.0.0.1:9000;
                fastcgi_index index.php;
                fastcgi_connect_timeout 60;
                fastcgi_send_timeout 180;
                fastcgi_read_timeout 180;
                fastcgi_buffer_size 128k;
                fastcgi_buffers 4 256k;
                fastcgi_busy_buffers_size 256k;
                fastcgi_temp_file_write_size 256k;
                fastcgi_intercept_errors on;
                fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        }
	access_log  /home/wwwlogs/yaf_access.log  main;
}
```
###实例

####定时任务
##### 基本用法

`croon.list`

```
* * * * * * ls -l >> /tmp/ls.log

# 兼容系统crontab
* * * * * pwd >> /tmp/pwd.log
```

执行

```
./bin/croon.php -p config/croon.list -l croon.log
```

`croon.log`

```
[2013-04-20 14:07:01] 27a6c9 -  debug   - Croon...!!!
[2013-04-20 14:07:01] 27a6c9 -   info   - Execute (ls >> /tmp/ls.log)
[2013-04-20 14:07:01] 27a6c9 -   info   - Finish (ls >> /tmp/ls.log)[0]
```

##### 以mysql数据库为计划任务源
* 修改配置文件的adapter参数为mysql
* 表结构为
```

+---------------------+--------------------------------------------------------+
| time                | command                                                |
+---------------------+--------------------------------------------------------+
| [秒] 分 时 日 月 周   | command                                                |
+---------------------+--------------------------------------------------------+

```
执行

```
./bin/croon.php -l croon.log
```


##### 高级用法

`bootstrap.php`

```php
<?php

// 绑定启动事件
$croon->on('run', function() use($croon) {
    // 注入db
    $croon->db = new \PDO('mysql://localhost:3306;dbname=reports');
});

// 绑定执行事件
$croon->on('executed', function ($command, $output) use ($croon) {
    // 记录执行结果
    $croon->db->exec(sprintf(
        'INSERT INTO cron(command, status, stdout, stderr, create_time) VALUES ("%s", "%s", "%s", "%s", "%s")',
        $command, $output[0], $output[1], $output[2], date('Y-m-d H:i:s'))
    );
});
```

执行

```
./bin/croon.php -p config/croon.list -l croon.log -b bootstrap.php
```

###yaf开发
```
[yaf手册](http://www.laruence.com/manual/index.html)
```

###本地访问
```
http访问:
    http://yaf.central.com/index/index?a=test
cli访问:
    /path/to/php app/cli.php "/index/index?a=test"
```
