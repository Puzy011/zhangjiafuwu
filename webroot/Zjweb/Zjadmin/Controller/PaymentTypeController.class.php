<?php
namespace Zjadmin\Controller;

class PaymentTypeController extends AdminController
{
    /**
     * 获取缴费类型列表
     */
    public function index(){
        $paymentType=M('PaymentType');
        $count    = $paymentType->count();// 查询满足要求的总记录数
        $page=getPage($count);
        $show = $page->show();// 分页显示输出
        $this->assign('_page',$show);// 赋值分页输出
        $list  = $paymentType->limit($page->firstRow.','.$page->listRows)->order('id DESC')->select();
        int_to_string($list);
        $this->assign('list', $list);
        $this->meta_title = '缴费类型列表';
        $this->display();
    }
    /**
     * 新增缴费类型
     */
    public function add(){
        if(IS_POST){
            $data['title']=I('post.title');
            $data['status']=1;
            $ref=D('PaymentType')->addPaymentType($data);
            if(0 < $ref){
                $this->success('缴费类型添加成功！',U('index'));
            } else { //添加失败，显示错误信息
                $this->error($this->showRegError($ref));
            }
        }else {
            $this->meta_title = '新增缴费类型';
            $this->display();
        }
    }
    /**
     *修改缴费类型
     */
    public function edit(){
        $id    = array_unique((array)I('ids',0));
        $id    = is_array($id) ? implode(',',$id) : $id;
        $data = M('PaymentType')->field(true)->find($id);
        $this->assign('data',$data);
        $this->meta_title = '编辑缴费类型';
        $this->display();
    }
    
    /**
     * 保存缴费类型修改
     */
    public function savePaymentType(){
        if(IS_POST){
            $id=I('post.id');
            $data['title']=I('post.title');
            $map['id']=$id;
             $res=D("PaymentType")->updatePaymentType($map,$data);
            if($res){
                $this->success('缴费类型修改成功！',U('index'));
            }else{
                $this->error("缴费类型修改失败");
            }
        }
    }
    /**
     * 删除缴费类型
     */
    public function setStatus(){
        $id = array_unique((array)I('id',0));
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $where = array_merge( array('id' => array('in', $id )) ,(array)$where );
        $msg   = array_merge( array( 'success'=>'操作成功！', 'error'=>'操作失败！', 'url'=>'' ,'ajax'=>IS_AJAX) , (array)$msg );
        $res=D('PaymentType')->deletePaymentType($where);
        if($res>0) {
            $this->success($msg['success'],$msg['url'],$msg['ajax']);
        }else{
            $this->error($msg['error'],$msg['url'],$msg['ajax']);
        }
    }
    /**
     * 缴费类型状态修改
     */
    public function changeStatus($method=null){
        $id = array_unique((array)I('id',0));
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
    
        $map['id'] =   array('in',$id);
        switch ( strtolower($method) ){
            case 'forbid':
                $this->forbid('PaymentType', $map );
                break;
            case 'resume':
                $this->resume('PaymentType', $map );
                break;
            case 'delete':
                $this->delete('PaymentType', $map );
                break;
            default:
                $this->error('参数非法');
        }
    }
}

?>