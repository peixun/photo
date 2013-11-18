<?php
// 本类由系统自动生成，仅供测试用途
class PhotoAction extends PublicAction
	{

		public function index()
			{
				import ( "@.ORG.Pages" );
				//--------最新相册---------
				$PhotoAlbum = D ( "PhotoAlbum" );
				$count = $PhotoAlbum->Count ();
				$p = new Page ( $count, 6 );
				$photoAlbumList = $PhotoAlbum->query ( "select * from xc_photo_album order by create_time desc limit  {$p->firstRow} ,{$p->listRows} " );
				$p->setConfig ( 'header', '篇记录' );
				$p->setConfig ( 'prev', "<prev" );
				$p->setConfig ( 'next', 'next>' );
				$p->setConfig ( 'first', '<<' );
				$p->setConfig ( 'last', '>>' );
				$page = $p->show ();
				$this->assign ( "photoAlbumList", $photoAlbumList );
				$this->assign ( "page", $page );
				$this->display ();

			}
		public function read()
			{
				import ( "@.ORG.Pages" );
				//--------某相册相片---------
				$PicGallery = D ( "PicGallery" );

				$count = $PicGallery->query ( "select count(*) as count from xc_pic_gallery where  pic_id=" . $_GET ["albumid"] . " and model='Photo' " );
				$count = $count [0] ["count"];
				$p = new Page ( $count, 6 );

				$photoList = $PicGallery->query ( "select * from xc_pic_gallery where  pic_id=" . $_GET ["albumid"] . " and model='Photo' order by 'create_time' desc limit  {$p->firstRow} ,{$p->listRows}" );

				$p->setConfig ( 'header', '篇记录' );
				$p->setConfig ( 'prev', "<prev" );
				$p->setConfig ( 'next', 'next>' );
				$p->setConfig ( 'first', '<<' );
				$p->setConfig ( 'last', '>>' );
				$page = $p->show ();
				if ($photoList)
					{
						$this->assign ( "photoList", $photoList );
					}
				$this->assign ( "page", $page );
				$this->display ();

			}
		function setCount()
			{
				$id = $_POST ["photo_id"];
				$PlcGallery = D ( "PicGallery" );
				$where ["id"] = $id;
				$data ["clickcount"] = array ('exp', 'clickcount+1' );
				$pic = $PlcGallery->where ( $where )->save ( $data );
				$count = $PlcGallery->where ( $where )->find ();
				echo $count["clickcount"];

			}
	}
?>