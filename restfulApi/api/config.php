<?php


defined('DBSERVER') or define('DBSERVER', '127.0.0.1');
defined('DBUSER') or define('DBUSER', 'test');
defined('DBPASSWORD') or define('DBPASSWORD', 'test');
defined('DBNAME') or define('DBNAME', 'test');
defined('TBNAME') or define('TBNAME', 'books');

define('WEBURL', 'http://localhost:83/CiaraStudy/restfulApi/');
define('HTTP_VERSION', 'HTTP/1.1');

$tbname=TBNAME;
$_DATABASE=
<<<mysql
        create database if not exists restful default charset utf8;
        use restful;
        create table  if not exists  $tbname(id int(11) AUTO_INCREMENT primary key ,
            title varchar(50) ,isbn varchar(20),price float(9,2),seller varchar(15),
            createat timestamp);
        insert into products set title='book1',isbn='123456',price='3.50',seller='seller',createat=now();
mysql;
