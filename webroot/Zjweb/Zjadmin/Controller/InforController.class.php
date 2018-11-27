<?php

namespace Zjadmin\Controller;
use Think\Controller;
use Think\Page;
use Think\Model;
/*
 * 信息控制器
 */
 
 
class InforController extends AdminController {
    public function infor(){
        $appid = 'wxb7fd9c340ac095ef';
        $appsecret = '97b44604171aee51cff7fdea8c5e7006';
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret
';
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);

        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $output = curl_exec($ch);
        curl_close($ch);
        $jsoninfo = json_decode($output,true);
        $access_token = $jsoninfo['access_token'];
        $expires_in = $jsoninfo['expires_in'];
        console.log($access_token);
        console.log($expires_in);

        https://api.weixin.qq.com/datacube/getusersummary?access_token=$access_token;

        $this->display();
    }


}

?>