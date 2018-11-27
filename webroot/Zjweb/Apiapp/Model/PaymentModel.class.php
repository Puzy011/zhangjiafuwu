<?php
namespace Apiapp\Model;
use Think\Model;
class PaymentModel extends Model{
	///显示金额和相对应的标签
	public function paymentorder_select($members_id){
		$data = M('Payment')->join('zj_payment_type on zj_payment.type = zj_payment_type.id')
							->where($members_id) 
							->field('admin_id,paymoney,title,time,order_number')
							->select();
							return $data;
	}
	///新增缴费单
	public function paymentorder_add($data){
		$datas = M('Payment')->add($data);
		return $datas;
	}

}

?>