<?php
namespace Zjadmin\Controller;
use Think\Page;

class MemberController extends AdminController
{
    /**
     * 业主列表
     */
    public function index(){
        $rid=I('residential');
        $phone=I('phone');
        $name=I('name');
        $authentication=I('authentication');
        $floor=I('floor');

        if(!empty($rid)){
            $setrid= array_merge( array('residential_id' => array('in', $rid )) ,(array)$setrid );
        }
        if(!empty($phone)){
            $setrid['phone']=array('like', '%'.(string)$phone.'%');
        }
        if(!empty($name)){
            $setrid['name']=array('like', '%'.(string)$name.'%');
        }
        if(!empty($authentication)){
            $setrid['authentication']=$authentication;
        }
        if(!empty($floor)){
            $setrid['floor']=$floor;
        }
        if(empty($rid)){
            $rid=getRid();
            $setrid= array_merge( array('residential_id' => array('in', $rid )) ,(array)$setrid );
        }
        $setrid['zj_member.status']=1;
        $count=M('Member')->where($setrid)->count();
        $parameter['residential']=$rid;
        $parameter['phone']=$phone;
        $parameter['name']=$name;
        $parameter['authentication']=$authentication;
        $parameter['floor']=$floor;
        $page=getPage($count,$map,$parameter);
        $list=D('Member')->search($setrid,$page);

        $show = $page->show();// 分页显示输出
        $this->assign('_page',$show);// 赋值分页输出

        $residential=getRlist();
        int_to_string($list);
        $this->assign('list', $list);
        $this->assign('residentiallist', $residential);
        $this->meta_title = '业主信息';
        $this->display();
 }


    public function memberList(){
        $rid=I('residential');
        $phone=I('phone');
        $name=I('name');
        $authentication=I('authentication');
        $floor=I('floor');
        $mtype=I('mtype');

        if(!empty($rid)){
            $setrid= array_merge( array('residential_id' => array('in', $rid )) ,(array)$setrid );
        }
        if(!empty($phone)){
            $setrid['phone']=array('like', '%'.(string)$phone.'%');
        }
        if(!empty($name)){
            $setrid['name']=array('like', '%'.(string)$name.'%');
        }
        if(!empty($authentication)){
            $setrid['authentication']=$authentication;
        }
        if(!empty($floor)){
            $setrid['floor']=$floor;
        }
        if(!empty($mtype)){
            $setrid['mtype']=$mtype;
        }
        if(empty($rid)){
            $rid=getRid();
            $setrid= array_merge( array('residential_id' => array('in', $rid )) ,(array)$setrid );
        }
        $setrid['zj_member.status']=1;
        $count=M('Member')->where($setrid)->count();
        $parameter['residential']=$rid;
        $parameter['phone']=$phone;
        $parameter['name']=$name;
        $parameter['authentication']=$authentication;
        $parameter['floor']=$floor;
        $parameter['mtype']=$mtype;
        $parameter['floor']=$floor;
        $page=getPage($count,$map,$parameter);
        $list=D('Member')->search($setrid,$page);
        file_put_contents(__LINE__.'.txt',D('Member')->getLastSql());
        

        $show = $page->show();// 分页显示输出
        $this->assign('_page',$show);// 赋值分页输出

        $residential=getRlist();
        int_to_string($list);

        foreach ($list as $key => $value){
            if($value['authentication'] == 1){
                $list[$key]['authentication'] = '<a href=\'http://www.zhangjiamenhu.com/admin.php?s=/Member/submitMember/id/'.$value['id'].'.html\' style=\'color: red;\'>用户认证</a>';
            }else{
                $list[$key]['authentication'] = "<a style='color: green;' href='#'>已认证</a>";
            }


        }

        $this->assign('list', $list);
        $this->assign('residentiallist', $residential);
        $this->meta_title = '业主信息';
        $this->display();


//        $User = M('Member'); // 实例化User对象
//// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
//        $list = $User->where('authentication=2')->order('id asc')->page($_GET['p'].',25')->select();
//        $this->assign('list',$list);// 赋值数据集
//        $count      = $User->where('authentication=2')->count();// 查询满足要求的总记录数
//        $Page       = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数
//        $show       = $Page->show();// 分页显示输出
//        $this->assign('page',$show);// 赋值分页输出
//        $this->display(); // 输出模板
    }

