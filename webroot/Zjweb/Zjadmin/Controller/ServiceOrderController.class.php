<?php
namespace Zjadmin\Controller;
use Think\Page;

class ServiceOrderController extends AdminController
{
    /**
     *服务订单列表生成
     */
    public function index(){
        $data=$this->service();
        $list=$data['list'];
        $page=$data['page'];
        $residential=getRlist();
        $status['status']=1;
        $service   = D('ServiceOrder')->getService($status);
        int_to_string($list);

        foreach ($list as $ke => $va){
            if($list[$ke]['type'] == 3){
                $describe = json_decode($list[$ke]['describe'],true);
                $des = "";
                foreach ($describe as $k => $v){
                    $des .= "【".$k."】: 名称：".$v['name']." 数量：".$v['num']." 单价：".$v['price']."<br>";
                }
                $list[$ke]['describe'] = $des;
            }else{

            }

        }

        $show = $page->show();// 分页显示输出
        $this->assign('_page',$show);// 赋值分页输出
        $this->assign('list', $list);
        $this->assign('servicelist', $service);
        $this->assign('residentiallist', $residential);
        $this->meta_title = '服务订单列表';
        $this->display();
        
    }
    public function phoneOrder(){
        $data=$this->service();
        
        $list=$data['list'];
        $page=$data['page'];
        
        
        $residential=getRlist();
        $status['status']=1;
        $service   = D('ServiceOrder')->getService($status);
        int_to_string($list);
        $show = $page->show();// 分页显示输出
        $this->assign('_page',$show);// 赋值分页输出
        $this->assign('list', $list);
        $this->assign('servicelist', $service);
        $this->assign('residentiallist', $residential);
        $this->meta_title = '服务订单列表';
        $this->display();
    }
    /**
     * 早餐订单
     */
    public function breakfast(){
        $setype=3;
        $data=$this->service($setype);
        $list=$data['list'];
        $page=$data['page'];
        
        $residential=getRlist();
        int_to_string($list);
        $show = $page->show();// 分页显示输出
        $this->assign('_page',$show);// 赋值分页输出
        $this->assign('list', $list);
        $this->assign('residentiallist', $residential);
        $this->meta_title = '掌家早餐订单';
        $this->display();
    }
    /**
     * 快递订单
     */
    public function courier(){
        $setype=2;
        $data=$this->service($setype);
        $list=$data['list'];
        $page=$data['page'];
    
        $residential=getRlist();
        int_to_string($list);
        $show = $page->show();// 分页显示输出
        $this->assign('_page',$show);// 赋值分页输出
        $this->assign('list', $list);
        $this->assign('residentiallist', $residential);
        $this->meta_title = '掌家快递订单';
        $this->display();
    }
    /**
     * 当日达订单
     */
    public function theday(){
        $setype=7;
        $data=$this->service($setype);
        $list=$data['list'];
        $page=$data['page'];
    
        $residential=getRlist();
        int_to_string($list);
        $show = $page->show();// 分页显示输出
        $this->assign('_page',$show);// 赋值分页输出
        $this->assign('list', $list);
        $this->assign('residentiallist', $residential);
        $this->meta_title = '当日达订单';
        $this->display();
    }
    /**
     * 车内清洁订单
     */
    public function clean(){
        $setype=1;
        $data=$this->service($setype);
        $list=$data['list'];
        $page=$data['page'];
    
        $residential=getRlist();
        int_to_string($list);
        $show = $page->show();// 分页显示输出
        $this->assign('_page',$show);// 赋值分页输出
        $this->assign('list', $list);
        $this->assign('residentiallist', $residential);
        $this->meta_title = '车内清洁订单';
        $this->display();
    }
    /**
     * 夜间洗车订单
     */
    public function washcar(){
        $setype=8;
        $data=$this->service($setype);
        $list=$data['list'];
        $page=$data['page'];
    
        $residential=getRlist();
        int_to_string($list);
        $show = $page->show();// 分页显示输出
        $this->assign('_page',$show);// 赋值分页输出
        $this->assign('list', $list);
        $this->assign('residentiallist', $residential);
        $this->meta_title = '夜间洗车订单';
        $this->display();
    }
    /**
     * 获取服务订单
     */
    public function add(){
        if(IS_POST){
            $type=I('post.type');
            $data['type']=$type;
            $data['member_id']=I('post.member_id');
            //自动生成订单编号
            $data['order_number']='ZJ'.$type.date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
            $data['describe']=I('post.describe');
            $data['price']=I('post.price');
            $data['time']=date('Y-m-d H:i:s',time());
            $data['status']=0;
            $res = M('ServiceOrder')->add($data);
            if(0 < $res){
                $this->success('订单提交成功！',U('index'));
            } else { //提交失败，显示错误信息
                $this->error($this->showRegError($res));
            }
        }else{
            $service   = $this->lists('Service');
            $this->assign('_servicelist', $service);
            $this->meta_title = '新增服务订单';
            $this->display();
        }
    }
    /**
     * 修改订单状态
     */
    public function changeStatus(){
        $where['zj_service_order.id']=I('id');
        $data=D('ServiceOrder')->getServiceOrder($where);
        $this->assign('data', $data[0]);
        $this->meta_title = '修改服务订单';
        $this->display();
    }
    /**
     * 手机修改订单状态
     */
    public function phoneChangeStatus(){
        $where['zj_service_order.id']=I('id');
        $data=D('ServiceOrder')->getServiceOrder($where);
        $this->assign('data', $data[0]);
        $this->meta_title = '修改服务订单';
        $this->display();
    }

