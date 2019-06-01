<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/23
 * Time: 16:18
 */

namespace app\api\model;


class Product extends BaseModel
{
    protected $hidden = ['delete_time', 'update_time', 'main_img_id', 'pivot', 'from', 'category_id', 'create_time'];
    public function getMainImgUrlAttr($value, $data){
        return $this->prefixImgUrl($value, $data);
    }

    //关联商品详细表：product_image
    public function imgs(){
        return $this->hasMany('ProductImage', 'product_id', 'id');      //注意要加return，将关联模型结果返回回去。
    }
    //关联商品详细表：product_property
    public function properties(){
        return $this->hasMany('ProductProperty', 'product_id', 'id');   //注意要加return，将关联模型结果返回回去。
    }

    public static function getMostRecent($count){
        $products = self::limit($count)->order('create_time desc')->select();
        return $products;
    }

    public static function getProductsByCategoryID($categoryId){
        $products = self::where('category_id', '=', $categoryId)->select();
        return $products;
    }

    public static function getProductDetail($id){
        //Query 此处对order的排序是个难点
        $product = self::with([
            'imgs'  => function($query){
                $query->with(['imgUrl'])->order('order','asc');
            }
        ])
            ->with(['properties'])
            ->where('id', '=', $id)->find();
        return $product;
    }
}