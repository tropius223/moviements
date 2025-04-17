<?php

	/**
		@file
		@brief アクセスしたユーザーに対してクリックポイントの加算を行う。
	*/

	ob_start();

	try
	{
		include_once 'custom/head_main.php';

		$sysDB    = GMList::getDB( 'system' );
		$sysRec   = $sysDB->selectRecord( 'ADMIN' );
		$useClick = $sysDB->getData( $sysRec , 'use_click_point' );

		if( !$useClick ) //クリックポイントの使用が許可されていない場合
			{ throw new IllegalAccessException( '不正なアクセスです' ); }

		print System::getHead( $gm , $loginUserType , $loginUserRank );

		switch( $loginUserType ) //ユーザー種別で分岐
		{
			case 'nUser' : //ログインユーザー
			{
				$addPoint = addClickPoint( $rec );

				if( $addPoint ) //ポイントが加算できた場合
					{ Template::drawTemplate( $gm[ 'system' ] , $rec , $loginUserType , $loginUserRank , '' , 'CLICK_POINT_RESULT_DESIGN' ); }
				else //ポイントが加算できない場合
					{ Template::drawTemplate( $gm[ 'system' ] , $rec , $loginUserType , $loginUserRank , '' , 'CLICK_POINT_FAILED_DESIGN' ); }

				break;
			}

			default : //その他
			{
				Template::drawTemplate( $gm[ 'system' ] , $rec , $loginUserType , $loginUserRank , '' , 'TOP_PAGE_DESIGN' );

				break;
			}
		}

		print System::getFoot( $gm , $loginUserType , $loginUserRank );
	}
	catch( Exception $e )
	{
		ob_end_clean();

		//エラーメッセージをログに出力
		$errorManager = new ErrorManager();
		$errorMessage = $errorManager->GetExceptionStr( $e );

		$errorManager->OutputErrorLog( $errorMessage );

		//例外に応じてエラーページを出力
		$className = get_class( $e );
		ExceptionUtil::DrawErrorPage( $className );
	}

	ob_end_flush();
