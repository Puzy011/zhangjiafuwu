<?php
namespace Home\Controller;

class MessageController extends HomeController
{
    public function index()
    {
        $sortid=I('sortid');
        $time=time();
        if(empty($sortid)){
            $where['sortid']=1;
        }else {
            $where['sortid']=$sortid;
        }
        $where['status']=1;
        $map['status']=1;
        $map['sortid']=array('elt',4);
        $click=D('Article')->getLists($map,$order='`click` DESC');
        $article=D('Article')->getLists($where,$order = '`id` DESC');
        $this->assign('click', $click);
        $this->assign('list', $article);
        $this->display("message");
    }
    public function details()
    {
        $where['id']=I('id');
        $data=D('Article')->articleInfo($where);
        $data=$data[0];
        $this->assign('data', $data);
        $this->display("details");
    }
}

?>