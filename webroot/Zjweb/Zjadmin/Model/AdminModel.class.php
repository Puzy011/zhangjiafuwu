<?php


namespace Zjadmin\Model;
use Think\Model;

/**
 * 用户模型

 */

class AdminModel extends Model {

    protected $_validate = array(
        array('nickname', '1,16', '昵称长度为1-16个字符', self::EXISTS_VALIDATE, 'length'),
        array('nickname', '', '昵称被占用', self::EXISTS_VALIDATE, 'unique'), //用户名被占用
    );

    public function lists($status = 1, $order = 'id DESC', $field = true){
        $map = array('status' => $status);
        return $this->field($field)->where($map)->order($order)->select();
    }

    /**
     * 登录指定用户
     * @param  integer $uid 用户ID
     * @return boolean      ture-登录成功，false-登录失败
     */
    public function login($uid){
        /* 检测是否在当前应用注册 */
        $user = $this->field(true)->find($uid);
    
        //记录行为
      //  action_log('user_login', 'member', $uid, $uid);

        /* 登录用户 */
        $this->autoLogin($user);
        return true;
    }

    /**
     * 注销当前用户
     * @return void
     */
    public function logout(){
        session('admin_auth', null);
        session('admin_auth_sign', null);
    }

    /**
     * 自动登录用户
     * @param  integer $user 用户信息数组
     */
    private function autoLogin($user){
        /* 更新登录信息 */
        $data = array(
            'id'             => $user['id'],
            'login_time' => NOW_TIME,
            'login_ip'   => get_client_ip(1),
        );
        $this->save($data);

        /* 记录登录SESSION和COOKIES */
        $auth = array(
            'id'             => $user['id'],
            'username'        => $user['username'],
            'login_time' => $user['login_time'],
        );

        session('admin_auth', $auth);
        session('admin_auth_sign', data_auth_sign($auth));

    }

    public function getNickName($uid){
        return $this->where(array('uid'=>(int)$uid))->getField('nickname');
    }


    //entry控制器用和withdraw公用的方法
    public function publicselect($wid){
              return  $this->where($wid)
                            ->field('price,bankname,bankcard,cardname,lock_price')
                            ->select();
      
    }
    //entry控制器用的方法
    public function publicsave($wid,$lock_price){
           return  $this->where($wid)->save($lock_price);
    }     
    public function getAdmin($where){
        $data=M('Admin')->where($where)->field('id,price,rid,pic,username,bankcard,price,nickname,cellphone,bankname')->select();
        return $data;
    }
    public function updateAdmin($where,$data){
        $data=M('Admin')->where($where)->data($data)->save();
        return $data;
    }

    
}
