<?php
namespace Apiapp\Controller;
use Think\Upload;

class MaintenanceController extends BaseController
{
    /**
     * 获取小区维修清单
     */
    public function index(){
        $openid=I('openid');
        $where['openid']=$openid;
        $memberInfo=D('Member')->getMemberInfo($where);
        $memberid=$memberInfo[0]['id'];
        $map['member_id']=$memberid;
        $data=D('Maintenance')->newOrder($map);
        if($data){
            echo jsonShow(200,'获取服务最新订单成功',$data);
        }else{
            echo jsonShow(402,'获取服务最新订单失败',$data);
        }
        
    }
    /**
     * 获取小区维修列表
     */
    public function getMaintenanceList(){
        $openid=I('openid');
        $where['openid']=$openid;
        $memberInfo=D('Member')->getMemberInfo($where);
        $memberid=$memberInfo[0]['id'];
        $map['member_id']=$memberid;
        $data=D('Maintenance')->getMaintenance($map);
        echo jsonShow(200,'获取小区维修列表成功',$data);
    }


    //获取公众号关注用户列表
    public function getUserList(){
        $openid=I('openid');
        $where['openid']=$openid;
        $memberInfo=D('Member')->getMemberInfo($where);

        $openid=I('openid');
        $where['openid']=$openid;
        $memberInfo=D('Member')->getMemberInfo($where);
        $url_acc = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx41554ca553b353df&secret=e26af0b33530482bcaaff9ebc62e9fc1";
        $acc_arr=json_decode(file_get_contents($url_acc),true);

        $acc_t = $acc_arr['access_token'];
        $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=".$acc_t;
        echo $res = file_get_contents($url);
    }

    //发送小程序通知-早餐状态修改
    public function postServiceStatusWithBreak(){
        $openid=I('openid');
        $where['openid']=$openid;
        $memberInfo=D('Member')->getMemberInfo($where);

        $serviceOrder = M('ServiceOrder');
        $srow = $serviceOrder->where('id=324')->select();

        $url_acc = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxb7fd9c340ac095ef&secret=1ebd797e7414747609c29075e9308b6f";
        $acc_arr=json_decode(file_get_contents($url_acc),true);
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$acc_arr['access_token'];

        switch ($srow[0]['status']){
            case 0:
                $status = "已完成";
                break;
            case -1:
                $status = "已完成";
                break;
            case 1:
                $status = "进行中";
                break;
            default:
                $status = "进行中";
                break;
        }

        $describe = json_decode($srow[0]['describe'],true);
        $des = "";
        foreach ($describe as $k => $v){
            $des .= "【".$k."】: 名称：".$v['name']." 数量：".$v['num']." 单价：".$v['price'];
        }
        $srow[0]['describe'] = $des;

        $keyword1 = $srow[0]['order_number'];
        $keyword2 = $srow[0]['describe'];
        $keyword3 = "掌家早餐";
        $keyword4 = $status;
        $keyword5 = date('Y-m-d H:i:s',$srow[0]['time']);
        $keyword6 = $srow[0]['price'];
        $keyword7 = $srow[0]['name'];
        $keyword8 = $srow[0]['floor']."楼".$srow[0]['household']."户";
        $keyword9 = "隔日早上7：00";

        $mem = M('Member');
        $mrow = $mem->where('id='.$srow[0]['member_id'])->select();

        $arr = array(
            "touser" => $mrow[0]['openid'],
            "template_id" => "DqQfnwrTYpiQ7P8uY8EOPKv9yYGr0sua8r8GMtWNNVs",
            "page" => "pages/index/index",
            "form_id" => $mrow[0]['form_id'],
            "data" => array(
                "keyword1" => array(
                    "value" => $keyword1,
                    'color' => "#173177"
                ),
                "keyword2" => array(
                    "value" => $keyword2,
                    'color' => "#173177"
                ),
                "keyword3" => array(
                    "value" => $keyword3,
                    'color' => "#173177"
                ),
                "keyword4" => array(
                    "value" => $keyword4,
                    'color' => "#ff5027"
                ),
                "keyword5" => array(
                    "value" => $keyword5,
                    'color' => "#173177"
                ),
                "keyword6" => array(
                    "value" => $keyword6,
                    'color' => "#173177"
                ),
                "keyword7" => array(
                    "value" => $keyword7,
                    'color' => "#173177"
                ),
                "keyword8" => array(
                    "value" => $keyword8,
                    'color' => "#173177"
                ),
                "keyword9" => array(
                    "value" => $keyword9,
                    'color' => "#173177"
                )
            ),
            "emphasis_keyword" => ""
        );
        $data = json_encode($arr,true);
        echo $res = $this->postUrl($url,$data);
    }

