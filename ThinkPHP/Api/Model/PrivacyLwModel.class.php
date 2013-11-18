<?php

class PrivacyLwModel extends LW_Model {
        private $remind_type = array( 
  "message",
  "wall",
  "comment",
  "quan",
  "add_friend"       => "add_friend",
  "invite_group"     => "group_invite",
  "agree_group"      => "group_agree",
  "birthday"         => "birthday",
  "fri_feed"         => "mini",
  "fri_photo"        => "photo",
  "fri_friend"       => "agree_friend",
  "fri_head"         => "head",
  "fri_info"         => "info",
  "fri_join_group"   => "group_join",
  "fri_create_group" => "group_create",
  "fri_add_app"      => "app_add",
  "fri_wall"         => "wall",
  "frequency",
  "not_in",
            );


	public function see($mid,$uid,$type) {

		$map["uid"] = $uid;
		$map["type"] = "basic";
		$r = $this->where($map)->find();
		$privacy = unserialize($r["privacy"]);

		//没有设置过的话,默认从缓存里面读
        if(!$privacy){
            $site_opts = TS_D("Option")->get();
            $privacy = unserialize($site_opts["privacy"]);
        }
					

		$privacy = intval($privacy[$type]);

		
		if($mid == $uid){   //如果是看自己空间，肯定显示
			return true;
		}else{              //看别人空间
			switch($privacy) { 
				case 0:  return true;                           //任何人
				case 1:  {
					$api = new TS_API();
					return $api->friend_areFriends($mid,$uid); //仅好友
				}
				case 2 : return false;                          //他自己可看
				default: return true;
			}		
		}
	
	}

	public function get($mid,$type) {

		$map["uid"] = $mid;
		$map["type"] = "basic";
		$r = $this->where($map)->find();
		$privacy = unserialize($r["privacy"]);

		//没有设置过的话,默认从缓存里面读
        if(!$privacy){
            $site_opts = TS_D("Option")->get();
            $privacy = unserialize($site_opts["privacy"]);
        }

		return $privacy[$type];
	}

    /**
     * savemail 
     * 发送邮件,将数据通过api中的saveEmail存储到数据库中
     * @param mixed $data 
     * @access public
     * @return void
     */
    public function savemail( $data,$input_type ){
        //定义要用到的变量
        $friendDao = TS_D( 'Friend' );
        $user      = TS_D( 'User' );

        //循环数据集开始
		foreach ( $data as $key=>$template_data ){
		    $array     = array( 'wall'=>$template_data['author'].'留言给您','message'=>$data['author'].'给您发了消息' );
			//得到当前通知模板的目标用户id
            $uid = $template_data['uid'];

            //得到当前通知的类型
            $type = $template_data['type'];
            //如果当前通知模板数据中的类型为空
            if( !isset( $type ) || empty($type) ){

                $mail = $user->getInfo( $uid,'email' );
                $toEmail[] = $mail['email'];
				$input_type = $template_data['cate'];
				
                //如果传入类型为wall,message,quan,comment其中之一
                if( isset($array[$input_type]) ){
                    //以目标用户为核心。查看他的提醒设置。
                    $remind_config = $this->__checkRemind( $uid,$input_type);
					$template_data['title'] = $array[$input_type];
                    //如果他的提醒设置为真
                    if( $remind_config ){
                        //发送邮件,参数为类容，站点信息
                        $this->__sendEmail( $template_data,$uid,$toEmail );
                    }
                }
            //反之
            }else{
                //通过类型，得到邮件设置类型
                $remind_type = $this->__actionToRemind( $type );

                //如果邮件设置类型为好友交流类消息
                if( false !== strpos( $remind_type,'fri_' ) ){

                    //查找目标用户的好友,得到数组 
                    $friend = $friendDao->get( $uid );

                    //如果没有好友。退出。不进行任何操作
                    if( empty($friend) ) continue;


                    //好友循环开始
                    foreach( $friend as $fri_uid){
                        //通过邮件设置类型取得邮件提醒设置
                        $remind_config = $this->__checkRemind( $fri_uid,$remind_type );
                        //如果他的提醒设置为真
                        if( true == $remind_config ){
                            $mail = $user->getInfo( $fri_uid,'email' );
                            $toEmail[] = $mail['email'];
                        }
                        ////发送邮件,参数为类容，站点信息
                    }
                    $this->__sendEmail( $template_data,$uid,$toEmail );
                    //好友循环结束
                //反之
                }else{
                    //通过邮件设置类型取得邮件提醒设置
                    $remind_config = $this->__checkRemind( $uid,$remind_type );
                    $mail = $user->getInfo( $uid,'email' );
                    $toEmail = $mail['email'];

                    //如果他的提醒设置为真
                    if( $remind_config ){
                        //发送邮件,参数为类容，站点信息
                        $this->__sendEmail( $template_data,$uid,$toEmail );
                    }
                }
            //判断结束
            }
        }
        //循环数据集结束
    }


