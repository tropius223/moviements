<?php

	/**
	 * システムコールクラス
	 * 
	 * @author ----
	 * @version 1.0.0
	 * 
	 */
	class newsSystem extends System
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

			$db = $gm[ $_GET[ 'type' ] ]->getDB();
			$db->setData( $rec , 'id' , SystemUtil::getNewId( $db , $_GET[ 'type' ] ) );
			$db->setData( $rec , 'regist' , time() );
			$db->setData( $rec , 'open_time' , mktime( 0 , 0 , 0 , $_POST[ 'open_month' ] , $_POST[ 'open_day' ] , $_POST[ 'open_year' ] ) );

//			parent::registProc( $gm, $rec, $loginUserType, $loginUserRank ,$check );
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
			
			$db = $gm[ $_GET[ 'type' ] ]->getDB();
			$db->setData( $rec , 'open_time' , mktime( 0 , 0 , 0 , $_POST[ 'open_month' ] , $_POST[ 'open_day' ] , $_POST[ 'open_year' ] ) );
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
			$table = WS::Finder( 'news' )->searchReadableTable( $table );
			$table = WS::Finder( 'news' )->sortTable( $table );

			return parent::searchProc( $gm, $table, $loginUserType, $loginUserRank );
		}
	}

?>