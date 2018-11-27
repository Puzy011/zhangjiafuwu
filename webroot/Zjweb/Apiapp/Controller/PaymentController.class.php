<?php
namespace Apiapp\Controller;
	#缴费列表 交完后生成的给用户看的数据
class PaymentController{
	
	#缴费订单
	public function index(){
		$money = I('money');
	  	if (empty($money)) {
			echo jsonShow(411,'未输入缴费金额','未输入缴费金额'); 
			exit();
		}
		
		$openid['openid'] = I('openid'); //获取openid
		$memberInfo=D('Member')->getMember($openid);
		$authentication=$memberInfo[0]['authentication'];
		if($authentication ==0){//判断是否认证
		    echo jsonShow(400,'未进行业主认证',$authentication);
		    exit();
		}
		$residential_id = D('member')->getMemberInfo($openid); //小区的id 
		$member_floor = $residential_id[0]['floor']; //业主地址
		$member_household = $residential_id[0]['household']; //业主地址
		$id['id'] = $residential_id[0]['residential_id']; //把小区的id存入数组
		$rid = $residential_id[0]['residential_id']; //把小区的id存入数组
		
		if(empty($rid)){
		   //自动生成订单编号
		    $order_payment='ZJ'.date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
		    $members=D('member')->getMember($openid);
		    $member_id=$members[0]['id'];
		    $people = $members[0]['name']; //获取业主名字
		    $member_floor=$members[0]['floor'];
		    $member_household=$members[0]['household'];
		    $relation=D('Member')->getMemberRelation($openid);
		    $community=$relation[0]['residential_name'];
		     $data = array(
		        'member_id' => $member_id,
		        'community'=>$community, //小区名字
		        'people' => $people, //业主名字
		        'floor' => $member_floor,
		         'household' => $member_household,
		        'order_number' => $order_payment,
		        'time' => time(), //时间
		        'status'=> 0, //状态
		        'paymoney'=>$money,
		    );
		}else{
		    
		    $name = D('residential')->residential_list($id);
		    $admin_id = $name[0]['admin_id'];
		    $status = $name[0]['status'];
		    if ($status!=1) {
		        echo jsonShow(407,'当前小区不提供缴费',$status);
		        exit();
		    }
		    //自动生成订单编号
		    $order_payment='ZJ'.date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
		    
		    $community = $name[0]['residential_name']; //获取小区名称
		    $member_id = $residential_id[0]['id'];
		    $people = $residential_id[0]['name']; //获取业主名字
		    $member_floor=$residential_id[0]['floor'];
		    $member_household=$residential_id[0]['household'];
		    $data = array(
		        'admin_id' =>$admin_id,  //当前获取的物业的id
		        'member_id' => $member_id,
		        'residential_id' => $id['id'],
		        'community'=>$community, //小区名字
		        'people' => $people, //业主名字
		         'floor' => $member_floor,
		         'household' => $member_household,
		        'order_number' => $order_payment,
		        'time' => time(), //时间
		        'status'=> 0, //状态
		        'paymoney'=> $money,
		    );
		}
		$arr = D('payment')->paymentorder_add($data);
	  
        //业主提交的金额全部传至与之绑定的物业的price里面
	  	$paytype='掌家缴费';
	  	$orderid=$order_payment;
	  	$price=$money;
	  	$openid=I('openid');
	  	$wxpay=A('Wxpay')->Pay($orderid,$openid,$price,$paytype);
	  	echo jsonShow(200,$arr,$wxpay);
	  	exit;
	}

	//便利店支付发起
    public function storePayment(){
        $did = I('did');
        $openid = I('openid'); //获取openid

        if (empty($did)) {
            echo jsonShow(400,'未输入设备信息','未输入设备信息');
            exit();
        }

        $rm = M('RfidMember');
        $rmInfo = $rm->where('openid LIKE \''.$openid.'\'')->select();
        $status = $rmInfo[0]['status'];

        if($status != 1){//判断是否认证
            echo jsonShow(400,'该用户被冻结或被删除',$status);
            exit();
        }
        $ro = M('RfidOrd');
        $rorow = $ro->where('device = '.$did.' AND paystatus = 2')->select();
        if(empty($rorow)){
            echo jsonShow(400,'订单不存在',$rorow);
            exit();
        }

        $goodsJson = json_decode($rorow[0]['sid'],true);

        $rg = M('RfidGoods');
        $price = 0;
        foreach ($goodsJson as $k => $v){
            $rgr = $rg->where('id = '.$v)->select();
            $price += $rgr[0]['price'];
        }

        $oid = $rorow[0]['id'];
        $data['buyer'] = $rmInfo[0]['id'];
        $ro->where('id = '.$oid)->save($data);

        $orderid='ZJ'.date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $paytype='掌GO支付';
        $openid=I('openid');
        $wxpay=A('Wxstore')->Pay($orderid,$openid,$price,$paytype);
        echo jsonShow(200,'参数获取成功',$wxpay);
        exit;

    }

