<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/24
 * Time: 18:58
 */

namespace app\api\validate;


class TokenGet extends BaseValidate
{
    protected $rule = [
        'code'  =>  'require|isNotEmpty'
    ];
   protected $message = [
       'code'   =>  '没有Code还想获取Token，做梦哦。'
   ];
}