    //发送早餐通知-成功回调接口
    //1，肚子里有料 2，五润
    public function postLeilongWudi(){
        $sell_id = I('sell_id');
        for($i=0;$i<2;$i++){
            if($i == 0){
                $touser = $sell_id == 1 ? "oxn_ZwJWS0Vibr5cQKNYcqXsMnvY" : "oxn_ZwCgw_n1PqEGkKceklsjkLS0" ;
            }else{
                $touser = "oxn_ZwDSxblvm89GytnVdv2cSYiA";
            }
            $this->postBreak($touser);
        }
        echo jsonShow(200,'早餐通知成功','早餐通知成功');
    }

    //服务通知成功-成功回调接口
    //暂定通知铭哥一次
    public function postServiceInfo(){
//        for($i=0;$i<2;$i++){
//            if($i == 0){
//                $touser = "oxn_ZwDSxblvm89GytnVdv2cSYiA";
//            }else{
//                $touser = "oxn_ZwDSxblvm89GytnVdv2cSYiA";
//            }
//            $this->postBreak($touser);
//        }
        $touser = "oxn_ZwHZqPtQI52BoOZtSFjIoQko";
        $this->postCarWash($touser);
        echo jsonShow(200,'服务通知成功','服务通知成功');
    }

    //发送洗车，家政，快递等服务通知模板
    public function postCarWash($touser){
        $sermap['id'] = I('sid');
        $sermap['status'] = "1";
        $openid=I('openid');
        $where['openid']=$openid;
        $memberInfo=D('Member')->getMemberInfo($where);

        $oid = I('oid');
        $serviceOrder = M('ServiceOrder');
        $srow = $serviceOrder->where('id='.$oid)->select();

        $url_acc = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx41554ca553b353df&secret=e26af0b33530482bcaaff9ebc62e9fc1";
        $acc_arr=json_decode(file_get_contents($url_acc),true);
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$acc_arr['access_token'];

        $service = M('service');
        $srow = $service->where($sermap)->select();
        $first = "您有一份新的".$srow[0]['title']."订单";

        $keyword1 = $srow[0]['describe'];
        $keyword2 = $srow[0]['order_number'];
        $keyword3 = $srow[0]['price']."元";
        $remark = "购买人：".$memberInfo[0]['name']." 地址：楼：".$memberInfo[0]['floor']." 户：".$memberInfo[0]['household']." 电话：".$memberInfo[0]['phone'];
        $arr = array(
            "touser" => $touser,
            "template_id" => "F1flX9cAI5VcZKzibaYSleejUHKWo-LBYwVC7GSkMh8",
            "url" => "",
            "topcolor" => "#6a8ada",
            "data" => array(
                "first" => array(
                    "value" => $first,
                    'color' => "#173177"
                ),
                "keyword1" => array(
                    "value" => $keyword1,
                    'color' => "#173177"
                ),
                "keyword2" => array(
                    "value" => $keyword2,
                    'color' => "#173177"
                ),
                "keyword3" => array(
                    "value" => $keyword3,
                    'color' => "#173177"
                ),
                "remark" => array(
                    "value" => $remark,
                    'color' => "#173177"
                )
            )
        );
        $data = json_encode($arr,true);
        echo $res=$this->postUrl($url,$data);
    }

    //发送早餐通知-成功回调接口
    //1，肚子里有料 2，五润
    public function postBreakInfo(){

        $sell_id = I('sell_id');
        for($i=0;$i<5;$i++){
            if($i == 0){
//                $touser = $sell_id == 1 ? "oxn_ZwIdIIJ1Owma6MB9TSipjlhQ" : "oxn_ZwHcVk-BmuOeniTlSQkCBf7o" ;
                switch ($sell_id){
                    case 1:
                        $touser = "oxn_ZwIdIIJ1Owma6MB9TSipjlhQ";//肚子里有料
                        break;
                    case 2:
                        $touser = "oxn_ZwHcVk-BmuOeniTlSQkCBf7o";//五润
                        break;
                    case 3:
                        $touser = "oxn_ZwMjjXWmXZfbvNpSEKaTp2d4";//市场部
                        break;
                    case 4:
                        $touser = "oxn_ZwDVLUoNyulM2XpMBYBKJsow";//晨间厨房
                        break;
                }
            }else if($i == 1){
//                $touser = "oxn_ZwHZqPtQI52BoOZtSFjIoQko";
                $touser = "oxn_ZwMjjXWmXZfbvNpSEKaTp2d4";  //市场部的
            }else if($i == 2){
                $touser = "oxn_ZwHZqPtQI52BoOZtSFjIoQko";   //老大的
            }else if($i == 3){
                $touser = "oxn_ZwBext44JXrJEBcVccO7sDAs"; //技术部
            }
            $this->postBreak($touser,$sell_id);
        }
        echo jsonShow(200,'早餐通知成功','早餐通知成功');
    }

