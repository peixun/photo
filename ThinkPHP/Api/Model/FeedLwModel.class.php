<?php

class FeedLwModel extends LW_Model {
    private $uid;
    private $feed_conformity_type = array('add_friend');

    /**
     * 动态添加API
     *
     * @param string $type  动态类型
     * @param string $title_data  动态标题
     * @param string $body_data   动态内容
     * @param int $appid  应用ID
     * @param int $feedtype 0=应用和空间都可见 1=应用内部可见
     * @param int $fid      应该内部操作结果ID
     * @return Array
     */
    function publish($type,$title_data=null,$body_data=null,$appid='0',$feedtype='0',$fid='0') {
        $userLwDao	 =	TS_D("User");
        $uid		 =	$this->uid?$this->uid:$userLwDao->getLoggedInUser();
        $username	 =	$this->uid?$userLwDao->getUserName($this->uid):$userLwDao->getLoggedInName();
        //
        //        //feed的隐私同意控制
        //        $privacyDao = TS_D("Privacy");
        //        $privacy = $privacyDao->feed($uid,$type);

        //        if(!$privacy) return false;

        $data["uid"]		=	$uid;
        $data["username"]	=	$username;
        $data["type"]		=	$type;
        $data["title_data"]	=	$title_data?serialize($title_data):0;
        $data["body_data"]	=	$body_data?serialize($body_data):0;
        $data["cTime"]	    =	time();
        $data["appid"]		=	$appid;
        $data["feedtype"]   =	$feedtype;
        $data["fid"]        =	$fid;
        $result = $this->add($data);
        if($result){
        	    $feedTemplateDao = TS_D("FeedTemplate");
        	    $data['id'] = $result;
                $this->FeedTemplateOne($data,$feedTemplateDao);
                return $result;
        }

        
        //$data['id'] = $result;
        //$email      = array( $data );
        //$data = $this->__feedTemplate( $email );
        //$privacyDao->savemail( $data,$type );
        return false;
    }
    public function getFeedField($feedId,$field) {
        $id = intval($feedId);
        $request = $this->where('id='.$id)->find();
        return $request[$field];
    }
    public function updateFeed($feedId,$field,$value) {
        $condition['id'] = intval($feedId);
        $map[$field] = $this->__getFeedField($field,$value);
        return $this->where($condition)->save($map);
    }
    private function __getFeedField($field,$value) {
        $array = array('title_data','body_data');
        return in_array($field,$array)?serialize($value):$value;
    }

