<?php

	/**
	 * システムコールクラス
	 * 
	 * @author ----
	 * @version 1.0.0
	 * 
	 */
	class paymentSystem extends System
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
		function registProc( &$gm, &$rec, $loginUserType, $loginUserRank , $check = false )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $LOGIN_ID;
			global $ACTIVE_NONE;
			// **************************************************************************************

			$rate = SystemUtil::getTableData( 'system' , 'ADMIN' , 'point_to_yen_rate' );

			$db  = $gm[ $_GET[ 'type' ] ]->getDB();
			$yen = $db->getData( $rec , 'value_yen' );

			$db->setData( $rec , 'owner'    , $LOGIN_ID );
			$db->setData( $rec , 'activate' , $ACTIVE_NONE );
			$db->setData( $rec , 'value'    , $yen / $rate );

			parent::registProc( $gm, $rec, $loginUserType, $loginUserRank , $check );
		}

		/**
		 * 登録処理完了処理。
		 * 登録完了時にメールで内容を通知したい場合などに用います。
		 * 
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param rec レコードデータ。
		 */
		function registComp( &$gm, &$rec, $loginUserType, $loginUserRank ,$check = false)
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			// **************************************************************************************

			$db       = $gm[ $_GET[ 'type' ] ]->getDB();
			$owner_id = $db->getData( $rec, 'owner' );
			$value    = $db->getData( $rec, 'value' );

			$db = $gm[ 'nUser' ]->getDB();
			$_rec = $db->selectRecord( $owner_id );
			$db->setData( $_rec, 'point', $db->getData( $_rec, 'point') - $value );
			$db->updateRecord( $_rec );

			parent::registProc( $gm, $rec, $loginUserType, $loginUserRank, $check );
		}

		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// 検索関係
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

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
			// **************************************************************************************

			if( 'admin' == $loginUserType && isset( $_POST ) && isset($_POST['id']) )
			{
				$db    = $gm[ $_GET[ 'type' ] ]->getDB();
				$rec = $db->selectRecord( $_POST['id'] );
				if($rec){
					$db->setData( $rec, 'activate' , $_POST[ 'activate'  ]  );
					$db->updateRecord( $rec );
				}
				unset($_POST['id']);
				unset($_POST['activate']);
			}

			if( 'nUser' == $loginUserType )
			{
				$db    = GMList::getDB( $_GET[ 'type' ] );
				$table = $db->searchTable( $table , 'owner' , '=' , $LOGIN_ID );
			}

			parent::searchProc( $gm, $table, $loginUserType, $loginUserRank );
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
			global $LOGIN_ID;
			// **************************************************************************************

			$point = SystemUtil::getTableData('nUser',$LOGIN_ID,'point');
			
			$rate = SystemUtil::getSystemData('point_to_yen_rate');
			$min  = SystemUtil::getSystemData('minimum_payment');

			if( $min > $point * $rate )
				Template::drawTemplate( $gm[ $_GET['type'] ] , null ,'' , $loginUserRank , '' , 'MINIMUM_PAYMENT_DESIGN' );
			else
				parent::drawRegistForm( $gm, $rec, $loginUserType, $loginUserRank ); // 二重描画になるので編集する場合は削除
		}
	}

?>