<?php

class NotifyLwModel extends LW_Model {
    private $appid = 0;
    private $anonymous = false;

    function send($uids,$type=null,$title_data=null,$body_data=null,$url,$cate="notification") {
        $userLwDao	 =	TS_D("User");
        $authorid	 =    $this->anonymous?0:$userLwDao->getLoggedInUser();
        $author		 =	$this->anonymous?0:$userLwDao->getLoggedInName();
        $data["type"]		=	$type;
        $data["new"]		=	1;
        $data["authorid"]	=	$authorid;
        $data["author"]		=	$author;
        $data["title"]		=	serialize($title_data);
        $data["body"]		=	serialize($body_data);
        $data["url"]		=	$url;
        $data["cTime"]	    =	time();
        $data["cate"]		=	$cate;
        $this->appid !=0 && $data['appid']         = $this->appid;
        if(!is_array($uids)) {
            $uids = split(",",$uids);
        }

        //notify的邮箱控制

        //        //定义需要使用的对象变量
        //        $privacyDao = TS_D( "Privacy" );
        //
        //        //获取通知的数据数组
        //        $notify = $this->__notifyTemplate( $temp_data,$type );
        //
        //        foreach( $uids as $key=>$uid ){
        //            $notify[$key] = $data;
        //            $notify[$key]['uid'] = $uid;
        //        }
        //
        //        //用邮箱控制对象进行发送邮件
        //        $privacyDao->savemail( $notify );

        //邮箱控制结束



        foreach($uids as $v) {
            $data["uid"]	=	$v;
            $result = $this->add($data);
        }
        return $result?true:false;
    }
    public function setAnonymous(){
            $this->anonymous = true;
    }
    public function setAppId($appid) {
        $this->appid = intval($appid);
        return $this;
    }

    function get($cate=null,$type=null,$pageLimit=10,$format = "php") {
        //分页相关1
        $limit = $this->__getPageLimitFirst($pageLimit);

        $map = $this->__paramMap($cate,$type);

        //查询
        $Notifys = $this->where($map)->order("cTime desc")->limit($limit)->findAll();
        $notify_num = $this->where($map)->field("count(*) as count")->find();

        //渲染
        $Notify_output = $this->__notifyTemplate( $Notifys );

        //分页相关2
        if($Notify_output) {
            $result["total_page"] = $this->__getPageLimitSecond($notify_num['count'],$pageLimit);   //总页数
            $result["data"] = $Notify_output;
        }


        if($Notifys) {
        //设置为已读
            $map .= " AND new = 1";
            $data["new"] = 0;
            $this->where($map)->save($data);
        }
        switch($format) {
            case "json": return json_encode($result);
            default    : return $result;
        }
    }

    function getNewNum($uid =null,$cate=null,$format = "php") {
        $uid	 = $uid?$uid:TS_D("User")->getLoggedInUser();
        switch ($cate) {
            case "notification":
                $result["notification"] = $this->__getNotifyNum(1,$uid, 'notification');
                break;
            case "friend":
                $result["friend"] = $this->__getNotifyNum(1,$uid, 'friend');
                break;
            case "message":
                $result["message"] = intval(TS_D( 'Msg' )->getNewNum( $uid ));
                break;
            case "wall":
                $result["wall"] = $this->__getNotifyNum(1,$uid, 'wall');
                break;
            default:
                $result["notification"] = $this->__getNotifyNum(1,$uid,'notification');
                $result["friend"] = $this->__getNotifyNum(1,$uid, 'friend');
                $result["message"] = intval(TS_D( 'Msg' )->getNewNum( $uid ));
                $result["wall"] = $this->__getNotifyNum(1,$uid, 'wall');
        }

        switch($format) {
            case "json": return json_encode($result);
            default    : return $result;
        }
    }

    private function __getNotifyNum($new,$uid,$cate) {
        $map['new'] = $new;
        $map['cate'] = $cate;
        $map['uid'] = $uid;
        $r1 = $this->where($map)->field("count(*)")->find();
        return  intval($r1["count(*)"]);
    }

    private function __notifyTemplate( $notifys ) {

        $notifyTemplateDao = TS_D("NotifyTemplate");
        foreach($notifys as $fkey=>$notify) {
            $notify_template = $notifyTemplateDao->where(array("type"=>$notify['type']))->find();
            //将通知和动态中不同的数据统一字段
            $notify = $this->_extra($notify);
            $notify_output[$fkey]["title"] = $this->__getTitle($notify_template['title'],$notify);


            $body_template = $notify_template["body"]; //body的模板
            if($body_template) {
                $notify_output[$fkey]["body"] = $this->__getBody($body_template,$notify);
            }
            $this->_unsetExtra($notify);
            $notify_output[$fkey]["authorid"] = $notify["authorid"];
            $notify_output[$fkey]["author"] = $notify["author"];
            $notify_output[$fkey]["type"] = $notify_template["type"];
            $notify_output[$fkey]["type_cn"] = $notify_template["type_cn"];
            $notify_output[$fkey]["deal"] = $notify_template["deal"];
            $notify_output[$fkey]["id"] = $notify["id"];
            $notify_output[$fkey]["url"] = $this->_replaceConstant($notify["url"],$notify['appid']);
            $notify_output[$fkey]["new"] = $notify["new"];
            $notify_output[$fkey]["cTime"] = $notify["cTime"];
        }
        return $notify_output;
    }


    private function _extra($data) {
        $result = $data;
        $result['uid'] = $data['authorid'];
        $result['username'] = $data['author'];
        return $result;
    }
    private function _unsetExtra($data) {
        $result = $data;
        unset($result['uid']);
        unset($result['username']);
        return $result;
    }

   private function __paramMap($cate,$type) {
        if($cate!="all") {
            $map = "cate = '".$cate."'";
        }else {
            $map = "(cate = 'friend' OR cate = 'notification')";
        }
        if($type) $map.=" AND type = '".$type."'";

        //只查我的
        $mid = TS_D("User")->getLoggedInUser();
        $map.=" AND uid = ".$mid;
        return $map;
    }

}

?>