    //发送早餐通知模板
    public function postBreak($touser,$sell_id){
        $openid=I('openid');
        $where['openid']=$openid;
        $memberInfo=D('Member')->getMemberInfo($where);

        $oid = I('oid');
        $serviceOrder = M('ServiceOrder');
        $srow = $serviceOrder->where('id='.$oid)->select();

        $url_acc = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx41554ca553b353df&secret=e26af0b33530482bcaaff9ebc62e9fc1";
        $acc_arr=json_decode(file_get_contents($url_acc),true);

        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$acc_arr['access_token'];
        switch ($sell_id){
            case 1:
                $first = "肚子里有料您有一份新的早餐订单";//肚子里有料
                break;
            case 2:
                $first = "五润您有一份新的早餐订单";//五润
                break;
            case 3:
                $first = "您有一份新的早餐订单";
                break;
            case 4:
                $first = "晨间厨房您有一份新的早餐订单";//晨间厨房
                break;
        }


        $describe = json_decode($srow[0]['describe'],true);

        $des = "";
        foreach ($describe as $k => $v){
            $des .= "【".$k."】:".$v['name']." 数量：".$v['num']." 单价：".$v['price'];
        }
        $srow[0]['describe'] = $des;

        $keyword1 = $srow[0]['describe'];
        $keyword2 = $srow[0]['order_number'];
        $keyword3 = $srow[0]['price'];
        $remark = "购买人：".$memberInfo[0]['name']." 地址：楼：".$memberInfo[0]['floor']." 户：".$memberInfo[0]['household']." 电话：".$memberInfo[0]['phone'];
        $arr = array(
            "touser" => $touser,
            "template_id" => "F1flX9cAI5VcZKzibaYSleejUHKWo-LBYwVC7GSkMh8",
            "url" => "",
            "topcolor" => "#6a8ada",
            "data" => array(
                "first" => array(
                    "value" => $first,
                    'color' => "#173177"
                ),
                "keyword1" => array(
                    "value" => $keyword1,
                    'color' => "#173177"
                ),
                "keyword2" => array(
                    "value" => $keyword2,
                    'color' => "#173177"
                ),
                "keyword3" => array(
                    "value" => $keyword3,
                    'color' => "#173177"
                ),
                "remark" => array(
                    "value" => $remark,
                    'color' => "#173177"
                )
            )
        );
        $data = json_encode($arr,true);
        echo $res=$this->postUrl($url,$data);
    }

    //用户认证通过服务通知
    public function postX(){
        $openid=I('openid');
        $where['openid']=$openid;
        $memberInfo=D('Member')->getMemberInfo($where);

        $map['id'] = $memberInfo[0]['id'];
        //发送小程序通知
        $mem = M('Member');
        $mrow = $mem->where($map)->select();
        $url_acc = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxb7fd9c340ac095ef&secret=1ebd797e7414747609c29075e9308b6f";
        $acc_arr=json_decode(file_get_contents($url_acc),true);
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$acc_arr['access_token'];
        $keyword1 = "您已通过用户认证";
        $keyword2 = $mrow[0]['name'];
        $keyword3 = $mrow[0]['phone'];
        $keyword4 = $mrow[0]['idcard'];
        $keyword5 = $mrow[0]['regtime'];
        $keyword6 = $mrow[0]['floor']."楼".$mrow[0]['household']."户";
        $keyword7 = "恭喜您成为我们的一份子";
        $arr = array(
            "touser" => $mrow[0]['openid'],
            "template_id" => "7i-Eet3jI6e7Wi3k-1hIf-cyRjj1YmIwkKW_erqQSp4",
            "page" => "pages/index/index",
            "form_id" => $mrow[0]['form_id'],
            "data" => array(
                "keyword1" => array(
                    "value" => $keyword1,
                    'color' => "#173177"
                ),
                "keyword2" => array(
                    "value" => $keyword2,
                    'color' => "#173177"
                ),
                "keyword3" => array(
                    "value" => $keyword3,
                    'color' => "#173177"
                ),
                "keyword4" => array(
                    "value" => $keyword4,
                    'color' => "#173177"
                ),
                "keyword5" => array(
                    "value" => $keyword5,
                    'color' => "#173177"
                ),
                "keyword6" => array(
                    "value" => $keyword6,
                    'color' => "#173177"
                ),
                "keyword7" => array(
                    "value" => $keyword7,
                    'color' => "#173177"
                ),
            ),
            "emphasis_keyword" => ""
        );
        $data = json_encode($arr,true);
        echo $res = $this->postUrl($url,$data);

    }

