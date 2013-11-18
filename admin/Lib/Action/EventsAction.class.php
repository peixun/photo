<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  liuqiang <liuqiang@eyoo.cn>
 * @time    2011-02-08
 * @Action  Index Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */

class EventsAction extends CommonAction {
	public function _before_update() {
		
		if (! empty ( $_FILES ['images'] ['tmp_name'] )) {
			
			import ( "ORG.Net.UploadFile" );
			$upload = new UploadFile ();
			$upload->maxSize = 1000000000000;
			$upload->allowExts = explode ( ',', 'jpg,gif,png,jpeg,flv' );
			$upload->thumb = false;
			$upload->thumbPrefix = 'm_,s_';
			$upload->thumbMaxHeight = '98,60';
			$upload->thumbMaxWidth = '130,80';
			
			$upload->saveRule = uniqid;
			
			$upload->savePath = './Public/upload/events/';
			if (! $upload->upload ()) {
				
				$_POST ['image'] = $_POST ["images"];
			} else {
				
				$uploadList = $upload->getUploadFileInfo ();
				
				$_POST ['images'] = $uploadList [0] ["savename"];
				$_POST ['update_time'] = time ();
			}
		}
	}
	public function _before_insert() {
		
		if (! empty ( $_FILES ['images'] ['tmp_name'] )) {
			
			import ( "ORG.Net.UploadFile" );
			$upload = new UploadFile ();
			$upload->maxSize = 1000000000000;
			$upload->allowExts = explode ( ',', 'jpg,gif,png,jpeg,flv' );
			$upload->thumb = false;
			$upload->thumbPrefix = 'm_,s_';
			$upload->thumbMaxHeight = '98,60';
			$upload->thumbMaxWidth = '130,80';
			
			$upload->saveRule = uniqid;
			
			$upload->savePath = './Public/upload/events/';
			if (! $upload->upload ()) {
				
				$_POST ['image'] = $_POST ["images"];
			} else {
				
				$uploadList = $upload->getUploadFileInfo ();
				
				$_POST ['images'] = $uploadList [0] ["savename"];
				$_POST ['create_time'] = time ();
			}
		}
	}
}
?>
