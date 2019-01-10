<?php
namespace YiiThrift;

use Thrift\TMultiplexedProcessor;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TPhpStream;
use Thrift\Transport\TBufferedTransport;

class ThriftHttpServices{
    /**
     * @var TMultiplexedProcessor
     */
    private $tMultiplexedProcessor;
    private $transport;
    private $protocol;

    /**
     * 初始化Thrift服务
     * ThriftHttpServices constructor.
     */
    public function __construct(){
        $this->tMultiplexedProcessor = new TMultiplexedProcessor();
        $this->transport = new TBufferedTransport(new TPhpStream(TPhpStream::MODE_R | TPhpStream::MODE_W));
        $this->protocol = new TBinaryProtocol($this->transport, true, true);
    }

    /**
     * 运行Thrift服务
     */
    public function run(){
        $this->transport->open();
        $this->tMultiplexedProcessor->process($this->protocol, $this->protocol);
        $this->transport->close();
    }

    /**
     * 注册Thrift服务
     * @param $serviceName
     * @param $processor
     */
    public function registerProcessor($serviceName, $processor){
        $this->tMultiplexedProcessor->registerProcessor($serviceName, $processor);
    }
}