<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/19
 * Time: 13:28
 */

namespace app\api\controller\v2;

class Banner
{
    /**
     * 获取指定id的banner信息
     * @url /banner/:id
     * @http GET
     * @id banner的id号
     */
    public function getBanner($id){
        return 'This is V2 version.';
    }
}