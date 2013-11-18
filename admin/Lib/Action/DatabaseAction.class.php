<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 数据库管理
class DatabaseAction extends CommonAction{
	public function index()
	{
		$db_back_dir = $this->getRealPath()."/Public/db_backup/";
		$sql_list = $this->dirFileInfo($db_back_dir,".sql");
		$this->assign("sql_list",$sql_list);
		$this->display();
	}
	
	public function dump()
	{
		$sqlDump = new SqlDump();
		if($sqlDump->dump())
		{
			$msg = '数据库备份成功';
			$this->saveLog(1,0,$msg);
			$this->success(L('DUMP_SUCCESS'),true);	
			
		}
		else
		{
			$msg = '数据库备份失败';
			$this->saveLog(0,0,$msg);
			$this->error(L('DUMP_FAILED'),true);	
		}
	}
	
	public function delete()
	{
		$groupname = $_REQUEST['file'];
		$db_back_dir = $this->getRealPath()."/Public/db_backup/";
		$sql_list = $this->dirFileInfo($db_back_dir,".sql");
		$deleteGroup = $sql_list[$groupname];
		foreach($deleteGroup as $fileItem)
		{
			@unlink($db_back_dir.$fileItem['filename']);
		}
		$this->success(L('DEL_SUCCESS'),true);		
	}
	
	public function restore()
	{
		set_time_limit(0);
		$groupname = $_REQUEST['file'];
		$db_back_dir = $this->getRealPath()."/Public/db_backup/";
		$sql_list = $this->dirFileInfo($db_back_dir,".sql");
		$restoreGroup = $sql_list[$groupname];
		
		$sqlDump = new SqlDump();
		$msg = $sqlDump->restore($restoreGroup);
		if($msg=="")
		{
			$msg = '数据库恢复成功';
			$this->saveLog(1,0,$msg);
			clear_cache();
			$this->success(L('RESTORE_SUCCESS'),true);		
		}
		else 
		{
			$msg = '数据库恢复失败';
			$this->saveLog(0,0,$msg);
			$this->error($msg,true);	
		}
		
	}
	
	
	//用于获取指定路径下的文件组
	private function dirFileInfo($dir,$type)   
	{  
		  if(!is_dir($dir))
		  		return   false;  
		  $dirhandle=opendir($dir);  
		  $arrayFileName=array();  
		  while(($file   =   readdir($dirhandle))   !==   false)
		  {  	
		 	 if (($file!=".")&&($file!=".."))   
		 	 {  
		  		$typelen=0-strlen($type);  		   
		  		if	(substr($file,$typelen)==$type)  
		  		{
		  			$file_only_name = substr($file,0,strlen($file)+$typelen);
		  			$file_name_arr = explode("_",$file_only_name);
		  			$file_only_name = $file_name_arr[0];
		  			$fileIdx = $file_name_arr[1];
		  			if($fileIdx)
		  			{
			 	 		$arrayFileName[$file_only_name][$fileIdx]=array
			 	 		(
			 	 			'filename'=>$file,
			 	 			'filedate'=>toDate($file_only_name)
			 	 		);
		  			}
		  			else 
		  			{
		  				$arrayFileName[$file_only_name][]=array
			 	 		(
			 	 			'filename'=>$file,
			 	 			'filedate'=>toDate($file_only_name)
			 	 		);
		  			}
		  		}
		  	}  
		   
		  }  
		  //通过ArrayList类对数组排序
		  foreach($arrayFileName as $k=>$group)
		  {
		  		$arr = new ArrayList($group);
		  		$arr->ksort();
		  		$arrayFileName[$k] = $arr->toArray();
		  }

	  	return   $arrayFileName;  
   }
   
   public function zipdown()
   {
   		set_time_limit(0);
		$groupname = $_REQUEST['file'];		
		$db_back_dir = $this->getRealPath()."/Public/db_backup/";
		$sql_list = $this->dirFileInfo($db_back_dir,".sql");
		$file_group = $sql_list[$groupname];
		$ziper = new PHPZip();
		$file_list = array();
		$file_name_list = array();
		foreach($file_group as $fileItem)
		{
			array_push($file_list,$db_back_dir.$fileItem['filename']);
			array_push($file_name_list,$fileItem['filename']);
		}		
		$ziper->addFiles($file_list,$file_name_list);  //array of files
		$ziper->output($groupname.".zip");   	
   }
}
?>