	//显示缴费后了的信息
	public function porder(){
        $openid['openid'] = I('openid');
        $member_id = D('member')->getMemberInfo($openid); //小区id
        $members_id['member_id'] = $member_id[0]['id'];
        $members_id['status']=1;
//		$arr = D('payment')->paymentorder_select($members_id); //显示指定字段
        $arr = M('payment')->where($members_id)->select();
        foreach ($arr as $key => $value) {
            $time = $value['time'];
            $arr[$key]['time'] = date('Y-m-d H:i:s',$time);
        }
        echo jsonShow(200,'成功',$arr); //返回缴费的信息
        exit();
	}

	public function wxCallBack(){
        $openid = I('openid');

        $data['payment'] = 0;
        $mem = M('Member');
        $res = $mem->where('openid = \''.$openid.'\'')->save($data);
        if($res){
            echo jsonShow(200,'成功',$res); //返回缴费的信息
            exit();
        }
    }

    //支付成功回调
    public function wxpayReback(){
        $oid = I('oid');
        $order['aprice'] = I('aprice');
        $order['status'] = I('status');
        $order['paymentime'] = time();
        $order['month'] = date('m');

        $payOrder = M('Payment');
        $prow = $payOrder->where('id = '.$oid)->select();

        $member = M('Member');

        if($order['status'] == 1 && $prow){
            echo 1;
            $res = $payOrder->where('id ='.$oid)->save($order);
            $mr['payment'] = 0;
            if($res>0){

                //对用户通知进行重置及paymentOrder进行修改
                $member->where('id = '.$prow[0]['member_id'])->save($mr);

                $paymentOrder = M('PaymentOrder')->where('status = 0 AND member_id = '.$prow[0]['member_id'])->select();
                foreach ($paymentOrder as $k => $v){
//                    echo $v['yprice'];
                    $po['aprice'] = $v['yprice'];
                    $po['status'] = 1;
                    $po['paymentime'] = time();
                    $po['month'] = date('m');
                    M('PaymentOrder')->where('id = '.$v['id'])->save($po);
                }

                echo jsonShow(200,'回调成功！',$res);
                exit();
            }else{
                echo jsonShow(401,'回调失败！',$res);
                exit();
            }
        }else{
            echo jsonShow(401,'无该订单！',$prow);
            exit();
        }
    }

    //获取家政列表
    public function getHomeServiceList(){
        $map['status'] = "1";
        $homeSerObj = M('homeservice');
        $hrow = $homeSerObj->where($map)->select();
        foreach ($hrow as $k => $v){
            $ontime = $this->timediff($begin_time=time(),$end_time=strtotime($v['over_time']));
            $hrow[$k]['all_sec'] = $ontime['all_sec'];
        }
        echo jsonShow(200,'获取家族列表成功！',$hrow);
        exit();
    }

    function timediff($begin_time,$end_time)
    {
        if($begin_time < $end_time){
            $starttime = $begin_time;
            $endtime = $end_time;
        }else{
            $starttime = $end_time;
            $endtime = $begin_time;
        }
        //计算天数
        $timediff = $endtime-$starttime;
        $days = intval($timediff/86400);
        //计算小时数
        $remain = $timediff%86400;
        $hours = intval($remain/3600);
        //计算分钟数
        $remain = $remain%3600;
        $mins = intval($remain/60);
        //计算秒数
        $secs = $remain%60;

        //计算总秒数
        $all_sec = $days * 86400 + $hours * 3600 + $mins * 60 + $secs;
        $res = array("day" => $days,"hour" => $hours,"min" => $mins,"sec" => $secs,"all_sec" => $all_sec);
        return $res;
    }
}
?>