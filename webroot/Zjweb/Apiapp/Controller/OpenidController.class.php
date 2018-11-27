<?php
namespace Apiapp\Controller;
use Think\Controller;

class OpenidController {
        /*
         * 获取OPENID
         */
    public function getopenid(){

        $code=I("code");
        if($code)
        {
            $url="https://api.weixin.qq.com/sns/jscode2session";
            $data['appid']="wxb7fd9c340ac095ef";
            $data['secret']="1ebd797e7414747609c29075e9308b6f";
            $data['js_code']=$code;
            $data['grant_type']="authorization_code";
            //获取openid
            $res=$this->postRequest($url,$data);
            $res=json_decode($res,true);
        } 
        $where['openid']=$res['openid'];
        $openid=$res['openid'];
        $map['openid']=$res['openid'];
        $map['reg_time']=time();
        $map['login_time']=time();
        $update['login_time']=time();
        $update['login_ip']=get_client_ip();
        $map['login_ip']=get_client_ip();
        $map['reg_ip']=get_client_ip();
        $map['status']=0;
        $map['authentication']=0;
        $map['lingling_id']=$this->getLingLingId();
        if(!empty($openid)){
            $member=D('Member')->add($where,$map,$update);
        }
        echo jsonShow(200,'添加业主成功',$res);
        exit;
    }
    /**
     * 获取令令ID
     */
    protected function getLingLingId(){
        //请求  JSON 数据
        $dataJson = '{
		"requestParam":{
    
		},
		"header":{
			"signature":"813c8589-91a5-495e-9cab-b517b320f483",
			"token":"1494298012345"
		}
	    }';
        $data = array('MESSAGE'=> $dataJson);
        //发送请求
        $result=send_post('http://120.24.172.108:8889/cgi-bin/qrcode/getLingLingId/F144BAEEFB10560B8A1B8D43FFEAF7D1', $data);
        $res=json_decode($result,true);
        $ling=$res['responseResult']['lingLingId'];
        return $ling;
    }
    /**
     * post提交
     * @param $url
     * @param $data  array
     */
    protected function postRequest($url,$data){
        $postStr = '';
        foreach($data as $k => $v){
            $postStr .= $k."=".$v."&";
        }
        $postStr=substr($postStr,0,-1);
        return $this->postUrl($url,$postStr);
    }
    /**
     * post提交
     * @param $url
     * @param $data Str
     */
    protected function postUrl($url,$data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_URL,$url);
        //为了支持cookie
        //curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        //返回结果
        //拒绝验证ca证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close ($ch);
        return $result;
    }

}