<?php
namespace Apiapp\Controller;
	#缴费列表 交完后生成的给用户看的数据
class PaymentOrderController extends BaseController{
	public function index(){
		$id['openid'] = I('openid');
		$arr = D('payment_order')->paymentorder_select($id);
		echo jsonShow(200,'成功',$arr); //返回缴费的信息
	}
	#缴费订单
	public function porder(){
		//获取信息保存到缴费的订单表里面
		$id['openid'] = I('openid');  
		$conmunity = I('conmunity'); //获取小区名称
		$people = I('people'); //获取业主名字
		$water = I('water'); //获取水费
		$electricity = I('electricity');  //获取电费
		$internet = I('internet');  //获取网费
		$address = I('address');  //获取物业费
		$car = I('car');  //获取停车费
		$time = date('Y-m-d,H:i:s',time());
		$status = I('status');
		$data = array(
			'openid' => $id, //openid //$openid
			'community' => $conmunity,
			'people' => $people,
			'water' => $water,
			'electricity' => $electricity,
			'internet' => $internet,
			'property' => $property,
			'address' => $address,
			'car' => $car,
			'time' => date('Y-m-d,H:i:s',time()), 
			'status' => 1,
			);
		$arr = D('payment_order')->paymentorder_add($data);
		echo jsonShow(200,'成功','缴费成功'); //返回提交成功的信息
	}

}
?>