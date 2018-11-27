<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/27
 * Time: 10:13
 */

namespace Apiapp\Controller;
use Think\Upload;

class RfidController
{
    //接收RFID设备传来的数据
    public function addRfidInto(){
        header('Access-Control-Allow-Origin:*');
        $sid = $_GET['sid'];
        $did = $_GET['did'];
        $rf_mess = M('RfidOrd');
        //查询某设备最新一个待支付的订单
        $crow = $rf_mess->where('paystatus = 2 AND device = '.$did)->select();
        if(!empty($sid) && !empty($did)){
            $data['sid'] = $sid; //后期修改为JSON串
            $data['date'] = date('Y-m-d H:i:s');
            $res = $rf_mess->where('id = '.$crow[0]['id'])->save($data);
            if($res){
                echo jsonShow(200,'数据插入成功',$res);
                exit;
            }else{
                echo jsonShow(400,'数据插入失败',$res);
                exit;
            }
        }else{
            echo jsonShow(400,'商品数据不能为空','请重新扫描');
            exit;
        }
    }

    //判断当前是否有商品需要付款并显示
    //后期增加key
    public function getRfidPayStatus(){
        header('Access-Control-Allow-Origin:*');
        $RfidStatus = M('RfidStatus');
        $where['id'] = $_GET['did'];

        if(!empty($where)){
            $rrow = $RfidStatus->where($where)->select();
            $res = $rrow[0]['status'];

            $rorder = M('RfidOrd');
            $row = $rorder->where('device = '.$where['id'].' AND paystatus = 2')->select();
            $id = $row[0]['id'];

            if($rrow){
                echo jsonShow(200,$id,$res);
                exit;
            }else{
                echo jsonShow(400,$id,$res);
                exit;
            }
        }else{
            echo jsonShow(400,'设备序号不能为空','请重新发送');
            exit;
        }
    }

    //修改状态，插入数据
    public function updateRfidPayStatus(){
        header('Access-Control-Allow-Origin:*');
        $RfidStatus = M('RfidStatus');
        $where['id']        = $_GET['did'];
        $data['status']     = $_GET['status'];
        $data['begintime']  = date('Y-m-d H:i:s');
        $d['sid']           = $_GET['sid'];

        //查询设备
        $res = $RfidStatus->where($where)->select($data);


        if($res){

            //查询是否首次插入信息
            $rorder = M('RfidOrd');
            $or['device'] = '1';
            $or['paystatus'] = '2';

            //查询芯片读取的次数
            $row = $rorder->where('device = '.$where['id'].' AND paystatus = 2')->select();

            if($row){
                $sid = json_decode($row[0]['sid'],true);

                //查询该芯片所携带的商品id并修改该商品状态
                $Rgoods = M('RfidGoods');
                $rgrow = $Rgoods->where('rfid LIKE \''.$d['sid'].'\' AND status = 1')->select();

                if($rgrow){

                    //修改设备的读取状态为挂起
                    $RfidStatus->where($where)->save($data);

                    $rsid = array($rgrow[0]['id']);
                    $rg['status'] = '-1';
                    $rgr = $Rgoods->where('id = '.$rgrow[0]['id'])->save($rg);

                    $n['sid'] = json_encode(array_merge($sid,$rsid),true);
                    $r = $rorder->where('id = '.$row[0]['id'])->save($n);

                    echo jsonShow(200,'数据插入成功',$r);
                    exit;
                }else{
                    echo jsonShow(400,'无该商品或该商品状态已改变',$rgrow);
                    exit;
                }


            }else{
                $Rgoods = M('RfidGoods');
                $rgrow = $Rgoods->where('rfid LIKE \''.$d['sid'].'\' AND status = 1')->select();
                if($rgrow){

                    //修改设备的读取状态为挂起
                    $RfidStatus->where($where)->save($data);

                    //首次
                    $or['date'] = date('Y-m-d H:i:s');
                    $or['sid'] = json_encode(array($rgrow[0]['id']),true);
                    $oid = $rorder->add($or);

                    $rg['status'] = '-1';
                    $rgr = $Rgoods->where('id = '.$rgrow[0]['id'])->save($rg);

                    echo jsonShow(200,'首次插入数据成功',$oid);
                    exit;
                }else{
                    echo jsonShow(400,'无该商品',$rgrow);
                    exit;
                }

            }
        }else{
            echo jsonShow(400,'设备状态修改失败，Parameter error',$res);
            exit;
        }
    }

