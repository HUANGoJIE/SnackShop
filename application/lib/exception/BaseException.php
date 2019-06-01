<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/21
 * Time: 20:26
 */

namespace app\lib\exception;


use think\Exception;
use Throwable;

class BaseException extends Exception
{
    //HTTP 状态码 400,200...
    public $code = 400;

    //错误具体信息
    public $msg = '参数错误';

    //自定义错误码
    public $errorCode = 10000;

    public function __construct($param = [])
    {
       if ( !is_array($param)){
           return ;
//           throw new Exception('参数必须是数组');
       }
       if (array_key_exists('code', $param)){
           $this->code = $param['code'];
       }
        if (array_key_exists('msg', $param)){
            $this->msg = $param['msg'];
        }
        if (array_key_exists('errorCode', $param)){
            $this->errorCode = $param['errorCode'];
        }
    }
}