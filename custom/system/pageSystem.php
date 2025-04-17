<?php

	/**
	 * システムコールクラス
	 * 
	 * @author ----
	 * @version 1.0.0
	 * 
	 */
	class pageSystem extends System
	{
		/**********************************************************************************************************
		 * 汎用システム用メソッド
		 **********************************************************************************************************/



		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// 登録関係
		/////////////////////////////////////////////////////////////////////////////////////////////////////////


		/**
		 * 登録内容確認。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param edit 編集なのか、新規追加なのかを真偽値で渡す。
		 * @return エラーがあるかを真偽値で渡す。
		 */
		function registCheck( &$gm, $edit, $loginUserType, $loginUserRank )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			// **************************************************************************************
			
			$result	 = parent::registCheck( $gm, $edit, $loginUserType, $loginUserRank );
			
			if($result){
				$db = $gm['page']->getDB();
				$table = $db->getTable();
				$table = $db->searchTable( $table, 'name', '=', $_POST['name'] );
				foreach( $_POST['authority'] as $auth ){
					$table_buf[] = $db->searchTable( $db->getTable(), 'authority', 'in', '%'.$auth.'%' );
				}
				$table2 = $db->getTable();
				foreach($table_buf as $table_auth){
					$table2 = $db->orTable($table2,$table_auth);
				}
				
				$table = $db->andTable($table,$table2);
				
				if($edit){
					$table = $db->searchTable( $table, 'id', '!', $_POST['id'] );
				}
				
				$row = $db->getRow($table);
				if($row){
					System::$checkData->addError('name_dup');
					$result = false;
				}
			}
			return $result;
		}

		/**
		 * 登録処理完了処理。
		 * 登録完了時にメールで内容を通知したい場合などに用います。
		 * 
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param rec レコードデータ。
		 */
		function registComp( &$gm, &$rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $page_path;
			// **************************************************************************************

			$db = $gm['page']->getDB();
			
			$new_id = $db->getData( $rec , 'id' );
			fileWrite( $page_path.$new_id.".dat" , $_POST['html'] );
			
			parent::registComp( $gm, $rec, $loginUserType, $loginUserRank );
		}



		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// 編集関係
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

		/**
		 * 編集完了処理。
		 * フォーム入力以外の方法でデータを登録する場合は、ここでレコードに値を代入します。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param rec フォームのからの入力データを反映したレコードデータ。
		 */
		function editComp( &$gm, &$rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $page_path;
			// **************************************************************************************
			
			fileWrite( $page_path.$_GET['id'].".dat" , $_POST['html'] );
			
			parent::editComp( $gm, $rec, $loginUserType, $loginUserRank );			
		}

		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// 編集関係
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

		/**
		 * 編集フォームを描画する。
		 *
		 * @param gm GUIManagerオブジェクト配列　連想配列で gm[ TABLE名 ] でアクセスが可能です。
		 * @param rec 編集対象のレコードデータ
		 * @param loginUserType ログインしているユーザの種別
		 * @param loginUserRank ログインしているユーザの権限
		 */
		function drawEditForm( &$gm, &$rec, $loginUserType, $loginUserRank )
		{
			// ** conf.php で定義した定数の中で、利用したい定数をココに列挙する。 *******************
			global $EDIT_FORM_PAGE_DESIGN;
			global $LOGIN_ID;
            global $NOT_LOGIN_USER_TYPE;
            
            global $page_path;
			// **************************************************************************************
			
            $this->setErrorMessage($gm[ $_GET['type'] ]);

			$db		 = $gm[ 'page' ]->getDB();
			$_GET['html'] = fileRead( $page_path.$_GET['id'].".dat");
			
			parent::drawEditForm( $gm, $rec, $loginUserType, $loginUserRank );
		}

	}

?>