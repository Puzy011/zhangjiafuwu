<?php
namespace Apiapp\Controller;
use Think\Controller;
use Apiapp\Wxpay\WxPayApi;
use Apiapp\Wxpay\WxPayConfig;
use Apiapp\Wxpay\WxPayUnifiedOrder;
class WxpayController extends Controller {
     
	 //空操作
	 public function Index(){

	}
	
	//支付方式
	public function Pay($orderid,$openid,$price,$paytype){

	    /*
        $orderid=I('orderid');  //订单号
		$money=I("money");  //金额
        $openid=I("openid");  //openid
	    */
	    //设置参数
        $orderid = $orderid;
        $money = $price;
        $openid = $openid;
		$money=number_format($money,2,".","")*100;  //转换成小数2位
        //组织参数
        $input = new WxPayUnifiedOrder();
        $input->SetBody('掌家付款');//商品描述
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
        $paydata['prepay_id'] = $result['prepay_id'];

        return $paydata;
	
	}

		  
}



?>