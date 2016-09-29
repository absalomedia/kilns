<?php

namespace ABM\Kilns\Module;

/**
 * SpeakerRecognition
 * 云服务器模块类.
 */
class SpeakerRecognition extends Core
{
    /**
     * $serverHost
     * 接口域名.
     *
     * @var string
     */
    protected $serverHost = 'https://api.projectoxford.ai/spid/v1.0/';
}
