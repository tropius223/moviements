<?php

	/**
	 * システムコールクラス
	 * 
	 * @author ----
	 * @version 1.0.0
	 * 
	 */
	class multimailSystem extends System
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

			if( !$_POST[ 'receive_id' ] )
			{
				$udb    = $gm[ 'nUser' ]->getDB();
				$utable = $udb->getTable();

				for( $i = 0 ; $i < $udb->getRow( $utable ) ; $i++ )
				{
					$urec     = $udb->getRecord( $utable , $i );
					$userID[] = $udb->getData( $urec , 'id' );
				}

				$udb->setData( $rec , 'receive_id' , implode( '/' , $userID ) );
			}

			parent::registProc( $gm, $rec, $loginUserType, $loginUserRank ,$check );
		}

		/**********************************************************************************************************
		 * 汎用システム描画系用メソッド
		 **********************************************************************************************************/

		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// 登録関係
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

		/**
		 * 登録フォームを描画する。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param loginUserType ログインしているユーザの種別
		 * @param loginUserRank ログインしているユーザの権限
		 */
		function drawRegistForm( &$gm, $rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			// **************************************************************************************

			$db  = $gm[ 'nUser' ]->getDB();
			$row = $db->getRow( $db->getTable() );

			if( !$row )
				$loginUserType = 'notFound';

			parent::drawRegistForm( $gm, $rec, $loginUserType, $loginUserRank ); // 二重描画になるので編集する場合は削除
		}
	}

?>