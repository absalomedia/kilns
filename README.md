# Kilns

A Composer edition of the Microsoft Project Oxford SDK initally written by @xiaotinghe

The name comes from C.S Lewis' house in Oxford.

~~~
<?php
require_once './src/ProjectOxford/Api.php';

$config = array('Subscription-Key'       => '你的订阅id',
                'Content-Type'      => 'Body的Content-Type',
                'Request-Method'=> 'POST',
                );

// 第一个参数表示使用哪种API
$service = ProjectOxford::load(ProjectOxford::Vision, $config);

// 请求参数，请参考官方Api文档上对应接口的说明
$parameters = array('language' => 'zh-Hans',
                 'detectOrientation' => true
                );


// 重新设置secretKey
$secretKey = '你的secretKey';
$service->setConfigSecretKey($secretKey);
// 重新设置method
$method = 'POST';
$service->setConfigRequestMethod($method);

// 请求方法为对应接口的接口名，请参考wiki文档上对应接口的接口名
$a = $service->Ocr($parameters);

// 生成请求的URL，不发起请求
$a = $service->generateUrl('DescribeInstances', $parameters);

if ($a === false) {
    // 请求失败，解析错误信息
    $error = $service->getError();
    echo "Error code:" . $error->getCode() . ' message:' . $error->getMessage();

} else {
    // 请求成功
    var_dump($a);
}
~~~