    public function updateStatus(){
        $user = session('admin_auth');
        $id = I('id');
        $type=I('type');
        $status=I('status');
        $data['status']=I('status');
        $data['courier_number']=I('courier_number');
        $data['admin_id']=$user['id'];
        $map['id'] =   array('in',$id);
        $res=D('ServiceOrder')->updateStatus($map,$data);
        if(0 < $res){
            //修改actionlog
            $actionlog = array(
                'username' => $user['username'],
                'action'   => 3,
                'time'     => time(),
                'ip'       => get_client_ip(),
                'remark'   => '订单id：'.$id.'，状态修改为：'.$status
            );
            D('withdraw')->actionlogs($actionlog);
            $now = strtotime(date('Y-m-d'));
            $end=$now+24*3600;
            $where['type']=8;
            $where['zj_service_order.time']=array(array('egt',$now),array('elt',$end),'and');
            $where['zj_service_order.status']=-1;
            $where['admin_id']=$user['id'];
            $count=D('ServiceOrder')->searchCount($where);
            if($count > 8){
                $whereOrder['time']=array(array('egt',$now),array('elt',$end),'and');
                $whereOrder['type']=8;
                $whereOrder['admin_id']=$user['id'];
                $orderData['type']=8;
                $orderData['time']=time();
                $orderData['admin_id']=$user['id'];
                $orderData['number']=$count-8;
                $orderData['status']=1;
                $ordeRes=D('ServiceOrder')->getOrder($whereOrder);
                $orderid['id']=$ordeRes[0]['id'];
                if(!empty($ordeRes)){
                    $order=D('ServiceOrder')->saveOrder($orderid,$orderData);
                }else{
                    $order=D('ServiceOrder')->addOrder($orderData);
                }
            }
            if($type==1){
                $this->success('状态修改成功！',U('index'));
            }
            if($type==2){
                $this->success('状态修改成功！',U('index'));
            }
            if($type==3){
                $this->postServiceStatusWithBreak($id);
                $this->success('状态修改成功！',U('index'));
            }
            if($type==7){
                $this->success('状态修改成功！',U('index'));
            }
            if($type==8){
                $this->postServiceStatusWithCarWash($id);
                $this->success('状态修改成功！',U('index'));
            }
            
        } else { //提交失败，显示错误信息
            $this->error($this->showRegError($res));
        }
    }

