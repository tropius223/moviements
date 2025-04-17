<?php

	/*******************************************************************************************************
	 * <PRE>
	 *
	 * activate.php - 専用プログラム
	 * ユーザのアクティベーション処理を行うプログラム。
	 *
	 * </PRE>
	 *******************************************************************************************************/

	ob_start();

	try
	{
		// ヘッダーを読み込みます。
		include_once "custom/head_main.php";

		print System::getHead($gm,$loginUserType,$loginUserRank);

		// データベースの取得
		$db		 = $gm[ $_GET['type'] ]->getDB();
		$table	 = $db->getTable();
		$table	 = $db->searchTable($table, 'id', '=', $_GET['id'] );

		$check = false;
		$sys	 = SystemUtil::getSystem( $_GET["type"] );

		if( $db->getRow($table) != 0 )
		{
			$rec	 = $db->getRecord( $table, 0 );

			// 取得したレコードのid,メールアドレスから生成したハッシュと
			// アクセスされたメールに記載されたハッシュ値と一致するかを確認
			if(   md5(  $db->getData( $rec, 'id' ). $db->getData( $rec, 'mail' )  ) == $_GET['md5']   )
			{
				// 登録成功
				$check = $sys->activateAction( $gm , $rec , $loginUserType, $loginUserRank );
			}
		}
		if($check)
			$sys->drawActivateComp( $gm , $rec , $loginUserType, $loginUserRank );
		else
			$sys->drawActivateFaled( $gm , $rec , $loginUserType, $loginUserRank );

		print System::getFoot($gm,$loginUserType,$loginUserRank);
	}
	catch( Exception $e_ )
	{
		ob_end_clean();

		//エラーメッセージをログに出力
		$errorManager = new ErrorManager();
		$errorMessage = $errorManager->GetExceptionStr( $e_ );

		$errorManager->OutputErrorLog( $errorMessage );

		//例外に応じてエラーページを出力
		$className = get_class( $e_ );
		ExceptionUtil::DrawErrorPage( $className );
	}

	ob_end_flush();
?>