<?php
namespace Apiapp\Controller;

class ArticleController
{

    //资讯首页
    public function index(){
        
        $time=time();
        $openid=I('openid');
        $nickname=I('nickname');
        $pic=I('pic');
        $wheren['openid']=$openid;
        $datam['nickname']=$nickname;
        $datam['pic']=$pic;
        $member=D('Member')->binding($wheren,$datam);
        $map['openid']=I('openid');
        $map['authentication']=array('egt',1);
        $wherev['starttime']=array('elt',$time);
        $wherev['endtime']=array('egt',$time);
        $Article = D('Article');
        $rid=D('Member')->getMemberInfo($map);
       
        if(!empty($rid)){
            $wherev['residential_id']=$rid[0]['residential_id'];
            $wherev['vote']=1;
            $arr =$Article->index($wherev);
            if(empty($arr)){
                $where['residential_id']=$rid[0]['residential_id'];
                $where['vote']=0;
                $arr =$Article->index($where);
                $arrt=$arr;
            }
            
            if(empty($arrt)&&empty($arr)){
                 $wheres['sortid']=7;
            $length=1;
            $arr=$Article->home($wheres,$length);
            }
        }else{
            $where['sortid']=7;
            $length=1;
            $arr=$Article->home($where,$length);
        }
        $time=$arr[0]['time'];
        $arr[0]['time']=date('Y-m-d', $time);
        $username['username']=$arr[0]['author'];
      
        $user=$Article->getUser($username);
        $arr[0]['upic']=$user[0]['pic'];
        $money=$this->getPaymentMoney($openid);
        if($money>0){
            $arr[0]['money']=$money;
        }else{
            $arr[0]['money']=0;
        }
        echo jsonShow(200,'获取资讯成功',$arr);
        exit;
    }
   
    //资讯列表
    public function lists(){
        $where['vote']=0;
        $where['sortid']=I('type');
        $Article = D('Article');
        $length=10;
        $arr=$Article->home($where,$length);
        foreach ($arr as &$value){
            $value['time']=str_replace($value['time'],$value['time'],date('Y-m-d', $value['time']));
        }
        echo jsonShow(200,'获取资讯列表成功',$arr);
        exit;
    }

    //咨询列表，获取最新四条咨询
    public function listsWithFour(){

        //获取当前用户所属小区数据
        $map['openid']=I('openid');
        $Article = D('Article');
        $rid=D('Member')->getMemberInfo($map);

        if(!empty($rid)){
            if (in_array($rid[0]['residential_id'], [13, 14,15, 16])) {
                $r_id = 16;
            } else {
                $r_id = $rid[0]['residential_id'];
            }
            $wherev['residential_id']=$r_id;
            // $wherev['vote']=1;
            $arr =$Article->home($wherev);
            if(empty($arr)){
                $where['residential_id']=$r_id;
                $where['vote']=0;
                $arr =$Article->home($where);
                $arrt=$arr;
            }

            if(empty($arrt)&&empty($arr)){
                $wheres['sortid']=7;
                $length=4;
                $arr=$Article->home($wheres,$length);
            }
        }else{
            $where['sortid']=7;
            $length=4;
            $arr=$Article->home($where,$length);
        }

        foreach ($arr as &$value){
            $value['time']=str_replace($value['time'],$value['time'],date('Y-m-d', $value['time']));
        }

        echo jsonShow(200,'获取资讯列表成功',$arr);
        exit;
    }

    //资讯详情
    public function info(){
        $id=I("id");
        $Article = D('Article');
        $arr =$Article->info($id);
        $time=$arr[0]['time'];
        $arr[0]['time']=date('Y-m-d', $time);
        $content=$arr[0]['content'];
        $arr[0]['content']=getConent($content);
        echo jsonShow(200,'获取资讯详情成功',$arr);
        exit;
    }

