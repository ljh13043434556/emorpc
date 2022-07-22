<?php
namespace ljh13043434556\emorpc\services;


class TipServices
{

    public function xitong($title, $content)
    {
        $shop = config('rpcclient.services.tip.shop');
        $param['level'] = 'xitong';
        $param['title'] = $title . "【{$shop}|{$param['level']}】";
        $param['msg'] = $content;

        try{

            $result = app('rpc', [], true)->tip->Tip->send($param);
            return $result;

        }catch (\Exception $e) {


        }
    }


    public function yewu($title, $content)
    {
        $shop = config('rpcclient.services.tip.shop');
        $param['level'] = 'yewu';
        $param['title'] = $title . "{$shop}|【{$param['level']}】";
        $param['msg'] = $content;
        try{

            $result = app('rpc', [], true)->tip->Tip->send($param);
            return $result;

        }catch (\Exception $e) {

        }

    }
}
