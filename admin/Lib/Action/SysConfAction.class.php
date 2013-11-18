<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 系统配置

class SysConfAction extends CommonAction{

	public function index(){
		$list = D($this->getActionName())->where("status=1 and group_id=1 and is_show = 1")->findAll();
		$conf_list = array(); //用于输出分组格式化后的数组
        //dump($list);
		foreach($list as $k=>$v)
		{
			if($v['name']=='DEFAULT_LANG')
			{
			   $lang_list =D("LangConf")->findAll();
			   $v['val_arr'] = array();
			   foreach($lang_list as $lang_item)
			   {
			   		$v['val_arr'][] = $lang_item['lang_name'];
			   }
			}
			elseif($v['name']=='DEFAULT_USER_GROUP')
			{
			   $group_list =D("UserGroup")->findAll();
			   $v['val_arr'] = array();
			   foreach($group_list as $group_item)
			   {
			   		$v['val_arr'][] = $group_item['id'];
			   }
			}
			else
			{
				$v['val_arr'] = explode(",",$v['val_arr']);
			}
			if($v['name']!='URL_MODEL')
			$conf_list[L("SYSCONF_GROUP_".$v['group_id'])][$k] = $v;
		}

		$this->assign("conf_list",$conf_list);
		$this->display();
	}

	public function update()
	{
		$upload_list = $this->uploadFile(0,"public");
		if($upload_list)
		{
			foreach($upload_list as $upload_item)
			{
				if($upload_item['key']=="SHOP_LOGO")
				{
					$shop_logo = $upload_item['recpath'].$upload_item['savename'];
				}
				if($upload_item['key']=="WATER_IMAGE")
				{
					$water_image = $upload_item['recpath'].$upload_item['savename'];
				}
				if($upload_item['key']=="NO_PIC")
				{
					$no_pic = $upload_item['recpath'].$upload_item['savename'];
				}
				if($upload_item['key']=="FOOT_LOGO")
				{
					$foot_logo = $upload_item['recpath'].$upload_item['savename'];
				}
				if($upload_item['key']=="GROUP_IMG_TMPL")
				{
					$group_img_tmpl = $upload_item['recpath'].$upload_item['savename'];
				}
			}
		}
		$list = D($this->getActionName())->where("status=1")->findAll();
		foreach($list as $k=>$v)
		{

			//开始临时保存原有admin.php的值
			if($v['name']=="ADMIN_FILE_NAME")
			{
				$o_admin_file = $v['val'];
				if($v['val']!=$_REQUEST[$v['name']])
				{
					$file_str = @file_get_contents($this->getRealPath()."/".$o_admin_file);
					@file_put_contents($this->getRealPath()."/".$_REQUEST[$v['name']],$file_str);
					if(file_exists($this->getRealPath()."/".$_REQUEST[$v['name']]))
					{
						$v['val'] = isset($_REQUEST[$v['name']])?$_REQUEST[$v['name']]:$v['val'];
					}
				}
			}
			else
			$v['val'] = isset($_REQUEST[$v['name']])?$_REQUEST[$v['name']]:$v['val'];
			if($v['name']=="SHOP_LOGO")
			{
				if($shop_logo)
				{
					@unlink($this->getRealPath().$v['val']);
					$v['val'] = $shop_logo;
				}
			}

			if($v['name']=="WATER_IMAGE")
			{
				if($water_image)
				{
					@unlink($this->getRealPath().$v['val']);
					$v['val'] = $water_image;
				}
			}
			if($v['name']=="NO_PIC")
			{
				if($no_pic)
				{
					@unlink($this->getRealPath().$v['val']);
					$v['val'] = $no_pic;
				}
			}
			if($v['name']=="FOOT_LOGO")
			{
				if($foot_logo)
				{
					@unlink($this->getRealPath().$v['val']);
					$v['val'] = $foot_logo;
				}
			}
			if($v['name']=="GROUP_IMG_TMPL")
			{
				if($group_img_tmpl)
				{
					@unlink($this->getRealPath().$v['val']);
					$v['val'] = $group_img_tmpl;
				}
			}
			if($v['name']=='URL_ROUTE')
			{
				$url_model_cfg_id = D("SysConf")->where("name='URL_MODEL'")->getField("id");
				if($v['val']==0)
				{
					$sql = "update ".C("DB_PREFIX")."sys_conf set val = 0 where id=".$url_model_cfg_id;
				}
				else
				{
					$sql = "update ".C("DB_PREFIX")."sys_conf set val = 2 where id=".$url_model_cfg_id;
				}
			}
		if($v['name'] == 'PAGE_BOTTOM')
			{
				$v['val'] = stripslashes($v['val']);
			}

		if($v['name'] == 'DB_PCONNECT')
			{
				//开始将$db_config写入配置

		    	$db_config_str 	 = 	"<?php\r\n";
		    	$db_config_str	.=	"return array(\r\n";
		    	$db_config_str.="'DB_HOST'=>'".C('DB_HOST')."',\r\n";
		    	$db_config_str.="'DB_NAME'=>'".C('DB_NAME')."',\r\n";
		    	$db_config_str.="'DB_USER'=>'".C('DB_USER')."',\r\n";
		    	$db_config_str.="'DB_PWD'=>'".C('DB_PWD')."',\r\n";
		    	$db_config_str.="'DB_PORT'=>'".C('DB_PORT')."',\r\n";
		    	$db_config_str.="'DB_PREFIX'=>'".C('DB_PREFIX')."',\r\n";
		    	if($v['val']==1)
		    	{
		    		$db_config_str.="'DB_PCONNECT'=>1,\r\n";
		    	}
		    	$db_config_str.=");\r\n";
		    	$db_config_str.="?>";
		    	@file_put_contents($this->getRealPath()."/config/db_config.php",$db_config_str);
			}
			D($this->getActionName())->save($v);
		}
		D("SysConf")->query($sql);

		//写入global_config.php
		write_timezone();
		//开始写入配置文件
		$sys_configs = M()->query("select name,val from ".C("DB_PREFIX")."sys_conf");
		$config_str = "<?php\n";
		$config_str .= "return array(\n";
		foreach($sys_configs as $k=>$v)
		{
			$config_str.="'".$v['name']."'=>'".addslashes($v['val'])."',\n";
		}
		$config_str.=");\n ?>";
		@file_put_contents($this->getRealPath()."/config/sys_config.php",$config_str);
		clear_cache();


		$this->success(L('EDIT_SUCCESS'));
	}

	public function checkSSL()
	{
		if(extension_loaded("openssl"))
		{
			echo 1;
		}
		else
		{
			echo 0;
		}
	}

	public function checkFile()
	{
		$file_name = $_REQUEST['file_name'];

		if(preg_match("/^[a-zA-Z0-9]{1,15}\.php$/i",$file_name))
		{
			$obj['status'] = 1;
			$obj['file_name'] = $file_name;
		}
		else
		{
			$obj['status'] = 0;
			$obj['file_name'] = eyooC("ADMIN_FILE_NAME");
		}
		echo json_encode($obj);
	}
}
?>