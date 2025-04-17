<?php

	/**
	 * システムコールクラス
	 * 
	 * @author ----
	 * @version 1.0.0
	 * 
	 */
	class kickbackSystem extends System
	{
		/**********************************************************************************************************
		 * 汎用システム用メソッド
		 **********************************************************************************************************/

		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// 削除関係
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

		/**
		 * 削除完了処理。
		 * 登録削除完了時に実行したい処理があればココに記述します。
		 * 削除完了メールを送信したい場合などに利用します。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param rec フォームのからの入力データを反映したレコードデータ。
		 */
		function deleteComp( &$gm, &$rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $KICKBACK_STATE_ON;
			// **************************************************************************************
			$db = $gm[ $_GET['type'] ]->getDB();
			$state = $db->getData( $rec, 'state');
			
			if( $state == $KICKBACK_STATE_ON ){
				$point		= $db->getData( $rec, 'point');
				$owner_id	= $db->getData( $rec, 'owner');
				
				$db = $gm[ 'nUser' ]->getDB();
				$_rec = $db->selectRecord( $owner_id );
				$now_point = $db->getData( $_rec, 'point' );

				if( $now_point < $point )
					{ $db->setData( $_rec, 'point', 0 ); }
				else
					{ $db->setData( $_rec, 'point', $now_point - $point ); }

				$db->updateRecord( $_rec );
			}
			
			parent::deleteComp( $gm, $rec, $loginUserType, $loginUserRank );			
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
			global $KICKBACK_STATE_OFF;
			global $KICKBACK_STATE_ON;
			// **************************************************************************************

			if( 'admin' != $loginUserType )
			{
				$db    = $gm[ $_GET[ 'type' ] ]->getDB();
				$table = $db->searchTable( $table , 'owner' , '=' , $LOGIN_ID );
			}else if( isset( $_POST ) && isset($_POST['id']) )
			{
				$db    = $gm[ $_GET[ 'type' ] ]->getDB();
				$rec = $db->selectRecord( $_POST['id'] );
				if($rec){
					$now_state = $db->getData( $rec, 'state');
					$next_state = $_POST[ 'state' ];
					
					if( $now_state != $next_state ){
						$owner_id	= $db->getData( $rec, 'owner');
						$point		= $db->getData( $rec, 'point');
						
						$db->setData( $rec, 'state' , $next_state );
						$db->updateRecord( $rec );
						
						$db = $gm[ 'nUser' ]->getDB();
						$_rec = $db->selectRecord( $owner_id );
						$now_point = $db->getData( $_rec, 'point' );
						switch($now_state){
							case $KICKBACK_STATE_ON:
								//onからoffへ  減算

								if( $now_point < $point )
									{ $db->setData( $_rec, 'point', 0 ); }
								else
									{ $db->setData( $_rec, 'point', $now_point - $point ); }

								break;
							case $KICKBACK_STATE_OFF:
								//offからonへ  加算
								$db->setData( $_rec, 'point', $now_point + $point );
								break;
						}
						$db->updateRecord( $_rec );

						ChangeTier( $next_state , $_POST[ 'id' ] );
					}
				}
				unset($_POST['id']);
				unset($_POST['state']);
			}

			parent::searchProc( $gm, $table, $loginUserType, $loginUserRank );			
		}
	}

?>