    //显示业主家人
    public function showUserFamily(){
        echo 1;
//        $openid=I('openid');
//        $type=2;
//        $where['openid']=$openid;
//        $member=D('Member')->getMemberInfo($where);
//        $map['main_id']=$member[0]['id'];
//        $map['type']=$type;
//        $map['zj_relation.status']=1;
//        $data=D('Member')->getTenantList($map);
//        $this->assign('data',$data);
//        $this->meta_title = '查看业主家人';
//        $this->display();
    }

    public function edit(){
        $data = M('Member')->field(true)->find($id);
        $this->assign('data',$data);
        $this->meta_title = '业主认证';
        $this->display();
    }
    public function cancel(){
        $id    = I('id');
        $data = M('Member')->field(true)->find($id);
        $this->assign('data',$data);
        $this->meta_title = '取消业主认证';
        $this->display();
    }
    
    public function submitMember(){
        $id    = I('id');
//        $floor=I('floor');
//        $household=I('household');
//        $acreage=I('acreage');
//        $data['floor']=$floor;
//        $data['household']=$household;
//        $data['acreage']=$acreage;
        $data['authentication']   =  2;
        $map['id']=$id;
        $member = D("Member")->updateMember($map,$data);

        if($member>0){
            $res = $this->postMemberStatus($map);
            $res = json_decode($res,true);
            if($res['errcode'] === 0){
                $this->success('业主认证成功！',U('memberList'));
            } else{
                $this->error($this->showRegError($member));
            }
        } else { //操作失败，显示错误信息
            $this->error($this->showRegError($member));
        }
    }

    //发送小程序通知-早餐状态修改
    public function postServiceStatusWithBreak($oid){

        $serviceOrder = M('ServiceOrder');
        $srow = $serviceOrder->where('id='.$oid)->select();

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
        $mrow = $mem->where('id='.$srow[0]['keyword'])->select();

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
                    "value" => $keyword9,
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
        return $res = $this->postUrl($url,$data);
    }

