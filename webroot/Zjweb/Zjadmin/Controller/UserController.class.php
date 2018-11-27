<?php

namespace Zjadmin\Controller;
use User\Api\UserApi;

/**
 * 后台用户控制器
 */
class UserController extends AdminController {

    /**
     * 用户管理首页
     */
    public function index(){
        $nickname       =   I('nickname');
        $map['status']  =   array('egt',0);
        if(is_numeric($nickname)){
            $map['id|username']=   array(intval($nickname),array('like','%'.$nickname.'%'),'_multi'=>true);
        }else{
            $map['username']    =   array('like', '%'.(string)$nickname.'%');
        }
        $admin=M('admin');
        $count    = $admin->count();// 查询满足要求的总记录数
        $page=getPage($count);
        $show = $page->show();// 分页显示输出
        $this->assign('_page',$show);// 赋值分页输出
        $list  = $admin->limit($page->firstRow.','.$page->listRows)->order('id DESC')->select();
        
        int_to_string($list);
	    $this->assign('_list', $list);
        $this->meta_title = '用户信息';
        $this->display();
    }
	
	
    /**
     * 修改管理员资料
     */
    public function updateAdmin(){
        $this->meta_title = '修改管理员资料';
		$uid=I("uid");
		if ($uid){
		 $map['id']=$uid;
		}else{
		  $map['id']=UID;
		}
		$linfo=M("admin")->where($map)->find();
        
        $this->assign('info', $linfo);
		$this->assign('uid', $uid);	
        $address = M('Article')->select();
        $this->assign('address', $address);
        $this->display();
    }
    /**
     * 修改管理员资料提交

     */
    public function submitAdmin(){
        //获取参数
        $data['proid']   =   I('post.proid');
        $data['cityid']   =   I('post.cityid');
	    $data['telephone']   =   I('post.telephone');
	    $data['address']   =   I('post.address');
	    $data['cellphone']   =   I('post.cellphone');
	    $data['contact']   =   I('post.contact');
		$uid   =   I('post.uid');
	    if ($uid){
		  $map['id']=$uid;
		}else{
		  $map['id']=UID;
		}
		
		$res=M("admin")->where($map)->data($data)->save();
		
        if($res){
            $this->success('资料修改成功！',U('index'));
        }else{
            $this->error("资料修改失败");
        }
    }
	
	/**
     * 修改密码初始化
     */
    public function updatePassword(){
        $this->meta_title = '修改密码';
        $this->display();
    }
	public function updatePhonePassword(){
	    $this->display('updatePhonePassword');
	    exit();
	}
    /**
     * 修改密码提交

     */
    public function submitPassword(){
        //获取参数
        $password   =   I('post.old');
        empty($password) && $this->error('请输入原密码');
        $data['password'] = I('post.password');
        empty($data['password']) && $this->error('请输入新密码');
        $repassword = I('post.repassword');
        empty($repassword) && $this->error('请输入确认密码');

        if($data['password'] !== $repassword){
            $this->error('您输入的新密码与确认密码不一致');
        }

        $Api    =   new UserApi();
        $res    =   $Api->updateInfo(UID, $password, $data);
        if($res['status']){
            $this->success('修改密码成功！',U('Index/index'));
        }else{
            $this->error($res['info']);
        }
    }

