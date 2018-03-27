# 简单的restful接口实例。
# 实例地址:http://hostname/
# this is a simple restfulApi test.
# get :select all products or one product;
# post:insert a new product
# put :update product info
# delete :delete one product

# 简单的接口验证实例。
# 实例地址:http://hostname/client 

# 文件结构：
# |--api/                                   api服务端
# |--api/controller                             功能模块 
# |--api/controller/ProductRequest                  restful实例代码
# |--api/controller/TestRequest                     接口验证实例代码
# |--api/controller/TokenRequest                    接口验证实例token生成器
# |--api/lib                                    类库
# |--api/lib/Apps.php                               token验证类库
# |--api/lib/MysqlClass.php                         mysql操作类 
# |--api/lib/Times.php                              时间操作类
# |--api/restful                                api服务端restful实现
# |--api/config.php                                 服务端、数据库配置

# |--client/                                app验证客户端调用实例
# |--client/lib                                 类库
# |--client/lib/Api.php                            Api类 
# |--client/lib/Curl.php                           Curl类 
# |--client/lib/PHPRedis.php                       PHPRedis 
# |--client/lib/Times.php                          时间操作类
# |--client/lib/func.php                           自定义函数
# |--api.php                                api入口文件
# |--index.php                              restful 测试入口文件
# |--apps.sql                               接口验证数据表
# |--.htaccess                              分布式配置文件

