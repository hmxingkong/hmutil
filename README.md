# hmutil
一个基于PHP的工具包

-------------------------------------------------

## 安装

使用composer安装

~~~
composer require hmxingkong/hmutil
~~~
提示： 包名可指定版本号，e.g. hmxingkong/hmutil or hmxingkong/hmutil:0.1.0 or hmxingkong/hmutil=0.1.0 or "hmxingkong/hmutil 0.1.0"

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
   
v0.5.1 更新内容
 + 新增工具类MCode，提供去除给定代码的注释内容的操作
 + 调整工具类AES命名为MAes
 + 调整工具类RSA命名为MRsa
   
v0.5.0 更新内容
 + 新增工具类AES，提供方便进行AES加/解密的操作
 + 新增工具类RSA，提供方便进行RSA加/解密的操作
   
v0.4.2 更新内容
 + 新增工具类方法MFile::createFile，提供方便创建文件的能力
 + 新增工具类方法MFile::deleteFile，提供方便删除文件的能力
   
v0.4.1 更新内容
 + 工具类方法MString::arr2xml修复对多维数组转换时跟节点的兼容
   
v0.4.0 更新内容
 + 工具类方法MHttp::doRequest参数列表调整，添加请求参数类型指定 $reqDataType
 + 工具类方法MString::arr2xml添加多维数组兼容
    
v0.3.2 更新内容
 + 工具类方法MSpelling::getSpellFromToneName修复对 v & ü 的兼容
    
v0.3.1 更新内容
 + 工具类方法MSpelling::getPartsFromSpell、MSpelling::getPartsFromToneName新增三拼音节中的介母识别
    
v0.3.0 更新内容
 + 新增工具类MSpelling,提供处理汉语拼音的能力
 + 新增工具类方法MString::strlen，提供计算中英文混合字符串长度的能力
    
v0.2.5 更新内容
 + 工具类MContentType修正.xlsx文件对应的Content-Type(Mime-Type)
 + 工具类方法MHttp::getPretendArgs定义改为static修饰
    
v0.2.4 更新内容
 + 工具类方法MNetFile::sendFileToClient修复浏览器无法识别下载文件名的BUG
    
v0.2.3 更新内容
 + 新增工具类方法MTime::formatSeconds，提供对指定秒数格式化的能力
    
v0.2.2 更新内容
 + 添加工具类MNetFile，提供服务端大文件下载功能
    
v0.2.1 更新内容
 + 修复MDir引用BUG
    
v0.2.0 更新内容
 + 划分包名，将MHttp归类到network包，将MDir归类到file包
 + 新增工具类MFile，提供文件路径处理方法
 + 新增工具类MContentType，提供网络资源类型处理
 + 工具类方法MDir::listFiles修复BUG
 + 工具类方法MString::startWith修复BUG
  
v0.1.3 更新内容：
 + 工具类方法MDir::listFiles新增回调参数，用于大量文件存在的场景
 + 工具类方法MDir::listFiles新增显示递归参数
 + 工具类方法MDir::listFiles废弃includeDir参数
 + 工具类方法MDir::mkdir返回值调整，当目标文件夹存在时返回true而不是false
  
v0.1.2 更新内容：
 + 添加文件夹工具类： hmxingkong\utils\MDir
 + 日志工具类修改，日志内容输出添加换行
  
v0.1.1 更新内容：
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