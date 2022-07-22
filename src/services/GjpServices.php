<?php
namespace ljh13043434556\emorpc\services;



class GjpServices
{

    public function test()
    {
        $result = app('rpc')->gjp->Order->test(['a' => 1]);
        return $result;
    }

    /**
     * 查所有仓库
     */
    public function queryktypelist()
    {
        $result = app('rpc')->gjp->Warehouse->queryktypelist();
        return $result;
    }


    /**
     * 上传 / 更新 商品
     * @param StoreProduct $product
     * @return mixed
     */
    public function uploadproducts($product)
    {
        $param = DataTransform::productInfo($product);
        $param['OuterId'] = config('rpcclient.services.gjp.shop') . $param['OuterId'];
        $result = app('rpc')->gjp->Product->uploadproducts([$param]);
        return $result;
    }


    /**
     * 查询商品信息
     * @param $param
     * @return mixed
     */
    public function queryproductinfo($param = [])
    {
        $param['pagesize'] = $param['pagesize'] ?? 50;
        $param['pageno']   = $param['pageno'] ?? 1;

        $result = app('rpc')->gjp->Product->queryproductinfo($param);
        return $result;
    }


    /**
     * 商品库存批量查询
     * @param $param ['numids' =>[], 'ktypeids' => []]  numids商品ID , ktypeids 仓库ID
     */
    public function batchquerysaleqty($param)
    {
        $param['ktypeids']   = $param['ktypeids'] ?? [];
        $result = app('rpc')->gjp->Warehouse->batchquerysaleqty($param);
        return $result;
    }


    /**
     * 查询商品库存
     * @param $param  ['numid' =>’124‘, 'ktypeids' => []]  numid商品ID , ktypeids 仓库ID
     */
    public function querysaleqty($param)
    {
        $param['ktypeids']   = $param['ktypeids'] ?? [];
        $result = app('rpc')->gjp->Warehouse->querysaleqty($param);
        return $result;
    }


    /**
     * 批量上传订单
     * @param $orders 订单对象数组
     */
    public function uploadsaleorders($orders)
    {
        $param = DataTransform::Orders($orders);
        $result = app('rpc')->gjp->Order->uploadsaleorders($param);
        return $result;
    }


    /**
     * 被取消的订单，管家婆服务，会一直执行，直到结束
     * @param $orders
     * @return mixed
     */
    public function cancelOrderAsy($orders)
    {
        $param = DataTransform::Orders($orders);
        $result = app('rpc')->gjp->Order->cancelOrderAsy($param);
        return $result;
    }


    public function isCancel($param)
    {
        if(!is_array($param)) {
            $param = ['tradeid' => $param];
        }
        $result = app('rpc')->gjp->Order->isCancel($param);
        return $result;
    }


    /**
     * 查询订单处理状态
     * @param $tradeid 订单编号
     */
    public function querytradestatus($param)
    {
        if(!is_array($param)) {
            $param = ['tradeid' => $param];
        }
        $result = app('rpc')->gjp->Order->querytradestatus($param);
        return $result;
    }


    /**
     *订单查询
     * @param $tradeid
     */
    public function querysaleorder($param)
    {
        if(!is_array($param)) {
            $param = ['tradeid' => $param];
        }
        $result = app('rpc')->gjp->Order->querysaleorder($param);
        return $result;
    }

}
