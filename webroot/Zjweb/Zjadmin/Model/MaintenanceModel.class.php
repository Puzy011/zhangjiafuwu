<?php
namespace Zjadmin\Model;
use Think\Model;

/**
 * 小区维修模型
 */
class MaintenanceModel extends Model
{
    /**
     * 获取小区维修清单
     */
    public function getMaintenance($map,$page,$order = 'id DESC'){
        $data = D("Maintenance")
        ->join('zj_member on zj_maintenance.member_id = zj_member.id')
        ->join('zj_residential on zj_member.residential_id = zj_residential.id')
        ->where($map)
        ->field('zj_maintenance.id,order_number,describe,prices,zj_maintenance.pic,name,residential_name,phone,household,floor,zj_maintenance.time,zj_maintenance.status')
        ->limit($page->firstRow.','.$page->listRows)
        ->order($order)
        ->select();
        return $data;
    
    }
    public function getCount($map,$page,$order = 'id DESC'){
        $count = D("Maintenance")
        ->join('zj_member on zj_maintenance.member_id = zj_member.id')
        ->join('zj_residential on zj_member.residential_id = zj_residential.id')
        ->where($map)
        ->field('zj_maintenance.id,describe,prices,zj_maintenance.pic,name,residential_name,phone,household,floor,zj_maintenance.time,zj_maintenance.status')
        ->count();
        return $count;
    
    }
    public function getStatus($where){
        $maintenance=M('Maintenance')
        ->join('zj_service on zj_maintenance.type = zj_service.id')
        ->join('zj_member on zj_maintenance.member_id = zj_member.id')
        ->join('zj_residential on zj_member.residential_id = zj_residential.id')
        ->where($where)
        ->field('zj_maintenance.id,title,order_number,describe,prices,zj_maintenance.pic,name,residential_name,phone,household,floor,zj_maintenance.time,zj_maintenance.status')
        ->select();
        return $maintenance;
    }
    /**
     * 更新状态
     */
    public function updateStatus($map,$data){
        $data=M('Maintenance')->where($map)->data($data)->save();
        return $data;
    }
}

?>