<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/26
 * Time: 14:55
 */

namespace app\api\validate;


class AddressNew extends BaseValidate
{
    protected $rule = [
        'name'       =>  'require|isNotEmpty',
        'mobile'     =>  'require|isMobile',
        'province'   =>  'require|isNotEmpty',
        'city'       =>  'require|isNotEmpty',
        'detail'     =>  'require|isNotEmpty',
    ];
}