    /**
     * 会员状态修改
     */
    public function changeStatus($method=null){
        $id = array_unique((array)I('id',0));
        if( in_array(C('USER_ADMINISTRATOR'), $id)){
            $this->error("不允许对超级管理员执行该操作!");
        }
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
      
        $map['id'] =   array('in',$id);
        switch ( strtolower($method) ){
            case 'forbiduser':
                $this->forbid('admin', $map );
                break;
            case 'resumeuser':
                $this->resume('admin', $map );
                break;
            case 'deleteuser':
                $this->delete('admin', $map );
                break;
            default:
                $this->error('参数非法');
        }
    }
    /*
    * 新增admin用户
     */
    public function add($username = '', $password = '', $repassword = '', $email = '',$nickname = '',$cellphone = ''){
        if(IS_POST){
            /* 检测密码 */
            if($password != $repassword){
                $this->error('密码和重复密码不一致！');
            }

            /* 调用注册接口注册用户 */
            $User   =   new UserApi;
            $uid    =   $User->register($username, $password, $email,$nickname,$cellphone);
            if(0 < $uid){ //注册成功
             //   $user = array('adminid' => $uid, 'username' => $username, 'lock' => 0);
              //  if(!M('Admin')->add($user)){
              //      $this->error('用户添加失败！');
             //   } else {
                    $this->success('用户添加成功！',U('index'));
             //   }
            } else { //注册失败，显示错误信息
                $this->error($this->showRegError($uid));
            }
        } else {
            $this->meta_title = '新增用户';
            $this->display();
        }
    }

    /**
     * 获取用户注册错误信息
     * @param  integer $code 错误编码
     * @return string        错误信息
     */
    private function showRegError($code = 0){
        switch ($code) {
            case -1:  $error = '用户名长度必须在16个字符以内！'; break;
            case -2:  $error = '用户名被禁止注册！'; break;
            case -3:  $error = '用户名被占用！'; break;
            case -4:  $error = '密码长度必须在6-30个字符之间！'; break;
            case -5:  $error = '邮箱格式不正确！'; break;
            case -6:  $error = '邮箱长度必须在1-32个字符之间！'; break;
            case -7:  $error = '邮箱被禁止注册！'; break;
            case -8:  $error = '邮箱被占用！'; break;
            case -9:  $error = '手机格式不正确！'; break;
            case -10: $error = '手机被禁止注册！'; break;
            case -11: $error = '手机号被占用！'; break;
            default:  $error = '未知错误';
        }
        return $error;
    }
    
