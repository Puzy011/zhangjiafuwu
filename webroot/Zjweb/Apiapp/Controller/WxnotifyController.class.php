<?php
namespace Apiapp\Controller;
use Think\Controller;
use Apiapp\Wxpay\Notify;
use Apiapp\Wxpay\WxPayResults;

class WxnotifyController extends Controller
{

    public function Index()
    {

    }


    //微支付返回
    public function notify_url()
    {

        logResult("11111");
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];

        $result = WxPayResults::Init($xml);

        $prestr = createLinkstring($result);
        //商户订单号
        $out_trade_no = $result['out_trade_no'];
        //微信金额
        $total_fee = $result['total_fee'];
        //微信支付订单
        $trade_no = $result['transaction_id'];


        $wxNotify = new Notify();

        $wxNotify->Handle(false);

        $verify_result = $wxNotify->GetReturn_code();
        logResult("222222");
        if ($verify_result == "SUCCESS") {//验证成功
            logResult("33333333");
            logResult($result);
            $type=$result['body'];
            if($type=='掌家服务'){
                logResult("44444");
                $data=$result['detail'];
                logResult($data);
                eval("\$data = $data;");
                logResult($data);
                $data['time']=time();
                $data['status']=0;
                $res = D('ServiceOrder')->addServiceOrder($data);
                echo jsonShow(200,'支付成功',$res);
                exit;
            }
            

        } else {


        }


    }
}


?>