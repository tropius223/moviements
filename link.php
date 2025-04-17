<?php

	ob_start();

	try
	{
		include_once 'custom/head_main.php';
		include_once 'custom/link/base.php';

		$ASPType = GetASPType();

		switch( $ASPType )
		{
			case 'appdriver' :
			{
				include_once 'custom/link/appdriver.php';
				$link = new LinkAPPDriver();
				break;
			}

			case 'janet' :
			{
				include_once 'custom/link/janet.php';
				$link = new LinkJANet();
				break;
			}

			default :
			{
				$link = new LinkBase();
				break;
			}
		}

		$link->action();
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

	function GetASPType()
	{
		$adwaresID = $_GET[ 'id' ];

		$adDB  = GMList::getDB( 'adwares' );
		$adRec = $adDB->selectRecord( $adwaresID );
		$aspID = $adDB->getData( $adRec , 'asp_type' );

		$aspDB   = GMList::getDB( 'asp_type' );
		$aspRec  = $aspDB->selectRecord( $aspID );
		$aspName = $aspDB->getData( $aspRec , 'system_id' );

		return $aspName;
	}
