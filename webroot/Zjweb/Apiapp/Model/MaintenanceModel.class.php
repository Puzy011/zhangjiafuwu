<?php
namespace Apiapp\Model;
use Think\Model;

/**
 * 小区维修模型
 */
class MaintenanceModel extends Model
{
    /**
     * 获取小区维修清单
     */
    public function getMaintenance($map){
        $data=M('Maintenance')->where($map)->order('id desc')->select();
        return $data;
    
    }
    /**
     * 提交小区维修订单
     */
    public function addMaintenance($data){
        $ref=M('Maintenance')->add($data);
        return $ref;
    
    }
    /**
     * 获取障碍报修最新订单
     */
    public function newOrder($map,$order = 'id DESC'){
        $data=M('Maintenance')->limit($length=1)->where($map)->order($order)->select();
        return $data;
    }
}

?>