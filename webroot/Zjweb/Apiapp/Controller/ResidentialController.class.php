<?php
namespace Apiapp\Controller;

class ResidentialController
{
    public function index(){
        //返回小区的列表
        $status['status'] = 1;
        $arr = D('residential')->residential_list($status);
    
        echo jsonShow(200,'获取小区列表成功',$arr);
    }
}

?>