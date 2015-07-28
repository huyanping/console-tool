<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 2015/7/28
 * Time: 12:27
 *
 * HTTP�����������֤����
 */

namespace Jenner\ConsoleTool\Net;


class ProxyVerify
{

    /**
     * ��֤��URL
     * @var
     */
    protected $url;
    /**
     * ���ӳ�ʱʱ��
     * @var int
     */
    protected $timeout;
    /**
     * ���䳬ʱʱ��
     * @var int
     */
    protected $transfer_timeout;

    /**
     * �����б�
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
     * ִ��
     */
    public function handle()
    {
        foreach ($this->proxies as $proxy) {
            $this->verifyProxy($proxy['host'], $proxy['port']);
        }
    }

    /**
     * ��Ӵ���
     * @param $host
     * @param $port
     */
    public function addProxy($host, $port)
    {
        array_push($this->proxies, array('host' => $host, 'port' => $port));
    }

    /**
     * �������
     * @param $proxies
     */
    public function batchAddProxy($proxies)
    {
        $this->proxies = array_merge($this->proxies, $proxies);
    }

    /**
     * ִ��CURL����֤��Ӧ����
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