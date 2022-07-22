<?php
namespace ljh13043434556\emorpc\services;
use ljh13043434556\emorpc\exception\RpcException;


class AddressServices
{

    public function handle($str)
    {
        try{

            $result = app('rpc', [], true)->tool->Address->analyse(['str' => $str]);
            return $result;

        }catch (\Exception $e) {

            throw new RpcException('地址解释失败');

        }

    }

}
