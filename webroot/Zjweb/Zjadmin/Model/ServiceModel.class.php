<?php
namespace Zjadmin\Model;
use Think\Model;

/**
 * 服务类型模型
 */
class ServiceModel extends Model
{
    /**
     *添加服务类型
     */
    public function addService($data){
        $data =M('Service')->add($data);
        return $data;
    }
    /**
     *修改服务类型
     */
    public function updateService($map,$data){
        $data =M("Service")->where($map)->data($data)->save();
        return $data;
    }
    /**
     *删除服务类型
     */
    public function deleteService($map){
        $data =M("Service")->where($map)->delete();
        return $data;
    }
}

?>