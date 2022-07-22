<?php
namespace ljh13043434556\emorpc\services;


class OcrServices
{
    public function idcard1($url)
    {
        $result = app('rpc', [], true)->tool->Ocr->idcard([
            'url' => $url,
            'side' => 'front'
        ]);

        return $result;
    }

    public function idcard2($url)
    {
        $result = app('rpc', [], true)->tool->Ocr->idcard([
            'url' => $url,
            'side' => 'back'
        ]);

        return $result;
    }

    public function bankcard($url)
    {
        $result = app('rpc', [], true)->tool->Ocr->bankcard([
            'url' => $url,
            'side' => 'back'
        ]);

        return $result;
    }
}