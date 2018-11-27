<?php

namespace Apiapp\Model;
use Think\Model;


/**
 * 业主模型
 */
class MemberModel extends Model{
	/**
	 * 添加业主
	 */
	public function add($where,$map,$update){
	    $mainid=M('Member')->where($where)->getField('id');
	    if(empty($mainid)){
	        $member=M('Member')->add($map);
	    }else{
	        $member=M('Member')->where($where)->save($update);
	    }
	    return $member;
	}

	/**
	 * 获取业主详情
	 */
	public function getMemberInfo($map){
	    $member=M('Member')->join('zj_residential on zj_member.residential_id = zj_residential.id')
	    ->field('zj_member.id,name,residential_id,residential_name,phone,floor,household,authentication,lingling_id,idcard,mtype,zj_member.pic')->where($map)->select();
	    return $member;
	}
	public function getMember($where){
	    $data=M('Member')->where($where)->select();
	    return $data;
	}
	/**
	 * 更新业主
	 */
	public function binding($map,$data){
	    $member = M("Member")->where($map)->data($data)->save();
	    return $member;
	}
	/**
	 * 获取租客数量
	 */
	public function getNumber($where){
	    $number=M('Relation')->where($where)->count();
	    return $number;
	}
	/**
	 * 查询租客
	 */
	public function searchTenant($where){
	    $data=M('Relation')->where($where)->select();
	    return $data;
	}
	/**
	 * 更新租客状态
	 */
	public function changStatus($where,$status){
	    $data=M('Relation')->where($where)->data($status)->save();
	    return $data;
	}
	/**
	 * 获取租客列表
	 * */
	public function getTenantList($where){
	    $data=M('Relation')
	           ->join('zj_member on zj_relation.vice_id = zj_member.id')
	           ->where($where)
	           ->field('vice_id,name,phone,floor,household')
	           ->select();
	    return $data;
	}
    /**
     * 添加个人用户
     */
	public function addMemberRelation($data){
	    $data=M('MemberRelation')->data($data)->add();
	}
    /**
     * 修改个人用户
     */
	public function updateMemberRelation($where,$datas){
	    $data=M('MemberRelation')->where($where)->save($datas);
	}
	/**
	 * 获取个人用户
	 */
	public function getMemberRelation($where){
	    $data=M('Member')
	    ->join('zj_member_relation on zj_member.id=zj_member_relation.member_id')
	    ->where($where)
	    ->field('name,phone,floor,household,residential_name,bankname,bankcard,wname,wphone')
	    ->select();
	    return $data;
	    
	}
	public function getMemberRes($where){
	    $data=M('Member')
	    ->join('zj_relation on zj_member.id = zj_relation.vice_id')
	    ->where($where)
	    ->field('vice_id,type,authentication')
	    ->select();
	    return $data;
	}
	public function getPaymentMoney($where){
	  $data=M('PaymentOrder')
        ->join('zj_member on zj_payment_order.member_id = zj_member.id')
        ->where($where)
        ->field('member_id,sum(yprice) as money')
        ->group('member_id')
        ->select();
        return $data;
	}
	public function getPaymentInfo($where){
	        $data=M('Member')
            ->join('zj_payment_order on zj_member.id = zj_payment_order.member_id')
            ->join('zj_payment_type on zj_payment_order.payment_type = zj_payment_type.id')
            ->where($where)
            ->field('name,endtime,title,yprice,phone,floor,household,aprice,payment_type,last_month,this_monh,practical,price,month,acreage')
            ->select();
        return $data;
	}
}

?>