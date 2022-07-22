<?PHP

namespace ljh13043434556\emorpc\services;


/**
 * 用于把 crmeb 数据 转换成 ZYX接受的数据
 * Class DataTransform
 * @package crmeb\zyx
 */
class DataTransform
{
    protected static $productList = [];



    /**
     * 转换商品信息, 把 emo中的商品数据，转成 管家婆中的数据
     */
    public static function productInfo($productModel)
    {
        $pos = strpos($productModel->image, '?');
        $img = $pos !== false ? substr($productModel->image, 0, $pos) : $productModel->image;
        $newData = [
            'ProductName' => $productModel->store_name,        //商品名称
            'NumId' => $productModel->id,              //商品数字ID（请与订单明细中ptypeid保持一致）
            'OuterId' => $productModel->id,            //商品商家编码
            'PicUrl' => $img,             //商品主图片地址
            'Price' => 0,              //商品价格
            'StockStatus' => $productModel->is_show ? 1 : 2,        //商品在售状态(1-在售;2-库中)
        ];

        $skus = [];
        $attrs = $productModel->attrvalue;

        foreach($attrs as $item) {
            $skuId = substr(md5($item->suk), 0 ,12);
            $skus[] = [
                'NumId' => $productModel->id,
                'SkuId' => $skuId,
                'OuterSkuId' => $skuId,
                "Properties" => null,
                'PropertiesName' => $item->suk,
                'Qty' => $item->stock,
                'Price' => 0,
                'BarCode' => $item->bar_code,
            ];
        }


        $newData['Skus']  = $skus;

        return $newData;
    }


    /**
     * 把文本框中的 括号 去掉
     */
    protected static function removeKh($str)
    {
        $str = str_replace(['（', '）'], ['(', ')'], $str);
        $str = str_replace(['【', '】'], ['(', ')'], $str);
        $str = str_replace(['[', ']'], ['(', ')'], $str);
        $str = preg_replace('/\((.*?)\)/','', $str);
        return $str;
    }


    public static function removeFh($str)
    {
        return str_replace(['+', '&'], '', $str);
    }


    /**
     * @param $order    订单
     * @param $refundOrder  退款订单
     */
    public static function refund($order, $refundOrder)
    {
        $tradestatus = 0;
        if($order['status'] == 2 || $order['status'] == 3) {
            $tradestatus = 4;
        }else if($order['status'] == 1) {
            $tradestatus = 3;
        } else if($order['total_num'] == $refundOrder['refund_num']) {
            $tradestatus = -1;
        } else if($order['paid']) {
            $tradestatus = 2;
        } else {
            $tradestatus = 1;
        }

        $refundstatus = -1;
        $cart_info = $refundOrder->cart_info;   //退款商品信息

        foreach($cart_info as $cart)
        {
            $data = [
                'refundnumber' => $refundOrder['id'],  //*售后单号
                'tradeid' => $refundOrder['store_order_id'],    //*交易单号
                'tradestatus' => $tradestatus,      //*订单交易状态（交易状态 -1=全部,1= 未付款订单，2=已付款订单，3=已发货订单，4=交易成功订单，5=已关闭订单，6=部分发货）
                'buyernick' => $order['real_name'],//买家昵称
                'refundcreatetime' => date('Y-m-d H:i:s', $refundOrder['add_time']), //*退款申请时间
                'description' => $refundOrder['refund_goods_explain'],  //退款说明
                'refundtype' => $refundOrder['refund_good'], //*售后状态(0:仅退款,1:退款退货)
                'reason' => $refundOrder['refund_reason'],  //退款原因
                'platformnumid' => $cart['product_id'], //*平台商品数字id
                'oid' => '', //*子订单编号

            ];
        }




    }



    /**
     * 订单数据转换
     * @param $orders
     */
    public static function Orders($orders) {

        $postData = [];
        $shop = config('rpcclient.services.gjp.shop');
        foreach($orders as $order)
        {
            $status = $order->getData('status');
            if($order->paid && $status == 0) {
                $tradestatus = 2;
            } else if($order->paid == 0){
                $tradestatus = 1;
            } else if($status == 1) {
                $tradestatus = 3;
            } else if($status == 2) {
                $tradestatus = 4;
            } else if($status == -1 || $status == -2) {
                $tradestatus = 5;
            }

            $newData = [
                'tradeid' => $shop.$order->id,            //订单号
//                'payno' => '',              //支付单号
                'tradestatus' => $tradestatus,         //交易状态（交易状态 -1=全部,1= 未付款订单，2=已付款订单，3=已发货订单，4=交易成功订单，5=已关闭订单，6=部分发货）
                'buyermessage' => static::removeFh($order->mark),       //买家备注
                'tradecreatetime' => date('Y-m-d H:i:s', $order->add_time),    //交易创建时间
                'tradetype' => 0,           //（0=普通，1=预售，2=征集，3=货到付款）
                'refundstatus' => 0,        //售后状态（0=正常，1=退款中，2=退款成功）
                'tradetotal' => $order->total_price,         //订单总金额
                'total' => $order->pay_price,              //订单实付金额
                'preferentialtotal' => 0,   //订单优惠金额,
                'orderdetails' => [],       //订单明细,
                'eshopbuyer' => [],          //买家信息
            ];


            $cartInfo = $order->cartInfo;
            $orderdetails = [];
            foreach($cartInfo as $c) {


                $productInfo = $c['cart_info']['productInfo'];
                $hasWh = strpos($productInfo['image'], '?');
                $picurl = $hasWh ? substr($productInfo['image'], 0, $hasWh) : $productInfo['image'];

                $product_id = $c['product_id'];
                $orderdetailData = [
                    'ptypeid'                => $product_id,    //商品数字ID
                    'outid'                  => $c['cart_info']['product_attr_unique'],
                    'productname'            => $productInfo['store_name'],    //商品名称,
                    'oid'                    => $c['id'],        //子订单编号（商品明细的索引，需保证明细的唯一性）
                    'picurl'                 => $picurl,     //图片地址
                    'skuid'                  => $skuId = substr(md5($productInfo['attrInfo']['suk']), 0 ,12),      //Sku数字id
                    'platformpropertiesname' => static::removeKh($productInfo['attrInfo']['suk']), //Sku属性名称
//                    'platformpropertiesname' => $productInfo['attrInfo']['suk'], //Sku属性名称
                    'tradeoriginalprice'     => $c['cart_info']['cart_num'] * $c['cart_info']['truePrice'],
                    'preferentialtotal'      => 0,
                    'qty'                    => $c['cart_info']['cart_num'],
                    'refundstatus'           => 0,   //售后状态（0=正常,1=卖家同意,2=退款成功）
                ];

                $orderdetails[] = $orderdetailData;
            }


            $newData['orderdetails'] = $orderdetails;

            $pcd = explode(' ', $order['user_address']);

            $province = array_shift($pcd);
            $city = array_shift($pcd);
            $district = array_shift($pcd);
            $address = implode($pcd);
            $eshopbuyer = [
                'customerreceiver' => static::removeFh($order->real_name),       //收件人姓名
                'customerreceivermobile' => $order->user_phone, //买家手机号
                'customerreceivercountry' => '中国',    //国家
                'customerreceiverprovince' => $province,   //省
                'customerreceivercity' => $city, //市
                'customerreceiverdistrict' => $district, //区
                'customerreceiveraddress' =>  str_replace(['&'], '', $address)    //详细地址,
            ];

            $newData['eshopbuyer'] = $eshopbuyer;

            $postData[] = $newData;
        }


        return $postData;
    }
}

