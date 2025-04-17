<?php

	class accessTradeAPISystem extends System
	{
		function submitCheck( &$iGM_ , $iEdit_ , $iLoginUserType_ , $iLoginUserRank_ )
		{
			self::$checkData->generalCheck( $iEdit_ );

			return self::$checkData->getCheck();
		}

        function submitCompCheck( &$iGM_ , $iRec_ , $iLoginUserType_ , $iLoginUserRank_ , $iEdit_ = false )
		{
            $db = $iGM_[ $_GET[ 'type' ] ]->getDB();

            if( !$iEdit_ ) //編集でない場合
			{
				//重複登録チェック
				$id    = $db->getData( $iRec_ , 'id' );
				$table = $db->getTable();
				$table = $db->searchTable( $table , 'id' , '=' , $id );
				$row   = $db->getRow( $table );

				if( $row ) //既にIDを持つレコードが存在する場合
					{ self::$checkData->addError( 'duplication_id' ); }
            }

			if( $iEdit_ ) //編集の場合
			{
				//Const/AdminData/MailDupのチェック
				$options = $iGM_[ $_GET[ 'type' ] ]->colEdit;

				foreach( $options as $column => $validates )
				{
					$validates = explode( '/' , $validates );

					if( in_array( 'Const' , $validates ) )
						self::$checkData->checkConst( $column , null );

					if( in_array( 'AdminData' , $validates ) )
						self::$checkData->checkAdminData( $column , null );

					if( in_array( 'MailDup' , $validates ) )
						self::$checkData->checkMailDup( $column , null );
				}
			}

			return self::$checkData->getCheck();
        }

		function submitProc( &$iGM_ , &$ioRec_ , $iLoginUserType_ , $iLoginUserRank_ , $iCheck_ = false )
		{
			$db = $iGM_[ $_GET[ 'type' ] ]->getDB();

			// IDと登録時間を記録。
			$db->setData( $ioRec_ , 'id'     , SystemUtil::getNewId( $db , $_GET[ 'type' ] ) );
			$db->setData( $ioRec_ , 'regist' , time() );

			if( !$check )
				{ $this->uplodeComp( $iGM_ , $db , $iRec_ ); } // ファイルのアップロード完了処理
		}

		function submitComp( &$iGM_ , &$iRec_ , $iLoginUserType_ , $iLoginUserRank_ )
		{
			$result = accessTradeAPILogic::GetResultsBySubmit( $iRec_ );
			$result = accessTradeAPILogic::ConvertAPIResultToCommonFormat( $result );

			$adDB = $iGM_[ 'adwares' ]->getDB();

			foreach( $result as $element )
			{
				$adRec = adwaresLogic::CreateRecordByAPIResult( 'accessTradeAPI' , $element , $iRec_ );

				if( $adRec )
					{ $adDB->addRecord( $adRec ); }
			}
		}

		function drawSubmitForm( &$iGM_ , $iRec_ , $iLoginUserType_ , $iLoginUserRank_ )
		{
			$this->setErrorMessage( $iGM_[ $_GET[ 'type' ] ] );

			if( 2 <= $iGM_[ $_GET[ 'type' ] ]->maxStep )
				{ Template::drawTemplate( $iGM_[ $_GET[ 'type' ] ] , $iRec_ , $iLoginUserType_ , $iLoginUserRank_ , $_GET[ 'type' ] , 'SUBMIT_FORM_PAGE_DESIGN' . $_POST[ 'step' ] , 'submit.php?type=' . $_GET[ 'type' ] ); }
			else
				{ Template::drawTemplate( $iGM_[ $_GET[ 'type' ] ] , $iRec_ , $iLoginUserType_ , $iLoginUserRank_ , $_GET[ 'type' ] , 'SUBMIT_FORM_PAGE_DESIGN' , 'submit.php?type=' . $_GET[ 'type' ] ); }
		}

		function drawSubmitCheck( &$iGM_ , $iRec_ , $iLoginUserType_ , $iLoginUserRank_ )
			{ Template::drawTemplate( $iGM_[ $_GET[ 'type' ] ] , $iRec_ , $iLoginUserType_ , $iLoginUserRank_ , $_GET[ 'type' ] , 'SUBMIT_CHECK_PAGE_DESIGN' , 'submit.php?type=' . $_GET[ 'type' ] ); }

		function drawSubmitComp( &$iGM_ , $iRec_ , $iLoginUserType_ , $iLoginUserRank_ )
			{ Template::drawTemplate( $iGM_[ $_GET[ 'type' ] ] , $iRec_ , $iLoginUserType_ , $iLoginUserRank_ , $_GET[ 'type' ] , 'SUBMIT_COMP_PAGE_DESIGN' ); }

		function drawSubmitFaled( &$iGM_ , $iLoginUserType_ , $iLoginUserRank_ )
		{
			$this->setErrorMessage( $iGM_[ $_GET[ 'type' ] ] );

			Template::drawTemplate( $iGM_[ $_GET[ 'type' ] ] , null , '' , $iLoginUserRank_ , '' , 'SUBMIT_FALED_DESIGN' );
		}
	}
