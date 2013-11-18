<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 商品图库
class GoodsGalleryAction extends CommonAction{
	public function batch()
	{
		$this->display();
	}
	public function doBatch()
	{
    
	    $gallery_id = intval($_REQUEST['id']);  //如果提交的图库ID
	    if($gallery_id==0)
	    {
	    	//第一个
	    	$res = D("GoodsGallery")->order("id asc")->limit(1)->find();  //查出第一第二个图库   	
	    }
	    else 
	    {
	    	$res = D("GoodsGallery")->where("id>".$gallery_id)->order("id asc")->limit(1)->find();  //查出第一第二个图库
	    }
		if($res)
		{
		    $info = $this->reMake($res);
		    if($info)
		    {
		    		$result['html'] = $info['origin_img']."&nbsp;&nbsp;".L("REMAKE_SUCCESS")."<br />";
		    		$result['id'] = intval($res['id']);
		    }
		    else 
		    {
		    		$result['html'] = $info['origin_img']."&nbsp;&nbsp;".L("REMAKE_FAILED")."<br />";
		    		$result['id'] = intval($res['id']);
		    }	
		}
		else 
		{
			$result['html'] = '';
			$result['id'] = 0;
			$msg = '操作了全站图片批处理';
			$this->saveLog(1,0,$msg);
		}
		
		
	    echo json_encode($result);
	}
	/**
	 * 重新处理图库的函数
	 *
	 */
	private function reMake($gallery_info)
	{
		//初始化图片处理的参数
		$water = eyooC("WATER_MARK");  	//是否开启水印
		$water_mark = $this->getRealPath().eyooC("WATER_IMAGE");   //水印图
		$alpha = eyooC("WATER_ALPHA");  	//水印图
	    $place = eyooC("WATER_POSITION");  //位置
	    
	    $big_width = eyooC("BIG_WIDTH");
        $big_height = eyooC("BIG_HEIGHT");
        $small_width = eyooC("SMALL_WIDTH");
        $small_height = eyooC("SMALL_HEIGHT");
        			
	    
	    $origin_img = $this->getRealPath().$gallery_info['origin_img']; //原图物理路径
	    $big_img = $this->getRealPath().$gallery_info['big_img'];  		//大图
	    $small_img = $this->getRealPath().$gallery_info['small_img'];   //小图
	    
	    if(file_exists($origin_img))
	    {
		    
		    //缩放处理
		    Image::thumb($origin_img,$small_img,'',$small_width,$small_height);
		    Image::thumb($origin_img,$big_img,'',$big_width,$big_height);
		    
		    //水印处理
			if($water&&file_exists($water_mark))
		    {
		        Image::water($big_img,$water_mark,$big_img,$alpha,$place);	
		    }
		    
		    return $gallery_info;
	    }
	    else 
	    {
	    	return false;
	    }
	}
}
?>