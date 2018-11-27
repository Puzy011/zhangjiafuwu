<?php
namespace Zjadmin\Model;
use Think\Model;

/**
 * 订单列表模型
 */
class ServiceOrderModel extends Model{
    public function search($map,$page, $order = 'id DESC'){

        $data = M("ServiceOrder")
        ->join('zj_service on zj_service_order.type = zj_service.id')
        ->where($map)
        ->field('zj_service_order.id,type,title,name,residential_name,phone,household,floor,describe,courier_number,order_number,price,zj_service_order.time,zj_service_order.status,zj_service_order.sell_id')
        ->limit($page->firstRow.','.$page->listRows)
        ->order($order)
        ->select();
        return $data;
    }
    public function searchCount($map){
    
        $data = M("ServiceOrder")
        ->join('zj_service on zj_service_order.type = zj_service.id')
        ->where($map)
        ->count();
        return $data;
    }
    
    public function getOrder($where){
        $data=M('Order')->where($where)->select();
        return $data;
    }
    public function addOrder($data){
        $data=M('Order')->data($data)->add();
    }
    public function saveOrder($where,$data){
        $data=M('Order')->where($where)->save($data);
    }
    public function getOrderCount($where){
        $data=M('Order')->where($where)->group('admin_id')->field('sum(number) as number')->select();
        return $data;
    }
    /**
     * 更新状态
     */
  public function updateStatus($map,$data){
      $data=M('ServiceOrder')->where($map)->data($data)->save();
      return $data;
  }
  /**
   * 根据id获取服务订单
   */
  public function getServiceOrder($where){
        $data = M("ServiceOrder")
        ->join('zj_service on zj_service_order.type = zj_service.id')
        ->where($where)
        ->field('zj_service_order.id,type,title,name,residential_name,phone,household,floor,describe,courier_number,order_number,price,zj_service_order.time,zj_service_order.status')
        ->select();
        return $data;
  }
  public function getService($where){
      $service=M('Service')->where($where)->select();
      return $service;
  }
}

?>