    //根据设备实时获取数据
    public function getTheInfoByDevice(){
        header('Access-Control-Allow-Origin:*');

        $rorder = M('RfidOrd');
        $where['device'] = $_GET['did'];
        $where['paystatus'] = '2';
        $time = $_GET['time'];
        $rrow = $rorder->where($where)->select();
        if($rrow){
            $narray = array();
            $json = json_decode($rrow[0]['sid'],true);
            $rg = M('RfidGoods');

            foreach ($json as $k => $v){
                $rgr = $rg->where('id = '.$v)->select();
                $narray[$k]['id'] = $v;
                $narray[$k]['price'] = $rgr[0]['price'];
                $narray[$k]['name'] = $rgr[0]['name'];
                $narray[$k]['time'] = date('Y-m-d H:i:s');
            }
            echo jsonShow(200,'获取成功',$narray);
            exit;
        }else{
            echo jsonShow(400,'该设备无可预付的订单',$rrow);
            exit;
        }
    }


    //用户长时间未付款对商品状态进行重置，订单删除
    public function clearOrder(){
        header('Access-Control-Allow-Origin:*');

        //修改商品状态
        $rorder = M('RfidOrd');
        $or['device'] = $_GET['did'];
        $or['paystatus'] = '2';

        $row = $rorder->where('device = '.$_GET['did'].' AND paystatus = 2')->select();

        if($row){
            $sid = json_decode($row[0]['sid'],true);
            $rgoods = M('RfidGoods');

            foreach ($sid as $k=>$v){
                $rgrow = $rgoods->where('id='.$v.' AND status = \'-1\'')->select();
                if(empty($rgrow)){
                    echo jsonShow(400,'该商品不存在',$rgrow);
                    exit;
                }
                $data['status'] = '1';
                $rgoods->where('id='.$v.' AND status = \'-1\'')->save($data);
            }

            $rstatus = M('RfidStatus');
            $rs['status'] = '0';
            $rstatus->where('id='.$_GET['did'])->save($rs);
            $res = $rorder->where('device = '.$_GET['did'].' AND paystatus = 2')->delete();
            if($res){
                echo jsonShow(200,'重置成功',$res);
                exit;
            }
        }else{
            echo jsonShow(400,'该设备的预付订单不存在',$row);
            exit;
        }
    }

    //用户支付成功后的回调接口
    public function successPayCallBack(){
        header('Access-Control-Allow-Origin:*');

        //修改订单状态
        $rorder = M('RfidOrd');
        $or['device'] = I('did');
        $totalprice = I('totalprice');
        $or['paystatus'] = '2';

        $row = $rorder->where('device = '.$_GET['did'].' AND paystatus = 2')->select();

        if($row){
            $sid = json_decode($row[0]['sid'],true);
            $rgoods = M('RfidGoods');

            //修改商品状态
            foreach ($sid as $k=>$v){
                $rgrow = $rgoods->where('id='.$v.' AND status = \'-1\'')->select();
                if(empty($rgrow)){
                    echo jsonShow(400,'该商品不存在',$rgrow);
                    exit;
                }
                $data['status'] = '0';
                $rgoods->where('id='.$v.' AND status = \'-1\'')->save($data);
            }

            //修改设备状态
            $rstatus = M('RfidStatus');
            $rs['status'] = '2';
            $rstatus->where('id='.$or['device'])->save($rs);
            $rdata['paystatus'] = '1';
            $rdata['totalprice'] = $totalprice;
            $res = $rorder->where('device = '.$or['device'].' AND paystatus = 2')->save($rdata);
            if($res){
                echo jsonShow(200,'重置成功',$res);
                exit;
            }
        }else{
            echo jsonShow(400,'该设备的预付订单不存在',$row);
            exit;
        }
    }


