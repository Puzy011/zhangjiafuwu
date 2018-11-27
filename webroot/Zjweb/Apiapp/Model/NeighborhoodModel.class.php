<?php
namespace Apiapp\Model;
use Think\Model;

class NeighborhoodModel extends Model
{
    public function addNeighoorhood($data){
        $data=M('Neighborhood')->data($data)->add();
        return $data;
    }
    public function updateNeighborhood($where,$data){
        $data=M('Neighborhood')->where($where)->save($data);
        return $data;
    }
    public function getNeighoorhoodList($where,$order = 'id DESC'){
        $data=M('Neighborhood')
          //->join('zj_member on zj_neighborhood.rid = zj_member.residential_id')
            ->where($where)
            ->order($order)
            ->field('zj_neighborhood.id,member_id,nickname,mpic,admin_id,number,rid,content,zj_neighborhood.pic,click,zj_neighborhood.time,zj_neighborhood.type,zj_neighborhood.status,main_id,service_status,pic_2,pic_3')
            ->select();
        return $data;
    }
    public function getNeighborhoodInfo($where){
        $data=M('Neighborhood')
        //->join('zj_member on zj_neighborhood.member_id = zj_member.id')
        ->where($where)
        ->field('zj_neighborhood.id,member_id,number,admin_id,nickname,mpic,rid,title,content,zj_neighborhood.pic,click,zj_neighborhood.time,pic_2,pic_3,pic_4,pic_5,pic_6,pic_7,pic_8,pic_9,type,main_id')
        ->select();
        return $data;
    }
    public function getFabulousInfo($where){
        $data=M('Fabulous')
        ->where($where)
        ->field('id,neig_id,member_id,status,mpic,nickname,time')
        ->select();
        return $data;
    }
    public function addClick($data){
        $data=M('Fabulous')->data($data)->add();
        return $data;
    }
    public function updateClick($where,$data){
        $data=M('Fabulous')->where($where)->save($data);
        return $data;
    }
    public function addComment($data){
        $data=M('Comment')->data($data)->add();
        return $data;
    }
    public function myNeighborhood($where){
        $data=M('Neighborhood')
        ->join('zj_member on zj_neighborhood.member_id = zj_member.id')
        ->where($where)
        ->field('zj_neighborhood.id,member_id,number,admin_id,zj_neighborhood.nickname,mpic,rid,content,zj_neighborhood.pic,click,zj_neighborhood.time')
        ->select();
        return $data;
}
public function getComment($where){
    $data=M('Comment')->where($where)->field('id,neig_id,member_id,content,time,pic,nickname')->select();
    return $data;
}
public function deleteNeighborhood($map,$data){
    $data=M('Neighborhood')->where($map)->data($data)->save();
    return $data;
}
}
?>