    /**
     * APISTORE 获取数据
     * @param $url 请求地址
     * @param null $params 请求的数据
     * @param $appCode 您的APPCODE
     * @return mixed
     */
    public function APISTORE($url, $params = null, $appCode)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url . '?' . http_build_query($params));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array (
            'Authorization:APPCODE '.$appCode
        ));
        //如果是https协议
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            //CURL_SSLVERSION_TLSv1
            curl_setopt($curl, CURLOPT_SSLVERSION, 1);
        }
        //超时时间
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //返回内容
        $callbcak = curl_exec($curl);
        //关闭,释放资源
        curl_close($curl);
        //返回内容JSON_DECODE
        return json_decode($callbcak, true);
    }


    public function realnameAuthentication($cardNo,$realName){
        //接口地址
        $url = 'http://1.api.apistore.cn/idcard';
        //请正确填写appcode,如果填写错误阿里云会返回错误
        //appcode查看地址 https://market.console.aliyun.com/imageconsole/
        $appCode = '82fe3fc9914644ed9f7852b11953062c';
        //身份证号码
        $params['cardNo']=$cardNo;
        //身份证姓名
        $params['realName']=$realName;

        //发送远程请求;
        $result = $this->APISTORE($url, $params, $appCode);

        if ($result['error_code'] == 0 && $result['result']['isok']==1) {
            //恭喜您,您的身份证姓名和号码一致
            return true;
        } else {
            //验证失败, 信息不一致
            return false;
        }
    }

    //业主实名判断
    public function bhudsichsdu(){
        $mem = M('Member');
        $whe['authentication'] = "2";
        $mrow = $mem->where($whe)->select();
        $rarr = array();
        foreach ($mrow as $k => $v){
            $result = $this->realnameAuthentication($v['idcard'],$v['name']);
            if(!$result){
                $rarr[] = $v['id'];
            }
        }
        var_dump($rarr);
    }

    /**
     * 业主认证
     */
    public function memberAuthentication(){
        $form_id = I('form_id');
        $number=I('number');
        $openid=I('openid');
        $mphone=I('mphone');
        $vphone=I('vphone');
        $type=I('type');
        $name=I('name');
        $floor=I('floor');
        $household=I('household');
        $residential_name=I('rid');
        $idcard=I('idcard');
        if(empty($mphone) || empty($name) || empty($household) || empty($floor) || empty($residential_name) || empty($idcard)){
            echo jsonShow(401,'业主认证失败','信息填写不完整');
            exit();
        }

        $result = $this->realnameAuthentication($idcard,$name);
        if(!$result){
            echo jsonShow(402,'业主认证失败','实名认证不通过');
            exit();
        }

//        //单个业主判断
//        $mone['floor'] = $floor;
//        $mone['household'] = $household;
//        $mem = M('Member');
//        $rela = M('Relation');
//        $mrow = $mem->where($mone)->select();
//        foreach ($mrow as $k => $v){
//            $rela['main_id'] = $v['id'];
//            $rrow = $rela->where($rela)->select();
//            if($rrow){
//                echo jsonShow(403,'业主认证失败','该楼户业主已被认证');
//                exit();
//            }
//        }

        if($residential_name=='浅水湾畔'){
            $residential_id=7;
        }
        if($residential_name=='大洋山庄'){
            $residential_id=11;
        }
        if($residential_name=='尚品国际'){
            $residential_id=12;
        }
        if($residential_name=='文园雅阁1栋'){
            $residential_id=13;
        }
        if($residential_name=='文园雅阁2栋'){
            $residential_id=14;
        }
        if($residential_name=='文园雅阁3栋'){
            $residential_id=15;
        }
        if($residential_name=='文园雅阁4栋'){
            $residential_id=16;
        }
        if($type==0){
            $kphone=$mphone.$vphone;
            $data['form_id']=$form_id;
            $data['payment']=0;
            $data['phone']=$mphone;
            $data['name']=$name;
            $data['floor']=$floor;
            $data['household']=$household;
            $data['residential_id']=$residential_id;
            $data['idcard']=$idcard;
            $data['status']   =   1;
            $data['authentication']   =   1;
            $data['mtype']   =   0;

            $mnumber=S($kphone);
            if($number != $mnumber || empty($number)){
                echo jsonShow(401,'验证码错误',$number);
                exit();
            }

            $map['openid']=I('openid');
            $memberInfo=D('Member')->getMemberInfo($map);
            if(!empty($memberInfo)){
                $mid['vice_id']=$memberInfo[0]['id'];
                $status['status']=-1;
                $res=D('Member')->changStatus($mid,$status);
            }
            $member = D('Member')->binding($map,$data);
            if($member){
                echo jsonShow(200,'业主认证成功',$member);
            } else {
                echo jsonShow(200,'业主认证成功',$member);
            }
        }
        if($type==1){
            $kphone=$mphone.$vphone;
            $data['form_id']=$form_id;
            $data['payment']=0;
            $data['phone']=$vphone;
            $data['name']=$name;
            $data['floor']=$floor;
            $data['household']=$household;
            $data['residential_id']=$residential_id;
            $data['idcard']=$idcard;
            $data['status'] = 1;
            $data['authentication']=2;
            $data['mtype']   =   1;

            $mnumber=S($kphone);
            if($number != $mnumber || empty($number)){
                echo jsonShow(401,'验证码错误',$number);
                exit();
            }  
            $map['phone']=$mphone;
            $map['authentication']=2;
            $member=D('Member')->getMemberInfo($map);
            if(!empty($member)){//判断member是否存在
                $where['openid']=$openid;
                $mid=D('Member')->binding($where,$data);
                $memberInfo=D('Member')->getMemberInfo($where);
                $memberid=$member[0]['id'];
                $viceid=$memberInfo[0]['id'];
                $relation['main_id']=$memberid;
                $relation['vice_id']=$viceid;
                $status['type']=$type;
                $status['status']=1;
                $relationInfo=D('Member')->searchTenant($relation);
                if(!empty($relationInfo)){

                    $res=D('Member')->changStatus($relation,$status);
                    echo jsonShow(200,'租客认证成功',$res);
                    exit();
                }
                $relation['status']=1;
                $relation['type']=$type;
                $rid=M('Relation')->add($relation);//关系表添加一条数据

                if(empty($rid)){
                    echo jsonShow(401,'租客认证失败',$rid);
                }else{
                    echo jsonShow(200,'租客认证成功',$rid);
                }
            }else{
                echo jsonShow(401,'该业主未认证',$member);
            }
        }
        if($type==2){
            
            $kphone=$mphone.$vphone;
            $data['form_id']=$form_id;
            $data['payment']=0;
            $data['phone']=$vphone;
            $data['name']=$name;
            $data['floor']=$floor;
            $data['household']=$household;
            $data['residential_id']=$residential_id;
            $data['idcard']=$idcard;
            $data['status']   =   1;
            $data['authentication']=2;
            $data['mtype']   =   2;

            $mnumber=S($kphone);
            if($number != $mnumber || empty($number)){
                echo jsonShow(401,'验证码错误',$number);
                exit();
            } 
            $map['phone']=$mphone;
            $map['authentication']=2;
            $member=D('Member')->getMemberInfo($map);
            if(!empty($member)){//判断member是否存在
                $where['openid']=$openid;
                $mid=D('Member')->binding($where,$data);
                $memberInfo=D('Member')->getMemberInfo($where);
                $memberid=$member[0]['id'];
                $viceid=$memberInfo[0]['id'];
                $relation['main_id']=$memberid;
                $relation['vice_id']=$viceid;
                $status['type']=$type;
                $status['status']=1;
                $relationInfo=D('Member')->searchTenant($relation);
                if(!empty($relationInfo)){
                    $res=D('Member')->changStatus($relation,$status);
                    echo jsonShow(200,'家人认证成功',$res);
                    exit();
                }
                $relation['type']=$type;
                $relation['status']=1;
                $rid=M('Relation')->add($relation);//关系表添加一条数据

                if(empty($rid)){
                    echo jsonShow(401,'家人认证失败',$rid);
                }else{
                    echo jsonShow(200,'家人认证成功',$rid);
                }
            }else{
                echo jsonShow(401,'该业主未认证',$member);
            }
        }
       
    }


    //发送小程序通知-注册成功通知
    public function postMemberStatus(){
        $openid = I('openid');
        $type = I('type') == 1?"家人":"租户";
        $mem = M('Member');
        $mrow = $mem->where('openid LIKE \'%'.$openid.'%\'')->select();
        $url_acc = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxb7fd9c340ac095ef&secret=1ebd797e7414747609c29075e9308b6f";
        $acc_arr=json_decode(file_get_contents($url_acc),true);
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$acc_arr['access_token'];
        $keyword1 = "您已通过".$type."认证";
        $keyword2 = $mrow[0]['name'];
        $keyword3 = $mrow[0]['phone'];
        $keyword4 = substr($mrow[0]['idcard'],0,5)."*********";
        $keyword5 = date('Y-m-d H:i:s',$mrow[0]['reg_time']);
        $keyword6 = $mrow[0]['floor']."楼".$mrow[0]['household']."户";
        $keyword7 = "欢迎您成为掌家门户的一份子";
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
        $res = $this->postUrl($url,$data);
        echo jsonShow(200,$type."认证成功",$res);
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

    /**
     * 个人认证
     */
    public function memberRelation(){
        $phone=I('phone');
        $name=I('name');
          $floor=I('floor');
        $household=I('household');
        $resname=I('resname');
        $wname=I('wname');
        $wphone=I('wphone');
        $bankname=I('bankname');
        $bankcard=I('bankcard');
        if(empty($phone) || empty($name) || empty($address) || empty($resname)||
            empty($wname) || empty($wphone) || empty($bankname) || empty($bankcard)){
            echo jsonShow(401,'业主认证失败','信息填写不完整');
            exit();
        }
        $data['phone']=I('phone');
        $data['name']=I('name');
        $data['floor']=$floor;
            $data['household']=$household;
        $data['idcard']=I('idcard');
        $data['status']   =   1;
        $data['residential_id']   =   null;
        $data['authentication']   =   3;
        $map['openid']=I('openid');
        $memberInfo=D('Member')->getMember($map);
        if(!empty($memberInfo)){
            $mid['vice_id']=$memberInfo[0]['id'];
            $status['status']=-1;
            $res=D('Member')->changStatus($mid,$status);
        }
        $member = D('Member')->binding($map,$data);
            $where['member_id']=$memberInfo[0]['id'];
            $datas['wphone']=$wphone;
            $datas['wname']=$wname;
            $datas['residential_name']=$resname;
            $datas['status']=1;
            $datas['member_id']=$memberInfo[0]['id'];
            $datas['bankname']=$bankname;
            $datas['bankcard']=$bankcard;
            $memberRelation=D('Member')->getMemberRelation($where);
            if(empty($memberRelation)){
                $addMemberRelation=D('Member')->addMemberRelation($datas);
                echo jsonShow(200,'业主认证成功',$member);
            }else{
                $updateMemberRelation=D('Member')->updateMemberRelation($where,$datas);
                echo jsonShow(200,'业主认证成功',$member);
            }
            
        
    }
    /**
     * 绑定小区
     */
//     public function bindingRid(){
//         $data['residential_id']=I('rid');
//         $map['openid']=I('openid');
//         $member = D('Member')->binding($map,$data);
//         if($member>0){
//             echo jsonShow(200,'绑定小区成功',$member);
//         } else {
//             echo jsonShow(402,'绑定小区失败',$member);
//         }
//     }
    
    
    public function getMemberInfo(){
        $map['openid']=I('openid');
        $memberInfo=D('Member')->getMember($map);
        $auth=$memberInfo[0]['authentication'];
        if($auth==3){
            $member=D('Member')->getMemberRelation($map);
        }else{
            $member=D('Member')->getMemberInfo($map);
        }
        if($member){
            echo jsonShow(200,'获取业主详情成功',$member);
        }else{
            echo jsonShow(400,'获取业主详情失败',$member);
        }
    }
    public function geteMemberRes(){
      $where['openid']=I('openid');
        $wherem['openid']=I('openid');
        $wherem['authentication']=array('egt',1);
        $where['zj_relation.status']=1;
        $member=D('Member')->getMemberRes($where);
        if(empty($member)){
              $member=D('Member')->getMemberInfo($wherem);
              if(empty($member)){
                  $type=3;
                  echo jsonShow(200,'获取数量成功',$type);
                  exit();
              }
              $map['main_id']=$member[0]['id'];
              $map['type']=1;
            $map['status']=1;
            $count['type1']=D('Member')->getNumber($map);
            $map['type']=2;
            $count['type2']=D('Member')->getNumber($map);
            $count['type']=0;
            $count['auth'] = $member[0]['authentication'];
            echo jsonShow(200,'获取数量成功',$count);
        }else{
            $type['type']=$member[0]['type'];
            $type['auth']=$member[0]['authentication'];

            echo jsonShow(200,'获取用户类型成功',$type);
        }
       
       
    }
    public function getPaymentMoney($openid){
        $where['openid']=$openid;
        $where['zj_payment_order.status']=0;
        $data=D('Member')->getPaymentMoney($where);
        $money=$data[0]['money'];
        return $money;
    }
    public function getPaymentInfo(){
        $openid=I('openid');
        $where['openid']=I('openid');
        $thismonth = date('m');
        $thisyear = date('Y');
        $startDay = $thisyear . '-' . $thismonth . '-1';
        $endDay = $thisyear . '-' . $thismonth . '-' . date('t', strtotime($startDay));
        $b_time  = strtotime($startDay);//当前月的月初时间戳
        $e_time  = strtotime($endDay);//当前月的月末时间戳
        $where['zj_payment_order.time']=array(array('egt',$b_time),array('elt',$e_time),'and');
        $data=D('Member')->getPaymentInfo($where);
        foreach ($data as $k => $v){
            if($v['payment_type']==1){
                $datas['last_monthsf']=$v['last_month'];
                $datas['this_monthsf']=$v['this_monh'];
                $datas['practicalsf']=$v['practical'];
                $datas['pricesf']=$v['price'];
                $datas['ypricesf']=$v['yprice'];
            }
            if($v['payment_type']==2){
                $datas['last_monthdf']=$v['last_month'];
                $datas['this_monthdf']=$v['this_monh'];
                $datas['practicaldf']=$v['practical'];
                $datas['pricedf']=$v['price'];
                $datas['ypricedf']=$v['yprice'];
            }
            if($v['payment_type']==3){
                $datas['ypricea']=$v['yprice'];
            }
            if($v['payment_type']==4){
                 
                $datas['ypriceb']=$v['yprice'];
            }
            if($v['payment_type']==5){
                 
                $datas['ypricec']=$v['yprice'];
            }
            if($v['payment_type']==6){
                 
                $datas['ypriced']=$v['yprice'];
            }
            if($v['payment_type']==7){
                 
                $datas['ypricee']=$v['yprice'];
            }
            if($v['payment_type']==8){
                 
                $datas['ypricef']=$v['yprice'];
            }
            if($v['payment_type']==9){
                 
                $datas['ypriceg']=$v['yprice'];
            }
            if($v['payment_type']==10){
                 
                $datas['ypriceh']=$v['yprice'];
            }
            if($v['payment_type']==11){
        
                $datas['monthi']=$v['month'];
                $datas['pricei']=$v['price'];
                $datas['ypricei']=$v['yprice'];
            }
            if($v['payment_type']==12){
                $datas['monthj']=$v['month'];
                $datas['pricej']=$v['price'];
                $datas['ypricej']=$v['yprice'];
            }
        }
        $zmoney=D('Member')->getPaymentMoney($where);
        $datas['zmoney']=$zmoney[0]['money'];
        $datas['acreage']=$data[0]['acreage'];
        $datas['name']=$data[0]['name'];
        $datas['floor']=$data[0]['floor'];
        $datas['household']=$data[0]['household'];
        echo jsonShow(200,'获取缴费金额成功',$datas);
    }
}

?>