    //注册接口
    public function regUser(){
        header('Access-Control-Allow-Origin:*');

        $name = I('name');
        $idcard = I('idcard');
        $number=I('num');
        $phone=I('phone');
        $openid=I('openid');

        $num = S($phone);

        if($number != $num || empty($number)){
            echo jsonShow(401,'验证码错误',$number);
            exit();
        }

        $rm = M('RfidMember');

        $mrow = $rm->where('openid LIKE \''.$openid.'\' AND status = 1')->select();
        if(!empty($mrow)){
            echo jsonShow(400,'该用户已注册，请勿重复注册',$mrow);
            exit;
        }

        $ar = new ArticleController();
        $cardNo = $idcard;
        $realName = $name;
        $result = $ar->realnameAuthentication($cardNo,$realName);
        if(!$result){
            echo jsonShow(402,'业主认证失败','实名认证不通过');
            exit();
        }

        $data['name'] = $name;
        $data['openid'] = $openid;
        $data['credit'] = '100';
        $data['regtime'] = date('Y-m-d H:i:s');
        $data['idcard'] = $idcard;
        $data['phone'] =  $phone;
        $data['phonecode'] =  $number;
        $data['status'] =  1;
        $res = $rm->add($data);
        if($res){
            echo jsonShow(200,'注册成功',$res);
            exit;
        }else{
            echo jsonShow(400,'注册失败',$res);
            exit;
        }
    }

    //实名调用接口
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


    //电话校验
    public function validateMobile(){;
        header('Access-Control-Allow-Origin:*');

        $phone=I('phone');
        $rm = M('RfidMember');
        $mrow = $rm->where('phone = '.$phone)->select();
        if(!empty($mrow)){
            echo jsonShow(400,'该手机已被注册',$mrow);
            exit;
        }

        $number=rand(100000,999999);
        $content="【掌Go便利店】您的验证码为:".$number."，在10分钟内有效。";//要发送的短信内容
        $result=getSmsbao($content,$phone);
        S($phone,$number,600);

        echo jsonShow(200,'发送成功',$result);
        exit;
    }

    //获取openid
    public function getOpenId(){
        header('Access-Control-Allow-Origin:*');

        $code=I("code");
        if($code)
        {
            $url="https://api.weixin.qq.com/sns/jscode2session";
            $data['appid']="wxfef9a46cc6624af8";
            $data['secret']="25540b9bc6872c0893dfec250bfa573e";
            $data['js_code']=$code;
            $data['grant_type']="authorization_code";
            //获取openid
            $res=$this->postRequest($url,$data);
            $res=json_decode($res,true);
        }
        $openid = $res['openid'];


        echo jsonShow(200,'获取成功',$openid);
        exit;
    }

    //商品批量上架
    public function addRfidWithGoods(){
        header('Access-Control-Allow-Origin:*');

        $sid = I('sid');
        $did = I('did');

        $rstatus = M('RfidStatus');
        $rsr = $rstatus->where('id='.$did)->select();
        $status = $rsr[0]['managestatus'];
        $type = $rsr[0]['managetype'];
        $area = $rsr[0]['area'];

        $rt = M('RfidType');
        $rtr = $rt->where('id = '.$type)->select();

        $rg = M('RfidGoods');

        switch ($status){
            case 1:
                //开始上架，判断是否存在相同编号标签，有则重新上架
                $rer = $rg->where('rfid LIKE \''.$sid.'\'')->select();

                if(!empty($rer)){
                    $data['status'] = 1;
                    $data['name'] = $rtr[0]['name'];
                    $data['price'] = $rtr[0]['price'];
                    $data['rfid'] = $sid;
                    $data['area'] = $area;
                    $data['uptime'] = date('Y-m-d H:i:s');
                    $data['status'] = 1;
                    $data['type'] = $type;
                    $data['did'] = $did;
                    $res = $rg->where('rfid LIKE \''.$sid.'\'')->save($data);
                }else{
                    $data['name'] = $rtr[0]['name'];
                    $data['price'] = $rtr[0]['price'];
                    $data['rfid'] = $sid;
                    $data['area'] = $area;
                    $data['uptime'] = date('Y-m-d H:i:s');
                    $data['status'] = 1;
                    $data['type'] = $type;
                    $data['did'] = $did;

                    $res = $rg->add($data);
                }
                break;
            case 2:
                //结束上架
                $data['managestatus'] = 0;
                $data['managetype'] = 0;
                $data['endtime'] = date('Y-m-d H:i:s');
                $res = $rstatus->where('id='.$did)->save($data);
                break;
            case 3:
                //开始下架
                $data['status'] = 2;
                $data['downtime'] = date('Y-m-d H:i:s');
                $data['did'] = $did;
                $res = $rg->where('rfid LIKE \''.$sid.'\'')->save($data);
                break;
            case 4:
                //结束下架
                $data['managestatus'] = 0;
                $data['managetype'] = 0;
                $data['endtime'] = date('Y-m-d H:i:s');
                $res = $rstatus->where('id='.$did)->save($data);
                break;
            case 5:
                //查询检测
                $res = $rg->where('rfid LIKE \''.$sid.'\'')->select();
                break;
            default:
                $res = 0;
                break;
        }


        echo json_encode($res,true);
        exit;

    }

