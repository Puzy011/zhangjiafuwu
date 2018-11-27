<?php 
  
namespace Zjadmin\Controller;
use Think\Page;
use Think\Model;

class PaymentController extends AdminController {
    public function index(){
     /*  $paymenttype=I('payment_type'); //获取选择的类型
      $user = session('admin_auth');
      $paytype['residential_id']=array('neq',0);
      $paytype['zj_payment.status']=array('egt',1);
      $where['zj_payment.status']=array('egt',1);
      $where['admin_id'] = $user['id'];
      if (IS_ROOT) {
          if($paymenttype>0){//按费用类型查询
              $paytype['type']=I('payment_type');
          }
          $count=D('Payment')->getCount($paytype);
          $parameter['payment_type']=$paymenttype;
          $page=getPage($count,$num,$parameter);
          $payment = D("Payment")->payment_select($paytype,$page);
      }else{
          if($paymenttype>0){//按费用类型查询
              $where['type']=I('payment_type'); 
          }
          $count=D('Payment')->getCount($where);
          $parameter['payment_type']=$paymenttype;
          $page=getPage($count,$num,$parameter);
          $payment = D("Payment")->payment_select($where,$page);
      }
      
        $map['status']  =   array('egt',0);
        int_to_string($payment);
        $type   = D('Payment')->getType($map);//显示下拉框的内容
        $show = $page->show();// 分页显示输出
        $this->assign('_page',$show);// 赋值分页输出
        $this->assign('payment', $payment); //显示总的物业信息
        $this->assign('paytypelist', $type); //显示下拉框的内容
        $this->meta_title = '小区缴费列表';
        $this->display();//重新加载小区列表 */
        $thismonth = date('m');
        $thisyear = date('Y');
        $startDay = $thisyear . '-' . $thismonth . '-1';
        $endDay = $thisyear . '-' . $thismonth . '-' . date('t', strtotime($startDay));
        $b_time  = strtotime($startDay);//当前月的月初时间戳
        $e_time  = strtotime($endDay);//当前月的月末时间戳
        $wherep['zj_payment_order.time']=array(array('egt',$b_time),array('elt',$e_time),'and');
        $rid=getRid();
        $wherep= array_merge( array('residential_id' => array('in', $rid )) ,(array)$wherep );
        $number=D('Member')->getPaymentMid($wherep);
        $count=count($number);
        $page=getPage($count);
        $data=D('Member')->getPaymentMid($wherep,$page);
        foreach ($data as $k => $v){
            if($v['paymentime']>0){
                $data[$k]['paymentime']=$v['paymentime']=date('Y-m-d H:i:s',$v['paymentime']);
            }else{
                $data[$k]['paymentime']='';
            }
             
        }
        $show = $page->show();// 分页显示输出
        $this->assign('_page',$show);// 赋值分页输出
        $this->assign('data',$data);
        $this->meta_title = '小区缴费列表';
        $this->display('paymentList');//重新加载小区列表 */
    }
    public function nocooperation(){
        $paymenttype=I('payment_type'); //获取选择的类型
        $status=I('status');
        $paytype['zj_payment.status']=1;
        $paytype['residential_id']=0;
            if($paymenttype>0){//按费用类型查询
                $paytype['type']=I('payment_type');
            }
            if($status != -4 && $status !=''){
                $paytype['zj_payment.status']=$status;
            }
            if($status=='' || $status==-4){
               $paytype['zj_payment.status']=1;
            }
            $count=D('Payment')->nocooperationCount($paytype);
            $parameter['payment_type']=$paymenttype;
            $page=getPage($count,$num,$parameter);
            $payment = D("Payment")->paymentNocooperation($paytype,$page);
        $map['status']  =   array('egt',0);
        int_to_string($payment);
        $type   = D('Payment')->getType($map);//显示下拉框的内容
        $show = $page->show();// 分页显示输出
        $this->assign('_page',$show);// 赋值分页输出
        $this->assign('payment', $payment); //显示总的物业信息
        $this->assign('paytypelist', $type); //显示下拉框的内容
        $this->meta_title = '未合作小区缴费列表';
        $this->display();//重新加载小区列表
    }
    public function changeStatus(){
        $where['zj_payment.id']=I('id');
        $payment=D("Payment")->paymentNocooperation($where);
        $this->assign('data', $payment[0]);
        $this->meta_title = '缴费列表状态修改';
        $this->display();//重新加载小区列表
    }
    public function updateStatus(){
        $id=I('id');
        $data['status']=I('status');
        $where['id']=$id;
        $res=D('Payment')->updateStatus($where,$data);
        if($res>0){
            $this->success('状态修改成功！',U('nocooperation'));
        }else { //提交失败，显示错误信息
            $this->error($this->showRegError($res));
        }
    }
    //导出数据列表
    public function select_table(){
         $thismonth = date('m');
        $thisyear = date('Y');
        $startDay = $thisyear . '-' . $thismonth . '-1';
        $endDay = $thisyear . '-' . $thismonth . '-' . date('t', strtotime($startDay));
        $b_time  = strtotime($startDay);//当前月的月初时间戳
        $e_time  = strtotime($endDay);//当前月的月末时间戳
        $wherep['zj_payment_order.time']=array(array('egt',$b_time),array('elt',$e_time),'and');
        $rid=getRid();
        $wherep= array_merge( array('residential_id' => array('in', $rid )) ,(array)$wherep );
        if (IS_ROOT) {
          $data=D('Member')->getPaymentMidDown();
        }else{
          $data=D('Member')->getPaymentMidDown($wherep);
        }
        foreach ($data as $k => $v){
            if($v['paymentime']>0){
                $data[$k]['paymentime']=$v['paymentime']=date('Y-m-d',$v['paymentime']);
            }else{
                $data[$k]['paymentime']='';
            }
       
        }
        //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能import导入
        import("Org.Util.PHPExcel");
        import("Org.Util.PHPExcel.Writer.Excel5");
        import("Org.Util.PHPExcel.IOFactory.php");
        $filename="缴费情况";
        $headArr=array("业主","楼号","户号","手机号码","应缴金额","实缴金额","时间");
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