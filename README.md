# yii2-thrift

## 项目介绍
本项目用于在Yii框架下，进行Thrift服务端开发。

## 安装方法
`composer require evondu/yii2-thrift:dev-master`

## 配置方法
* 替换`app/web/index.php`中的Application类：`yii\web\Application`=>`YiiThrift\Application`
* 在`app/config/main.php`中配置thrift组件：
    * path默认为应用目录下的gen-php
    * definitions为空时，会自动扫描加载gen-php目录下的所有文件夹
    * serviceMap为空时，会自动扫描加载服务名空间目录下的所有类
    * 配置例子如下：
```
'components' => 
[
    //……
    'thrift' => [
        //thrift文件生成路径（默认为应用目录下的gen-php）
        'path' => "",
        //配置名空间对应目录(名空间 => 对应目录)(相对于生成目录)
        'definitions' => [
            'thybot' => "thybot"
        ],
        //配置服务名空间（相当于配置控制器名空间）
        'serviceNamespace' => 'api_thrift\services',
        //配置服务名映射(服务名 => 服务名空间目录下的服务类)
        'serviceMap' => [
            'DemoService' => 'DemoService',
        ],
    ]
    //……
],
```
* 实现Service类
    * 在配置的`serviceNamespace`下添加Service类
    * Service类必须实现两个接口，一个是thrift编译生成的服务If接口，一个是`YiiThrift\ThriftServerInterface`
    * 实现例子如下：
```
<?php
namespace api_thrift\services;

use YiiThrift\ThriftServerInterface;
use thybot\tlk\demo\DemoServiceIf;
use thybot\tlk\ApiSession;

class DemoService implements DemoServiceIf, ThriftServerInterface{
    public function ping(ApiSession $session)
    {
        return true;
    }

    public function getProcessorClass()
    {
        return 'thybot\tlk\demo\DemoServiceProcessor';
    }
}
?>
```
    