<?php

	/*******************************************************************************************************
	 * <PRE>
	 *
	 * regist.php - 汎用プログラム
	 * 新規登録処理。
	 *
	 * </PRE>
	 *******************************************************************************************************/

	ob_start();

	try
	{
		include_once "custom/head_main.php";

		SaveTierParameter();

		print System::getHead($gm,$loginUserType,$loginUserRank);
		System::$checkData	 = new CheckData( $gm, false, $loginUserType, $loginUserRank );

		$sys	 = SystemUtil::getSystem( $_GET["type"] );

		if(   $THIS_TABLE_IS_NOHTML[ $_GET['type'] ] || !isset(  $gm[ $_GET['type'] ]  )   )
		{
			$sys->drawRegistFaled( $gm, $loginUserType, $loginUserRank );
		}
		else
		{
			$db		 = $gm[ $_GET['type'] ]->getDB();

			if(isset($_POST['back']))
			{
				$_POST['post'] = "";

				if($_POST['step'])
					$_POST['step']--;
			}

			$useStep = false;

			if( 0 < $terminal_type ) //携帯端末の場合
				{ $useStep = $THIS_TABLE_IS_STEP_MOBILE[ $_GET[ 'type' ] ]; }
			else //PC端末の場合
				{ $useStep = $THIS_TABLE_IS_STEP_PC[ $_GET[ 'type' ] ]; }

			// 登録情報入力フォームを描画
			if(  !isset( $_POST['post'] ) || !strlen($_POST['post']) )
			{
				if(!$_POST['step'])
					$_POST['step'] = 1;

				if(strlen($_GET['copy']) && $sys->copyCheck( $gm, $loginUserType, $loginUserRank ))
				{
					$rec	 = $db->selectRecord($_GET['copy']);
					$gm[ $_GET['type'] ]->setForm( $rec );
				}
				else
				{
					$gm[ $_GET['type'] ]->setForm( $_GET );
					$rec	 = $db->getNewRecord( $_GET );
				}

				$gm[ $_GET['type'] ]->addHiddenForm( 'post', 'check' );
				$gm[ $_GET['type'] ]->addHiddenForm( 'step', $_POST['step'] );

				//フォームを全てhiddenで追加
				foreach($gm[ $_GET['type'] ]->colStep as $key => $value)
				{
					if( $useStep )
					{
						if($value && $value < $_POST['step'] )
							$gm[ $_GET['type'] ]->addHiddenForm( $key , ($_POST['back'] ? $_POST[$key] : $_GET[$key]) );
					}
				}

				$sys->drawRegistForm( $gm, $rec, $loginUserType, $loginUserRank );
			}
			else
			{
				// 登録情報確認画面を描画
				if( $_POST['post'] == 'check' )
				{

					// 入力内容確認
					$check	 = $sys->registCheck( $gm, false, $loginUserType, $loginUserRank );

					if($check)
						$_POST[ 'step' ]++;

					if( $useStep )
					{
					if($gm[ $_GET[ 'type' ] ]->maxStep >= 2 && $gm[ $_GET[ 'type' ] ]->maxStep + 1 > $_POST[ 'step' ])
						$check = false;
					}

					$rec	 = $db->getNewRecord( $_POST );

				  	if( $check )
					{// 新しくPOST内容を利用してレコードを作成する。

						$sys->registProc( $gm, $rec, $loginUserType, $loginUserRank ,true);

						$gm[ $_GET['type'] ]->setHiddenFormRecord( $rec );

						// 登録内容確認ページを出力。
						$gm[ $_GET['type'] ]->addHiddenForm( 'post', 'regist' );
						$gm[ $_GET['type'] ]->addHiddenForm( 'step', $_POST['step'] );
						$sys->drawRegistCheck( $gm, $rec, $loginUserType, $loginUserRank );
					}
					else
					{// 入力内容に不備がある場合
						//$gm[ $_GET['type'] ]->setHiddenFormRecord( $rec );
						$gm[ $_GET['type'] ]->addHiddenForm( 'post', 'check' );
						$gm[ $_GET['type'] ]->addHiddenForm( 'step', $_POST['step'] );

						$gm[ $_GET['type'] ]->setForm( $rec );

						///stepの異なる項目を全てhiddenで追加
						foreach($gm[ $_GET['type'] ]->colStep as $key => $value)
						{
							if($value && $value < $_POST['step'])
								$gm[ $_GET['type'] ]->addHiddenForm( $key , $_POST[$key] );
						}

						$sys->drawRegistForm( $gm, $rec, $loginUserType, $loginUserRank );
					}
				}
				else if( $_POST['post'] == 'regist'  )
				{ // 登録実行処理
					// 新しくPOST内容を利用してレコードを作成する。
					$rec	 = $db->getNewRecord( $_POST );

					$check	 = $sys->registCompCheck( $gm, $rec ,$loginUserType, $loginUserRank);

					if( $check )
					{
						$sys->registProc( $gm, $rec, $loginUserType, $loginUserRank );

						// レコードを追加します。
						$db->addRecord($rec);

						$sys->registComp( $gm, $rec, $loginUserType, $loginUserRank );

						// 登録完了ページを出力します。
						$sys->drawRegistComp( $gm, $rec, $loginUserType, $loginUserRank );
					}
					else
					{
						$sys->drawRegistFaled( $gm, $loginUserType, $loginUserRank );
					}
				}
			}
		}

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