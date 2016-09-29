<?php

if (!defined('Kilns_ROOT_PATH')) {
    // 目录入口
    define('Kilns_ROOT_PATH', dirname(dirname(__FILE__)));
}
require_once Kilns_ROOT_PATH.'/Common/Base.php';
/**
 * Kilns_Module_Base
 * 模块基类.
 */
abstract class Kilns_Module_Base extends Kilns_Common_Base
{
    /**
     * $serverHost
     * 接口域名.
     *
     * @var string
     */
    protected $serverHost = '';
    /**
     * $secretKey
     * secretKey.
     *
     * @var string
     */
    protected $secretKey = '';

    /**
     * __construct.
     *
     * @param array $config [description]
     */
    public function __construct($config = array())
    {
        if (!empty($config)) {
            $this->setConfig($config);
        }
    }
    /**
     * setConfig
     * 设置配置.
     *
     * @param array $config 模块配置
     */
    public function setConfig($config)
    {
        if (!is_array($config) || !count($config)) {
            return false;
        }
        foreach ($config as $key => $val) {
            switch ($key) {
                case 'Subscription-Key':
                    $this->setConfigSecretKey($val);
                    break;
                case 'Content-Type':
                    $this->setConfigContentType($val);
                    break;
                case 'Request-Method':
                    $this->setConfigRequestMethod($val);
                    break;
                default:
                    ;
                break;
            }
        }

        return true;
    }
    /**
     * setConfigSecretKey
     * 设置secretKey.
     *
     * @param string $secretKey
     */
    public function setConfigSecretKey($secretKey)
    {
        $this->_secretKey = $secretKey;

        return $this;
    }
    /**
     * setConfigRequestMethod
     * 设置请求方法.
     *
     * @param string $method
     */
    public function setConfigContentType($ContentType)
    {
        $this->_ContentType = strtoupper($ContentType);

        return $this;
    }
    /**
     * setConfigSecretKey
     * 设置secretKey.
     *
     * @param string $secretKey
     */
    public function setConfigRequestMethod($RequestMethod)
    {
        $this->_RequestMethod = $RequestMethod;

        return $this;
    }
    /**
     * getLastRequest
     * 获取上次请求的url.
     *
     * @return
     */
    public function getLastRequest()
    {
        require_once Kilns_ROOT_PATH.'/Common/Request.php';

        return Kilns_Common_Request::getRequestUrl();
    }
    /**
     * getLastResponse
     * 获取请求的原始返回.
     *
     * @return
     */
    public function getLastResponse()
    {
        require_once Kilns_ROOT_PATH.'/Common/Request.php';

        return Kilns_Common_Request::getRawResponse();
    }
    /**
     * generateUrl
     * 生成请求的URL，不发起请求
     *
     * @param string $name   接口方法名
     * @param array  $params 请求参数
     * @param string $body   请求Body
     *
     * @return
     */
    public function generateUrl($name, $params, $body)
    {
        require_once Kilns_ROOT_PATH.'/Common/Request.php';
        $action = ucfirst($name);

        return Kilns_Common_Request::generateUrl($params, $this->_secretKey, $this->_ContentType, $this->_RequestMethod, $this->_serverHost.$name, $body);
    }
    /**
     * __call
     * 通过__call转发请求
     *
     * @param string $name      方法名
     * @param array  $arguments 参数
     *
     * @return
     */
    public function __call($name, $arguments)
    {
        require_once Kilns_ROOT_PATH.'/Module/Base.php';
        $response = $this->_dispatchRequest($name, $arguments);

        return $this->_dealResponse($response);
    }
    /**
     * _dispatchRequest
     * 发起接口请求
     *
     * @param string $name      接口名
     * @param array  $arguments 接口参数
     *
     * @return
     */
    protected function _dispatchRequest($name, $arguments)
    {
        $action = ucfirst($name);
        $params = array();
        if (is_array($arguments) && !empty($arguments)) {
            $params[0] = (array) $arguments[0];
        }
        if (is_array($arguments) && !empty($arguments)) {
            $params[1] = $arguments[1];
        }

        require_once Kilns_ROOT_PATH.'/Common/Request.php';
        $response = Kilns_Common_Request::send($params, $this->_secretKey, $this->_ContentType, $this->_RequestMethod, $this->_serverHost.$action);

        return $response;
    }
    /**
     * _dealResponse
     * 处理返回.
     *
     * @param array $rawResponse
     *
     * @return
     */
    protected function _dealResponse($rawResponse)
    {
        if (!is_array($rawResponse)) {
            $this->setError('', 'request falied!');

            return false;
        }
        if ($rawResponse['code']) {
            $ext = '';
            require_once Kilns_ROOT_PATH.'/Common/Error.php';
            $this->setError($rawResponse['code'], $rawResponse['message'], $ext);

            return false;
        }
        unset($rawResponse['code'], $rawResponse['message']);
        if (count($rawResponse)) {
            return $rawResponse;
        } else {
            return true;
        }
    }
}
