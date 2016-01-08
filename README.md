#Yaf-api
######yaf安装：
```shell
sudo yum -y install gcc gcc-c++ make automake autoconf

wget http://pecl.php.net/get/yaf-2.2.9.tgz && tar zxvf yaf-2.2.9.tgz && cd yaf-2.2.9

/path/to/php/bin/phpize 

./configure --with-php-config=/path/to/php/bin/php-config

make && make install
```

######yaf配置php.ini
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

######nginx配置：
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

######应用目录
```
--app           应用目录
----controllers 控制器
******Error.php 异常捕捉控制器(必须)
----models      模型
----plugins     插件
----views       视图
------error     错误视图
****Bootstrap.php   引导文件
****cli.php         命令行入口
--config        配置目录
****application.ini 主配置文件
--library       类库目录
----eYaf        自定义扩展
----Helper      工具目录
******Tools.php     工具类
--log           日志目录
--public        入口目录
****index.php   入口文件
--tests         测试目录
--vendor        扩展目录
```

######yaf开发
```
[yaf手册](http://www.laruence.com/manual/index.html)
```

######本地访问
```
http访问:
    http://yaf.central.com/index/index?a=test
cli访问:
    /path/to/php app/cli.php "/index/index?a=test"
```
