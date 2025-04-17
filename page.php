<?php

	/*******************************************************************************************************
	 * <PRE>
	 *
	 * page.php - 専用プログラム
	 * 指定したファイルを出力します。
	 *
	 * </PRE>
	 *******************************************************************************************************/

	ob_start();

	try
	{
		include_once "custom/head_main.php";

		print System::getHead($gm,$loginUserType,$loginUserRank);

		$db = $gm['page']->getDB();

		$table = $db->getTable();

		if( $loginUserType != 'admin'){

			$table = $db->searchTable( $table, 'name', '=',  (string)$_GET['p'] );

			$table = $db->searchTable( $table, 'authority', '=', '%'.$loginUserType.'%' );

			$table = $db->searchTable( $table, 'open', '=', true );
		}else{
			$table = $db->searchTable( $table, 'id', '==',  (string)$_GET['id'] );
		}
		$row = $db->getRow($table);

		if( $row ){
			$rec = $db->getRecord( $table, 0 );
			$HTML = $page_path.$db->getData($rec,'id').".dat";
		}else{
			$HTML =  Template::getLabelFile( 'ERROR_PAGE_DESIGN' );
		}
		print $gm['system']->getString($HTML , null ,null );

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