<?php
namespace Apiapp\Controller;
use Think\Page;
use Think\Model;

class ServiceOrderController extends BaseController
{
    /**
     * 获取服务订单列表
     */
    public function index(){
        $openid=I('openid');
        $where['openid']=$openid;
        $memberInfo=D('Member')->getMemberInfo($where);
        $memberid=$memberInfo[0]['id'];
        $map['member_id']=$memberid;
        $map['zj_service_order.status']=array('egt',-1);
        $data=D('ServiceOrder')->getOrderList($map);
        echo jsonShow(200,'获取服务订单列表成功',$data);
        
        
    }
    /**
     * 获取服务最新订单
     */
    public function newOrder(){
        $where['openid']=I('openid');
        $memberInfo=D('Member')->getMemberInfo($where);
        $memberid=$memberInfo[0]['id'];
        $map['type']=I('type');
        $map['member_id']=$memberid;
        $map['zj_service_order.status']=array('egt',-1);
        $data=D('ServiceOrder')->newOrder($map);
        if($data){
            echo jsonShow(200,'获取服务最新订单成功',$data);
        }else{
            echo jsonShow(402,'获取服务最新订单失败',$data);
        }
    }
    /**
     * 获取服务订单详情
     */
    public function getOrder(){
        $map['id'] =   I('id');
        $data=D('ServiceOrder')->getServiceOrder($map);
        echo jsonShow(200,'获取服务订单详情成功',$data);
    
    }
    /**
     * 提交服务订单
     */
    public function add(){
        $openid=I('openid');
        $price=I('price');
        $type=I('type');
        $where['openid']=$openid;
        $memberInfo=D('Member')->getMemberInfo($where);
        
        $memberid=$memberInfo[0]['id'];
        $data['name']=$memberInfo[0]['name'];
        $data['floor']=$memberInfo[0]['floor'];
        $data['household']=$memberInfo[0]['household'];
        $data['phone']=$memberInfo[0]['phone'];
        $data['member_id']=$memberid;
        $rid['id']=$memberInfo[0]['residential_id'];
        $residential = D('residential')->residential_list($rid);
        $data['residential_name']=$residential[0]['residential_name'];
        $data['residential_id']=$memberInfo[0]['residential_id'];
        $data['type']=I('type');
        $describe=I('describe');
        $str=str_replace('[','', $describe);
        $str=str_replace(']','', $str);
        $str=str_replace('"','', $str);
        $str=str_replace('：,','：', $str);
        if($type==3){
                $list = explode(",", $str);//将字符串转换成一维数组
            $count = count($list);//获取数组长度
            $arr = array();
            for($y = 0; $y < $count/4; $y++){//按长度转换成二维数组
                for($x = 0; $x < 4; $x++){
                    $arr[$y][$x] = $list[$y*4+$x];
                }
            }
            $arrs = array();
            foreach($arr as $v){
                if(!isset($arrs[$v[0]])) {
                    $arrs[$v[0]] = $v;//去除相同的数据
                }else{
                    $arrs[$v[0]][3] = $v[3];//把数量相加
                }
            }
//             foreach ($arrs as $key => $value) {
//                 unset($arrs[$key][0]);
//             }
//             print_r($arrs);
            foreach ($arrs as $v)
            {
                $v = join(",",$v); //可以用implode将一维数组转换为用逗号连接的字符串
                $temp[] = $v;
            }
            $temps="";
            foreach($temp as $v){
                $temps.=$v.",";
            }
            $temps=substr($temps,0,-1);
            $data['describe']=$temps;
        }else{
            $data['describe']=$str;
        }
        
        $data['price']=$price;
        if($price>0){
            //自动生成订单编号
            $data['order_number']='ZJ'.$type.date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
            $orderid=$data['order_number'];
            $paytype='掌家服务';
            $data['time']=time();
            $data['status']=-2;
            $wxpay=A('Wxpay')->Pay($orderid,$openid,$price,$paytype);
            $data['form_id'] = $wxpay['prepay_id'];
            $res = D('ServiceOrder')->addServiceOrder($data);

            echo jsonShow(200,$res,$wxpay);
            exit;
        }else{
            echo jsonShow(402,'提交服务订单失败',$price);
            exit();
        }
    }

