<?php

namespace ABM\tests;

use ABM\Kilns\Kilns;

/**
 * @covers ABM\Kilns\Kilns
 */
class KilnsTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSubProtocolsReturnsArray()
    {
        $null = new Kilns();
        $this->assertInternalType('array', $null->getSubProtocols());
    }

    public function testArchitectureReturnsArray()
    {
        $kiln = new Kilns();

        $config = ['Subscription-Key' => '填写您的订阅key',
                'Content-Type'        => 'Body的Content-Type',
                'Request-Method'      => 'POST',
                ];

        $service = $kiln::load($kiln::Vision, $config);

        // 请求参数，请参考官方Api文档上对应接口的说明
        $parameters = ['language'    => 'zh-Hans',
                 'detectOrientation' => true,
                ];

//请求Body，请参考官方Api文档上对应接口的说明
            $package = '{"url":"http://example.com/1.jpg"}';

//$a = $service->generateUrl('Ocr', $parameters, $package);
            $tester = $service->Ocr($parameters, $package);
        if ($tester === false) {
            $error = $service->getError();
            echo 'Error code:'.$error->getCode().".\n";
            echo 'message:'.$error->getMessage().".\n";
            echo 'ext:'.var_export($error->getExt(), true).".\n";
        } else {
            var_dump($tester);
        }
    }
}
