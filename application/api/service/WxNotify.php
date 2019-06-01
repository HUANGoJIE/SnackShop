<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/31
 * Time: 13:58
 */

namespace app\api\service;
use app\api\model\Product;
use app\lib\enum\OrderStatusEnum;
use think\Db;
use think\Exception;
use think\Loader;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use think\Log;

Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');

class WxNotify extends \WxPayNotify
{
    public function NotifyProcess($data, &$msg)
    {
        if ($data['result_code'] == 'SUCCESS'){
            $orderNo = $data['out_trade_no'];
            Db::startTrans();
            try{
                $order = OrderModel::where('order_no', '=', $orderNo)->find();
                if ($order->status == 1){
                    $service = new OrderService();
                    $stockStatus = $service->checkOrderStock($order->id);
                    if ($stockStatus['pass']){
                        $this->updateOrderStatus($order->id, true);
                        $this->reduceStock($stockStatus);
                    }else{
                        $this->updateOrderStatus($order->id, false);
                    }
                }
                Db::commit();
                return true;
            }catch (Exception $e)
            {
                Db::rollback();
                Log::error($e);
                return false;
            }
        }else{
            return true;    //不真正的true，只是为了让回调方法继续执行。
        }
    }

    private function updateOrderStatus($orderID, $success){
        $status = $success?OrderStatusEnum::PAID : OrderStatusEnum::PAID_BUT_OUT_OF;
        OrderModel::where('id', '=', $orderID)->update(['status' => $status]);
    }
    private function reduceStock($stockStatus){
        foreach ($stockStatus['pStatusArray'] as $singlePStatus){
            //$singlePStatus['count']
            Product::where('id', '=', $singlePStatus['id'])->setDec('stock', $singlePStatus);
        }
    }
}