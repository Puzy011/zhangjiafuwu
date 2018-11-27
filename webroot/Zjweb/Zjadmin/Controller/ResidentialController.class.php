<?php
namespace Zjadmin\Controller;
use Think\Page;
class ResidentialController extends AdminController
{
    /**
     * 获取小区列表
     */
    public function index(){
        $residential=M('Residential');
        $count    = $residential->count();// 查询满足要求的总记录数
        $page=getPage($count);
        $show = $page->show();// 分页显示输出
        $this->assign('_page',$show);// 赋值分页输出
        $list  = $residential->limit($page->firstRow.','.$page->listRows)->order('id DESC')->select();
        $this->assign('_list',$list);// 赋值数据集
        $this->meta_title = '小区列表';
        $this->display();
    }
    
    public function add(){
    if(IS_POST){
        $data['admin_id']=0;
        $data['province']=I('post.province');
        $data['city']=I('post.city');
        $data['county']=I('post.county');
        $data['address']=I('post.address');
        $data['residential_name']=I('post.residential_name');
        $data['status']=1;
        $data['pic']=I('post.pic');
        $res = D('Residential')->addResidential($data);
         if(0 < $res){ 
                    $this->success('小区添加成功！',U('index'));
            } else { //注册失败，显示错误信息
                $this->error($this->showRegError($res));
            }
        }else{
            $region = M('region');
            $condition['parentCode'] = 100000;
            $sheng = $region->where($condition)->select();
            $this->assign('sheng', $sheng);
            $this->meta_title='添加小区';
            $this->display();
        }
        
    }
    /**
     * 删除小区
     */
    public function setStatus(){
         $id = array_unique((array)I('id',0));
         $id = is_array($id) ? implode(',',$id) : $id;
         if ( empty($id) ) {
             $this->error('请选择要操作的数据!');
         }
         $where = array_merge( array('id' => array('in', $id )) ,(array)$where );
         $msg   = array_merge( array( 'success'=>'操作成功！', 'error'=>'操作失败！', 'url'=>'' ,'ajax'=>IS_AJAX) , (array)$msg );
         $res=D('Residential')->deleteResidential($where);
         if($res>0) {
             $this->success($msg['success'],$msg['url'],$msg['ajax']);
         }else{
             $this->error($msg['error'],$msg['url'],$msg['ajax']);
         }
    }
    public function edit(){
        
        $id = I('get.ids','');
        $data = D('Residential')->getResidential($id);
        $region = M('region');
        $condition['parentCode'] = 100000;
        $sheng = $region->where($condition)->select();
        $str=$data['province'];
        $code=substr($str,0,2);
        $wherecity['type']=2;
        $wherecity['code'] =array('like', (string)$code.'%');
        $city = $region->where($wherecity)->select();
        $strxian=$data['county'];
        $codexian=substr($strxian,0,4);
        $wherexian['type']=3;
        $wherexian['code'] =array('like',(string)$codexian.'%');
        $xian = $region->where($wherexian)->select();
        $this->assign('sheng', $sheng);
        $this->assign('city', $city);
        $this->assign('xian', $xian);
        $this->assign('data',$data);
        $this->meta_title = '编辑小区';
        $this->display();
    }

    
    /**
     * 修改小区
     */
    public function saveResidential(){
        if(IS_POST){
            $id=I('post.id');
            $data['province']=I('post.province');
            $data['city']=I('post.city');
            $data['county']=I('post.county');
            $data['address']=I('post.address');
            $data['residential_name']=I('post.residential_name');
            $data['pic']=I('post.pic');
            $map['id']=$id;
            $res=D("Residential")->updateResidential($map,$data);
            if($res){
                $this->success('小区修改成功！',U('index'));
            }else{
                $this->error("小区修改失败");
            }
        }
    }
    /**
     * 小区状态修改
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
                $this->forbid('Residential', $map );
                break;
            case 'resume':
                $this->resume('Residential', $map );
                break;
            case 'delete':
                $this->delete('Residential', $map );
                break;
            default:
                $this->error('参数非法');
        }
    }
}

?>