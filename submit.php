<?php

	/*******************************************************************************************************
	 * <PRE>
	 *
	 * submit.php - 汎用プログラム
	 * 新規データ送信処理。
	 *
	 * </PRE>
	 *******************************************************************************************************/

	ob_start();

	try
	{
		include_once 'custom/head_main.php';

		if( !$gm[ $_GET[ 'type' ] ] ) //GUIManagerオブジェクトが見つからない場合
			{ throw new IllegalAccessException( '不正なアクセスです[' . $_GET[ 'type' ] . ']' ); }

		if( !$THIS_TABLE_IS_SUBMIT[ $_GET[ 'type' ] ] ) //送信可能なテーブルに指定されていない場合
			{ throw new IllegalAccessException( '不正なアクセスです[' . $_GET[ 'type' ] . ']' ); }

		print System::getHead( $gm , $loginUserType , $loginUserRank );

		System::$checkData = new CheckData( $gm , false , $loginUserType , $loginUserRank );
		$sys               = SystemUtil::getSystem( $_GET[ 'type' ] );
		$db                = $gm[ $_GET[ 'type' ] ]->getDB();

		if( isset( $_POST[ 'back' ] ) ) //戻るが押された場合
		{
			$_POST[ 'post' ] = '';

			if( 1 < $_POST[ 'step' ] ) //ステップが1より大きい場合
				{ --$_POST[ 'step' ]; }
		}

		// 登録情報入力フォームを描画
		if( !$_POST[ 'post' ] ) //フォームが送信されていない場合
		{
			if( !$_POST[ 'step' ] ) //ステップがない場合
				{ $_POST[ 'step' ] = 1; }

			if( $_GET[ 'copy' ] && $sys->copyCheck( $gm , $loginUserType , $loginUserRank ) ) //コピーが指定されている場合
			{
				$rec = $db->selectRecord( $_GET[ 'copy' ] );

				$gm[ $_GET[ 'type' ] ]->setForm( $rec );
			}
			else //コピーが指定されていない場合
			{
				$rec = $db->getNewRecord( $_GET );

				$gm[ $_GET[ 'type' ] ]->setForm( $_GET );
			}

			$gm[ $_GET[ 'type' ] ]->addHiddenForm( 'post' , 'check' );
			$gm[ $_GET[ 'type' ] ]->addHiddenForm( 'step' , $_POST[ 'step' ] );

			//フォームを全てhiddenで追加
			foreach( $gm[ $_GET[ 'type' ] ]->colStep as $key => $step ) //全てのステップ設定を処理
			{
				if( $step && $_POST[ 'step' ] > $step ) //過去のステップの場合
					{ $gm[ $_GET[ 'type' ] ]->addHiddenForm( $key , ( $_POST[ 'back' ] ? $_POST[ $key ] : $_GET[ $key ] ) ); }
			}

			$sys->drawSubmitForm( $gm , $rec , $loginUserType , $loginUserRank );
		}
		else //フォームが送信されている場合
		{
			switch( $_POST[ 'post' ] ) //postの種類で分岐
			{
				case 'check' : //登録情報確認画面
				{
					$success = $sys->submitCheck( $gm , false , $loginUserType , $loginUserRank );

					if( $success ) //データが正当な場合
						{ ++$_POST[ 'step' ]; }

					if( $gm[ $_GET[ 'type' ] ]->maxStep >= 2 && $gm[ $_GET[ 'type' ] ]->maxStep + 1 > $_POST[ 'step' ] ) //最後のステップではない場合
						{ $success = false; }

					$rec = $db->getNewRecord( $_POST );

					if( $success ) //送信内容に問題がない場合
					{
						$sys->submitProc( $gm , $rec , $loginUserType , $loginUserRank , true );

						$gm[ $_GET[ 'type' ] ]->setHiddenFormRecord( $rec );

						// 登録内容確認ページを出力。
						$gm[ $_GET[ 'type' ] ]->addHiddenForm( 'post' , 'submit' );
						$gm[ $_GET[ 'type' ] ]->addHiddenForm( 'step' , $_POST[ 'step' ] );
						$sys->drawSubmitCheck( $gm , $rec , $loginUserType , $loginUserRank );
					}
					else //送信内容に問題がある場合
					{
						$gm[ $_GET[ 'type' ] ]->addHiddenForm( 'post' , 'check' );
						$gm[ $_GET[ 'type' ] ]->addHiddenForm( 'step' , $_POST[ 'step' ] );

						$gm[ $_GET[ 'type' ] ]->setForm( $rec );

						///stepの異なる項目を全てhiddenで追加
						foreach( $gm[ $_GET[ 'type' ] ]->colStep as $key => $step )
						{
							if( $step && $_POST[ 'step' ] > $step ) //過去のステップの場合
								{ $gm[ $_GET[ 'type' ] ]->addHiddenForm( $key , $_POST[ $key ] ); }
						}

						$sys->drawSubmitForm( $gm , $rec , $loginUserType , $loginUserRank );
					}

					break;
				}

				case 'submit' : //送信完了
				{
					// 新しくPOST内容を利用してレコードを作成する。
					$rec     = $db->getNewRecord( $_POST );
					$success = $sys->submitCompCheck( $gm , $rec , $loginUserType , $loginUserRank );

					if( $success ) //送信内容に問題がない場合
					{
						$sys->submitProc( $gm , $rec , $loginUserType , $loginUserRank );
						$sys->submitComp( $gm , $rec , $loginUserType , $loginUserRank );

						// 登録完了ページを出力します。
						$sys->drawSubmitComp( $gm , $rec , $loginUserType , $loginUserRank );
					}
					else
						{ $sys->drawSubmitFaled( $gm , $loginUserType , $loginUserRank ); }

					break;
				}

				default : //その他
					{ throw new IllegalAccessException( '不正なアクセスです[' . $_GET[ 'type' ] . ']' ); }
			}
		}

		print System::getFoot( $gm , $loginUserType , $loginUserRank );
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
