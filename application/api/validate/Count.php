<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/24
 * Time: 15:13
 */

namespace app\api\validate;


class Count extends BaseValidate
{
    protected $rule = [
        'count' =>  'isPositiveInteger|between:1,15'        //记住|左右不要加空格
    ];

}