<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/28
 * Time: 20:03
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\WxNotify;
use app\api\validate\IDMustBePositiveInt;
use app\api\service\Pay as PayService;

class Pay extends BaseController
{
    protected $beforeActionList = [
        'checkExclusiveScope'   =>  ['only' => 'getPreOrder']
    ];
    //请求预订单
    public function getPreOrder($id = ''){
        (new IDMustBePositiveInt())->goCheck();
        $pay = new PayService($id);
        return $pay->pay();
    }

    public function receiveNotify(){
        /*
         * 1. 检查库存量，超卖
         * 2. 更新这个订单的status状态
         * 3. 减库存
         * 4. 如果成功处理，我们返回微信成功处理的信息；否则，我们需要返回没有成功处理
         * 特点：POST, XML格式, 不会携带参数
         */

        $notify = new WxNotify();
        $notify->Handle();
    }
}