<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/4
 * Time: 18:16
 */

namespace Apiapp\Controller;
use Think\Controller;

use Apiapp\Wxstore\WxPayApi;
use Apiapp\Wxstore\WxPayConfig;
use Apiapp\Wxstore\WxPayUnifiedOrder;

class WxstoreController extends Controller
{
    //空操作
    public function Index(){

    }

    //支付方式
    public function Pay($orderid,$openid,$price,$paytype){
        //设置参数
        $money = $price * 100;
        //组织参数
        $input = new WxPayUnifiedOrder();
        $input->SetBody('掌家便利店小程序付款');//商品描述
        $input->SetAttach($paytype);//商品描述
        $input->SetOut_trade_no($orderid);//商户订单号
        $input->SetTotal_fee($money);//标价金额
        // $input->SetSpbill_create_ip(get_client_ip());//终端IP
        $input->SetNotify_url("https://www.zhangjiamenhu.com/wxnotify.php");//通知地址
        $input->SetTrade_type("JSAPI");//交易类型
        $input->SetOpenid($openid);//用户标识
        //调用微信接口
        $wxpayapi = new WxPayApi();
        $result = $wxpayapi->unifiedOrder($input);

        //返回参数
        $paydata['appId'] = $result['appid']; //Appid
        $paydata['nonceStr'] = $wxpayapi->getNonceStr();//32位随机数
        $paydata['package'] = "prepay_id=".$result['prepay_id'];//prepay_id=*
        $paydata['key'] = WxPayConfig::KEY;

        return $paydata;

    }

}