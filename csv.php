<?php

	/**
		@file
		@brief 換金申請に関するデータのCSVを出力する。
	*/

	ob_start();

	try
	{
		include_once 'custom/head_main.php';

		switch( $loginUserType ) //ユーザー種別で分岐
		{
			case 'admin' : //管理者
				{ break; }

			default : //その他
				{ throw new Exception( $loginUserType ); }
		}

		$nDB    = $gm[ 'nUser' ]->getDB();
		$pDB    = $gm[ 'payment' ]->getDB();
		$pTable = $pDB->getTable();

		switch( $_POST[ 'status' ] ) //ステータス指定で分岐
		{
			case 'accept' : //換金完了
			{
				$pTable = $pDB->searchTable( $pTable , 'activate' , '=' , $ACTIVE_ACCEPT );

				break;
			}

			case 'activate' : //換金中
			{
				$pTable = $pDB->searchTable( $pTable , 'activate' , '=' , $ACTIVE_ACTIVATE );

				break;
			}

			case 'none' : //未承認
			{
				$pTable = $pDB->searchTable( $pTable , 'activate' , '=' , $ACTIVE_NONE );

				break;
			}

			default : //その他
				{ break; }
		}

		$beginTime = mktime( 0 , 0 , 0 , $_POST[ 'month' ] , 1 , $_POST[ 'year' ] );
		$endTime   = mktime( 0 , 0 , 0 , $_POST[ 'month' ] + 1 , 1 , $_POST[ 'year' ] );
		$pTable    = $pDB->searchTable( $pTable , 'regist' , 'b' , $beginTime , $endTime );
		$pRow      = $pDB->getRow( $pTable );
		$result    = Array();

		for( $i = 0 ; $i < $pRow ; ++$i ) //全てのレコードを処理
		{
			$pRec   = $pDB->getRecord( $pTable , $i );
			$userID = $pDB->getData( $pRec , 'owner' );

			$nRec = $nDB->selectRecord( $userID );

			$result[] = CreateCSVLineString( $nRec , $pRec );
		}

		$fileName = 'payment.csv';

		header( 'Cache-Control: public' );
		header( 'Pragma:' );
		header( 'Content-Disposition: attachment; filename="' . $fileName . '"' );
		header( 'Content-type: application/x-octet-stream; charset=Shift_JIS' );

		print join( ',' , Array( 'ユーザーID' , '換金申請ID' , 'ユーザー名' , '金額' , '銀行名' , '支店名' , '口座種類' , '口座番号' , '口座名義' ) ) . "\n";
		print join( "\n" , $result ) . "\n";
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

	function CreateCSVLineString( $iNRec_ , $iPRec_ )
	{
		$nDB    = SystemUtil::getGMforType( 'nUser' )->getDB();
		$pDB    = SystemUtil::getGMforType( 'payment' )->getDB();
		$result = Array();

		$result[] = $nDB->getData( $iNRec_ , 'id' );
		$result[] = $pDB->getData( $iPRec_ , 'id' );
		$result[] = $nDB->getData( $iNRec_ , 'name' );
		$result[] = $pDB->getData( $iPRec_ , 'value' );
		$result[] = $nDB->getData( $iNRec_ , 'bank_name' );
		$result[] = $nDB->getData( $iNRec_ , 'branch_name' );
		$result[] = $nDB->getData( $iNRec_ , 'bank_type' );
		$result[] = $nDB->getData( $iNRec_ , 'bank_number' );
		$result[] = $nDB->getData( $iNRec_ , 'bank_user' );

		foreach( $result as &$value )
		{
			if( preg_match( '/,/' , $value , $match ) )
				{ $value = '"' . $value . '"'; }
		}

		return implode( ',' , $result );
	}
