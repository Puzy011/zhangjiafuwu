<?php
namespace Apiapp\Controller;

class DeviceController
{

    /**
     * 生成业主二维码
     */
    public function addOwnerQrCode(){
        $token='1494298012345';
        $signature='813c8589-91a5-495e-9cab-b517b320f483';
        $openid=I('openid');
         $where['openid']=$openid;
         $where['authentication']=2;
         $member=D('Member')->getMemberInfo($where);
         if(empty($member)){
             echo jsonShow(401,'暂无开门权限,请先进行认证',$member);
             exit();
         }
        $map['residential_id']=$member[0]['residential_id'];
        $device=D('Device')->selectDevice($map);
        $deviceId=$device[0]['device_id'];
        $deviceSn=$device[0]['device_code'];
        $data['requestParam']['lingLingId']=$member[0]['lingling_id'];
        $data['requestParam']['sdkKeys'][0]=$this->makeSdkKey($deviceId,$signature,$token);
//        $data['requestParam']['startTime']=time();
        $data['requestParam']['endTime']=1;
//        $data['requestParam']['effecNumber']=1;
        $data['requestParam']['strKey']='2017ABCD';
        $data['header']['signature']=$signature;
        $data['header']['token']=$token;
        $dataJson=json_encode($data);
        $data = array('MESSAGE'=> $dataJson);
        //发送请求
        $result=send_post('http://120.24.172.108:8889/cgi-bin/qrcode/addOwnerQrCode/F144BAEEFB10560B8A1B8D43FFEAF7D1', $data);
        $res=json_decode($result,true);
        $code=$res['responseResult']['qrcodeKey'];
        $pic=$this->getQrcode($code,$deviceSn);
        echo $pic;
    }
    /**
     * 远程开门
     */
    public function remoteOpenDoor(){
        $token='1494298012345';
        $signature='813c8589-91a5-495e-9cab-b517b320f483';
        $openid=I('openid');
        $where['openid']=$openid;
        $where['authentication']=2;
        $member=D('Member')->getMemberInfo($where);
        file_put_contents('member.txt',json_encode($member));
        if(empty($member)){
            echo jsonShow(401,'暂无开门权限,请先进行认证',$member);
            exit();
        }
        $map['residential_id']=$member[0]['residential_id'];
        $device=D('Device')->selectDevice($map);
        file_put_contents(__LINE__.'.txt',json_encode($device));
        $deviceId=$device[0]['device_id'];
        $sdk=$this->makeSdkKey($deviceId,$signature,$token);
        $data['requestParam']['sdkKey']=$sdk;
        $data['header']['signature']=$signature;
        $data['header']['token']=$token;
        $dataJson=json_encode($data);
        $data = array('MESSAGE'=> $dataJson);
        file_put_contents('test.txt',json_encode($data));
        //发送请求
        $result=send_post('http://120.24.172.108:8889/cgi-bin/key/remoteOpenDoor/F144BAEEFB10560B8A1B8D43FFEAF7D1', $data);
        echo $result;
    }
    /**
     * 生成开门密钥
     */
    protected function makeSdkKey($deviceId,$signature,$token){
        $keyEffecDay='365';
        $data['requestParam']['deviceIds'][0]=$deviceId;
        $data['requestParam']['keyEffecDay']=$keyEffecDay;
        $data['header']['signature']=$signature;
        $data['header']['token']=$token;
        $dataJson=json_encode($data);
        $data = array('MESSAGE'=> $dataJson);
        file_put_contents(__LINE__.'.txt',json_encode($data));
        //发送请求
        $result=send_post('http://120.24.172.108:8889/cgi-bin/key/makeSdkKey/F144BAEEFB10560B8A1B8D43FFEAF7D1', $data);
        $res=json_decode($result,true);
        $skd=$res['responseResult'][$deviceId];
        return $skd;
    }
 
    /**
     * 获取二维码
     */
    public function getQrcode($code,$deviceSn){
        //获取二维码
        vendor("Qrcode.phpqrcode");
        $data =$code;
        $level = 'L';
        $size =5;
        $url = "/zj/webroot/Doorimg/".$deviceSn.".png";
        $QRcode = new \QRcode();
        ob_start();
        $png = $QRcode->png($data,$url,$level,$size,2);
        $pic="www.zhangjiamenhu.com/Doorimg/".$deviceSn.".png";
        return  $pic;
    }
}

?>