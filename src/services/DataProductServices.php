<?php
namespace ljh13043434556\emorpc\services;
use ljh13043434556\emorpc\exception\RpcException;


class DataProductServices
{


    public function __call($name, $arguments)
    {

        try{
            $result = app('rpc', [], true)->dataEmoProduct->EmoProduct->$name($arguments[0]);
            return $result;
        }catch (\Exception $e) {
            throw new RpcException($e->getMessage());

        }
    }


    public function upload($product_id, $data)
    {
        $this->setProduct($data['product']);
        $this->setProductResult(['product_id' => $product_id, 'data' => $data['productResult']]);
        $this->setProductAttr(['product_id' => $product_id, 'data' => $data['productAttr']]);
        $this->setProductAttrValue(['product_id' => $product_id, 'data' => $data['productAttrValue']]);
    }


    public function download($product_id)
    {
        $data = [
            'product' => $this->getProduct(['product_id' => $product_id]),
            'productResult' => $this->getProductResult(['product_id' => $product_id]),
            'productAttr' => $this->getProductAttr(['product_id' => $product_id]),
            'productAttrValue' => $this->getProductAttrValue(['product_id' => $product_id]),
        ];
        return $data;
    }


}