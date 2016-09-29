<?php
/**
 * Kilns_Common_Request.
 */
class Kilns_Common_Request
{
    /**
     * $_requestUrl
     * 请求url.
     *
     * @var string
     */
    protected static $_requestUrl = '';
    /**
     * $_rawResponse
     * 原始的返回信息.
     *
     * @var string
     */
    protected static $_rawResponse = '';

    /**
     * $_timeOut
     * 设置连接主机的超时时间.
     *
     * @var int 数量级：秒
     * */
    protected static $_timeOut = 30;
    /**
     * getRequestUrl
     * 获取请求url.
     */
    public static function getRequestUrl()
    {
        return self::$_requestUrl;
    }
    /**
     * getRawResponse
     * 获取原始的返回信息.
     */
    public static function getRawResponse()
    {
        return self::$_rawResponse;
    }
    /**
     * generateUrl
     * 生成请求的URL.
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
    public static function generateUrlBody($paramArray, $secretKey, $ContentType, $requestMethod, $url, $body)
    {
        if ($ContentType) {
            $header[] = 'Content-Type:'.strtolower($ContentType);
        }

        $header[] = 'Ocp-Apim-Subscription-Key:'.$secretKey;
        if ($requestMethod == 'GET') {
            $url .= '&'.http_build_query($body);
        } else {
            $url .= '?'.http_build_query($paramArray);
            $urlBody['body'] = $body;
        }
        $urlBody = array_merge(array('url' => $url, 'method' => $requestMethod, 'header' => $header), $urlBody);

        return $urlBody;
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
    public static function send($paramArray, $secretKey, $ContentType, $requestMethod, $requestHost)
    {
        $param = $paramArray[0];
        $body = $paramArray[1];
        if ($ContentType) {
            $header[] = 'Content-Type:'.strtolower($ContentType);
        }

        $header[] = 'Ocp-Apim-Subscription-Key:'.$secretKey;

        $url = $requestHost;

        $ret = self::_sendRequest($url, $param, $requestMethod, $body, $header);

        return $ret;
    }
    /**
     * _sendRequest.
     *
     * @param string $url        请求url
     * @param array  $paramArray 请求参数
     * @param string $method     请求方法
     * @param string $body       请求Body
     * @param string $header     请求header
     *
     * @return
     */
    protected static function _sendRequest($url, $paramArray, $method, $body, $header)
    {
        $ch = curl_init();

        if ($method == 'POST') {
            $body = is_array($body) ? http_build_query($body) : $body;
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            $url .= '?'.http_build_query($paramArray);
        } else {
            $url .= '&'.http_build_query($body);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        self::$_requestUrl = $url;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::$_timeOut);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
        $resultStr = curl_exec($ch);

        self::$_rawResponse = $resultStr;
        $result = json_decode($resultStr, true);
        if (!$result) {
            return $resultStr;
        }

        return $result;
    }
}
