<?php


namespace Zjadmin\Model;
use Think\Model;

/**
 * 业主模型
 */

class MemberModel extends Model {
/**
 *修改业主信息
 */
public function updateMember($map,$data){
    $data=M("Member")->where($map)->data($data)->save();
    return $data;
}

    /**
     *修改业主信息
     */
    public function updateStoreMember($map,$data){
        $data=M("StoreMember")->where($map)->data($data)->save();
        return $data;
    }

    //修改便利店用户认证
    public function saveCancelStore($map,$data){
        $data=M("StoreMember")->where($map)->data($data)->save();
        return $data;
    }

/**
 *删除业主信息
 */
public function deleteMember($map){
    $data=M("Member")->where($map)->delete();
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
     * 获取业主详情
     */
    public function getMemberInfo($map){
        $member=M('Member')->join('zj_residential on zj_member.residential_id = zj_residential.id')
            ->field('zj_member.id,name,nickname,residential_id,residential_name,phone,floor,household,authentication,lingling_id,idcard')->where($map)->select();
        return $member;
    }

public function search($map,$page) {
        $list = M("Member")
        ->join('zj_residential on zj_member.residential_id = zj_residential.id')
        ->where($map)
        ->limit($page->firstRow.','.$page->listRows)
        ->field('zj_member.id,idcard,name,residential_name,phone,floor,household,residential_name,zj_member.status,authentication,acreage,payment,openid,mtype')
        ->order('id DESC')
        ->select();
        return $list;
    }

    public function searchStore($map,$page) {
        $list = M("StoreMember")
            ->where($map)
            ->limit($page->firstRow.','.$page->listRows)
            ->field('zj_store_member.id,is_auth,name,id_card,reg_time,status,is_owner,mobile,open_id,mobile_code,code_status,code_time,floor,household,area,credit_score')
            ->order('id DESC')
            ->select();
        return $list;
    }

    public function getMemberRelation($where,$page){
        $data=M('Member')
        ->join('zj_member_relation on zj_member.id=zj_member_relation.member_id')
        ->where($where)
        ->limit($page->firstRow.','.$page->listRows)
        ->field('zj_member.id,name,residential_name,phone,floor,household,zj_member.status,authentication')
        ->order('id DESC')
        ->select();
        return $data;
    }
    public function addPaymentOrder($data){
        $data=M('PaymentOrder')->data($data)->add();
        return $data;
    }
    public function getNotPayment($wherep,$page){
        $data=M('PaymentOrder')
        ->join('zj_member on zj_payment_order.member_id = zj_member.id')
            ->where($wherep)
            ->limit($page->firstRow.','.$page->listRows)
            ->field('member_id,name,phone,floor,household,zj_payment_order.status,yprice,payment_type')
            ->group('member_id')
            ->select();
        return $data;
    }
    public function getNotPaymentCount($wherep){
        $data=M('PaymentOrder')
        ->join('zj_member on zj_payment_order.member_id = zj_member.id')
        ->where($wherep)
        ->field('member_id,name,phone,floor,household')
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
    public function getPaymentMid($wherep,$page){
        $data=M('PaymentOrder')
        ->join('zj_member on zj_payment_order.member_id = zj_member.id')
        ->where($wherep)
        ->limit($page->firstRow.','.$page->listRows)
        ->field('member_id,name,floor,household,phone,sum(yprice) as ymoney,sum(aprice) as money,paymentime')
        ->group('member_id')
        ->select();
        return $data;
    }
    public function getPaymentByMid($wherep){
        $data=M('PaymentOrder')
        ->join('zj_member on zj_payment_order.member_id = zj_member.id')
        ->where($wherep)
        ->field('name,floor,household,phone,yprice,aprice,zj_payment_order.status,payment_type,member_id')
        ->select();
        return $data;
    }
    public function getPaymentMidDown($wherep){
        $data=M('PaymentOrder')
        ->join('zj_member on zj_payment_order.member_id = zj_member.id')
        ->where($wherep)
        ->field('name,floor,household,phone,sum(yprice) as ymoney,sum(aprice) as money,paymentime')
        ->group('member_id')
        ->select();
        return $data;
    }
}