	public function feed($mid,$type){

		$type_arr = array("info","head","mini","photo","blog","group_create","add_friend","add_app","wall","comment",'group_join');

		$map["uid"] = $mid;
		$map["type"] = "feed";
		$r = $this->where($map)->find();

		//if($r == false) return true; 

		$privacy = unserialize($r["privacy"]);
		

        if(!$privacy){
            $site_opts = TS_D("Option")->get();
            $privacy = unserialize($site_opts["feed_privacy"]);
        }


		if($privacy[$type] || !in_array($type,$type_arr)){
			return true;
		}else{
			return false;
		}
	}


    /**
     * remind 
     * 获得指定类型的privacy
     * @param mixed $mid 
     * @param mixed $type 
     * @access public
     * @return void
     */
	public function remind($mid,$type=null){
		$map["uid"] = $mid;
		$map["type"] = "remind";
		$r = $this->where($map)->find();

		$privacy = unserialize($r["privacy"]);
        if( isset( $type ) ){
		    return $privacy[$type];		
        }else{
            return $privacy;
        }
	}

	public function black($mid="",$uid="") {
		
		if(!$uid) return false;   //如果访问自己的话

		$dao = TS_D("FriendBlack");

		$r = $dao->where("uid=$uid")->findAll();

		foreach($r as $key=>$v){
			$black_friends[] = $v["fuid"];
		}

		return in_array($mid,$black_friends);		

	}


	public function hide($type,$mid="",$uid="") {
		
		if(!$uid) return false;     //如果访问自己的话 

		$dao = TS_D("FriendHide");

		$r = $dao->where("uid=$uid AND type='$type'")->findAll();

		foreach($r as $key=>$v){
			$black_friends[] = $v["fuid"];
		}

		return in_array($mid,$black_friends);		

	}

    /**
     * __checkRemind 
     * 检查邮件设置
     * @param mixed $mid 
     * @param mixed $type 
     * @access private
     * @return void
     */
    private function __checkRemind( $mid,$type ){
        //设置静态变量。当这个对象存在时。此值始终保持存在
        static $remind = array();
        $remind[$mid] = empty( $remind[$mid] )?$this->remind( $mid ):$remind[$mid];
        return $remind[$mid][$type];
    }
    
    /**
     * __actionToRemind 
     * 类型到邮件配置转换
     * @param mixed $type 
     * @access private
     * @return void
     */
    private function __actionToRemind( $type ){
        //如果是评论类的类型
        if( false !== strpos( $type,'comment' ) ){
            //函数返回comment固定类型
            return 'comment';
        //如果不是
        }else{
            //返回 设置类型到通知/动态类型中中取得设置类型的结果
            return array_search( $type,$this->remind_type );
        }
        //判断结束
    }

    private function __sendEmail( $data,$uid,$toEmail ){
        //存储到数据库
        $email = TS_D( 'SaveEmail' );
		$data = $this->__paramData($data['title'],$data['body'],$data['url']);
        $title   = $data['title'];
        $content = $data['body'];
	    $result = $email->saveEmail($toEmail,$title,$content,$uid,$name='',$type='');
        return $result;
    }
    
    private function __paramData($title,$body,$url){
    	$result = array();
    	$body = stripslashes($body);
    	$title = stripslashes($title);
    	$body = unserialize($body)?unserialize($body):$body;
    	$result['body'] = t(array_shift($body)).sprintf('<a href=\'%s\'>去看看</a>',$url);
    	$result['title'] = t($title);
    	return $result;
    }
}

?>
