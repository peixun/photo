<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 模板布局
class LayoutAction extends CommonAction{

	public function add()
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
		parent::add();
	}
	public function edit()
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
		
		parent::edit();
	}
	public function getPageList()
	{
		$tmpl = $_REQUEST['tmpl'];
		$arr =  get_all_files($this->getRealPath()."/home/Tpl/".$tmpl."/");
		
		foreach($arr as $item)
		{
			if(substr($item,-6)==".tpl")
			{
				$item = preg_replace("/.tpl/", "", $item);
				$item = explode("/".$tmpl."/",$item);
				$item = $item[1];
				$files[] = $item;
			}
		}
		


        $xml = simplexml_load_file($this->getRealPath()."/home/Tpl/".$tmpl."/Pages.xml");
        

        $pages = array();
        
        if($xml)
        {
	        $xml = ((array)($xml));
	        foreach($xml['page'] as $item)
	        {
	        	$item = (array)$item;
	        	if(count($item['file'])==0)
	        	$item['file'] = '';
	        	$pages[] = $item;
	        }
        }
		
        $res['files'] = $files;
        $res['pages'] = $pages;
		echo json_encode($res);
		
	}
	
	public function getLayoutList()
	{
		$tmpl = $_REQUEST['tmpl'];
		$page = $_REQUEST['page'];
		
		$file_content = @file_get_contents($this->getRealPath()."/home/Tpl/".$tmpl.$page.".tpl");
		
		$layout_array = array();
		preg_match_all("/<eyoo:layout(\s+)layout_id=\"(\S+)\"([^>]*)>/",$file_content,$layout_array);
		
		foreach($layout_array[2] as $item)
		{
			//if(M("Layout")->where(array('tmpl'=>$tmpl,'page'=>$page,'layout_id'=>$item))->count()==0)
				$layout_ids[] = $item;
		}
		
		echo json_encode($layout_ids);
	}
	
	public function getCateList()
	{
		$rec_module = $_REQUEST['rec_module'];
		
		$lang_envs = D("LangConf")->findAll();
		$lang_ids = array();
		$dispname_arr = array();
		$lang_names = array();
		foreach($lang_envs as $lang_item)
		{
			$dispname_arr[] = "name_".$lang_item['id'];
			$lang_ids[]=$lang_item['id'];
			$lang_names[] = $lang_item['lang_name'];
		}
		
		$default_lang_id = D("LangConf")->where("lang_name='".C('DEFAULT_LANG')."'")->field("id")->find();
		$default_lang_id = $default_lang_id['id'];  //默认语言的ID
		$select_dispname = "name_".$default_lang_id;
		
		$lang_ids = implode(",",$lang_ids);
		$this->assign("lang_ids",$lang_ids);
		$lang_names = implode(",",$lang_names);
		$this->assign("lang_names",$lang_names);
		
		if($rec_module != "AdvPosition")
		{
			$cate_list = D($rec_module."Cate")-> where("status=1")-> findAll();
			$cate_list = D($rec_module."Cate")-> toFormatTree($cate_list,$dispname_arr);
			foreach($cate_list as $k=>$v)
			{
				$cate_list[$k]['name'] = $v['name_'.$default_lang_id];
			}
		}
		else
			$cate_list = D($rec_module)->field("id,name")-> findAll();
		echo json_encode($cate_list);
	}

}
?>