    //获取商品状态
    public function getGoodsList(){
        header('Access-Control-Allow-Origin:*');

        $did = I('did');
        $rg = M('RfidGoods');
        $rgr = $rg->where('did = '.$did)->select();
        echo json_encode($rgr,true);
    }

    //修改设备状态
    public function updateDidManageStatus(){
        header('Access-Control-Allow-Origin:*');

        $did = I('did');
        $data['managestatus'] = I('managestatus');
        $data['managetype'] = I('managetype');
        $data['begintime'] = date('Y-m-d H:i:s');

        if($data['managestatus'] == 2 || $data['managestatus'] == 4){
            //结束下架-结束上架
            $data['endtime'] = date('Y-m-d H:i:s');
        }
        $rs = M('RfidStatus');
        $res = $rs->where('id='.$did)->save($data);
        if($res){
            echo json_encode($res,true);
            exit;
        }
    }

    //上传商品类型
    public function addType(){
        header('Access-Control-Allow-Origin:*');

        $data['name'] = I('name');
        $data['price'] = I('price');
        $data['code'] = 'SD'.time();
        $data['brand'] = I('brand');
        $rt = M('RfidType');
        $res = $rt->add($data);
        if($res){
            echo json_encode($res,true);
            exit;
        }
    }

    //获取商品列表
    public function getOrderList(){
        header('Access-Control-Allow-Origin:*');

        $openid = I('openid');
        $status = I('status');
        $me = M('RfidMember');
        $mrow = $me->where('openid LIKE \''.$openid.'\' AND status = 1')->select();

        if(!empty($mrow)){

            $ro = M('RfidOrd');
            $rorow = $ro->where('buyer = '.$mrow[0]['id'].' AND paystatus = '.$status)->order('date desc')->select();

            $rg = M('RfidGoods');

            foreach ($rorow as $k => $v){
                $sid = $v['sid'];
                $sid_arr = json_decode($sid,true);
                foreach ($sid_arr as $ke => $va){
                    $rgrow = $rg->where('id = '.$va)->select();
                    $rorow[$k]['goods_array'][$ke] = $rgrow[0];
                }
            }

            echo jsonShow(200,'获取成功',$rorow);
            exit;
        }else{
            echo jsonShow(400,'该用户未注册或已被注销',$mrow);
            exit;
        }
    }

    //获取商品类型
    public function getTypes(){
        header('Access-Control-Allow-Origin:*');

        $rt = M('RfidType');
        $row = $rt->select();
        echo json_encode($row,true);
    }

    //验证用户是否登录
    public function validateUser(){
        header('Access-Control-Allow-Origin:*');

        $openid = I('openid');
        $me = M('RfidMember');
        $mrow = $me->where('openid LIKE \''.$openid.'\' AND status = 1')->select();
        if(!empty($mrow)){
            echo jsonShow(200,'验证成功',$mrow);
            exit;
        }else{
            echo jsonShow(400,'该用户未注册或已被注销',$mrow);
            exit;
        }
    }

    //获取用户信用分和购买次数
    public function getUserCreditAndBuyCount(){
        header('Access-Control-Allow-Origin:*');

        $openid = I('openid');
        $me = M('RfidMember');
        $mrow = $me->where('openid LIKE \''.$openid.'\' AND status = 1')->select();

        $wh['buyer'] = $mrow[0]['id'];
        $wh['paystatus'] = 1;

        $ro = M('RfidOrd');
        $ror = $ro->where($wh)->count();

        $rr['credit'] = $mrow[0]['credit'];
        $rr['buycount'] = $ror;

        if(!empty($mrow)){
            echo jsonShow(200,'验证成功',$rr);
            exit;
        }else{
            echo jsonShow(400,'该用户未注册或已被注销',$mrow);
            exit;
        }
    }

    //POST请求
    protected function postRequest($url,$data){
        $postStr = '';
        foreach($data as $k => $v){
            $postStr .= $k."=".$v."&";
        }
        $postStr=substr($postStr,0,-1);
        return $this->postUrl($url,$postStr);
    }

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