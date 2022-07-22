<?php
namespace ljh13043434556\emorpc\decorate;
class GjpDecorate
{
    /**
     * 给发送前的数据，处理下
     * @param $param
     */
    public static function handle($param)
    {
        return [
            'arg' => $param,
            '_shop_' => config('rpcclient.services.tip.shop')
        ];
    }
}