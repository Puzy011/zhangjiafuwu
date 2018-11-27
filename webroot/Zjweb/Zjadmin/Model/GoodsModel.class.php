<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/29
 * Time: 16:45
 */

namespace Zjadmin\Model;
use Think\Model;

class GoodsModel extends Model
{
    //获取商品列表
    public function searchGoodsStore($map,$page) {
        $list = M("StoreGoodsCate")
            ->where($map)
            ->limit($page->firstRow.','.$page->listRows)
            ->field('zj_store_goods_cate.id,status,name,price,stock,add_time,manufacture_date')
            ->order('id DESC')
            ->select();
        return $list;
    }
}