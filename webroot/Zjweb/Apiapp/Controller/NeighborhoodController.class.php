<?php
namespace Apiapp\Controller;
use Think\Upload;

class NeighborhoodController extends BaseController
{
    public function addNeighoorhood(){
        $mpic=I('mpic');
        $nickname=base64_encode(I('nickname'));
        $openid=I('openid');
        $where['openid']=$openid;
        $where['authentication']=array('egt',2);
        $member=D('Member')->getMemberInfo($where);
        if(empty($member)){
            echo jsonShow(401,'邻里圈发布失败！',$member);
            exit();
        }
        $mid=$member[0]['id'];
        $rid=$member[0]['residential_id'];
        $content=base64_encode(I('content'));
        if(empty($content)){
            echo jsonShow(401,'邻里圈发布内容不能为空！',$content);
            exit();
        }
//        $pic=$this->uploadPic();
        $data['rid']=$rid;
        $data['content']=$content;
        $data['pic']=1;
        $data['pic_2'] = 1;
        $data['pic_3'] = 1;
        $data['pic_4'] = 1;
        $data['pic_5'] = 1;
        $data['pic_6'] = 1;
        $data['pic_7'] = 1;
        $data['pic_8'] = 1;
        $data['pic_9'] = 1;
        $data['time']=time();
        $data['status']=1;
        $data['member_id']=$mid;
        $data['mpic']=$mpic;
        $data['nickname']=$nickname;
        $data['number']=0;
        $data['click']=0;
        $data['type']=1;
        $res=D('Neighborhood')->addNeighoorhood($data);
        if($res>0){
            echo jsonShow(200,'邻里圈发布成功！',$res);
            exit();
        }else{
            echo jsonShow(401,'邻里圈发布失败！',$res);
            exit();
        }
        
    }

    //添加邻里圈图片
    public function updateNeiPic(){
        $openid=I('openid');
        $where['openid']=$openid;
        $where['authentication']=2;

        $member=D('Member')->getMemberInfo($where);
        if(empty($member)){
            echo jsonShow(401,'邻里圈获取失败！',$member);
            exit();
        }
        $id = I('id');
        $num = I('num');
        $pic = $this->uploadPic();
//        $data['pic']  = $pic;
//        if($num == 1){
//            $data['pic'] = $pic;
//        }else{
//            $pic_name = 'pic_'.$num;
//            $data[$pic_name] = $pic;
//        }
        switch ($num){
            case 1:
                $data['pic'] = $pic;
                break;
            case 2:
                $data['pic_2'] = $pic;
                break;
            case 3:
                $data['pic_3'] = $pic;
                break;
            case 4:
                $data['pic_4'] = $pic;
                break;
            case 5:
                $data['pic_5'] = $pic;
                break;
            case 6:
                $data['pic_6'] = $pic;
                break;
            case 7:
                $data['pic_7'] = $pic;
                break;
            case 8:
                $data['pic_8'] = $pic;
                break;
            case 9:
                $data['pic_9'] = $pic;
                break;
        }
        $res = M('Neighborhood')->where('id ='.$id)->save($data);

        if($res>0){
            echo jsonShow(200,'邻里圈发布成功！',$res);
            exit();
        }else{
            echo jsonShow(401,'邻里圈发布失败！',$res);
            exit();
        }
    }


    public function getNeighborhoodList(){
        $openid=I('openid');
        $where['openid']=$openid;
        $where['authentication']=array('egt',1);
        $member=D('Member')->getMemberInfo($where);
        if(empty($member)){
            echo jsonShow(401,'邻里圈获取失败！',$member);
            exit();
        }

        $rid['rid']=$member[0]['residential_id'];
        $rid['status']=1;
        $data=D('Neighborhood')->getNeighoorhoodList($rid);
        foreach ($data as $k=>$v){
            $data[$k]['time']=$v['time']=date('Y-m-d H:i:s',$v['time']);
            $data[$k]['nickname'] = base64_decode($v['nickname']);
            $data[$k]['content'] = base64_decode($v['content']);

            //报修状态获取
            $wherem['id'] = $data[$k]['main_id'];
            $datam = M('Maintenance')->where($wherem)->select();
            $data[$k]['service_status'] = $datam[0]['status'];

            for($i = 0;$i<9;$i++){
                $p_a = $i == 0 ? "pic" : "pic_".($i+1);
                $pic = $i == 0 ? $data[$k]['pic'] : $data[$k][$p_a];
                if($pic != null AND $pic != "1"){
                    $data[$k]['pic_arr'][] = $pic;
                }
            }
        }

        if($data>0){
            echo jsonShow(200,'邻里圈获取成功！',$data);
            exit();
        }else{
            echo jsonShow(401,'邻里圈获取失败！',$data);
            exit();
        }
    }


