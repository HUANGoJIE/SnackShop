<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/24
 * Time: 19:09
 */

namespace app\api\model;


class User extends BaseModel
{
    public function address(){
        return $this->hasOne('UserAddress', 'user_id', 'id');       //User中无外键，外键在UserAddress中，用hasOne
    }

    public static function getByOpenID($openid){
        $user = self::where('openid', '=', $openid)->find();
        return $user;
    }
}