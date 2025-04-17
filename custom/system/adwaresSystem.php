<?php

	/**
	 * システムコールクラス
	 * 
	 * @author ----
	 * @version 1.0.0
	 * 
	 */
	class adwaresSystem extends System
	{
		/**********************************************************************************************************
		 * 汎用システム用メソッド
		 **********************************************************************************************************/



		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// 登録関係
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

		/**
		 * 登録前段階処理。
		 * フォーム入力以外の方法でデータを登録する場合は、ここでレコードに値を代入します。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param rec フォームのからの入力データを反映したレコードデータ。
		 */
		function registProc( &$gm, &$rec, $loginUserType, $loginUserRank ,$check = false)
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			// **************************************************************************************

			$limitTime = mktime( 0 , 0 , 0 , $_POST[ 'limit_month' ] , $_POST[ 'limit_day' ] + 1 , $_POST[ 'limit_year' ] );

			$db = $gm[ $_GET[ 'type' ] ]->getDB();

			$db->setData( $rec , 'limit_time' , $limitTime );

			AdwaresLogic::setSelectCarrierName( $rec );

			parent::registProc( $gm, $rec, $loginUserType, $loginUserRank ,$check );
		}

		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// 編集関係
		/////////////////////////////////////////////////////////////////////////////////////////////////////////



		/**
		 * 編集前段階処理。
		 * フォーム入力以外の方法でデータを登録する場合は、ここでレコードに値を代入します。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param rec フォームのからの入力データを反映したレコードデータ。
		 */
		function editProc( &$gm, &$rec, $loginUserType, $loginUserRank ,$check = false)
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			// **************************************************************************************
			
			$limitTime = mktime( 0 , 0 , 0 , $_POST[ 'limit_month' ] , $_POST[ 'limit_day' ] + 1 , $_POST[ 'limit_year' ] );

			$db = $gm[ $_GET[ 'type' ] ]->getDB();

			$db->setData( $rec , 'limit_time' , $limitTime );

			AdwaresLogic::setSelectCarrierName( $rec );

			parent::editProc( $gm, $rec, $loginUserType, $loginUserRank ,$check );			
		}

		/**
		 * 検索処理。
		 * フォーム入力以外の方法で検索条件を設定したい場合に利用します。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param table フォームのからの入力内容に一致するレコードを格納したテーブルデータ。
		 */
		function searchProc( &$gm, &$table, $loginUserType, $loginUserRank )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $LOGIN_ID;
			global $terminal_type;
			// **************************************************************************************

			if( $_GET[ '_carrier' ] )
			{
				switch( $_GET[ '_carrier' ] ) //端末の種類で分岐
				{
					case 'docomo'   :
					case 'au'       :
					case 'softbank' :
					case 'iphone'   :
					case 'android'  :
					{
						$db     = $gm[ 'adwares' ]->getDB();
						$tableA = $db->searchTable( $table , 'use_carrier_url' , '=' , false );
						$tableA = $db->searchTable( $tableA , 'url' , '!=' , '' );
						$tableB = $db->searchTable( $table , 'use_carrier_url' , '=' , true );
						$tableB = $db->searchTable( $tableB , 'url_' . $_GET[ '_carrier' ] , '!=' , '' );
						$table  = $db->orTable( $tableA , $tableB );

						break;
					}

					case 'pc' :
					{
						$db    = $gm[ 'adwares' ]->getDB();
						$table = $db->searchTable( $table , 'use_carrier_url' , '=' , false );
					}
				}
			}

			$table = WS::Finder( 'adwares' )->searchQueryTable( $table );
			$table = WS::Finder( 'adwares' )->searchReadableTable( $table );

			if( !$_GET[ 'sort' ] )
				{ $table = WS::Finder( 'adwares' )->sortTable( $table ); }

			parent::searchProc( $gm, $table, $loginUserType, $loginUserRank );			
		}
	}

?>