    //发送小程序通知-注册成功通知
    public function postMemberStatus($map){
        $mem = M('Member');
        $mrow = $mem->where($map)->select();
        $url_acc = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxb7fd9c340ac095ef&secret=1ebd797e7414747609c29075e9308b6f";
        $acc_arr=json_decode(file_get_contents($url_acc),true);
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$acc_arr['access_token'];
        $keyword1 = "您已通过业主认证";
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
        return $res = $this->postUrl($url,$data);
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


    public function submitMemberStore(){
        $id    = I('id');
        $floor=I('floor');
        $household=I('household');
        $data['floor']=$floor;
        $data['household']=$household;
        $data['is_auth']   =  2;
        $map['id']=$id;
        $member = D("Member")->updateStoreMember($map,$data);
        if($member>0){
            $this->success('业主认证成功！',U('memberStore'));
        } else { //操作失败，显示错误信息
            $this->error($this->showRegError($member));
        }
    }

    public function saveCancel(){

        //获取参数
        $id=I('id');
        $data['authentication']   =  1;
        $data['status']=-1;
        $data['remarks']=I('remarks');
        $map['id']=$id;
//        $member = D("Member")->updateMember($map,$data);
        $me = M('Member');
        $member = $me->where($map)->delete();

        $re = M('Relation');
        $re->where('main_id='.$id)->delete();
        $re->where('vice_id='.$id)->delete();

        if($member>0){
            $this->success('删除成功！',U('memberList'));
        } else { //操作失败，显示错误信息
            $this->error('删除失败！');
        }
    }
    public function saveCancelStore(){
        //获取参数
        $id=I('id');
        $data['status']=-1;
        $map['id']=$id;
        $member = D("Member")->saveCancelStore($map,$data);
        if($member>0){
            $this->success('删除成功！',U('index'));
        } else { //操作失败，显示错误信息
            $this->error('删除失败！');
        }
    }
    /**
     * 修改业主状态
     */
    public function changeStatus($method=null){
        $id = array_unique((array)I('id',0));
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
    
        $map['id'] =   array('in',$id);
        switch ( strtolower($method) ){
            case 'forbid':
                $this->forbid('Member', $map );
                break;
            case 'resume':
                $this->resume('Member', $map );
                break;
            case 'delete':
                $this->delete('Member', $map );
                break;
            default:
                $this->error('参数非法');
        }
    }
    public function getMemberRelation(){
        $phone=I('phone');
        $name=I('name');
        
        if(!empty($phone)){
            $setrid['phone']=array('like', '%'.(string)$phone.'%');
        }
        if(!empty($name)){
            $setrid['name']=array('like', '%'.(string)$name.'%');
        }
        $setrid['authentication']=3;
        $count=M('Member')->where($setrid)->count();
        $parameter['phone']=$phone;
        $parameter['name']=$name;
        $page=getPage($count,$map,$parameter);
        $list=D('Member')->getMemberRelation($setrid,$page);
        
        $show = $page->show();// 分页显示输出
        $this->assign('_page',$show);// 赋值分页输出
        
        int_to_string($list);
        $this->assign('list', $list);
        $this->meta_title = '未合作小区业主信息';
        $this->display('member');
    }
    public function paymentList(){
        $phone=I('phone');
        $name=I('name');
        $authentication=I('authentication');
     
        if(!empty($phone)){
            $setrid['phone']=array('like', '%'.(string)$phone.'%');
        }
        if(!empty($name)){
            $setrid['name']=array('like', '%'.(string)$name.'%');
        }
        if(!empty($authentication)){
            $setrid['authentication']=$authentication;
        }
            $rid=getRid();
            $setrid= array_merge( array('residential_id' => array('in', $rid )) ,(array)$setrid );
            $setrid['payment']=0;
        $count=M('Member')->where($setrid)->count();
        $parameter['phone']=$phone;
        $parameter['name']=$name;
        $parameter['authentication']=$authentication;
        $page=getPage($count,$map,$parameter);
        $list=D('Member')->search($setrid,$page);
        
        $show = $page->show();// 分页显示输出
        $this->assign('_page',$show);// 赋值分页输出
        
        int_to_string($list);
        $this->assign('list', $list);
        $this->meta_title = '业主信息';
        $this->display();
    }
    public function paymentOrder(){
        if(IS_POST){
            $id=I('id');
            $endtime=I('endtime');
            $dfyprice=I('dfyprice');
            $sfyprice=I('sfyprice');
            $ayprice=I('ayprice');
            $byprice=I('byprice');
            $cyprice=I('cyprice');
            $dyprice=I('dyprice');
            $eyprice=I('eyprice');
            $fyprice=I('fyprice');
            $gyprice=I('gyprice');
            $hyprice=I('hyprice');
            $iyprice=I('iyprice');
            $jyprice=I('jyprice');
            
           
            if(empty($endtime)){
                $this->error('截止日期不能为空!');
            }
           if($dfyprice>0){
               $datadf['status']=0;
               $datadf['member_id']=$id;
               $datadf['time']=time();
               $datadf['endtime']=$endtime;
               
               $datadf['payment_type'] = 2;
               $datadf['yprice'] = $dfyprice;
               $datadf['last_month']=I('dflast_month');
               $datadf['this_monh']=I('dfthis_monh');
               $datadf['practical']=I('dfpractical');
               $datadf['price']=I('dfprice');
               $arr = D('Member')->addPaymentOrder($datadf);
           }
           if($sfyprice>0){ 
               $datasf['status']=0;
            $datasf['member_id']=$id;
            $datasf['time']=time();
            $datasf['endtime']=$endtime;
            
               $datasf['payment_type'] = 1;
               $datasf['yprice'] = $sfyprice;
               $datasf['last_month']=I('sflast_month');
               $datasf['this_monh']=I('sfthis_monh');
               $datasf['practical']=I('sfpractical');
               $datasf['price']=I('sfprice');
               $arr = D('Member')->addPaymentOrder($datasf);
           }
           if($ayprice>0){
               $dataa['status']=0;
               $dataa['member_id']=$id;
               $dataa['time']=time();
               $dataa['endtime']=$endtime;
               
               $dataa['payment_type'] = 3;
               $dataa['yprice'] = $ayprice;
               $arr = D('Member')->addPaymentOrder($dataa);
           }
           if($byprice>0){
               $datab['status']=0;
               $datab['member_id']=$id;
               $datab['time']=time();
               $datab['endtime']=$endtime;
               
               $datab['payment_type'] = 4;
               $datab['yprice'] = $byprice;
               $arr = D('Member')->addPaymentOrder($datab);
           }
           if($cyprice>0){
               $datac['status']=0;
               $datac['member_id']=$id;
               $datac['time']=time();
               $datac['endtime']=$endtime;
               
               $datac['payment_type'] = 5;
               $datac['yprice'] = $cyprice;
             
               $arr = D('Member')->addPaymentOrder($datac);
           }
           if($dyprice>0){
               $datad['status']=0;
               $datad['member_id']=$id;
               $datad['time']=time();
               $datad['endtime']=$endtime;
               
               $datad['payment_type'] = 6;
               $datad['yprice'] = $dyprice;
               $arr = D('Member')->addPaymentOrder($datad);
           }
           if($eyprice>0){
               $datae['status']=0;
               $datae['member_id']=$id;
               $datae['time']=time();
               $datae['endtime']=$endtime;
               
               $datae['payment_type'] = 7;
               $datae['yprice'] = $eyprice;
               $arr = D('Member')->addPaymentOrder($datae);
           }
           if($fyprice>0){
               $dataf['status']=0;
               $dataf['member_id']=$id;
               $dataf['time']=time();
               $dataf['endtime']=$endtime;
               
               $dataf['payment_type'] = 8;
               $dataf['yprice'] = $fyprice;
               $arr = D('Member')->addPaymentOrder($dataf);
           }
           if($gyprice>0){
               $datag['status']=0;
               $datag['member_id']=$id;
               $datag['time']=time();
               $datag['endtime']=$endtime;
               
               $datag['payment_type'] = 9;
               $datag['yprice'] = $gyprice;
               $arr = D('Member')->addPaymentOrder($datag);
           }
           if($hyprice>0){
               $datah['status']=0;
               $datah['member_id']=$id;
               $datah['time']=time();
               $datah['endtime']=$endtime;
               
               $datah['payment_type'] = 10;
               $datah['yprice'] = $hyprice;
              
               $arr = D('Member')->addPaymentOrder($datah);
           }
           if($iyprice>0){
               $datai['status']=0;
               $datai['member_id']=$id;
               $datai['time']=time();
               $datai['endtime']=$endtime;
               
               $datai['payment_type'] = 11;
               $datai['yprice'] = $iyprice;
               $datai['month']=I('imonth');
               $datai['price']=I('iprice');
               $arr = D('Member')->addPaymentOrder($datai);
           }
           if($jyprice>0){
               $dataj['status']=0;
               $dataj['member_id']=$id;
               $dataj['time']=time();
               $dataj['endtime']=$endtime;
               
               $dataj['payment_type'] = 12;
               $dataj['yprice'] = $jyprice;
               $dataj['month']=I('jmonth');
               $dataj['price']=I('jprice');
               $arr = D('Member')->addPaymentOrder($dataj);
           }
         
            
            if($arr>0){
                $where['id']=$id;
                $member['payment']=1;
                $res=D('Member')->updateMember($where,$member);
                $wherem['zj_member.id']=$id;
                $wherem['zj_payment_order.status']=0;
                $datam=D('Member')->getPaymentInfo($wherem);
               
                $name=$datam[0]['name'];
                $endtime=$datam[0]['endtime'];
                $phone=$datam[0]['phone'];
                $content='【掌家门户】您好，你的本月物业应缴账单已通过小程序发送到您的账号，请及时登陆微信-小程序-掌家门户缴费。有问题请拨0592-2199500';
                
                $sms = $this->smsbao($content,$phone);
                if($sms==0){
                    $this->success('填写缴费单成功！',U('index'));
                }else{
                    $this->error('填写缴费单失败！');
                }
            
            } else { //操作失败，显示错误信息
                $this->error($this->showRegError($arr));
            }
        }
        $id=I('id');
        $where['zj_member.id']=$id;
        $data=D('Member')->search($where);
        $this->assign('data', $data[0]);
        $this->meta_title = '填写缴费单';
        $this->display('paymentOrder');
    }
    public function savePayment(){
     
        $endtime=I('endtime');
        $id=I('id');
        $shui=I('shui');
        $dian=I('dian');
        $ting=I('ting');
        $wuye=I('wuye');
        if (empty($shui)&&empty($dian)&&empty($ting)&&empty($wuye)) {
            $this->error('请填写缴费金额!');
        }
        if($shui<0 || $dian<0 || $ting<0 || $wuye<0){
            $this->error('缴费金额不能小于零!');
        }
        if(empty($endtime)){
            $this->error('截止日期不能为空!');
        }
        //水费添加
        if ($shui>0) {
            $data['payment_type'] = 1;
            $data['yprice'] = $shui;
            $data['status']=0;
            $data['member_id']=$id;
            $data['time']=time();
            $data['endtime']=$endtime;
            $arr = D('Member')->addPaymentOrder($data);
        }
        //电费添加
        if ($dian>0) {
           $data['payment_type'] = 2;
            $data['yprice'] = $dian;
            $data['status']=0;
            $data['member_id']=$id;
            $data['time']=time();
            $data['endtime']=$endtime;
            $arr = D('Member')->addPaymentOrder($data);
        }
        //停车费添加
        if ($ting>0) {
              $data['payment_type'] = 3;
            $data['yprice'] = $ting;
            $data['status']=0;
            $data['member_id']=$id;
            $data['time']=time();
            $data['endtime']=$endtime;
            $arr = D('Member')->addPaymentOrder($data);
        }
        //物业费添加
        if ($wuye>0) {
            $data['payment_type'] = 4;
            $data['yprice'] = $wuye;
            $data['status']=0;
            $data['member_id']=$id;
            $data['time']=time();
            $data['endtime']=$endtime;
            $arr = D('Member')->addPaymentOrder($data);
        }
        if($arr>0){
            $where['id']=$id;
            $member['payment']=1;
            $res=D('Member')->updateMember($where,$member);
                  $wherem['zj_member.id']=$id;
        $wherem['zj_payment_order.status']=0;
        $datam=D('Member')->getPaymentInfo($wherem);
        foreach ($datam as $k => $v){
            $data=$data.$v['title'].'：'.$v['yprice'].'，';
        }
        $data=str_replace('Array','', $data);
        $name=$datam[0]['name'];
        $endtime=$datam[0]['endtime'];
        $phone=$datam[0]['phone'];
        $content='【掌家门户】尊敬的'.$name.',您当月的'.$data.'请您在'.$endtime.'之前缴清。';
        $sms = $this->smsbao($content,$phone);
        if($sms==0){
            $this->success('填写缴费单成功！',U('paymentList'));
        }else{
            $this->error('填写缴费单失败！');
        }
        
        } else { //操作失败，显示错误信息
            $this->error($this->showRegError($arr));
        }
    }
    public function paymentBySmsbao(){
        $id=I('id');
        
        $wherem['zj_member.id']=$id;
        $where['member_id']=$id;
        $member=D('Member')->search($wherem);
        $payment=$member[0]['payment'];
        if($payment==2){
            $this->error('该业主已催缴！');
        }
        $where['zj_payment_order.status']=0;
        $payment = D("Member")->getPaymentInfo($where);
        $name=$payment[0]['name'];
        $endtime=$payment[0]['endtime'];
        $phone=$payment[0]['phone'];
        $content='【掌家门户】您好，你的本月物业应缴账单已通过小程序发送到您的账号，请及时登陆微信-小程序-掌家门户缴费。有问题请拨0592-2199500';
        $sms=$this->smsbao($content,$phone);
        if($sms==0){
            $map['id']=$id;
            $data['payment']=2;
            $res=D('Member')->updateMember($map,$data);
         if(0 < $res){
            $this->success('催缴成功！');
        } else { 
            $this->error('催缴失败！');
        }
        }
    }
    /**
     * 短息验证
     */
    public function smsbao($content,$phone){
       
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
        $content=$content;//要发送的短信内容
        $result=getSmsbao($content,$phone);
        return $result;
    }
    public function updatePaymentOrder(){
        $thismonth = date('m');
        $thisyear = date('Y');
        $startDay = $thisyear . '-' . $thismonth . '-1';
        $endDay = $thisyear . '-' . $thismonth . '-' . date('t', strtotime($startDay));
        $b_time  = strtotime($startDay);//当前月的月初时间戳
        $e_time  = strtotime($endDay);//当前月的月末时间戳
        $wherep['zj_payment_order.time']=array(array('egt',$b_time),array('elt',$e_time),'and');
        $payment=D('Member')->getPaymentByMid($wherep);
        if(!empty($payment)){
            $this->error('当月缴费单已填写！');
        }
        $data['payment']=0;
        $rid=getRid();
        $where= array_merge( array('residential_id' => array('in', $rid )) ,(array)$where );
        $res=D('Member')->updateMember($where,$data);
        if(0 < $res){
            $this->success('重置成功！');
        } else { 
            $this->error('重置失败！');
        }
    }
    public function paymentInfo(){
        $id=I('id');
        $thismonth = date('m');
        $thisyear = date('Y');
        $startDay = $thisyear . '-' . $thismonth . '-1';
        $endDay = $thisyear . '-' . $thismonth . '-' . date('t', strtotime($startDay));
        $b_time  = strtotime($startDay);//当前月的月初时间戳
        $e_time  = strtotime($endDay);//当前月的月末时间戳
        $where['zj_payment_order.time']=array(array('egt',$b_time),array('elt',$e_time),'and');
        $where['member_id']=$id;
        $payment = D("Member")->getPaymentInfo($where);
        foreach ($payment as $k => $v){
            if($v['payment_type']==1){
                $datasf['last_month']=$v['last_month'];
                $datasf['this_month']=$v['this_monh'];
                $datasf['practical']=$v['practical'];
                $datasf['price']=$v['price'];
                $datasf['yprice']=$v['yprice'];
            }
            if($v['payment_type']==2){
                $datadf['last_month']=$v['last_month'];
                $datadf['this_month']=$v['this_monh'];
                $datadf['practical']=$v['practical'];
                $datadf['price']=$v['price'];
                $datadf['yprice']=$v['yprice'];
            }
            if($v['payment_type']==3){
                $dataa['yprice']=$v['yprice'];
            }
            if($v['payment_type']==4){
               
                $datab['yprice']=$v['yprice'];
            }
            if($v['payment_type']==5){
               
                $datac['yprice']=$v['yprice'];
            }
            if($v['payment_type']==6){
             
                $datad['yprice']=$v['yprice'];
            }
            if($v['payment_type']==7){
               
                $datae['yprice']=$v['yprice'];
            }
            if($v['payment_type']==8){
               
                $dataf['yprice']=$v['yprice'];
            }
            if($v['payment_type']==9){
               
                $datag['yprice']=$v['yprice'];
            }
            if($v['payment_type']==10){
               
                $datah['yprice']=$v['yprice'];
            }
            if($v['payment_type']==11){
                
                $datai['month']=$v['month'];
                $datai['price']=$v['price'];
                $datai['yprice']=$v['yprice'];
            }
            if($v['payment_type']==12){
                $dataj['month']=$v['month'];
                $dataj['price']=$v['price'];
                $dataj['yprice']=$v['yprice'];
            }
        }
        $money=D('Member')->getPaymentMid($where);
        
        $data['name']=$payment[0]['name'];
        $data['floor']=$payment[0]['floor'];
        $data['household']=$payment[0]['household'];
        $data['endtime']=$payment[0]['endtime'];
        $data['acreage']=$payment[0]['acreage'];
        $this->assign('money',$money[0]);
        $this->assign('data',$data);
        $this->assign('datasf',$datasf);
        $this->assign('datadf',$datadf);
        $this->assign('dataa',$dataa);
        $this->assign('datab',$datab);
        $this->assign('datac',$datac);
        $this->assign('datad',$datad);
        $this->assign('datae',$datae);
        $this->assign('dataf',$dataf);
        $this->assign('datag',$datag);
        $this->assign('datah',$datah);
        $this->assign('datai',$datai);
        $this->assign('dataj',$dataj);
        $this->meta_title = '查看业主缴费详情';
        $this->display();
    }
    public function urgentNotice(){
        $this->meta_title = '发布紧急通知';
        $this->display();
    }

    public function memberStore(){
        $rid=I('residential');
        $phone=I('phone');
        $name=I('name');
        $authentication=I('authentication');
        if(!empty($rid)){
            $setrid= array_merge( array('residential_id' => array('in', $rid )) ,(array)$setrid );
        }
        if(!empty($phone)){
            $setrid['phone']=array('like', '%'.(string)$phone.'%');
        }
        if(!empty($name)){
            $setrid['name']=array('like', '%'.(string)$name.'%');
        }
        if(!empty($authentication)){
            $setrid['authentication']=$authentication;
        }
        if(empty($rid)){
            $rid=getRid();
            $setrid= array_merge( array('residential_id' => array('in', $rid )) ,(array)$setrid );
        }
        $setrid['zj_store_member.status']=1;
        $count=M('StoreMember')->where($setrid)->count();
        $parameter['residential']=$rid;
        $parameter['phone']=$phone;
        $parameter['name']=$name;
        $parameter['authentication']=$authentication;
        $page=getPage($count,$map,$parameter);
        $list=D('Member')->searchStore($setrid,$page);

        $show = $page->show();// 分页显示输出
        $this->assign('_page',$show);// 赋值分页输出

        $residential=getRlist();
        int_to_string($list);
        foreach ($list as $key => $value){
            $map['id'] = $value['area'];
            $res = M('StoreArea')->where($map)->select();
            $list[$key]['area'] = $res[0]['area_name'];
        }
        $this->assign('list', $list);
        $this->assign('residentiallist', $residential);
        $this->meta_title = '业主信息';
        $this->display();
    }
}

?>