    public function postTemp($first,$performance,$remark,$touser){
//        "oxn_ZwHZqPtQI52BoOZtSFjIoQko"
        $openid=I('openid');
        $where['openid']=$openid;
        $memberInfo=D('Member')->getMemberInfo($where);

        $openid=I('openid');
        $where['openid']=$openid;
        $memberInfo=D('Member')->getMemberInfo($where);
        $url_acc = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx41554ca553b353df&secret=e26af0b33530482bcaaff9ebc62e9fc1";
        $acc_arr=json_decode(file_get_contents($url_acc),true);

        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$acc_arr['access_token'];
        $arr = array(
            "touser" => $touser,
            "template_id" => "MDBwwpIYAGx-cDk1WGLVII8ucv-7NMubzm9hR0SCIwk",
            "url" => "",
            "topcolor" => "#6a8ada",
            "data" => array(
                "first" => array(
                    "value" => $first,
                    'color' => "#173177"
                ),
                "performance" => array(
                    "value" => $performance,
                    'color' => "#173177"
                ),
                "time" => array(
                    "value" => date("Y-m-d H:i:s"),
                    'color' => "#173177"
                ),
                "remark" => array(
                    "value" => $remark,
                    'color' => "#173177"
                )
            )
        );
        $data = json_encode($arr,true);
        return $res=$this->postUrl($url,$data);
    }
  /**
     * 提交小区维修定单
     */
    public function getMaintenance(){
        $form_id=I('form_id');
        $openid=I('openid');
        $type=I('type');
        $where['openid']=$openid;
        $memberInfo=D('Member')->getMemberInfo($where);
        $memberid=$memberInfo[0]['id'];
        $data['member_id']=$memberid;
        $data['describe']=I('describe');
        $data['pic']=$this->uploadPic();
        $data['status']=0;
        $data['prices']=0;
        $data['time']=time();
        $data['type']=$type;
        $data['form_id']=$form_id;
        $data['order_number']='ZJ'.$type.date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $ref=D('Maintenance')->addMaintenance($data);

        //插入邻里圈
        $m = M('Member');
        $mr= $m->where('openid LIKE \''.$openid.'\'')->select();

//        //修改用户form_id
//        $datam['form_id'] = $form_id;
//        $m->where('openid LIKE \''.$openid.'\'')->save($datam);

        $neighborhood = M('neighborhood');
        $datan['rid']=7;
        $datan['content']=base64_encode(I('describe'));
        $datan['pic']=$data['pic'];
        $datan['time']=time();
        $datan['status']=1;
        $datan['member_id']=$memberid;
        $datan['mpic']=$mr[0]['pic'];
        $datan['nickname']=base64_encode($mr[0]['name']);
        $datan['number']=0;
        $datan['click']=0;
        $datan['type']=2;
        $datan['service_status']=0;
        $datan['main_id']=$ref;
        $ren = $neighborhood->add($datan);

        $first = "有新的故障保修啦！请速去查看";
        $performance = I('describe');
        $remark = "业主电话：".$memberInfo[0]['phone'];


        $touser = "oxn_ZwMjjXWmXZfbvNpSEKaTp2d4";
        $this->postTemp($first,$performance,$remark,$touser);

        $touser = "oxn_ZwFgmoIz-1sgkKTrLpJbwB3Y";
        $res = $this->postTemp($first,$performance,$remark,$touser);

        if($ref>0 && $ren ==$ren>0 && $res){
            echo jsonShow(200,'提交小区维修定单成功',$ref);
        }else{
            echo jsonShow(402,'提交小区维修定单失败',$ref);
        }
    }
    public function uploadPic(){
        /* 返回标准数据 */
        $picconfig=C('PICTURE_UPLOAD');
        $Upload = new Upload($picconfig);
        $info   = $Upload->upload($_FILES);
        if($info){ //文件上传成功，记录文件信息
            foreach ($info as $key => &$value) {
                /* 记录文件信息 */
                $value['path'] = "upload/".$value['savepath'].$value['savename'];	//在模板里的url路径
            }
        }
        $data=$info['photo']['path'];
        return $data;
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

?>