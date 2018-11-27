<?php
namespace Zjadmin\Controller;
use Think\Page;
use Think\Model;
/*物业提现申请*/
class WithdrawController extends AdminController {
    public function index(){
        
        $user = session('admin_auth'); //获取当前登录的账户
        $admin_id['id'] = $user['id']; //获取当前账户id
        $uid= $user['id'];
        $withdraw=M('admin');
        if (IS_ROOT) {
            $admin_id['id']=array('neq',$uid);
            $count    = $withdraw->where($admin_id)->count();// 查询满足要求的总记录数
            $page=getPage($count);
            $show = $page->show();// 分页显示输出
            $this->assign('_page',$show);// 赋值分页输出
            $list  = $withdraw->where($admin_id)->limit($page->firstRow.','.$page->listRows)->order('id DESC')->select();
        }else{
        
            $count    = $withdraw->where($admin_id)->count();// 查询满足要求的总记录数
            $page=getPage($count);
            $show = $page->show();// 分页显示输出
            $this->assign('_page',$show);// 赋值分页输出
            $list  = $withdraw->where($admin_id)->limit($page->firstRow.','.$page->listRows)->order('id DESC')->select();
        }
        int_to_string($list);
        $this->assign('withdraw', $list); //内容
        $this->meta_title = '提现申请'; //网页标题栏
        $this->display();//重新加载列表
    }
    //提现申请表
    public function takemoney(){
        $user['id'] = session('admin_auth.id'); //获取当前登录的账户     
        $withdraw = D('admin')->publicselect($user);
        $this->assign('withdraw',$withdraw[0]);
        $this->meta_title = '提现申请';
        $this->display('takemymoney');  
    }
    public function takebonus(){
        if(IS_POST){
            $money=I('money');
            if(empty($money)){
                $this->error('提现金额不能为空！');
            }
            if($money<0){
                $this->error('提现金额不能为负数！');
            }
            $user=session('admin_auth');
            $where['admin_id']=$user['id'];
            $data['status']=0;
            $res=D('Withdraw')->updateBonus($where,$data);
            //修改actionlog
            $actionlog = array(
                'username' => $user['username'],
                'action'   => 1,
                'time'     => time(),
                'ip'       => get_client_ip(),
                'remark'   => '申请提现金额：'.$money
            );
            D('withdraw')->actionlogs($actionlog);
            $admin_id['id']=$user['id'];
            $smoney  = D('admin')->publicselect($admin_id);//现有金额
            $data = array(
                'name'             => $smoney[0]['cardname'], //账户名
                'bankcard'         => $smoney[0]['bankcard'], //银行卡
                'money' => $money,  //提现金额
                'moneytime'        => time(), //获取提交时间
                'moneyip'          => get_client_ip(), //获取ip
                'status'           => 1,
                'admin_id'         => $user['id'], //保存当前的物业的id
                'paymoney'         => $smoney[0]['price']//物业现有金额
            );
            $entry = M('withdraw')->add($data);
            if(0 < $entry){
                $this->success('申请提交成功！',U('Index/index'));
            } else { //失败，显示错误信息
                $this->error($this->showRegError($entry));
            }
        }
        $user['id'] = session('admin_auth.id'); //获取当前登录的账户
        $withdraw = D('admin')->publicselect($user);
        $this->assign('admin',$withdraw[0]);
        
        $where['admin_id']=session('admin_auth.id');
        $where['status']=1;
        $withdraw = D('Withdraw')->getBonus($where);
        $this->assign('withdraw',$withdraw[0]);
        $this->meta_title = '提现申请';
        $this->display('takebonus');
    }
    //
    public function allwithdraws(){
        $bankname=I('bankname');
        
        $money  = I('money'); //提现金额
        if(empty($money)){
            $this->error('提现金额不能为空！');
        } 
        if($money<0){
            $this->error('提现金额不能为负数！');
        }
        if(empty($bankname)){
            $this->error('提现账号不能为空！');
        }
        $user = session('admin_auth'); 
        
        $admin_id['id'] = $user['id'];
        //获取当前账号对应的payment表所有的金额
        $smoney  = D('admin')->publicselect($admin_id);//现有金额
        //修改admin表中的price和lock_price内容
        $price['price'] = $smoney[0]['price'] - $money; 
        if ($price['price']<0) {
            $this->error('提现金额大于现有金额');
            exit();
        }
        $lock_price = $money+$smoney[0]['lock_price'];
        $pricemoney = array(
            'price' => $price['price'],
            'lock_price' => $lock_price
            );
        //修改现有金额和提现金额，提现金额为冻结金额
        $adminprice = D('admin')->publicsave($admin_id,$pricemoney);
        
        //修改actionlog
        $actionlog = array(
            'username' => $user['username'],
            'action'   => 1,
            'time'     => time(),
            'ip'       => get_client_ip(),
            'remark'   => '申请提现金额：'.$money
            );
        D('withdraw')->actionlogs($actionlog);
        //把提现的数据保存
        $data = array(
             'name'             => $smoney[0]['cardname'], //账户名
             'bankcard'         => $smoney[0]['bankcard'], //银行卡
             'money' => $money,  //提现金额
             'moneytime'        => time(), //获取提交时间
             'moneyip'          => get_client_ip(), //获取ip 
             'status'           => 1,
             'admin_id'         => $user['id'], //保存当前的物业的id
             'paymoney'         => $smoney[0]['price']//物业现有金额
            ); 
        $entry = M('withdraw')->add($data);
            if(0 < $entry){ 
                $this->success('申请提交成功！',U('Index/index'));
            } else { //失败，显示错误信息
                $this->error($this->showRegError($entry));
            }

    }

}
?>