<?php
namespace Zjadmin\Model;
use Think\Model;
/*
    提现模型
 */
class WithdrawModel extends Model {
    #Entry调用这个
    public function entry_save($maps,$status){
        return $this->where($maps)->save($status);
    }
    //操作日志记录
    public function actionlogs($actionlog){
        $actionlog = M('actionlog')->add($actionlog);
        return $actionlog;
    }
    /**
     * 物业分红
     */
    public function addBonus($data){
        $data=M('bonus')->add($data);
        return $data;
    }
    public function getBonus($where){
        $data=M('bonus')->where($where)->field('admin_id,sum(money) as money')->group('admin_id')->select();
        return $data;
    }
    public function updateBonus($where,$data){
        $data=M('bonus')->where($where)->data($data)->save();
        return $data;
    }
}


?>