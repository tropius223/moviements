<?php

	/*******************************************************************************************************
	 * <PRE>
	 *
	 * edit.php - 汎用プログラム
	 * 登録データ編集処理。
	 *
	 * </PRE>
	 *******************************************************************************************************/

	ob_start();

	try
	{
		include_once "custom/head_main.php";

		print System::getHead($gm,$loginUserType,$loginUserRank);
		System::$checkData	 = new CheckData( $gm, true, $loginUserType, $loginUserRank );

		$sys	 = SystemUtil::getSystem( $_GET["type"] );

		if(   $THIS_TABLE_IS_NOHTML[ $_GET['type'] ] || !isset(  $gm[ $_GET['type'] ]  )   )
		{
			$sys->drawEditFaled( $gm, $loginUserType, $loginUserRank );
		}
		else
		{
			$db		 = $gm[ $_GET['type'] ]->getDB();

			if( !isset($_GET['id']) && $_GET['type'] == $loginUserType ){
				$_GET['id'] = $LOGIN_ID;
			}

			$rec	 = $db->selectRecord($_GET['id']);

			if( !isset($rec) )
			{// 編集対象のデータが存在しない。
				$sys->drawEditFaled( $gm, $loginUserType, $loginUserRank );
			}
			else
			{
				// 情報編集フォームを描画
				if( ( !isset( $_POST['post'] ) && !isset( $_POST['delete'] ) && !isset( $_GET['delete'] ) )  || isset($_POST['back']) )
				{
					if(isset($_POST['back']) && $_POST['post'] != 'delete' )
					{
						$rec	 = $db->getNewRecord( $_POST );
					}
					$gm[ $_GET['type'] ]->setForm( $rec );
					$gm[ $_GET['type'] ]->addHiddenForm( 'post', 'check' );
					
					$sys->drawEditForm( $gm, $rec, $loginUserType, $loginUserRank );
				}
				else
				{
					// 登録削除確認処理
					if(  isset( $_POST['delete'] ) || isset( $_GET['delete'] )  )
					{// 削除確認ページを出力します。
						
						$check = $sys->deleteCheck( $gm, $rec, $loginUserType, $loginUserRank );
						if( $check ){
							$gm[ $_GET['type'] ]->addHiddenForm( 'post', 'delete' );
							$sys->drawDeleteCheck( $gm, $rec, $loginUserType, $loginUserRank );
						}else{
							$gm[ $_GET['type'] ]->setForm( $rec );
							$gm[ $_GET['type'] ]->addHiddenForm( 'post', 'check' );
							$sys->drawEditForm( $gm, $rec, $loginUserType, $loginUserRank );
						}
					}else
					{//削除確認フラグ無し
						switch($_POST['post'])
						{
							case 'check': // 変更内容確認画面を描画
								//POSTデータが存在しないか、NULL文字のカラム
								for( $i=0; $i<count( $db->colName ); $i++ )
								{
									if(  !isset( $_POST[ $db->colName[$i] ] ) && !isset( $_POST[ $db->colName[$i]."_CHECKBOX" ] ) 
									&& $gm[ $_GET['type'] ]->colType[ $db->colName[$i] ] != 'image' || 
										( $_POST[ $db->colName[$i] ] == null ) && 
											(
												( $THIS_TABLE_IS_USERDATA[ $_GET['type'] ] && 
													(
														$db->colName[$i] == $LOGIN_PASSWD_COLUM[ $_GET['type'] ] ||
														$db->colName[$i] == $LOGIN_PASSWD_COLUM2[ $_GET['type'] ] 
													)
												)
											)
									)
									{
										$_POST[ $db->colName[$i] ]	 = $db->getData( $rec, $db->colName[$i] );
									}
								}

								// 入力内容確認
								$check	 = $sys->registCheck( $gm, true, $loginUserType, $loginUserRank );

								if($check)
								{// 入力内容に問題が無い場合

									// 新しくPOST内容を利用してレコードを作成する。
									$rec	 = $db->setRecord( $rec, $_POST );

									$sys->editProc( $gm, $rec, $loginUserType, $loginUserRank ,true);

									$gm[ $_GET['type'] ]->setHiddenFormRecordEdit( $rec );

									// 登録内容確認ページを出力。
									$gm[ $_GET['type'] ]->addHiddenForm( 'post', 'update' );
									$sys->drawEditCheck( $gm, $rec, $loginUserType, $loginUserRank );
								}
								else
								{// 入力内容に不備がある場合
									$gm[ $_GET['type'] ]->addHiddenForm( 'post', 'check' );

									$rec	 = $db->getNewRecord( $_POST );

									$gm[ $_GET['type'] ]->setForm( $rec );

									$sys->drawEditForm( $gm, $rec, $loginUserType, $loginUserRank );
								}
								break;

							case 'update': // 登録実行処理
								//POSTデータが存在しないか、NULL文字のカラム
								for( $i=0; $i<count( $db->colName ); $i++ )
								{
									if(  !isset( $_POST[ $db->colName[$i] ] ) || 
										( $_POST[ $db->colName[$i] ] == null ) && 
											( 
												$gm[ $_GET['type'] ]->colType[ $db->colName[$i] ] == 'image' || 
												( $THIS_TABLE_IS_USERDATA[ $_GET['type'] ] && 
													(
														$db->colName[$i] == $LOGIN_PASSWD_COLUM[ $_GET['type'] ] ||
														$db->colName[$i] == $LOGIN_PASSWD_COLUM2[ $_GET['type'] ] 
													)
												)
											)
									)
									{
										$_POST[ $db->colName[$i] ]	 = $db->getData( $rec, $db->colName[$i] );
									}
								}

								// 新しくPOST内容を利用してレコードを作成する。
								$rec	 = $db->setRecord( $rec, $_POST );

								$check	 = $sys->registCompCheck( $gm, $rec ,$loginUserType, $loginUserRank, true);

								if( $check )
								{
									$sys->editProc( $gm, $rec, $loginUserType, $loginUserRank );

									// レコードを追加します。
									$db->updateRecord( $rec );

									$sys->editComp( $gm, $rec, $loginUserType, $loginUserRank );

									// 登録完了ページを出力します。
									$sys->drawEditComp( $gm, $rec, $loginUserType, $loginUserRank );
								}
								else
								{
									$sys->drawEditFaled( $gm, $loginUserType, $loginUserRank );
								}
								break;
							case 'delete': // 登録削除実行処理
							   $sys->deleteProc( $gm, $rec, $loginUserType, $loginUserRank );

							   // 削除完了ページを出力します。
							   $sys->drawDeleteComp( $gm, $rec, $loginUserType, $loginUserRank );

							   $sys->deleteComp( $gm, $rec, $loginUserType, $loginUserRank );
							   break;
						}
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