<?php
namespace Apiapp\Model;
use Think\Model;
class AdminModel extends Model{
    ///payment控制器用的方法
    public function payment_save($where,$priceadmin){
      $data = M('Admin')->where($where)
                        ->save($priceadmin);
                      return $data;
    }   
    ///获取物业账号的id
    public function admin_select($where){
        $data = M('Admin')->field('id,price')
                          ->where($where)
                          ->select();
                          return $data;
    }  
}


?>