<?php
namespace Zjadmin\Model;
use Think\Model;

/**
 * 小区模型
 */

class ResidentialModel extends Model
{
    public function lists($status = 1, $order = 'id DESC', $field = true){
        
        $map = array('status' => $status);
        return $this->field($field)->where($map)->order($order)->select();
    }
    /**
     *添加小区信息
     */
    public function addResidential($data){
        $data=M("Residential")->add($data);
        return $data;
    }
    /**
     *修改小区信息
     */
    public function updateResidential($map,$data){
        $data=M("Residential")->where($map)->data($data)->save();
        return $data;
    }
    /**
     *根据id获取小区信息
     */
    public function getResidential($map){
        $data=M("Residential")->field(true)->find($map);
        return $data;
    }
    /**
     *删除小区
     */
    public function deleteResidential($map){
        $data=M("Residential")->where($map)->delete();
        return $data;
    }
    public function residential_address($name){
    return $address =M('residential')
                    ->where($name)
                    ->field('id,province,city,county,address,residential_name')
                    ->select();
    }
    
}

?>