<?php
class UserCreditLwModel extends LW_Model{
    public $table_name = "user_credit";
    public function addUserCredit($userId,$action,$credit) {
        $map['uid']	=	$userId;
        $map['action']	=	$action;
        $map['credit']	=	$credit;
        $map['cTime']		=	time();
        $map['status']	=	1;	//0 未生效 1 生效
        $result = $this->add($map);
        return $result;
    }
}

?>
