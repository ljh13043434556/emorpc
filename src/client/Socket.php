<?php
namespace ljh13043434556\emorpc\client;
use ljh13043434556\emorpc\exception\RpcException;

class Socket
{

    protected $rpc;

    public function __construct(RpcClient $rpc)
    {
        $this->rpc = $rpc;
    }



    /**
     * 发送及，返回结果
     * @param String $name
     * @param String $raw
     * @return string
     * @throws \think\Exception
     */
    public function request(array $data)
    {

        // tcp://127.0.0.1:9600（示例请求地址） 是 rpc 服务端的地址，这里是本地，所以使用 127.0.0.1
        // 开发者需要根据实际情况调整进行调用
        $ip = $this->rpc->getConfig('ip');
        $port = $this->rpc->getConfig('port');

        $ipaddr = "tcp://{$ip}:{$port}";
        $fp = stream_socket_client($ipaddr);
        $raw = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        fwrite($fp, pack('N', strlen($raw)) . $raw); // pack 数据校验

        $try  = 3;
        $data = fread($fp, 4);
        if (strlen($data) < 4 && $try > 0) {
            $data .= fread($fp, 4);
            $try--;
            usleep(1);
        }

        // 做长度头部校验
        $len  = unpack('N', $data);
        $data = '';
        $try  = 3;
        while (strlen($data) < $len[1] && $try > 0) {
            $data .= fread($fp, $len[1]);
            $try--;
            usleep(1);
        }

        trace('request:' . $raw, 'rpc');
        trace('response:' . $data, 'rpc');


        if (strlen($data) != $len[1]) {
            throw new RpcException('调用服务返回数据错误!');
        }


        fclose($fp);

        return $data;
    }





}