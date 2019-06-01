<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/21
 * Time: 19:29
 */

namespace app\api\model;


class Banner extends BaseModel
{
    protected $hidden = ['delete_time', 'update_time'];

    public function items(){
        return $this->hasMany('BannerItem', 'banner_id', 'id');
    }
    //protected $table = 'banner_item';
    public static function getBannerById($id){
        $banner = self::with(['items', 'items.img'])->find($id);        //get(返回1个), find(返回1个), all, select
        return $banner;
    }
}