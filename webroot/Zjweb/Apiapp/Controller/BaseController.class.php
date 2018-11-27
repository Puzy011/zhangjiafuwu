<?php
namespace Apiapp\Controller;
use Think\Controller;

class BaseController extends Controller {
    /**
     * 判断是否已经认证
     */
    public function _initialize(){
        $openid['openid']=I('openid');
        $memberInfo=D('Member')->getMemberInfo($openid);
        $authentication=$memberInfo[0]['authentication'];
        $residential=$memberInfo[0]['residential_id'];
        if($authentication ==0){//判断是否认证
            echo jsonShow(400,'未进行业主认证',$authentication);
            exit();
        }
        if(empty($residential)){//判断是否绑定小区
            echo jsonShow(401,'未绑定小区',$residential);
            exit();
        }
    }

}