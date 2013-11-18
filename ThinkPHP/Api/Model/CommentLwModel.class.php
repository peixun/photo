<?php 
class CommentLwModel extends LW_Model {
    private $mid;     //当前访问者Id
    private $name;    //当前访问者名字
    private $uid;     //当前应用的主UID

    public function __construct($lw_db_configs) {
        $userLwDao	 =	TS_D("User");
        $this->mid		 =	$userLwDao->getLoggedInUser();
        $this->name	 =	$userLwDao->getLoggedInName();
        parent::__construct($lw_db_configs);
    }
    public function getCount($type,$appid) {
        $map['type'] = $type;
        $map['appid'] = $appid;
        $map['toId'] = 0;
        $count = $this->where($map)->field('count(1) as count')->find();
        return $count['count'];
    }
    /**
     * getComment
     * 获取评论数据
     * @access public
     * @return void
     */
    public function get($page,$post) {
        if( $this->mid == $post['mid']) {
            $map['type']  = $post['type'];
            $map['appid'] = $post['id'];
            $map['toId']  = '0';
        }else {
            $map = "(uid = {$this->mid} OR (quietly <> 1 AND uid <>{$this->mid}))  AND toId = 0 AND appid = {$post['id']} AND type = '{$post['type']}'";
        }
        $comment = $this->getComments( $map,"cTime DESC",$page,'findPage' );
        foreach( $comment as $key=>&$value ) {
            unset( $map );
            //子回复
            $map['toId']  = $value['id'];
            $map['type']  = $post['type'];
            $map['appid'] = $post['id'];
            if( $subcomment = $this->getComments( $map,"id ASC",null,"findAll" ) ) {
                $value['subcomment'] = $subcomment;
                unset( $value['subcomment']['data'] );
                $value['isDelete'] = false;
            }
        }
        return $comment;
    }

    public function add($post) {
        $map['comment'] = $post['comment'];
        $map['type']    = $post['type'];
        $map['appid']   = $post['appid'];
        $map['cTime']   = time();
        $map['name']    = $this->name;
        $map['status']  = 0;
        $map['uid']     = $this->mid;
        $map['toId']    = $post['toId'];
        $map['quietly'] = $post['quietly'];
        $result = $map;
        if($add = $this->add($map)) {
            $result['cTime']    = "刚刚";
            $result['id']       = $add;
            $result['face']     = getUserFace( $result['uid'] );
            $result['comment']  = $this->replaceContent( $result['comment'],'mini' );//TODO 表情多应用化
            $result['isDelete'] = true;
            return json_encode(($result));
        }else {
            return false;
        }

    }

    public function notify(array $type,array $data,$appid) {
        $dao = TS_D('Notify');
        $dao->setAppId($appid);
        if($this->mid != $data['uids']) {
            switch ( true ) {
                case ( $data['uids'] == $data['toUid'] && !empty($data['toUid']) ): //发布者的回复的回复
                    setScore($data['toUid'], 'comment_comment');
                    $notify = $dao->send( $data['toUid'],'comment_comment',$data['title_data'],$data['title_body'],$data['url'] );
                    break;
                case ( $data['uids'] <> $data['toUid'] ) && !empty( $data['toUid'] ): //回复的回复
                    setScore($this->mid, 'commented');
                    if($data['toUid'] != $this->mid) {
                            setScore($data['toUid'], 'comment_comment');
                            $notify = $dao->send( $data['toUid'],'comment_comment',$data['title_data'],$data['title_body'],$data['url'] );
                    }
                    setScore($data['uids'], 'commented');
                    $notify = $dao->send( $data['uids'],$type.'_comment',$data['title_data'],$data['title_body'],$data['url'] );
                    break;
                default://评论
                    setScore($this->mid, 'comment');
                    setScore($data['uids'], 'commented');
                    $notify = $dao->send( $data['uids'],$type.'_comment',$data['title_data'],$data['title_body'],$data['url'] );
            }

            return $notify;
        }else {
            if($data['uids'] <> $data['toUid']  && !empty( $data['toUid'])) {
                setScore($data['toUid'], 'comment_comment');
                $notify = $dao->send( $data['toUid'],'comment_comment',$data['title_data'],$data['title_body'],$data['url'] );
            }
        }
    }

    /**
     * getComments
     * 获取评论
     * @param mixed $map
     * @access private
     * @return void
     */
    private function getComments( $map,$order,$page,$method ) {

    //获取评论数据
        $comment = $this->where( $map )->order( $order )->findAll();

        //如果没有，返回false
        if( !$comment ) {
            return false;
        }

        return  $comment = $this->replace( $comment );
    }

    private function replace( $data ) {
        foreach( $data as $key=>&$value ) {
            $value['face']       = getUserFace( $value['uid'] );
            $value['cTime']      = friendlyDate( $value['cTime'] );
            $value['comment']    = $this->replaceContent( $value['comment'],'mini' );//TODO 表情多应用化
            $value['isDelete']   = (( $this->mid == $value['uid'] )|| $this->mid == $this->uid)?true:false;
        //悄悄话
        }
        return $data;
    }

    /**
     * replaceContent
     * 替换内容
     * @param mixed $content
     * @access private
     * @return void
     */
    private function replaceContent( $content,$type ) {
    //TODO 每一个应用可以应用一套表情
        $path = __PUBLIC__."/images/biaoqing/mini/";//路径
        $smile = ts_cache( "smile_mini" );
        //循环替换掉文本中所有ubb表情
        foreach( $smile as $value ) {
            $img = sprintf("<img title='%s' src='%s%s'>",$value['title'],$path,$value['filename']);
            $content = str_replace( $value['emotion'],$img,$content );
        }
        return $content;
    }


}
?>
