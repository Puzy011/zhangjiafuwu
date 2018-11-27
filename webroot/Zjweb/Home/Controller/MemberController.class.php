<?php
namespace Home\Controller;

class MemberController extends HomeController
{
    public function index()
    {
        //获取小区
        $Residential = M("Residential")->field('id as id,residential_name as name')->where('status=1')->order('id')->select();
        
        $this->assign('Residential', $Residential);
        $this->display('register');
    }
    public function submitMember(){
        //获取参数
        $data['name']   =   I('post.name');
        $data['residential_id']   =   I('post.residential');
        $data['address']   =   I('post.address');
        $data['phone']   =   I('post.phone');
        $data['status']   =   0;
        $member = M("Member")->add($data);
        if(0 < $member){
            $this->redirect('Index/index');
        } else { //注册失败，显示错误信息
            $this->error($this->showRegError($member));
        }
        
    }
}

?>