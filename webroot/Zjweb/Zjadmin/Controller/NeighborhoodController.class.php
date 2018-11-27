<?php
namespace Zjadmin\Controller;

class NeighborhoodController extends AdminController
{
    public function neighborhood(){
        if(IS_POST){
            $id=session('admin_auth.id');
            $where['id']=$id;
            $admin=D('Admin')->getAdmin($where);
            $data['rid']=$admin[0]['rid'];
            $data['nickname']=$admin[0]['nickname'];
            $data['mpic']=$admin[0]['pic'];
            $data['content']=I('content');
            $data['pic']=I('pic');
            $data['time']=time();
            $data['status']=1;
            $data['admin_id']=$id;
            $res=D('Neighborhood')->addNeighoorhood($data);
            if($res>0){
                $this->success('邻里圈发布成功！',U('Neighborhood/neighborhoodList'));
            }else{
                $this->error('邻里圈发布失败！');
            }
        }
        $this->meta_title = '发布邻里圈';
        $this->display('neighborhood');
    }
    public function neighborhoodList(){
        $rid=getRid();
        $wheren= array_merge( array('rid' => array('in', $rid )) ,(array)$wheren );
        $wheren['zj_neighborhood.status']=1;
        $count    = D('Neighborhood')->getNeighborhoodCount($wheren);
        $page=getPage($count);
        $neighborhood=D('Neighborhood')->getNeighoorhoodList($wheren,$page);
        foreach ($neighborhood as $k=>$v){
            if($v['admin_id']==null){
                $mid['zj_member.id']=$v['member_id'];
                $member=D('Member')->search($mid);
                $neighborhood[$k]['name']=$member[0]['name'];
                $neighborhood[$k]['floor']=$member[0]['floor'];
                $neighborhood[$k]['household']=$member[0]['household'];
            }else{
                $aid['id']=$v['admin_id'];
                $admin=D('Admin')->getAdmin($aid);
                $neighborhood[$k]['name']=$admin[0]['nickname'];
            }
            if($v['type'] == 2){
                $maint = M('Maintenance');
                $mtr = $maint->where('time = '.$v['time'])->select();
                $neighborhood[$k['status']] = $mtr[0]['status']; //0，已收到 1，进行中 -1，已完成
            }
            $neighborhood[$k]['content'] = substr(base64_decode($v['content']),0,45);
        }
        $show = $page->show();// 分页显示输出
        $this->assign('_page',$show);// 赋值分页输出
        $this->assign('data',$neighborhood);
        $this->meta_title = '邻里圈管理';
        $this->display('neighborhoodList');
    }
    public function comment(){
        $nid=I('nid');
        $where['neig_id']=$nid;
        $where['zj_comment.status']=1;
        $comment=D('Neighborhood')->getCommentList($where);
        $this->assign('data',$comment);
        $this->meta_title = '邻里圈评论';
        $this->display('commentList');
    }
    public function deleteComment(){
        $nid=I('nid');
        $id=I('id');
        $where['id']=$id;
        $status['status']=-1;
//        $res=D('Neighborhood')->deleteComment($where,$status);
        $nei = M('Comment');
        $res = $nei->where($where)->delete();
        if($res>0){
            $map['id']=$nid;
            $neig=D('Neighborhood')->getNeighoorhoodList($map);
            $old=$neig[0]['number'];
            $data['number']=$old-1;
            $rest=D('Neighborhood')->updateNeighborhood($map,$data);
            if($rest>0){
                $this->success('评论删除成功！',U('Neighborhood/neighborhoodList'));
            }else{
                $this->error("评论删除失败");
            }
            
        }else{
            $this->error("评论删除失败");
        }
    }
    public function neighborhoodInfo(){
        $id=I('id');
        $where['zj_neighborhood.id']=$id;
        $data=D('Neighborhood')->getNeighoorhoodList($where);
        $mid=$data[0]['member_id'];
        
        if($mid==null){
            $neig=D('Neighborhood')->getNeighoorhoodInfoByaid($where);
        }else{
            $neig=D('Neighborhood')->getNeighoorhoodInfoBymid($where);
        }
        foreach ($data as $k => $v){
            $data[$k]['content'] = base64_decode($v['content']);
            $data[$k]['nickname'] = base64_decode($v['nickname']);
        }
        $this->assign('data',$data[0]);
        $this->meta_title = '邻里圈详情';
        $this->display('neighborhoodInfo');
    }
    public function deleteNeighborhood(){
        $id=I('id');
        $where['id']=$id;
        $data['status']=-1;
        $rest=D('Neighborhood')->updateNeighborhood($where,$data);
        if($rest>0){
            $this->success('邻里圈删除成功！',U('Neighborhood/neighborhoodList'));
        }else{
            $this->error("邻里圈删除失败");
        }
    }
}

?>