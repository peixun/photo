<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 编辑
class EditorAction extends CommonAction{
	public function editTpl()
	{
		$arr  =   Dir::getList($this->getRealPath()."/home/Tpl/");
		foreach($arr as $item)
		{
			if($item!='..'&&$item!='.'&&$item!='.svn')
			{
				$themes[] = $item;
			}
		}
		$this->assign("themes",$themes);	
		$this->display();
	}
	
	public function getFileList()
	{
		$tmpl = $_REQUEST['tmpl'];
		$arr =  get_all_files($this->getRealPath()."/home/Tpl/".$tmpl."/");
		
		foreach($arr as $item)
		{
			if(substr($item,-6)==".tpl")
			{
				//$item = preg_replace("/.tpl/", "", $item);
				$item = explode("/".$tmpl."/",$item);
				$item = $item[1];
				$files[] = $item;
			}
		}		
		echo json_encode($files);
	}
	
	public function readTplContent()
	{
		$tmpl = $_REQUEST['tmpl'];
		$file = $_REQUEST['file'];
		$filename = $this->getRealPath()."/home/Tpl/".$tmpl.$file;
		echo @file_get_contents($filename);
	}
	
	public function updateTpl()
	{
		$tmpl = $_REQUEST['tmpl'];
		$tpl = $_REQUEST['tpl'];
		$file_content = $_REQUEST['file_content'];
		$filename = $this->getRealPath()."/home/Tpl/".$tmpl.$tpl;
		@file_put_contents($filename,$file_content);
		//$this->writeUTF8File($filename,$file_content);
		$this->success("编辑成功");
	}
	
	
	//语言包
	public function editLang()
	{
		$arr  =   Dir::getList($this->getRealPath()."/home/Lang/");
		foreach($arr as $item)
		{
			if($item!='..'&&$item!='.'&&$item!='.svn')
			{
				$langs[] = $item;
			}
		}
		$this->assign("langs",$langs);	
		$this->display();	
	}
	
	public function getLangFileList()
	{
		$lang = $_REQUEST['lang'];
		$arr =  get_all_files($this->getRealPath()."/home/Lang/".$lang."/");
		
		foreach($arr as $item)
		{
			if(substr($item,-4)==".php")
			{
				//$item = preg_replace("/.tpl/", "", $item);
				$item = explode("/".$lang."/",$item);
				$item = $item[1];
				$files[] = $item;
			}
		}
		
		echo json_encode($files);
	}
	
	public function readLangContent()
	{
		$lang = $_REQUEST['lang'];
		$lang_file = $_REQUEST['lang_file'];
		$filename = $this->getRealPath()."/home/Lang/".$lang.$lang_file;
		echo @file_get_contents($filename);
	}

	public function updateLang()
	{
		$lang = $_REQUEST['lang'];
		$lang_file = $_REQUEST['lang_file'];
		$lang_file_content = $_REQUEST['lang_file_content'];
		$filename = $this->getRealPath()."/home/Lang/".$lang.$lang_file;
		@file_put_contents($filename,$lang_file_content);
		//$this->writeUTF8File($filename,$lang_file_content);
		$this->success("编辑成功");
	}
	
	private function writeUTF8File($filename,$content) {
	   $dhandle=fopen($filename,"w");
	   fwrite($dhandle, pack("CCC",0xef,0xbb,0xbf));
	   fwrite($dhandle,$content);
	   fclose($dhandle);
	}
}
?>