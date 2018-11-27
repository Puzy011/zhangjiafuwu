<?php
namespace Apiapp\Model;
use Think\Model;

/**
 * 服务订单模型
 */
class ServiceOrderModel extends Model
{
    /**
     * 获取服务订单
     */
    public function getServiceOrder($map,$order = 'id DESC'){
        $data=M('ServiceOrder')->where($map)->order($order)->select();
        return $data;
    
    }
    /**
     * 提交服务订单
     */
    public function addServiceOrder($data){
        $res = M('ServiceOrder')->add($data);
        return $res;
    }
    /**
     * 获取服务订单列表
     */
    
    public function getOrderList($map){
        $data = M('ServiceOrder')
                ->join('zj_service on zj_service_order.type=zj_service.id')
                ->where($map)
                ->field('zj_service_order.id,title,order_number,courier_number,zj_service_order.status')
                ->select();
        return $data;
    }
    /**
     * 获取服务最新订单
     */
    public function newOrder($map,$order = 'id DESC'){
        $data=M('ServiceOrder')->limit($length=1)->where($map)->order($order)->select();
        return $data;
    }
}

?>