    public function getNeighborhoodInfo(){
        $openid=I('openid');
        $id=I('id');
        $where['openid']=$openid;
        $where['authentication']=array('egt',1);
        $member=D('Member')->getMemberInfo($where);
        if(empty($member)){
            echo jsonShow(401,'邻里圈内容获取失败！',$member);
            exit();
        }
        $map['id']=$id;
        $data=D('Neighborhood')->getNeighborhoodInfo($map);
        foreach ($data as $k=>$v){
            $data[$k]['nickname'] = base64_decode($v['nickname']);
            $data[$k]['content'] = base64_decode($v['content']);

            //报修状态获取
            $wherem['id'] = $v['main_id'];
            $datam = M('Maintenance')->where($wherem)->select();
            $data[$k]['service_status'] = $datam[0]['status'];

            $mapf['neig_id'] = $data[0]['id'];
            $mapf['member_id'] = $member[0]['id'];
            $fabu = M('Fabulous');
            $frow = $fabu->where($mapf)->select();
            $data[0]['is_good'] = empty($frow) ? false : true ;

            $data[0]['is_my'] = $member[0]['id'] == $data[0]['member_id'] ? true : false ;

            //生成图片数组用于前端循环
            for($i = 0;$i<9;$i++){
                $p_a = $i == 0 ? "pic" : "pic_".($i+1);
                $pic = $i == 0 ? $data[$k]['pic'] : $data[$k][$p_a];
                if($pic != null AND $pic != "1"){
                    $data[$k]['pic_arr'][] = "https://www.zhangjiamenhu.com/".$pic;
                }
            }
        }
        if($data>0){
            $time=$data[0]['time'];
            $data[0]['time']=date('Y-m-d', $time);
            echo jsonShow(200,'邻里圈获取成功！',$data);
            exit();
        }else{
            echo jsonShow(401,'邻里圈获取失败！',$data);
            exit();
        }
    }
    public function getMyNeighborhoodInfo(){
        $openid=I('openid');
        $id=I('id');
        if(empty($id)){
            echo jsonShow(401,'邻里圈内容获取失败！',$id);
            exit();
        }
        $where['openid']=$openid;
        $where['authentication']=array('egt',1);
        $member=D('Member')->getMemberInfo($where);
        if(empty($member)){
            echo jsonShow(401,'邻里圈内容获取失败！',$member);
            exit();
        }
        $map['id']=$id;
        $data=D('Neighborhood')->getNeighborhoodInfo($map);
        $mapc['neig_id']=$id;
        $comment=D('Neighborhood')->getComment($mapc);
        if($data>0){
            $time=$data[0]['time'];
            $data[0]['comment']=$comment;
            $data[0]['time']=date('Y-m-d', $time);

            echo jsonShow(200,'邻里圈获取成功！',$data);
            exit();
        }else{
            echo jsonShow(401,'邻里圈获取失败！',$data);
            exit();
        }
    }
    public function saveClick(){
        $mpic=I('pic');
        $nickname=I('nickname');
        $neig=I('nid');
        if(empty($neig)){
            echo jsonShow(401,'邻里圈点赞失败！',$neig);
            exit();
        }
        $openid=I('openid');
        $where['openid']=$openid;
        $where['authentication']=array('egt',1);
        $member=D('Member')->getMemberInfo($where);
        if(empty($member)){
            echo jsonShow(401,'邻里圈点赞失败！',$member);
            exit();
        }
        $mid=$member[0]['id'];
        $map['member_id']=$mid;
        $map['neig_id']=$neig;
        $fabulous=D('Neighborhood')->getFabulousInfo($map);
        if(empty($fabulous)){
            $data['member_id']=$mid;
            $data['neig_id']=$neig;
            $data['time']=time();
            $data['status']=1;
            $data['mpic']=$mpic;
            $data['nickname']=$nickname;
            $res=D('Neighborhood')->addClick($data);
            if($res>0){
                $wherec['zj_neighborhood.id']=$neig;
                $data=D('Neighborhood')->getNeighborhoodInfo($wherec);
                $number=$data[0]['click'];
                $click['click']=$number + 1;
                $resc=D('Neighborhood')->updateNeighborhood($wherec,$click);
                if($resc>0){
                    echo jsonShow(200,'点赞成功！',$resc);
                    exit();
                }else{
                    echo jsonShow(401,'点赞失败！',$resc);
                    exit();
                }
                
            }else{
                echo jsonShow(401,'点赞失败！',$res);
                exit();
            }
        }else{

            echo jsonShow(401,'已点赞！',$fabulous);
            exit();
            
        }
    }
    public function addComment(){
        $openid=I('openid');
        $nid=I('nid');
        if(empty($nid)){
            echo jsonShow(401,'邻里圈评论失败！',$nid);
            exit();
        }
        $content=I('content');
        if(empty($content)){
            echo jsonShow(401,'邻里圈评论内容不能为空！',$content);
            exit();
        }
        $pic=I('pic');
        $nickname=I('nickname');
        $where['openid']=$openid;
        $where['authentication']=array('egt',1);
        $member=D('Member')->getMemberInfo($where);
        if(empty($member)){
            echo jsonShow(401,'邻里圈评论失败！',$member);
            exit();
        }
        
        $data['neig_id']=$nid;
        $data['content']=base64_encode($content);
        $data['pic']=$member[0]['pic'];
        $data['nickname']=base64_encode($nickname);
        $data['member_id']=$member[0]['id'];
        $data['time']=time();
        $data['status']=1;
        $res=D('Neighborhood')->addComment($data);
        if($res>0){
            $wheren['id']=$nid;
            $datan=D('Neighborhood')->getNeighborhoodInfo($wheren);
            $number=$datan[0]['number'];
            $numbers['number']=$number + 1;
            $resc=D('Neighborhood')->updateNeighborhood($wheren,$numbers);
            if($resc>0){
                $map['neig_id']=$nid;
                $comment=D('Neighborhood')->getComment($map);
                echo jsonShow(200,'评论成功！',$comment);
                exit();
            }else{
                echo jsonShow(401,'评论失败！',$resc);
                exit();
            }
         
        }else{
            echo jsonShow(401,'评论失败！',$res);
            exit();
        }
    }
    public function uploadPic(){
        /* 返回标准数据 */
        $picconfig=C('PICTURE_UPLOAD');
        $Upload = new Upload($picconfig);
        $info   = $Upload->upload($_FILES);
        if($info){ //文件上传成功，记录文件信息
            foreach ($info as $key => &$value) {
                /* 记录文件信息 */
                $value['path'] = "upload/".$value['savepath'].$value['savename'];	//在模板里的url路径
            }
        }
        $data=$info['photo']['path'];
        return $data;
    }
    public function myNeighborhood(){
        $openid=I('openid');
        $where['openid']=$openid;
        $where['zj_neighborhood.status']=1;
        $data=D('Neighborhood')->myNeighborhood($where);
        foreach ($data as $k=>$v){
            $data[$k]['time']=$v['time']=date('Y-m-d H:i:s',$v['time']);
        }
        echo jsonShow(200,'邻里圈获取成功！',$data);
        exit();
    }
    public function getComment(){
        $id=I('id');
        $openid=I('openid');
        $where['openid']=$openid;
        $where['authentication']=array('egt',1);
        $member=D('Member')->getMemberInfo($where);
        if(empty($member)){
            echo jsonShow(401,'邻里圈内容获取失败！',$member);
            exit();
        }
        $map['neig_id']=$id;
        $comment=D('Neighborhood')->getComment($map);
        foreach ($comment as $k => $v){
            $comment[$k]['content'] = base64_decode($v['content']);
            $comment[$k]['nickname'] = base64_decode($v['nickname']);
            $comment[$k]['time'] = date('Y-m-d H:i:s',$v['time']);
        }

            echo jsonShow(200,'邻里圈评论获取成功！',$comment);
            exit();
        
    }
    public function deleteNeighborhood(){
        $id=I('id');
        $openid=I('openid');
        $where['openid']=$openid;
        $where['authentication']=array('egt',1);
        $member=D('Member')->getMemberInfo($where);
        if(empty($member)){
            echo jsonShow(401,'邻里圈内容获取失败！',$member);
            exit();
        }
        $map['id']=$id;
        $data['status']=-1;
        $res=D('Neighborhood')->deleteNeighborhood($map,$data);
        if($res>0){
            echo jsonShow(200,'邻里圈删除成功！',$res);
            exit();
        }else{
            echo jsonShow(401,'邻里圈删除失败！',$res);
            exit();
        }
    }
}

?>