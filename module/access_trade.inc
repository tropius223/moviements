<?php

	$EDIT_TYPE                            = 'accessTradeAPI';
	$TABLE_NAME[]                         = $EDIT_TYPE;
	$THIS_TABLE_IS_USERDATA[ $EDIT_TYPE ] = false;
	$THIS_TABLE_IS_NOHTML[ $EDIT_TYPE ]   = true;
	$THIS_TABLE_IS_SUBMIT[ $EDIT_TYPE ]   = true;
	$LOGIN_KEY_COLUM[ $EDIT_TYPE ]        = 'mail';
	$LOGIN_PASSWD_COLUM[ $EDIT_TYPE ]     = 'pass1';
	$LOGIN_PASSWD_COLUM2[ $EDIT_TYPE ]    = 'pass2';
	$LST[ $EDIT_TYPE ]                    = './lst/module/access_trade_api.csv';
	$TDB[ $EDIT_TYPE ]                    = './tdb/module/access_trade_api.csv';
	$ID_HEADER[ $EDIT_TYPE ]              = 'ACTR';
	$ID_LENGTH[ $EDIT_TYPE ]              = 5;

	$EDIT_TYPE                            = 'accessTradeFirstCategory';
	$TABLE_NAME[]                         = $EDIT_TYPE;
	$THIS_TABLE_IS_USERDATA[ $EDIT_TYPE ] = false;
	$THIS_TABLE_IS_NOHTML[ $EDIT_TYPE ]   = true;
	$LOGIN_KEY_COLUM[ $EDIT_TYPE ]        = 'mail';
	$LOGIN_PASSWD_COLUM[ $EDIT_TYPE ]     = 'pass1';
	$LOGIN_PASSWD_COLUM2[ $EDIT_TYPE ]    = 'pass2';
	$LST[ $EDIT_TYPE ]                    = './lst/module/access_trade_first_category.csv';
	$TDB[ $EDIT_TYPE ]                    = './tdb/module/access_trade_first_category.csv';
	$ID_HEADER[ $EDIT_TYPE ]              = 'ACTR';
	$ID_LENGTH[ $EDIT_TYPE ]              = 5;

	$EDIT_TYPE                            = 'accessTradeSecondCategory';
	$TABLE_NAME[]                         = $EDIT_TYPE;
	$THIS_TABLE_IS_USERDATA[ $EDIT_TYPE ] = false;
	$THIS_TABLE_IS_NOHTML[ $EDIT_TYPE ]   = true;
	$LOGIN_KEY_COLUM[ $EDIT_TYPE ]        = 'mail';
	$LOGIN_PASSWD_COLUM[ $EDIT_TYPE ]     = 'pass1';
	$LOGIN_PASSWD_COLUM2[ $EDIT_TYPE ]    = 'pass2';
	$LST[ $EDIT_TYPE ]                    = './lst/module/access_trade_second_category.csv';
	$TDB[ $EDIT_TYPE ]                    = './tdb/module/access_trade_second_category.csv';
	$ID_HEADER[ $EDIT_TYPE ]              = 'ACTR';
	$ID_LENGTH[ $EDIT_TYPE ]              = 5;

	$EDIT_TYPE                            = 'accessTradeThirdCategory';
	$TABLE_NAME[]                         = $EDIT_TYPE;
	$THIS_TABLE_IS_USERDATA[ $EDIT_TYPE ] = false;
	$THIS_TABLE_IS_NOHTML[ $EDIT_TYPE ]   = true;
	$LOGIN_KEY_COLUM[ $EDIT_TYPE ]        = 'mail';
	$LOGIN_PASSWD_COLUM[ $EDIT_TYPE ]     = 'pass1';
	$LOGIN_PASSWD_COLUM2[ $EDIT_TYPE ]    = 'pass2';
	$LST[ $EDIT_TYPE ]                    = './lst/module/access_trade_third_category.csv';
	$TDB[ $EDIT_TYPE ]                    = './tdb/module/access_trade_third_category.csv';
	$ID_HEADER[ $EDIT_TYPE ]              = 'ACTR';
	$ID_LENGTH[ $EDIT_TYPE ]              = 5;

	class mod_accessTradeAPI extends command_base
	{
		function drawPreview( &$iGM_ , $iRec_ , $iArgs_ )
		{
			$template = Template::GetLabelFile( 'ACCESS_TRADE_API_PREVIEW_DESIGN' );
			$result   = accessTradeAPILogic::GetResultsBySubmit( $iRec_ );
			$result   = accessTradeAPILogic::ConvertAPIResultToCommonFormat( $result );
			$count    = 0;

			$this->addBuffer( $iGM_->getString( $template , null , 'head' ) );

			foreach( $result as $element )
			{
				$adRec = adwaresLogic::CreateRecordByAPIResult( 'accessTradeAPI' , $element , $iRec_ );

				if( !$adRec )
					{ continue; }

				++$count;

				$this->addBuffer( $iGM_->getString( $template , $adRec , 'list' ) );
			}

			$this->addBuffer( $iGM_->getString( $template , null , 'foot' ) );

			$iGM_->setVariable( 'count' , $count );
			$this->addBuffer( $iGM_->getString( $template , $adRec , 'count' ) );
		}
	}
