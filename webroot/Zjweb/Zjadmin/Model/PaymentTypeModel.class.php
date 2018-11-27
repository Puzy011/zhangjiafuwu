<?php
namespace Zjadmin\Model;
use Think\Model;

/**
 * 缴费类型模型
 */
class PaymentTypeModel extends Model
{
    /**
     *添加缴费类型
     */
    public function addPaymentType($data){
        $data =M('PaymentType')->add($data);
        return $data;
    }
    /**
     *修改缴费类型
     */
    public function updatePaymentType($map,$data){
        $data =M("PaymentType")->where($map)->data($data)->save();
        return $data;
    }
    /**
     *删除缴费类型
     */
    public function deletePaymentType($map){
        $data =M("PaymentType")->where($map)->delete();
        return $data;
    }  
}

?>