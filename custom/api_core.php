<?php

	/*******************************************************************************************************
	 * <PRE>
	 *
	 * api_core.php - apiアクセス用
	 * JavaScriptからデータを取得したり変更したりする際や
	 * 情報変更のフォームをinfoやindexに埋め込む場合等に使用。
	 *
	 * </PRE>
	 *******************************************************************************************************/
	
	class Api_core
	{
		/**********************************************************************************************************
		 *　ajax_core
		 **********************************************************************************************************/
		
		
		/**
		 * データの更新。
		 * 
		 * @param table 更新するレコードのテーブル名。
		 * @param id 更新するレコードID。
		 * @param column 更新するカラム。
		 * @param value セットする値。
		 */
		function update( $param )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $loginUserType;
			global $loginUserRank;
			global $ACTIVE_ACTIVATE;
			// **************************************************************************************
			
			if( $loginUserType == 'admin' && $loginUserRank == $ACTIVE_ACTIVATE )
			{
				$db	 = $gm[ $param['table'] ]->getDB();
				
				$rec	 = $db->selectRecord( $param['id'] );
				if( isset($rec) )
				{
					$db->setData( $rec , $param['column'] , $param['value'] );
					$db->updateRecord( $rec );
				}
			}
			
			if( $param['info_change_flg'] ) { info_change_result( $param, ' { "success" : true , "msg" : "success update." } ' ); }
		}
		
		
		/**
		 * データの削除。
		 * 
		 * @param type 削除するレコードのテーブル名。
		 * @param id 削除するレコードID。
		 */
		function delete( $param )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $loginUserType;
			global $loginUserRank;
			global $ACTIVE_ACTIVATE;
			// **************************************************************************************
			
			if( $loginUserType == 'admin' && $loginUserRank == $ACTIVE_ACTIVATE )
			{
				$db		 = $gm[ $param['type'] ]->getDB();
				
    	        $rec	 = $db->selectRecord( $param['id'] );
				$draws	 = '{ "success" : false , "msg" : "no match id." }';
				if( isset($rec) )
				{
					$_GET['type'] =	$param['type'];	 // system.php内部では$_GET['type']で処理を分岐しているので念のため
					
					$sys = SystemUtil::getSystem( $param['type'] );
					$sys->deleteProc( $gm, $rec, $loginUserType, $loginUserRank );
					$sys->deleteComp( $gm, $rec, $loginUserType, $loginUserRank );
					
					$draws = '{ "success" : true , "msg" : "success delete." }';
				}
			}
			
			if( $param['info_change_flg'] ) { info_change_result( $param, $draws ); }

		}
		
		
		/**
		 * 市区町村情報の取得。
		 * 
		 * @param id 市区町村情報を取得する都道府県のID。
		 */
		function load_addsub( $param )
		{
			global $gm;
			print $gm['parentCategory']->getCCResult( null, '<!--# ecode drawAddsubAjaxForm '.$param['id'].' #-->' );
		}

		function GetSecondCategoryForm( $param )
		{
			global $gm;

			print $gm['moba8SecondCategory']->getCCResult( null , '<!--# code tableSelectForm second_category moba8SecondCategory name id  選択してください  parent_id = ' . $param[ 'first_category' ] . ' #-->' );
		}

		/**********************************************************************************************************
		 *　info_change_sys
		 **********************************************************************************************************/
		
		/**
		 * データの更新。
		 * 
		 * @param type 更新するレコードのテーブル名。
		 * @param id 更新するレコードID。
		 */
		function set( $param )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $gm;
			global $loginUserType;
			global $loginUserRank;
			global $ACTIVE_ACTIVATE;
			// **************************************************************************************
			
			if( $loginUserType == 'admin' && $loginUserRank == $ACTIVE_ACTIVATE )
			{
				$db	 = $gm[ $param['type'] ]->getDB();
				
				$rec = $db->selectRecord( $param['id'] );
				if( isset($rec) )
				{
					foreach( $db->colName as $name )
					{
						if( isset($param[$name]) && strlen($param[$name]) ) { $db->setData( $rec, $name, $param[ $name ] ); }
					}
					
					$db->updateRecord( $rec );
				}
			}

			if( $_SERVER[ 'HTTP_REFERER' ] )
			{
				global $HOME;
				preg_match( '/.*\/([^\/]*(\?.*)?)/' , $_SERVER[ 'HTTP_REFERER' ] , $match );
				SystemUtil::innerLocation( $match[ 1 ] );
			}
			else
				SystemUtil::innerLocation( 'info.php?type=' . $param[ 'type' ] . '&id=' . $param[ 'id' ] );

//			if( $param['info_change_flg'] ) { $this->info_change_result( $param, '{ "success" : true , "msg" : "success table set." }' ); }

		}		
				
				
		/**
		 * info_change_sysと同等の処理を返す。
		 * 
		 * @param js JSONデータ返却フラグ
		 * @param draws JSONデータ
		 * @param jump リロード先
		 */
		function  info_change_result( &$param, $draws = '' )
		{
			if( $param['js'] == "true" ) { print $draws; }
			else
			{
				$jump = "index.php";
				if( isset($param['jump']) && strlen($param['jump']) )  { $jump = $param['jump']; }
				SystemUtil::innerLocation( $jump);
			}
		}
		
	}

?>