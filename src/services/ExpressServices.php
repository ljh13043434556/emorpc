<?php
namespace ljh13043434556\emorpc\services;


use ljh13043434556\emorpc\exception\RpcException;

class ExpressServices
{

    public function handle($param)
    {
        if(empty($param['delivery_id'])) {
            throw new RpcException('快递信息查询请转入快递单号');
        }
        if(empty($param['user_phone'])) {
            throw new RpcException('快递信息查询请转入收件人手机号码');
        }

        try{

            $result = app('rpc', [], true)->tool->Express->query($param);
            return $result;

        }catch (\Exception $e) {

            throw new \Exception('地址解释失败');

        }

    }

}
