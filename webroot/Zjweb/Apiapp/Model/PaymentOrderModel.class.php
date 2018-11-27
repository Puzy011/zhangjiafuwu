<?php
namespace Apiapp\Model;
use Think\Model;
class PaymentOrderModel extends Model{
	public function paymentorder_select($id){
		return $this->where($id)
					->field('water,electricity,internet,property,car')
					->find();
	}
	public function paymentorder_add($data){
		return $this->add($data);
	}

}



?>