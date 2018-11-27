<?php
namespace Apiapp\Model;
use Think\Model;

class ResidentialModel extends Model
{
    ///返回小区名字和id 小程序显示小区列表用
    public function residential_list($status){
        $data = M('residential')->field('id,residential_name,status,admin_id,province,city,county,address')
        ->where($status)
        ->select();
        return $data;
    }
}

?>