    public function getFeed( $map,$limit,$optitype=array()) {
        $request = $this->where( $map )->limit( '0,'.$limit )->order( 'id DESC' )->findAll();
        $feed_output = $this->__feedTemplate( $request );
        return $feed_output;
    }
    public function setUid($uid) {
        $this->uid = $uid;
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $appid
     * @param unknown_type $page
     * @param unknown_type $pageLimit
     * @param unknown_type $filter
     * @param unknown_type $format
     * @param unknown_type $feedtype
     * @param unknown_type $fid
     * @return unknown
     */
    function getApp($appid,$page=null,$pageLimit=30,$filter=null,$format="php",$feedtype=0,$fid,$map='') {
        $feedTemplateDao = TS_D("FeedTemplate");
        $userLwDao	 =	TS_D("User");
        $uid	 =	$userLwDao->getLoggedInUser();
        $username	 =	$userLwDao->getLoggedInName();

        if(!is_array($appid)) {
            $map["appid"] = $appid;
        }else {
            $map["appid"] = array("IN",$appid);
        }

        $dellFeedId = $this->__filterFeedId($uid);
        if(!empty($dellFeedId)) {
            $map['id'] = array('NOT IN',$this->__filterFeedId($uid));

        }
        if($filter)  $map["type"] = array("NOT IN",$filter);

        if($fid) {
            $map["feedtype"] = $feedtype;
            $map["fid"] = $fid;
        }

        //分页相关1
        $curPage = $page?$page:1;
        $firstRow = ($curPage-1)*$pageLimit;
        $limit    =	$firstRow.','.$pageLimit;
        //分页相关1 end

        $feeds = $this->where($map)->limit($limit)->order("id desc")->findAll();

        $feed_output = $this->__feedTemplate($feeds);


        //分页相关2
        if($feed_output) {
            $result["total_page"] = (int)ceil($fris_num["count"]/$pageLimit);   //总页数
            $result["data"] = $fris;
        }

        switch($format) {
            case "json": return json_encode($feed_output);
            default    : return $feed_output;
        }

    }
    public function restoreNullCache($type){
    	$contition['type'] = $type;
    	$save['cache'] = null;
    	$this->where($contition)->save($save);
    }
    /**
     * getCount
     * 获得计数
     * @param mixed $data
     * @access public
     * @return void
     */
    public function getCount( $data,$uid ) {
        if(isset($uid)) $map['uid'] = $uid;
        $count = $this->where( $map )->field( 'count(*)' )->find();
        return $count['count(*)'];
    }
    function get($who,$type,$page=null,$pageLimit=30,$format="php") {


        $feedTemplateDao = TS_D("FeedTemplate");
        $map = $this->__paramMap( $who,$type );
        //求的分页数据
        $limit    = $this->__getPageLimitFirst($pageLimit,$page?$page:1);
        $feeds = $this->where($map)->limit($limit)->order("id desc")->findAll();
        if('Home' == MODULE_NAME && $_POST['user'] == 'undefined') {
             $feeds = $this->__filterFeed($feeds);
        }

        //重新组装数据
        $feed_output = $this->__feedTemplate( $feeds );
        //$feed_output = $this->conformityData($feed_output);

        switch($format) {
            case "json": return json_encode($feed_output);
            default    : return $feed_output;
        }
    }
    private function __filterFriendUid($uid,$userLwDao,$friendLwDao){
            $mid = $userLwDao->getLoggedInUser();
            $friends = $friendLwDao->get($mid);
            return in_array($uid,$friends);
    }

    private function __filterFeed($feeds){
            $opts = TS_D('Option')->get();
            $filter_type = $opts['feed_filter'];
            $filter_count = $opts['feed_filter_count'];
            $typeCount = $this->__getTypeCount($feeds, $filter_type);


            //过滤过分重复的数据
            if($typeCount) {
                if('user' == $filter_type) {
                    $temp_feed = $this->__getArrayKeyValue($feeds, 'uid');
                    $temp_feed = $this->__getTempFeeds($temp_feed,$typeCount,$filter_count);
                    if($temp_feed) {
                        foreach($feeds as $key=>$value) {
                            if(!isset($temp_feed[$value['id']])) {
                                unset($feeds[$key]);
                            }
                        }
                    }
                }else {
                    if( count($typeCount[$filter_type]) > ceil(count($feeds)/2) ) {
                    //取出前N条记录
                        $typeCount = array_slice($typeCount[$filter_type],0,$filter_count);
                        foreach($feeds as $key=>$value) {
                            if(false !== strpos($value['type'], $filter_type) && !in_array($value['id'],$typeCount)) {
                                unset($feeds[$key]);
                            }
                        }
                    }
                }
            }

            foreach ($feeds as $key=>$value){
                    if('add_friend' == $value['type']){
                            $temp[$value['cTime']] = $value['id'];
                    }
            }
            foreach ($feeds as $key=>$value){
                    if('add_friend' == $value['type'] && !in_array($value['id'],$temp)){
                            unset($feeds[$key]);
                    }
            }
            return $feeds;
    }



    private function __getTempFeeds($temp_feed,$typeCount,$filter_count) {
        foreach ($typeCount as $key=>$value) {
            if($value>$filter_count) {
                foreach ($temp_feed as $id=>$feed) {
                    if($feed == $key) {
                        if($i[$key]>=$filter_count) {
                            unset($temp_feed[$id]);
                        }
                        $i[$key] = $i[$key]+1;
                    }
                }
            }
        }
        return $temp_feed;
    }
    private function __getTypeCount($feeds,$type) {
        $count = array();
        if('user' !== $type) {
            $user_array = $this->__getArrayKeyValue($feeds, 'type');
            if($this->__getDiffCount($user_array)) {
                foreach( $feeds as $value) {
                    if(false !== strpos($value['type'], $type)) {
                        $count[$type][] = $value['id'];
                    }
                }
                return $count;
            }
        }else {
            $user_array = $this->__getArrayKeyValue($feeds, 'uid');
            if($this->__getDiffCount($user_array)) {
                $count = array_count_values($user_array);
                return $count;
            }
        }
        return false;
    }
    private function __getDiffCount($array) {
        $count1 = count($array);
        $temp_user_list  = array_unique($user_array);
        $count2 = count($temp_user_list);
        return $count2 < ceil($count1/2);
    }


    private function __getArrayKeyValue($array,$key) {
        $result = array();
        foreach($array as $value) {
            $result[$value['id']] = $value[$key];
        }
        return $result;
    }
    function publishTemplatizedAction() {

    }


    private function __filterFeedId($uid) {
    //剔除删除过的
        $map_d["uid"] = $uid;
        $del_feeds = TS_D("FeedDel")->getDelList($map_d);

        foreach($del_feeds as $v) {
            $dids[] = $v["feedId"];
        }
        return $dids;
    }

    //我的好友
    private function __getMyFris() {
        $my_fris = TS_D("Friend")->get();

        //看后台是否设置了没有好友,就显示全站动态
        if(!$my_fris) {
            $opts = TS_D("Option")->get();
            if($opts["fri_dongtai"] == "1") {
                $all_users = TS_D("User")->field("id")->findAll();
                foreach($all_users as $user) {
                    $all_user_ids[] = $user["id"];
                    $my_fris = $all_user_ids;
                }
            }
        }
        return $my_fris;
    }
    private function __screenFriends($uid) {
        $my_fris = $this->__getMyFris();
        //剔除屏蔽的那些好友id
        $ping_fris = TS_D("FriendPing")->where("uid=$uid")->findAll();
        foreach($ping_fris as $v) {
            $ping_fri_ids[] = $v["fuid"];
        }

        return $ping_fri_ids?array_diff($my_fris,$ping_fri_ids):$my_fris;
    }

    private function __getFeedUid($who,$uid) {
        if($who != "fri") {
            if (is_array($who)) return array('in',$who);
            if(!empty($who)) return intval($who);
        }else {
        //去除屏蔽的好友动态id
            $last_fris = $this->__screenFriends($uid);
            if(!$last_fris) return; //如果没有好友id了，那就直接返回吧
            return array("IN",$last_fris);
        }
    }

    private function __paramMap( $who,$type ) {
        $uid = TS_D("User")->getLoggedInUser();
        $feedId = $this->__filterFeedId ($uid);
        if(!empty($feedId)) {
            $map['id'] = array('not in',$feedId);
        }

        $_uid = $this->__getFeedUid($who, $uid);
        if($_uid)
            $map['uid'] = $_uid;

        if($type != "all")
            $map['appid'] = $type;

        $map['type'] = array('neq',13);
        return $map;
    }

    private function __feedTemplate( $feeds ) {
    //feed的icon
        $feedTemplateDao = TS_D("FeedTemplate");
        $feed_output =array();
        foreach($feeds as $fkey=>$feed) {
        	if(!empty($feed['cache'])){
        		$feed_output[$fkey] = unserialize($feed['cache']);
        	}else{
        		$feed_output[$fkey] = $this->FeedTemplateOne($feed,$feedTemplateDao);
        		//刷新缓存字段
        	}
        }
        return $feed_output;
    }
    public function FeedTemplateOne($feed,$feedTemplateDao){
    	    //不用额外处理的动态部分
            $feed_output["type"] = $feed["type"];
            $feed_output["id"] = $feed["id"];
            $feed_output["cTime"] = $feed["cTime"];
            $feed_output["uid"] = $feed["uid"];
            //TODO 临时数据转换
            $feed['title'] = $feed['title_data'];
            $feed['body'] = $feed['body_data'];
            unset($feed['title_data']);
            unset($feed['body_data']);

            $feed_template = $feedTemplateDao->where(array("type"=>$feed['type']))->find();
            $feed_output["title"] = $this->__getTitle($feed_template['title'],$feed);

            $body_template = $feed_template["body"]; //body的模板
            if($body_template) {
                $feed_output["body"] = $this->__getBody($body_template,$feed);
            }
            $feed_output['icon'] = getAppInfo($feed['appid'],'APP_ICON');
            if(empty($feed['cache'])){
            	$data['cache'] = serialize($feed_output);
            	$this->where('id='.$feed['id'])->save($data);
            }
            return $feed_output;
    }
}

?>
