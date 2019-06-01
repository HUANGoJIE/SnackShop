<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/19
 * Time: 22:21
 */

namespace app\api\validate;


class IDMustBePositiveInt extends BaseValidate
{
    protected $rule = [
        'id'    =>  'require|isPositiveInteger',
    ];

    protected $message=[
        'id'    =>  '必须是正整数'
    ];
}