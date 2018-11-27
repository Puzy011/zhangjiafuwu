<?php
namespace Zjadmin\Model;
use Think\Model;

/**
 * 设备模型
 */
class DeviceModel extends Model
{
    /* 自动验证规则 */
    protected $_validate = array(
    
        array('device_name', 'require', '设备名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('title', '1,50', '设备名称不能超过80个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
    
    );
    /* 自动完成规则 */
    protected $_auto = array(
        array('time', 'getCreateTime', self::MODEL_BOTH,'callback'),
        array('status',1, self::MODEL_INSERT),
    );
    /**
     * 添加设备
     */
    public function addDevice($data){
        $res=M('Device')->add($data);
        return $res;
    }
    /**
     * 修改设备
     */
    public function updateDevice($where,$data){
        $res=M('Device')->where($where)->data($data)->save();
        return $res;
    }
    /**
     * 删除设备
     */
    public function deleteDevice($where){
        $res=M('Device')->where($where)->delete();
        return $res;
    }
}

?>