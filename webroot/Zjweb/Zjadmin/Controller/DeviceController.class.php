<?php
namespace Zjadmin\Controller;

class DeviceController extends AdminController
{
    
    public function index(){

        $user = D('admin')->where(['id' => UID])->find();
        // print_r(explode(',', $user['rid']));exit();
        //  找管理员的信息rid->where(['residential_id' => [rid ]] )
        $device=M('Device');
        $count    = $device->where(['residential_id' => ['in', $user['rid']]]  )->count();// 查询满足要求的总记录数
        //print_r($count);exit();
        $page=getPage($count);
        $show = $page->show();// 分页显示输出
        $this->assign('_page',$show);// 赋值分页输出
        $list  = $device->where(['residential_id' => ['in', $user['rid']]] )->limit($page->firstRow.','.$page->listRows)->order('id DESC')->select();
        $this->assign('list',$list);// 赋值数据集
        $this->meta_title = '设备列表';
        $this->display();
    }
    /**
     * 添加设备
     */
    public function add(){
     if(IS_POST){
    
            $token='1494298012345';
            $signature='813c8589-91a5-495e-9cab-b517b320f483';
            $deviceName=I('deviceName');
            $deviceCode=I('deviceCode');
            $residential=I('residential_id');
            $device['device_name']=$deviceName;
            $device['device_code']=$deviceCode;
            $device['residential_id']=$residential;
            $device['status']=1;
            $device['time']=time();
            
            $res=$this->addDevice($deviceName,$deviceCode,$signature,$token);
            $code=$res['statusCode'];
            if($code !=1){
                $this->error(添加设备失败！);
                exit();
            }
            $device['device_id']=$res['responseResult']['deviceId'];
            $res = D('Device')->addDevice($device);
            if(0 < $res){
                $this->success('设备添加成功！',U('index'));
            } else { //注册失败，显示错误信息
                $this->error($this->showRegError($res));
            }
        }else{
            $residential=getRlist();
            $this->assign('residential',   $residential);
            $this->meta_title='添加设备';
            $this->display();
        }
    }
    /**
     * 修改设备
     */
    public function edit(){
        $id    = array_unique((array)I('id',0));
        $id    = is_array($id) ? implode(',',$id) : $id;
        $data = M('Device')->field(true)->find($id);
        $residential=getRlist();
        $this->assign('residential',   $residential);
        $this->assign('data',$data);
        $this->meta_title = '编辑设备';
        $this->display();
    }
    /**
     * 保存设备修改
     */
    public function updateDevice(){
        
        $deviceName=I('deviceName');
        $deviceCode=I('deviceCode');
        $deviceId=I('deviceId');
        $residential=I('residential_id');
        $device['device_name']=$deviceName;
        $device['device_code']=$deviceCode;
        $device['residential_id']=$residential;
        $where['device_id']=$deviceId;
        $res=D('Device')->updateDevice($where,$device);
        if($res){
             $this->success('设备修改成功！',U('index'));
        }else{
             $this->error("设备修改失败");
        }
        
        
    }
    /**
     * 删除设备
     */
    public function deleteDevice(){
        $token='1494298012345';
        $signature='813c8589-91a5-495e-9cab-b517b320f483';
        $deviceId=I('deviceId');
        $datad['requestParam']['deviceId']=$deviceId;
        $datad['header']['signature']=$signature;
        $datad['header']['token']=$token;
        $dataJson=json_encode($datad);
        $where['device_id']=$deviceId;
        $data = array('MESSAGE'=> $dataJson);
        //发送请求
        $result=send_post('http://120.24.172.108:8889/cgi-bin/device/delDevice/F144BAEEFB10560B8A1B8D43FFEAF7D1',$data);
        $res=json_decode($result,true);
        $code=$res['statusCode'];
        if($code ==1){
            $res=D('Device')->deleteDevice($where);
            $this->success('设备删除成功！',U('index'));
        }
        echo $result;
    }
 /**
     * 添加设备
     */
    protected function addDevice($deviceName,$deviceCode,$signature,$token){
            $datad['requestParam']['deviceName']=$deviceName;
            $datad['requestParam']['deviceCode']=$deviceCode;
            $datad['header']['signature']=$signature;
            $datad['header']['token']=$token;
            $dataJson=json_encode($datad);
            $data = array('MESSAGE'=> $dataJson);
            //发送请求
            $result=send_post('http://120.24.172.108:8889/cgi-bin/device/addDevice/F144BAEEFB10560B8A1B8D43FFEAF7D1',$data);
            $res=json_decode($result,true);
            return $res;
        
    }
}

?>