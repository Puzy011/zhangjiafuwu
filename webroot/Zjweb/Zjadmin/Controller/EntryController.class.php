<?php
namespace Zjadmin\Controller;
use Think\Page;
use Think\Model;
        /*管家人员查看物业提现表*/
class EntryController extends AdminController {
    public function index(){
        $map['status']  =   array('egt',0);
        $withdraw=M('Withdraw');
        $count    = $withdraw->where($map)->count();// 查询满足要求的总记录数
        $page=getPage($count);
        $show = $page->show();// 分页显示输出
        $this->assign('_page',$show);// 赋值分页输出
        $list  = $withdraw->where($map)->limit($page->firstRow.','.$page->listRows)->order('id DESC')->select();
        int_to_string($list);
        $this->assign('entry', $list); //内容
        $this->meta_title = '查看提现申请'; //网页标题栏
        $this->display();//重新加载列表
    }
        public function agreement(){
                
            //获取提现的物业账号id    
            $id    = array_unique((array)I('id',0)); //获取传过来的id 0为设置的值
            $id    = is_array($id) ? implode(',',$id) : $id; //判断

            $autoid    = array_unique((array)I('autoid',0)); //获取传过来的id 0为设置的值
            $autoid    = is_array($autoid) ? implode(',',$autoid) : $autoid; //判断
            //获取提现的金额money
            $money    = array_unique((array)I('money',0)); //获取传过来的id
            $money    = is_array($money) ? implode(',',$money) : $money; //判断是否是数组
        //private function execute($id,$money,$autoid){ //改变数据库   
                //掌家实现业主提现时---操作日志
                $user = session('admin_auth');
                $actionlog = array(
                    'username' => $user['username'],
                    'action'   => 2, 
                    'time'     => time(),
                    'ip'       => get_client_ip(),
                    'remark'   => '修改提现金额：'.$money
                    );
                D('withdraw')->actionlogs($actionlog);
                $wid['id'] = $id; //传过来的物业id  假设id为5
                $moneyprice = D('admin')->publicselect($wid); //调用admin模型里面的对应的方法
                //显示与id对应的金额的值
                $pricea = $moneyprice[0]['lock_price']; //显示与id对应的金额的值
                $operation = ($pricea-$money); //总的金额减去冻结的金额
                //判断如果值为负数就不走
                if ($operation<0) {
                     $this->error('暂时无可提现金额',U('index'));
                     exit();
                }
                $lock_price['lock_price'] = $operation;

                $status['status'] = 0;
                $maps['id'] = $autoid; //把withdraw表的admin_id里面和$id一致的的status改为0  
              $uid = D('admin')->publicsave($wid,$lock_price); //调用模型里面的方法
                //成功时跳转
                if(0 < $uid){ 
                D('withdraw')->entry_save($maps,$status); //调用模型里面的方法
                $this->success('金额已经提现给物业！',U('index'));
            } else { //注册失败，显示错误信息
                $this->error('金额提现给物业失败！',U('index'));
                exit();
            }
            
        }
}
?>