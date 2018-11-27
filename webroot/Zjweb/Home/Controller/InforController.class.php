<?php

namespace Home\Controller;
use Think\Controller;
use Think\Page;
use Think\Model;
/*
 * 信息控制器
 */
 
 
class InforController extends HomeController {
    public function infor(){
        date_default_timezone_set("PRC");
        //前一天时间
        $date = date("Y-m-d",strtotime("-1 day"));
        //前两天时间
        $olddate = date("Y-m-d",strtotime("-2 day"));
        //前三天时间
        $olddate3 = date("Y-m-d",strtotime("-3 day"));

        $appid = 'wxb7fd9c340ac095ef';
        $appsecret = '1ebd797e7414747609c29075e9308b6f';
       // $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";  
        $token = file_get_contents($url);
        $token = json_decode($token)->access_token;

        //获取累计用户数
        $postUrl = "https://api.weixin.qq.com/datacube/getweanalysisappiddailysummarytrend?access_token=$token";
        $postData = ['begin_date'=>$date,'end_date'=>$date];
        $postRet = $this->sendPost($postUrl,json_encode($postData));
        $postArr = json_decode($postRet,true)['list'][0]['visit_total'];
        //前一天的累计用户数
        $postDataOld = ['begin_date'=>$olddate,'end_date'=>$olddate];
        $postRetOld = $this->sendPost($postUrl,json_encode($postDataOld));
        $postArrOld = json_decode($postRetOld,true)['list'][0]['visit_total'];
        //获取大前天用户数
        $postData3 = ['begin_date'=>$olddate3,'end_date'=>$olddate3];
        $postRet3 = $this->sendPost($postUrl,json_encode($postData3));
        $postArr3= json_decode($postRet3,true)['list'][0]['visit_total'];

        //用户数增长量
        $peoGdp = $postArr - $postArrOld;
        $peothree = $postArrOld - $postArr3;
        $this->assign("postArr", $postArr);
        $this->assign("peoGdp", $peoGdp);
        $this->assign("peothree", $peothree);


        //访问页面数据
        $postUrlT = "https://api.weixin.qq.com/datacube/getweanalysisappidvisitpage?access_token=$token";
        $postDataT = ['begin_date'=>$date,'end_date'=>$date];
        $postRetT = $this->sendPost($postUrlT,json_encode($postDataT));
        $psotArrT = json_decode($postRetT,true);
//            var_dump($psotArrT);

        //
        $this->display("infor");
    }
     /**
     * 跨域post请求（适配文件上传）
     * @param $url 上传地址
     * @param $post_data 请求数据
     */
    protected function sendPost ($url,$post_data)
    {
        $ch = curl_init();
        curl_setopt($ch , CURLOPT_URL , $url);
        curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch , CURLOPT_POST, 1);
        curl_setopt($ch , CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }


}



?>
