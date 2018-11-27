<?php
namespace Zjadmin\Controller;
use Think\Upload;

class MaintenanceController extends AdminController
{
   
    /**
     * 获取小区维修列表
     */
    public function index(){
        $residential=I('residential_id');
        $status=I('status');
        $order_number=I('order_number');
      
        if(!empty($residential)){
            $map['zj_member.residential_id']=$residential;
        }
        if($status != -4 && $status !=''){
            $map['zj_maintenance.status']=$status;
        }
        if(!empty($order_number)){
            $map['order_number']=$order_number;
        }
        if(empty($residential)){
            $rid=getRid();
            $map['zj_member.residential_id']=array('in',$rid);
        
        }
        if($status=='' || $status==-4){
            $map['zj_maintenance.status']=array('egt',0);
        }
        $residential=getRlist();
        $count=D('Maintenance')->getCount($map);
        $parameter=$map;
        $page=getPage($count,$num,$parameter);
        $list=D('Maintenance')->getMaintenance($map,$page);
        $show = $page->show();// 分页显示输出
        $this->assign('_page',$show);// 赋值分页输出
        $this->assign('residentiallist', $residential);
        $this->assign('list', $list);
        $this->meta_title = '障碍报修列表';
        $this->display();
    }
    public function phoneMain(){
        $residential=I('residential_id');
        $status=I('status');
        $order_number=I('order_number');
      
        if(!empty($residential)){
            $map['zj_member.residential_id']=$residential;
        }
        if($status != -4 && $status !=''){
            $map['zj_maintenance.status']=$status;
        }
        if(!empty($order_number)){
            $map['order_number']=$order_number;
        }
        if(empty($residential)){
            $rid=getRid();
            $map['zj_member.residential_id']=array('in',$rid);
        
        }
        if($status=='' || $status==-4){
            $map['zj_maintenance.status']=array('egt',0);
        }
        $residential=getRlist();
        $count=D('Maintenance')->getCount($map);
        $parameter=$map;
        $page=getPage($count,$num,$parameter);
        $list=D('Maintenance')->getMaintenance($map,$page);
        $show = $page->show();// 分页显示输出
        $this->assign('_page',$show);// 赋值分页输出
        $this->assign('residentiallist', $residential);
        $this->assign('list', $list);
        $this->meta_title = '障碍报修列表';
        $this->display();
    }
    /**
     * 修改订单状态
     */
    public function changeStatus(){
        
        $where['zj_maintenance.id']=I('id');
        $data=D('Maintenance')->getStatus($where);
        $this->assign('data', $data[0]);
        $this->meta_title = '修改障碍报修订单';
        $this->display();
    }
    public function phoneChangeStatus(){
    
        $where['zj_maintenance.id']=I('id');
        $data=D('Maintenance')->getStatus($where);
        $this->assign('data', $data[0]);
        $this->meta_title = '修改障碍报修订单';
        $this->display();
    }
    
    public function updateStatus(){
        $id = I('id');
        $type=I('type');
        $data['status']=I('status');
        $map['id'] =   array('in',$id);
        $res=D('Maintenance')->updateStatus($map,$data);

        if(0 < $res){
            $this->postServiceStatusWithBreak($id);
            if($type==2){
                $this->success('状态修改成功！',U('phoneMain'));
            }else{
                $this->success('状态修改成功！',U('index'));
            }

        } else { //提交失败，显示错误信息
            $this->error($res);
        }
    }

    //发送小程序通知-早餐状态修改
    public function postServiceStatusWithBreak($id){

        $serviceOrder = M('Maintenance');
        $srow = $serviceOrder->where('id='.$id)->select();

        $url_acc = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxb7fd9c340ac095ef&secret=1ebd797e7414747609c29075e9308b6f";
        $acc_arr=json_decode(file_get_contents($url_acc),true);
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$acc_arr['access_token'];

        switch ($srow[0]['status']){
            case 0:
                $status = "已接单";
                break;
            case -1:
                $status = "已完成";
                break;
            case 1:
                $status = "进行中";
                break;
            default:
                $status = "进行中";
                break;
        }

        $keyword1 = "故障报修";
        $keyword2 = date('Y-m-d H:i:s',$srow[0]['time']);
        $keyword3 = $status;

        $mem = M('Member');
        $mrow = $mem->where('id='.$srow[0]['member_id'])->select();

        $arr = array(
            "touser" => $mrow[0]['openid'],
            "template_id" => "LHwWoYUO1qOnqYeFKAKUHUQMwbh65bs6MSPTThak0XQ",
            "page" => "pages/my/message/repairs/repairs",
            "form_id" => $srow[0]['form_id'],
            "data" => array(
                "keyword1" => array(
                    "value" => $keyword1,
                    'color' => "#173177"
                ),
                "keyword2" => array(
                    "value" => $keyword2,
                    'color' => "#173177"
                ),
                "keyword3" => array(
                    "value" => $keyword3,
                    'color' => "#173177"
                )
            ),
            "emphasis_keyword" => "keyword3"
        );
        $data = json_encode($arr,true);
        return $res = $this->postUrl($url,$data);
    }

    //早餐列表
    public function addBreak(){
        $break = M('Breakfast');
        $brow = $break->where('status=1')->select();
        $map['status'] = 1;
        $parameter = $map;
        $count=M('Breakfast')->where($map)->count();
        $page=getPage($count,$map,$parameter);
        $show = $page->show();// 分页显示输出
        $this->assign('_page',$show);// 赋值分页输出
        $this->assign('list', $brow);
        $this->meta_title = '早餐上架';
        $this->display();
    }

    //早餐上架
    public function addBreakAct(){
        if($_POST){
             $data['name'] = I('fname');
             $data['seller_id'] = I('seller');
             $data['category'] = I('category');
             switch (I('seller')){
                 case 1:
                     $seller = "肚子里有料";
                     break;
                 case 2:
                     $seller = "五润";
                     break;
                 case 3:
                     $seller = "掌家早餐";
                     break;
			     case 4:
                     $seller = "晨间厨房";
                     break;
                 default:
                     $seller = "判断失败";
                     break;
             }
             $data['seller'] = $seller;
             $data['price'] = I('price');
             $data['oldprice'] = I('oldprice');
             $data['description'] = I('description');
             $data['info'] = I('info');
             $data['sellcount'] = I('sellcount');
             $data['rating'] = I('rating');
             $data['regtime'] = date('Y-m-d H:i:s');
             $data['status'] = '1';
             $data['pic'] = $this->uploadPic();
             $data['icon'] = $data['pic'];
             $data['image'] = $data['pic'];

             $break = M('Breakfast');
             $res = $break->add($data);
             if(!empty($res)){
                 $this->success('早餐上架成功',U('addBreak'));
             }else{
                 $this->error('早餐上架失败',U('addBreak'));
             }
        }else{
            $this->display();
        }
    }

    //早餐删除
    public function deleteBreakFood(){
        $map['id'] = I('id');
        $break = M('Breakfast');
        $res = $break->where($map)->delete();
        if($res){
            $this->success('早餐删除成功',U('addBreak'));
        }else{
            $this->error('早餐删除失败',U('addBreak'));
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
        $data=$info[0]['path'];
        return $data;
    }

    /**
     * post提交
     * @param $url
     * @param $data Str
     */
    protected function postUrl($url,$data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_URL,$url);
        //为了支持cookie
        //curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        //返回结果
        //拒绝验证ca证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close ($ch);
        return $result;
    }

}

?>