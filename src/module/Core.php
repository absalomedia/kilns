<?php

namespace ABM\Kilns\Module;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

abstract class Core
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
    public function __construct($config = [])
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
                default:;
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
    public function setConfigContentType($contentType)
    {
        $this->_contentType = strtoupper($contentType);

        return $this;
    }

    /**
     * setConfigSecretKey
     * 设置secretKey.
     *
     * @param string $secretKey
     */
    public function setConfigRequestMethod($requestMethod)
    {
        $this->_requestMethod = $requestMethod;

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
        $response = new Response();
    }

    /**
     * getLastResponse
     * 获取请求的原始返回.
     *
     * @return
     */
    public function getLastResponse($response)
    {
        $response = new Response();

        return $response;
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
        $action = ucfirst($name);

        return $this->generateUrlBody($params, $this->_secretKey, $this->_contentType, $this->_requestMethod, $this->_serverHost.$name, $body);
    }

    /**
     * generateUrlBody
     * 生成请求的URLBody.
     *
     * @param array  $paramArray    请求参数
     * @param string $secretKey     订阅密钥
     * @param string $ContentType   请求Body的类型
     * @param string $requestMethod 请求方式，GET/POST
     * @param string $url           接口URL
     * @param string $body          请求Body
     *
     * @return
     */
    public static function generateUrlBody($paramArray, $secretKey, $contentType, $requestMethod, $url, $body)
    {
        if ($contentType) {
            $header[] = 'Content-Type:'.strtolower($contentType);
        }

        $header[] = 'Ocp-Apim-Subscription-Key:'.$secretKey;
        switch ($requestMethod) {
            case 'GET': $url .= '&'.http_build_query($body);
                break;
            default:  $url .= '?'.http_build_query($paramArray);
                $urlBody['body'] = $body;
                break;
        }

        $urlBody = array_merge(['url' => $url, 'method' => $requestMethod, 'header' => $header], $urlBody);

        return $urlBody;
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
    public function call($name, $arguments)
    {
        require_once Kilns_ROOT_PATH.'/Module/Core.php';
        $response = $this->dispatchRequest($name, $arguments);

        return $this->dealResponse($response);
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
    protected function dispatchRequest($name, $arguments)
    {
        $action = ucfirst($name);
        $params = [];
        if (is_array($arguments) && !empty($arguments)) {
            $params[0] = (array) $arguments[0];
        }
        if (is_array($arguments) && !empty($arguments)) {
            $params[1] = $arguments[1];
        }

        $response = $this->send($params, $this->_secretKey, $this->_contentType, $this->_requestMethod, $this->_serverHost.$action);

        return $response;
    }

    /**
     * send
     * 发起请求
     *
     * @param array  $paramArray    请求参数
     * @param string $secretKey     订阅密钥
     * @param string $ContentType   请求Body的类型
     * @param string $requestMethod 请求方式，GET/POST
     * @param string $url           接口URL
     * @param string $body          请求Body
     *
     * @return
     */
    public static function send($paramArray, $secretKey, $contentType, $requestMethod, $requestHost)
    {
        $param = $paramArray[0];
        $body = $paramArray[1];
        if ($contentType) {
            $header[] = 'Content-Type:'.strtolower($contentType);
        }

        $header[] = 'Ocp-Apim-Subscription-Key:'.$secretKey;

        $url = $requestHost;

        switch ($requestMethod) {
            case 'POST': $url .= '?'.http_build_query($param);
                break;
            default: $url .= '&'.http_build_query($body);
                break;
        }
        $request = new Request($requestMethod, $url, $header, $body);

        return $request;
    }

    /**
     * _dealResponse
     * 处理返回.
     *
     * @param array $response
     *
     * @return
     */
    protected function dealResponse($response)
    {
        $phrase = $response->getReasonPhrase();
        if ($phrase !== 'OK') {
            echo $response->getStatusCode();
            echo $response->getBody();

            return false;
        }

        if (count($response)) {
            return $response;
        } else {
            return true;
        }
    }
}
