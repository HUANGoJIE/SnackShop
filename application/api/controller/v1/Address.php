<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/26
 * Time: 14:53
 */

namespace app\api\controller\v1;


use app\api\model\User as UserModel;
use app\api\service\Token as TokenService;
use app\api\validate\AddressNew;
use app\lib\exception\SuccessMessage;
use app\lib\exception\UserException;
use think\BaseController;

class Address extends BaseController
{
    protected $beforeActionList = [
        'checkPrimaryScope' =>  ['only' => 'createOrUpdateAddress']
    ];

    public function createOrUpdateAddress(){
        $validate = new AddressNew();
        $validate->goCheck();
        //1.根据Token获取uid
        //2.根据uid来查找用户数据，判断用户是否存在，如果不存在则抛出异常。
        //3.获取用户从客户端提交来的地址信息
        //4.根据用户地址信息是否存在，从而判断是添加地址还是更新地址（系统默认一个用户对应一个地址）

        $uid = TokenService::getCurrentUid();
        $user = UserModel::get($uid);
        if (!$user){
            throw new UserException();
        }

        $dataArray= $validate->getDataByRule(input('post.'));       //getDataByRule()进行参数过滤，input('post.')获取POST过来的值

        $userAddress = $user->address;

        if (!$userAddress){         //创建新的
            $user->address()->save($dataArray);
        }else{                      //修改，注意address无()
            $user->address->save($dataArray);
        }
        return json(new SuccessMessage(), 201);
    }
}