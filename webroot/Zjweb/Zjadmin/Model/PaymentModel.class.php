<?php
namespace Zjadmin\Model;
use Think\Model;

/**
 * 支付模型
 */

class PaymentModel extends Model{
      ////输出excel表格的方法  
      public function payment_table($admin_id){
      $data=$this->join('zj_payment_type on zj_payment.type = zj_payment_type.id')
                   ->where($admin_id)
                   ->field('zj_payment.id,community,people,sum(paymoney),floor,household,time,order_number,title,zj_payment.status')
                   ->group('id')
                   ->order('id desc')
                   ->select();
      return $data;
    }
    //显示当前账户下的小区等值
    public function payment_select($where,$page){
          $data= $this->join('zj_payment_type on zj_payment.type = zj_payment_type.id')
                      ->where($where)
                      ->field('zj_payment.id,community,people,title,floor,household,paymoney,time,order_number,zj_payment.status')
                      ->order('id desc')
                      ->limit($page->firstRow.','.$page->listRows)
                      ->select();
          return $data;
        
    }
    //显示未合作小区业主缴费列表
    public function paymentNocooperation($where,$page){
        $data =M('Payment')
                ->join('zj_payment_type on zj_payment.type = zj_payment_type.id')
                ->join('zj_member_relation on zj_payment.member_id = zj_member_relation.member_id')
                ->where($where)
                ->field('zj_payment.id,community,people,title,zj_payment.floor,zj_payment.household,paymoney,zj_payment.time,order_number,zj_payment.status,wname,wphone,bankname,bankcard')
                ->order('id desc')
                ->limit($page->firstRow.','.$page->listRows)
                ->select();
        return $data;
    }
    public function nocooperationCount($where){
        $data =M('Payment')
        ->join('zj_payment_type on zj_payment.type = zj_payment_type.id')
        ->join('zj_member_relation on zj_payment.member_id = zj_member_relation.member_id')
        ->where($where)
        ->count();
        return $data;
    }
    public function updateStatus($where,$data){
        $res =M('Payment')->where($where)->save($data);
        return $res;
    }
    public function getCount($where){
        $where['status']=array('egt',1);
        $count=M('Payment')->where($where)->count();
        return $count;
    }
    public function payment_add($data){
          $data= $this->add($data);
          return $data;
    }
    public function getType($where){
        $paymentType=M('PaymentType')->where($where)->select();
        return $paymentType;
    }
    public function getPaymentModel($where){
        $data=M('PaymentModel')->where($where)->field('id,electricity,water')->select();
        return $data;
    }
    public function addPaymentModel($data){
        $data=M('PaymentModel')->data($data)->add();
        return $data;
    }
    public function savePaymentModel($where,$data){
        $data=M('PaymentModel')->where($where)->save($data);
        return $data;
    }
}

?>