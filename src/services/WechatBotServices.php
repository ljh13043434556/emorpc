<?php

namespace ljh13043434556\emorpc\services;

/**
 * 用于特殊应用下用websocket 主动推送消息
 * Class WebsocketServices
 * @package ljh13043434556\emorpc\services
 */
class WechatBotServices
{
    /**
     * @param $path  接收信息的对象
     * @param $to   接收信息的微信
     * @param $msg  发送的信息
     * @return mixed
     * @throws \Exception
     */
    public function msg($path, $to, $msg)
    {
        try{

            $result = app('rpc', [], true)->websocket->WechatBot->send([
                'path' => $path,
                'to' => $to,
                'type' => 'msg',
                'data' => $msg
            ]);
            return $result;

        }catch (\Exception $e) {
            throw new \Exception('发送失败');
        }
    }


    /**
     * @param $path  接收消息对象
     * @param $to   接收信息的微信
     * @param $url  文件的地址
     * @return mixed
     * @throws \Exception
     */
    public function file($path, $to, $url)
    {
        try{

            $result = app('rpc', [], true)->websocket->WechatBot->send([
                'path' => $path,
                'to' => $to,
                'type' => 'file',
                'data' => $url
            ]);
            return $result;

        }catch (\Exception $e) {
            throw new \Exception('发送失败');
        }
    }


    /**
     * @param $path 接收的对象
     * @param $post_url 获取用户列表后，提交的接口
     * @return mixed
     * @throws \Exception
     */
    public function getWxUserList($path, $post_url)
    {
        try{

            $result = app('rpc', [], true)->websocket->WechatBot->send([
                'path' => $path,
                'type' => 'user_list',
                'post_url' => $post_url
            ]);
            return $result;

        }catch (\Exception $e) {
            throw new \Exception('发送失败');
        }
    }
}