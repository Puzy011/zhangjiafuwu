<?php
namespace Zjadmin\Model;
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
    public function getNeighborhoodCount($where){
        $data=M('Neighborhood')
        ->where($where)
        ->count();
        return $data;
    }
    public function getNeighoorhoodList($where,$page,$order = 'id DESC'){
        $data=M('Neighborhood')
        //->join('zj_member on zj_neighborhood.rid = zj_member.residential_id')
        ->where($where)
        ->order($order)
        ->field('zj_neighborhood.id,member_id,nickname,mpic,admin_id,number,rid,content,zj_neighborhood.pic,click,zj_neighborhood.time,main_id')
        ->limit($page->firstRow.','.$page->listRows)
        ->select();
        return $data;
    }
    public function getNeighoorhoodInfoBymid($where){
        $data=M('Neighborhood')
        ->join('zj_member on zj_neighborhood.member_id = zj_member.id')
        ->where($where)
        ->field('zj_neighborhood.id,member_id,zj_neighborhood.nickname,mpic,zj_neighborhood.admin_id,number,rid,content,zj_neighborhood.pic,click,zj_neighborhood.time')
        ->select();
        return $data;
    }
    public function getNeighoorhoodInfoByaid($where){
        $data=M('Neighborhood')
        ->join('zj_admin on zj_neighborhood.admin_id = zj_admin.id')
        ->where($where)
        ->field('zj_neighborhood.id,zj_neighborhood.nickname,mpic,zj_neighborhood.admin_id,number,content,zj_neighborhood.pic,click,zj_neighborhood.time')
        ->select();
        return $data;
    }
    public function getCommentList($where,$order = 'id DESC'){
        $data=M('Comment')
        ->join('zj_member on zj_comment.member_id = zj_member.id')
        ->where($where)
        ->order($order)
        ->field('zj_comment.id,member_id,name,content,zj_comment.time,floor,household,neig_id')
        ->select();
        return $data;
    }
    public function deleteComment($where,$data){
        $data=M('Comment')->where($where)->save($data);
        return $data;
    }
}

?>