   /**
     * 获取小区列表
     */
    public function residentialList(){
        $uid=I('uid');
        
        $map['status']  =   array('egt',0);
        $map['id']=$uid;
        $rid = M('Admin')->where($map)
        ->getfield('rid');
        $list=$this->lists('Residential');
        int_to_string($list);
        $this->assign('list', $list);
        $this->assign('ridList', $rid);
        $this->assign('uid', $uid);
        $this->meta_title = '小区列表';
        $this->display();
    }
    /**
     * 物业绑定小区
     */
    public function bindingResidential($uid=null){
        $uid=strtolower($uid);
        $rid    = array_unique((array)I('ids',0));
        $rid    = is_array($rid) ? implode(',',$rid) : $rid;
        if ( empty($rid) ) {
            $this->error('请选择要操作的数据!');
        }
        $aid['admin_id']=$uid;
        $groupUid['uid']=$uid;
        $group=M('authGroupAccess')->where($groupUid)->field('group_id')->select();
        $gid=$group[0]['group_id'];
        $map=array_merge( array('id' => array('in', $rid )) ,(array)$map );
        if($gid == 6){
        $residential=D('Residential')->updateResidential($map,$aid);
        }
        $data['rid']        =   $rid;
        $where = array_merge( array('id' => array('in', $uid )) ,(array)$where );
        $res = M('Admin')->where($where)->save($data);
        if(0 < $res){
            $this->success('小区绑定成功！',U('index'));
        } else { //注册失败，显示错误信息
            $this->error($this->showRegError($res));
        }
    }
    public function property(){
        $user=session('admin_auth');
        $whereuid['id']=$user['id'];
        $admin=D('Admin')->getAdmin($whereuid);
        $rid['id']=$admin[0]['rid'];
        $rname=D('Residential')->residential_address($rid);
        $this->assign('rid', $rname[0]);
        $this->assign('user', $admin[0]);
        $this->meta_title = '物业设置';
        $this->display();
    }
    public function upPic(){
        $user=session('admin_auth');
        $uid['id']=$user['id'];
        $admin=D('Admin')->getAdmin($uid);
        $this->assign('user', $admin[0]);
        $this->meta_title = '上传头像';
        $this->display();
    }
    public function saveUppic(){
        $pic=I('pic');
        $nickname=I('nickname');
        $cellphone=I('cellphone');
        $data['pic']=$pic;
        $data['nickname']=$nickname;
        $data['cellphone']=$cellphone;
        $where['id']=session('admin_auth.id');
        $res=D('Admin')->updateAdmin($where,$data);
        if(0 < $res){
            $this->success('上传头像成功！',U('property'));
        } else { 
            $this->error('上传头像失败！');
        }
    }
    public function bindingBank(){
        if(IS_POST){
            $data['bankname']=I('bankname');
            $data['cardname']=I('cardname');
            $data['bankcard']=I('bankcard');
            $where['id']=session('admin_auth.id');
            $res=D('Admin')->updateAdmin($where,$data);
            if(0 < $res){
                $this->success('绑定成功！',U('property'));
            } else {
                $this->error('绑定失败！');
            }
        }else{
            $where['id']=session('admin_auth.id');
            $admin=D('Admin')->getAdmin($where);
            $bankcard=$admin[0]['bankcard'];
            if(!empty($bankcard)){
                $this->error('提现账号已绑定，需要修改请联系掌家客服！');
            }
            $this->meta_title = '绑定提现银行账号';
            $this->display();
        }
        
    }
    public function paymentModel(){
        if(IS_POST){
            $data['water']=I('water');
            $data['status']=1;
            $data['admin_id']=session('admin_auth.id');
            $data['electricity']=I('electricity');
            $where['admin_id']=session('admin_auth.id');
            $pmodel=D('Payment')->getPaymentModel($where);
            if(empty($pmodel)){
                $res=D('Payment')->addPaymentModel($data);
            }else{
                $res=D('Payment')->savePaymentModel($where,$data);
            }
            if($res>0){
                $this->success('设置缴费模板成功！',U('property'));
            }else{
                $this->error('设置缴费模板失败！');
            }
            
        }
        $where['admin_id']=session('admin_auth.id');
        $pmodel=D('Payment')->getPaymentModel($where);
       $this->assign('data', $pmodel[0]);
        $this->meta_title = '设置缴费模板';
        $this->display();
    }
    /**
     * 物业分红
     */
    public function bonus(){
        if(IS_POST){
            $id=I('id');
            $money=I('money');
            if(empty($money)){
                $this->error('提现金额不能为空！');
            }
            if($money<0){
                $this->error('提现金额不能为负数！');
            }
            $data['admin_id']=$id;
            $data['money']=$money;
            $data['time']=time();
            $data['status']=1;
            $res=D('Withdraw')->addBonus($data);
            if($res>0){
                $this->success('填写分红成功！',U('index'));
            }else{
                $this->error('填写分红失败！');
            }
        }
        $uid=I('uid');
        $where['id']=$uid;
        $admin=D('Admin')->getAdmin($where);
        $this->assign('data', $admin[0]);
         $this->meta_title = '填写物业分红';
        $this->display();
        
    }
    /**
     * 获取缴费类型列表
     */
    public function type($uid=null){
        if(IS_POST){
            $uid=strtolower($uid);
            $pid    = array_unique((array)I('ids',0));
            $pid    = is_array($pid) ? implode(',',$pid) : $pid;
            if ( empty($pid) ) {
                $this->error('请选择要操作的数据!');
            }
            $data['paytype']        =   $pid;
            $where['id'] = $uid;
            $res = M('Admin')->where($where)->save($data);
            if(0 < $res){
                $this->success('物业缴费设置成功！',U('index'));
            } else { //注册失败，显示错误信息
                $this->error($this->showRegError($res));
            }
        }
        $uid=I('uid');
        $map['id']=$uid;
        $pid = M('Admin')->where($map)
        ->getfield('paytype');
        $paymentType=M('PaymentType');
        $list  = $paymentType->order('id DESC')->select();
        int_to_string($list);
        $this->assign('list', $list);
        $this->assign('uid', $uid);
        $this->assign('pid', $pid);
        $this->meta_title = '缴费类型列表';
        $this->display();
    }
}

?>