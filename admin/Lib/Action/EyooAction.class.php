<?php
/*********************/
/*                   */
/*  Version : 5.1.0  */
/*  Author  : RM     */
/*  Comment : 071223 */
/*                   */
/*********************/

class EyooAction extends Action
{

				//public function __construct( )
				//{
								////$this->checkAuthorization( );
								////$this->checkInstall();
								//filter_request( $_REQUEST );
								//filter_request( $_POST );
								//filter_request( $_GET );
								//$langSet = C( "DEFAULT_LANG" );
                                ////echo $langSet;
                               //// echo
								//define( "FANWE_LANG_SET", strtolower( $langSet ) );
								//if ( is_file( LANG_PATH.$langSet."/common.php" ) )
								//{
												//L( include( LANG_PATH.$langSet."/common.php" ) );
								//}
								////$files = scandir( $this->getRealPath( )."/admin/Lang/".$langSet."/payment/" );
								////foreach ( $files as $file )
								////{
								////				if ( !( $file != "." ) && !( $file != ".." ) )
								////				{
								////								L( include( LANG_PATH.$langSet."/payment/".$file ) );
								////				}
								////}
								//$group = "";
								//if ( define( "GROUP_NAME" ) )
								//{
												//$group = GROUP_NAME.C( "TMPL_FILE_DEPR" );
												//if ( is_file( LANG_PATH.$langSet."/".$group."lang.php" ) )
												//{
																//L( include( LANG_PATH.$langSet."/".$group."lang.php" ) );
												//}
								//}
								//if ( is_file( LANG_PATH.$langSet."/".$group.strtolower( MODULE_NAME ).".php" ) )
								//{

												//L( include( LANG_PATH.$langSet."/".$group.strtolower( MODULE_NAME ).".php" ) );
								//}

								//$langItem = D( "LangConf" )->where( "lang_name='".C( "DEFAULT_LANG" )."'" )->find( );

								//define( "SHOP_NAME", $langItem['shop_name'] );

								////$this->assign( "SHOP_NAME", '易友软件' );
								//$default_lang_id = $langItem['id'];
								//define( "DEFAULT_LANG_ID", $default_lang_id );
                               ///// echo $default_lang_id;
                               //// exit();
								////$this->assign( "DEFAULT_LANG_ID", DEFAULT_LANG_ID );
								////Autorun();
				//}

//				public function getRealPath()
//				{
//								return getcwd();
//				}

				//protected function saveLog( $result = "1", $dataId = 0, $msg = "" )
				//{
								//if ( eyooC( "APP_LOG" ) )
								//{
												//$log_app = C( "LOG_APP" );
												//$log_module = MODULE_NAME;
												//$log_action = $_REQUEST[c( "VAR_ACTION" )];
												//if ( in_array( $log_action, $log_app[$log_module] ) )
												//{
																//$logData['log_module'] = $log_module;
																//$logData['log_action'] = $log_action;
																//if ( !$dataId )
																//{
																				//$pk = M( MODULE_NAME )->getPk( );
																				//$dataId = intval( $_REQUEST[$pk] );
																//}
																//$logData['data_id'] = $dataId;
																//$logData['log_time'] = gmttime( );
																//$logData['adm_id'] = intval( $_SESSION[C( "USER_AUTH_KEY" )] );
																//$logData['ip'] = get_client_ip( );
																//$logData['log_result'] = $result;
																//$logData['log_msg'] = $msg;
																//D( "Log" )->add( $logData );
												//}
								//}
				//}

				//protected function uploadFile( $water = 0, $dir = "attachment", $uploadType = 0, $showstatus = FALSE )
				//{
								//$water_mark = $this->getRealPath( ).eyooC( "WATER_IMAGE" );
								//$alpha = eyooC( "WATER_ALPHA" );
								//$place = eyooC( "WATER_POSITION" );
								////( );
								//$upload = new UploadFile( );
								//$upload->maxSize = eyooC( "MAX_UPLOAD" );
								//$upload->allowExts = explode( ",", eyooC( "ALLOW_UPLOAD_EXTS" ) );
								//if ( $uploadType )
								//{
												//$save_rec_Path = "/Public/upload/".$dir."/origin/".todate( gmttime( ), "Ym" )."/";
								//}
								//else
								//{
												//$save_rec_Path = "/Public/upload/".$dir."/".todate( gmttime( ), "Ym" )."/";
								//}
								//$savePath = $this->getRealPath( ).$save_rec_Path;
								//if ( !is_dir( $savePath ) )
								//{
												//mk_dir( $savePath );
								//}
								//$upload->saveRule = "uniqid";
								//$upload->savePath = $savePath;
								//if ( $upload->upload( ) )
								//{
												//$uploadList = $upload->getUploadFileInfo( );
												//foreach ( $uploadList as $k => $fileItem )
												//{
																//if ( $uploadType )
																//{
																				//$big_width = eyooC( "BIG_WIDTH" );
																				//$big_height = eyooC( "BIG_HEIGHT" );
																				//$small_width = eyooC( "SMALL_WIDTH" );
																				//$small_height = eyooC( "SMALL_HEIGHT" );
																				//$file_name = $fileItem['savepath'].$fileItem['savename'];
																				//$big_save_path = str_replace( "origin", "big", $savePath );
																				//if ( !is_dir( $big_save_path ) )
																				//{
																								//mk_dir( $big_save_path );
																				//}
																				//$big_file_name = str_replace( "origin", "big", $file_name );
																				//if ( eyooC( "AUTO_GEN_IMAGE" ) == 1 )
																				//{
																								//Image::thumb( $file_name, $big_file_name, "", $big_width, $big_height );
																				//}
																				//else
																				//{
																								//@copy( $file_name, $big_file_name );
																				//}
																				//if ( $water && file_exists( $water_mark ) && eyooC( "AUTO_GEN_IMAGE" ) == 1 )
																				//{
																								//Image::water( $big_file_name, $water_mark, $big_file_name, $alpha, $place );
																				//}
																				//$small_save_path = str_replace( "origin", "small", $savePath );
																				//if ( !is_dir( $small_save_path ) )
																				//{
																								//mk_dir( $small_save_path );
																				//}
																				//$small_file_name = str_replace( "origin", "small", $file_name );
																				//Image::thumb( $file_name, $small_file_name, "", $small_width, $small_height );
																				//$big_save_rec_Path = str_replace( "origin", "big", $save_rec_Path );
																				//$small_save_rec_Path = str_replace( "origin", "small", $save_rec_Path );
																				//$uploadList[$k]['recpath'] = $save_rec_Path;
																				//$uploadList[$k]['bigrecpath'] = $big_save_rec_Path;
																				//$uploadList[$k]['smallrecpath'] = $small_save_rec_Path;
																//}
																//else
																//{
																				//$uploadList[$k]['recpath'] = $save_rec_Path;
																				//$file_name = $fileItem['savepath'].$fileItem['savename'];
																				//if ( !$water && !file_exists( $water_mark ) )
																				//{
																								//Image::water( $file_name, $water_mark, $file_name, $alpha, $place );
																				//}
																//}
												//}
												//if ( $showstatus )
												//{
																//$result['status'] = TRUE;
																//$result['uploadList'] = $uploadList;
																//$result['msg'] = "";
																//return $result;
												//}
												//return $uploadList;
								//}
								//if ( $showstatus )
								//{
												//$result['status'] = FALSE;
												//$result['uploadList'] = FALSE;
												//$result['msg'] = $upload->getErrorMsg( );
												//return $result;
								//}
								//return $uploadList;
				//}

