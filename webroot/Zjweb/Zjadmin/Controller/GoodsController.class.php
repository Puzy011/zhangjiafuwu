<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/29
 * Time: 16:25
 */

namespace Zjadmin\Controller;
use User\Api\UserApi as UserApi;

class GoodsController extends AdminController
{
    //商品模型列表
    public function goodsList(){
        $StoreGoods = M('StoreGoodsCate'); // 实例化User对象
        $count      = $StoreGoods->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出
// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $StoreGoods->order('add_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display(); // 输出模板
    }

    //商品模型添加
    public function addGoods(){
        if($_POST){
            $para['name'] = I('name');
            $para['status'] = '1';
            $para['price'] = I('price');
            $para['stock'] = I('stock');
            $para['add_time'] = date('Y-m-d H:i:s');
            $para['manufacture_date'] = I('manufacture_date');
            $para['overdue_date'] = I('overdue_date');

            $goods = M('StoreGoodsCate')->add($para);
            if($goods>0){
                $this->success('添加成功！',U('goodsList'));
            } else { //操作失败，显示错误信息
                $this->error('添加失败！');
            }
        }
        $this->display();
    }

    //商品模型删除
    public function deleteGoodsCate(){
        $para['id'] = I('id');
        $res = M('StoreGoodsCate')->where($para)->delete();
        if($res>0){
            $this->success('删除成功！',U('goodsList'));
        } else { //操作失败，显示错误信息
            $this->error('删除失败！');
        }
    }

    //商品列表显示
    public function goodsListByCate(){
        $para['cate_id'] = I('id');
        $StoreGoods = M('StoreGoods'); // 实例化User对象
        $count      = $StoreGoods->where($para)->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出
// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $StoreGoods->order('status desc')->where($para)->limit($Page->firstRow.','.$Page->listRows)->select();

        foreach ($list as $key => $value){
            $para['id'] = $value['area'];
            $area = M('StoreArea');
            $res = $area->where($para)->select();
            $list[$key]['area'] = $res[0]['area_name'];
        }

        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('cate_id',$para['cate_id']);
        $this->display(); // 输出模板
    }

    //单个商品删除
    public function deleteGoods(){
        $para['id'] = I('id');
        $Goods = M('StoreGoods');
        $res = $Goods->where($para)->delete();

        $grow = $Goods->where($para)->select();
        if($res>0){
            $this->success('删除成功！',U('Goods/goodsListByCate/id/'.$grow['id']));
        } else { //操作失败，显示错误信息
            $this->error('删除失败！');
        }
    }

    //商品模型添加
    public function addGoodsByCate(){
        $id = I('id');
        $para['cate_id'] = $id;

        $condi['status'] = '1';
        $area = M('StoreArea');
        $res = $area->where($condi)->select();
        $this->assign('res',$res);
        $this->assign('cate_id',$id);

        if($_POST){
            $para['area'] = I('area');
            $para['rfid_code'] = I('rfid_code');
            $para['status'] = '1';
            $para['cate_id'] = I('cate_id');
            $goods = M('StoreGoods')->add($para);
            if($goods>0){
                $this->success('添加成功！',U('Goods/goodsListByCate/id/'.I('cate_id')));
            } else { //操作失败，显示错误信息
                $this->error('添加失败！');
            }
        }
        $this->display();
    }

    //实时门店人员管理
    public function storeLive(){
        $StoreGoods = M('StoreLive'); // 实例化User对象
        $count      = $StoreGoods->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出
// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $StoreGoods->order('id asc')->limit($Page->firstRow.','.$Page->listRows)->select();

        foreach ($list as $key => $value){
            $para['id'] = $value['area'];
            $area = M('StoreArea');
            $res = $area->where($para)->select();
            $list[$key]['area'] = $res[0]['area_name'];
        }


        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display(); // 输出模板
    }

    //地区管理
    public function storeManage(){
        $para['status'] = "1";
        $StoreArea = M('StoreArea');
        $count = $StoreArea->where($para)->count();
        $Page       = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出
// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $StoreArea->order('id asc')->where($para)->limit($Page->firstRow.','.$Page->listRows)->select();


        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display(); // 输出模板
    }

    //删除地区
    public function deleteArea(){
        $para['id'] = I('id');
        $res = M('StoreArea')->where($para)->delete();
        if($res>0){
            $this->success('删除成功！',U('storeManage'));
        } else { //操作失败，显示错误信息
            $this->error('删除失败！');
        }
    }

    //添加地区
    public function addArea(){
        if($_POST){
            $para['area_name'] = I('area_name');
            $para['status'] = "1";

            $goods = M('StoreArea')->add($para);
            if($goods>0){
                $this->success('添加成功！',U('storeManage'));
            } else { //操作失败，显示错误信息
                $this->error('添加失败！');
            }
        }
        $this->display();
    }
}