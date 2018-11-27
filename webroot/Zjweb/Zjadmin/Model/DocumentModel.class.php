<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------

namespace Zjadmin\Model;
use Think\Model;

/**
 * 文档基础模型
 */
class DocumentModel extends Model{

    /* 自动完成规则 */
    protected $_auto = array(
        array('title', "", self::MODEL_INSERT),
        array('content', "", self::MODEL_INSERT),
        array('type', 0, self::MODEL_INSERT),
        array('author', "", self::MODEL_INSERT),
        array('time', NOW_TIME, self::MODEL_INSERT),
        array('status', '1'),
    );
    
    public function lists($status = 1, $order = 'id DESC', $field = true){
        $map = array('status' => $status);
        return $this->field($field)->where($map)->order($order)->select();
    }
    public function create($title='',$content='',$type='',$author=''){
        $this->error("1111111");
        $data = array(
            'title' => $title,
            'content' => $content,
            'type'=> $type,
            'author' => $author,
            'time' => date('Y-m-d H:i:s',time()),
            'status'=> 1,
        );
        if($this->create($data)){
            $uid = $this->add();
            return $uid ? $uid : 0; //0-未知错误，大于0-注册成功
        } else {
            return $this->getError(); //错误详情见自动验证注释
        }
    }
    public function update(){
        /* 获取数据对象 */
        $data = $this->create($_POST);
        if(empty($data)){
            return false;
        } else { //更新数据
            $status = $this->save(); //更新基础内容
            if(false === $status){
                $this->error = '更新行为出错！';
                return false;
            }
        }
        //删除缓存
        S('action_list', null);
    
        //内容添加或更新完成
        return $data;
    
    }
}
