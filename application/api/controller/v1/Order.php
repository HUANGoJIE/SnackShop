<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/26
 * Time: 20:48
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\OrderPlace;
use app\api\service\Token as TokenService;
use app\api\service\Order as OrderService;
use app\api\validate\PagingParameter;
use app\api\service\Token;
use app\api\model\Order as OrderModel;
use app\lib\exception\OrderException;

class Order extends BaseController
{
    /* #订单流程（难点重点）
     * 1.用户在选择商品后，向API提交包含它所选商品的相关信息
     * 2.API在接收到信息后，需要检查订单相关商品的库存量（1）
     * 3.有库存，把订单数据存入数据库中，下单成功了，返回客户端信息，告诉客户端可以支付了
     * 4.调用支付接口进行支付
     * 5.还需要再次进行库存量检测（2）
     * 6.服务器这边就可以调用微信的支付窗口进行支付
     * 7.微信会返回给我们一个支付的结果[异步调用]
     * 8.成功，也要进行库存量的检测（3）
     * 9.成功：进行库存的扣除
     */

    protected $beforeActionList = [
        'checkExclusiveScope' =>  ['only' => 'placeOrder'],
        'checkPrimaryScope'   =>  ['only' => 'getDetail, getSummaryByUser'],

    ];

    public function  getSummaryByUser($page=1, $size=15){
        (new PagingParameter())->goCheck();
        $uid = Token::getCurrentUid();
        $pagingOrders = OrderModel::getSummaryByUser($uid, $page, $size);
        if ($pagingOrders->isEmpty()){
            return [
                'data' => [],
                'current_page' => $pagingOrders->getCurrentPage(),
            ];
        }else{
            $data = $pagingOrders->hidden(['snap_items', 'snap_address', 'prepay_id'])->toArray();
            return [
                'data' => $data,
                'current_page' => $pagingOrders->getCurrentPage(),
            ];
        }
    }

    public function getDetail($id){
        (new IDMustBePositiveInt())->goCheck();
        $orderDetail = OrderModel::get($id);
        if (!$orderDetail){
            throw new OrderException();
        }else{
            return $orderDetail->hidden(['prepay_id']);
        }
    }

    public function placeOrder(){
        (new OrderPlace())->goCheck();
        $products = input('post.products/a');       //直接写post.products获取不到数组，要写post.products/a才行。
        $uid = TokenService::getCurrentUid();

        $order = new OrderService();
        $status = $order->place($uid, $products);
        return $status;
    }
}