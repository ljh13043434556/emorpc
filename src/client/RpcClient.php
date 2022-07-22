<?php
namespace ljh13043434556\emorpc\client;


class RpcClient
{
    protected $config = [];
    protected $services = [];           //存放服务端的名称，及IP与端口
    protected $currentService = '';     //当前服务名称
    protected $currentClass = '';       //当前服务，调用的类
    protected $socket;                  //Socket 类

    public function  __construct()
    {
        //rpc名称，及服务器IP地址，端口
        $config = config('rpcclient.services');
        $this->services = array_keys($config);
        $this->config = $config;
        $this->socket = new Socket($this);

    }

    /**
     * 获取相应的 IP 及 端口
     * @param $name
     * @return mixed
     */
    public function getConfig($name)
    {
        return $this->config[$name];
    }


    /**
     * app('rpc')->gjp->OrderInterface->querySaleOrder(['a' => 1,  'b' => 2]);
     * 中的   gjp   OrderInterface 调用方式
     * @param $name
     * @return $this
     */
    public function __get($name)
    {
        $this->setService($name) || $this->setClass($name);
        return $this;
    }


    /**
     * 服务中的模块
     * @param $name
     */
    protected function setClass($name)
    {
        $this->currentClass = $name;

    }


    public function getParam()
    {
        return json_decode(app()->request->post('param'), true);
    }

    /**
     * 如服务中在的 gjp
     * @param $name
     * @return bool
     */
    protected function setService($name)
    {
        if(!in_array($name, $this->services)) {
            return false;
        }
        $this->currentService = $name;
        $this->config = $this->config[$name];
        return true;

    }



    /**
     * 通过socket 调用 的 rpc服务端程序
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws \think\Exception
     */
    protected function execute($name, $arguments)
    {


        $config = $this->config;

        $serviceName = $config['service'];
        $param = [
            'arg'     => $arguments[0],
            'service' => $serviceName,
            'module'  => $this->currentClass,
            'action'  => $name
        ];


        if(isset($config['beforeOp'])) {
            $param['arg'] = call_user_func( [$config['beforeOp'], 'handle'], $param['arg']);
        }

        $responseData = $this->socket->request($param);

        $result =  json_decode($responseData, true);

        switch ($result['status'])
        {
            case 0:
                return $result['result'];
                break;
//            case 1001:      //获取不到服务端可用节点，调用失败
//            case 1002:      //客户端连接服务端节点超时，调用失败
//            case 1003:      //服务端响应超时，调用失败
//            case 2001:      //服务端读取客户端请求数据包超时，调用失败
//            case 2002:      //客户端发送的数据包不合法，调用失败
//            case 3000:      //服务端节点宕机，调用失败
//            case 3001:      //调用的服务不存在，调用失败
//            case 3002:      //调用服务的子模块不存在，调用失败
//            case 3003:      //调用服务错误，调用失败
            default:        //未知错误，调用失败
                throw new \think\Exception($result['msg']);
                break;
        }

    }


    /**
     * app('rpc')->gjp->OrderInterface->querySaleOrder(['a' => 1,  'b' => 2]);
     * 中的 querySaleOrder 方法及 参数
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->execute($name, $arguments);
    }
}