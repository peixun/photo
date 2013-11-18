<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 文件上传
class FileUploadAction extends CommonAction{
	public function doUpload()
	{
		$result = $this->uploadFile(0,'kind',0,true);		
		$list = $result['uploadList'];
		if($result['status'])
		{
			$file_url = ".".$list[0]['recpath'].$list[0]['savename'];
			$html = '<html>';
			$html.= '<head>';
			$html.= '<title>Insert Image</title>';
			$html.= '<meta http-equiv="content-type" content="text/html; charset=utf-8">';
			$html.= '</head>';
			$html.= '<body>';
			$html.= '<script type="text/javascript">';
			$html.= 'parent.parent.KE.plugin["image"].insert("' . $_POST['id'] . '", "' . $file_url . '","' . $_POST['imgTitle'] . '","' . $_POST['imgWidth'] . '","' . $_POST['imgHeight'] . '","' . $_POST['imgBorder'] . '","' . $_POST['align'] . '");';
			$html.= '</script>';
			$html.= '</body>';
			$html.= '</html>';
		echo $html;
		}
		else
		{
			echo "<script>alert('".$result['msg']."');</script>";
		}
	
	}
	
}
?>