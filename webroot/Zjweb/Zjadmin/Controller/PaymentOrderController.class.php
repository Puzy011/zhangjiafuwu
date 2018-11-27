<?php
namespace Zjadmin\Controller;

class PaymentOrderController extends AdminController
{
    public function index(){
        $map['status']  =   array('egt',0);
        $list   = $this->lists('Payment', $map);
        int_to_string($list);
        $this->assign('_list', $list);
        $this->meta_title = '缴费列表';
        $this->display();
    }
    public function printview(){
        $this->display();
    }
}

?>