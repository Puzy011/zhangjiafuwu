<?php
namespace Apiapp\Controller;
require_once "SmsSender.php";

class MemberController{

    //验证用户是否填写信息
    public function validateUserStatus(){
        $openid = I('openid');
        $mem = M('Member');
        $mrow = $mem->where('openid LIKE \''.$openid.'\'')->select();
        echo jsonShow(401,'信息填写不完整',$mrow[0]['authentication']);
    }

    /**
     * 租客认证
     */
    public function tenantAuthentication(){
        $number=I('number');
        $openid=I('openid');
        $mphone=I('mphone');
        $vphone=I('vphone');
        $type=I('type');
        if(empty($number) || empty($mphone) || empty($vphone)){
            echo jsonShow(401,'信息填写不完整',$number);
            exit();
        }
        $kphone=$mphone.$vphone;
        $data['authentication']=2;
        $mnumber=S($kphone);
         if($number != $mnumber){
             echo jsonShow(401,'验证码错误',$number);
             exit();
         }
        $map['phone']=$mphone;
        $map['authentication']=2;
        $member=D('Member')->getMemberInfo($map);
        if(!empty($member)){//判断member是否存在
            $where['openid']=$openid;
            $memberInfo=D('Member')->getMemberInfo($where);
            $memberid=$member[0]['id'];
            $viceid=$memberInfo[0]['id'];
            $mid=D('Member')->binding($where,$data);
            $relation['main_id']=$memberid;
            $relation['vice_id']=$viceid;
            $relation['type']=$type;
            $status['status']=1;
            $relationInfo=D('Member')->searchTenant($relation);
            if(!empty($relationInfo)){
                $res=D('Member')->changStatus($relation,$status);
                echo jsonShow(200,'租客认证成功',$res);
                exit();
            }
            $relation['status']=1;
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

    //业主的短信认证
    public function smsbaoYezhu(){
        $where['openid']=I('openid');
        $vmember=D('Member')->getMemberInfo($where);
        $number=rand(1000,9999);
        $mphone=I('mphone');
        $vphone=I('vphone');
        $residen=I('residential');
        $kphone=$mphone.$vphone;

        $statusStr = array(
            "0" => "短信发送成功",
            "-1" => "参数不全",
            "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
            "30" => "密码错误",
            "40" => "账号不存在",
            "41" => "余额不足",
            "42" => "帐户已过期",
            "43" => "IP地址限制",
            "50" => "内容含有敏感词"
        );
        $content="【掌家门户】您的验证码为:".$number."，在10分钟内有效。";//要发送的短信内容
//        $result=getSmsbao($content,$mphone);
        $result=$this->tenxunSms($mphone,$residen,$number);
        S($kphone,$number,600);
        echo jsonShow(200,0,$result);
    }
    /**
     * 短息验证
     */
    public function smsbao(){
        $type = I('type');
        $name = I('name');
        $where['openid']=I('openid');
        $where['authentication']=2;
        $vmember=D('Member')->getMemberInfo($where);
        if(!empty($vmember)){
            echo jsonShow(401,'您已经是认证用户',$vmember);
            exit();
        }
        $number=rand(1000,9999);
         $mphone=I('mphone');
         $vphone=I('vphone');
         $kphone=$mphone.$vphone;
         $map['phone']=$mphone;
         $map['authentication']=2;
         $member=D('Member')->getMemberInfo($map);
         if(empty($member)){
             echo jsonShow(401,'该业主未认证',$mphone);
             exit();
         }
         $statusStr = array(
             "0" => "短信发送成功",
             "-1" => "参数不全",
             "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
             "30" => "密码错误",
             "40" => "账号不存在",
             "41" => "余额不足",
             "42" => "帐户已过期",
             "43" => "IP地址限制",
             "50" => "内容含有敏感词"
         );
         $content="【掌家门户】您的验证码为:".$number."，在10分钟内有效。";//要发送的短信内容
//         $result=getSmsbao($content,$mphone);
        $type = $type == 1?"租户":"家人";
        $result=$this->tenxunSmsOther($mphone,$number,$type,$name);
         S($kphone,$number,600);
         echo jsonShow(200,0,$result);
    }

    /**
     * 腾讯短信服务
     */
    public function tenxunSms($mphone,$residen,$number){
        // 请根据实际 appid 和 appkey 进行开发，以下只作为演示 sdk 使用
        // appid,appkey,templId申请方式可参考接入指南 https://www.qcloud.com/document/product/382/3785#5-.E7.9F.AD.E4.BF.A1.E5.86.85.E5.AE.B9.E9.85.8D.E7.BD.AE
        $appid = 1400055640;
        $appkey = "83634e8ccea24509ad1cc98fb1986228";

        $singleSender = new SmsSingleSender($appid, $appkey);

        // 普通单发
        $result = $singleSender->send(0, "86", $mphone, "您好！您的".$residen."认证验证码为：".$number."，在五分钟内有效。衷心感谢您使用掌家门户，掌家呵护您的每一天！", "", "");
        $rsp = json_decode($result);
        return $rsp;
    }

    /**
     * 腾讯短信服务
     */
    public function tenxunSmsOther($mphone,$number,$type,$name){
        // 请根据实际 appid 和 appkey 进行开发，以下只作为演示 sdk 使用
        // appid,appkey,templId申请方式可参考接入指南 https://www.qcloud.com/document/product/382/3785#5-.E7.9F.AD.E4.BF.A1.E5.86.85.E5.AE.B9.E9.85.8D.E7.BD.AE
        $appid = 1400055640;
        $appkey = "83634e8ccea24509ad1cc98fb1986228";

        $singleSender = new SmsSingleSender($appid, $appkey);

        // 普通单发
        $result = $singleSender->send(0, "86", $mphone, "您好！您的".$type."，姓名：".$name."正在进行用户认证，如真实有效，请将验证码为".$number."提供给指定人员，该验证码在五分钟内有效。衷心感谢您使用掌家门户，掌家呵护您的每一天！", "", "");
        $rsp = json_decode($result);
        return $rsp;
    }

    /**
     * 获取租户数量
     */
    public function getTenantNumber(){
        $openid=I('openid');
        $where['openid']=$openid;
        $member=D('Member')->getMemberInfo($where);
        $map['main_id']=$member[0]['id'];
        $map['type']=1;
        $map['status']=1;
        $count['type1']=D('Member')->getNumber($map);
        $map['type']=2;
        $count['type2']=D('Member')->getNumber($map);
        echo jsonShow(200,'获取数量成功',$count);
    }
    /**
     * 获取租客列表
     * */
    public function getTenantList(){
        $openid=I('openid');
        $type=I('type');
        $where['openid']=$openid;
        $member=D('Member')->getMemberInfo($where);
        $map['main_id']=$member[0]['id'];
        $map['type']=$type;
        $map['zj_relation.status']=1;
        $data=D('Member')->getTenantList($map);
        if(!empty($data)){
            echo jsonShow(200,'获取列表成功',$data);
        }else{
            echo jsonShow(401,'暂无租户',$data);
        }
    }
    /**
     * 删除租户
     */
    public function chanStatus(){
        $openid=I('openid');
        $mid=I('mid');
        $where['id']=$mid;
        $mdata['authentication']=1;
        $member=D('Member')->binding($where,$mdata);
        $map['vice_id']=$mid;
        $data['status']=-1;
        $relation=D('Member')->changStatus($map,$data);//修改租户状态
        if(empty($relation)){
            echo jsonShow(402,'删除租户失败',$relation);
        }else{
            echo jsonShow(200,'删除租户成功',$relation);
        }
    }
    

     /*
     * 业主投票 
     */
    //投票首页保存信息
    public function vote_obtain(){
            $openid = I('openid'); //业主的openid
            $id['id'] = I('id'); //文章id
            $type = I('type');  //投票类型   
            $votetime = S($openid);
            $timeout = time() - $votetime['time'];
      if ($timeout<300) {
            echo jsonShow(410,'失败','当前已经投过票');
            exit();
      }
      if (!empty($id['id'])&&empty($type)) {
            $arr = D('article')->article_select($id);
            echo jsonShow(200,'成功',$arr);
            exit();
      }else{
        if (empty($id['id'])&&empty($type)) {
            echo jsonShow(409,'失败','文章投票方式错误');
            exit();
        }
        if (empty($id['id'])&&!empty($type)) {
            echo jsonShow(412,'失败','没有文章');
            exit();
        }
        
        $articles = D('article')->article_select($id);
        foreach ($articles as $key => $value) {
            $vote = $value['vote'];
            $click = $value['click'];
            $up = $value['up'];
            $down = $value['down'];
            $endtime = $value['endtime'];
        }
        $time = time();
        if ($vote == 1&&$time<$endtime) { //文章开启了,且文章在有效时间内 否则不进来  
            $opentime['time'] = time();
            S($openid,$opentime);
            //赞同
            if ($type == 'up') {
                $voteclick = array(
                    'up' => $up+1,
                    'click' => $click+1
                );
            $arr = D('article')->vote_save($id,$voteclick);
            }
            //反对
            if ($type == 'down') {
                $voteclick = array(
                    'down' => $down+1,
                    'click' => $click+1
                );
            $arr = D('article')->vote_save($id,$voteclick);
            }
        $arrs = D('article')->article_select($id);    
        echo jsonShow(200,'投票成功',$arrs);
        exit();
        }else{
        echo jsonShow(406,'投票失败','文章未开启投票且已过投票期');  
        exit(); 
        }
        
      }
      
    }              
}
?>