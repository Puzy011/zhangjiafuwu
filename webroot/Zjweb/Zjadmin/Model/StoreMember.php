<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/7
 * Time: 16:06
 */

namespace Zjadmin\Model;


class StoreMember
{
    public function search($map,$page) {
        $list = M("Store_member")
            ->where($map)
            ->limit($page->firstRow.','.$page->listRows)
            ->field('zj_store_member.id,is_auth,name,id_card,reg_time,status,is_owner,mobile,open_id,mobile_code,code_status,code_time')
            ->order('id DESC')
            ->select();
        return $list;
    }

}