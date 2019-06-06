# hmutil
一个基于PHP的工具包

-------------------------------------------------

## 安装

使用composer安装

~~~
composer require hmxingkong/hmutil
~~~

使用工具包
~~~
require_once __DIR__ . '/vendor/autoload.php';

use hmxingkong\utils\MTime;

Mtime::sleep(100);
...
~~~

更新工具包
~~~
composer update hmxingkong/hmutil
~~~


说明：
    首批工具类命名均以 M 开头，为了避免与原生类冲突，还没研究通过namespace来处理这个问题。
  
v0.1.2 内容：
 + 添加文件夹工具类： hmxingkong\utils\MDir
 + 日志工具类修改，日志内容输出添加换行
  
v0.1.1 内容：
 + 添加日志工具类： hmxingkong\utils\MLog
    
v0.1.0 内容：

 + 添加网络请求工具类： hmxingkong\utils\MHttp
 + 添加字符串处理工具类： hmxingkong\utils\MString
 + 添加时间处理工具类： hmxingkong\utils\MTime
    
~~~
1、MHttp类提供 http/https 网络请求、请求内容获取、请求头解析、header参数构造 以及 随机IP 功能，
通过组合使用可以实现接口请求、连接爬取、文件下载等功能；
2、MString类提供字符串包含判断、随机字符串生成、数组与xml双向转换功能，其中字符串包含判断分 以xxx开
头、以xxx结尾、包含 xxx 这三种类型；
3、MTime类提供毫秒时间戳获取、毫秒时间睡眠、格林威治时间处理
~~~