    //发送小程序通知-洗车状态修改
    public function postServiceStatusWithCarWash($id){

        $serviceOrder = M('ServiceOrder');
        $srow = $serviceOrder->where('id='.$id)->select();

        $url_acc = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxb7fd9c340ac095ef&secret=1ebd797e7414747609c29075e9308b6f";
        $acc_arr=json_decode(file_get_contents($url_acc),true);
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$acc_arr['access_token'];

        switch ($srow[0]['status']){
            case 0:
                $status = "已完成";
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

        $keyword1 = $srow[0]['order_number'];
        $keyword2 = date('Y-m-d H:i:s',$srow[0]['time']);
        $keyword3 = "您的洗车订单服务已完成，请查收，期望下次为您服务";
        $keyword4 = $status;
        $keyword5 = $srow[0]['price']."元";

        $mem = M('Member');
        $mrow = $mem->where('id='.$srow[0]['member_id'])->select();

        $arr = array(
            "touser" => $mrow[0]['openid'],
            "template_id" => "2Gawz6T0drJtb4elxr4OP0d8OBW2O0VwrvEH8D9hbM8",
            "page" => "pages/my/message/xiangqing/xiangqing",
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
                ),
                "keyword4" => array(
                    "value" => $keyword4,
                    'color' => "#ff5027"
                ),
                "keyword5" => array(
                    "value" => $keyword5,
                    'color' => "#173177"
                )
            ),
            "emphasis_keyword" => ""
        );
        $data = json_encode($arr,true);
        return $res = $this->postUrl($url,$data);
    }

    //发送小程序通知-早餐状态完成通知
    public function postServiceStatusWithBreak($id){

        $serviceOrder = M('ServiceOrder');
        $srow = $serviceOrder->where('id='.$id)->select();

        $url_acc = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxb7fd9c340ac095ef&secret=1ebd797e7414747609c29075e9308b6f";
        $acc_arr=json_decode(file_get_contents($url_acc),true);
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$acc_arr['access_token'];

        switch ($srow[0]['status']){
            case 0:
                $status = "已完成";
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

        $describe = json_decode($srow[0]['describe'],true);
        $des = "";
        foreach ($describe as $k => $v){
            $des .= "【".$k."】: 名称：".$v['name']." 数量：".$v['num']." 单价：".$v['price'];
        }
        $srow[0]['describe'] = $des;

        $keyword1 = $srow[0]['order_number'];
        $keyword2 = $srow[0]['describe'];
        $keyword3 = "掌家早餐";
        $keyword4 = $status;
        $keyword5 = date('Y-m-d H:i:s',$srow[0]['time']);
        $keyword6 = $srow[0]['price'];
        $keyword7 = $srow[0]['name'];
        $keyword8 = $srow[0]['floor']."楼".$srow[0]['household']."户";


        //判断是否有早餐箱
        $goodsListArray = json_decode($srow[0]['describe'],true);
        foreach ($goodsListArray as $k => $v){
            if($v['id'] == 78){
                $keyword9 = "尊敬的业主，您的早餐已在您门口，我们给您放置在掌家保温箱里。祝您用餐愉快！浅水湾畔专属管家微信为：zhangjiamenhu 客服电话：05922199500";
            }else{
                $keyword9 = "尊敬的业主，您的早餐已在您门口，由于您未有掌家早餐箱，我们给您挂在门上。祝您用餐愉快！浅水湾畔专属管家微信为：zhangjiamenhu 客服电话：05922199500";
            }
        }

        $mem = M('Member');
        $mrow = $mem->where('id='.$srow[0]['member_id'])->select();

        $arr = array(
            "touser" => $mrow[0]['openid'],
            "template_id" => "2Gawz6T0drJtb4elxr4OP7GZ2pwdzDaIrq8SaQBLVxc",
            "page" => "pages/my/message/xiangqing/xiangqing",
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
                ),
                "keyword4" => array(
                    "value" => $keyword4,
                    'color' => "#ff5027"
                ),
                "keyword5" => array(
                    "value" => $keyword5,
                    'color' => "#173177"
                ),
                "keyword6" => array(
                    "value" => $keyword6,
                    'color' => "#173177"
                ),
                "keyword7" => array(
                    "value" => $keyword7,
                    'color' => "#173177"
                ),
                "keyword8" => array(
                    "value" => $keyword8,
                    'color' => "#173177"
                ),
                "keyword9" => array(
                    "value" => $keyword9,
                    'color' => "#173177"
                )
            ),
            "emphasis_keyword" => ""
        );
        $data = json_encode($arr,true);
        return $res = $this->postUrl($url,$data);
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

    public function pupdateStatus(){
        $user = session('admin_auth');
        $id = I('id');
        $type=I('type');
        $status=I('status');
        $data['status']=I('status');
        $data['courier_number']=I('courier_number');
        $data['admin_id']=$user['id'];
        $map['id'] =   array('in',$id);
        $res=D('ServiceOrder')->updateStatus($map,$data);
        if(0 < $res){
            //修改actionlog
            $actionlog = array(
                'username' => $user['username'],
                'action'   => 3,
                'time'     => time(),
                'ip'       => get_client_ip(),
                'remark'   => '订单id：'.$id.'，状态修改为：'.$status
            );
            D('withdraw')->actionlogs($actionlog);
            $now = strtotime(date('Y-m-d'));
            $end=$now+24*3600;
            $where['type']=8;
            $where['zj_service_order.time']=array(array('egt',$now),array('elt',$end),'and');
            $where['zj_service_order.status']=-1;
            $where['admin_id']=$user['id'];
            $count=D('ServiceOrder')->searchCount($where);
            if($count > 8){
                $whereOrder['time']=array(array('egt',$now),array('elt',$end),'and');
                $whereOrder['type']=8;
                $whereOrder['admin_id']=$user['id'];
                $orderData['type']=8;
                $orderData['time']=time();
                $orderData['admin_id']=$user['id'];
                $orderData['number']=$count-8;
                $orderData['status']=1;
                $ordeRes=D('ServiceOrder')->getOrder($whereOrder);
                $orderid['id']=$ordeRes[0]['id'];
                if(!empty($ordeRes)){
                    $order=D('ServiceOrder')->saveOrder($orderid,$orderData);
                }else{
                    $order=D('ServiceOrder')->addOrder($orderData);
                }
    
            }
       $this->success('状态修改成功！',U('phoneOrder'));
        } else { //提交失败，显示错误信息
            $this->error($res);
        }
    }
    public function service($setype){
        
        $type=I('type');
        $residential=I('residential_id');
        $sell_id=I('sell_id');
        $status=I('status');
        $statime=I('statime');
        $endtime=I('endtime');
        $order_number=I('order_number');
        $courier_number=I('courier_number');
        if(!empty($setype)){
            $map['type']=$setype;
        }
        if(!empty($type)){
            $map['type']=$type;
        }
        if(!empty($residential)){
            $map['zj_service_order.residential_id']=$residential;
        }
        if(!empty($sell_id)){
            $map['zj_service_order.sell_id']=$sell_id;
        }
        if($status != -4 && $status !=''){
            $map['zj_service_order.status']=$status;
        }
        if(!empty($statime)){
            $statime=strtotime($statime);
            $map['time']=array('egt',$statime);
        }
        if(!empty($endtime)){
            $endtime=strtotime($endtime);
            $map['time']=array('elt',$endtime);
        }
        if(!empty($statime) && !empty($endtime)){
            $map['time']=array(array('egt',$statime),array('elt',$endtime),'and');
        }
        if(!empty($order_number)){
            $map['order_number']=$order_number;
        }
        if(!empty($courier_number)){
            $map['courier_number']=array('like', '%'.(string)$courier_number.'%');
        }
        if(empty($residential)){
            $rid=getRid();
            $map['zj_service_order.residential_id']=array('in',$rid);
        
        }
        if($status=='' || $status==-4){
            $map['zj_service_order.status']=array('egt',0);
        }
        $count=D('ServiceOrder')->searchCount($map);//查询条数
        $parameter=$map;
        $parameter['statime']=date('Y-m-d', $statime);
        $parameter['$endtime']=date('Y-m-d', $endtime);
        $page=getPage($count,$num,$parameter);//把查询条件带入分页
        
        $list=D('ServiceOrder')->search($map,$page);
        $data['list']=$list;
        $data['page']=$page;
        return $data;
    }

    //导出当日商家订单
    public function outputSellTable(){
        $map['status'] = "1";
        $map['type'] = "3";
        $map['sell_id'] = I('sell_id');
        $service = M('ServiceOrder');
        $data = $service->where($map)->field('order_number,residential_name,floor,household,name,phone,price,')->select();
        $headArr=array("订单编号","小区","楼号","户号","户主名","手机号码","金额");
        $filename = I('sell_id') == 1 ? "肚子里有料" : "五润" ;

        $this->getExcel($filename,$headArr,$data);
        $this->display();
    }
    private    function getExcel($fileName,$headArr,$data){
        //对数据进行检验
        if(empty($data) || !is_array($data)){
            die("data must be a array");
        }
        //检查文件名
        if(empty($fileName)){
            exit;
        }
        $date = date("Y_m_d",time());
        $fileName .= "_{$date}.xls";
        //创建PHPExcel对象，注意，不能少了\
        $objPHPExcel = new \PHPExcel();
        $objProps = $objPHPExcel->getProperties();

        //设置表头
        $key = ord("A");
        foreach($headArr as $v){
            $colum = chr($key);
            $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
            $key += 1;
        }
        #设定表格的宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(13);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(13);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $column = 2;
        $objActSheet = $objPHPExcel->getActiveSheet();
        foreach($data as $key => $rows){ //行写入
            $span = ord("A");
            foreach($rows as $keyName=>$value){// 列写入
                $j = chr($span);
                $objActSheet->setCellValue($j.$column, $value);
                $span++;
            }
            $column++;
        }
        $fileName = iconv("utf-8", "gb2312", $fileName);
        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        ob_end_clean();
        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载
        exit;

    }
}

?>