    //订购早餐接口（旧接口describe字段值不规则）
    public function addBreakOrder(){
        $openid=I('openid');
        $price=I('price');
        $type=I('type');
        $where['openid']=$openid;
        $memberInfo=D('Member')->getMemberInfo($where);

        $memberid=$memberInfo[0]['id'];
        $data['name']=$memberInfo[0]['name'];
        $data['floor']=$memberInfo[0]['floor'];
        $data['household']=$memberInfo[0]['household'];
        $data['phone']=$memberInfo[0]['phone'];
        $data['member_id']=$memberid;
        $rid['id']=$memberInfo[0]['residential_id'];
        $residential = D('residential')->residential_list($rid);
        $data['residential_name']=$residential[0]['residential_name'];
        $data['residential_id']=$memberInfo[0]['residential_id'];
        $data['type']=I('type');
        $describe=I('describe');

        $break = M('Breakfast');
        $describe_arr = json_decode($describe,true);
        foreach ($describe_arr as $k => $v){
            $wh['id'] = $v['id'];
            $brow = $break->where($wh)->select();
            $bdata['sellcount'] = intval($brow[0]['sellcount']) - intval($v['num']);
            $break->where($wh)->save($bdata);
        }

        $data['describe']=$describe;
        $data['sell_id'] = I('sell_id');

        $data['price']=$price;
        if($price>0){
            //自动生成订单编号
            $data['order_number']='ZJ'.$type.date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
            $orderid=$data['order_number'];
            $paytype='掌家服务';
            $data['time']=time();
            $data['status']=-2;
            $wxpay=A('Wxpay')->Pay($orderid,$openid,$price,$paytype);
            $data['form_id'] = $wxpay['prepay_id'];
            $res = D('ServiceOrder')->addServiceOrder($data);

            echo jsonShow(200,$res,$wxpay);
            exit;
        }else{
            echo jsonShow(402,'提交服务订单失败',$price);
            exit();
        }
    }

