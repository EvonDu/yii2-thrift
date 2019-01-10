<?php
namespace YiiThrift;

use Yii;
use yii\base\Exception;

/**
 * Class Thrift
 * @package YiiThrift
 */
class ThriftComponent extends \yii\base\Component
{
    /**
     * 编译后生成目录(gen-php)
     * @var string
     */
    public $path;

    /**
     * 定义生成文件的名空间映射
     * ```
     * [
     *    'namespace' => '' //名空间 => 目录(这里的目录是gen-php下的相对路径)
     * ]
     * ```
     * @var array
     */
    public $definitions = [];

    /**
     * 服务类的名空间
     * @var string
     */
    public $serviceNamespace;

    /**
     * 定义服务名称映射
     * ```
     * [
     *    'demo' => 'DemoService' //服务名 => 对应服务实现类
     * ]
     * ```
     * @var array
     */
    public $serviceMap = [];

    /**
     * 初始化（包括默认赋值）
     * @throws \yii\base\InvalidConfigException
     */
    public function init(){
        parent::init();

        if (empty($this->path)) {
            $this->path = Yii::$app->getBasePath() . DIRECTORY_SEPARATOR . 'gen-php' . DIRECTORY_SEPARATOR;
        }
        if (empty($this->definitions = $this->definitions ?: $this->scanDefinitions())) {
            throw new \yii\base\InvalidConfigException('Namespace definitions can not be empty');
        }
        if (empty($this->serviceMap = $this->serviceMap ?: $this->scanServices())) {
            throw new \yii\base\InvalidConfigException('Service maps can not be empty');
        }
        $this->registerNamespace();
    }

    /**
     * 注册服务的名空间
     * @return void
     */
    private function registerNamespace(){
        foreach ($this->definitions as $namespace => $dir){
            Yii::setAlias("@$namespace", $this->path.$dir);
        }
    }

    /**
     * 扫描生成目录下的文件夹成名空间(当$definitions为空时)
     * @return array
     */
    private function scanDefinitions(){
        //处理路径
        $pathinfo = pathinfo($this->path);
        $path = $pathinfo["dirname"] . DIRECTORY_SEPARATOR . $pathinfo["filename"] . DIRECTORY_SEPARATOR;

        //扫描路径
        $definitions = [];
        if(is_dir($path)){
            $dir = opendir($path);
            while ($file = readdir($dir)){
                if($file === "." || $file === "..")
                    continue;
                if(is_dir($path.$file))
                    $definitions[$file] = $file;
            }
        }

        //返回
        return $definitions;
    }

    /**
     * 扫描名空间下的所有服务文件(当$serviceMap为空时)
     * @return array
     * @throws Exception
     */
    private function scanServices(){
        //判断名空间合法
        $ns = explode("\\",$this->serviceNamespace);
        if(empty($ns))
            throw new Exception("thrift configuration parameter error : [serviceNamespace].");

        //获取路径
        $dir = array_shift($ns);
        $base = Yii::getAlias("@$dir");
        array_unshift($ns,$base);
        $path = implode(DIRECTORY_SEPARATOR, $ns);

        //扫描路径
        $services = [];
        if(is_dir($path)){
            $dir = opendir($path);
            while ($file = readdir($dir)){
                if(preg_match("/.php$/",$file)){
                    $file = substr($file,0,-4);
                    $services[$file] = $file;
                }
            }
        }

        //返回结构
        return $services;
    }

    /**
     * 加载映射中的所有服务
     */
    public function loadServices(){
        //创建服务
        $server = new ThriftHttpServices();

        //遍历服务
        foreach($this->serviceMap as $name => $class){
            //服务类实例
            $classNmae = "$this->serviceNamespace\\$class";
            $service = new $classNmae();
            $processorName = $service->getProcessorClass();
            $processor = new $processorName($service);

            //注册到服务
            $server->registerProcessor($name, $processor);
        }

        //运行服务
        $server->run();
    }
}
