<?php
namespace Zjadmin\Controller;
use User\Api\UserApi as UserApi;

class IndexController extends AdminController{
    /**
     * 后台首页
      */
    public function index(){
        if(UID){
            $user=session('admin_auth');
            $uid=$user['id'];
            $groupUid['uid']=$uid;
            $group=M('authGroupAccess')->where($groupUid)->field('group_id')->select();
            $gid=$group[0]['group_id'];
            $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
            $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
            
            if($gid==7){
                
                $where['admin_id']=$user['id'];
                $thismonth = date('m');
                $thisyear = date('Y');
                $startDay = $thisyear . '-' . $thismonth . '-1';
                $endDay = $thisyear . '-' . $thismonth . '-' . date('t', strtotime($startDay));
                $b_time  = strtotime($startDay);//当前月的月初时间戳
                $e_time  = strtotime($endDay);//当前月的月末时间戳
                $where['zj_service_order.time']=array(array('egt',$b_time),array('elt',$e_time),'and');
                $where['type']=3;
                $where['zj_service_order.status']=-1;
                $where['admin_id']=$uid;
                $map['admin_id']=$uid;
                $map['time']=array(array('egt',$b_time),array('elt',$e_time),'and');
                $price=D('ServiceOrder')->searchCount($where);
                $order=D('ServiceOrder')->getOrderCount($map);
                $number=$order[0]['number'];
                $washcar=$number*20;
                $data['name']=$user['username'];
                $data['prices']=$price+$washcar;
                $data['money']=$price+$washcar+3000;
                
                if ($thismonth == 1) {
                    $lastmonth = 12;
                    $lastyear = $thisyear - 1;
                } else {
                    $lastmonth = $thismonth - 1;
                    $lastyear = $thisyear;
                }
                $lastStartDay = $lastyear . '-' . $lastmonth . '-1';
                $lastEndDay = $lastyear . '-' . $lastmonth . '-' . date('t', strtotime($lastStartDay));
                $bs_time = strtotime($lastStartDay);//上个月的月初时间戳
                $es_time = strtotime($lastEndDay);//上个月的月末时间戳
                $wheres['zj_service_order.time']=array(array('egt',$bs_time),array('elt',$es_time),'and');
                $wheres['type']=3;
                $wheres['zj_service_order.status']=-1;
                $wheres['admin_id']=$uid;
                $maps['admin_id']=$uid;
                $maps['time']=array(array('egt',$bs_time),array('elt',$es_time),'and');
                $prices=D('ServiceOrder')->searchCount($wheres);
                $orders=D('ServiceOrder')->getOrderCount($maps);
                $numbers=$orders[0]['number'];
                $washcars=$numbers*20;
                $datas['name']=$user['username'];
                $datas['prices']=$prices+$washcars;
                $datas['money']=$prices+$washcars+3000;
                $this->assign('data',$data);
                $this->assign('datas',$datas);
                $this->meta_title = '管理首页';
                $this->display('index');
                exit();
            }
            if($gid==6 || $gid==5){
                $user=session('admin_auth');
                $where['author']=$user['username'];
                $whereuid['id']=$user['id'];
                $admin=D('Admin')->getAdmin($whereuid);
                
              
                $article=D('Article')->getArticle($where);
                $rid=$admin[0]['rid'];
                $wherewc['residential_id']=$rid;
                $wherewc['zj_service_order.status']=-1;
                $wherewc['type']=8;
                $wherewc['zj_service_order.time']=array(array('egt',$beginToday),array('elt',$endToday),'and');
                $wcservice=D('ServiceOrder')->searchCount($wherewc);
                $this->assign('wcservice',$wcservice);
                
                $wherebf['residential_id']=$rid;
                $wherebf['zj_service_order.status']=-1;
                $wherebf['type']=3;
                $wherebf['zj_service_order.time']=array(array('egt',$beginToday),array('elt',$endToday),'and');
                $bfservice=D('ServiceOrder')->searchCount($wherebf);
                $this->assign('bfservice',$bfservice);
                
                $wherec['residential_id']=$rid;
                $wherec['zj_service_order.status']=-1;
                $wherec['type']=2;
                $wherec['zj_service_order.time']=array(array('egt',$beginToday),array('elt',$endToday),'and');
                $cservice=D('ServiceOrder')->searchCount($wherec);
                $this->assign('cservice',$cservice);
                
                $wheretd['residential_id']=$rid;
                $wheretd['zj_service_order.status']=-1;
                $wheretd['type']=7;
                $wheretd['zj_service_order.time']=array(array('egt',$beginToday),array('elt',$endToday),'and');
                $tdservice=D('ServiceOrder')->searchCount($wheretd);
          
                $this->assign('tdservice',$tdservice);
                
                $wheregz['residential_id']=$rid;
                $wheregz['zj_maintenance.status']=-1;
                $wheregz['zj_maintenance.time']=array(array('egt',$beginToday),array('elt',$endToday),'and');
                $gzservice=D('Maintenance')->getCount($wheregz);
                
                $this->assign('gzservice',$gzservice);
                
                $time=date('Y-m-d',time());
                $wherep['endtime']=array('elt',$time);
                $wherep['zj_payment_order.status']=0;
                $wherep['zj_member.status']=1;
                $rid=getRid();
                $wherep= array_merge( array('residential_id' => array('in', $rid )) ,(array)$wherep );
                $number=D('Member')->getNotPaymentCount($wherep);
                $count=count($number);
                $page=getPage($count);
                $show = $page->show();// 分页显示输出
                $this->assign('_page',$show);// 赋值分页输出
                $member=D('Member')->getNotPayment($wherep,$page);
                $this->assign('member',$member);
                
                $whereb['admin_id']=$user['id'];
                $whereb['status']=1;
                $bonus=D('Withdraw')->getBonus($whereb);
                
                $this->assign('bonus',$bonus[0]);
                $this->assign('admin',$admin[0]);
                $this->assign('data',$article[0]);
                $this->assign('datas',$article);
                $this->meta_title = '管理首页';
                //$this->redirect('Article/index');
                $this->display('main');
                exit();
            }else{
                $this->meta_title = '管理首页';
                //$this->redirect('Article/index');
                $this->display('content');
                exit();
            }
            
          } else {
            $this->redirect('Public/login');
            exit();
        }


    }
    public function phone(){
            $user=session('admin_auth');
            $uid=$user['id'];
            $groupUid['uid']=$uid;
            $group=M('authGroupAccess')->where($groupUid)->field('group_id')->select();
            $gid=$group[0]['group_id'];
            if($gid == 7){
                $where['admin_id']=$user['id'];
                $thismonth = date('m');
                $thisyear = date('Y');
                $startDay = $thisyear . '-' . $thismonth . '-1';
                $endDay = $thisyear . '-' . $thismonth . '-' . date('t', strtotime($startDay));
                $b_time  = strtotime($startDay);//当前月的月初时间戳
                $e_time  = strtotime($endDay);//当前月的月末时间戳
                $where['zj_service_order.time']=array(array('egt',$b_time),array('elt',$e_time),'and');
                $where['type']=3;
                $where['zj_service_order.status']=-1;
                $where['admin_id']=$uid;
                $map['admin_id']=$uid;
                $map['time']=array(array('egt',$b_time),array('elt',$e_time),'and');
                $price=D('ServiceOrder')->searchCount($where);
                $order=D('ServiceOrder')->getOrderCount($map);
                $number=$order[0]['number'];
                $washcar=$number*20;
                $data['name']=$user['username'];
                $data['prices']=$price+$washcar;
                $data['money']=$price+$washcar+3000;
        
                if ($thismonth == 1) {
                    $lastmonth = 12;
                    $lastyear = $thisyear - 1;
                } else {
                    $lastmonth = $thismonth - 1;
                    $lastyear = $thisyear;
                }
                $lastStartDay = $lastyear . '-' . $lastmonth . '-1';
                $lastEndDay = $lastyear . '-' . $lastmonth . '-' . date('t', strtotime($lastStartDay));
                $bs_time = strtotime($lastStartDay);//上个月的月初时间戳
                $es_time = strtotime($lastEndDay);//上个月的月末时间戳
                $wheres['zj_service_order.time']=array(array('egt',$bs_time),array('elt',$es_time),'and');
                $wheres['type']=3;
                $wheres['zj_service_order.status']=-1;
                $wheres['admin_id']=$uid;
                $maps['admin_id']=$uid;
                $maps['time']=array(array('egt',$bs_time),array('elt',$es_time),'and');
                $prices=D('ServiceOrder')->searchCount($wheres);
                $orders=D('ServiceOrder')->getOrderCount($maps);
                $numbers=$orders[0]['number'];
                $washcars=$numbers*20;
                $datas['name']=$user['username'];
                $datas['prices']=$prices+$washcars;
                $datas['money']=$prices+$washcars+3000;
        
                $this->assign('data',$data);
                $this->assign('datas',$datas);
                $this->meta_title = '管理首页';
                $this->display('index');
            }else{
                $this->meta_title = '管理首页';
                $this->display('main');
            }
    }
    public function contactUs(){
        $this->meta_title = '联系我们';
        $this->display();
    }
    public function getServiceList(){
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

        $user=session('admin_auth');
        $whereuid['id']=$user['id'];
        $admin=D('Admin')->getAdmin($whereuid);
        $type=I('type');
        if($type==9){
            $rid=$admin[0]['rid'];
//            $map['residential_id']=$rid;
//            $map['zj_maintenance.status']=0;
//            $map['zj_maintenance.time']=array(array('egt',$beginToday),array('elt',$endToday),'and');
            $list=D('Maintenance')->getMaintenance($map);
            $this->assign('data', $list);
            $this->meta_title = '障碍报修列表';
            $this->display('serviceList');
        }
//          $rid=$admin[0]['rid'];
//        $wherewc['residential_id']=$rid;
//        $wherewc['zj_service_order.status']=0;
        $wherewc['type']=$type;
//        $wherewc['zj_service_order.time']=array(array('egt',$beginToday),array('elt',$endToday),'and');
        $data=D('ServiceOrder')->search($wherewc);

        $describe = json_decode($data[0]['describe'],true);
        $des = "";
        foreach ($describe as $k => $v){
            $des .= "【".$k."】: 名称：".$v['name']." 数量：".$v['num']." 单价：".$v['price']."<br>";
        }
        $data[0]['describe'] = $des;

        $this->assign('data',$data);
        $this->meta_title = '服务详情';
        $this->display('serviceList');
    }

    /**
     * 修改订单状态
     */
    public function changeStatus(){
        $where['zj_service_order.id']=I('id');
        $data=D('ServiceOrder')->getServiceOrder($where);
        $this->assign('data', $data[0]);
        $this->meta_title = '修改服务订单';
        $this->display();
    }

    public function token(){
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
        var_dump($access_token);
        var_dump($expires_in);
    }
}

?>