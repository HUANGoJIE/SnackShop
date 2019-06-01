<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/19
 * Time: 13:28
 */

namespace app\api\controller\v1;
use app\api\model\Banner as BannerModel;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\BannerMissException;

class Banner
{
    /**
     * 获取指定id的banner信息
     * @url /banner/:id
     * @http GET
     * @id banner的id号
     */
    public function getBanner($id){
        //AOP 面向切面编程
        (new IDMustBePositiveInt())->batch()->goCheck();        //验证
        $banner = BannerModel::getBannerById($id);              //获取Banner详细信息

        if (!$banner){                                          //为空抛异常
            throw new BannerMissException();
        }
        return $banner;
    }
}