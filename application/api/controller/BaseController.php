<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/26
 * Time: 21:27
 */

namespace app\api\controller;


use think\Controller;
use app\api\service\Token as TokenService;
class BaseController extends Controller
{

    protected function checkPrimaryScope(){
        TokenService::needPrimaryScope();
    }

    protected function checkExclusiveScope(){
        TokenService::needExclusiveScope();
    }
}