				//private function checkInstall( )
				//{
								//if ( !file_exists( $this->getRealPath( )."/Public/install.lock" ) )
								//{
												//Dir::deldir( $this->getRealPath( )."/admin/Runtime/" );
												//Dir::deldir( $this->getRealPath( )."/home/Runtime/" );
												//Dir::deldir( $this->getRealPath( )."/install/Runtime/" );
												//@mkdir( $this->getRealPath( )."/admin/Runtime/", 511 );
												//@mkdir( $this->getRealPath( )."/home/Runtime/", 511 );
												//@mkdir( $this->getRealPath( )."/install/Runtime/", 511 );
												//header( "Location:".__ROOT__."/install.php" );
												//exit( );
								//}
				//}

				//private function init_module( )
				//{
								//$arr = Dir::getlist( $this->getRealPath( )."/admin/Lib/Action/" );
								//$modules = M( "RoleNode" )->where( "auth_type = 1" )->findAll( );
								//$exists_modules = array( );
								//foreach ( $arr as $k => $v )
								//{
												//if ( !( $v != "." ) && !( $v != ".." ) )
												//{
																//$module = str_ireplace( "Action.class.php", "", $v );
																//if ( !( $module != $v ) && !( $module != "Fanwe" ) && !( $module != "Common" ) && !( $module != "Public" ) && !( $module != "GoodsSpec" ) && !( $module != "Promote" ) && !( $module != "UserConsignee" ) && !( $module != "SpecType" ) && !( $module != "Brand" ) && !( $module != "Test" ) && !( $module != "Verify" ) && !( $module != "Index" ) && !( $module != "Currency" ) )
																//{
																				//if ( M( "RoleNode" )->where( "module='".$module."' and action=''")->count() == 0 )
																				//{
																								//$module_data['status'] = 1;
																								//$module_data['module'] = $module;
																								//$module_data['module_name'] = $module;
																								//$module_data['auth_type'] = 1;
																								//M( "RoleNode" )->add( $module_data );
																				//}
																				//array_push( &$exists_modules, $module );
																//}
												//}
								//}
								//M( "RoleNode" )->where( array("module" => array("not in",$exists_modules),"action" => ""))->delete();
				//}

				//private function checkAuthorization()
				//{
								//$auth_file = getcwd( )."/domain_key.dat";
								//if ( !file_exists( $auth_file ) )
								//{
												//header( "Content-Type: text/html; charset=utf-8" );
												//echo "没有授权文件";
												//exit( );
								//}
								//$str = @file_get_contents( $auth_file );
								//( 64 );
								//$xtea = new xTeaKey( );
								//$str = $xtea->decrypt( $str, "FANWEAWFIGQGROUP" );
								//base64_encode( base64_encode( base64_encode( serialize( $code ) ) ).base64_encode( serialize( $code ) ) );
								//$arr = explode( "|", base64_decode( $str ) );
								//$arr = unserialize( $arr[1] );
								//foreach ( $arr as $k => $v )
								//{
												//$arr[$k] = base64_decode( base64_decode( $v ) );
								//}
								//$host = $_SERVER['HTTP_HOST'];
								//$host = explode( ":", $host );
								//$host = $host[0];
								//$passed = FALSE;
								//foreach ( $arr as $k => $v )
								//{
												//if ( !( substr( $v, 0, 2 ) == "*." ) )
												//{
																//continue;
												//}
												//$preg_str = substr( $v, 2 );
												//if ( !( 0 < preg_match( "/".$preg_str."\$/", $host ) ) )
												//{
																//continue;
												//}
												//$passed = TRUE;
												//break;
								//}
								//if ( !$passed || !in_array( $host, $arr ) )
								//{
												//header( "Content-Type: text/html; charset=utf-8" );
												//echo "没有授权的域名";
												//exit( );
								//}
				//}

}

?>