    //根据商家名称获取早餐
    public function getBreakFast()
    {
        $openid = I('openid');
        $map['seller_id'] = I('seller_id');
        $isSeller = I('isSeller');
        $where['openid'] = $openid;
        $memberInfo = D('Member')->getMemberInfo($where);

        $break = M('Breakfast');
        $brow = $break->where($map)->order('sellcount desc')->select();

        if ($isSeller == "True") {
            //商品分类 流食 主食 手工蛋饼等
            $all_row['principle']['name'] = "主食";
            $all_row['principle']['type'] = 1;
            $all_row['liquid']['name'] = "流食";
            $all_row['liquid']['type'] = 2;
            $all_row['shougongdanbao']['name'] = "手工蛋饼";
            $all_row['shougongdanbao']['type'] = 5;
            $all_row['hesu']['name'] = "盒酥";
            $all_row['hesu']['type'] = 6;
            $all_row['yangguangbao']['name'] = "阳光堡";
            $all_row['yangguangbao']['type'] = 7;
            $all_row['sanmingzhi']['name'] = "总汇三明治";
            $all_row['sanmingzhi']['type'] = 8;
            $all_row['qingshi']['name'] = "招牌轻食";
            $all_row['qingshi']['type'] = 9;
            $all_row['yinpin']['name'] = "饮品";
            $all_row['yinpin']['type'] = 10;


            foreach ($brow as $k => $v) {
                $brow[$k]['Count'] = 0;
                if ($v['category'] == "1") {
                    $all_row['principle']['foods'][] = $brow[$k];
                } else if ($v['category'] == "2") {

                    $all_row['liquid']['foods'][] = $brow[$k];
                } else if ($v['category'] == "5") {
                    $all_row['shougongdanbao']['foods'][] = $brow[$k];
                } else if ($v['category'] == "6") {
                    $all_row['hesu']['foods'][] = $brow[$k];
                } else if ($v['category'] == "7") {
                    $all_row['yangguangbao']['foods'][] = $brow[$k];
                } else if ($v['category'] == "8") {
                    $all_row['sanmingzhi']['foods'][] = $brow[$k];
                } else if ($v['category'] == "9") {
                    $all_row['qingshi']['foods'][] = $brow[$k];
                } else {
                    $all_row['yinpin']['foods'][] = $brow[$k];
                }
            }

            //热销类
            $all_row['hot']['name'] = "热销商品";
            $all_row['hot']['type'] = 3;
            for ($i = 0; $i < 3; $i++) {
                $all_row['hot']['foods'][] = $brow[$i];
            }

            //保温袋
            $all_row['other']['name'] = "保温袋";
            $all_row['other']['type'] = 4;
            $baowen = $break->where('id=78')->select();
            foreach ($baowen as $k => $v) {
                $baowen[$k]['Count'] = 0;
                $all_row['other']['foods'][] = $baowen[$k];
            }

        } else {

            foreach ($brow as $k => $v) {
                $brow[$k]['Count'] = 0;
            }

            //热销类
            $all_row['hot']['name'] = "热销商品";
            $all_row['hot']['type'] = 3;
            for ($i = 0; $i < 9; $i++) {
                $all_row['hot']['foods'][] = $brow[$i];
            }
        }


        if ($isSeller == "True") {

            $all_row_con = [];
            if (!empty($all_row['principle']['foods'])) {
                array_push($all_row_con,$all_row['principle']);
            }
            if (!empty($all_row['liquid']['foods'])) {
                array_push($all_row_con, $all_row['liquid']);
            }
            if (!empty($all_row['shougongdanbao']['foods'])) {
                array_push($all_row_con, $all_row['shougongdanbao']);
            }
            if (!empty($all_row['hesu']['foods'])) {
                array_push($all_row_con, $all_row['hesu']);
            }
            if (!empty($all_row['yangguangbao']['foods'])) {
                array_push($all_row_con, $all_row['yangguangbao']);
            }
            if (!empty($all_row['sanmingzhi']['foods'])) {
                array_push($all_row_con, $all_row['sanmingzhi']);
            }
            if (!empty($all_row['qingshi']['foods'])) {
                array_push($all_row_con, $all_row['qingshi']);
            }
            if (!empty($all_row['yinpin']['foods'])) {
                array_push($all_row_con, $all_row['yinpin']);
            }

            array_unshift($all_row_con, $all_row['other'],$all_row['hot']);
        /*$all_row_con = array(
            $all_row['other'], $all_row['hot'],
            $all_row['principle'], $all_row['liquid'],
            $all_row['shougongdanbao'], $all_row['hesu'],
            $all_row['yangguangbao'], $all_row['sanmingzhi'],
            $all_row['qingshi'], $all_row['yinpin']);
    */

        //$all_row_con = array_filter($all_row_con1,function ($item){
        //return $item.foods.length>0;
        //});


        $all_row_json = json_encode($all_row_con, true);
        }

        else{
            $all_row_con = array($all_row['hot']);
            $all_row_json = json_encode($all_row_con,true);
        }

        echo jsonShow(200,'提交服务订单成功',$all_row_json);
    }

    //判断是否首次购买订单接口
    public function isFirstBuying(){
        $openid=I('openid');
        $type = I('type');

        $where['openid']=$openid;
        $memberInfo=D('Member')->getMemberInfo($where);

//        $map['type'] = $type;
//        $map['status'] = "-1";
//        $map['member_id'] = $memberInfo[0]['id'];

        $break = M('ServiceOrder');
        $brow = $break->where('type = '.$type.' && status != -2 && member_id = '.$memberInfo[0]['id'])->select();
        if($brow){
            echo jsonShow(200,'用户非首次购买',$brow);
        }else{
            echo jsonShow(400,'用户首次购买',$brow);
        }

    }
}

?>