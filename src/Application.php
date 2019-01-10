<?php
namespace YiiThrift;

use Yii;

/**
 * Class Application
 * @package YiiThrift
 */
class Application extends \yii\web\Application{

    /**
     * 处理请求（继承重写）
     * @param \yii\web\Request $request
     * @return \yii\web\Response
     */
    public function handleRequest($request)
    {
        //加载Thrift服务
        Yii::$app->get('thrift')->loadServices();

        //设置response
        $response = $this->getResponse();
        return $response;
    }

    /**
     * 设置组件对应类（继承重写）
     * @return array
     */
    public function coreComponents()
    {
        return array_merge(parent::coreComponents(), [
            'request' => ['class' => 'yii\web\Request'],
            'response' => ['class' => 'yii\web\Response'],
            'thrift' => ['class' => 'YiiThrift\ThriftComponent']
        ]);
    }
}