<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/24
 * Time: 19:10
 */

namespace app\api\service;


use app\lib\enum\ScopeEnum;
use app\lib\exception\TokenException;
use app\lib\exception\WechatException;
use think\Exception;
use app\api\model\User as UserModel;

class UserToken extends Token
{
    protected $code;
    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxLoginUrl;

    function __construct($code)
    {
        $this->code = $code;
        $this->wxAppID = config('wx.app_id');
        $this->wxAppSecret = config('wx.app_secret');
        $this->wxLoginUrl = sprintf(config('wx.login_url'), $this->wxAppID, $this->wxAppSecret, $this->code);
    }

    public function get(){
        $result = curl_get($this->wxLoginUrl);
        $wxResult = json_decode($result, true);     //加true返回数组

        //错误判断
        if (empty($wxResult)){
            throw new Exception('获取session_key及openID时异常，微信内部错误。');
        }else{
            $loginFall = array_key_exists('errcode', $wxResult);
            if ($loginFall){
                $this->processLoginError($wxResult);
            }else{
                return $this->grantToken($wxResult);
            }
        }
    }

    private function grantToken($wxResult){
        //拿到openid
        //数据库里看一下，这个openid是不是已经存在
        //如果存在，则不处理；不存在则新增一条记录
        //生成令牌，准备缓存数据，写入缓存
        //把令牌返回到客户端中去
        //key:令牌
        //value: wxResult, uid, scope
        $openid = $wxResult['openid'];
        $user = UserModel::getByOpenID($openid);
        if ($user){
            $uid = $user->id;
        }else{
            $uid = $this->newUser($openid);
        }
        $cachedValue = $this->prepareCacheValue($wxResult, $uid);
        $token = $this->saveToCache($cachedValue);
        return $token;
    }

    private function saveToCache($cachedValue){
        $key = self::generateToken();
        $value = json_encode($cachedValue);
        $expire_in = config('setting.token_expire_in');

        $request = cache($key, $value, $expire_in); //TP5自带加入缓存
        if (!$request){
            throw new TokenException([
                'msg'   =>  '服务器缓存异常',
                'errorCode' =>  10005
            ]);
        }
        return $key;
    }

    private function prepareCacheValue($wxResult, $uid){
        $cachedValue = $wxResult;
        $cachedValue['uid'] = $uid;
        //scope=16 代表APP用户的权限数值 =32代表CMS（管理员）用户的权限数值
        $cachedValue['scope'] = ScopeEnum::User;
        return $cachedValue;
    }
    private function newUser($openid){
        $user = UserModel::create([
            'openid'    =>  $openid
        ]);
        return $user->id;
    }
    private function processLoginError($wxResult){
        throw new WechatException([
            'msg'   =>  $wxResult['errmsg'],
            'errorCode' =>  $wxResult['errcode']
        ]);
    }
}