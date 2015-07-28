<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/7/28
 * Time: 12:27
 *
 * HTTP代理服务器验证工具
 */

namespace Jenner\ConsoleTool\Net;


class ProxyVerify
{

    /**
     * 验证的URL
     * @var
     */
    protected $url;
    /**
     * 链接超时时间
     * @var int
     */
    protected $timeout;
    /**
     * 传输超时时间
     * @var int
     */
    protected $transfer_timeout;

    /**
     * 代理列表
     * @var
     */
    private $proxies;

    /**
     * @param $url
     * @param int $timeout
     * @param int $transfer_timeout
     */
    public function __construct($url, $timeout = 60, $transfer_timeout = 300)
    {
        $this->url = $url;
        $this->timeout = $timeout;
        $this->transfer_timeout = $transfer_timeout;
    }

    /**
     * 执行
     */
    public function handle()
    {
        foreach ($this->proxies as $proxy) {
            $this->verifyProxy($proxy['host'], $proxy['port']);
        }
    }

    /**
     * 添加代理
     * @param $host
     * @param $port
     */
    public function addProxy($host, $port)
    {
        array_push($this->proxies, array('host' => $host, 'port' => $port));
    }

    /**
     * 批量添加
     * @param $proxies
     */
    public function batchAddProxy($proxies)
    {
        $this->proxies = array_merge($this->proxies, $proxies);
    }

    /**
     * 执行CURL，验证响应代码
     * @param $host
     * @param $port
     * @return mixed
     * @throws \RuntimeException
     */
    public function verifyProxy($host, $port)
    {
        $curl = curl_init();
        $proxy = "http://{$host}:{$port}";
        curl_setopt($curl, CURLOPT_PROXY, $proxy);
        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_NOBODY, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
        $result = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($http_code != 200) {
            throw new \RuntimeException("error code:" . $http_code);
        }

        return $result;
    }
}